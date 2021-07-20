<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsRecurringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_recurring', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('event_id')->nullable();
            $table->integer('is_recurring')->default(0)->nullable();
            $table->string('repeat', 100)->nullable();
            $table->integer('hourly_recur')->nullable();
            $table->integer('daily_recur')->nullable();
            $table->integer('daily_recur_weekdays')->default(0)->nullable();
            $table->integer('weekly_recur')->nullable();
            $table->integer('monday')->default(0)->nullable();
            $table->integer('tuesday')->default(0)->nullable();
            $table->integer('wednesday')->default(0)->nullable();
            $table->integer('thursday')->default(0)->nullable();
            $table->integer('friday')->default(0)->nullable();
            $table->integer('saturday')->default(0)->nullable();
            $table->integer('sunday')->default(0)->nullable();
            $table->integer('month_repeat_on')->nullable();
            $table->integer('day_of_month')->nullable();
            $table->integer('months')->nullable();
            $table->string('position_of_day', 255)->nullable();
            $table->string('type_of_day', 255)->nullable();
            $table->integer('every_month')->nullable();
            $table->integer('yearly_recur')->nullable();
            $table->integer('yearly_repeat_on')->nullable();
            $table->string('position_of_month', 255)->nullable();
            $table->integer('day_of_every_month')->nullable();
            $table->string('yearly_position_of_day', 255)->nullable();
            $table->string('yearly_type_of_day', 255)->nullable();
            $table->string('month_of_year', 255)->nullable();
            $table->string('repeat_end', 255)->default('no')->nullable();
            $table->integer('end_after_occur')->nullable();
            $table->string('end_by_date', 255)->nullable();
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
        Schema::dropIfExists('events_recurring');
    }
}
