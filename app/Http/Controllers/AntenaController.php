<?php

namespace App\Http\Controllers;

use http\Encoding\Stream;
use Illuminate\Http\Request;

class AntenaController extends Controller {
  use \App\Traits\ChannelController;
  
  private function baseUrl(): string {
    https://github.com/iptv-org/iptv/issues
    return "https://rundle.deta.dev/hls/LIVE\$#channel/6.m3u8/Level(19012010)?start=LIVE&end=END";
  }
  
  private function channelName(): string {
    return "antena";
  }
  
  public function stream($channel) {
    $link = $this->getChannelStreamLink($channel);
    
    return response()->redirectTo($link);
  }
}
