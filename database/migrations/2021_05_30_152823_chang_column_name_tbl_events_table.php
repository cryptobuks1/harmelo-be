<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangColumnNameTblEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_events', function (Blueprint $table) {
            $table->renameColumn('booking_date', 'event_date');

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
            $table->renameColumn('event_date', 'booking_date');
        });
    }
}
