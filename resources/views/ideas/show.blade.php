@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
{{--{{dd($idea->attachments)}}--}}
                @if ($idea->archived_at)
                    <div class="alert alert-warning">Находится в архиве</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Идея #{{ $idea->id }}</h5>

                        <div style="white-space: pre-line;">{!! $idea->text !!}</div>
                        @if($idea->archive_comment)
                            <div>Причина архивации: {!! $idea->archive_comment !!}</div>
                        @endif

                        @if ($idea->from)
                            <p>Автор: {{ $idea->from }}</p>
                        @elseif($idea->user)
                            <p>Автор: {{ $idea->user->name }}</p>
                        @endif
                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        @if ($idea->archived_at === null)
                            @can('create', \App\Models\Post::class)
                                <a href="{{ route('posts.create', ['idea' => $idea->id]) }}" class="btn btn-primary">Поставить задачу</a>
                                <a href="{{ route('ideas.edit', $idea->id) }}"  class="btn btn-primary" >Редактировать</a>
                            @endcan
                        @endif

                        @can('update', $idea)

                            @if ($idea->archived_at)
                                <form action="{{ route('ideas.archived.destroy', $idea->id) }}" method="post">
                                    @csrf
                                    @method('delete')

                                    <button type="submit" class="btn btn-primary">Восстановить</button>
                                </form>
                            @else

                                    <button name="display_comment" type="submit" class="btn btn-primary">Архивировать</button>
                            @endif

                        @endcan
                    </div>
                </div>

                    <br></br>

                    <div class="card" id="archive_comment_block">
                        <div class="card-body">
                            <h5 class="card-title">Добавьте комментарий</h5>

                            <form action="{{ route('ideas.archived.store', $idea->id) }}" method="post">
                                @csrf
                                @method('patch')

                                <div class="col">
                                    <div class="form-group">
                                        <textarea rows="5" name="archive_comment" id="summernote" class="form-control{{ $errors->has('text') ? ' is-invalid' : '' }}"> {{ old('archive_comment', $idea->archive_comment) }}</textarea>

                                        @error('text')
                                        <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Отправить в архив</button>
                        </div>
                    </form>

                    </div>


                @include('layouts.dropzone', ['url' => '/api/ideas/' . $idea->id . '/files', 'files' => $idea->attachments])

            </div>
        </div>
    </div>
@endsection
