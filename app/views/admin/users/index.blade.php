@php global $router @endphp
@extends('layouts.master')

@section('title', 'Users')

@section('head')
    <style>

    </style>
@endsection

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h3>Genres</h3>
            <a class="btn btn-primary" href="/{{ ADMIN_URL }}/users/new">New</a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th class="text-center">Sessions</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($list as $user)
                    <tr>
                        <td>
                            @if(isset($user['image']))
                                <img style="max-height: 3rem; max-width: 3rem; margin: .5rem" src="/content/images/{{ $user['image'] }}">
                            @endif
                            <span>{{ $user['name'] }}</span>
                        </td>
                        <td class="text-center">{{ count($user['sessions']) }}</td>
                        <td class="text-center">
                            <a href="/{{ ADMIN_URL }}/users/edit/{{ $user['_id'] }}" class="btn btn-primary">Edit</a>
                            <a href="/{{ ADMIN_URL }}/users/delete/{{ $user['_id'] }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
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