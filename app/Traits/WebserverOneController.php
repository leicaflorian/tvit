<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;
use Symfony\Component\HttpFoundation\Response;
use function Psy\debug;

trait WebserverOneController {
  private $referer = "https://y0r90gl9c89ljx.opposepresent.net/";
  
  private function baseUrl(): string {
//    return "https://y0r90gl9c89ljx.opposepresent.net/embed/#channel";
    return "https://jlev8mvvawek39.opposepresent.net/embed/#channel";
    
  }
  
  private function decryptFnE($c, $a) {
    $fromBase  = 10;
    $firstNum  = ($c < $a ? '' : $this->decryptFnE(intval($c / $a), $a));
    $c         = $c % $a;
    $secondNum = ($c > 35 ? chr($c + 29) : base_convert($c, $fromBase, 36));

//      echo "c: $c c36: " . base_convert($c, $fromBase, 36) . " first: $firstNum  second: $secondNum sum: " . ($firstNum . $secondNum);
    
    return $firstNum . $secondNum;
  }
  
  private function decrypt($p, $a, $c, $k, $e, $d) {
    if (true) {
      while ($c--) {
        $key = $this->decryptFnE($c, $a);
        
        $d[$key] = $k[$c] ?? $this->decryptFnE($c, $a);
      }
      
      
      $k = [function ($e) use ($d) {
        $key = $e[0];
        $key = is_numeric($key) ? intval($key) : $key;
        
        try {
          return $d[$key];
        } catch (\Exception $exception) {
          
          return "";
        }
      }];
      
      $e = function () {
        return '\\w+';
      };
      
      $c = 1;
    };
    
    while ($c--) {
      if ($k[$c]) {
        $p = preg_replace_callback("/\\b{$e($c)}\\b/", $k[$c], $p);
      }
    }
    
    return $p;
  }
  
  private function getListUrl($link) {
    
    $result = Http::withHeaders([
      "Referer" => "https://webserver.one/Embed/Rai_2_Online.php"
    ])->get($link);
    
    if ($result->ok()) {
      // la risposta contiene una cosa del genere.
      $html = $result->body();
      
      $domOptions = new Options();
      $domOptions->setRemoveScripts(false);
      
      $dom = new Dom();
      $dom->setOptions($domOptions);
      $dom->loadStr($html);
      $scripts = $dom->find('script');
      
      foreach ($scripts as $script) {
        $content = $script->text;
        
        if (Str::contains($content, "eval")) {
          preg_match("/return p.*\.split/", $content, $m);
          $content = str_replace(["return p}(", ".split"], "", $m[0]);
          
          
          $preg_match      = preg_match("/('.*'),(\d{2,}),(\d{2,}),('.*)/", $content, $m);
          $decryptedString = $this->decrypt(
            $m[1],
            $m[2],
            $m[3],
            explode("|", $m[4],),
            0,
            []
          );
          
          if (str_contains($decryptedString, "http")) {
            preg_match("/src=\".*\";/", $decryptedString, $m);
            $link = str_replace(["src=\"", "\";"], "", $m[0]);
            $link = str_replace("?=", "?s=", $link);
            $link = str_replace("&=", "&e=", $link);
            
            return $link;
          }
        }
      }
    }
    
    Log::error($result->reason());
    
    return abort(Response::HTTP_BAD_REQUEST);
  }
  
  public function stream($channel) {
    $link    = $this->getChannelStreamLink($channel);
    $listUrl = $this->getListUrl($link);
    
    Log::debug($link);
    Log::debug($listUrl);
    
    $result = Http::withHeaders([
      "Referer" => "https://y0r90gl9c89ljx.opposepresent.net/"
    ])->get($listUrl);
    
    Log::debug($result->body());
    
    
    // si sostituisce l'ultima parte dell'url con il ts
    if ($result->ok()) {
      $data = $result->body();
      
      // devo sostituire i vari link con un mio link che poi farà il proxy delle chiamate
      $pattern      = "/^[0-9].*/m";
      $finalContent = preg_replace_callback($pattern, function ($match) use ($channel) {
        $tsString = str_replace("\r", "", $match[0]);
        
        return route("rai.ts", ["channel" => $channel, "any" => $tsString]);
      }, $data);
      
      return response($finalContent)->header("Content-Type", "application/vnd.apple.mpegurl");
    }
    
    Log::error($result->status());
    Log::error($result->reason());
    
    return abort(Response::HTTP_BAD_REQUEST);
  }
  
  public function ts($channel, $page) {
    $link = "https://sk4snlj0w3wp1g.cdnexpress63.net:8443/hls/$page";
    
    $result = Http::withHeaders([
      "Referer" => "https://y0r90gl9c89ljx.opposepresent.net/"
    ])->get($link);
    
    if ($result->ok()) {
      $data = $result->body();
      
      return response($data)->header("Content-Type", "video/MP2T");
    }
  
    Log::error($result->reason());
    return abort(Response::HTTP_BAD_REQUEST);
  }
}
