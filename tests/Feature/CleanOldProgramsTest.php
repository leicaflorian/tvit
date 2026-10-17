<?php

namespace Tests\Feature;

use App\Jobs\CleanOldPrograms;
use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Models\Program;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleanOldProgramsTest extends TestCase {
  use RefreshDatabase;

  public function test_deletes_programs_ended_more_than_two_days_ago(): void {
    $this->channel();
    $old    = $this->program(now()->subDays(3));
    $recent = $this->program(now()->subDay());

    (new CleanOldPrograms())->handle();

    $this->assertModelMissing($old);
    $this->assertModelExists($recent);
  }

  public function test_is_scheduled_daily_before_the_guide_scraping(): void {
    $event = collect(app(Schedule::class)->events())
      ->first(fn($event) => $event instanceof CallbackEvent && $event->description === CleanOldPrograms::class);

    $this->assertNotNull($event, "CleanOldPrograms is not scheduled");
    $this->assertSame("30 2 * * *", $event->expression);
    $this->assertSame("Europe/Rome", $event->timezone);
  }

  private function channel(): void {
    $group = ChannelGroup::create(["name" => "Rai", "slug" => "rai", "scrapper" => "SuperGuidaTv"]);

    $channel = new Channel([
      "name"           => "Rai 1",
      "tvg_slug"       => "rai-1",
      "tvg_name"       => "Rai 1",
      "tvg_code"       => "217",
      "iptv_code"      => "2606803",
      "logo_url_color" => "",
      "logo_url_light" => "",
      "dtt_num"        => 1,
      "group"          => "rai",
    ]);
    $channel->group_id = $group->id;
    $channel->save();
  }

  private function program($end): Program {
    return Program::create([
      "channel_tvg_slug" => "rai-1",
      "start"            => $end->copy()->subHour(),
      "end"              => $end,
      "title"            => "Program",
    ]);
  }
}
