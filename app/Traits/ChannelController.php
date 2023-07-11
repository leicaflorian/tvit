<?php

namespace App\Traits;

use App\Models\Channel;

trait ChannelController {
  private abstract function baseUrl(): string;
  
  private abstract function channelName(): string;
  
  private function getChannelStreamLink($channel, $toLowercase = true): string {
    return str_replace("#channel", $this->getChannelCode($channel, $toLowercase), $this->baseUrl());
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
  
  private function getChannelCode($channel, $toLowerCase = true): string {
    $str = $this->getChannelData($channel)["iptv_code"];
    
    if ($toLowerCase) {
      $str = strtolower($str);
    }
    
    return $str;
  }
  
  private function parseTsUrls($tsList, $url = "test", $urlParts = []): string {
    $pattern      = "/^[^#].*/m";
    $finalContent = preg_replace_callback($pattern, function ($match) use ($url, $urlParts) {
      $tsString = str_replace("\r", "", $match[0]);
      
      return url($url, [...$urlParts]) . "/" . $tsString;
    }, $tsList);
    
    return $finalContent;
  }
}
