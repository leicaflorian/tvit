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
				
		}
		
		return redirect($link);
	}
}

https://dplus-sport-live.akamai.prod-live.h264.io/primary/1/a73694de63294bbe82e604a52187174d/hdntl=exp=1703297142~acl=/primary/1/a73694de63294bbe82e604a52187174d/*~data=hdntl~hmac=0a23bd3e2f9af56107de13908d6ff72300548a5e09eea0e6a4e4e0f29b8e2f4a/index.m3u8?aws.manifestfilter=video_codec:h264;video_height:1-1080;video_dynamic_range:sdr;audio_codec:AACL&hdnts=exp=1703297142~acl=/primary/1/a73694de63294bbe82e604a52187174d/index.m3u8%3Faws.manifestfilter%3Dvideo_codec%3Ah264%3Bvideo_height%3A1-1080%3Bvideo_dynamic_range%3Asdr%3Baudio_codec%3AAACL*~hmac=52995339fcfe7157799eb171280369a6269bd317a3951bd2a26ca9d1d76d0e05
