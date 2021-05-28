@if ($post->archived())
    <div class="alert alert-warning">Публикация находится в архиве</div>
@endif

<div class="card">
    <div class="card-body">
        <h4 class="card-title d-flex justify-content-between">
            <span>{{ $post->title }}</span>
            <small class="text-muted">№{{ $post->id }}</small>
        </h4>

        <p></p>
        {!! $post->text !!}
    </div>

    <div class="card-footer text-muted d-flex justify-content-between">
        <div class="d-flex align-items-center">
            @can('archive', $post)
                @if ($post->archived())
                    <form action="{{ route('posts.archived.delete', $post->id) }}" method="post">
                        @csrf
                        @method('delete')

                        <button type="submit" class="btn btn-primary mr-1" title="Вернуть публикацию из архива">Вернуть из архива</button>
                    </form>
                @else
                    <form action="{{ route('posts.archived.store', $post->id) }}" method="post">
                        @csrf

                        <button type="submit" class="btn btn-primary mr-1" title="Архивировать публикацию">Архивировать</button>
                    </form>
                @endif
            @endcan

            @can('put', $post)
                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-primary">Редактировать</a>
            @endcan
        </div>

        <div class="d-flex flex-column align-items-end">
            <div>Дата создания: {{ $post->created_at->setTimezone(Auth::user()->timezone)->translatedFormat('d F Y, H:i') }}</div>
            <div>Истекает: {{ $post->expires_at ? $post->expires_at->setTimezone(Auth::user()->timezone)->translatedFormat('d F Y, H:i') : '' }}</div>
        </div>
    </div>
</div>
