<?php

use Adrianorosa\GeoLocation\GeoLocation;
use App\Http\Controllers\TestController;
use GuzzleHttp\Client;
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
  return redirect()->route('channels.index');
});

Route::get("myip", function () {
  $userIp  = request()->ip();
  $details = GeoLocation::lookup($userIp);

  dump(config("app.app_url"));
  dump($userIp, $details);
});

Route::name("channels.")
  ->prefix("channels")
  ->group(function () {
    Route::get('/', [\App\Http\Controllers\ChannelController::class, 'index'])->name('index');
  });

Route::get('/iptv/list.m3u', [\App\Http\Controllers\IptvController::class, 'm3uList']);
Route::get('/iptv/list.json', [\App\Http\Controllers\IptvController::class, 'jsonList']);

Route::get('/epg/list.xml', [\App\Http\Controllers\EpgController::class, 'xmlList']);
Route::get('/epg/list.json', [\App\Http\Controllers\EpgController::class, 'jsonList']);

Route::name("mediaset.")
  ->prefix("mediaset")
  ->group(function () {
    Route::get("/{channel}/stream.m3u8", [\App\Http\Controllers\MediasetController::class, "stream"])->name("stream");
    Route::get("/{channel}/{stream}/streamPlaylist.m3u8", [\App\Http\Controllers\MediasetController::class, "streamPlaylist"])->name("streamPlaylist");
    Route::get("/{channel}/{stream}/{any}", [\App\Http\Controllers\MediasetController::class, "ts"])
      ->where('any', '.*')->name("ts");
  });


Route::name("rai.")
  ->prefix("rai")
  ->group(function () {
    Route::get("/{channel}/stream.m3u8", [\App\Http\Controllers\RaiController::class, "stream"])->name("stream");
    Route::get("/{channel}/{any}", [\App\Http\Controllers\RaiController::class, "ts"])
      ->where('any', '.*')->name("ts");
  });

Route::get("/raiplay/{channel}/stream.m3u8", [\App\Http\Controllers\RaiPlayController::class, "stream"])->name("raiplay.stream");
Route::get("/raiplay/{channel}/chunks/{any?}", [\App\Http\Controllers\RaiPlayController::class, "chunks"])
  ->where('any', '.*')->name("raiplay.chunk");

Route::name("sky.")
  ->prefix("sky")
  ->group(function () {
    Route::get("/{channel}/stream.m3u8", [\App\Http\Controllers\SkyController::class, "stream"])->name("stream");
    Route::get("/{channel}/{any}", [\App\Http\Controllers\SkyController::class, "ts"])
      ->where('any', '.*')->name("ts");
  });

Route::name("discovery.")
  ->prefix("discovery")
  ->group(function () {
    Route::get("/{channel}/stream.m3u8", [\App\Http\Controllers\DiscoveryController::class, "stream"])->name("stream");
    Route::get("/{channel}/{any}", [\App\Http\Controllers\DiscoveryController::class, "ts"])
      ->where('any', '.*')->name("ts");
  });

Route::name("protv.")
  ->prefix("protv")
  ->group(function () {
    Route::get("/{channel}/stream.m3u8", [\App\Http\Controllers\ProTvController::class, "stream"]);
    Route::get("/{channel}/part", [\App\Http\Controllers\ProTvController::class, "part"]);
  });

Route::name("antena.")
  ->prefix("antena")
  ->group(function () {
    Route::get("/{channel}/stream.m3u8", [\App\Http\Controllers\AntenaController::class, "stream"]);
    Route::get("/{channel}/{any}", [\App\Http\Controllers\AntenaController::class, "ts"])
      ->where('any', '.*')->name("ts");
  });

Route::name("la7.")
  ->prefix("la7")
  ->group(function () {
    Route::get("/{channel}/stream.m3u8", [\App\Http\Controllers\La7Controller::class, "stream"]);
  });

Route::name("plutotv.")
  ->prefix("plutotv")
  ->group(function () {
    Route::get("/{channel}/stream.m3u8", [\App\Http\Controllers\PlutoTvController::class, "stream"]);
    Route::get("/{channel}/{any}", [\App\Http\Controllers\PlutoTvController::class, "ts"])
      ->where('any', '.*')->name("ts");
  });


Route::name("regionali.")
    ->prefix("regionali")
    ->group(function () {
        Route::get("/{channel}/stream.m3u8", [\App\Http\Controllers\RegionaliController::class, "stream"]);
    });

Route::any('/proxy/{path}', [\App\Http\Controllers\ProxyController::class, "proxy"])->where('path', '.*'); // required to allow $path to catch all sub-path
