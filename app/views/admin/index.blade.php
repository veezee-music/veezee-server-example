@php global $router @endphp
@extends('layouts.master')

@section('title', 'Admin dashboard')

@section('head')
    <style>

    </style>
@endsection

@section('body')
    <div class="row">
        <div class="col-lg-12 text-center">
            <h3>Admin dashboard</h3>
        </div>
    </div>
    <br>
    @if($opr != null)
        <br>
        <div class="row">
            <div class="col-lg-12">
                @if($opr['type'])
                    <div class="alert alert-{{ $opr['class'] }}" role="alert">
                        <span>{{ $opr['text'] }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>

    </script>
@endsection