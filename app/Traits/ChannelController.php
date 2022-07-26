<?php

namespace App\Traits;

use App\Models\Channel;

trait ChannelController {
  private abstract function baseUrl(): string;
  
  private abstract function channelName(): string;
  
  private function getChannelStreamLink($channel): string {
    return str_replace("#channel", $this->getChannelCode($channel), $this->baseUrl());
  }
  
  private function getChannelData($channel): Channel {
    if (isset($this->localMap)) {
      $config = [...array_filter($this->localMap, fn($el) => $el["id"] === $channel)];
      
      if (count($config) > 0) {
        $config = new Channel($config[0]);
      }
    } else {
      $config = Channel::where("tvg_slug", $channel)->first();
    }
    
    if ( !$config) {
      abort(404);
    }
//
//    $toReturn = $config->first(function ($entry) use ($channel) {
//      return $entry["id"] === strtolower($channel);
//    });
    
    /*if ( !$toReturn) {
      abort(404);
    }*/
    
    return $config;
  }
  
  private function getChannelCode($channel): string {
    return strtolower($this->getChannelData($channel)["iptv_code"]);
  }
}
