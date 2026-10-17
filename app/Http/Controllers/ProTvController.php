<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProTvController extends Controller {
  use \App\Traits\ChannelController;
  
  private function channelName(): string {
    return "protv";
  }
  private $url = "https://tvonline123.com/hls/protvhd.m3u8";
  private $tsUrl = "https://tvonline123.com/hls/";
  private $referer = "https://tvonline123.com/tvlive/any.html";
  
  private function baseUrl(): string {
    // other channels https://github.com/iptv-org/iptv/issues
    // https://rundle.deta.dev/hls/LIVE$ProTV.ro/6.m3u8/Level(19012010)?start=LIVE&end=END
//    https://tvonline123.com/tvlive/?url=protv-hd&s=204
//    return "https://cmero-ott-live.ssl.cdn.cra.cz/channels/cme-ro-voyo-news/playlist/rum/live_fullhd.m3u8?offsetSeconds=0&url=0";
//    return "https://thanos1.gamorax.cfd/porky/$path";
    return "https://www.tvonline123.com/hls/";
  }

  public function stream($channel) {
    // HbbTV feed: plain MPEG-TS over http (not HLS), so it plays in IPTV players like VLC but not in the browser
    return redirect("http://rpprod.hbbtv.cdn.cra.cz:8080/hbbTV/cme-ro-protv/live_fullhd?offsetSeconds=0");
  }

  public function ts($channel, $url) {
    $response = Http::withHeaders([
      "Referer" => $this->referer,
      "Origin" => $this->referer,
    ])->get($this->tsUrl . $url);

    return response($response->body());
  }
  
  public function part($channel, Request $request) {
    $url = $this->baseUrl() . urldecode($request->get('url'));
    
    return redirect($url);
  }
}
