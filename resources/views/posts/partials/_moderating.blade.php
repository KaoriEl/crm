<form action="{{ route('posts.moderated.delete', $post->id) }}" method="post" class="mt-2 d-inline">
    @csrf
    @method('delete')

    <div class="form-group mt-3">
        <label for="comment">Комментарий</label>
        <textarea name="comment" id="comment" rows="2" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Отправить на доработку</button>
</form>

<form action="{{ route('posts.moderated.store', $post->id) }}" method="post" class="d-inline">
    @csrf

    <textarea name="comment" style="display: none;"></textarea>

    <button type="submit" class="btn btn-success" id="btn-accept-post">Принять публикацию</button>
</form>
