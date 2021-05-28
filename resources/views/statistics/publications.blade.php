@extends('layouts.app')
<script src="{{ mix('js/addTableStatistic.js') }}" async></script>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">

                <h4 class="mb-3">Статистика по темам</h4>

                @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="">
                    <form action="{{ route('statistics.publications') }}">
                        <label>
                            <input type="date" name="date_start" value="{{ $dateStart }}">
                        </label>
                        <label>
                            <input type="date" name="date_end" value="{{ $dateEnd }}">
                        </label>
                            <button type="submit" class="btn btn-primary">Фильтровать</button>
                    </form>
                </div>
                <br>
                @if($dateStart > $dateEnd)
                    <label>Пожалуйста, выберите другую дату</label>
                @endif
                <div id="table-scroll" class="table-scroll">
                    <div class="table-wrap">
                        <table class="main-table table table-bordered dataTable no-footer">

                            <thead>

                            <tr>

                                <th class="fixed-side" scope="col">&nbsp;</th>

                                @if($dates == 0)
                                @else
                                    @foreach($dates as $date)
                                        @php
                                            $dateEndSort = $date
                                        @endphp
                                     <th scope="col"><a target="_blank" href="{{route("statistics.sort_table")}}?date_start={{$date}}&date_end={{ $dateEndSort = date("Y-m-d", strtotime($dateEndSort.'+ 1 days'))}}"> {{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d.m') }} </a></th>
                                    @endforeach
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @if($dates == 0)
                            @else
{{--                                {{dd($projects)}}--}}
                                @foreach($statistics as $id => $statistic)
{{--                                    {{dd($statistics)}}--}}
                                    <tr>
                                        <th class="fixed-side">{{ $statistic['name'] }}</th>
                                        @foreach($dates as $date)
                                            <td>
                                                @if(\array_key_exists($date, $statistic['dates']))
                                                    @php
                                                        $dateEndSort = $date
                                                    @endphp
                                                   <a target="_blank" href="{{route("statistics.post_project",$statistic['id'])}}?date_start={{$date}}&date_end={{ $dateEndSort = date("Y-m-d", strtotime($dateEndSort.'+ 1 days'))}}"> <span class="{{ $statistic['dates'][$date]['class'] }} font-weight-bold">{{ $statistic['dates'][$date]['count'] }}</span> </a>
                                                @else
                                                    0
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <br>

                    </div>
                </div>
            </div>
        </div>
        @include('posts.partials._table')
    </div>

    {{--    <style>--}}
    {{--        .dtHorizontalExampleWrapper {--}}
    {{--            max-width: 600px;--}}
    {{--            margin: 0 auto;--}}
    {{--        }--}}

    {{--        #dtHorizontalExample th, td {--}}
    {{--            white-space: nowrap;--}}
    {{--        }--}}

    {{--        table.dataTable thead .sorting:after,--}}
    {{--        table.dataTable thead .sorting:before,--}}
    {{--        table.dataTable thead .sorting_asc:after,--}}
    {{--        table.dataTable thead .sorting_asc:before,--}}
    {{--        table.dataTable thead .sorting_asc_disabled:after,--}}
    {{--        table.dataTable thead .sorting_asc_disabled:before,--}}
    {{--        table.dataTable thead .sorting_desc:after,--}}
    {{--        table.dataTable thead .sorting_desc:before,--}}
    {{--        table.dataTable thead .sorting_desc_disabled:after,--}}
    {{--        table.dataTable thead .sorting_desc_disabled:before {--}}
    {{--            bottom: .5em;--}}
    {{--        }--}}
    {{--    </style>--}}
    <style>
        .card.card-table {
            overflow: hidden;
            display: none;
        }

        table{
            font-size: 14.4px;
        }
    </style>
@endsection
