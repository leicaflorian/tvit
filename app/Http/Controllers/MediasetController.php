<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MediasetController extends Controller {
  use \App\Traits\ChannelController;
  
  private function baseUrl(): string {
    return "https://live2.msf.cdn.mediaset.net/content/hls_h0_clr_vos/live/channel(#channel)";
  }
  
  private function channelName(): string {
    return "mediaset";
  }
  
  public function stream($channel) {
    // Download m3u8 file
    $link   = $this->getChannelStreamLink($channel) . "/index.m3u8";
    $result = Http::get($link);
    
    if ($result->ok()) {
      // la risposta contiene una cosa del genere.
      $data = $result->body();
      
      // devo sostituire i vari link con un mio link che poi farà il proxy delle chiamate
      $pattern      = "/stream.*.m3u8/m";
      $finalContent = preg_replace_callback($pattern, function ($match) use ($channel) {
        $stream = explode("/", $match[0])[0];
        
        return route("mediaset.streamPlaylist", ["channel" => $channel, "stream" => $stream]);
      }, $data);
      
      return response($finalContent)->header("Content-Type", "application/vnd.apple.mpegurl");
    } else {
      dump($link, $result);
    }
  }
  
  public function streamPlaylist($channel, $stream) {
    $link = $this->getChannelStreamLink($channel) . "/" . $stream . "/streamPlaylist.m3u8";
    
    $result = Http::get($link);
    
    if ($result->ok()) {
      $data = $result->body();
      
      // devo sostituire i vari link con un mio link che poi farà il proxy delle chiamate
      $pattern      = "/^[0-9].*\\r/m";
      $finalContent = preg_replace_callback($pattern, function ($match) use ($channel, $stream) {
        $tsString = str_replace("\r", "", $match[0]);
        
        return route("mediaset.ts", ["channel" => $channel, "stream" => $stream, "any" => $tsString]);
      }, $data);
      
      return response($finalContent)->header("Content-Type", "application/vnd.apple.mpegurl");
    }
  }
  
  public function ts($channel, $stream, $page) {
    $link = $this->getChannelStreamLink($channel) . "/" . $stream . "/" . $page;
    
    $result = Http::get($link);
    
    if ($result->ok()) {
      $data = $result->body();
      
      return response($data)->header("Content-Type", "video/MP2T");
    }
  }
  
  
}
