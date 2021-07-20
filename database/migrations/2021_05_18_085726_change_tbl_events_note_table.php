<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTblEventsNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_events_note', function (Blueprint $table) {
            $table->integer('event_id')->nullable()->change();
            $table->integer('user_id')->nullable()->change();
            $table->string('type', 100)->nullable()->change();
            $table->longText('notes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_events_note', function (Blueprint $table) {
            //
        });
    }
}
