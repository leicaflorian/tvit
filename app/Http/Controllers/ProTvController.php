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
//    https://tvonline123.com/tvlive/?url=protv-hd&s=204
    return "https://cmero-ott-live.ssl.cdn.cra.cz/channels/cme-ro-voyo-news/playlist/rum/live_fullhd.m3u8?offsetSeconds=0&url=0";
  }

  public function stream($channel) {
    return $this->getChannelStreamLink($channel);
  }

  public function ts($channel, $url) {
    $response = Http::withHeaders([
      "Referer" => $this->referer,
      "Origin" => $this->referer,
    ])->get($this->tsUrl . $url);

    return response($response->body());
  }
}
