@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">

                @include('ideas.partials._menu')

                <h3 class="mb-3">Архив идей</h3>

                @include('ideas.partials._table-archive')

                <div class="d-flex justify-content-center">
                    {{ $ideas->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
