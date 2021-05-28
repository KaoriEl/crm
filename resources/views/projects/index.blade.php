@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-3">Активные проекты</h4>

                <div>
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">Добавить</a>
                </div>
                </div>

                <table border="1px solid black">
                </table>
                @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <table class="table table-striped table-sm table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название проекта</th>
                        <th>Описание проекта</th>
                        <th>Удалить</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $key => $project)
                            <tr>
                                <td width="1%">{{ $project->id }}</td>
                                <td><a href="{{ route("projects.edit", $project) }}">{{ $project->name }}</a></td>
                                <td>{{ $project->description }}</td>
                                <td width="1%">
                                    <form action="<?php echo e(route('projects.archived.store', $project->id)); ?>" method="post">
                                        <?php echo csrf_field(); ?>

                                        <button type="submit" class="btn btn-primary mr-1" title="Архивировать проект">Архивировать</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-4">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
