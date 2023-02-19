<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class EpgController extends Controller {
  
  /**
   * @return Collection
   */
  public function jsonList(): Collection {
    $channels = Channel::orderBy('dtt_num')->get();
    
    return $channels->map(function (Channel $channel) use (&$toReturn) {
      $channelJson = ["id"      => $channel->tvg_slug,
                      "name"    => $channel->name,
                      "epgName" => $channel->tvg_name,
                      "logo"    => $channel->logo_url_color,
                      "m3uLink" => $channel->m3u8Link,
      ];
      
      $programs = $channel->programs()->where("start", ">=", today())->get();
      
      $channelJson["programs"] = $programs->map(function (Program $program) use (&$channel) {
        return [
          "start"       => $program->start,
          "end"         => $program->end,
          "title"       => $program->title,
          "description" => $program->description,
          "category"    => $program->category,
          "poster"      => $program->cover_img,
          "channel"     => $program->channel_tvg_slug,
        ];
      });
      
      return $channelJson;
    });
  }
  
  /**
   * @return Response
   */
  public function xmlList() {
    // https://cesbo.com/en/latest/utils/xmltv-format
    
    $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<tv date="' . now()->format("Ymd") . '">'];
    
    $channels = Channel::orderBy('dtt_num')->get();
    
    $channels->each(function (Channel $channel) use (&$xml) {
      $channelXml   = ['<channel id="' . $this->escapeString($channel->tvg_name) . '">'];
      $channelXml[] = '<display-name lang="it">' . $this->escapeString($channel->name) . '</display-name>';
      $channelXml[] = '<icon src="' . $this->escapeString($channel->logo_url_color) . '"/>';
      $channelXml[] = '<url>' . $this->escapeString($channel->m3u8Link) . '</url>';
      
      $xml[] = implode("", $channelXml) . '</channel>';
    });
    
    $channels->each(function (Channel $channel) use (&$xml) {
      $programs = $channel->programs()->where("start", ">=", today())->get();
      
      $programs->each(function (Program $program) use (&$xml, $channel) {
        $programXml   = ['<programme start="' . $program->start->format("YmdHis") . '" stop="' . $program->end->format("YmdHis") . '" channel="' . $this->escapeString($channel->tvg_name) . '">'];
        $programXml[] = '<title lang="it">' . $this->escapeString($program->title) . '</title>';
        $programXml[] = '<desc lang="it">' . ($program->description ? $this->escapeString($program->description) : "-") . '</desc>';
        $programXml[] = '<category lang="it">' . $this->escapeString($program->category) . '</category>';
        
        $xml[] = implode("", $programXml) . '</programme>';
      });
    });
    
    return response(implode("\n", $xml) . "</tv>\n", 200, [
      'Content-Type' => 'application/xml'
    ]);
  }
  
  
  private function escapeString($str) {
    return htmlspecialchars($str, ENT_XML1 | ENT_COMPAT, 'UTF-8');
  }
}
