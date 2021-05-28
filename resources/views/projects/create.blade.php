@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3>Новый проект</h3>

                <form action="{{ route('projects.store') }}" method="post" id="create-project-form" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group mt-4">
                        <input name="name" id="name" rows="10" class="form-control form-control-lg" placeholder="Название проекта" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <textarea name="description" id="description" rows="10" class="form-control form-control-lg" placeholder="Введите описание проекта..." minlength="1"></textarea>
                    </div>

                    <div class="form-group mt-4">
                        <input type="number" name="publication_rate" id="publication_rate" class="form-control form-control-lg" placeholder="Норма кол-ва публикаций" min="0">
                    </div>

                    <div class="form-group mt-4">
                        <input name="site" id="site" rows="10" class="form-control form-control-lg" placeholder="Сайт" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="vk" id="vk" rows="10" class="form-control form-control-lg" placeholder="ВК" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="ok" id="ok" rows="10" class="form-control form-control-lg" placeholder="ОК" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="fb" id="fb" rows="10" class="form-control form-control-lg" placeholder="ФБ" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="insta" id="insta" rows="10" class="form-control form-control-lg" placeholder="Инста" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="y_dzen" id="y_dzen" rows="10" class="form-control form-control-lg" placeholder="Я.Дзен" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="y_street" id="y_street" rows="10" class="form-control form-control-lg" placeholder="Я.Район" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="yt" id="yt" rows="10" class="form-control form-control-lg" placeholder="Youtube" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="tg" id="tg" rows="10" class="form-control form-control-lg" placeholder="Telegram" minlength="1">
                    </div>

                    <div class="form-group mt-4">
                        <input name="tt" id="tt" rows="10" class="form-control form-control-lg" placeholder="TikTok" minlength="1">
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Создать</button>
                        <a href="{{ url()->previous() }}" class="btn btn-link">Назад</a>
                    </div>
                </form>

                @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
