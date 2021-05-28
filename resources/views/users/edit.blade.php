@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="row">
                <div class="col-12 col-md-6 offset-md-3">
                    <h3 class="mb-4">Изменить пользователя</h3>

                    <form action="{{ route('users.update', $user->id) }}" method="post">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label for="name">Имя пользователя</label>
                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="name" placeholder="Имя пользователя" value="{{ old('name', $user->name) }}" required>

                            @error('name')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Новый пароль (необязательно)</label>
                            <input type="text" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" name="password" placeholder="Пароль" value="{{ old('password') }}">

                            @error('password')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @else
                                <small class="form-text text-muted">Если пароль не нужно менять, оставьте это поле пустым</small>
                            @enderror

                        </div>

                        <div class="form-group">
                            <label for="phone">Имя пользователя телеграм (необязательно)</label>
                            <input type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" id="phone" name="phone" placeholder="Имя пользователя телеграм" value="{{ old('phone', $user->phone) }}">

                            @error('phone')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror

                        </div>

                        <div class="form-group">
                            <label for="timezone">Часовой пояс пользователя *</label>

                            <select name="timezone" id="timezone" class="form-control{{ $errors->has('timezone') ? ' is-invalid' : '' }}" required>
                                @foreach ($timezones as $tz)
                                    <option value="{{ $tz->value }}"{{ $tz->value === old('timezone', 'Asia/Novosibirsk') ? ' selected' : '' }}>{{ $tz->name }}</option>
                                @endforeach
                            </select>

                            @error('timezone')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Назначенные роли</label>

                            <ul class="list-group">
                                @foreach ($roles as $role)
                                    <li class="list-group-item list-group-item-action list-group-item-checkbox">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="roles[]" id="role-{{ $role->id }}" value="{{ $role->id }}"{{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                            <label for="role-{{ $role->id }}" class="custom-control-label">@lang($role->name)</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            @error('roles')
                                <div class="alert alert-danger mt-2">Пользователю должна быть назначена хотя бы одна роль.</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Назначенные проекты</label>

                            <ul class="list-group">
                                @foreach ($projects as $project)

                                    <li class="list-group-item list-group-item-action list-group-item-checkbox">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="projects[]" id="project-{{ $project->id }}" value="{{ $project->id }}"{{ $user->hasProject($project) ? 'checked' : '' }}>
                                            <label for="project-{{ $project->id }}" class="custom-control-label">@lang($project->name)</label>
                                        </div>
                                    </li>

                                @endforeach
                            </ul>

                            @error('projects')
                            <div class="alert alert-danger mt-2">Пользователю должен быть назначен хотя бы один проект.</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Сохранить</button>
                        <a href="{{ url()->previous() }}" class="btn btn-link">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
