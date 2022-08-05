<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller {
  private $channel = "b6";
  private $baseUrl = "https://live2.msf.cdn.mediaset.net/content/hls_h0_clr_vos/live/channel(lb)";
  
  public function index() {
    $link = $this->baseUrl . "/index.m3u8";
    
    // Download m3u8 file
    $result = Http::get($link);
    
    if ($result->ok()) {
      // la risposta contiene una cosa del genere.
      $data = $result->body();
      
      // devo sostituire i vari link con un mio link che poi farà il proxy delle chiamate
      $pattern      = "/stream.*.m3u8/m";
      $finalContent = preg_replace_callback($pattern, function ($match) {
        return "/m3u8/test/" . $match[0];
      }, $data);
      
      return response($finalContent)->header("Content-Type", "application/vnd.apple.mpegurl");
    }
  }
  
  public function show(Request $request, $stream, $list) {
    $link = $this->baseUrl;
    $url  = "/" . $stream . "/" . $list;
    
    $result = Http::get($link . $url);
    
    if ($result->ok()) {
      $data = $result->body();
      
      // devo sostituire i vari link con un mio link che poi farà il proxy delle chiamate
      $pattern      = "/^[0-9].*\\r/m";
      $finalContent = preg_replace_callback($pattern, function ($match) use ($stream) {
        return "/m3u8/test/ts/" . $stream . "/" . $match[0];
      }, $data);
      
      
      return response($finalContent)->header("Content-Type", "application/vnd.apple.mpegurl");
    }
  }
  
  public function ts($stream, $page) {
    $link = $this->baseUrl;
    $url  = $link . "/" . $stream . "/" . $page;
    
    $result = Http::get($url);
//    dd($result);
    
    if ($result->ok()) {
      $data = $result->body();
      
      return response($data)->header("Content-Type", "video/MP2T");
    }
  }
}
