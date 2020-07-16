<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Alertcomment;
use App\Alert;
use App\User;
use DB;
use App\Http\Requests\StoreAlertcomment;

class AlertcommentsController extends Controller
{
    public function ajax(Request $request){
        //$alertcommentにクリックしたコメントのデータを入れる。
        $alertcomment = Alertcomment::find($request->id);
        $user = User::find($alertcomment->user_id);
        
        //クリックしたコメントに親コメントがなければ
        if($alertcomment->parent_id == null){
            //クリックしたコメントに子コメントがあれば
            if(DB::table('alertcomments')->where('parent_id', $request->id)->exists()){
                //$undercommentにクリックしたコメントの子コメントのデータを入れる。
                $undercomments = DB::table('alertcomments')->join('users', 'users.id', '=', 'alertcomments.user_id')->where('parent_id' , $request->id)->orderBy('alertcomments.created_at', 'acs')->get();
                
                return response()->json([
                    'responseData' => $alertcomment,
                    'userData' => $user,
                    'underDatas' => $undercomments,
                    'upData' => '',
                    'upuserData' => '',
                ]);
            
            //クリックしたコメントに子コメントがなければ
            }else{
                return response()->json([
                    'responseData' => $alertcomment,
                    'userData' => $user,
                    'underDatas' => '',
                    'upData' => '',
                    'upuserData' => '',
                ]);
            }
        
        //クリックしたコメントに親コメントがあれば    
        }else{
            //クリックしたコメントに子コメントがあれば
            if(DB::table('alertcomments')->where('parent_id', $request->id)->exists()){
                
                //$upcommentにクリックしたコメントの親コメントのデータを入れる。
                $upcomment = Alertcomment::find(optional($alertcomment)->parent_id);
                $upuser = User::find(optional($upcomment)->user_id);
                $undercomments = DB::table('alertcomments')->join('users', 'users.id', '=', 'alertcomments.user_id')->where('parent_id' , $request->id)->orderBy('alertcomments.created_at', 'acs')->get();
                return response()->json([
                    'responseData' => $alertcomment,
                    'userData' => $user,
                    'underDatas' => $undercomments,
                    'upData' => $upcomment,
                    'upuserData' => $upuser,
                ]);
            //クリックしたコメントに子コメントがなければ 
            }else{
                $upcomment = Alertcomment::find(optional($alertcomment)->parent_id);
                $upuser = User::find(optional($upcomment)->user_id);
                
                return response()->json([
                    'responseData' => $alertcomment,
                    'userData' => $user,
                    'underDatas' => '',
                    'upData' => $upcomment,
                    'upuserData' => $upuser,
                ]);
            }
        }
    }    

    public function ajaxindex($id)
    {
         // id順に取得
        $alertcomments = DB::table('alertcomments')->join('users', 'users.id', '=', 'alertcomments.user_id')->orderBy('alertcomments.created_at', 'decs')->get();
        $AuthId = Alertcomment::where('alert_id' , $id)->orderBy('alertcomments.created_at', 'acs')->first()->user_id;
        
        return response()->json([
            "status" => "success",
            "message" => "成功",
            "comments" => $alertcomments,
            "AuthId" => $AuthId,
        ]);
    }
    
    public function ajaxstore(StoreAlertcomment $request)
    {
            if($request->parent_id == null){
                $params = $request->validate([
                    'alert_id' => 'required|exists:alerts,id',
                    'comment' => 'required|max:140',
                ]);
            }
            else{
                 $params = $request->validate([
                    'alert_id' => 'required|exists:alerts,id',
                    'parent_id' => 'required|exists:alertcomments,id',
                    'comment' => 'required|max:140',
                ]);
            }
            
            //時間のセット
            date_default_timezone_set('Asia/Tokyo');
            $now = date("Y/m/d H:i");
            
            $request->user()->alertcomments()->create([
                'comment' => $request->comment,
                'alert_id' => $request->alert_id,
                'parent_id' => $request->parent_id,
                'time' => $now,
            ]);
            return redirect("/ajax/{$request->alert_id}");
    }
    
    public function update(StoreAlertcomment $request, $id)
    {
        if($request->parent_id == null){
            $params = $request->validate([
                'alert_id' => 'required|exists:alerts,id',
                'comment' => 'required|max:140',
            ]);
        }
        else{
             $params = $request->validate([
                'alert_id' => 'required|exists:alerts,id',
                'parent_id' => 'required|exists:alertcomments,id',
                'comment' => 'required|max:140',
            ]);
        }
        
        //時間のセット
        date_default_timezone_set('Asia/Tokyo');
        $now = date("Y/m/d H:i");
        
        $alertcomment = Alertcomment::find($id);
            
        $alertcomment->comment = $request->comment;
        $alertcomment->alert_id = $request->alert_id;
        $alertcomment->parent_id = $request->parent_id;
        $alertcomment->time = $now;
        
        $alertcomment->save();
        
        return redirect('/alerts');
    }
    
    // getでalerts/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        $alertcomment = Alertcomment::find($id);

        return view('alertcomments.edit', [
            'alertcomment' => $alertcomment,
        ]);
    }
    
    // deleteでalerts/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        $alertcomment = Alertcomment::find($id);
        if (\Auth::id() === $alertcomment->user_id) {
            $alertcomment->delete();
        }
        return back();
    }
}