
@extends('layouts.master')

@section('title', 'Create a new account')

@section('head')
    <style>
        @media all and (max-width: 633px) {
            .row .col-sm-6 {
                margin-bottom: .5rem;
            }
        }

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
                <h4 class="text-center">Create a new account</h4>
                <br>

                <div style="display: {{ isset($message) && $message != null ? 'block' : 'none' }}" class="alert alert-info" role="alert" id="login-top-alert">{{ $message }}</div>

                <br>
                <section>
                    <div class="form-group iconed row">
                        <div class="col-sm-12">
                            <i class="fa fa-user"></i>
                            <input @keyup.enter="submit" v-model="name" type="text" required class="form-control" placeholder="Name">
                        </div>
                    </div>
                    {{--<div class="form-group iconed row" v-if="!registerWithEmail">--}}
                        {{--<div class="col-sm-12">--}}
                            {{--<i class="fa fa-phone"></i>--}}
                            {{--<input @keyup.enter="submit" v-model="phoneNumber" type="text" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required class="form-control text-left" dir="ltr" placeholder="Phone number">--}}
                            {{--<small class="hint" @click="registerWithEmail = !registerWithEmail"><i class="fa fa-envelope hint-icon"></i><span>Register with email</span></small>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group iconed row">
                        <div class="col-sm-12">
                            <i class="fa fa-envelope"></i>
                            <input @keyup.enter="submit" v-model="email" type="email" required class="form-control text-left" dir="ltr" placeholder="Email address">
                            {{--<small class="hint" @click="registerWithEmail = !registerWithEmail"><i class="fa fa-phone hint-icon"></i><span>Register with phone number</span></small>--}}
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
                            <button @click="submit" type="button" class="btn btn-block btn-primary">Submit</button>
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
                        <a href="/account/login" class="btn btn-block btn-info">Already registered? Log in</a>
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
                registerWithEmail: true,

                successAlertText: '',
                failureAlertText: '',

                name: '',
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

                    if(this.password === '' || this.password.length < 6) {
                        this.failureAlertText = 'Password must be at least 6 characters.';
                        return;
                    }
                    if(this.name === '') {
                        this.failureAlertText = 'Please enter your name.';
                        return;
                    }
                    if(this.registerWithEmail && (this.email === '' || this.email === null)) {
                        this.failureAlertText = 'Enter a valid email.';
                        return;
                    } else if(!this.registerWithEmail && (this.phoneNumber === '' || this.phoneNumber === null || this.phoneNumber.length < 11)) {
                        this.failureAlertText = 'Enter a valid phone number.';
                        return;
                    }

                    self.loading = true;
                    axios.post('/api/v1/account/register', { name: this.name, phoneNumber: this.phoneNumber, email: this.email, password: this.password }).then(function (response) {
                        self.failureAlertText = '';
                        self.successAlertText = 'New account was created. Please wait...';
                        setTimeout(function () {
                            window.location.replace('/account/info');
                        }, 1500);
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
                registerWithEmail: function (val) {
                    if(val) {
                        this.phoneNumber = '';
                    } else {
                        this.email = '';
                    }
                }
            }
        });
    </script>
@endsection