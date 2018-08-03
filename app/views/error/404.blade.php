@php
    global $router;
@endphp
@extends('layouts.master')

@section('title', 'صفحه وجود ندارد!')

@section('head')
    <style>
        .not-found-persian {
            margin: 1rem 0;
            color: #C0392B;
        }
    </style>
@endsection

@section('body')
    <div class="row vertical-most-screen-height-center top-margin-fix">
        <div class="col-lg-4"></div>
        <div class="col-lg-4 text-center">
            <i class="fa fa-meh-o fa-2x"></i>
            <h1 class="not-found-persian">صفحه وجود ندارد.</h1>
            <h4>404 NOT FOUND</h4>
        </div>
        <div class="col-lg-4"></div>
    </div>
@endsection

@section('scripts')
    <script>

    </script>
@endsection