<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdTblEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_activity_logs', function (Blueprint $table) {
            $table->integer('client_id')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_activity_logs', 'client_id')) {
                $table->dropColumn('client_id');
            }
        });
    }
}
