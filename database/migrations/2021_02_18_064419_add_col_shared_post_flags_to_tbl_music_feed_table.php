<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColSharedPostFlagsToTblMusicFeedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_music_feed', function (Blueprint $table) {
            $table->integer('is_shared')->nullable()->default(0)->after('privacy');
            $table->integer('source_user_id')->nullable()->after('is_shared');
            $table->integer('source_post_id')->nullable()->after('source_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_music_feed', function (Blueprint $table) {
            //
        });
    }
}
