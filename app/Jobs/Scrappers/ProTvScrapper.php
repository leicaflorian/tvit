<?php

namespace App\Jobs\Scrappers;

use App\Classes\ProxiedHttp;
use App\Models\Channel;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use PHPHtmlParser\Options;
use Psr\Http\Client\ClientExceptionInterface;

class ProTvScrapper {
  
  public static function scrap(Channel $channel) {
    dump('Scraping ProTV');
    
    // get bearer token
    $programs = self::getPrograms($channel);
    
    foreach ($programs as $program) {
      self::saveProgram($program, $channel);
    }
  }
  
  
  /**
   * @throws ChildNotFoundException
   * @throws CircularException
   * @throws StrictException
   * @throws ClientExceptionInterface
   * @throws NotLoadedException
   * @throws ContentLengthException
   * @throws LogicalException
   */
  private static function getPrograms(Channel $channel) {
    $programs   = collect([]);
    $link       = "https://www.protv.ro/program?channel=1";
    $domOptions = new Options();
    
    $domOptions->setRemoveScripts(false);
    $domOptions->setRemoveSmartyScripts(false);
    
    $dom = new Dom();
    $dom->loadFromUrl($link, $domOptions);
    
    $scripts = $dom->find('script');
    
    // get the script that contains var PageData
    $scripts->each(function ($script) use (&$programs) {
      $content = $script->innerHtml;
      
      if (strpos($content ?? '', 'var PageData') !== false) {
        $json = substr($content, strpos($content, "{"), -1);
        $data = json_decode($json, true);
        
        $today    = Carbon::now()->format('Y-m-d');
        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');
        
        $rawPrograms = collect($data["program"])->filter(fn($d) => str_contains($d['id'], $today) || str_contains($d['id'], $tomorrow));
        
        $rawPrograms->map(function ($program) use (&$programs) {
          $data = collect($program["channels"][0]["segments"])->map(fn($c) => $c["items"])->flatten(1);
          
          $programs->push($data->toArray());
        });
        
      }
    });
    
    return $programs->flatten(1);
  }
  
  private static function saveProgram($program, Channel $channel) {
    $title       = $program['title'];
    $description = $program['episode']['title'];
    $category    = null; //$program['episode']['series']['type'];
    $link        = $program['show']['url'];
    
    if ($title === $description) {
      $description = null;
    }

    $newProgram = [
      "channel_tvg_slug" => $channel->tvg_slug,
      // Dates are in UTC, so we need to subtract 2 hours for the timezone of Romania
      "start"            => (new Carbon($program['start_at']))->subtract("hour", 2)->toDate(),
      "end"              => (new Carbon($program['end_at']))->subtract("hour", 2)->toDate(),
      "title"            => $title,
      "category"         => $category,
      "link"             => $link,
      "description"      => $description,
      "cover_img"        => $program['image'],
    ];
    
    try {
      $res = Program::updateOrCreate([
        'start'            => $newProgram['start'],
        'end'              => $newProgram['end'],
        'channel_tvg_slug' => $channel->tvg_slug,
      ], $newProgram);
      
      /*if ($newProgram->link) {
       // if there is a link, scrap the program detail
        ScrapProgramDetail::dispatch($res)->onQueue("scrap-program-detail")->delay(now()->addSeconds(rand(1, 100)));
      }*/
    } catch (Exception $e) {
      dump($e->getMessage());
      throw $e;
    }
    
  }
}
