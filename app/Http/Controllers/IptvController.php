<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class IptvController extends Controller {
  /**
   * Return the JSON list for all channels
   *
   * @return Collection<Channel>
   */
  public function jsonList(): Collection {
    return Channel::orderBy('dtt_num')->get();
  }
  
  /**
   * Return the M3U8 list for all channels
   *
   * @param  Request  $request
   *
   * @return Response
   */
  public function m3uList(Request $request): Response {
    $logoVersion = $request->input('logoVersion', 'dark');
    $channels    = Channel::orderBy('dtt_num')->get();
    $content     = ['#EXTM3U'];
    
    $channels->each(function (Channel $channel) use (&$content, $logoVersion) {
      $logoUrl = $logoVersion === "light" ? $channel->logo_url_light : $channel->logo_url_color;
      
      $extChannel = ["#EXTINF:{$channel->dtt_num}",
        "tvg-id=\"$channel->tvg_slug\"",
        "tvg-name=\"$channel->tvg_name\"",
        "tvg-shift=\"\"",
        "tvg-logo=\"{$logoUrl}\"",
        "radio=\"\"",
        "group-title=\"{$channel->group}\",",
        "{$channel["name"]}"
      ];
      
      $content[] = implode(" ", $extChannel);
      $content[] = $channel->m3u8Link;
    });
    
    return response(implode("\n", $content))->withHeaders([
      'Content-Type'        => 'application/x-mpegURL',
      'Content-Disposition' => 'attachment; filename="channels.m3u"'
    ]);
  }
}
