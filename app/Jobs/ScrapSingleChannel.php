<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Program;
use App\Jobs\ScrapProgramDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Psr\Http\Client\ClientExceptionInterface;

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
    $dom = new Dom();
    
    // programmi di oggi
    dump("Starting programs from " . $this->channel->name . " channel");
    
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
            $link = null;
          } else {
            $link = str_replace(["sgtv_window_location_href", "(", ")", "'", ";"], "", $link->getAttribute("onclick"));
          }
        } elseif ($link->count() >= 1) {
          $link = $link->getAttribute("href");
        } else {
          $link = null;
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
          "start"            => $startTime->setTimezone("utc"),
          "end"              => $endTime->setTimezone("utc"),
          "title"            => $title,
          "category"         => $category,
          "link"             => $link,
        ]);
        
        //        dump($newProgram->toArray());
        
        try {
          $res = Program::updateOrCreate([
            'start'            => $newProgram->start,
            'end'              => $newProgram->end,
            'channel_tvg_slug' => $this->channel->tvg_slug,
          ], $newProgram->toArray());
          
          if ($newProgram->link) {
            // if there is a link, scrap the program detail
            ScrapProgramDetail::dispatch($res)->onQueue("scrap-program-detail")->delay(now()->addSeconds(rand(1, 100)));
          }
        } catch (Exception $e) {
          dump($e->getMessage());
          throw $e;
        }
      }
      
    } catch (ChildNotFoundException|CircularException|ContentLengthException|LogicalException|StrictException|ClientExceptionInterface|NotLoadedException $e) {
      dump($e);
      throw $e;
    }
  }
}
