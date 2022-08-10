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
  
  
}
