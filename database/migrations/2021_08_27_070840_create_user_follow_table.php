<!--userとuserのフォロー関係のレコードを保存する中間テーブル。-->

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFollowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     
    // 中間テーブルとして重要なのは、user＿idとfollow_id.
    public function up()
    {
        Schema::create('user_follow', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('follow_id');    //follow_idという名前にしているが、保存する内容はユーザID。（user_idとカラム名が被ることを避けるため）
            $table->timestamps();
            
            // 外部キー制約 : $table->foreign(外部キーを設定するカラム名)->references(参照先のカラム名)->on(参照先のテーブル名);
            // onDelete('cascade') : ユーザテーブルの削除が実行されると同時に、それに紐付くフォローテーブルのフォロー、フォロワーのレコードも削除される。
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('follow_id')->references('id')->on('users')->onDelete('cascade');
            
            // user_idとfollow_idの組み合わせを重複を許さない
            // (一度保存したフォロー関係を何度も保存しないためのテーブル制約)
            $table->unique(['user_id', 'follow_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_follow');
    }
}
