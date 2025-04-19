<?php

namespace App\Http\Controllers;

use App\Traits\WebserverOneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DiscoveryController extends Controller {
  use \App\Traits\ChannelController, WebserverOneController;

  private function channelName(): string {
    return "discovery";
  }

	public function stream($channel) {
		$link = "";

		switch ($channel) {
			case "real-time":
				$link = "https://amg16146-wbdi-amg16146c2-samsung-it-1835.playouts.now.amagi.tv/playlist/amg16146-warnerbrosdiscoveryitalia-realtime-samsungit/playlist.m3u8";
				break;
			case "nove":
				$link = "https://amg16146-wbdi-amg16146c1-samsung-it-1831.playouts.now.amagi.tv/playlist/amg16146-warnerbrosdiscoveryitalia-nove-samsungit/playlist.m3u8";
				break;
			case "dmax":
				$link = "https://amg16146-wbdi-amg16146c8-samsung-it-1841.playouts.now.amagi.tv/playlist/amg16146-warnerbrosdiscoveryitalia-dmax-samsungit/playlist.m3u8";
				break;
			case "k2":
				$link = "https://amg16146-wbdi-amg16146c6-samsung-it-1839.playouts.now.amagi.tv/playlist/amg16146-warnerbrosdiscoveryitalia-k2-samsungit/playlist.m3u8";
				break;
			case "warner-tv":
				$link = "https://amg16146-wbdi-amg16146c4-samsung-it-1837.playouts.now.amagi.tv/playlist/amg16146-warnerbrosdiscoveryitalia-warnertv-samsungit/playlist.m3u8";
				break;
            case "giallo":
				$link = "https://amg16146-wbdi-amg16146c5-samsung-it-1838.playouts.now.amagi.tv/playlist/amg16146-warnerbrosdiscoveryitalia-giallo-samsungit/playlist.m3u8";
				break;
            case "frisbee":
				$link = "https://amg16146-wbdi-amg16146c7-samsung-it-1840.playouts.now.amagi.tv/playlist/amg16146-warnerbrosdiscoveryitalia-frisbee-samsungit/playlist.m3u8";
				break;

		}

		return redirect($link);
	}
}
