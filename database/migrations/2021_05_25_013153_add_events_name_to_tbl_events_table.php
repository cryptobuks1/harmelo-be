<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventsNameToTblEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_events', function (Blueprint $table) {
            $table->string('name', 255)->nullable();
            $table->integer('cancellation')->nullable();
            $table->string('cancellation_type', 100)->nullable();
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
                if (Schema::hasColumn('tbl_events', 'name')) {
                    $table->dropColumn('name');
                }
            if (Schema::hasColumn('tbl_events', 'cancellation')) {
                $table->dropColumn('cancellation');
            }
            if (Schema::hasColumn('tbl_events', 'cancellation_type')) {
                $table->dropColumn('cancellation_type');
            }
        });
    }
}
