@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col col-lg-10 offset-lg-1">

                @if (session('error'))
                    <div class="alert alert-danger">
                        <span class="mdi mdi-alert"></span> {{ session('error') }}

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    @include('posts.partials._errors')
                @endif

                {{-- Карточка-превью публикации --}}
                @include('posts.partials._card-preview')

            </div>
        </div>

        <div class="row">
            <div class="col col-lg-10 offset-lg-1">
                {{-- Список файлов --}}
                @include('layouts.dropzone', ['url' => '/api/posts/' . $post->id . '/files', 'files' => $post->attachments])


                @if($post->status_task === null)
                <div class="card my-4">
                    <div class="card-body">
                        <form action="{{ route('posts.update.status', $post->id) }}" method="POST">
                            @csrf
                            <h5 class="card-title">Вам пришла новая задача</h5>
                            <input type="hidden" name="journalist_id" value="{{ Auth::id() }}">
                            <button class="btn btn-primary">Взять в работу</button>
                        </form>

                    </div>
                </div>
                @else

                <div class="card">
                    <div class="card-body">

                        {{-- Исполнитель --}}
                        {{-- ! !! ! ! ! !  Устарело, сейчас после взятию в работу ты становишься исполнителем по дефолту ! !! --}}
                        @include('posts.partials._assignee')

                        {{-- Если есть исполнитель --}}
                        @if ($post->hasJournalist())

                            {{-- Черновик --}}
                            @include('posts.partials._draft')

                            {{-- Если статью нужно проверить и текущий пользователь может это сделать --}}
                            @if ($post->needModeration() && Gate::allows('moderate', $post))

                                {{-- Принять или отправить на доработку публикацию --}}
                                @include('posts.partials._moderating')
                            @endif

                            <hr class="w-100">

                            @if ($post->approved)
                                {{-- Публикация --}}
                                @include('posts.partials._publication')
                            @endif

                        @endif

                        {{-- Если статья была опубликована --}}
                        @if ($post->published())

                            <form action="{{ route('posts.update', $post->id) }}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('patch')

                                {{-- Размещение в соц. сетях --}}
                                @include('posts.partials._posting')

                                {{-- Коммерческий посев --}}
                                @include('posts.partials._commercial_seeder')

                                {{-- Посев --}}
                                @include('posts.partials._seeding')

                                {{-- Таргет --}}
                                @include('posts.partials._targeting')

                                {{-- Комментирование --}}
                                @include('posts.partials._commenting')

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                        @endif
                    @endif
            </div>
        </div>
    </div>
@endsection
