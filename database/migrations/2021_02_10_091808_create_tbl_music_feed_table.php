<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMusicFeedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_music_feed', function (Blueprint $table) {
            $table->id();
            $table->string('caption')->nullable();
            $table->integer('is_album');
            $table->integer('track_id');
            $table->integer('user_id');
            $table->integer('privacy');
            $table->integer('producer_id')->nullable();
            $table->integer('is_delete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_music_feed');
    }
}
