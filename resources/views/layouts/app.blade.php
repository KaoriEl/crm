<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- CSRF-token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @auth
    {{-- API-token --}}
    <meta name="api-token" content="{{ Auth::user()->api_token }}">
    @endauth

    <meta name="pusher-app-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-app-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">

    <title>Редакция</title>

    {{--Material Design icons--}}
    <link rel="stylesheet" href="{{ asset('vendor/material-design-icons/css/materialdesignicons.min.css') }}">

    {{-- Styles --}}
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    {{-- <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.css" rel="stylesheet">
{{--    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.js"></script> --}}

    <script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/ckeditor.js"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}"> Редакция</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @auth

                        <li class="nav-item">
                            <a class="nav-link{{ request()->routeIs('posts.index') ? ' active' : '' }}" href="{{ route('posts.index') }}">Задачи</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link{{ (request()->routeIs('ideas.index') || request()->routeIs('ideas.archived.index')) ? ' active' : '' }}" href="{{ route('ideas.index') }}">База идей</a>
                        </li>

                        @can('view-any', \App\Models\User::class)
                        <li class="nav-item">
                            <a class="nav-link{{ request()->routeIs('users.index') ? ' active' : '' }}" href="{{ route('users.index') }}">Пользователи</a>
                        </li>
                        @endcan

                        {{-- @can('view-archived', \App\Models\Post::class) --}}
                        <li class="nav-item">
                            <a class="nav-link{{ request()->routeIs('posts.archived.index') ? ' active' : '' }}" href="{{ route('posts.archived.index') }}">Архив</a>
                        </li>
                            {{-- @endcan --}}


                        @can('create', \App\Models\Project::class)
                        <li class="nav-item">
                            <a class="nav-link{{ request()->routeIs('projects.index') ? ' active' : '' }}" href="{{ route('projects.index') }}">Проекты</a>
                        </li>
                        @endcan


                            <li class="nav-item dropdown">
                                <a class="nav-link  dropdown-toggle {{ request()->routeIs('statistics.publications') ? ' active' : '' }}" href="#" data-toggle="dropdown">  Статистика  </a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ 'https://docs.google.com/spreadsheets/d/' . config('app.GOOGLE_SPREADSHEET_ID') . '/edit?usp=sharing' }}" target="_blank">Статистика по публикациям</a>
                                    </li>
                                    <li><a class="dropdown-item" href="{{ route('statistics.publications') }}">Статистика по темам</a></li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link{{ request()->routeIs('export') ? ' active' : '' }}" href="{{ route('excel.export')  }}" target="_blank">Выгрузка</a>
                            </li>

                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">

                        @auth
                        @can('create', \App\Models\Post::class)
                        <div class="d-flex align-items-center mr-3">
                            <a href="{{ route('posts.create') }}" class="btn btn-outline-primary">Поставить задачу</a>
                        </div>
                        @endcan

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle{{ Auth::user()->hasRole('admin') ? ' crown' : '' }}" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <img src="{{ Auth::user()->gravatar }}" alt="{{ Auth::user()->name }}" width="24" height="24" class="rounded mr-1"> {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a href="{{ route('profile') }}" class="dropdown-item">Настройки</a>

                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <div id="toasts" class="toasts"></div>

    @auth
    <script>
        window.App = {
            User: @json(Auth::user())
        };

    </script>
    @endauth

    {{-- Scripts --}}
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>

    <script>
        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>

    <script src="https://cdn.tiny.cloud/1/{{ config('app.tinymce_key') }}/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea#summernote',
            plugins: 'link image code',
            convert_urls: false

        });
        tinymce.init({
            selector: 'textarea#comment',
        });
        tinymce.init({
            selector: 'textarea#text',

        });
    </script>
    {{-- <script>
        $('#summernote').summernote({
            placeholder: 'Тезисы'
            , tabsize: 2
            , height: 120
            , toolbar: [comment
                ['font', ['bold', 'underline', 'clear']]
                , ['para', ['ul', 'ol']]
                , ['insert', ['link']]
                , ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

    </script> --}}
</body>
</html>
