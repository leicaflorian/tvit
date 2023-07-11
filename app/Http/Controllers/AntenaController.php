<?php

namespace App\Http\Controllers;

use http\Encoding\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AntenaController extends Controller {
  use \App\Traits\ChannelController;
  
  private $url = "https://ceres1.visionman.cfd/ah1/usergenx304Jtlrnd.htm";
  private $tsUrl = "https://ceres1.visionman.cfd/ah1/";
  private $referer = "https://ceres1.visionman.cfd/000/anyone.html";
  
  
  private function baseUrl(): string {
//    https://github.com/iptv-org/iptv/issues
//    https://iptv-org.github.io/iptv/countries/ro.m3u // lista
    return "https://rundle.deta.dev/hls/LIVE\$#channel/6.m3u8/Level(19012010)?start=LIVE&end=END";
  }
  
  private function channelName(): string {
    return "antena";
  }
  
  public function stream($channel) {
    $response = Http::withHeaders([
      "Referer" => $this->referer
    ])->get($this->url);
    
    $list = $this->parseTsUrls($response->body(), "antena/" . $channel);
    
    return response($list)->header(
      "Content-Type", "application/vnd.apple.mpegurl"
    );
  }
  
  public function ts($channel, $url) {
    $response = Http::withHeaders([
      "Referer" => $this->referer,
    ])->get($this->tsUrl . $url);
    
    return response($response->body());
  }
}
