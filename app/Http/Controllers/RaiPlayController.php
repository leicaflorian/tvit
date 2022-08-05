<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RaiPlayController extends Controller {
  use \App\Traits\ChannelController;
  
  private function baseUrl(): string {
    return "https://mediapolis.rai.it/relinker/relinkerServlet.htm?cont=#channel&output=62";
  }
  
  private function channelName(): string {
    return "rai";
  }
  
  public function stream($channel) {
    $link   = $this->getChannelStreamLink($channel);
    $result = Http::get($link);
    
    if ($result->ok()) {
      $data       = $result->json();
      $m3u8Link   = $data["video"][0];
      $m3u8Result = Http::get($m3u8Link);
      $m3u8Data   = $m3u8Result->body();
      
      $m3u8Data = str_replace("URI=\"", "URI=\"" . route("raiplay.chunk", [
          "channel" => $channel,
          "any"     => ""
        ]) . "/", $m3u8Data);
      
      $pattern = "/^[0-9a-z].*/m";
      $m3u8Data = preg_replace_callback($pattern, function ($match) use ($channel) {
        return route("raiplay.chunk", ["channel" => $channel, "any" => $match[0]]);
      }, $m3u8Data);
  
      return response($m3u8Data)->header("Content-Type", "application/vnd.apple.mpegurl");
    }
  }
  
  public function chunks($channel, $page) {
    $link = "https://8e7439fdb1694c8da3a0fd63e4dda518.msvdn.net/raiuno1/hls" . "/" . $page;
    
    $result = Http::get($link);
    
    if ($result->ok()) {
      $data = $result->body();
      
//      return response($data)->header("Content-Type", "video/MP2T");
    }
  }
  
  }
}
