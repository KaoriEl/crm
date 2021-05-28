@if (Auth::user()->hasAnyRole(['editor', 'journalist']))
    <hr class="w-100">

    <h5>Материал</h5>

    @if (Auth::id() === $post->journalist_id || Auth::user()->hasRole('editor'))
        {{-- Комментарий от главного редактора --}}
        @include('posts.partials._comment')
    @endif

    @if ($post->hasDraft() && Auth::user()->hasRole('journalist') && $post->on_moderate == 1)
        <div class="form-group">
            <label for="draft_url">Ссылка на старый материал</label>
            <input type="text" class="form-control"value="{{ $post->draft_url }}" disabled>
        </div>

        <p><a href="{{ $post->draft_url }}" target="_blank">Открыть материал в новой вкладке</a></p>

    @elseif($post->hasDraft() && Auth::user()->hasRole('journalist') && $post->on_moderate == 0)
        <div class="form-group">
            <label for="draft_url">Ссылка на материал</label>
            <input type="text" class="form-control"value="{{ $post->draft_url }}" disabled>
        </div>

        <p><a href="{{ $post->draft_url }}" target="_blank">Открыть материал в новой вкладке</a></p>

    @endif

    @if ($post->hasDraft() && Auth::user()->hasRole('editor'))
        <div class="form-group">
            <label for="draft_url">Ссылка на материал</label>
            <input type="text" class="form-control"value="{{ $post->draft_url }}" disabled>
        </div>

        <p><a href="{{  $post->draft_url }}" target="_blank">Открыть материал в новой вкладке</a></p>

    @elseif(Auth::user()->hasRole('editor'))
        <p>Исполнитель ещё не добавил ссылку на материал.</p>
    @endif

    @if (Gate::allows('set-draft', $post))

        @if ($post->draft_url === null || $post->approved === false && !Auth::user()->hasRole('editor'))
            <form action="{{ route('posts.draft.store', $post->id) }}" method="post">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label for="draft_url">Ссылка на материал</label>
                    <input type="url" class="form-control{{ $errors->has('draft_url') ? ' is-invalid' : '' }}" name="draft_url" value="" placeholder="https://example.com/" required>

                    @error('draft_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group mt-3">
                    <label for="comment">Комментарий</label>
                    <textarea name="comment" id="comment" rows="2" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        @endif

    @endif

@endif
