<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsInstrumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_events_instrument', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id')->nullable();
            $table->integer('parent_event_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('instrument_id')->nullable();
            $table->integer('instrument_name')->nullable();
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
        Schema::dropIfExists('events_instrument');
    }
}
