<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ChannelController extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return View
   */
  public function index(): View {
    $channels = Channel::orderBy('dtt_num')->get();
    
    return view('channels.index', compact('channels'));
  }
  
  /**
   * @param  string  $slug
   *
   * @return View
   */
  public function show(string $slug): View {
    $channel = Channel::where(['tvg_slug' => $slug])->first();
    
    if ( !$channel) {
      abort(404);
    }
    
    $channel->load('programs')->where("start", ">=", today())
      ->where("end", "<=", now()->addHours(24));
    
    return view('channels.show', compact('channel'));
  }
  
  /**
   * Return the JSON list for all channels
   *
   * @param  bool  $returnCollection
   *
   * @return array|Collection
   */
  public function jsonList(bool $returnCollection = false): array|Collection {
    return Channel::orderBy('dtt_num')->get();
  }
  
  /**
   * Return the M3U8 list for all channels
   *
   * @param  Request  $request
   *
   * @return Response
   */
  public function m3u8List(Request $request): Response {
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
  
  public function epgList() {
    // TODO:: devo generare la lista xml durante i cron degli scraper e poi la mettere in cache
    // prechè troppo pesante e ci mette troppo a rispondere.
    // Devo anche recuperare le informazioni per ogni singolo programma
    
    // https://cesbo.com/en/latest/utils/xmltv-format
    
    $xml = new \SimpleXMLElement("<tv/>");
    $xml->addAttribute("date", now()->format("Ymd"));
    
    $channels = Channel::orderBy('dtt_num')->get();
    
    $channels->each(function (Channel $channel) use (&$xml) {
      $channelXml = $xml->addChild("channel");
      $channelXml->addAttribute("id", $channel->tvg_name);
      $channelXml->addChild("display-name", $channel->name)->addAttribute("lang", "it");
      $channelXml->addChild("icon")->addAttribute("src", $this->escapeString($channel->logo_url_color));
      $channelXml->addChild("url", $this->escapeString($channel->m3u8Link));
    });
    
    $channels->each(function (Channel $channel) use (&$xml) {
      $programs = $channel->programs()->where("start", ">=", today())->get();
      
      $programs->each(function (Program $program) use (&$xml, $channel) {
        $programXml = $xml->addChild("programme");
        $programXml->addAttribute("start", $program->start->format("YmdHis"));
        $programXml->addAttribute("stop", $program->end->format("YmdHis"));
        $programXml->addAttribute("channel", $channel->tvg_name);
        $programXml->addChild("title", $this->escapeString($program->title))->addAttribute("lang", "it");
        $programXml->addChild("desc", $this->escapeString($program->description))->addAttribute("lang", "it");
        $programXml->addChild("category", $this->escapeString($program->category))->addAttribute("lang", "it");
      });
    });
    
    return response($xml->asXML(), 200, [
      'Content-Type' => 'application/xml'
    ]);
  }
  
  
  private function escapeString($str) {
    return htmlspecialchars($str, ENT_XML1 | ENT_COMPAT, 'UTF-8');
  }
}
/*<programme start="20220809020000 +0000" stop="20220809040000 +0000" channel="ArenaSport1.rs">
<title lang="sr">PSG - Leipzig</title>
<desc lang="sr">LIGA ŠAMPIONA</desc>
<category lang="sr">Fudbal</category>
</programme>
*/
