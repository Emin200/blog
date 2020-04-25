@extends('layouts.layout', ['title' => "Пост №$post->post_id"])

@section('content')

    <div class="row">

        <div class="col-12">
            <div class="card">
                <div class="card-header"><h2>{{ $post->title }}</h2></div>
                <div class="card-body">
                    <div class="card-img card-img_max"
                         style="background-image: url({{ $post->img ?? asset('img/img.jpg') }})"></div>
                    <div class="card-descr">Описание: {{ $post->descr }}</div>
                    <div class="card-author">Автор: {{ $post->name }}</div>
                    <div class="card-date">Создано: {{ $post->created_at->diffForHumans() }}</div>
                    <div class="card-btn">
                        <a href="{{ route('post.index') }}" class="btn btn-outline-primary">На главную</a>
                        @auth
                            @if(\Illuminate\Support\Facades\Auth::user()->id == $post->author_id)
                            <a href="{{ route('post.edit', ['id' => $post->post_id]) }}" class="btn btn-outline-success">Редактировать</a>
                            <form action="{{ route('post.destroy', ['id' => $post->post_id]) }}" method="post"
                                  onsubmit="if (confirm('Удалить пост?')){return true} else {return false}">
                                @csrf
                                @method('DELETE')
                                <input type="submit" class="btn btn-outline-danger" value="Удалить">
                            </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection