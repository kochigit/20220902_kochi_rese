<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Request $request)
    {
        // 連打による重複お気に入りを防ぐ、UX低下を防ぐためにエラーは返さない。nullが返った後のことはNuxtに任せる。
        $doesExist = Favorite::where($request->all())->exists();
        if ($doesExist) {
            return;
        } else {
            $favorite = Favorite::create($request->all());
            return response()->json(compact('favorite'), 201);
        }
    }


    public function destroy(Favorite $favorite)
    {
        // 赤ハート連打で404エラーは返るが、UXを保持するためにNuxt側ではそのエラーをcatchして即return; consoleには出るが妥協。
        $favorite->delete();
        return response()->json(null, 200);
        // おそらく$favoriteがFavoriteモデルのインスタンスなので、削除済みのidでインスタンスを生成しようとすると404がでる。try,catchでも404は不可避だった。
        // 今回Twitter風SNSの模範解答に沿う形で作ってはみたが、正直Twitter風SNSのときに自分で組んだコントローラー側にcreate,deleteの判断をまとめて任せる方がまともな動きする。
        // like系の連打にいかに対応していくかは今後の課題。サーバー側へのトラフィックを増やしたくないのでUI側だけで完結させたい所。
        // disableとsetTimeOut使えば良さそうだけどbuttonにしかdisable使えないから他の方法がないか考え中。
    }
}
