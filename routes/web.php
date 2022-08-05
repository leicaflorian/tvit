<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('welcome');
});


Route::get('/channels', [\App\Http\Controllers\ChannelController::class, 'index'])->name('channels.index');
Route::get('/channels/iptv', [\App\Http\Controllers\ChannelController::class, 'iptv'])->name('channels.iptv');
Route::get('/channels/{channel}', [\App\Http\Controllers\ChannelController::class, 'show'])->name('channels.show');

Route::get("/test.m3u8", [TestController::class, "index"])->name("tests.index");
Route::get("/m3u8/test/{stream}/{list}", [TestController::class, "show"])->name("tests.show");
Route::get("/m3u8/test/ts/{stream}/{any}", [TestController::class, "ts"])
  ->where('any', '.*')->name("tests.ts");

Route::get("/mediaset/{channel}/stream.m3u8", [\App\Http\Controllers\MediasetController::class, "stream"])->name("mediaset.stream");
Route::get("/mediaset/{channel}/{stream}/streamPlaylist.m3u8", [\App\Http\Controllers\MediasetController::class, "streamPlaylist"])->name("mediaset.streamPlaylist");
Route::get("/mediaset/{channel}/{stream}/{any}", [\App\Http\Controllers\MediasetController::class, "ts"])
  ->where('any', '.*')->name("mediaset.ts");

Route::get("/rai/{channel}/stream.m3u8", [\App\Http\Controllers\RaiController::class, "stream"])->name("rai.stream");
Route::get("/rai/{channel}/{any}", [\App\Http\Controllers\RaiController::class, "ts"])
  ->where('any', '.*')->name("rai.ts");
