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

    // comedy play: https://stream1.antenaplay.ro/live/ComedyPlay/playlist.m3u8
    // Antena Monden: https://stream1.antenaplay.ro/live/AntenaMonden/playlist.m3u8
  }

  private function channelName(): string {
    return "antena";
  }

  public function stream($channel) {
    $links = [
      "antena1"       => "https://live4dai.antenaplay.ro/live2_sdi14/live2_sdi14_600k.m3u8",
      "comedy-play"   => "https://stream1.antenaplay.ro/live/ComedyPlay/playlist.m3u8",
      "antena-monden" => "https://stream1.antenaplay.ro/live/AntenaMonden/playlist.m3u8",
    ];

    if ( !isset($links[$channel])) {
      abort(404);
    }

    return redirect($links[$channel]);
  }

  public function ts($channel, $url) {
    $response = Http::withHeaders([
      "Referer" => $this->referer,
    ])->get($this->tsUrl . $url);

    return response($response->body());
  }
}
