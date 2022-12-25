<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeUrlColumnNullableInFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // make url column nullable
        Schema::table('files', function (Blueprint $table) {
            $table->string('url', 191)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // make url column not nullable
        Schema::table('files', function (Blueprint $table) {
            $table->string('url', 191)->nullable(false)->change();
        });
    }
}
