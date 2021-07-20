<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_events', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id');
            $table->double('duration', 18, 3);
            $table->string('event_start', 255);
            $table->string('event_end', 255);
            $table->string('date_start', 255);
            $table->string('date_end', 255);
            $table->string('event_type', 100);
            $table->string('instrument_ids', 255);
            $table->longText('instrument_names');
            $table->string('student_ids', 255);
            $table->longText('student_names');
            $table->string('category', 50);
            $table->longText('descriptions');
            $table->string('app_type', 100);
            $table->string('location', 255);
            $table->string('virtual_location', 255);
            $table->string('virtual_link', 255);
            $table->integer('spaces')->default(1);
            $table->double('price', 18, 3)->default(0);
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
      Schema::dropIfExists('events');
    }
}
