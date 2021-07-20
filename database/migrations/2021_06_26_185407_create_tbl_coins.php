<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblCoins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_coins', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('coach_id')->nullable();
            $table->integer('source_id')->nullable();
            $table->decimal('coins', 18, 3)->default(0)->nullable();
            $table->integer('is_delete', 0)->default(0)->nullable();
            $table->longText('reasons')->nullable();
            $table->string('type', 255)->nullable();
            $table->string('module', 255)->nullable();
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
        Schema::dropIfExists('tbl_coins');
    }
}
