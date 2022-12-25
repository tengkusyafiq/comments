<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add comments table separately
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('comment_id')->nullable()->index();
            $table->string('name', 191)->nullable();
            $table->string('url', 191);
            $table->string('additional_key')->nullable();
            $table->string('type', 191)->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->default(0);
            $table->tinyInteger('status')->default(0);
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
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
        Schema::dropIfExists('files');
    }
}
