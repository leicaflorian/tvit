<?php

namespace App\Http\Controllers;

use App\Traits\WebserverOneController;
use \App\Traits\ChannelController;
use Illuminate\Http\Request;

class SkyController extends Controller {
    use ChannelController;

    private function baseUrl(): string {
        return "https://hlslive-web-gcdn-skycdn-it.akamaized.net/TACT/#channel/master.m3u8?hdnea=st=1639498341~exp=1702463400~acl=/*~hmac=c3c4c2de19ff0df4b4bb20587ce59af5232eadab2995f445b7506525413805dd";
    }

    private function channelName(): string {
        return "sky";
    }

    public function stream($channel) {
        $link = $this->getChannelStreamLink($channel, false);

        return redirect($link);
    }
}
