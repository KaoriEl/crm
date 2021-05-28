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
                        <a href="{{ route('profile.vk') }}" class="nav-link {{request()->routeIs('profile.vk') ? ' active' : '' }}" id="home-tab">VK аккаунт</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.ok') }}" class="nav-link" id="home-tab">OK аккаунт</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.telegram') }}" class="nav-link" id="home-tab">Telegram номера</a>
                    </li>
                </ul>
            </div>

            <div class="col" style="display: grid;">

                <div class="card">
                    <div class="card-body">
                        @if (session('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <h5>Авторизация VK аккаунта</h5>
                        <label for="tokenget">Откройте ссылку в режиме инкогнито:</label>
                        <a id="tokenget"  target="_blank" href="https://oauth.vk.com/authorize?client_id=7822529&amp;redirect_uri={{config('app.VK_REDIRECT_URL')}}&amp;display=page&amp;scope=336896&amp;response_type=token&amp;v=5.101&amp;revoke=1">Получить токен</a>
                        <br>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-body">
                        <h5>Cписок активных аккаунтов</h5>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Ссылка на профиль</th>
                                <th scope="col">Токен</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach($accounts as $account)
                                <tr>
                                    <th scope="row">{{ $i++ }}</th>
                                    <td>{{ $account->name }}</td>
                                    <td style=" max-width: 350px;">{{ $account->token }}</td>
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
