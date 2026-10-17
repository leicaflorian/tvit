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
				$link = "https://streaming.aurora.enhanced.live/live/mpc/enl0eG1mLmVncmVzcy5mZDJ2eHI=/out/v1/IT_SIMULCAST/REALTIME/CMAF/index.m3u8";
				break;
			case "nove":
				$link = "https://streaming.aurora.enhanced.live/live/mpc/enl0eG1mLmVncmVzcy5mZDJ2eHI=/out/v1/IT_SIMULCAST/NOVE/CMAF/index.m3u8";
				break;
			case "dmax":
				$link = "https://streaming.aurora.enhanced.live/live/mpc/enl0eG1mLmVncmVzcy5mZDJ2eHI=/out/v1/IT_SIMULCAST/DMAX/CMAF/index.m3u8";
				break;
			case "k2":
				$link = "https://amg16146-wbdi-amg16146c6-samsung-it-1839.playouts.now.amagi.tv/playlist/amg16146-warnerbrosdiscoveryitalia-k2-samsungit/playlist.m3u8";
				break;
			case "warner-tv":
				$link = "https://streaming.aurora.enhanced.live/live/mpc/enl0eG1mLmVncmVzcy5mZDJ2eHI=/out/v1/IT_SIMULCAST/WARNERTV/CMAF/index.m3u8";
				break;
            case "giallo":
				$link = "https://streaming.aurora.enhanced.live/live/mpc/enl0eG1mLmVncmVzcy5mZDJ2eHI=/out/v1/IT_SIMULCAST/GIALLO/CMAF/index.m3u8";
				break;
            case "frisbee":
				$link = "https://amg16146-wbdi-amg16146c7-samsung-it-1840.playouts.now.amagi.tv/playlist/amg16146-warnerbrosdiscoveryitalia-frisbee-samsungit/playlist.m3u8";
				break;

		}

		return redirect($link);
	}
}
