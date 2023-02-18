<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PlutoTvController extends Controller {
  use \App\Traits\ChannelController;
  
  private function baseUrl(): string {
    return "https://service-stitcher.clusters.pluto.tv/v1/stitch/embed/hls/channel/#channel/master.m3u8?deviceId=channel&deviceModel=web&deviceVersion=1.0&appVersion=1.0&deviceType=rokuChannel&deviceMake=rokuChannel&deviceDNT=1&advertisingId=channel&embedPartner=rokuChannel&appName=rokuchannel&is_lat=1&bmodel=bm1&content=channel&platform=web&tags=ROKU_CONTENT_TAGS&coppa=false&content_type=livefeed&rdid=channel&genre=ROKU_ADS_CONTENT_GENRE&content_rating=ROKU_ADS_CONTENT_RATING&studio_id=viacom&channel_id=channel";
//    return "https://service-stitcher.clusters.pluto.tv/v1/stitch/embed/hls/channel/#channel/master.m3u8?advertisingId=&appName=web&appVersion=6.10.1-60e2ed8f33981a7b47e30eca5e56d8fb33dce90e&app_name=web&clientDeviceType=0&clientID=74efc5af-6985-4b9f-a710-1e4db5028634&clientModelNumber=1.0.0&country=IT&deviceDNT=false&deviceId=74efc5af-6985-4b9f-a710-1e4db5028634&deviceLat=45.4366&deviceLon=12.3330&deviceMake=chrome&deviceModel=web&deviceType=web&deviceVersion=110.0.0&marketingRegion=IT&serverSideAds=true&sessionID=d4fa02f0-afd1-11ed-8bb2-a64c8c44e280&sid=d4fa02f0-afd1-11ed-8bb2-a64c8c44e280&userId=";
  }
  
  private function channelName(): string {
    return "plutotv";
  }
  
  public function stream($channel) {
    $link = $this->getChannelStreamLink($channel, false);
    $channelCode = $this->getChannelCode($channel);
    
    $result = Http::get($link);
    
    // si sostituisce l'ultima parte dell'url con il ts
    if ($result->ok()) {
      $data = $result->body();
      
      // devo sostituire i vari link con un mio link che poi farà il proxy delle chiamate
      $pattern      = "/^[0-9].*/m";
      $finalContent = preg_replace_callback($pattern, function ($match) use ($channelCode) {
        $tsString = str_replace("\r", "", $match[0]);
        
        return route("plutotv.ts", ["channel" => $channelCode, "any" => $tsString]);
      }, $data);
      
      return response($finalContent)->header("Content-Type", "application/vnd.apple.mpegurl");
    }
    
    Log::error($result->reason());
    
    return abort(Response::HTTP_BAD_REQUEST);
  }
  
  public function ts($channel, $any, Request $request) {
    $link = "https://service-stitcher.clusters.pluto.tv/v1/stitch/embed/hls/channel/" . $channel . "/$any?" . $request->getQueryString();
    $result = Http::get($link);
    
    if ($result->ok()) {
      $data = $result->body();
      
      return response($data)->header("Content-Type", "video/MP2T");
    }
    
    Log::error($result->reason());
    
    return abort(Response::HTTP_BAD_REQUEST);
  }
}
