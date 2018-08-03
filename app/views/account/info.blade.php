
@extends('layouts.master')

@section('title', 'User info')

@section('head')
    <style>
        h6 {
            font-weight: bold;
        }

        h6 i {
            display: inline-block;
        }

        h6 span {
            margin-left: .3rem;
        }

        .user-data-rows .row {
            margin: 1.3rem 0;
        }
    </style>
@endsection

@section('body')
    <div id="app" v-cloak>

        <div class="row vertical-most-screen-height-center top-margin-fix">
            <div class="col-lg-3"></div>
            <div class="col-lg-6 basic-card-layout">
                <h4 class="text-center">User account</h4>
                <br>

                {{--<div style="display: {{ isset($redirect) && $redirect != null ? 'block' : 'none' }}" class="alert alert-warning" role="alert" id="login-top-alert">برای دسترسی به این بخش باید به حساب خود وارد شوید.</div>--}}

                <div class="row">
                    <div class="col-lg-6">
                        <a href="/account/logout" class="btn btn-block btn-secondary">Log out</a>
                    </div>
                    <div class="col-lg-6">
                        <a href="/account/edit" class="btn btn-block btn-primary">Edit info</a>
                    </div>
                    {{--<div class="col-lg-4">--}}
                        {{--<a href="" class="btn btn-block btn-danger">حذف حساب</a>--}}
                    {{--</div>--}}
                </div>

                <br>
                <section class="user-data-rows">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6><i class="fa fa-user"></i><span>Name</span></h6>
                            <span>{{ $_SESSION['name'] ?? '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <h6><i class="fa fa-envelope"></i><span>Email address</span></h6>
                            <span>{{ $_SESSION['email'] ?? '-' }}</span>
                        </div>
                    </div>
                </section>

                <div class="progress-bar-container" v-if="loading">
                    <div class="progress-bar">
                        <div class="progress-bar-value"></div>
                    </div>
                </div>
                <div class="alert alert-success" role="alert" v-if="successAlertText != ''">(( successAlertText ))</div>
                <div class="alert alert-danger" role="alert" v-if="failureAlertText != ''">(( failureAlertText ))</div>
            </div>
            <div class="col-lg-3"></div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            delimiters: ["((","))"],
            components: {
            },
            data: {
                loading: false,
                loginWithEmail: false,

                successAlertText: '',
                failureAlertText: '',

                phoneNumber: '',
                email: '',
                password: ''
            },
            mounted: function () {
                var self = this;

            },
            mixins: [globalMixin],
            methods: {
                submit: function () {
                    var self = this;

                    self.loading = true;
                    axios.post('/api/v1/account/login', { phoneNumber: this.phoneNumber, email: this.email, password: this.password }).then(function (response) {
                        self.failureAlertText = '';

                        var urlSearchParams = new URLSearchParams(window.location.search);
                        var redirect = '';
                        if(!urlSearchParams.has('redirect')) {

                        } else {
                            redirect = urlSearchParams.get('redirect');
                            window.location.replace(redirect + '?token=' + response.data.token);
                        }
                    }).catch(function (error) {
                        self.failureAlertText = error.response.data.error;
                    }).then(function () {
                        self.loading = false;
                    });
                },
                resetAlerts: function () {
                    this.successAlertText = '';
                    this.failureAlertText = '';
                }
            },
            watch: {

            }
        });
    </script>
@endsection