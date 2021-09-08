<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MicropostsController extends Controller
{
     public function index()
    {
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            // （後のChapterで他ユーザの投稿も取得するように変更しますが、現時点ではこのユーザの投稿のみ取得します）
            $microposts = $user->feed_microposts()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'microposts' => $microposts,
            ];
        }

        // Welcomeビューでそれらを表示
        return view('welcome', $data);
    }
    
    // storeアクションでは create メソッドを使ってMicropostを保存する
   public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'content' => 'required|max:255',
        ]);

        // 認証済みユーザ（閲覧者）の投稿として作成（リクエストされた値をもとに作成）
        $request->user()->microposts()->create([
            'content' => $request->content,
        ]);
                
            // 前のURLへリダイレクトさせる。（リクエスト元の投稿フォームのページへ戻る
           return back();
    }
    
    // 
     public function destroy($id)
    {
        // idの値で投稿を検索して取得
        $micropost = \App\Micropost::findOrFail($id);

        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $micropost->user_id) {
            $micropost->delete();
        }
        
        // 前のURLへリダイレクトさせる
        return back();
        
    }
    
    
    // /**お気に入り一覧の取得
    //  */
    //  public function favorites($userId)
    //  {
         
    //     //  idの値でユーザを検索して取得
    //     $user = User::findOrFail($userId);
        
    //     // 関係するモデルの件数をロードする。
    //     $user->loadRelationShipCounts();
        
    //     // お気に入り一覧を取得
    //     $favorites = $user->favorites()->paginate(10);
        
    //     // 一覧ビューでそれらを表示
    //     return view('users.favorites', [
    //         'user' => $user,
    //         'microposts' => $favorites,
    //         ]);
    //  }
     
    
}
