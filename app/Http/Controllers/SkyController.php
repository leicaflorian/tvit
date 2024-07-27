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
		$link = "";
		
		switch ($channel) {
			case "cielo":
				$link = "https://hlslive-web-gcdn-skycdn-it.akamaized.net/TACT/11219/cieloweb/master.m3u8?hdnea=st=1701861650~exp=1765449000~acl=/*~hmac=84c9f3f71e57b13c3a67afa8b29a8591ea9ed84bf786524399545d94be1ec04d";
				break;
			case "tv8":
				$link = "https://hlslive-web-gcdn-skycdn-it.akamaized.net/TACT/11223/tv8web/master.m3u8?hdnea=st=1701861650~exp=1765449000~acl=/*~hmac=84c9f3f71e57b13c3a67afa8b29a8591ea9ed84bf786524399545d94be1ec04d";
				break;
		}
		
		return redirect($link);
  
  }
}
