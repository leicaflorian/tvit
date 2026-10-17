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
		// Sky video platform livestream ids. The returned url carries a token valid for ~5 minutes,
		// so it must be requested on every play instead of being hardcoded.
		$liveIds = [
			"tv8"   => 7,
			"cielo" => 2,
		];

		if ( !isset($liveIds[$channel])) {
			abort(404);
		}

		$link = Http::get("https://video.sky.it/api/v1/getLivestream", [
			"id"       => $liveIds[$channel],
			"isMobile" => "false",
		])->json("streaming_url");

		if ( !$link) {
			abort(502);
		}

		return redirect($link);
  }
}
