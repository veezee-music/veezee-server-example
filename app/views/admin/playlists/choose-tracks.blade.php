@php global $router @endphp
@extends('layouts.master')

@section('title', 'Choose tracks')

@section('head')
    <style>
        .handle {
            cursor: move;
            cursor: -webkit-grabbing;
        }
    </style>
@endsection

@section('body')
    <script type="text/x-template" id="trackItemTemplate">
        <div class="row" style="background-color: white; padding: 1.5rem; margin: 1rem; border: 1px solid lightgrey;">
            <div class="col-lg-1">
                <i class="fa fa-arrows handle"></i>
            </div>
            <div class="col-lg-1">
                <button @click="$emit('delete')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
            </div>
            <div class="col-lg-6">
                <a :href="'/{{ ADMIN_URL }}/albums/edit/' + track.album._id.$oid">(( track.album.title ))</a> - <b>(( track.title ))</b>
            </div>
        </div>
    </script>

    <div class="row">
        <div class="col-lg-12">
            <h3>Choose Tracks</h3>
            <h5>Playlist: <span style="color: red">{{ $playlist['title'] }}</span></h5>
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
    <div class="row" id="app" v-cloak>
        <div class="col-lg-6 form-input-container">
            <label class="label light">Album</label>
            <v-select class="single-select"
                      :options="albums"
                      label="title"
                      v-model="album"
                      :on-change="albumOnChange"
                      placeholder="Select an album">
                <div slot="no-options">Nothing to show</div>
            </v-select>
            {{--<span class="message" :class="{ 'error-hint': e && require(announcement.location.state) }">استان را انتخاب کنید</span>--}}
        </div>
        <div class="col-lg-6 form-input-container">
            <label class="label light">Track</label>
            <v-select class="single-select"
                      :options="tracks"
                      label="title"
                      v-model="track"
                      :on-change="trackOnChange"
                      placeholder="Select a track">
                <div slot="no-options">Nothing to show</div>
            </v-select>
            {{--<span class="message" :class="{ 'error-hint': e && require(announcement.location.city) }">َشهر را انتخاب کنید</span>--}}
        </div>
        <draggable element="div" :options="{ handle: '.handle' }" v-model="playlistTracks" class="col-lg-12">
            <track-item v-for="(element, index) in playlistTracks" :track="element" v-on:delete="deleteTrackFromPlaylist(index)"></track-item>
        </draggable>
        <div class="col-lg-12 text-center">
            <div v-if="error != null" class="alert alert-warning std-vertical-margin" role="alert">(( error ))</div>
            <button class="btn btn-primary" type="button" @click="submitPlaylist()">(( isSubmitting ? 'Wait' : 'Submit' ))</button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var trackItem = {
            template: '#trackItemTemplate',
            delimiters: ["((","))"],
            props: ['track'],
            name: 'track-item',
            data: function () {
                return {

                }
            },
            created: function () {
            },
            methods: {

            },
            watch: {

            }
        };

        var app = new Vue({
            el: '#app',
            delimiters: ["((","))"],
            components:{
                vSelect: VueSelect.VueSelect,
                trackItem: trackItem
            },
            data: {
                albums: [],
                tracks: [],

                album: null,
                track: null,

                isSubmitting: false,
                error: null,

                playlistTracks: [],
            },
            mounted: function () {
                var self = this;

                this.getAlbums();
                // this.getTracks();
                this.getPlaylist();
            },
            methods: {
                deleteTrackFromPlaylist: function(index) {
                    this.playlistTracks.splice(index, 1);
                },
                getAlbums: function () {
                    var self = this;

                    axios.get('/{{ ADMIN_URL }}/albums/get-albums/')
                        .then(function (res) {
                            self.albums = res.data;
                            self.tracks = [];
                        })
                        .catch(function (err) {
                        })
                        .then(function () {
                        });
                },
                getTracks: function (albumId) {
                    var self = this;

                    if(albumId === undefined)
                        return;

                    self.tracks = self.album.tracks;

                    // axios.get('/admin/albums/get-tracks/' + albumId)
                    //     .then(function (res) {
                    //         self.tracks = res.data;
                    //     })
                    //     .catch(function (err) {
                    //     })
                    //     .then(function () {
                    //     });
                },
                albumOnChange: function (value) {
                    var self = this;
                    self.album = value;
                    self.getTracks(value._id.$oid);
                },
                trackOnChange: function (value) {
                    var self = this;
                    value.album = self.album;
                    self.playlistTracks.push(value);
                    // self.track = value;
                },
                submitPlaylist: function () {
                    var self = this;

                    self.isSubmitting = true;

                    for(var i = 0; i < self.playlistTracks.length; i++) {
                        delete self.playlistTracks[i].album.tracks;
                    }

                    var data = {
                        tracks: self.playlistTracks
                    };
                    axios.post('/{{ ADMIN_URL }}/playlists/choose-tracks-post/{{ $playlist['_id'] }}', data)
                        .then(function (res) {
                            window.location.href = '/{{ ADMIN_URL }}/playlists/';
                        })
                        .catch(function (err) {
                            self.error = err.response.data.error;
                            alert(self.error);
                        })
                        .then(function () {
                            self.isSubmitting = false;
                        });
                },
                getPlaylist: function () {
                    var self = this;

                    axios.get('/{{ ADMIN_URL }}/playlists/get-tracks/{{ $playlist['_id'] }}')
                        .then(function (res) {
                            self.playlistTracks = res.data;
                        })
                        .catch(function (err) {
                            alert('error');
                        })
                        .then(function () {
                        });
                }
            },
            watch: {
                // 'album.tracks': {
                //     handler: function (val) {
                //         console.log('ff')
                //     },
                //     deep: true
                // }
            }
        });
    </script>
@endsection