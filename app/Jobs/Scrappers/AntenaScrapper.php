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

class AntenaScrapper {
  
  public static function scrap(Channel $channel) {
    dump('Scraping Antena');
    
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
    $link       = "https://antenaplay.ro/program/" . $channel->tvg_slug;
    $domOptions = new Options();
    $dom        = new Dom();
    $dom->loadFromUrl($link, $domOptions);
    
    $days = $dom->find(".canalprogramzile .container-schedule[data-channel=\"$channel->tvg_slug\"]");
    
    // get the script that contains var PageData
    $progrs = collect($days->toArray())->map(function ($dayContainer) {
      $day      = $dayContainer->getAttribute('data-day');
      $inner    = $dayContainer->find(".sch-inner");
      $toReturn = [];
      
      $inner->each(function ($program) use (&$toReturn, &$day) {
        $start = $program->find("> span")?->innerHtml;
        $title = $program->find(".name")->innerHtml;
        
        $toReturn[] = [
          "start_at" => $start,
          "end_at"   => null,
          "day"      => $day,
          "title"    => $title,
        ];
      });
      
      return $toReturn;
    });
    $byDay  = $progrs->flatten(1)
      ->groupBy("day")
      ->map(function ($entries, $day) {
        $currDay    = new Carbon($day);
        $dayStartAt = null;
        
        return collect($entries)->map(function ($entry, $i) use (&$currDay, &$dayStartAt) {
          $hour = explode(":", $entry['start_at'])[0];
          
          if ($i === 0) {
            $dayStartAt = intval($hour);
          }
          
          if ($hour < $dayStartAt) {
            $currDay->addDay();
            $dayStartAt = intval($hour);
          }
          
          return [
            ...$entry,
            "start_date" => $currDay->format("Y-m-d") . "T" . $entry['start_at'],
            "day" => $currDay->format("Y-m-d"),
          ];
        });
      });
    $sorted = $byDay->values()->flatten(1)->sortBy([
      ['day', 'asc'],
      ['start_at', 'asc'],
    ]);
    
    // add endDate
    $toReturn = $sorted->map(function ($entry, $i) use (&$sorted) {
      $nextEntry = $sorted[$i + 1] ?? null;
      
      return [
        ...$entry,
        "end_at" => $nextEntry ? $nextEntry['start_date'] : null,
      ];
    });
    
    
    return $toReturn->toArray();
  }
  
  private static function saveProgram($program, Channel $channel) {
    $title       = $program['title'];
    $description = null; //$program['episode']['title'];
    $category    = null; //$program['episode']['series']['type'];
    $link        = null; //$program['show']['url'];
    
    $newProgram = [
      "channel_tvg_slug" => $channel->tvg_slug,
      // Dates are in UTC, so we need to subtract 2 hours for the timezone of Romania
      "start"            => (new Carbon($program['start_date']))->subtract("hour", 2)->toDate(),
      "end"              => (new Carbon($program['end_at']))->subtract("hour", 2)->toDate(),
      "title"            => $title,
      "category"         => $category,
      "link"             => $link,
      "description"      => $description,
      "cover_img"        => null //$program['image'],
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
