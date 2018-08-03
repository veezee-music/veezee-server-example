@php global $router @endphp
@extends('layouts.master')

@section('title', 'New Playlist')

@section('head')
    <style>

    </style>
@endsection

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h3>New Playlist</h3>
            @if($opr != null)
                <br>
                @if($opr['type'])
                    <div class="alert alert-{{ $opr['class'] }}" role="alert">
                        <span>{{ $opr['text'] }}</span>
                    </div>
                @endif
                @if(isset($opes))
                    @foreach($opes as $error)
                        <div class="alert alert-warning std-vertical-margin" role="alert">{{ $error }}</div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
    <br>
    <form class="row" action="/{{ ADMIN_URL }}/playlists/new-post" method="post" enctype="multipart/form-data">
        <div class="col-lg-6">
            <div class="form-group">
                <label for="name">Title</label>
                <input type="text" name="title" id="title" class="form-control">
            </div>
        </div>
        <div class="col-lg-12">
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>
    </form>
@endsection

@section('scripts')
    <script>

    </script>
@endsection