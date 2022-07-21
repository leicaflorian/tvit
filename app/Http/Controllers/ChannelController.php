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
      $program->end = Carbon::parse($program->end)->setTimezone("Europe/Berlin");
    });
    
    return view('channels.show', compact('channel'));
  }
}
