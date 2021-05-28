@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">

            @include('ideas.partials._menu')

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>База идей</h3>

                <div>
                    <a href="{{ route('ideas.create') }}" class="btn btn-primary">Добавить</a>
                </div>
            </div>

            @include('ideas.partials._table')

            <div class="d-flex justify-content-center">
                {{ $ideas->links() }}
            </div>
        </div>
    </div>
</div>
<script>
    ClassicEditor
        .create( document.querySelector( '#editor' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
@endsection
