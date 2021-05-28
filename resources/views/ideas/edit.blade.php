@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3>Редактировать идею</h3>
{{--{{dd($idea->attachments)}}--}}
                <form action="{{ route('ideas.editIdea', $idea->id) }}" method="post" id="create-idea-form" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group mt-4">
                        <textarea name="text" rows="10" class="form-control form-control-lg" placeholder="Введите описание идеи..." minlength="1">{{ strip_tags($idea->text) }}</textarea>
                    </div>

                    @include('layouts.dropzone', ['url' => '/api/files', 'files' => []])

                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="read_now" value="0">
                        @if ($idea->read_now == 1)
                        <input type="checkbox" class="custom-control-input" name="read_now" id="read_now" value="1" checked>
                        <label for="read_now" class="custom-control-label">Срочная идея</label>
                        @else
                        <input type="checkbox" class="custom-control-input" name="read_now" id="read_now" value="1">
                        <label for="read_now" class="custom-control-label">Срочная идея</label>
                        @endif
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Редактировать</button>
                        <a href="{{ url()->previous() }}" class="btn btn-link">Назад</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
