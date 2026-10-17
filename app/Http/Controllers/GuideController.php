<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GuideController extends Controller {
  // The guide days follow the Italian broadcast day, programs are stored in UTC
  private const TIMEZONE = "Europe/Rome";

  private const MAX_DAYS = 7;

  /**
   * Grid guide with all channels' programs for a single day.
   *
   * @param  Request  $request
   *
   * @return \Inertia\Response
   */
  public function index(Request $request): \Inertia\Response {
    $today = now(self::TIMEZONE)->startOfDay();
    $days  = $this->availableDays($today);

    $day = $today;

    if ($request->filled("day") && in_array($request->get("day"), $days)) {
      $day = Carbon::createFromFormat("Y-m-d", $request->get("day"), self::TIMEZONE)->startOfDay();
    }

    $dayStart = $day->copy()->utc();
    $dayEnd   = $day->copy()->addDay()->utc();

    $programs = Program::where("end", ">", $dayStart)
      ->where("start", "<", $dayEnd)
      ->orderBy("start")
      ->get()
      ->map(fn(Program $program) => [
        "id"               => $program->id,
        "channel_tvg_slug" => $program->channel_tvg_slug,
        "title"            => $program->title,
        "category"         => $program->category,
        "description"      => $program->description ? Str::limit($program->description, 280) : null,
        "start_tz"         => $program->start_tz,
        "end_tz"           => $program->end_tz,
      ])
      ->groupBy("channel_tvg_slug");

    return Inertia::render("Guide/Index", [
      "channels" => Channel::orderBy("dtt_num")->get(),
      "programs" => $programs,
      "day"      => $day->format("Y-m-d"),
      "days"     => $days,
      "dayStart" => $dayStart,
      "dayEnd"   => $dayEnd,
    ]);
  }

  /**
   * Days from today with programs for at least a third of the channels,
   * so the day chips never lead to an almost empty grid.
   *
   * @param  Carbon  $today
   *
   * @return string[]
   */
  private function availableDays(Carbon $today): array {
    $minChannels = max(1, intdiv(Channel::count(), 3));
    
    $days = Program::where("start", ">=", $today->copy()->utc())
      ->where("start", "<", $today->copy()->addDays(self::MAX_DAYS)->utc())
      ->get(["start", "channel_tvg_slug"])
      ->groupBy(fn(Program $program) => Carbon::parse($program->start, "UTC")->setTimezone(self::TIMEZONE)->format("Y-m-d"))
      ->filter(fn($programs) => $programs->unique("channel_tvg_slug")->count() >= $minChannels)
      ->keys()
      ->sort()
      ->values()
      ->all();
    
    $todayKey = $today->format("Y-m-d");
    
    return in_array($todayKey, $days) ? $days : array_merge([$todayKey], $days);
  }
}
