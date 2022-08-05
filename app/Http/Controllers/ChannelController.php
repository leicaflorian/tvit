<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Http\Request;

class ChannelController extends Controller {
  public function index() {
    $channels = Channel::all();
    
    return view('channels.index', compact('channels'));
  }
  
  public function show(Channel $channel) {
    $channel->load('programs');
    
    $channel->programs->each(function ($program) {
      $program->start = Carbon::parse($program->start)->setTimezone("Europe/Berlin");
      $program->end   = Carbon::parse($program->end)->setTimezone("Europe/Berlin");
    });
    
    return view('channels.show', compact('channel'));
  }
  
  /**
   * Show the m3u8 list
   *
   * @return void
   */
  public function jsonList() {
    $channels = collect(array_merge(...array_values(config('channels'))));
    $sorted   = $channels->sortBy('dvbNum');
    
    return array_values($sorted->toArray());
  }
  
  public function list() {
    $channels = collect(array_merge(...array_values(config('channels'))));
    $sorted   = $channels->sortBy('dvbNum');
    $content  = ['#EXTM3U'];
    
    $sorted->each(function ($channel) use (&$content) {
      $tvgId   = $channel['tvgId'] ?: $channel['id'] ?: 'EPG N/A';
      $tvgName = $channel["tvgName"] ?: $channel["name"] ?: '';
      
      $extChannel = ["#EXTINF:{$channel['dvbNum']}",
        "tvg-id=\"$tvgId\"",
        "tvg-name=\"$tvgName\"",
        "tvg-shift=\"\"",
        "tvg-logo=\"{$this->getLogoUrl($channel)}\"",
        "radio=\"\"",
        "group-title=\"{$channel["groupTitle"]}\"",
        ", {$channel["name"]}"
      ];
      
      $content[] = implode(" ", $extChannel);
      $content[] = env("APP_URL") . "/{$channel["groupTitle"]}/{$channel["id"]}/stream.m3u8";
    });
    
    return response(implode("\n", $content), 200, [
      'Content-Type' => 'application/x-mpegURL'
    ]);
  }
  
  private function getLogoUrl(
    $channel) {
    $logo = "https://api.superguidatv.it/v1/channels/#id/logo?width=120&theme=dark";
    
    if (str_starts_with($channel["tvgLogo"], "http")) {
      return $channel["tvgLogo"];
    }
    
    return str_replace("#id", $channel["tvgLogo"], $logo);
  }
}
