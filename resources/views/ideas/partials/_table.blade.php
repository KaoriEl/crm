<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Дата</th>
            <th>Описание</th>
            <th>Автор</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ideas as $idea)
            @if($idea->read_now)
            <tr style="background-color: #ff00004a">
                <td width="1%">{{ $idea->id }}</td>
                <td width="20%">{{ $idea->created_at->diffForHumans() }}</td>
                <td>{!! $idea->text !!}</td>
                @if ($idea->from)
                    <td width="10%">{{ $idea->from }}</td>
                @elseif($idea->user)
                    <td width="10%">{{ $idea->user->name }}</td>
                @else
                    <td width="10%"></td>
                @endif
                @else
                    <tr>
                        <td width="1%">{{ $idea->id }}</td>
                        <td width="20%">{{ $idea->created_at->diffForHumans() }}</td>
                        <td>{!! $idea->text !!}</td>
                        @if ($idea->from)
                            <td width="10%">{{ $idea->from }}</td>
                        @elseif($idea->user)
                            <td width="10%">{{ $idea->user->name }}</td>
                        @else
                            <td width="10%"></td>
                        @endif
                @endif

                <td width="5%"><a href="{{ route('ideas.show', $idea->id) }}">Подробнее</a>
                </td>
                <td width="5%"><a href="{{ route('ideas.edit', $idea->id) }}">Редактировать</a></td>
                <td width="5%">
                    <form action="{{ route('ideas.deleteIdea', $idea->id) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-primary">Удалить</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

