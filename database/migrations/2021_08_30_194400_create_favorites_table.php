<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('micropost_id');
            
            /** 外部キー制約
            * 「favoritesテーブルのuser_idがUser テーブルのid」が紐づいている状況
            */
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            /**「favortesテーブルのimicropost_idとmicropostsテーブルのid」が紐づいている状況
             */
            $table->foreign('micropost_id')->references('id')->on('microposts')->onDelete('cascade');
            
            
            /**user_id と micropost_id の組み合わせの重複を許さない制約
             */
            $table->unique(['user_id', 'micropost_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}
