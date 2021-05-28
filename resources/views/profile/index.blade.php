@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-3">
                <ul class="nav nav-tabs flex-column" role="tablist">
                    <li class="nav-item">
                        <a href="#home" class="nav-link active" id="home-tab" data-toggle="tab" role="tab"
                           aria-controls="home" aria-selected="true">Ваш профиль</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.instagram') }}" class="nav-link" id="home-tab">Instagram аккаунты</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.vk') }}" class="nav-link" id="home-tab">VK аккаунт</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.ok') }}" class="nav-link" id="home-tab">OK аккаунт</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.telegram') }}" class="nav-link" id="home-tab">Telegram номера</a>
                    </li>
                </ul>
            </div>

            <div class="col">

                <div class="card">
                    <div class="card-body">
                        @if (session('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <form action="{{ route('profile.update') }}" method="post">
                                    @csrf
                                    @method('patch')

                                    <div class="form-group row align-items-center">
                                        <label for="name" class="col-3">Имя</label>

                                        <div class="col">
                                            <input type="text" name="name"
                                                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                   placeholder="Имя"
                                                   value="{{ old('name', Auth::user()->name) }}">

                                            @error('name')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label for="email" class="col-3">E-mail</label>

                                        <div class="col">
                                            <input type="email" name="email"
                                                   class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                                   placeholder="Ваша электронная почта"
                                                   autocomplete="email"
                                                   value="{{ old('email', Auth::user()->email) }}">

                                            @error('email')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label for="timezone" class="col-3">Часовой пояс</label>

                                        <div class="col">
                                            <select name="timezone" id="timezone" class="form-control{{ $errors->has('timezone') ? ' is-invalid' : '' }}">
                                                @foreach ($timezones as $tz)
                                                    <option value="{{ $tz->value }}"{{ $tz->value === old('timezone', Auth::user()->timezone) ? ' selected' : '' }}>{{ $tz->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('timezone')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                </form>

                                <hr>

                                <form action="{{ route('profile.password') }}" method="post">
                                    @csrf
                                    @method('patch')

                                    <div class="form-group row align-items-center">
                                        <label for="name" class="col-3">Старый пароль</label>

                                        <div class="col">
                                            <input type="password" name="old_password"
                                                   class="form-control {{ $errors->has('old_password') ? 'is-invalid' : '' }}"
                                                   placeholder="Старый пароль"
                                                   value="{{ old('old_password') }}">

                                            @error('old_password')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label for="name" class="col-3">Пароль</label>

                                        <div class="col">
                                            <input type="password" name="password"
                                                   class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                                   placeholder="Новый пароль"
                                                   value="{{ old('password') }}">

                                            @error('password')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label for="name" class="col-3">Подтверждение пароля</label>

                                        <div class="col">
                                            <input type="password" name="password_confirmation"
                                                   class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                                   placeholder="Повторите пароль"
                                                   value="{{ old('password_confirmation') }}">

                                            @error('password_confirmation')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">Сменить пароль</button>
                                    </div>
                                </form>
                                @hasanyrole('admin')
                                @if($setting->count() != 0)
                                <hr>
                                <h5>Системные настройки</h5>
                                @if($setting[0]['setting_value'] == 'no' )
                                <div class="form-group row align-items-center mt-3">
                                    <label for="email" class="">Авторизация для Google Document </label>

                                    <div class="col">
                                        <a href="{{ $url }}">Авторизация</a>
                                    </div>
                                </div>
                                @else
                                    <div class="form-group row align-items-center mt-3">
                                        <p class="ml-2" style="color: greenyellow">Google: аккаунт авторизован</p>
                                    </div>
                                @endif
                                @endif

                                @if($inst_login !== null)
                                <p class="text-instagram" style="color: greenyellow">Инстаграм аккаунт: активирован</p>
                                @else
                                    <p class="text-instagram">Инстаграм аккаунт</p>
                                @endif
                                <p class="output-message"></p>
                                <div class="input-group user-info">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="">Логин и пароль</span>
                                    </div>
                                    <input type="text" class="form-control" name="username_insta" value="{{  $inst_login->setting_value ?? '' }}">
                                    <input type="password" class="form-control" name="password_insta" value="{{ $inst_pass->setting_value ?? ''  }}">
                                    <input type="hidden" value="" class="form-control" name="api_path">
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button id="auth_user" type="submit" class="btn btn-primary">Авторизовать</button>
                                </div>
                            </div>

                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div class="input-group sms_auth">
                                <input id="sms_code" type="text" class="form-control" placeholder="Код с почты" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btn_post_sms_to_auth">Вставить код с почты</button>
                                </div>



                                @endhasanyrole

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
