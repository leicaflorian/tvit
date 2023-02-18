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
