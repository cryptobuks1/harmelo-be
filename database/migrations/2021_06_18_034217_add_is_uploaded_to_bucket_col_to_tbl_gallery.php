<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsUploadedToBucketColToTblGallery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_gallery', function (Blueprint $table) {
            $table->integer('is_uploaded_to_bucket')->default(0)->nullable()->after('file_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_gallery', function (Blueprint $table) {
            //
        });
    }
}
