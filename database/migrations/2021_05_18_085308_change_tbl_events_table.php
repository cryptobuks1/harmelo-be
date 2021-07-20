<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTblEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_events', function (Blueprint $table) {
            $table->integer('parent_id')->nullable()->change();
            $table->string('event_start', 255)->nullable()->change();
            $table->string('event_end', 255)->nullable()->change();
            $table->string('date_start', 255)->nullable()->change();
            $table->string('date_end', 255)->nullable()->change();
            $table->string('event_type', 100)->nullable()->change();
            $table->string('instrument_ids', 255)->nullable()->change();
            $table->longText('instrument_names')->nullable()->change();
            $table->string('student_ids', 255)->nullable()->change();
            $table->longText('student_names')->nullable()->change();
            $table->string('category', 50)->nullable()->change();
            $table->longText('descriptions')->nullable()->change();
            $table->string('app_type', 100)->nullable()->change();
            $table->string('location', 255)->nullable()->change();
            $table->string('virtual_location', 255)->nullable()->change();
            $table->string('virtual_link', 255)->nullable()->change();
            $table->integer('spaces')->default(1)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_events', function (Blueprint $table) {
            //
        });
    }
}
