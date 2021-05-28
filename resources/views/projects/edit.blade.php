@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h4 class="mb-3">Редактировать проект</h4>
                @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('projects.update', $project->id) }}" method="post" id="create-project-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mt-4">
                        <input name="name" id="name" rows="10" class="form-control form-control-lg" placeholder="Название проекта" value="{{ $project->name }}" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <textarea name="description" id="description" rows="10" class="form-control form-control-lg" placeholder="Введите описание проекта" minlength="1">{{ $project->description }}</textarea>
                    </div>

                    <div class="form-group mt-4">
                        <input type="number" name="publication_rate" id="publication_rate" class="form-control form-control-lg" placeholder="Норма кол-ва публикаций" min="0" value="{{ $project->publication_rate }}">
                    </div>

                    <div class="form-group mt-4">
                        <input name="site" id="site" rows="10" class="form-control form-control-lg" placeholder="Сайт" value="{{ $project->site }}" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="vk" id="vk" rows="10" class="form-control form-control-lg" placeholder="ВК" value="{{ $project->vk }}" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="ok" id="ok" rows="10" class="form-control form-control-lg" placeholder="ОК" value="{{ $project->ok }}" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="fb" id="fb" rows="10" class="form-control form-control-lg" placeholder="ФБ" value="{{ $project->fb }}" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="insta" id="insta" rows="10" class="form-control form-control-lg" placeholder="Инста" value="{{ $project->insta }}" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="y_dzen" id="y_dzen" rows="10" class="form-control form-control-lg" placeholder="Я.Дзен" value="{{ $project->y_dzen }}" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="y_street" id="y_street" rows="10" class="form-control form-control-lg" placeholder="Я.Район" value="{{ $project->y_street }}" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="yt" id="yt" rows="10" class="form-control form-control-lg" placeholder="Youtube" value="{{ $project->yt }}" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="tg" id="tg" rows="10" class="form-control form-control-lg" placeholder="Telegram" value="{{ $project->tg }}" minlength="1">
                    </div>
                    <div class="form-group mt-4">
                        <input name="tt" id="tt" rows="10" class="form-control form-control-lg" placeholder="TikTok" value="{{ $project->tt }}" minlength="1">
                    </div>
                    <br>

                    <h4 class="mb-3">Пользователи в проекте</h4>

                    <table class="table table-striped table-sm table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя пользователя</th>
                            <th>Логин пользователя</th>
                            <th>Роль пользователя</th>
                            <th>Открепить</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($project->users as $user)
                            <tr>
                                <td width="1%">{{ $user->id }}</td>
                                <td><a href="/projects/{{ $user->id }}">{{ $user->name }}</a></td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->roles->pluck('name')->map(function ($item) { return __($item); })->implode(', ') }}</td>
                                <td width="1%">
                                    <form action="<?php echo e(route('projects.user.remove', $project->id)); ?>" method="post">
                                        <?php echo csrf_field(); ?>
                                        <input name="user" id="user" type="hidden" value="{{ $user->id }}">

                                        <button type="submit" class="btn btn-primary mr-1" title="Архивировать проект">Открепить</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                        <a href="{{ url()->previous() }}" class="btn btn-link">Назад</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
