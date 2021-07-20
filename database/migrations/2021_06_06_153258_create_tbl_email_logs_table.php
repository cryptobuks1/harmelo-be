<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblEmailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('message_id', 255)->default(NULL)->nullable();
            $table->integer('user_id')->default(0)->nullable();
            $table->integer('recipient_id')->default(0)->nullable();
            $table->string('name', 255)->default(NULL)->nullable();
            $table->string('email', 255)->default(NULL)->nullable();
            $table->string('subject', 255)->default(NULL)->nullable();
            $table->longText('body')->default(NULL)->nullable();
            $table->string('title', 255)->default(NULL)->nullable();
            $table->string('source', 255)->default(NULL)->nullable();
            $table->string('event', 40)->default(NULL)->nullable();
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
        Schema::dropIfExists('tbl_email_logs');
    }
}
