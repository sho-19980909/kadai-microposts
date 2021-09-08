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
       $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
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
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加。（自分自身のmicropostsも表示させるため。）
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        // （micropostsのテーブルのデータのうち、$userIds配列のいずれかの合致するuser_idをもつものに絞り込んで値を返す。）
        return Micropost::whereIn('user_id', $userIds);
    }
    
    /**
     * お気に入りの一覧を取得する機能
     */
     public function favorites()
     {
        //  第一引数:関係先のModelクラスを指定。
         return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id');
     }
     
     
     /**
      * お気に入りに追加する
      */
      public function favorite($micropostId)
      {
          // フォロー機能のときは...
          // すでにフォローしているか、自分自身をフォローしようとしていないか

          // お気に入り機能のときは...
          // すでにお気に入りしているか
          
          
          $exist = $this->is_favorite($micropostId);
          
          if($exist == $micropostId) {
              //すでにお気に入りに追加していれば何もしない。
              return false;
          }else{
              //未追加であれば追加する attach() : 中間テーブルのレコードを保存するあらかじめ用意されたメソッド。
              $this->favorites()->attach($micropostId);
              return true;
          }
      }
      
      
    /**
     * お気に入りを削除する
     */
     public function unfavorite($micropostId)
     {
         $exist = $this->is_favorite($micropostId);
        //  $its_me = $this->id == $;
         
         if($exist == $micropostId) {
            //  追加されていたらお気に入りから削除する。
             $this->favorites()->detach($micropostId);
             return true;
         }else{
            //  お気に入りに登録されていなければ、何もしない
            return false;
         }
     }
     
     
    /**
     * 指定された$userIdのmicropostをお気に入り登録中か調べる。フォロー中ならtrueを返す。
     */
     public function is_favorite($micropostId)
     {
        //  ユーザの中に$userIdのもが存在するか。
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
        
//         $this->favorites()
//         ↓
//         [
//             ['id' => 1, 'micropost_id' => 2, 'user_id' => 1],
//             ['id' => 2, 'micropost_id' => 3, 'user_id' => 1],
//             ['id' => 3, 'micropost_id' => 4, 'user_id' => 1],
//             ['id' => 4, 'micropost_id' => 5, 'user_id' => 2],
//         ]
        
//         ->where('micropost_id', $userId)

//         ->where('micropost_id', 1)
//         ->where('micropost_id', 2)
        

// micropost
// [ 'id' => 1, 'content' => 'こんにちは', 'user_id' => 1],
// [ 'id' => 2, 'content' => 'こんばんは', 'user_id' => 1],
// [ 'id' => 3, 'content' => 'おはよう', 'user_id' => 2],

     }
}

