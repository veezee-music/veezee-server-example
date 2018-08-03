@php
    global $router;
    $searchUrl = '/shared/search';
@endphp
@extends('layouts.master')

@section('title', 'خانه')

@section('head')
    <style>
        .job-card article {
            cursor: inherit;
            padding: .8rem;
        }

        .job-card header .title, .job-card header .title {
            margin: 0;
            display: inline-block;
        }

        .job-card header .position-note {
            display: inline-block;
        }

        .job-card .skills .label {
            font-family: Shabnam, Tahoma, sans-serif !important;
            font-size: .8rem;
            display: inline-block;
            border: 1px solid transparent;
            border-radius: .4rem;
            background: #ededed;
            color: #7e7e7e;
            padding: .1rem .5rem;
            margin: .1rem;
        }

        .job-card header .location {
            margin-right: .8rem;
            font-size: .75rem;
            display: inline-block;
        }

        .job-card header .location em {
            font-style: normal;
        }

        .job-card.premium-card header .employer {
            display: flex;
            flex-direction: row;
            align-content: center;
            align-items: center;
            align-self: center;
        }

        .job-card.premium-card header .employer > div:nth-child(2) {
            margin-right: 1rem;
        }

        .job-card.premium-card header .employer > div:nth-child(2) .location, .job-card.premium-card header .employer .location.with-employer-logo {
            margin-right: 0;
        }

        .job-card header .employer h6 {
            font-weight: 600;
            display: inline-block;
        }

        .job-card.premium-card header .employer h6 {
            display: block;
        }

        .job-card header .employer img {
            border-radius: .4rem;
            box-shadow: 0 0.1rem 0.4rem #cbcbcb;
        }

        .job-card .mini-spacer {
            padding: .3rem 0;
        }

        .job-card .half-spacer {
            padding: .5rem 0;
        }

        .job-card .spacer {
            padding: 1rem 0;
        }

        .job-card .contract span {
            font-family: Shabnam, Tahoma, sans-serif !important;
            font-size: .9rem;
        }

        .job-card .contract .money {
            color: #27AE60;
        }

        .job-card .extra {
            border-radius: .4rem;
            background-color: #fafafa;
            overflow: hidden;
        }

        .job-card .extra .text {
            font-family: Shabnam, Tahoma, sans-serif !important;
            font-weight: 100;
            font-size: .9rem;
            padding: 1rem;
        }

        @media all and (max-width: 1091px) {
            .extra {
                margin-top: 1rem;
            }
        }

        .job-card {
            margin: 0 !important;
            padding: .5rem 2rem !important;
        }

        .form-input-c {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: .9rem;
        }

        .search-row {
            background: #ffffff;
            background: url("data:image/svg+xml,%3Csvg width='6' height='6' viewBox='0 0 6 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23f2f3f4' fill-opacity='1' fill-rule='evenodd'%3E%3Cpath d='M5 0h1L0 6V5zM6 5v1H5z'/%3E%3C/g%3E%3C/svg%3E");
            padding: 1rem;
            margin-left: -30px;
            margin-right: -30px;
        }

        .search-row label span {
            font-family: Shabnam, Tahoma, sans-serif !important;
            -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
            -khtml-user-select: none; /* Konqueror HTML */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
        }

        @media all and (max-width: 1091px) {
            .search-row .sel {
                margin: .5rem 0;
            }
        }

        .card-attention:hover {
            border: none !important;
            box-shadow: 0 1.2rem 3rem rgba(0,0,0,.15) !important;
        }

        @keyframes card-attention-red-flashing {
            0% {
                box-shadow: unset;
            }
            50% {
                box-shadow: 0 0 0.4rem #C0392B;
            }
            100% {
                box-shadow: unset;
            }
        }

        .card-attention-red {
            border-color: #C0392B !important;
            box-shadow: 0 0 0.4rem #C0392B !important;
        }

        .card-attention-red-flashing {
            animation: card-attention-red-flashing 1s infinite;
        }


        @keyframes card-attention-green-flashing {
            0% {
                box-shadow: unset;
            }
            50% {
                box-shadow: 0 0 0.4rem #27AE60;
            }
            100% {
                box-shadow: unset;
            }
        }

        .card-attention-green {
            border-color: #27AE60 !important;
            box-shadow: 0 0 0.4rem #27AE60 !important;
        }

        .card-attention-green-flashing {
            animation: card-attention-green-flashing 1s infinite;
        }


        @keyframes card-attention-blue-flashing {
            0% {
                box-shadow: unset;
            }
            50% {
                box-shadow: 0 0 0.4rem #5499C7;
            }
            100% {
                box-shadow: unset;
            }
        }

        .card-attention-blue {
            border-color: #5499C7 !important;
            box-shadow: 0 0 0.4rem #5499C7 !important;
        }

        .card-attention-blue-flashing {
            animation: card-attention-blue-flashing 1s infinite;
        }


        @keyframes card-attention-orange-flashing {
            0% {
                box-shadow: unset;
            }
            50% {
                box-shadow: 0 0 0.4rem #FF9800;
            }
            100% {
                box-shadow: unset;
            }
        }

        .card-attention-orange {
            border-color: #FF9800 !important;
            box-shadow: 0 0 0.4rem #FF9800 !important;
        }

        .card-attention-orange-flashing {
            animation: card-attention-orange-flashing 1s infinite;
        }
    </style>
    <style>
        .owl-carousel .owl-dots, .owl-carousel .owl-nav {
            margin-top: -1.5rem !important;
            position: relative;
        }

        .course-card article {
            margin: 0;
        }

        .course-card header .category-container {
            margin-bottom: .5rem;
        }

        .course-card header .category-container small {
            font-weight: 100;
        }

        .course-card header .title-container {
            align-items: center;
            align-self: center;
            align-content: center;
            display: flex;
            margin: .5rem 0 1rem 0;
        }

        .course-card header .title-container .title {
            display: inline-block;
            margin: 0;
        }

        .course-card header .title-container .label-container {
            display: inline-block;
            margin-right: .5rem;
        }

        .course-card header .title-container .label-container img {
            max-width: 1.7rem;
            transform: rotate(-45deg);
        }

        .course-card .description-container p {
            font-weight: 300;
            font-size: .9rem;
            margin: 0;
        }

        .course-card .divider {
            border-top: 1px solid #c6c6c6;
            margin: .5rem 0;
            opacity: .5;
        }

        .course-card .course-info-container {
            display: flex;
            align-items: center;
        }

        .course-card .course-info-container .producer, .course-card .course-info-container .misc {
            padding: 0;
        }

        .course-card .course-info-container .producer .box-container {
            align-self: center;
            align-content: center;
            align-items: center;
            text-align: left;
            display: flex;
            flex-direction: row;
            margin: 0 1rem;
        }

        .course-card .course-info-container .misc .box-container {
            align-self: center;
            align-content: center;
            align-items: center;
            font-size: .8rem;
            margin: 0 1rem 0 .5rem;
        }

        .course-card .course-info-container .misc .box-container img {
            max-width: 1.2rem;
            display: inline-block;
        }

        .course-card .course-info-container .misc .box-container .text {
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        .course-card .course-info-container .producer .box-container .logo {
            border-radius: .4rem;
            max-width: 4rem;
            max-height: 4rem;
        }

        .course-card .course-info-container .producer .box-container .name-container {
            margin: 0 .8rem;
        }

        .course-card .course-info-container .producer .box-container .name-container .location-container {
            font-size: .8rem;
            margin: .3rem 0;
        }

        .course-card .course-info-container .producer .box-container .name-container .name-container-inner {
            font-size: 0.85rem;
            text-align: right;
            display: flex;
            align-items: center;
            align-self: center;
            align-content: center;
        }

        .course-card .course-info-container .producer .box-container .name-container .name-container-inner .producer-title {
            font-family: Shabnam, Tahoma, sans-serif !important;
            font-weight: 600;
            font-size: 1rem
        }

        .course-card .course-info-container .producer .box-container .name-container .name-container-inner .producer-title-label-container {
            display: inline-block;
            margin-right: .3rem;
        }

        .course-card .course-info-container .producer .box-container .name-container .name-container-inner .verified {
            max-width: 1rem;
        }

        .course-card .course-info-container .producer .box-container small {
            font-weight: 100;
        }

        .course-card .footer {
            align-items: center;
        }

        .course-card .footer .cost-container, .course-card .footer .countdown-container {
            font-size: .9rem;
            font-weight: 200;
        }

        .course-card .footer .cost-container .money {
            color: #27AE60;
            font-weight: 300;
        }

        .course-card .footer .column {
            padding: 0;
            margin-top: .3rem;
        }

        .course-card .footer .column > div {
            display: flex;
            flex-direction: column;
        }

        .course-card .footer .price-container {
            padding: 0 1rem;
        }

        .course-card .footer .price-container .price {
            font-weight: 600;
            color: #DF8908;
        }

        .course-card .footer .price-container .price.discounted {
            text-decoration: line-through;
            display: block;
            font-size: .8rem;
            font-weight: 300;
            color: #C0392B;
        }

        .course-card .footer .price-container .price.not-discounted {
            margin: .58rem 0;
            display: block;
        }

        .course-card .footer .buy-button-container {
            padding: 0 1rem;
            text-align: left;
        }

        .course-card .footer .buy-button-container button {
            padding: .2rem 1rem;
            font-family: Shabnam, Tahoma, sans-serif !important;
            width: 80%;
        }

        .course-card .course-info-container .misc .box-container .column {
            margin: .2rem 0;
        }

        @media all and (max-width: 633px) {
            .course-card .course-info-container .misc .box-container > .row {
                margin-top: 1.5rem;
                text-align: center;
            }
        }
    </style>
@endsection

@section('body')

    <script type="text/x-template" id="countdown-timer">
        <div>
            {{--<li>--}}
                {{--<p class="digit">(( days | twoDigits ))</p>--}}
                {{--<p class="text">days</p>--}}
            {{--</li>--}}
            <div style="    display: inline-block;">
                <span class="digit">(( convertNumbersToPersian(seconds) ))</span>
                <span class="text">:</span>
            </div>
            <div style="    display: inline-block;">
                <span class="digit">(( convertNumbersToPersian(minutes) ))</span>
                <span class="text">:</span>
            </div>
            <div style="    display: inline-block;">
                <span class="digit">(( convertNumbersToPersian(hours) ))</span>
            </div>
        </div>
    </script>

    <div class="row" id="app">
        <div class="col-lg-12">

            <div class="row search-row">
                <div class="col-lg-3 sel">
                    <input :style="{'background': search.query != '' ? '#fff' : 'transparent'}" type="text" v-model="search.query" class="form-input-c" placeholder="عنوان شغل، مهارت یا ...">
                </div>
                <div class="col-lg-3 sel">
                    <i class="fa fa-map-marker v-select-icon"></i>
                    <v-select :style="{'background': search.state != '' ? '#fff' : 'transparent'}" class="single-select icon" :options="states" v-model="search.state" placeholder="همه استان ها">
                        <div slot="no-options">نتیجه ای پیدا نشد.</div>
                    </v-select>
                </div>
                <div class="col-lg-3 sel">
                    <i class="fa fa-bars v-select-icon"></i>
                    <v-select :style="{'background': search.category != '' ? '#fff' : 'transparent'}" class="single-select icon" :options="categories" v-model="search.category" placeholder="همه دسته ها">
                        <div slot="no-options">نتیجه ای پیدا نشد.</div>
                    </v-select>
                </div>
                <div class="col-lg-3 sel" style="align-self: center; text-align: center;">
                    <div class="c-checkbox">
                        <input class="check" v-model="search.salary" type="checkbox" id="salary" />
                        <label for="salary"><span>فقط آگهی های با حقوق</span></label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="line-on-heading">جدیدترین آگهی ها</h1>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">

                    <div>
                        <section class="owl-carousel owl-theme" dir="ltr">

                            <div style="margin-bottom: 3rem; margin-top: .2rem; direction: rtl; padding: 0 1rem;">
                                <div class="c-card course-card">
                                    <article class="row">
                                        <div class="col-lg-12 course-info">
                                            <header>
                                                <div class="category-container">
                                                    <small>
                                                        <span>صنعت نفت</span>
                                                    </small>
                                                </div>
                                                <div class="title-container">
                                                    <h5 class="title">مدیریت بنگاه اقتصادی</h5>
                                                </div>
                                            </header>
                                            {{--<section class="description-container">--}}
                                                {{--<p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد.</p>--}}
                                            {{--</section>--}}
                                            <div class="divider"></div>
                                            <section class="row course-info-container">
                                                <div class="col-lg-8 col-sm-8 producer">
                                                    <div class="box-container">
                                                        <div>
                                                            <img class="logo" src="/assets/img/alopek.jpg">
                                                        </div>
                                                        <div class="name-container">

                                                            <div class="name-container-inner">
                                                                <span class="producer-title">دانشگاه تهران</span>
                                                            </div>
                                                            <div class="location-container text-right">
                                                                <i class="fa-fw fa fa-map-marker"></i>
                                                                <span class="text">کرج، البرز</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-4 misc text-center">
                                                    <div class="box-container">
                                                        <div class="row">
                                                            {{--<div class="col-lg-12 col-md-12 col-sm-12 col-6 column">--}}
                                                                {{--<i class="fa-fw fa fa-map-marker"></i>--}}
                                                                {{--<span class="text">کرج، البرز</span>--}}
                                                            {{--</div>--}}
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-female"></i>
                                                                <span class="text">خانم</span>
                                                            </div>
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-paste"></i>
                                                                <span class="text">تمام وقت</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <div class="divider"></div>
                                            <section class="row footer">
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>حقوق:</h6>
                                                        <div class="cost-container">
                                                            <span><span class="money">۳,۵۰۰,۰۰۰</span> تا <span class="money">۵,۰۰۰,۰۰۰</span> تومان</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>انقضای پیشنهاد:</h6>
                                                        <div class="countdown-container">
                                                            <countdown-timer deadline="January 22, 2018"></countdown-timer>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </article>
                                </div>
                            </div>
                            <div style="margin-bottom: 3rem; margin-top: .2rem; direction: rtl; padding: 0 1rem;">
                                <div class="c-card course-card">
                                    <article class="row">
                                        <div class="col-lg-12 course-info">
                                            <header>
                                                <div class="category-container">
                                                    <small>
                                                        <span>صنعت نفت</span>
                                                    </small>
                                                </div>
                                                <div class="title-container">
                                                    <h5 class="title">مدیریت بنگاه اقتصادی</h5>
                                                </div>
                                            </header>
                                            {{--<section class="description-container">--}}
                                            {{--<p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد.</p>--}}
                                            {{--</section>--}}
                                            <div class="divider"></div>
                                            <section class="row course-info-container">
                                                <div class="col-lg-8 col-sm-8 producer">
                                                    <div class="box-container">
                                                        <div>
                                                            <img class="logo" src="/assets/img/alopek.jpg">
                                                        </div>
                                                        <div class="name-container">

                                                            <div class="name-container-inner">
                                                                <span class="producer-title">دانشگاه تهران</span>
                                                            </div>
                                                            <div class="location-container text-right">
                                                                <i class="fa-fw fa fa-map-marker"></i>
                                                                <span class="text">کرج، البرز</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-4 misc text-center">
                                                    <div class="box-container">
                                                        <div class="row">
                                                            {{--<div class="col-lg-12 col-md-12 col-sm-12 col-6 column">--}}
                                                            {{--<i class="fa-fw fa fa-map-marker"></i>--}}
                                                            {{--<span class="text">کرج، البرز</span>--}}
                                                            {{--</div>--}}
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-female"></i>
                                                                <span class="text">خانم</span>
                                                            </div>
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-paste"></i>
                                                                <span class="text">تمام وقت</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <div class="divider"></div>
                                            <section class="row footer">
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>حقوق:</h6>
                                                        <div class="cost-container">
                                                            <span><span class="money">۳,۵۰۰,۰۰۰</span> تا <span class="money">۵,۰۰۰,۰۰۰</span> تومان</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>انقضای پیشنهاد:</h6>
                                                        <div class="countdown-container">
                                                            <countdown-timer deadline="January 22, 2018"></countdown-timer>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </article>
                                </div>
                            </div>
                            <div style="margin-bottom: 3rem; margin-top: .2rem; direction: rtl; padding: 0 1rem;">
                                <div class="c-card course-card">
                                    <article class="row">
                                        <div class="col-lg-12 course-info">
                                            <header>
                                                <div class="category-container">
                                                    <small>
                                                        <span>صنعت نفت</span>
                                                    </small>
                                                </div>
                                                <div class="title-container">
                                                    <h5 class="title">مدیریت بنگاه اقتصادی</h5>
                                                </div>
                                            </header>
                                            {{--<section class="description-container">--}}
                                            {{--<p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد.</p>--}}
                                            {{--</section>--}}
                                            <div class="divider"></div>
                                            <section class="row course-info-container">
                                                <div class="col-lg-8 col-sm-8 producer">
                                                    <div class="box-container">
                                                        <div>
                                                            <img class="logo" src="/assets/img/alopek.jpg">
                                                        </div>
                                                        <div class="name-container">

                                                            <div class="name-container-inner">
                                                                <span class="producer-title">دانشگاه تهران</span>
                                                            </div>
                                                            <div class="location-container text-right">
                                                                <i class="fa-fw fa fa-map-marker"></i>
                                                                <span class="text">کرج، البرز</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-4 misc text-center">
                                                    <div class="box-container">
                                                        <div class="row">
                                                            {{--<div class="col-lg-12 col-md-12 col-sm-12 col-6 column">--}}
                                                            {{--<i class="fa-fw fa fa-map-marker"></i>--}}
                                                            {{--<span class="text">کرج، البرز</span>--}}
                                                            {{--</div>--}}
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-female"></i>
                                                                <span class="text">خانم</span>
                                                            </div>
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-paste"></i>
                                                                <span class="text">تمام وقت</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <div class="divider"></div>
                                            <section class="row footer">
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>حقوق:</h6>
                                                        <div class="cost-container">
                                                            <span><span class="money">۳,۵۰۰,۰۰۰</span> تا <span class="money">۵,۰۰۰,۰۰۰</span> تومان</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>انقضای پیشنهاد:</h6>
                                                        <div class="countdown-container">
                                                            <countdown-timer deadline="January 22, 2018"></countdown-timer>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </article>
                                </div>
                            </div>
                            <div style="margin-bottom: 3rem; margin-top: .2rem; direction: rtl; padding: 0 1rem;">
                                <div class="c-card course-card">
                                    <article class="row">
                                        <div class="col-lg-12 course-info">
                                            <header>
                                                <div class="category-container">
                                                    <small>
                                                        <span>صنعت نفت</span>
                                                    </small>
                                                </div>
                                                <div class="title-container">
                                                    <h5 class="title">مدیریت بنگاه اقتصادی</h5>
                                                </div>
                                            </header>
                                            {{--<section class="description-container">--}}
                                            {{--<p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد.</p>--}}
                                            {{--</section>--}}
                                            <div class="divider"></div>
                                            <section class="row course-info-container">
                                                <div class="col-lg-8 col-sm-8 producer">
                                                    <div class="box-container">
                                                        <div>
                                                            <img class="logo" src="/assets/img/alopek.jpg">
                                                        </div>
                                                        <div class="name-container">

                                                            <div class="name-container-inner">
                                                                <span class="producer-title">دانشگاه تهران</span>
                                                            </div>
                                                            <div class="location-container text-right">
                                                                <i class="fa-fw fa fa-map-marker"></i>
                                                                <span class="text">کرج، البرز</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-4 misc text-center">
                                                    <div class="box-container">
                                                        <div class="row">
                                                            {{--<div class="col-lg-12 col-md-12 col-sm-12 col-6 column">--}}
                                                            {{--<i class="fa-fw fa fa-map-marker"></i>--}}
                                                            {{--<span class="text">کرج، البرز</span>--}}
                                                            {{--</div>--}}
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-female"></i>
                                                                <span class="text">خانم</span>
                                                            </div>
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-paste"></i>
                                                                <span class="text">تمام وقت</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <div class="divider"></div>
                                            <section class="row footer">
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>حقوق:</h6>
                                                        <div class="cost-container">
                                                            <span><span class="money">۳,۵۰۰,۰۰۰</span> تا <span class="money">۵,۰۰۰,۰۰۰</span> تومان</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>انقضای پیشنهاد:</h6>
                                                        <div class="countdown-container">
                                                            <countdown-timer deadline="January 22, 2018"></countdown-timer>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </article>
                                </div>
                            </div>
                            <div style="margin-bottom: 3rem; margin-top: .2rem; direction: rtl; padding: 0 1rem;">
                                <div class="c-card course-card">
                                    <article class="row">
                                        <div class="col-lg-12 course-info">
                                            <header>
                                                <div class="category-container">
                                                    <small>
                                                        <span>صنعت نفت</span>
                                                    </small>
                                                </div>
                                                <div class="title-container">
                                                    <h5 class="title">مدیریت بنگاه اقتصادی</h5>
                                                </div>
                                            </header>
                                            {{--<section class="description-container">--}}
                                            {{--<p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد.</p>--}}
                                            {{--</section>--}}
                                            <div class="divider"></div>
                                            <section class="row course-info-container">
                                                <div class="col-lg-8 col-sm-8 producer">
                                                    <div class="box-container">
                                                        <div>
                                                            <img class="logo" src="/assets/img/alopek.jpg">
                                                        </div>
                                                        <div class="name-container">

                                                            <div class="name-container-inner">
                                                                <span class="producer-title">دانشگاه تهران</span>
                                                            </div>
                                                            <div class="location-container text-right">
                                                                <i class="fa-fw fa fa-map-marker"></i>
                                                                <span class="text">کرج، البرز</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-4 misc text-center">
                                                    <div class="box-container">
                                                        <div class="row">
                                                            {{--<div class="col-lg-12 col-md-12 col-sm-12 col-6 column">--}}
                                                            {{--<i class="fa-fw fa fa-map-marker"></i>--}}
                                                            {{--<span class="text">کرج، البرز</span>--}}
                                                            {{--</div>--}}
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-female"></i>
                                                                <span class="text">خانم</span>
                                                            </div>
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-6 column">
                                                                <i class="fa-fw fa fa-paste"></i>
                                                                <span class="text">تمام وقت</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <div class="divider"></div>
                                            <section class="row footer">
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>حقوق:</h6>
                                                        <div class="cost-container">
                                                            <span><span class="money">۳,۵۰۰,۰۰۰</span> تا <span class="money">۵,۰۰۰,۰۰۰</span> تومان</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-6 column text-center">
                                                    <div>
                                                        <h6>انقضای پیشنهاد:</h6>
                                                        <div class="countdown-container">
                                                            <countdown-timer deadline="January 22, 2018"></countdown-timer>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </article>
                                </div>
                            </div>

                        </section>
                    </div>

                </div>
            </div>

            <div class="row grid">
                <div class="col-lg-3 grid-sizer"></div>

                <job-card v-for="item in list" :key="item._id.$oid" :item="item"></job-card>

                {{--<div class="col-lg-3 col-md-6 c-card job-card">--}}
                    {{--<article class="row card-attention card-attention-orange card-attention-orange-animated">--}}
                        {{--<div class="col-lg-12 job-info">--}}
                            {{--<header>--}}
                                {{--<div>--}}
                                    {{--<h5 class="title">توسعه دهنده وب</h5>--}}
                                    {{--<small class="position-note">(کارآموز)</small>--}}
                                {{--</div>--}}
                                {{--<div class="mini-spacer"></div>--}}
                                {{--<div class="employer">--}}
                                    {{--<h6>ایرانسل</h6>--}}
                                    {{--<div class="location">--}}
                                        {{--<i class="fa fa-map-marker"></i>--}}
                                        {{--<em>مشهد</em>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</header>--}}
                            {{--<div class="half-spacer"></div>--}}
                            {{--<section class="contract">--}}
                                {{--<div>--}}
                                    {{--<i class="fa fa-paste"></i>--}}
                                    {{--<span>تمام وقت</span>--}}
                                {{--</div>--}}
                                {{--<div>--}}
                                    {{--<i class="fa fa-money"></i>--}}
                                    {{--<span>حقوق از <span class="money">۳,۵۰۰,۰۰۰</span> تا <span class="money">۵,۰۰۰,۰۰۰</span> تومان</span>--}}
                                {{--</div>--}}
                            {{--</section>--}}
                            {{--<div class="half-spacer"></div>--}}
                            {{--<section class="skills">--}}
                                {{--<div class="label">لاراول</div>--}}
                                {{--<div class="label">پایگاه داده</div>--}}
                                {{--<div class="label">OPCAHE</div>--}}
                                {{--<div class="label">API</div>--}}
                            {{--</section>--}}
                        {{--</div>--}}
                        {{--<div class="extra">--}}

                        {{--</div>--}}
                    {{--</article>--}}
                {{--</div>--}}
                {{--<div class="col-lg-9 col-md-6 c-card job-card active-card">--}}
                    {{--<article class="row card-attention card-attention-green card-attention-green-animated">--}}
                        {{--<div class="col-lg-6 job-info">--}}
                            {{--<header>--}}
                                {{--<div>--}}
                                    {{--<h5 class="title">توسعه دهنده وب</h5>--}}
                                    {{--<small class="position-note">(کارآموز)</small>--}}
                                {{--</div>--}}
                                {{--<div class="mini-spacer"></div>--}}
                                {{--<div>--}}
                                    {{--<div class="employer">--}}
                                        {{--<div>--}}
                                            {{--<img width="50" src="https://jobinja.ir/files/uploads/images/a914db18-fe4e-11e6-8247-06a874001fea_b75d2c3a-84ec-40b9-8b6c-ca3bbababffe/companies_logo_128x128.jpg">--}}
                                        {{--</div>--}}
                                        {{--<div>--}}
                                            {{--<h6>ایرانسل</h6>--}}
                                            {{--<div class="location">--}}
                                                {{--<i class="fa fa-map-marker"></i>--}}
                                                {{--<em>مشهد</em>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</header>--}}
                            {{--<div class="half-spacer"></div>--}}
                            {{--<section class="contract">--}}
                                {{--<div>--}}
                                    {{--<i class="fa fa-paste"></i>--}}
                                    {{--<span>تمام وقت</span>--}}
                                {{--</div>--}}
                                {{--<div>--}}
                                    {{--<i class="fa fa-money"></i>--}}
                                    {{--<span>حقوق از <span class="money">۳,۵۰۰,۰۰۰</span> تا <span class="money">۵,۰۰۰,۰۰۰</span> تومان</span>--}}
                                {{--</div>--}}
                            {{--</section>--}}
                            {{--<div class="half-spacer"></div>--}}
                            {{--<section class="skills">--}}
                                {{--<div class="label">لاراول</div>--}}
                                {{--<div class="label">پایگاه داده</div>--}}
                                {{--<div class="label">OPCAHE</div>--}}
                                {{--<div class="label">API</div>--}}
                            {{--</section>--}}
                        {{--</div>--}}
                        {{--<div class="col-lg-6 extra">--}}
                            {{--<p class="text">الوپیک بزرگترین سامانه خدمات حمل و نقل آنلاین است که بسته ها و مرسولات را با قیمتی منطقی تر و سرعتی بیشتر به دست مشتریان می رساند. تمام مشتریان می توانند به راحتی به صورت آنلاین مسیر راننده پیک خود را دنبال کنند و از دریافت بسته خود مطمئن باشند و در کنار آن در هزینه و زمان خود صرفه جویی کنند.</p>--}}
                        {{--</div>--}}
                    {{--</article>--}}
                {{--</div>--}}
            </div>

            <br>

            <div class="row">
                <div class="col-lg-12">
                    <button style="font-family: Shabnam, Tahoma, sans-serif !important; cursor: pointer;" @click="loadAnnouncementsList()" class="btn btn-block btn-primary">بارگذاری آگهی های بیشتر</button>
                </div>
            </div>

            <br>

        </div>
    </div>

@endsection

@section('scripts')
    <script>
        var countdownTimer = {
            template: '#countdown-timer',
            delimiters: ["((","))"],
            props: ['deadline', 'stop'],
            name: 'countdown-timer',
            data: function() {
                return {
                    interval: null,
                    now: Math.trunc((new Date()).getTime() / 1000),
                    date: null,
                    diff: 0
                }
            },
            mounted: function() {
                var self = this;

                this.date = Math.trunc(Date.parse(this.deadline.replace(/-/g, "/")) / 1000);
                this.interval = setInterval(function () {
                    self.now = Math.trunc((new Date()).getTime() / 1000)
                }, 1000);
            },
            mixins: [globalMixin],
            methods: {
                convertToTwoDigits: function (value) {
                    if ( value.toString().length <= 1 ) {
                        return '0' + value.toString();
                    }
                    return value.toString();
                }
            },
            computed: {
                seconds: function() {
                    return Math.trunc(this.diff) % 60
                },
                minutes: function() {
                    return Math.trunc(this.diff / 60) % 60
                },
                hours: function() {
                    return Math.trunc(this.diff / 60 / 60) % 24
                },
                days: function() {
                    return Math.trunc(this.diff / 60 / 60 / 24)
                }
            },
            watch: {
                now: function(value) {
                    this.diff = this.date - this.now;
                    if(this.diff <= 0 || this.stop) {
                        this.diff = 0;
                        // Remove interval
                        clearInterval(this.interval);
                    }
                }
            }
        };

        var app = new Vue({
            el: '#app',
            delimiters: ["((","))"],
            components:{
                vSelect: VueSelect.VueSelect,
                jobCard: jobCard,
                countdownTimer: countdownTimer,
            },
            data: {
                autoLoad: true,
                loading: false,
                query: '',
                page: 1,

                states: [
                    'تهران',
                    'البرز',
                    'آذربایجان شرقی',
                    'آذربایجان غربی',
                    'اردبیل',
                    'اصفهان',
                    'ایلام',
                    'بوشهر',
                    'چهارمحال و بختیاری',
                    'خراسان جنوبی',
                    'خراسان رضوی',
                    'خراسان شمالی',
                    'خوزستان',
                    'زنجان',
                    'سمنان',
                    'سیستان و بلوچستان',
                    'فارس',
                    'قزوین',
                    'قم',
                    'کردستان',
                    'کرمان',
                    'کرمانشاه',
                    'کهگیلویه و بویراحمد',
                    'گلستان',
                    'گیلان',
                    'لرستان',
                    'مازندران',
                    'مرکزی',
                    'هرمزگان',
                    'همدان',
                    'یزد'
                ],
                categories: [
                    'نرم افزار',
                    'پزشکی'
                ],

                list: [],

                search: {
                    query: '',
                    state: '',
                    category: '',
                    salary: true
                }
            },
            mixins: [globalMixin],
            created: function () {
            },
            mounted: function () {
                var self = this;

                var owl = $('.owl-carousel').owlCarousel({
                    items: 3,
                    rewind: true,
                    autoplay: false,
                    autoplayTimeout: 4000,
                    autoplayHoverPause: true,
                    loop: false,
                    responsiveClass:true,
                    navText: ["<i style='vertical-align: middle;' class='fa fa-chevron-left'></i>", "<i style='vertical-align: middle;' class='fa fa-chevron-right'></i>"],
                    responsive:{
                        0:{
                            items:1,
                            nav: true,
                            dots: false,
                        },
                        800:{
                            items:2,
                            nav: false,
                            dots: true,
                        },
                        1000:{
                            items:3,
                            nav: false,
                            loop:false
                        }
                    }
                });
                var currentPage = 0;
                var clicked = false;
                owl.on('changed.owl.carousel', function(event) {
                    var pagesCount = event.page.count;
                    var itemsCount = event.item.count;
                    var itemsPerPage = event.page.size;
                    var dots = $('.owl-carousel .owl-dots');

                    currentPage += 1;
                    if(currentPage > (pagesCount - 1)) {
                        currentPage = 0;
                    }

                    if(clicked) {
                        clicked = false;
                        currentPage = event.page.index;
                    }
                    dots.find('.owl-dot').removeClass('active');
                    $(dots.find('.owl-dot').get(currentPage)).addClass('active');
                });
                $('.owl-carousel .owl-dot').click(function () {
                    clicked = true;
                });

                this.loadAnnouncementsList();
            },
            methods: {
                loadAnnouncementsList: function () {
                    var self = this;

                    if(self.loading) {
                        return;
                    }

                    console.log('load requested')

                    self.loading = true;
                    axios.get('/api/v1/jobs/list').then(function (response) {
                        self.page++;
                        self.list = self.list.concat(response.data);

                        setTimeout(function () {
                            $('.job-card').find('.title').responsiveHeadlines({
                                maxFontSize: 19.2,
                                minFontSize: 5
                            });

                            var msnry = new Masonry('.grid', {
                                // options...
                                itemSelector: '.job-card',
                                columnWidth: '.grid-sizer',
                                percentPosition: true,
//                                transitionDuration: '0.8s',
                                fitWidth: false,
                                resize: true,
                                gutter: 0
                            });
                        }, 500);
                    }).catch(function (error) {
                    }).then(function () {
                        self.loading = false;
                        console.log('request finished')
                    });
                },
                buildGetContentListUrl: function () {
                    return '/api/v1/jobs/list/' + this.page;
                }
            },
            watch: {

            }
        });


//        $grid.imagesLoaded().progress( function() {
//            $grid.masonry();
//        });
//        var maxHeight = -1;
//        $('.review-block .review-sum').each(function() {
//            if ($(this).height() > maxHeight) {
//                maxHeight = $(this).height();
//            }
//        }).height(maxHeight);
    </script>
@endsection