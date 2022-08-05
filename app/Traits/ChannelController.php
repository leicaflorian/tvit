<?php

namespace App\Traits;

trait ChannelController {
  private abstract function baseUrl(): string;
  
  private abstract function channelName(): string;
  
  private function getChannelStreamLink($channel): string {
    return str_replace("#channel", $this->getChannelCode($channel), $this->baseUrl());
  }
  
  private function getChannelData($channel): array {
    $config = collect(config("channels")[$this->channelName()]);
    
    $toReturn = $config->first(function ($entry) use ($channel) {
      return $entry["id"] === strtolower($channel);
    });
    
    if ( !$toReturn) {
      abort(404);
    }
    
    return $toReturn;
  }
  
  private function getChannelCode($channel): string {
    return strtolower($this->getChannelData($channel)["code"]);
  }
}
