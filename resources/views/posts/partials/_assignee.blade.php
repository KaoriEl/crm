<h5 class="card-title">Исполнитель</h5>

@if ($post->journalist)
<p class="mb-0">
    <img src="{{ $post->journalist->gravatar }}" alt="{{ $post->journalist->name }}" width="25" height="25" class="rounded mr-1">
    {{ $post->journalist->name }}
</p>

@role('admin')
<form action="{{ route('posts.assignee.update', $post->id) }}" method="post">
    @csrf
    @method('put')

    <div class="form-group">
        <label for="journalist">Выберите исполнителя:</label>
        <select name="journalist_id" id="journalist" class="form-control{{ $errors->has('journalist_id') ? ' is-invalid' : '' }}">
            @foreach (\App\Models\User::role('journalist')->get() as $journalist)
            <option value="{{ $journalist->id }}">{{ $journalist->name }}</option>
            @endforeach
        </select>

        @error('journalist_id')
        <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Назначить</button>
</form>
@elseif('editor')
<form action="{{ route('posts.assignee.update', $post->id) }}" method="post">
    @csrf
    @method('put')

    <div class="form-group">
        <label for="journalist">Выберите исполнителя:</label>
        <select name="journalist_id" id="journalist" class="form-control{{ $errors->has('journalist_id') ? ' is-invalid' : '' }}">
            @foreach (\App\Models\User::role('journalist')->get() as $journalist)
            <option value="{{ $journalist->id }}">{{ $journalist->name }}</option>
            @endforeach
        </select>

        @error('journalist_id')
        <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Назначить</button>
</form>
@endrole

@else
<p>Исполнитель не назначен.</p>

@role('editor')

<form action="{{ route('posts.assignee.store', $post->id) }}" method="post">
    @csrf
    @method('put')

    <div class="form-group">
        <label for="journalist">Выберите исполнителя:</label>
        <select name="journalist_id" id="journalist" class="form-control{{ $errors->has('journalist_id') ? ' is-invalid' : '' }}">
            @foreach (\App\Models\User::role('journalist')->get() as $journalist)
            <option value="{{ $journalist->id }}">{{ $journalist->name }}</option>
            @endforeach
        </select>

        @error('journalist_id')
        <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Назначить</button>
</form>

@else
<form action="{{ route('posts.assignee.store', $post->id) }}" method="post">
    @csrf
    @method('patch')

    <input type="hidden" name="journalist_id" value="{{ Auth::id() }}">

    <button type="submit" class="btn btn-primary">Стать исполнителем</button>
</form>
@endrole
@endif
