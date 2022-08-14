<?php

namespace App\Http\Controllers;

use App\Enums\ChannelGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RaiPlayController extends Controller {
  use \App\Traits\ChannelController;
  
  protected array $localMap = [
    [
      "id"         => "rai-1",
      "iptv_code"  => "2606803",
      "name"       => "Rai 1",
    ],
    [
      "id"         => "rai-2",
      "iptv_code"  => "308718",
      "name"       => "Rai 2",
    ],
    [
      "id"         => "rai-3",
      "iptv_code"  => "308709",
      "name"       => "Rai 3",
    ],
    [
      "id"         => "rai-4",
      "iptv_code"  => "746966",
      "name"       => "Rai 4",
    ],
    [
      "id"         => "rai-movie",
      "iptv_code"  => "747002",
      "name"       => "Rai Movie",
    ],
  ];
  
  private function baseUrl(): string {
    return "https://mediapolis.rai.it/relinker/relinkerServlet.htm?cont=#channel&output=12";
  }
  
  private function channelName(): string {
    return "rai";
  }
  
  public function stream($channel) {
    $link = $this->getChannelStreamLink($channel);
    // $result = Http::get($link);
    
    /*
     * I can't use the same logic as other programs due to geoBlocking.
     * This route must be used only if the user is from italy
     */
    
    return redirect($link);

//    if ($result->ok()) {
//      $data = $result->json();
//
//      dd($result);
//      $m3u8Link   = $data["video"][0];
//      $m3u8Result = Http::get($m3u8Link);
//      $m3u8Data   = $m3u8Result->body();
//
//      $m3u8Data = str_replace("URI=\"", "URI=\"" . route("raiplay.chunk", [
//          "channel" => $channel,
//          "any"     => ""
//        ]) . "/", $m3u8Data);
//
//      $pattern  = "/^[0-9a-z].*/
//    m";
//      $m3u8Data = preg_replace_callback($pattern, function ($match) use ($channel) {
//        return route("raiplay . chunk", ["channel" => $channel, "any" => $match[0]]);
//      }, $m3u8Data);
//
//      return response($m3u8Data)->header("Content - Type", "application / vnd . apple . mpegurl");
//    }
  }
  
  public function chunks($channel, $page) {
    $link = "https://8e7439fdb1694c8da3a0fd63e4dda518.msvdn.net/raiuno1/hls" . "/" . $page;
    
    $result = Http::get($link);
    
    if ($result->ok()) {
      $data = $result->body();

//      return response($data)->header("Content-Type", "video/MP2T");
    }
//  }
  
  }
}
