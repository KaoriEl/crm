{{--@if ($post->comment_after_moderating)--}}
{{--    <div class="alert alert-warning" role="alert">--}}
{{--        <h6 class="alert-heading d-flex justify-content-between align-items-center mb-0">--}}
{{--            <span>Главный редактор</span>--}}

{{--            @if (Auth::user()->hasRole('editor'))--}}
{{--                <small>--}}
{{--                    <form action="{{ route('posts.moderated.uncomment', $post->id) }}" method="post">--}}
{{--                        @csrf--}}
{{--                        @method('delete')--}}

{{--                        <button type="submit" class="btn btn-sm btn-link text-danger">Удалить комментарий</button>--}}
{{--                    </form>--}}
{{--                </small>--}}
{{--            @endif--}}
{{--        </h6>--}}

{{--        <p class="mb-0">{{ $post->comment_after_moderating }}</p>--}}
{{--    </div>--}}
{{--@endif--}}
@if($comments)
    <div class="comment-wrapper mt-30">
        @foreach($comments as $comment)
            @if($comment->role == 'Журналист')
{{--                <div class="alert alert-primary" role="alert">--}}
{{--                    <h6 class="alert-heading d-flex justify-content-between align-items-center mb-0">--}}
{{--                        <span>{{ $comment->role }}</span>--}}
{{--                        <small>{{ $comment->created_at }}</small>--}}
{{--                    </h6>--}}

{{--                    <p class="mb-0">{!! $comment->text !!}</p>--}}
{{--                </div>--}}
                <div class="card bg-light mb-3">
                    <div class="card-header"><span>{{ $comment->role }}</span> <small>{{ $comment->created_at }}</small></div>
                    <div class="card-body">
                        <p class="card-text">{!! $comment->text !!}</p>
                    </div>
                </div>
            @else
{{--                <div class="alert alert-warning" role="alert">--}}
{{--                    <h6 class="alert-heading d-flex justify-content-between align-items-center mb-0">--}}
{{--                        <span>{{ $comment->role }}</span>--}}
{{--                        <small>{{ $comment->created_at }}</small>--}}
{{--                    </h6>--}}

{{--                    <p class="mb-0">{!! $comment->text !!}</p>--}}
{{--                </div>--}}
                <div class="card bg-light mb-3">
                    <div class="card-header"><span>{{ $comment->role }}</span> <small>{{ $comment->created_at }}</small></div>
                    <div class="card-body">
                        <p class="card-text">{!! $comment->text !!}</p>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
