<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//Micropostのモデルファイル
class Micropost extends Model
{
    protected $fillable = ['content'];
    
    
    //この投稿を所有するユーザ。（Userモデルとの関係を定義）
    // Micropostのインスタンスが所属している唯一のUserを簡単な記述で取得できるようにする。(例:$micropost->user)
    public function user() 
    {
        // belongsTo()は、「逆むきのリレーション」。（属している多側から１側を取得する）
        return $this->belongsTo(User::class);    
    }
}
