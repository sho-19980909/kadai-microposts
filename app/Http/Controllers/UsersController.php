<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UsersController extends Controller
{
    public function index() {
        
        //ユーザー一覧をidの降順に取得
        $users = User::orderBy('id', 'desc')->paginate(1);
        
        // ユーザ一覧ビューでそれを表示
        return view('users.index', [
            'users' => $users,
        ]);
    }
    
    // //$id を利用して表示すべきユーザを特定するメソッド
    //  public function show($id)
    // {
    //     // idの値でユーザを検索して取得
    //     $user = User::findOrFail($id);

    //     // ユーザ詳細ビューでそれを表示
    //     return view('users.show', [
    //         'user' => $user,
    //     ]);
    // }
    
    // 対象のUserを取得後に、関係するモデルの件数と投稿の一覧を取得して、ビューに渡す。
    public function show($id)
    {
        // idの値でUserを検索して取得
        $user = User::findOrFail($id);
        
        //関係するモデルの件数をロードする
        // アクションでこのメソッドを $user->loadRelationshipCounts() のように呼び出し、ビューで $user->microposts_count のように件数を取得することになります。
        $user->loadRelationShipCounts();
        
        // ユーザの投稿一覧を作成日時の降順で取得
        $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);
        
        // ユーザ詳細ビューでそれらを表示
        return view('users.show', [
            'user' => $user,
            'microposts' => $microposts,
        ]);
    }
    
    
    /**
     * ユーザのフォロー一覧ページを表示するアクション。
     * 
     * @param $id ユーザのid
     * @return \Illuminate\Http\Response
     */
     public function followings($id)
     {
        // idの値でユーザを検索して取得。
        $user = User::findOrFail($id);
        
        // 関係するモデルの件数をロード。
         $user->loadRelationShipCounts();
         
        //  ユーザのフォロー一覧を取得。
        $followings = $user->followings()->paginate(10);
        
        // フォロー一覧ビューでそれらを表示。
        return view('users.followings', [
            'user' => $user,
            'users' => $followings,
            ]);
     }
     
     /**
      * ユーザのフォロワー一覧を表示するアクション
      * 
      * @param  $id  ユーザのid
     * @return \Illuminate\Http\Response
     */
     public function followers($id)
     {
        //  idの値でユーザを検索して取得。
        $user = User::findOrFail($id);
        
        // 関係するモデルの件数をロード
        $user->loadRelationShipCounts();
        
        // ユーザのフォロワー一覧を取得。
        $followers = $user->followers()->paginate(10);
        
        // フォロワー一覧ビューでそれらを表示
        return view('users.followers', [
            'user' => $user,
            'users' => $followers,
        ]);
     }
     
     
     
    /**お気に入り一覧の取得
     */
     public function favorites($userId)
     {
         
        //  idの値でユーザを検索して取得
        $user = User::findOrFail($userId);
        
        // 関係するモデルの件数をロードする。
        $user->loadRelationShipCounts();
        
        // お気に入り一覧を取得
        $favorites = $user->favorites()->paginate(10);
        
        // 一覧ビューでそれらを表示
        return view('users.favorites', [
            'user' => $user,
            'microposts' => $favorites,
            ]);
        
     }
     
     
     
}
