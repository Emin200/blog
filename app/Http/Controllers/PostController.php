<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Storage;
use App\Post;

class PostController extends Controller
{


    function __construct()
    {
        $this->middleware('auth')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->search){
            $posts = Post::join('users', 'author_id', '=', 'users.id')
                ->where('title', 'like', '%' .$request->search . '%')
                ->orWhere('descr', 'like', '%' .$request->search . '%')
                ->orWhere('name', 'like', '%' .$request->search . '%')
                ->orderBy('posts.created_At', 'desc')
                ->get();

            return view('posts.index', compact('posts'));
        }
        $posts = Post::join('users', 'author_id', '=', 'users.id')
            ->orderBy('posts.created_At', 'desc')
            ->paginate(4);
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post = new Post();
        $post->title = $request->title;
        $post->short_title = Str::length($request->title) > 30 ?
            Str::substr($request->title, 0, 30) . '...' : $request->title;
        $post->descr = $request->descr;
        $post->author_id = Auth::user()->id;

        if ($request->file('img')){
            $path = \Illuminate\Support\Facades\Storage::putFile('public', $request->file('img'));
            //$path = \Storage::putFile('public', $request->file('img'));
            $url = \Illuminate\Support\Facades\Storage::url($path);
            //$url = \Storage::url($path);
            $post->img = $url;
        }

        $post->save();

        return redirect()->route('post.index')->with('success', 'Пост успешно создан!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::join('users', 'author_id', '=', 'users.id')
                    ->find($id);
        
        if (!$post){
            return redirect()->route('post.index')->withErrors('Пост не найден!');
        }

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);

        if (!$post){
            return redirect()->route('post.index')->withErrors('Пост не найден!');
        }

        if ($post->author_id != \Auth::user()->id){
            return redirect()->route('post.index')->withErrors('Вы не можете редактировать данный пост');
        }
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostRequest|Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, $id)
    {
        $post = Post::find($id);

        if (!$post){
            return redirect()->route('post.index')->withErrors('Пост не найден!');
        }

        if ($post->author_id != \Auth::user()->id){
            return redirect()->route('post.index')->withErrors('Вы не можете редактировать данный пост');
        }

        $post->title = $request->title;
        $post->short_title = Str::length($request->title) > 30 ?
            Str::substr($request->title, 0, 30) . '...' : $request->title;
        $post->descr = $request->descr;


        if ($request->file('img')){
            $path = \Illuminate\Support\Facades\Storage::putFile('public', $request->file('img'));
            //$path = \Storage::putFile('public', $request->file('img'));
            $url = \Illuminate\Support\Facades\Storage::url($path);
            //$url = \Storage::url($path);
            $post->img = $url;
        }

        $post->update();
        $id = $post->post_id;
        return redirect()->route('post.show', compact('id'))->with('success', 'Пост успешно отредактирован!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post){
            return redirect()->route('post.index')->withErrors('Пост не найден!');
        }

        if ($post->author_id != \Auth::user()->id){
            return redirect()->route('post.index')->withErrors('Вы не можете удалить данный пост');
        }

        $post->delete();
        return redirect()->route('post.index')->with('success', 'Пост успешно удален!');
    }
}
