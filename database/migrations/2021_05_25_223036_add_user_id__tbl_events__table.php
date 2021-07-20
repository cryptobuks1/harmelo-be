<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdTblEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_contacts', function (Blueprint $table) {
            $table->integer('user_id')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_contacts', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_contacts', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
}
