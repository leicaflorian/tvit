<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use http\Client\Response;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class ChannelController extends Controller {
  /**
   * @param  string  $slug
   *
   * @return JsonResponse
   */
  public function show(string $slug): JsonResponse {
    $channel = Channel::where(['tvg_slug' => $slug])->first();
    
    if ( !$channel) {
      abort(404);
    }
    
    $channel->load(['programs' => function ($query) {
      $query->where("start", ">=", today())
        ->where("end", "<=", now()->addHours(24));
    }]);
  
    return response()->json($channel);
//    return view('channels.show', compact('channel'));
  }
}
