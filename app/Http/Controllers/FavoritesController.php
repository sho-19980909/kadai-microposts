<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * お気に入りに追加する
     */
     public function store($id)
     {
       // 認証済みのユーザが投稿をお気に入り追加する。
         \Auth::user()->favorite($id);
         return back();
     }
     
     
     /**
      * お気に入りから削除する。
      */
      public function destroy($id)
      {
       // 認証済みのユーザが投稿をお気に入りから削除する。
          \Auth::user()->unfavorite($id);
          return back();
      }
      
      
      
}
