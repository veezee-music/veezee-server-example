@php global $router @endphp
@extends('layouts.master')

@section('title', 'Playlists')

@section('head')
    <style>
        .handle {
            cursor: move;
            cursor: -webkit-grabbing;
        }
    </style>
@endsection

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h3>Playlists</h3>
            <a class="btn btn-primary" href="/{{ ADMIN_URL }}/playlists/new">New</a>
        </div>
    </div>
    <br>
    <div class="row" id="app" v-cloak>
        <button v-if="updatePossible" class="btn btn-success" type="button" @click="updateAllPlaylists()">(( isSubmitting ? 'Wait' : 'Submit changes' ))</button>
        <div class="col-lg-12">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th class="text-center">Number of tracks</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody is="draggable" :element="'tbody'" :options="{ handle: '.handle' }" @end="onPlaylistDragEnd" v-model="playlists">
                    <tr v-for="playlist in playlists">
                        <td>
                            <i class="fa fa-arrows handle"></i>
                            <span>(( playlist.title ))</span>
                        </td>
                        <td class="text-center">(( playlist.tracks.length ))</td>
                        <td class="text-center">
                            <a :href="`/{{ ADMIN_URL }}/playlists/edit/${playlist._id.$oid}`" class="btn btn-primary">Edit</a>
                            <a :href="`/{{ ADMIN_URL }}/playlists/choose-tracks/${playlist._id.$oid}`" class="btn btn-primary">Tracks</a>
                            <a :href="`/{{ ADMIN_URL }}/playlists/delete/${playlist._id.$oid}`" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            delimiters: ["((","))"],
            components:{

            },
            data: {
                playlists: [],

                isSubmitting: false,
                updatePossible: false
            },
            mounted: function () {
                var self = this;

                this.getPlaylists();
            },
            methods: {
                getPlaylists: function () {
                    var self = this;

                    axios.get('/{{ ADMIN_URL }}/playlists/get/')
                        .then(function (res) {
                            self.playlists = res.data;
                        })
                        .catch(function (err) {
                        })
                        .then(function () {
                        });
                },
                updateAllPlaylists: function () {
                    var self = this;

                    self.isSubmitting = true;

                    axios.post('/{{ ADMIN_URL }}/playlists/update-all-playlists', { playlists: self.playlists })
                        .then(function (res) {
                            alert('done successfully.');
                            self.updatePossible = false;
                        })
                        .catch(function (err) {
                            alert('error');
                        })
                        .then(function () {
                            self.isSubmitting = false;
                        });
                },
                onPlaylistDragEnd: function () {
                    this.updatePossible = true;
                }
            },
            watch: {

            }
        });
    </script>
@endsection