@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-3">
                <ul class="nav nav-tabs flex-column" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('profile') }}" class="nav-link {{request()->routeIs('profile') ? ' active' : '' }}" id="home-tab" >Ваш профиль</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.instagram') }}" class="nav-link {{request()->routeIs('profile.instagram') ? ' active' : '' }}" id="home-tab">Instagram аккаунты</a>
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
                                    <h5>Авторизация instagram аккаунта</h5>
                                    <p class="output-message"></p>
                                    <div class="input-group user-info">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="">Логин и пароль</span>
                                        </div>
                                        <input type="text" class="form-control" name="username_insta" value="">
                                        <input type="password" class="form-control" name="password_insta" value="" >
                                        <input type="hidden" value="" class="form-control" name="api_path">
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button id="auth_user" type="submit" class="btn btn-primary">Авторизовать</button>
                                    </div>

                            <div class="alert alert-success mt-2" role="alert">
                                Аккаунт успешно авторизован!
                            </div>
                            <div class="alert alert-danger mt-2" role="alert">
                                Не удалось авторизовать аккаунт. Попробуйте снова.
                            </div>



                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div class="input-group sms_auth mt-2">
                                <input id="sms_code" type="text" class="form-control" placeholder="Код с почты" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btn_post_sms_to_auth">Вставить код с почты</button>
                                </div>

                            </div>
                        </div>
                </div>

                <div class="card mt-2">
                    <div class="card-body">
                        <h5>Cписок активных аккаунтов</h5>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Логин</th>
                                <th scope="col">Имя профиля</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach($accounts as $account)
                            <tr>
                                <th scope="row">{{ $i++ }}</th>
                                <td>{{ $account->username }}</td>
                                <td>{{ $account->name_account }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                    </div>
                </div>
            </div>
    </div>
@endsection
