@php global $router @endphp
@extends('layouts.master')

@section('title', 'Artists')

@section('head')
    <style>

    </style>
@endsection

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h3>Albums</h3>
            <a class="btn btn-primary" href="/{{ ADMIN_URL }}/albums/new">New</a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Title</th>
                    <th class="text-center">Artist</th>
                    <th class="text-center">Number of tracks</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($list as $album)
                    <tr>
                        <td>
                            @if(isset($album['image']))
                                <img style="max-height: 3rem; max-width: 3rem; margin: .5rem" src="/content/images/{{ $album['image'] }}">
                            @endif
                            <span>{{ $album['title'] }}</span>
                        </td>
                        <td class="text-center">{{ $album['artist']['name'] }}</td>
                        <td class="text-center">{{ count($album['tracks']) }}</td>
                        <td class="text-center">
                            <a href="/{{ ADMIN_URL }}/albums/edit/{{ $album['_id'] }}" class="btn btn-primary">Edit</a>
                            <a href="/{{ ADMIN_URL }}/albums/upload-tracks/{{ $album['_id'] }}" class="btn btn-primary">Tracks</a>
                            <a href="/{{ ADMIN_URL }}/albums/delete/{{ $album['_id'] }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
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