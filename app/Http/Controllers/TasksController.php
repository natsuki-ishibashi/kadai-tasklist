<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;

use App\Task;


class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    // getでmessages/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        $data = [];
        if (\Auth::check()) { 
            // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];

            return view('tasks.index', [
                'tasks' => $tasks,
            ]);
        }
        
        else{
            
        return view('welcome');
            
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function create()
    {
        $task = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|max:10',   
            'content' => 'required|max:255',
        ]);
        
        $request->user()->tasks()->create([
            'status' => $request->status,
            'content' =>$request->content,
        ]);

        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    // getでmessages/（任意のid）にアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        // idの値でユーザを検索して取得
        $task = Task::findOrFail($id);

        return view('tasks.show', [
            'task' => $task,
        ]);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    // getでmessages/（任意のid）/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        $task = Task::findOrFail($id);

        return view('tasks.edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    // putまたはpatchでmessages/（任意のid）にアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
         // バリデーション
        $request->validate([
            'status' => 'required|max:10',   
            'content' => 'required|max:255',
        ]);
        
        $task = Task::findOrFail($id);
        // タスクを更新
        $task->status = $request->status;    
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    // deleteでmessages/（任意のid）にアクセスされた場合の「削除処理」
    public function destroy($id)
    {
      // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);

        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
            
           return back();

        }
        
        else{
            
        return view('welcome');
            
        }
           
    }
}
