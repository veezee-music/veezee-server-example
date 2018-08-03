@php global $router @endphp
@extends('layouts.master')

@section('title', 'Edit Artist')

@section('head')
    <style>

    </style>
@endsection

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h3>Edit Artist</h3>
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
    <form class="row" action="/{{ ADMIN_URL }}/artists/edit-post/{{ $artist['_id'] }}" method="post" enctype="multipart/form-data">
        <div class="col-lg-6">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="{{ $artist['name'] }}" class="form-control">
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" name="image" id="image" accept=".jpg,.png">
                @if(isset($artist['image']))
                    <div>
                        <img style="max-width: 10rem; max-height: 10rem; margin: 1rem;" src="/content/images/{{ $artist['image'] }}">
                    </div>
                @endif
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