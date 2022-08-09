<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Program;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Psr\Http\Client\ClientExceptionInterface;
use function MongoDB\BSON\toJSON;

class ScrapSingleChannel implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  
  /**
   * @var Channel
   */
  private Channel $channel;
  
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($channel) {
    $this->channel = $channel;
  }
  
  public function scrapPrograms(string $day) {
  
  }
  
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle(): void {
    $dom = new Dom;
    
    // programmi di oggi
    
    try {
      $dom->loadFromUrl($this->channel->rawGuideLink);
      
      $timelineEntries = $dom->find(".sgtvchannelplan_divContainer .sgtvchannelplan_divTableRow");
      // Set timezone to Rome because the website use that TZ
      $todayDate = today()->setTimezone("Europe/Rome");
      
      foreach ($timelineEntries as $timelineEntry) {
        $hourEL = $timelineEntry->find(".sgtvchannelplan_hoursCell");
        
        if ($hourEL->count() === 0) {
          continue;
        }
        
        $hour           = trim($hourEL->text());
        $title          = $timelineEntry->find(".sgtvchannelplan_spanInfoNextSteps")->text();
        $categoryString = $timelineEntry->find(".sgtvchannelplan_spanEventType")->text();
        $link           = $timelineEntry->find(".sgtvchannelplan_aNextStep");
        
        if ($link->count() === 0) {
          $link = $timelineEntry->find(".sgtvSpLink");
          
          if ($link->count() === 0) {
            $link = "";
          } else {
            $link = str_replace(["sgtv_window_location_href", "(", ")", "'"], "", $link->getAttribute("onclick"));
          }
        } elseif ($link->count() >= 1) {
          $link = $link->getAttribute("href");
        } else {
          $link = "";
        }
        
        preg_match_all("/(\w.*) (\(.*\))/", $categoryString, $match);
        
        $category = trim($match[1][0]);
        $duration = (int) str_replace(["(", ")", "'"], "", trim($match[2][0]));
        
        $startTime = (new Carbon($todayDate))->setTime(...explode(":", $hour));
        $endTime   = (new Carbon($startTime))->addMinutes($duration);
        
        if ($startTime->day > $todayDate->day) {
          $todayDate->setDay($startTime->day);
          
        } elseif ($endTime->day > $todayDate->day) {
          $todayDate->setDay($endTime->day);
        }
        
        // dump($startTime->setTimezone("utc")->toAtomString() . " -> " . $endTime->setTimezone("utc")->toAtomString());
        
        $newProgram = new Program([
          "channel_tvg_slug" => $this->channel->tvg_slug,
          "start"            => $startTime->setTimezone("utc")->toDateTime(), // Set TZ to UTC for the DB
          "end"              => $endTime->setTimezone("utc")->toDateTime(), // Set TZ to UTC for the DB
          "title"            => $title,
          // "description" => $descriptionEl->count() ? $descriptionEl->innerText : '',
          "category"         => $category,
          "link"             => $link,
          // "thumbnail"   => $thumbnailEl->count() ? $thumbnailEl[0]->getAttribute("src") : '',
        ]);
        
        //        dump($newProgram->toArray());
        
        try {
          Program::updateOrCreate([
            'start'            => $newProgram->start,
            'end'              => $newProgram->end,
            'channel_tvg_slug' => $this->channel->tvg_slug,
          ], $newProgram->toArray());
        } catch (Exception $e) {
          dump($e->getMessage());
        }
      }
      
    } catch (ChildNotFoundException|CircularException|ContentLengthException|LogicalException|StrictException|ClientExceptionInterface|NotLoadedException $e) {
      dump($e);
    }
  }
}
