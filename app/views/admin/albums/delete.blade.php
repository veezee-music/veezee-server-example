@php global $router @endphp
@extends('layouts.master')

@section('title', 'Delete Albums')

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h3>Delete Album: {{ $album['title'] }}</h3>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12 text-center">
            <form method="post" action="/{{ ADMIN_URL }}/albums/delete-post/{{ $album['_id'] }}">
                <h5>Are you sure?</h5>
                <button class="btn btn-danger" type="submit">Yes, Delete</button>
            </form>
        </div>
    </div>
@endsection