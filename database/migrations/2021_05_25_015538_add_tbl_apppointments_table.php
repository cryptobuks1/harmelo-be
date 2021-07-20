<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTblApppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_apppointments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('client_name', 255)->nullable();
            $table->integer('event_id')->nullable();
            $table->string('event_date', 255)->nullable();
            $table->string('time_start', 100)->nullable();
            $table->string('time_end', 100)->nullable();
            $table->longText('event_desc')->nullable();
            $table->string('event_name', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('action_taken', 50)->nullable();
            $table->decimal('price', 18, 3)->nullable();
            $table->integer('is_delete')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_apppointments');
    }
}
