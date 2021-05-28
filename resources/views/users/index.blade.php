@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Пользователи</h3>

                <div>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">Создать</a>
                </div>
            </div>

            @if (session('message'))
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    {{ session('message') }}

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button></div>
            @endif

            <table class="table table-striped table-sm table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Логин</th>
                        <th>Имя пользователи</th>
                        <th>E-mail</th>
                        <th>Роли</th>
                        <th>Проект</th>
                        <th>Часовой пояс</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td width="1%">{{ $user->id }}</td>
                            <td><a href="{{ route('users.show', $user->id) }}">{{ $user->username }}</a></td>
                            <td><a href="{{ route('users.show', $user->id) }}"><img src="{{ $user->gravatar }}" alt="{{ $user->name }}" width="25" height="25" class="rounded mr-1"> {{ $user->name }}</a></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->pluck('name')->map(function ($item) { return __($item); })->implode(', ') }}</td>
                            <td>{{ $user->projects->pluck('name')->map(function ($item) { return __($item); })->implode(', ') }}</td>
                            <td>{{ $user->timezoneTranslated }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
