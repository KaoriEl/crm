@extends('layouts.app')

@section('content')
    <div class="container" style="max-width: 1200px!important;">
        <div class="row">
            <div class="col">

                <h4 class="mb-4">Архив</h4>
                <div class="">
                    <form action="{{ route('arhived.index') }}">
                        <label>
                            <input type="date" name="date_start" value="{{ $dateStart }}">
                        </label>
                        <label>
                            <input type="date" name="date_end" value="{{ $dateEnd }}">
                        </label>
                        <button type="submit" class="btn btn-primary">Фильтровать</button>
                    </form>
                </div>
                @if($dateStart > $dateEnd)
                    <label>Пожалуйста, выберите другую дату</label>
                @endif

                @if($dateStart > $dateEnd)
                @else
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @include('posts.partials._table-archive')

                    <div class="d-flex justify-content-center mt-4">
{{--                        {{ $posts->links() }}--}}
                    </div>
            </div>
        </div>
    </div>
@endif
@endsection
