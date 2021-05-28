<h5>Публикация</h5>

@if ($post->publication_url !== null)
    <div class="form-group">
        <label for="publication_url">Ссылка на публикацию</label>
        <input type="text" class="form-control" value="{{ $post->publication_url }}" disabled>
    </div>

    <p><a href="{{ $post->publication_url }}" target="_blank">Открыть публикацию в новой вкладке</a></p>
@else
    <p>Ссылка на публикацию ещё не добавлена.</p>
@endif

@if ($post->publication_url === null && Gate::allows('set-publication', $post))
    <form action="{{ route('posts.published.store', $post->id) }}" method="post">
        @csrf

        <div class="form-group">
            <label for="publication_url">Ссылка на публикацию *</label>
            <input type="url" name="publication_url" id="publication_url"
                   class="form-control{{ $errors->has('publication_url') ? ' is-invalid' : '' }}"
                   value="{{ old('publication_url', $post->publication_url)  }}"
                   placeholder="https://example.com/" required>

            @error('publication_url')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
@endif

<hr class="w-100">
