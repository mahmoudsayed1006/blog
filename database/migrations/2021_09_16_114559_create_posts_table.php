<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->text('title_en');
            $table->text('title_ar');
            $table->longtext('description_en');
            $table->longtext('description_ar');
            $table->foreignId('user_id');
			//$table->integer('user_id')->unsigned()->index();
			//$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('category_id');
            $table->boolean('deleted')->default(false);
            $table->integer('rateCount')->default(0);
            $table->integer('rateNumbers')->default(0);
            $table->integer('rate')->default(0);
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
        Schema::dropIfExists('posts');
    }
}
