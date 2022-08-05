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
    Schema::table('channels', function (Blueprint $table) {
      $table->string("provider_id")->nullable();
      $table->string("tvgLogo")->nullable();
      $table->string("tvgId")->nullable();
      $table->boolean("mpd")->default(false)->comment("Indicates that the channel must be provided as a MPD stream.");
    });
  }
  
  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::table('channels', function (Blueprint $table) {
      $table->removeColumn("provider_id");
      $table->removeColumn("tvgLogo");
      $table->removeColumn("tvgId");
      $table->removeColumn("mpd");
    });
  }
};
