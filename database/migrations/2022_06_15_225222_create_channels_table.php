<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('channels', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('tvg_name');
      $table->string('tvg_slug')->unique()->index();
      $table->string('tvg_code');
      $table->string('iptv_code');
      $table->string('logo_url_color');
      $table->string('logo_url_light');
      $table->integer('dtt_num');
      $table->string('group');
      $table->timestamps();
    });
  }
  
  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('channels');
  }
};
