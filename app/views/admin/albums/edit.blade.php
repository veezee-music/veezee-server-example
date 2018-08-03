@php global $router @endphp
@extends('layouts.master')

@section('title', 'Edit Album')

@section('head')
    <style>

    </style>
@endsection

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h3>Edit Album - <a href="/{{ ADMIN_URL }}/albums/upload-tracks/{{ $album['_id'] }}">Tracks list</a></h3>
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
    <form class="row" action="/{{ ADMIN_URL }}/albums/edit-post/{{ $album['_id'] }}" method="post" enctype="multipart/form-data">
        <div class="col-lg-4">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" value="{{ $album['title'] }}" class="form-control">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label for="artist">Artist (Required)</label>
                <select name="artist" id="artist">
                    <option disabled>Select an artist</option>
                    @foreach($artists as $artist)
                        <option value="{{ $artist['_id'] }}" {{ $artist['_id'] == $album['artist']['_id'] ? 'selected' : '' }}>{{ $artist['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label for="mainGenre">Main genre</label>
                <select name="mainGenre" id="mainGenre">
                    @foreach($genres as $genre)
                        <option {{ @$album['genres'][0]['title'] == $genre['title'] ? 'selected' : null }} value="{{ (string)$genre['_id'] }}">{{ $genre['title'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label for="genres">Genres</label>
                <select name="genres[]" id="genres" multiple>
                    @foreach($genres as $genre)
                        <option {{ @in_array($genre['title'], $genresTitles) && (@$album['genres'][0]['title'] != $genre['title']) ? 'selected' : null }} value="{{ (string)$genre['_id'] }}">{{ $genre['title'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" name="image" id="image">
                @if(isset($album['image']))
                    <div>
                        <img id="artwork-img-preview" style="max-width: 10rem; max-height: 10rem; margin: 1rem;" src="/content/images/{{ $album['image'] }}">
                        <button class="btn btn-danger" type="button" onclick="removeImage()">Remove image</button>
                    </div>
                    <div>
                        <br>
                        <button type="button" onclick="extractColors()">Extract colors</button>
                        <br><br>
                        <div>
                            <label for="primaryColor">Primary</label>
                            <input id="primaryColor" name="primaryColor" type="color" {{ isset($album['colors']['primaryColor']) ? "value=" . $album['colors']['primaryColor'] . "" : "" }}>
                            <br>
                            <label for="accentColor">Accent</label>
                            <input id="accentColor" name="accentColor" type="color" {{ isset($album['colors']['accentColor']) ? "value=" . $album['colors']['accentColor'] . "" : "" }}>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <div class="form-group">
                <br><br>
                <label for="headerActive">Header active</label>
                <input type="checkbox" name="headerActive" id="headerActive" {{ isset($album['header']) && $album['header']['active'] ? 'checked' : '' }}>
                <br><br>
                <label for="headerImage">Header image (rectangle)</label>
                <input type="file" name="headerImage" id="headerImage" accept=".jpg,.png">
                @if(isset($album['header']) && isset($album['header']['image']))
                    <div>
                        <img style="max-width: 20rem; max-height: 10rem; margin: 1rem;" src="/content/images/{{ $album['header']['image'] }}">
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-2 text-center">
            <label for="publishUpdate">Publish update</label>
            <input type="checkbox" name="publishUpdate" id="publishUpdate" {{ isset($album['publishUpdate']) ? $album['publishUpdate'] ? 'checked' : '' : 'checked' }}>
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

        function removeImage() {
            axios.post('/{{ ADMIN_URL }}/albums/remove-image-post/{{ $album['_id'] }}')
                .then(function (res) {
                    window.location.reload();
                })
                .catch(function (err) {
                    alert(err.response.data.error);
                })
                .then(function () {

                });
        }
    </script>
@endsection