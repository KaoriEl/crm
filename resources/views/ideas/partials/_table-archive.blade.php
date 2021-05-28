<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Дата</th>
            <th>Описание</th>
            <th>Автор</th>
            <th>Причины архивации</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ideas as $idea)
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

                <td width="5%">{!! $idea->archive_comment !!}</td>

                <td width="5%"><a href="{{ route('ideas.show', $idea->id) }}">Подробнее</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
