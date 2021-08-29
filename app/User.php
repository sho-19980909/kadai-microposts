<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * このユーザに関係するモデルの件数をロードする。
     */
    // Userが持つMicropostの数をカウントするためのメソッド
    public function loadRelationShipCounts() 
    {
       //loadCountメソッドの引数は、リレーション名。
       $this->loadCount(['microposts', 'followings', 'followers']); 
    }
    
    
     /**
     * このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
     */
    // Userが持つMicropostsを簡単な記述で取得できるようになる。(例：$user->microposts)
     public function microposts()
     {
        return $this->hasMany(Micropost::class);
     }
    
     
    /**
     * このユーザがフォロー中のユーザ。（Userモデルとの関係を定義。）
     */
     public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
     
     
    /**
     * このユーザをフォロー中のユーザ。（Userモデルとの関係を定義。）
     */
     public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
     
     
    /**
     * $userIdで指定されたユーザはフォローする。
     * 
     * @param int $userId
     * @return bool
     */
     public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
     
     
    /**
     * $userIdで指定されたユーザをアンフォローする。
     * 
     * @param int $userId
     * @return bool
     */
     public function unfollow($userId)
    {
        // すでにフォローしているか確認
        $exist = $this->is_following($userId);
        // 対象が自分自身か確認
        $its_me = $this->id == $userId;
        
        if($exist && !$its_me)
        {
            // すでにフォローしていればフォローを外す。
            $this->followings()->detach($userId);
            return true;
        }else{
            // 未フォローでしたら何もしない。
            return false;
        }
    }
    
    
    /**
     * 指定された$userIdのユーザをこのユーザがフォロー中であるか調べる。（フォロー中ならturnを返す。）
     * 
     * @param int $userId
     * @return bool
     */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    
    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする。
        // pluck() : 引数として与えられたテーブルのカラムの値だけを引き出す命令。
        // toArray() : 通常の配列に変換する。
        $userId = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加。（自分自身のmicropostsも表示させるため。）
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        // （micropostsのテーブルのデータのうち、$userIds配列のいずれかの合致するuser_idをもつものに絞り込んで値を返す。）
        return Micropost::whereIn('user_id', $userIds);
    }
}