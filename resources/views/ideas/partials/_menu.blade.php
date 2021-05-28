@role('editor')
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('ideas.index') ? ' active' : '' }}" href="{{ route('ideas.index') }}">Текущие</a>
        </li>

        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('ideas.archived.index') ? ' active' : '' }}" href="{{ route('ideas.archived.index') }}">Архив</a>
        </li>
    </ul>
@endrole
