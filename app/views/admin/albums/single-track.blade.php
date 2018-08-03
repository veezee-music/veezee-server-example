@php global $router @endphp
@extends('layouts.master')

@section('title', 'Edit Track')

@section('head')
    <style>

    </style>
@endsection

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h3>Edit Track - <a href="/{{ ADMIN_URL }}/albums/edit/{{ $albumId }}">Edit album</a></h3>
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
    <form class="row" action="/{{ ADMIN_URL }}/albums/single-track-post/{{ $albumId }}/{{ $track['_id'] }}" method="post" enctype="multipart/form-data">
        <div class="col-lg-6">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" value="{{ $track['title'] }}" class="form-control">
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" name="image" id="image">
                @if(isset($track['image']))
                    <div>
                        <img id="artwork-img-preview" style="max-width: 10rem; max-height: 10rem; margin: 1rem;" src="/content/images/{{ $track['image'] }}">
                    </div>
                    <div>
                        <br>
                        <button type="button" onclick="extractColors()">Extract colors</button>
                        <br><br>
                        <div>
                            <label for="primaryColor">Primary</label>
                            <input id="primaryColor" name="primaryColor" type="color" {{ isset($track['colors']['primaryColor']) ? "value=" . $track['colors']['primaryColor'] . "" : "" }}>
                            <br>
                            <label for="accentColor">Accent</label>
                            <input id="accentColor" name="accentColor" type="color" {{ isset($track['colors']['accentColor']) ? "value=" . $track['colors']['accentColor'] . "" : "" }}>
                        </div>
                    </div>
                @endif
                <br>
                <label for="readImageFromAlbum">Read image from album (replaces current image with the one from album)</label>
                <input type="checkbox" name="readImageFromAlbum" id="readImageFromAlbum">
                <br>
                <label for="publishUpdate">Publish Update</label>
                <input type="checkbox" name="publishUpdate" id="publishUpdate" {{ !isset($track['publishUpdate']) || $track['publishUpdate'] ? 'checked' : '' }}>
            </div>
        </div>
        <div class="col-lg-12 text-center">
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="/assets/js/ImageAnalyzer.js"></script>
    <script>
        function rgb2hex(red, green, blue) {
            var rgb = blue | (green << 8) | (red << 16);
            return '#' + (0x1000000 + rgb).toString(16).slice(1)
        }

        function hex2rgb(hex) {
            // long version
            r = hex.match(/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i);
            if (r) {
                return r.slice(1,4).map(function(x) { return parseInt(x, 16); });
            }
            return null;
        }

        var backgroundColor, primaryColor, secondaryColor, accentColor = null;
        function extractColors() {
            var imageNode = document.getElementById('artwork-img-preview');

            ImageAnalyzer(imageNode.src, function(bgcolor, pColor, sColor, detailColor) {
                primaryColor = pColor;
                primaryColor = primaryColor.split(',');
                primaryColor = rgb2hex(primaryColor[0], primaryColor[1], primaryColor[2]);
                document.getElementById('primaryColor').value = primaryColor;

                accentColor = detailColor;
                accentColor = accentColor.split(',');
                accentColor = rgb2hex(accentColor[0], accentColor[1], accentColor[2]);
                document.getElementById('accentColor').value = accentColor;
            });
        }
    </script>
@endsection