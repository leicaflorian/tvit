<?php

namespace App\Jobs\Scrappers;

use App\Jobs\ScrapProgramDetail;
use App\Models\Channel;
use App\Models\Program;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuperGuidaTvScrapper {
  const BASE_URL = "https://www.superguidatv.it";

  /**
   * @throws Exception
   */
  public static function scrap(Channel $channel) {
    $html = Http::withHeaders(["User-Agent" => "Mozilla/5.0"])
      ->timeout(30)
      ->get($channel->rawGuideLink)
      ->throw()
      ->body();

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // Each program is a <li style="--category-color:..."> element
    $timelineEntries = $xpath->query('//li[starts-with(@style, "--category-color")]');

    // The website uses the Rome TZ
    $currentDate   = Carbon::today("Europe/Rome");
    $lastStartTime = null;
    $saved         = 0;

    foreach ($timelineEntries as $timelineEntry) {
      $hourEl  = $xpath->query('.//p[' . self::hasClass("sgtv-w-20") . ']', $timelineEntry)->item(0);
      $infoEls = $xpath->query('.//p[' . self::hasClass("sgtv-truncate") . ']', $timelineEntry);

      if ( !$hourEl || $infoEls->length < 2) {
        continue;
      }

      $hour           = trim($hourEl->textContent);
      $title          = trim($infoEls->item(0)->textContent);
      $categoryString = trim($infoEls->item(1)->textContent);

      if ( !preg_match("/^(\d{1,2}):(\d{2})$/", $hour, $hourMatch)) {
        continue;
      }

      // "Categoria (NN')"
      if (preg_match("/^(.*?)\s*\((\d+)'\)$/u", $categoryString, $match)) {
        $category = trim($match[1]);
        $duration = (int) $match[2];
      } else {
        $category = $categoryString;
        $duration = 0;
      }

      $startTime = (new Carbon($currentDate))->setTime((int) $hourMatch[1], (int) $hourMatch[2]);

      // The schedule crosses midnight: when a start time goes backwards, move to the next day
      if ($lastStartTime && $startTime->lt($lastStartTime)) {
        $currentDate->addDay();
        $startTime->addDay();
      }

      $lastStartTime = new Carbon($startTime);
      $endTime       = (new Carbon($startTime))->addMinutes($duration);

      $newProgram = new Program([
        "channel_tvg_slug" => $channel->tvg_slug,
        "start"            => $startTime->setTimezone("utc"),
        "end"              => $endTime->setTimezone("utc"),
        "title"            => $title,
        "category"         => $category,
        "link"             => self::detailLink($xpath, $timelineEntry),
      ]);

      $res = Program::updateOrCreate([
        'start'            => $newProgram->start,
        'end'              => $newProgram->end,
        'channel_tvg_slug' => $channel->tvg_slug,
      ], $newProgram->toArray());

      $saved++;

      if ($newProgram->link) {
        // if there is a link, scrap the program detail
        ScrapProgramDetail::dispatch($res)->onQueue("scrap-program-detail")->delay(now()->addSeconds(rand(1, 100)));
      }
    }

    if ($saved === 0) {
      $message = "SuperGuidaTv: no programs found for channel \"" . $channel->name . "\" (" . $channel->rawGuideLink . "). The website markup may have changed.";

      Log::error($message);
      throw new Exception($message);
    }

    Log::info("SuperGuidaTv: saved " . $saved . " programs for channel \"" . $channel->name . "\"");
  }

  /**
   * The detail link is not an <a>, it's built from the data-* attributes of the "hidden-link" span.
   */
  private static function detailLink(DOMXPath $xpath, DOMElement $timelineEntry): ?string {
    $linkEl = $xpath->query('.//*[@data-epg-type="hidden-link"][@data-slug][@data-id]', $timelineEntry)->item(0);

    if ( !$linkEl) {
      return null;
    }

    $base = rtrim($linkEl->getAttribute("data-base") ?: "/dettaglio-programma", "/");

    return self::BASE_URL . $base . "/" . $linkEl->getAttribute("data-slug") . "/" . $linkEl->getAttribute("data-id") . "/";
  }

  private static function hasClass(string $class): string {
    return 'contains(concat(" ", normalize-space(@class), " "), " ' . $class . ' ")';
  }
}
