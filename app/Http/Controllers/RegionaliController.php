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

class RegionaliController extends Controller {
  use ChannelController;

  private function baseUrl(): string {
      //
  }

  private function channelName(): string {
    return "";
  }

  public function stream($channel) {
		$link = "";

		switch ($channel) {
			case "tele-norba":
				// $link = "https://router.xdevel.com/video2s976570-2117/stream/playlist_dvr.m3u8";
				// $link = "https://stream12.xdevel.com/video2s976570-2526/stream/playlist_dvr.m3u8";
				$link = "/proxy/video2s976570-2526/stream/playlist_dvr.m3u8?base_uri=" . urlencode("https://stream12.xdevel.com");
				break;
		}

		return redirect($link);
  }
}
