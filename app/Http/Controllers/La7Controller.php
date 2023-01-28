<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class La7Controller extends Controller {
    use \App\Traits\ChannelController;


    private function baseUrl(): string {
        return "https://d15umi5iaezxgx.cloudfront.net/#channel/CLN/HLS-B/Live_1280x720_.m3u8";
    }

    private function channelName(): string {
        return "la7";
    }

    public function stream($channel) {
        $link = $this->getChannelStreamLink($channel, false);

        return redirect($link);
    }
}
