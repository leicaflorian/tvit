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
use Inertia\Inertia;

class ChannelController extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return \Inertia\Response
   */
  public function index(): \Inertia\Response {
    $channels = Channel::orderBy('dtt_num')->get();
    
    return Inertia::render('Channels/Index', [
      'channels' => $channels,
    ]);
//    return view('channels.index', compact('channels'));
  }
  
  /**
   * @param  string  $slug
   *
   * @return \Inertia\Response
   */
  public function show(string $slug): \Inertia\Response {
    $channel = Channel::where(['tvg_slug' => $slug])->first();
    
    if ( !$channel) {
      abort(404);
    }
    
    $channel->load('programs')->where("start", ">=", today())
      ->where("end", "<=", now()->addHours(24));
    
    return Inertia::render('Channels/Show', [
      'channel' => $channel,
    ]);
//    return view('channels.show', compact('channel'));
  }
  
  
}
