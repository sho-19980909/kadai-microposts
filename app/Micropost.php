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
    
    
    public function favoriteUsers()
    {
        // belongsToの第一引数：関係先のModelクラスを指定する。
        return $this->belongsToMany(User::class, 'favorites', 'micropost_id', 'user_id');
    }
    
    // favorites数のカウント
    // public function loadRelationshipCounts()
    // {
    //     $this->loadCount(['favorites']);
    // }
}
