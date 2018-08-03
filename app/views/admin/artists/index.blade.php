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
            <h3>Artists</h3>
            <a class="btn btn-primary" href="/{{ ADMIN_URL }}/artists/new">New</a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($list as $artist)
                    <tr>
                        <td>
                            @if(isset($artist['image']))
                                <img style="max-height: 3rem; max-width: 3rem; margin: .5rem" src="/content/images/{{ $artist['image'] }}">
                            @endif
                            <span>{{ $artist['name'] }}</span>
                        </td>
                        <td class="text-center">
                            <a href="/{{ ADMIN_URL }}/artists/edit/{{ $artist['_id'] }}" class="btn btn-primary">Edit</a>
                            <a href="/{{ ADMIN_URL }}/artists/delete/{{ $artist['_id'] }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
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