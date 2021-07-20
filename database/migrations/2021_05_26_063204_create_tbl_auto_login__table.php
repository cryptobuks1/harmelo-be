<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAutoLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_auto_login', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('code', 255)->nullable();
            $table->string('redirect_to', 255)->nullable();
            $table->string('module', 255)->nullable();
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
        Schema::dropIfExists('tbl_auto_login');
    }
}
