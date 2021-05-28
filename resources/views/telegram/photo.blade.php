<?php
/**
 * Created by PhpStorm.
 * User: Thermaltake
 * Date: 17.01.2020
 * Time: 16:14
 */
?>
@extends('telegram.layout')

@section('content')
    <form action="{{ url('/telegram/store-photo') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <div class="custom-file">
                <input type="file" id="file" name="file" class="custom-file-input">
                <label class="custom-file-label" for="file">Choose file</label>
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
@endsection
