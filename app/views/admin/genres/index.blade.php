@php global $router @endphp
@extends('layouts.master')

@section('title', 'Genres')

@section('head')
    <style>

    </style>
@endsection

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h3>Genres</h3>
            <a class="btn btn-primary" href="/{{ ADMIN_URL }}/genres/new">New</a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Title</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($list as $genre)
                    <tr>
                        <td>
                            @if(isset($genre['image']))
                                <img style="max-height: 3rem; max-width: 3rem; margin: .5rem" src="/content/images/{{ $genre['image'] }}">
                            @endif
                            <span>{{ $genre['title'] }}</span>
                        </td>
                        <td class="text-center">
                            <a href="/{{ ADMIN_URL }}/genres/edit/{{ $genre['_id'] }}" class="btn btn-primary">Edit</a>
                            <a href="/{{ ADMIN_URL }}/genres/delete/{{ $genre['_id'] }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

    </script>
@endsection