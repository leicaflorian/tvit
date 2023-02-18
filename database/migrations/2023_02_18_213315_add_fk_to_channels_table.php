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
      $table->unsignedBigInteger('group_id')
        ->after("group")
        ->nullable();
      
      $table->foreign('group_id')
        ->references('id')
        ->on('channel_groups');
    });
  }
  
  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::table('channels', function (Blueprint $table) {
      $table->dropForeign(['channels_group_id_foreign']);
      $table->dropColumn('group_id');
    });
  }
};
