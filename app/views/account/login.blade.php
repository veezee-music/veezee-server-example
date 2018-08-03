
@extends('layouts.master')

@section('title', 'Login')

@section('head')
    <style>
        input {
            font-size: 1rem !important;
        }

        input:focus {
            font-size: 1rem !important;
        }
    </style>
@endsection

@section('body')
    <div id="app" v-cloak>

        <div class="row vertical-most-screen-height-center top-margin-fix">
            <div class="col-lg-4"></div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-sm-3 col-3"></div>
                    <div class="col-sm-6 col-6">
                        <img class="img-fluid" src="/assets/img/veezee-logotype.svg">
                    </div>
                    <div class="col-sm-3 col-3"></div>
                </div>
                <br>
                <hr>
                <h4 class="text-center">Login to your account</h4>
                <br>

                <div style="display: {{ isset($redirect) && $redirect != null ? 'block' : 'none' }}" class="alert alert-warning" role="alert" id="login-top-alert">You need to login to access this page.</div>
                <div style="display: {{ isset($message) && $message != null ? 'block' : 'none' }}" class="alert alert-info" role="alert" id="login-top-alert">{{ $message }}</div>

                <br>
                <section>
                    {{--<div class="form-group iconed row" v-if="!loginWithEmail">--}}
                        {{--<div class="col-sm-12">--}}
                            {{--<i class="fa fa-phone"></i>--}}
                            {{--<input @keyup.enter="submit" v-model="phoneNumber" type="text" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required class="form-control text-left" dir="ltr" placeholder="Phone number">--}}
                            {{--<small class="hint" @click="loginWithEmail = !loginWithEmail"><i class="fa fa-envelope hint-icon"></i><span>Login with Email</span></small>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group iconed row">
                        <div class="col-sm-12">
                            <i class="fa fa-envelope"></i>
                            <input @keyup.enter="submit" v-model="email" type="email" required class="form-control text-left" dir="ltr" placeholder="Email address">
                            {{--<small class="hint" @click="loginWithEmail = !loginWithEmail"><i class="fa fa-phone hint-icon"></i><span>Login with phone number</span></small>--}}
                        </div>
                    </div>
                    <div class="form-group iconed row">
                        <div class="col-sm-12">
                            <i class="fa fa-key"></i>
                            <input @keyup.enter="submit" v-model="password" type="password" required class="form-control text-left" dir="ltr" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button @click="submit" type="button" class="btn btn-block btn-primary">Login</button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 text-center">
                            <a href="/account/reset-password"><small>Forgot password?</small></a>
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
                <hr>
                <div class="form-group row">
                    <div class="col-sm-12">
                        <a href="/account/register" class="btn btn-block btn-info">Create a new account</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4"></div>
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

                var urlSearchParams = new URLSearchParams(window.location.search);
                var redirect = urlSearchParams.get('redirect');
                console.log(redirect)
            },
            mixins: [globalMixin],
            methods: {
                submit: function () {
                    var self = this;

                    self.loading = true;
                    axios.post('/api/v1/account/login', { phoneNumber: this.phoneNumber, email: this.email, password: this.password }).then(function (response) {
                        self.failureAlertText = '';
                        self.successAlertText = 'Successfully logged in. Please wait...';

                        var urlSearchParams = new URLSearchParams(window.location.search);
                        var redirect = urlSearchParams.get('redirect');

                        if(!urlSearchParams.has('redirect') || redirect === '/account/login') {
                            window.location.replace('/account/info');
                        } else {
                            window.location.replace(redirect);
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