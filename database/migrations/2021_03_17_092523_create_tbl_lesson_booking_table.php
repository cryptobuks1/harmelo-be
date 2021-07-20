<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblLessonBookingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_lesson_booking', function (Blueprint $table) {
            $table->id();
            $table->integer('instructor_id');
            $table->integer('enrollee_id');
            $table->string('ddate');
            $table->string('time');
            $table->integer('instrument_id')->nullable();
            $table->integer('status')->default(0)->nullable();
            $table->integer('is_delete')->default(0)->nullable();
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
        Schema::dropIfExists('tbl_lesson_booking');
    }
}
