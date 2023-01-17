<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProTvController extends Controller {
  use \App\Traits\ChannelController;
  
  private function baseUrl(): string {
    // other channels https://github.com/iptv-org/iptv/issues
    // https://rundle.deta.dev/hls/LIVE$ProTV.ro/6.m3u8/Level(19012010)?start=LIVE&end=END
    return "https://hlsrundle-stream-iptv.gq/api/hls/#channel";
  }
  
  private function channelName(): string {
    return "mediaset";
  }
  
  public function stream($channel) {
    // Download m3u8 file
    $link = $this->getChannelStreamLink($channel) . ".m3u8";
    
    $result = Http::get($link);
    
    if ($result->ok()) {
      // la risposta contiene una cosa del genere.
      $data = $result->body();
      
      return response($data)->header("Content-Type", "application/vnd.apple.mpegurl");
    } else {
      return response("Can't read channel data", 503);
    }
  }
  
  public function chunk($channel){
    $query = request()->getQueryString();
    $link = str_replace("#channel", "chunk?" . $query, $this->baseUrl());
    
    $result = Http::get(urldecode($link));
    
    if ($result->ok()) {
      // la risposta contiene una cosa del genere.
      $data = $result->body();
      
      return response($data)->header("Content-Type", "application/javascript");
    } else {
      dump($result);
//      return response("Can't read channel chunk", 503);
    }
  }
}
