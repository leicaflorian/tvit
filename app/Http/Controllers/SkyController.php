<?php

namespace App\Http\Controllers;

use App\Classes\ProxiedHttp;
use App\Traits\WebserverOneController;
use \App\Traits\ChannelController;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SkyController extends Controller {
  use ChannelController;
  
  private function baseUrl(): string {
//        return "https://hlslive-web-gcdn-skycdn-it.akamaized.net/TACT/#channel/master.m3u8?hdnea=st=1639498341~exp=1702463400~acl=/*~hmac=c3c4c2de19ff0df4b4bb20587ce59af5232eadab2995f445b7506525413805dd";
    return "https://apid.sky.it/vdp/v1/getLivestream?id=#channel&isMobile=false";
  }
  
  private function channelName(): string {
    return "sky";
  }
  
  public function stream($channel) {
    $link = $this->getChannelStreamLink($channel, false);
    
    try {
      $data = ProxiedHttp::get("it", $link);
      
      return redirect($data["streaming_url"]);
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      
      return abort(Response::HTTP_BAD_REQUEST);
    }
    
  }
}
