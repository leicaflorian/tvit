<?php

namespace App\Jobs\Scrappers;

use App\Classes\ProxiedHttp;
use App\Models\Channel;
use App\Models\Program;
use Carbon\Carbon;

class PlutoTvScrapper {
  
  public static function scrap(Channel $channel) {
    dump('Scraping PlutoTv');
    
    // get bearer token
    $bearerToken = self::getBearerToken();
    $programs    = self::getPrograms($bearerToken, $channel);
    
    foreach ($programs as $program) {
      self::saveProgram($program, $channel);
    }
  }
  
  private static function getBearerToken() {
    $url = "https://boot.pluto.tv/v4/start?appName=web&appVersion=6.10.1-60e2ed8f33981a7b47e30eca5e56d8fb33dce90e&deviceVersion=110.0.0&deviceModel=web&deviceMake=chrome&deviceType=web&clientID=74efc5af-6985-4b9f-a710-1e4db5028634&clientModelNumber=1.0.0&channelSlug=pluto-tv-horror-it&serverSideAds=true&constraints=&drmCapabilities=widevine%3AL3&clientTime=2023-02-18T21%3A15%3A39.317Z";
    $res = ProxiedHttp::get("it", $url);
    
    return $res['sessionToken'];
  }
  
  private static function getPrograms($bearer, Channel $channel) {
    // because i can get only 240minutes of programs, i need to make multiple requests
    $programs     = [];
    $minutesToAdd = 0;
    $startOfDay   = (new Carbon())->startOf("day");
    
    do {
      $start = $startOfDay->clone();
      $start->addMinutes($minutesToAdd);
      
      $startFormatted = $start->format("Y-m-d\TH:i:s\Z");
      
//      dump($startFormatted);
      
      $link = "https://service-channels.clusters.pluto.tv/v2/guide/timelines?start=$startFormatted&channelIds=$channel->iptv_code&duration=240";
      $res = ProxiedHttp::get("it", $link, [
        "headers" => [
          "Authorization" => "Bearer " . $bearer
        ]
      ]);
      
      $programs = array_merge($programs, $res['data'][0]['timelines']);
      
      $minutesToAdd += 240;
    } while ($start->isBefore($startOfDay->clone()->addDay()));
    
    return $programs;
  }
  
  private static function saveProgram($program, Channel $channel) {
    $title    = $program['title'];
    $category = $program['episode']['series']['type'];
    $link     = $program['episode']['_id'];
    
    if ($category === 'tv') {
      $episode = $program['episode']['number'];
      $season  = $program['episode']['number'];
      $title   .= "(St. $season - Ep. $episode)";
    }
    
    $newProgram = [
      "channel_tvg_slug" => $channel->tvg_slug,
      "start"            => new \DateTime($program['start']),
      "end"              => new \DateTime($program['stop']),
      "title"            => $title,
      "category"         => $category,
      "link"             => $link,
      "description"      => $program['episode']['description'],
      "cover_img"        => $program['episode']['poster']["path"],
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
