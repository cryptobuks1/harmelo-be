<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblActivityLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->nullable();
            $table->integer('source_id')->default(0)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('module', 100)->nullable();
            $table->string('actiom', 255)->nullable();
            $table->longText('descriptions')->nullable();
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
        Schema::dropIfExists('tbl_activity_logs');
    }
}
