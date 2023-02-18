<?php

namespace App\Jobs\Scrappers;

use App\Jobs\ScrapProgramDetail;
use App\Models\Channel;
use App\Models\Program;
use Carbon\Carbon;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Psr\Http\Client\ClientExceptionInterface;

class SuperGuidaTvScrapper {
  public static function scrap(Channel $channel){
    $dom = new Dom();
  
      $dom->loadFromUrl($channel->rawGuideLink);
    
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
          "channel_tvg_slug" => $channel->tvg_slug,
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
            'channel_tvg_slug' => $channel->tvg_slug,
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
    
    
  }
}
