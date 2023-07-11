<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProTvController extends Controller {
  use \App\Traits\ChannelController;
  
  private $url = "https://tvonline123.com/hls/protvhd.m3u8";
  private $tsUrl = "https://tvonline123.com/hls/";
  private $referer = "https://tvonline123.com/tvlive/any.html";
  
  private function baseUrl(): string {
    // other channels https://github.com/iptv-org/iptv/issues
    // https://rundle.deta.dev/hls/LIVE$ProTV.ro/6.m3u8/Level(19012010)?start=LIVE&end=END
    return "https://hlsrundle-stream-iptv.gq/api/hls/#channel";
  }
  
  private function channelName(): string {
    return "mediaset";
  }
  
  public function stream($channel) {
//    https://www.cool-etv.net/ch/protv.htm#!
    
    $response = Http::withHeaders([
      "Referer" => $this->referer,
      "Origin" => $this->referer,
    ])->get($this->url);
    
    $list = $this->parseTsUrls($response->body(), "protv/" . $channel);
    
    return response($list)->header(
      "Content-Type", "application/vnd.apple.mpegurl"
    );
  }
  
  public function ts($channel, $url) {
    $response = Http::withHeaders([
      "Referer" => $this->referer,
      "Origin" => $this->referer,
    ])->get($this->tsUrl . $url);
    
    return response($response->body());
  }
}
