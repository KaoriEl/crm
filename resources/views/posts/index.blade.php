@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">

                <h4 class="mb-3">Все задачи</h4>

                @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @include('posts.partials._table')

            </div>
        </div>
    </div>
@endsection
