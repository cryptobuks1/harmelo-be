<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMeetingCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_meeting_code', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('code', 255)->nullable();
            $table->integer('source_id')->nullable();
            $table->string('module')->nullable();
            $table->longText('url')->nullable();
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
        Schema::dropIfExists('tbl_meeting_code');
    }
}
