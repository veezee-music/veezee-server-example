@php global $router @endphp
@extends('layouts.master')

@section('title', 'New Album')

@section('head')
    <style>
        .handle {
            cursor: move;
            cursor: -webkit-grabbing;
        }
    </style>
@endsection

@section('body')
    <script type="text/x-template" id="uploadItemTemplate">
        <div class="row" style="background-color: white; padding: 1.5rem; margin: 1rem; border: 1px solid lightgrey;">
            <div class="col-lg-1" v-if="track.displaySize == undefined">
                <i class="fa fa-arrows handle"></i>
            </div>
            <div class="col-lg-1" v-if="track.displaySize == undefined">
                <div @click="play" v-if="!isPlaying"><i class="fa fa-play"></i></div>
                <div @click="pause" v-if="isPlaying"><i class="fa fa-pause"></i></div>
            </div>
            <div class="col-lg-1">
                <button @click="$emit('delete')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
            </div>
            <div v-if="track._id != undefined" class="col-lg-6">
                <a :href="`/{{ ADMIN_URL }}/albums/single-track/{{ $album['_id'] }}/${track._id.$oid}`">(( track.title ))</a>
            </div>
            <div v-else>(( track.title ))</div>
            <div class="col-lg-2">
                (( track.displaySize != undefined ? track.displaySize : '' ))
            </div>
            <div class="col-lg-2">

            </div>
        </div>
    </script>

    <div class="row">
        <div class="col-lg-12">
            <h3>Upload Tracks - <a href="/{{ ADMIN_URL }}/albums/edit/{{ $album['_id'] }}">Edit album</a></h3>
            <h5>Album: <span style="color: red">{{ $album['title'] }}</span></h5>
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
    <div id="app" v-cloak>
        <div class="row">
            <div class="col-lg-6">
                <label for="trackInput">Select tracks (mp3, m4a)</label>
                <input type="file" name="trackInput" id="trackInput" multiple accept=".mp3,.m4a">
                <upload-item v-for="(element, index) in queue" :track="element" v-on:delete="deleteTrackFromQueue(index)"></upload-item>
                <div class="text-center" v-if="queue.length > 0">
                    <label v-if="!isUploading" for="publishUpdate">Publish Update</label>
                    <input type="checkbox" v-model="publishUpdate" id="publishUpdate">
                    <button class="btn btn-primary" type="button" @click="uploadTracks()">(( isUploading ? 'Uploading %' + currentUploadProgress : 'Upload' ))</button>
                </div>
            </div>
            <div class="col-lg-6">
                <label for="trackInput">Already uploaded tracks (refresh the page before any changes)</label>
                <draggable element="div" :options="{ handle: '.handle' }" @end="onTrackDragEnd" v-model="tracks">
                    <upload-item v-for="(element, index) in tracks" :track="element" v-on:delete="deleteTrackFromServer(index)"></upload-item>
                </draggable>
                <div class="text-center" v-if="updatePossible && !refreshNeeded">
                    <button class="btn btn-primary" type="button" @click="updateAlreadyUploadedTracks()">(( isSubmitting ? 'Wait' : 'Submit' ))</button>
                </div>
                <div style="color: red; padding: 1rem;" v-else-if="refreshNeeded">
                    <span>Refresh the page if you want to make changes to files</span>
                    <button class="btn btn-sm btn-primary" @click="reloadPage()">Reload</button>
                </div>
                <div style="padding-bottom: 10rem"></div>
            </div>
        </div>
        <br><br><br>
        {{--<h3>Choose tracks from other albums to add to this album</h3>--}}
        {{--<div class="row">--}}
            {{--<div class="col-lg-6 form-input-container">--}}
                {{--<label class="label light">Album</label>--}}
                {{--<v-select class="single-select"--}}
                          {{--:options="a_albums"--}}
                          {{--label="title"--}}
                          {{--v-model="a_album"--}}
                          {{--:on-change="albumOnChange"--}}
                          {{--placeholder="Select an album">--}}
                    {{--<div slot="no-options">Nothing to show</div>--}}
                {{--</v-select>--}}
            {{--</div>--}}
            {{--<div class="col-lg-6 form-input-container">--}}
                {{--<label class="label light">Track</label>--}}
                {{--<v-select class="single-select"--}}
                          {{--:options="a_tracks"--}}
                          {{--label="title"--}}
                          {{--v-model="a_track"--}}
                          {{--:on-change="trackOnChange"--}}
                          {{--placeholder="Select a track">--}}
                    {{--<div slot="no-options">Nothing to show</div>--}}
                {{--</v-select>--}}
            {{--</div>--}}
            {{--<draggable element="div" :options="{ handle: '.handle' }" v-model="playlistTracks" class="col-lg-12">--}}
                {{--<upload-item v-for="(element, index) in playlistTracks" :track="element" v-on:delete="deleteTrackFromPlaylist(index)"></upload-item>--}}
            {{--</draggable>--}}
            {{--<div class="col-lg-12 text-center" v-if="playlistTracks.length > 0">--}}
                {{--<div v-if="error != null" class="alert alert-warning std-vertical-margin" role="alert">(( error ))</div>--}}
                {{--<button class="btn btn-primary" type="button" @click="submitPlaylist()">(( isSubmitting ? 'Wait' : 'Add tracks from other albums' ))</button>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<br><br><br>--}}
    </div>
@endsection

@section('scripts')
    <script>
        var albumId = '{{ $album['_id'] }}';
        var uploadItem = {
            template: '#uploadItemTemplate',
            delimiters: ["((","))"],
            props: ['track'],
            name: 'upload-item',
            data: function () {
                return {
                    audio: null,
                    isPlaying: false,
                    albumId: null
                }
            },
            created: function () {
                if(this.track.album !== undefined) {
                    this.albumId = this.track.album._id.$oid;
                } else {
                    this.albumId = albumId;
                }
            },
            methods: {
                play: function () {
                    if(this.audio == null) {
                        this.audio = new Audio('/content/music/albums/' + this.albumId + '/' + this.track.fileName);
                    }
                    this.audio.play();
                    this.isPlaying = true;
                },
                pause: function () {
                    this.audio.pause();
                    this.isPlaying = false;
                }
            },
            watch: {

            }
        };

        var app = new Vue({
            el: '#app',
            delimiters: ["((","))"],
            components:{
                uploadItem: uploadItem,
                vSelect: VueSelect.VueSelect,
            },
            data: {
                publishUpdate: true,
                tracks: [],
                queue: [],
                currentUploadProgress: 0,
                refreshNeeded: false,
                isUploading: false,
                isSubmitting: false,
                updatePossible: false,


                a_albums: [],
                a_tracks: [],
                a_album: null,
                a_track: null,
                playlistTracks: [],
                error: null
            },
            mounted: function () {
                var self = this;

                this.getTracks();

                // self.data = new FormData()

                var input = document.getElementById('trackInput');
                // var formData = new FormData();
                // formData.append('fucj', 'niggaz')
                input.onchange = function(e) {
                    for(var i = 0; i < e.target.files.length; i++) {
                        var f = e.target.files[i];
                        f.displaySize = self.humanFileSize(e.target.files[i].size, true);
                        f.title = f.name.replace(/\.[^/.]+$/, "");
                        self.queue.push(f);
                        // formData.append('tracks[]', f, f.name);
                    }
                    document.getElementById("trackInput").value = "";
                };

                this.getAlbums();
                this.getTracksForSelectedAlbum();
            },
            methods: {
                humanFileSize: function(bytes, si) {
                    var thresh = si ? 1000 : 1024;
                    if(Math.abs(bytes) < thresh) {
                        return bytes + ' B';
                    }
                    var units = si
                        ? ['kB','MB','GB','TB','PB','EB','ZB','YB']
                        : ['KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];
                    var u = -1;
                    do {
                        bytes /= thresh;
                        ++u;
                    } while(Math.abs(bytes) >= thresh && u < units.length - 1);
                    return bytes.toFixed(1)+' '+units[u];
                },
                deleteTrackFromServer: function(index) {
                    this.tracks.splice(index, 1);
                    this.updatePossible = true;
                },
                onTrackDragEnd: function () {
                    this.updatePossible = true;
                },
                deleteTrackFromQueue: function(index) {
                    this.queue.splice(index, 1);
                },
                getTracks: function () {
                    var self = this;

                    axios.get('/{{ ADMIN_URL }}/albums/get-tracks/{{ $album['_id'] }}')
                        .then(function (res) {
                            self.tracks = res.data;
                        })
                        .catch(function (err) {
                            console.log('fuck, damn')
                        })
                        .then(function () {
                        });
                },
                uploadTracks: function () {
                    if(this.isUploading) {
                        return;
                    }

                    this.uploadFromQueueByIndex(0);
                },
                updateAlreadyUploadedTracks: function () {
                    var self = this;

                    self.isSubmitting = true;

                    axios.post('/{{ ADMIN_URL }}/albums/update-already-uploaded-tracks-post/{{ $album['_id'] }}', { tracks: self.tracks })
                        .then(function (res) {
                            alert('done successfully');
                            self.updatePossible = false;
                        })
                        .catch(function (err) {
                            alert(err.response.data.error);
                        })
                        .then(function () {
                            self.isSubmitting = false;
                        });
                },
                uploadFromQueueByIndex: function (index) {
                    var self = this;

                    self.isUploading = true;
                    var formData = new FormData();
                    var track = this.queue[index];
                    formData.append('track', track);

                    var config = {
                        onUploadProgress: function(progressEvent) {
                            var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                            self.currentUploadProgress = percentCompleted;
                        }
                    };

                    axios.post('/{{ ADMIN_URL }}/albums/upload-track-post/{{ $album['_id'] }}?publishUpdate=' + this.publishUpdate, formData, config)
                        .then(function (res) {
                            self.tracks.push(self.queue[index]);
                            self.deleteTrackFromQueue(index);

                            if(self.queue.length !== 0) {
                                self.uploadFromQueueByIndex(0);
                            } else {
                                self.updatePossible = false;
                            }
                        })
                        .catch(function (err) {
                            alert(err.response.data.error);
                        })
                        .then(function () {
                            self.isUploading = false;
                        });
                },
                reloadPage: function() {
                    window.location.reload()
                },




                deleteTrackFromPlaylist: function(index) {
                    this.playlistTracks.splice(index, 1);
                },
                getAlbums: function () {
                    var self = this;

                    axios.get('/{{ ADMIN_URL }}/albums/get-albums/')
                        .then(function (res) {
                            self.a_albums = res.data;
                            self.a_tracks = [];
                        })
                        .catch(function (err) {
                        })
                        .then(function () {
                        });
                },
                getTracksForSelectedAlbum: function (albumId) {
                    var self = this;

                    if(albumId === undefined)
                        return;

                    self.a_tracks = self.a_album.tracks;

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
                    self.a_album = value;
                    self.getTracksForSelectedAlbum(value._id.$oid);
                },
                trackOnChange: function (value) {
                    var self = this;
                    value.album = self.a_album;
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
                    axios.post('/{{ ADMIN_URL }}/albums/add-tracks-from-other-albums-post/{{ $album['_id'] }}', data)
                        .then(function (res) {
                            window.location.href = '/{{ ADMIN_URL }}/albums/upload-tracks/{{ $album['_id'] }}';
                        })
                        .catch(function (err) {
                            self.error = err.response.data.error;
                        })
                        .then(function () {
                            self.isSubmitting = false;
                        });
                },
            },
            watch: {
                isUploading: function (val) {
                    if(val)
                        this.refreshNeeded = true;
                }
            }
        });
    </script>
@endsection