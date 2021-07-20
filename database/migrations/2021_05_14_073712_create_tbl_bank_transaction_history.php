<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblBankTransactionHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_bank_transaction_history', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('source_id');
            $table->string('amount');
            $table->string('status');
            $table->string('bank');
            $table->string('transaction_type');
            $table->string('reward_equivalent')->nullable();
            $table->string('ddate');
            $table->integer('is_delete')->default(0);
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
        Schema::dropIfExists('tbl_bank_transaction_history');
    }
}
