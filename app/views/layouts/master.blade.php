@php global $router @endphp
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>@yield('title') - {{ SITE_TITLE }}</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
          name='viewport'/>
    <meta name="description" content="جابسیتی به شما کمک میکنه تا بهترین موقعیت های شغلی و کارآموزی مناسب دانشجویی رو پیدا کنید.">
    <meta name="keywords"
          content="job30t,jobs,internship,apprenticeship,programs,شغل,جابسیتی,پیدا کردن شغل,university,شغل دانشجویی,student jobs">
    <meta name="language" content="Persian">

    <meta name="fontiran.com:license" content="BSXBV">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=PY4raRoJKz">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=PY4raRoJKz">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=PY4raRoJKz">
    <link rel="manifest" href="/manifest.json?v=PY4raRoJKz">
    <link rel="mask-icon" href="/safari-pinned-tab.svg?v=PY4raRoJKz" color="#ff9800">
    <link rel="shortcut icon" href="/favicon.ico?v=PY4raRoJKz">
    <meta name="theme-color" content="#ffffff">
    <meta name="samandehi" content="313785600"/>

    <!-- Open Graph Tags -->
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="جابسیتی به شما کمک میکنه تا بهترین موقعیت های شغلی و کارآموزی مناسب دانشجویی رو پیدا کنید."/>
    <meta property="og:site_name" content="JOB30t - بهترین موقعیت های شغلی و کارآموزی دانشجویی"/>
    <meta property="og:locale" content="fa_IR"/>

    <link href="/assets/css/bundle.bootstrap.owl.carousel.carousel-theme-default.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/site.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/noty.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/css/croppie.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/croppie.min.css" rel="stylesheet" type="text/css" />
    {{--<link href="/assets/css/jquery-bar-rating/themes/bars-horizontal.css" rel="stylesheet" type="text/css">--}}
    {{--<link href="/assets/css/jquery-bar-rating/themes/rate-1.css" rel="stylesheet" type="text/css">--}}
    <script>
        // window.paceOptions = {
        //     ajax: {
        //         trackWebSockets: false,
        //         restartOnRequestAfter: true,
        //         trackMethods: ['GET', 'POST', 'PUT', 'DELETE', 'REMOVE']
        //     }
        // };
    </script>
    <script src="/assets/js/site.js"></script>
    @if(ENVIRONMENT == 'dev')
        <script src="/assets/js/vue.js"></script>
    @else
        <script src="/assets/js/vue.min.js"></script>
        <script>
            window['_fs_debug'] = false;
            window['_fs_host'] = 'fullstory.com';
            window['_fs_org'] = '9730N';
            window['_fs_namespace'] = 'FS';
            (function (m, n, e, t, l, o, g, y) {
                if (e in m) {
                    if (m.console && m.console.log) {
                        m.console.log('FullStory namespace conflict. Please set window["_fs_namespace"].');
                    }
                    return;
                }
                g = m[e] = function (a, b) {
                    g.q ? g.q.push([a, b]) : g._api(a, b);
                };
                g.q = [];
                o = n.createElement(t);
                o.async = 1;
                o.src = 'https://' + _fs_host + '/s/fs.js';
                y = n.getElementsByTagName(t)[0];
                y.parentNode.insertBefore(o, y);
                g.identify = function (i, v) {
                    g(l, {uid: i});
                    if (v) g(l, v)
                };
                g.setUserVars = function (v) {
                    g(l, v)
                };
                g.identifyAccount = function (i, v) {
                    o = 'account';
                    v = v || {};
                    v.acctId = i;
                    g(o, v)
                };
                g.clearUserCookie = function (c, d, i) {
                    if (!c || document.cookie.match('fs_uid=[`;`]*`[`;`]*`[`;`]*`')) {
                        d = n.domain;
                        while (1) {
                            n.cookie = 'fs_uid=;domain=' + d +
                                ';path=/;expires=' + new Date(0).toUTCString();
                            i = d.indexOf('.');
                            if (i < 0) break;
                            d = d.slice(i + 1)
                        }
                    }
                };
            })(window, document, window['_fs_namespace'], 'script', 'user');
        </script>
    @endif
    <script>
        var globalMixin = {
            methods: {
                getSiteAddress: function () {
                    return '{{ SITE_ADDRESS }}';
                },
                clone: function (obj) {
                    if (obj === null || typeof(obj) !== 'object' || 'isActiveClone' in obj)
                        return obj;

                    if (obj instanceof Date)
                        var temp = new obj.constructor(); //or new Date(obj);
                    else
                        var temp = obj.constructor();

                    for (var key in obj) {
                        if (Object.prototype.hasOwnProperty.call(obj, key)) {
                            obj['isActiveClone'] = null;
                            temp[key] = this.clone(obj[key]);
                            delete obj['isActiveClone'];
                        }
                    }

                    return temp;
                },
                convertNumbersToPersian: function (str) {
                    return str.toString().replace(/0/g, '۰').replace(/1/g, '۱').replace(/2/g, '۲').replace(/3/g, '۳').replace(/4/g, '۴').replace(/5/g, '۵').replace(/6/g, '۶').replace(/7/g, '۷').replace(/8/g, '۸').replace(/9/g, '۹');
                },
                range: function (min, max) {
                    var array = [],
                        j = 0;
                    for (var i = min; i <= max; i++) {
                        array[j] = i;
                        j++;
                    }
                    return array;
                },
                rangeReverse: function (min, max) {
                    var array = [],
                        j = 0;
                    for (var i = max; i >= min; i--) {
                        array[j] = i;
                        j++;
                    }
                    return array;
                },
                removeSpaces: function (str) {
                    return str.replace(/\s+/g, '');
                },
                getNestedObject: function (pathUsingDotsOrIndexes, parentObject) {
                    return pathUsingDotsOrIndexes.split('.').reduce(function (prev, curr) {
                        return prev ? prev[curr] : null
                    }, parentObject || self);
                },
                setNestedObject: function (pathUsingDotsOrIndexes, newValue, parentObject) {
                    var stack = pathUsingDotsOrIndexes.split('.');

                    while (stack.length > 1) {
                        parentObject = parentObject[stack.shift()];
                    }
                    parentObject[stack.shift()] = newValue;
                },
                getPersianMonths: function () {
                    return [
                        'فروردین',
                        'اردیبهشت',
                        'خرداد',
                        'تیر',
                        'مرداد',
                        'شهریور',
                        'مهر',
                        'آبان',
                        'آذر',
                        'دی',
                        'بهمن',
                        'اسفند'
                    ];
                },
                stripTags: function (input, allowed) {

                    // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
                    allowed = (((allowed || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

                    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
                    // don't mess with next line, Intelij is confused, the statement is valid
                    var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

                    var before = input;
                    var after = input;
                    // recursively remove tags to ensure that the returned string doesn't contain forbidden tags after previous passes (e.g. '<<bait/>switch/>')
                    while (true) {
                        before = after;
                        after = before.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
                            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
                        });

                        // return once no more tags are removed
                        if (before === after) {
                            return after
                        }
                    }
                },
                toRial: function (str) {
                    str = str.toString();
                    str = str.replace(/\,/g, '');
                    var objRegex = new RegExp('(-?[0-9]+)([0-9]{3})');

                    while (objRegex.test(str)) {
                        str = str.replace(objRegex, '$1,$2');
                    }

                    return str;
                },
                merge: function(a, b) {
                    var c = {};
                    for(var idx in a) {
                        c[idx] = a[idx];
                    }
                    for(var idx in b) {
                        c[idx] = b[idx];
                    }
                    return c;
                }
            }
        };
        var validatorFuncs = {
            methods: {
                min: function (data, limit) {
                    if(data !== null && data !== undefined)
                        return data.length < limit;
                },
                max: function (data, limit) {
                    if(data !== null && data !== undefined)
                        return data.length > limit;
                },
                require: function (data) {
                    return this.checkRequire(data)
                },
                checkRequire: function (data) {
                    if (typeof data === "number")
                        data = String(data);
                    return data === null || data === '' || typeof data === 'undefined';
                },
                equal: function (data, limit) {
                    if(data !== null && data !== undefined)
                        return (data.length !== parseInt(limit));
                },
                digit: function (data) {
                    return typeof data !== "number";
                },
                email: function (data) {
                    return this.checkEmail(data);
                },
                checkEmail: function (data) {
                    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                    return !data.match(mailformat);
                },
                'no-repeat': function (data, item) {
                    return this.checkDuplicate(data, item);
                },
                checkDuplicate: function (data, item) {
                    if (Array.isArray(data)) {
                        if (typeof item !== 'undefined') {
                            if (data.indexOf(item) > -1) {
                                return true;
                            }
                        } else {
                            for (var i = 0; i < data.length; i++) {
                                for (var j = i; j < data.length; j++) {
                                    if (i !== j && data[i] === data[j])
                                        return true;
                                }
                            }
                        }
                    }
                    return false;
                },
                length: function (data, limit) {
                    if (Array.isArray(data)) {
                        return (data.length >= limit);
                    }
                }
            }
        }

    </script>
    <script src="/assets/js/sortable.js"></script>
    <script src="/assets/js/vuedraggable.min.js"></script>
    <script src="/assets/js/vue-clickaway.min.js"></script>
    <script src="/assets/js/vue-select.min.js"></script>

    <style type="text/css">
        @keyframes lds-ripple {
            0% {
                top: 96px;
                left: 96px;
                width: 0;
                height: 0;
                opacity: 1;
            }
            100% {
                top: 18px;
                left: 18px;
                width: 156px;
                height: 156px;
                opacity: 0;
            }
        }

        @-webkit-keyframes lds-ripple {
            0% {
                top: 96px;
                left: 96px;
                width: 0;
                height: 0;
                opacity: 1;
            }
            100% {
                top: 18px;
                left: 18px;
                width: 156px;
                height: 156px;
                opacity: 0;
            }
        }

        .lds-ripple {
            position: relative;
        }

        .lds-ripple div {
            box-sizing: content-box;
            position: absolute;
            border-width: 4px;
            border-style: solid;
            opacity: 1;
            border-radius: 50%;
            -webkit-animation: lds-ripple 1.7s cubic-bezier(0, 0.2, 0.8, 1) infinite;
            animation: lds-ripple 1.7s cubic-bezier(0, 0.2, 0.8, 1) infinite;
        }

        .lds-ripple div:nth-child(1) {
            border-color: #1d3f72;
        }

        .lds-ripple div:nth-child(2) {
            border-color: #5699d2;
            -webkit-animation-delay: -0.85s;
            animation-delay: -0.85s;
        }

        .lds-ripple {
            width: 96px !important;
            height: 96px !important;
            -webkit-transform: translate(-48px, -48px) scale(0.48) translate(48px, 48px);
            transform: translate(-48px, -48px) scale(0.48) translate(48px, 48px);
        }

        .lds-text {
            display: flex;
            width: 100%;
            align-self: flex-end;
            justify-content: center;
            justify-items: center;
            justify-self: center;
            margin-top: .5rem;
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        .lds-container {
            padding: 1rem 1.5rem;
            width: 80%;
            background-color: white;
            margin: 0 auto;
            border-radius: .4rem;
            opacity: .8;
        }
    </style>

    <style>
        /*input styles*/
        input, select, textarea {
            border-radius: .4rem !important;
            background-color: transparent;
            transition: all 0.2s ease-in-out !important;
            border: 1px solid #d4dadf;
            box-shadow: 0 0 0.4rem rgba(0, 0, 0, .12);
            outline: none;
        }

        input.active-input {
            background-color: #ffffff !important;
        }

        input:focus, textarea:focus {
            outline: none;
            box-shadow: 0 1.2rem 3rem rgba(0, 0, 0, .15);
            background-color: #fff !important;
            border: none;
            font-size: .9rem;
        }

        textarea:not(.original-placeholder)::-webkit-input-placeholder, input:not(.original-placeholder)::-webkit-input-placeholder, textarea:not(.original-placeholder):placeholder-shown, input:not(.original-placeholder):placeholder-shown {
            font-family: Shabnam, Tahoma, sans-serif;
            font-size: .9rem;
            color: #b6b6b6 !important;
        }

        textarea:-ms-input-placeholder, input:-ms-input-placeholder {
            font-family: Shabnam, Tahoma, sans-serif;
        }

        textarea::-moz-placeholder, input::-moz-placeholder {
            font-family: Shabnam, Tahoma, sans-serif;
        }

        select:not([size]):not([multiple]) {
            height: calc(2.25rem + 2px);
        }

        label {
            outline: none;
        }

        button, .btn, select, input, .dropdown {
            border-radius: .4rem;
            border: 1px solid #d4dadf;
            /*border: .1rem solid rgba(0, 0, 0, .06);*/
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, .05);
            outline: none;
        }

        .btn {
            line-height: unset !important;
        }

        input::-webkit-input-placeholder {
            /* WebKit browsers */
            text-align: left;
            direction: ltr;
        }

        input:-moz-placeholder {
            /* Mozilla Firefox 4 to 18 */
            text-align: left;
            direction: ltr;
        }

        input:placeholder-shown {
            text-align: left;
            direction: ltr;
        }

        input::-moz-placeholder {
            /* Mozilla Firefox 19+ but I'm not sure about it working */
            text-align: left;
            direction: ltr;
        }

        input:-ms-input-placeholder {
            /* Internet Explorer 10 */
            text-align: left;
            direction: ltr;
        }

        input::placeholder {
            text-align: left;
            direction: ltr;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button,
        input[type=password]::-webkit-inner-spin-button,
        input[type=password]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /*.input-filter-checkbox-c {*/
        /*display: -webkit-box;*/
        /*display: -ms-flexbox;*/
        /*display: flex;*/
        /*-webkit-box-align: center;*/
        /*-ms-flex-align: center;*/
        /*align-items: center;*/
        /*margin-bottom: 16px;*/
        /*margin-bottom: 1rem;*/
        /*border: 1px solid #e2e2e2;*/
        /*border-radius: .7rem;*/
        /*-webkit-transition: background-color .3s ease;*/
        /*-o-transition: background-color .3s ease;*/
        /*transition: background-color .3s ease;*/
        /*position: relative;*/
        /*}*/

        /*.input-filter-checkbox-c:hover {*/
        /*background-color: #eee;*/
        /*border-color: #cbcbcb;*/
        /*}*/

        /*.custom-control-indicator {*/
        /*left: inherit !important;*/
        /*right: 0;*/
        /*}*/

        /*.v-select-icon {*/
        /*color: #b6b6b6 !important;*/
        /*position: absolute;*/
        /*display: inline;*/
        /*vertical-align: middle;*/
        /*top: 30%;*/
        /*z-index: 2;*/
        /*right: 1.5rem;*/
        /*}*/

        /*.v-select .dropdown-toggle {*/
        /*display: inline-flex;*/
        /*border: none;*/
        /*overflow-x: hidden;*/
        /*width: 100%;*/
        /*}*/

        /*.v-select .dropdown-toggle > input {*/
        /*box-shadow: inherit !important;*/
        /*background-color: inherit !important;*/
        /*font-size: .9rem;*/
        /*}*/

        /*.v-select.single-select .selected-tag {*/
        /*background-color: transparent;*/
        /*border: none;*/
        /*width: 100%;*/
        /*}*/

        /*.v-select.single-select.icon input {*/
        /*right: 2rem;*/
        /*}*/

        /*.v-select.single-select.icon .selected-tag {*/
        /*position: absolute;*/
        /*right: 2rem;*/
        /*}*/

        /*.v-select.open {*/
        /*background-color: #fff !important;*/
        /*border: none;*/
        /*border-bottom-left-radius: 0;*/
        /*border-bottom-right-radius: 0;*/
        /*}*/

        /*.v-select .selected-tag {*/
        /*float: right;*/
        /*}*/

        /*.v-select .open-indicator {*/
        /*right: inherit !important;*/
        /*left: 10px;*/
        /*}*/

        /*!*.dropdown {*!*/
        /*!*border-radius: .4rem;*!*/
        /*!*box-shadow: 0 0 0.4rem rgba(0,0,0,.12);*!*/
        /*!*background-color: #fff;*!*/
        /*!*border: 1px solid transparent;*!*/
        /*!*}*!*/

        /*.v-select .dropdown-menu {*/
        /*border: none;*/
        /*direction: ltr;*/
        /*text-align: left;*/
        /*padding-top: 1rem;*/
        /*}*/

        /*.v-select .selected-tag {*/
        /*display: inline-flex;*/
        /*!*font-size: small;*!*/
        /*height: inherit !important;*/
        /*width: max-content;*/
        /*}*/

        /*.v-select .selected-tag .close {*/
        /*margin-right: .3rem;*/
        /*}*/

        /*.v-select .open-indicator {*/
        /*bottom: inherit !important;*/
        /*display: inline-flex;*/
        /*align-self: center;*/
        /*margin-top: .2rem;*/
        /*}*/

        /*.v-select input[type=search], .v-select input[type=search]:focus {*/
        /*padding: .5rem;*/
        /*height: inherit !important;*/
        /*line-height: inherit !important;*/
        /*font-size: .9rem;*/
        /*}*/

        /*.dropdown:focus {*/
        /*border: none !important;*/
        /*}*/

        .form-group .hint i {
            display: inline-block;
            vertical-align: middle;
            margin-right: .5rem;
        }

        .form-group .hint span {
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        .form-group .hint {
            display: block;
            margin-left: .2rem;
            margin-top: .3rem;
            margin-bottom: -.5rem;
            cursor: pointer;
        }

        .form-group.iconed i:not(.hint-icon) {
            display: inline-block;
            position: absolute;
            top: .6rem;
            left: 1.5rem;
            color: #b6b6b6;
            width: 1rem;
            text-align: center;
        }

        .btn i {
            display: inline-block;
            vertical-align: middle;
            /*margin-left: .3rem;*/
        }

        .form-group.iconed input {
            padding-left: 2.1rem;
        }

        .form-input-container .message {
            display: block;
            font-family: Shabnam, Tahoma, sans-serif !important;
            font-size: .8rem;
            margin: .5rem 0;
        }

        .form-input-container .message.error {
            color: #C0392B;
        }

        .form-input-container .message.info {
            color: #5499C7;
        }

        .form-input-container label, .form-input-container .label {
            font-family: Shabnam, Tahoma, sans-serif !important;
            font-size: .9rem;
            display: block;
            margin: .3rem 0;
        }

        .form-input-container label.light, .form-input-container .label.light {
            font-weight: 100;
        }

        .form-input-container input {
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, .05);
            outline: none;
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: .9rem;
            border: 1px solid #d4dadf;
            color: #4A4A4A;
        }

        .form-input-container textarea {
            font-size: .9rem;
        }

        .form-input-container textarea:focus {
            font-size: .9rem;
        }

        .form-input-container input:focus {
            border: 1px solid transparent;
        }

        .form-input-container .custom-input-group {
            display: flex;
            flex-wrap: nowrap;
        }

        .form-input-container input[type='file'], .c-file input[type='file'] {
            display: none;
        }

        .basic-card-layout {
            background: #ffffff;
            border-radius: .4rem;
            box-shadow: 0 0 0.4rem rgba(0, 0, 0, .12);
            padding: 1rem;
        }

        .c-file {
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        .custom-input-group .input-right {
            padding: 0 .9rem !important;
            border-bottom-left-radius: 0;
            border-top-left-radius: 0;
        }

        .input-button {
            color: #DF8908 !important;
        }

        .input-button:focus {
            box-shadow: none !important;
            background-color: transparent !important;
        }

        .custom-input-group .input-left {
            padding: 0 .9rem !important;
            border-bottom-right-radius: 0;
            border-top-right-radius: 0;
        }

        .custom-input-group .single-select,
        .custom-input-group input[type="text"] {
            width: 100%;
            border-bottom-right-radius: 0 !important;
            border-top-right-radius: 0 !important;
        }

        .v-select-icon {
            color: #b6b6b6 !important;
            position: absolute;
            display: inline;
            vertical-align: middle;
            top: 30%;
            z-index: 2;
            right: 1.5rem;
        }

        /*.v-select input.form-control {*/
            /*padding-right: 0 !important;*/
        /*}*/

        .v-select .selected-tag {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }

        .dropdown .dropdown-toggle {
            display: inline-flex;
            overflow-x: hidden;
            width: 100%;
            border: none;
        }

        .dropdown .dropdown-toggle > input {
            box-shadow: inherit !important;
            background-color: inherit !important;
            font-size: .9rem;
        }

        .dropdown.single-select .selected-tag {
            background-color: transparent;
            border: none;
            margin: 0;
            display: flex;
            align-items: center;
            padding: 0 0.75rem;
            padding-left: .5rem !important;
            color: #4A4A4A;
        }

        .dropdown.single-select.icon input {
            left: 2rem;
        }

        .dropdown.single-select.icon .selected-tag {
            position: absolute;
            left: 2rem;
            top: .3rem;
        }

        .dropdown.open {
            background-color: #fff !important;
            border: none;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .dropdown .selected-tag {
            float: left;
        }

        .dropdown .open-indicator {
            left: inherit !important;
            right: 10px;
        }

        .dropdown .dropdown-menu {
            border: none;
            direction: ltr;
            text-align: left;
            padding-top: 1rem;
        }

        .dropdown .selected-tag {
            display: inline-flex;
            height: inherit !important;
            width: auto;
        }

        .dropdown .selected-tag .close {
            margin-left: .3rem;
        }

        .dropdown .open-indicator {
            bottom: inherit !important;
            display: inline-flex;
            align-self: center;
            margin-top: .2rem;
        }

        .dropdown .dropdown-toggle .spinner {
            position: absolute;
            right: 10px !important;
            left: auto !important;

        }

        .dropdown input[type=search], .dropdown input[type=search]:focus {
            padding: 0.5rem 0.75rem;
            height: inherit !important;
            line-height: inherit !important;
            font-size: .9rem;
            color: #4A4A4A;
        }

        .dropdown:focus {
            border: none !important;
        }

        .dropdown {
            transition: border 0.4s ease;
        }

        .dropdown.open {
            border: 1px solid transparent;
            box-shadow: 0 1.2rem 3rem rgba(0, 0, 0, .15);
        }

        .dropdown .selected-tag {
            font-size: .9rem;
        }

        /* Base for label styling */
        .check[type="checkbox"]:not(:checked),
        .check[type="checkbox"]:checked {
            position: absolute;
            left: -9999px;
            display: none;
        }

        .check[type="checkbox"]:not(:checked) + label,
        .check[type="checkbox"]:checked + label {
            position: relative;
            padding-left: 1.95em;
            cursor: pointer;
        }

        /* checkbox aspect */
        .check[type="checkbox"]:not(:checked) + label:before,
        .check[type="checkbox"]:checked + label:before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            width: 1.25em;
            height: 1.25em;
            border: 1px solid #d4dadf;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, .05);
        }

        /* checked mark aspect */
        .check[type="checkbox"]:not(:checked) + label:after,
        .check[type="checkbox"]:checked + label:after {
            content: '✔';
            position: absolute;
            top: .1em;
            right: -.2em;
            font-size: 1.3em;
            line-height: 0.8;
            color: #FF9800;
            transition: all .2s;
        }

        /* checked mark aspect changes */
        .check[type="checkbox"]:not(:checked) + label:after {
            opacity: 0;
            transform: scale(0);
        }

        .check[type="checkbox"]:checked + label:after {
            opacity: 1;
            transform: scale(1);
        }

        /* disabled checkbox */
        .check[type="checkbox"]:disabled:not(:checked) + label:before,
        .check[type="checkbox"]:disabled:checked + label:before {
            box-shadow: none;
            border-color: #bbb;
            background-color: #ddd;
        }

        .check[type="checkbox"]:disabled:checked + label:after {
            color: #999;
        }

        .check[type="checkbox"]:disabled + label {
            color: #aaa;
        }

        /* accessibility */
        .check[type="checkbox"]:checked:focus + label:before,
        .check[type="checkbox"]:not(:checked):focus + label:before {
            border: 2px dotted #566573;
        }

        .c-checkbox label > span {
            margin-right: 1.7rem;
        }

        .c-radio {
            display: inline-block;
        }

        .c-radio input[type="radio"] {
            display: none;
        }

        .c-radio .radio-label {
            cursor: pointer;
            display: inline-flex;
            justify-content: space-between;
            align-items: center;
        }

        .c-radio .radio-label:after {
            content: '';
            margin: 0 .3rem;
            border-radius: 50%;
            height: .8rem;
            width: .8rem;
            background-color: #fff;
            border: 1px solid #bbb;
            transition: border 0.2s linear;
        }

        .c-radio input[type='radio']:checked + .radio-label:after {
            content: '';
            text-align: center;
            align-self: center;
            background-color: #fff;
            border: 4px solid orange;
            padding: 2px;
        }

        .c-toggle-switch {
            display: inline-flex !important;
            align-items: center;
        }

        .c-toggle-switch .toggle[type="checkbox"] {
            display: none;
        }

        .c-toggle-switch span {
            margin: 0 .5rem !important;
        }

        .thumb {
            cursor: pointer;
        }

        .c-toggle-switch .thumb {
            direction: ltr !important;
            border-radius: 5rem;
            border: 1px solid #bbb;
            display: inline-flex;
            align-items: center;
            height: 1.3rem;
            width: 2.2rem;
            padding: 0 2px;
            background-color: #FF9800;
            transition: all .3s ease-out;
        }

        .c-toggle-switch .thumb:before {
            content: '';
            background-color: #FFF;
            height: 1rem;
            width: 1rem;
            border-radius: 50%;
            border: none !important;
            transition: all .3s ease-out;
        }

        .c-toggle-switch .toggle[type="checkbox"]:not(:checked) + .thumb {
            background-color: #ccc;
        }

        .c-toggle-switch .toggle[type="checkbox"]:checked + .thumb::before {
            transform: translate(calc(1rem - 2px));
        }

        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            text-indent: 1px;
            text-overflow: '';
            border-radius: .4rem;
            border: 1px solid #d4dadf;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, .05);
            outline: none;
            background: rgb(255, 255, 255);
            width: 100%;
            padding: 0 .5rem;
        }

        .form-input-container.iconed-select i {
            position: absolute;
            bottom: .6rem;
            left: 1.5rem;
            color: rgba(60, 60, 60, .5);
        }
    </style>

    <style>
        body {
            background-color: #fafafa;
            overflow-x: hidden;
        }

        main {
            padding-top: 8.5rem;
            margin-bottom: -4rem;
        }

        .top-margin-fix {
            margin-top: -3.5rem;
        }

        @media all and (max-width: 600px) {
            .top-margin-fix {
                margin-top: 0 !important;
            }
        }

        @media all and (min-width: 400px) {
            main {
                margin-right: 1rem;
                margin-left: 1rem;
            }
        }

        .dropdown-toggle {
            overflow: inherit !important;
        }

        .margin-center {
            margin: 0 auto;
        }

        .img-container {
            text-align: center;
        }

        .img-container > img {
            border-radius: .4rem;
            border: 1px solid #d4dadf;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, .05);
        }

        .shabnam-farsi-numbers {
            font-family: ShabnamFD, Tahoma, sans-serif !important;
        }

        .shabnam-font {
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        .icon-label-left {
            margin-right: .5rem;
        }

        .icon-label-right {
            margin-left: .5rem;
        }

        .align-webkit-moz-middle-baseline {
            vertical-align: -webkit-baseline-middle;
            vertical-align: -moz-middle-with-baseline;
            vertical-align: text-bottom;
        }

        .std-vertical-margin {
            margin: 1rem 0;
        }

        .alert {
            border: none;
            border-radius: .4rem;
        }

        .form-control {
            border: none;
        }

        .form-control:focus {
            border: none;
        }

        .vertical-align-block-center-base-line {
            vertical-align: middle;
            vertical-align: -webkit-baseline-middle;
            vertical-align: -moz-middle-with-baseline;
        }

        div, span, label {
            margin: 0;
            padding: 0;
            border: 0;
            vertical-align: baseline;
        }

        #page-wrapper {
            min-height: 100vh;
        }

        .vertical-most-screen-height-center {
            min-height: 92vh;
            display: flex;
            align-items: center;
        }

        .light-font {
            font-weight: 200;
        }

        .btn-primary:focus, .btn-primary.focus {
            color: lightgray;
        }

        .half-rem-margin-top {
            margin-top: .5rem;
        }

        .no-margin-p {
            margin: 0;
        }

        .full-height {
            height: 100%;
        }

        .custom-control-indicator {
            left: inherit !important;
            right: 0;
        }

        .custom-control {
            padding-left: inherit !important;
            margin-right: inherit !important;

            padding-right: 1.5rem;
            margin-left: 1rem;
        }

        .section-out {
            margin: 1rem 0;
            background-color: #fff;
            width: 100%;
            padding: 1.5rem;
            border-radius: .4rem;
            box-shadow: inset 0 0 0.1rem 0 rgba(0, 0, 0, .12);
        }

        .progress-bar {
            height: 4px;
            background-color: rgba(5, 114, 206, 0.2);
            width: 100%;
            overflow: hidden;
        }

        .progress-bar-value {
            width: 100%;
            height: 100%;
            background-color: rgb(5, 114, 206);
            animation: indeterminateAnimation 1s infinite linear;
            transform-origin: 0% 50%;
        }

        [v-cloak] {
            display: none;
        }

        @keyframes indeterminateAnimation {
            0% {
                transform: translateX(0) scaleX(0);
            }
            40% {
                transform: translateX(0) scaleX(0.4);
            }
            100% {
                transform: translateX(100%) scaleX(0.5);
            }
        }

        /*.btn-group > .btn:first-child:not(:last-child):not(.dropdown-toggle) {*/
        /*border-top-left-radius: 0 !important;*/
        /*border-bottom-left-radius: 0 !important;*/
        /*border-top-right-radius: 0.1875rem !important;*/
        /*border-bottom-right-radius: 0.1875rem !important;*/
        /*}*/

        /*.btn-group > .btn-group:last-child:not(:first-child) > .btn:first-child {*/
        /*border-bottom-left-radius: 0.1875rem !important;*/
        /*border-top-left-radius: 0.1875rem !important;*/
        /*border-bottom-right-radius: 0 !important;;*/
        /*border-top-right-radius: 0 !important;;*/
        /*}*/

        /*.btn-group > .btn:last-child:not(:first-child), .btn-group > .dropdown-toggle:not(:first-child) {*/
        /*border-bottom-left-radius: 0.1875rem !important;*/
        /*border-top-left-radius: 0.1875rem !important;*/
        /*border-bottom-right-radius: 0 !important;;*/
        /*border-top-right-radius: 0 !important;;*/
        /*}*/

        .form-check-label {
            padding-left: inherit !important;
            padding-right: 1.25rem;
        }

        .form-check-input {
            margin-left: inherit !important;
            margin-right: -1.25rem;
        }

        .no-padding {
            padding: 0;
        }

        .hidden {
            display: none !important;
        }

        .font-small {
            font-size: small;
        }

        .sticky-actions-bar {
            position: sticky;
            top: 3.9rem !important;
            /*margin-bottom: 1.1rem;*/
            height: 5rem !important;
        }

        .styled-label {
            font-family: Shabnam, Tahoma, sans-serif !important;
            margin-bottom: .5rem;
        }

        .desktop-header {
            width: 100%;
            align-items: center;
            display: flex;
            height: 4rem;
            top: 0;
            background-color: #fff;
            -webkit-box-shadow: 0 0.1rem 0.8rem rgba(0, 0, 0, .21);
            box-shadow: 0 0.1rem 0.8rem rgba(0, 0, 0, .21);
            z-index: 10;
            color: #4a4a4a;
            transition: top 0.4s ease-in-out;
        }

        .desktop-header .container-alt {
            width: 100%;
            display: flex;
        }

        @media all and (min-width: 850px) {
            .desktop-header .container-alt {
                margin: 0 10rem;
            }
        }

        .desktop-header .left {
            margin-left: .5rem;
        }

        .desktop-header .right {
            margin-right: .5rem;
        }

        .desktop-header .container-alt .left {
            margin-right: auto;
        }

        .desktop-header .container-alt .right {
            margin-left: auto;
        }

        .desktop-header a.l-item {
            color: #4a4a4a;
            transition: border-color .3s;
            text-decoration: none;
            border-bottom: 4px solid transparent;
            font-weight: 300 !important;
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        .desktop-header a.l-item:hover {
            border-color: #777;
        }

        .desktop-header ul {
            display: flex;
            list-style-type: none;
            margin: 0;
        }

        .desktop-header li {
            margin: 0 1rem;
        }
    </style>
    <style>
        .btn-group > .btn:hover, .btn-group-vertical > .btn:hover {
            z-index: inherit !important;
        }

        .btn:focus, .btn:hover, .btn:active {
            border: none;
            outline: none;
        }

        .btn:focus {
            color: #FFFFFF;
            box-shadow: 0 1.2rem 3rem rgba(0, 0, 0, .15);
        }

        .btn-hand {
            cursor: pointer;
        }

        .no-decoration, .no-decoration:hover, .no-decoration:visited, .no-decoration:focus {
            text-decoration: none;
        }

        .link, .link:hover, .link:visited, .link:focus {
            text-decoration: none;
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        .btn-rainbow-dark {
            background: linear-gradient(124deg, #27AE60, #229954, #5499C7, #2980B9, #C0392B, #A93226);
            background-size: 1800% 1800%;
            animation: rainbow 50s ease infinite;
            padding: .9rem;
            color: #FFFFFF;
            box-shadow: 0 0.1rem 0.8rem rgba(0, 0, 0, .21);
        }

        .btn-rainbow-dark:hover {
            color: #FFFFFF;
        }

        .btn {
            background-color: #FAFAFA;;
            color: #4A4A4A;
        }

        .btn:hover, .btn:focus {
            color: #FF9800;
            border: 1px solid #FAFAFA;
        }

        .btn-secondary:focus {
            color: #333333;
        }

        .link-primary {
            color: #566573;
        }

        .link-primary:hover {
            color: #2C3E50;
        }

        .btn-primary {
            color: #FFFFFF;
            background-color: #566573;
        }

        .btn-primary:hover, .btn-primary:focus {
            color: #FFFFFF;
            background-color: #2C3E50;
            border: 1px solid #2C3E50;
        }

        .btn-secondary {
            color: #4A4A4A;
            background-color: #FFFFFF;
        }

        .btn-secondary:hover, .btn-secondary:focus {
            color: #4A4A4A;
            background-color: #E6E6E6;
            border: 1px solid #E6E6E6;
        }

        .link-success {
            color: #27AE60;
        }

        .link-success:hover {
            color: #229954;
        }

        .btn-success {
            color: #FFFFFF;
            background-color: #27AE60;
        }

        .btn-success:hover, .btn-success:focus {
            color: #FFFFFF;
            background-color: #229954;
            border: 1px solid #229954;
        }

        .link-danger {
            color: #A93226;
        }

        .btn-danger {
            color: #FFFFFF;
            background-color: #C0392B;
        }

        .btn-danger:hover, .btn-danger:focus {
            color: #FFFFFF;
            background-color: #A93226;
            border: 1px solid #A93226;
        }

        .link-info {
            color: #5499C7;
        }

        .link-info:hover {
            color: #2980B9;
        }

        .btn-info {
            color: #FFFFFF;
            background-color: #5499C7;
        }

        .btn-icon {
            font-family: Shabnam, Tahoma, sans-serif !important;
            margin-right: .3rem;
        }

        .btn-info:hover, .btn-info:focus {
            color: #FFFFFF;
            background-color: #2980B9;
            border: 1px solid #2980B9;
        }

        .link-accent {
            color: #FF9800;
        }

        .link-accent:hover {
            color: #DF8908;
        }

        .btn-accent {
            background-color: #FF9800;
            color: #FFFFFF !important;
        }

        .btn-accent:hover, .btn-accent:focus {
            color: #FFFFFF;
            background-color: #DF8908;
            border: 1px solid #DF8908;
        }

        .btn-accent-gradient {
            background-color: #ff9800;
            color: #FFFFFF;
            background: linear-gradient(to right, #dbdd37, #ff9800);
        }

        .btn-accent-gradient:hover, .btn-accent-gradient:focus {
            background-color: #DF8908;
            background: linear-gradient(to right, #dbdd37, #DF8908);
            color: #FFFFFF;
            border-width: 1px;
            border-style: solid;
            border-image: linear-gradient(to right, #dbdd37, #DF8908);
            border-left: none;
            border-right: none;
        }

        .badge-btn {
            position: relative;
        }

        .badge-btn[data-badge]:after {
            content: attr(data-badge);
            position: absolute;
            top: -10px;
            left: -10px;
            font-size: .7em;
            background: green;
            color: white;
            width: 18px;
            height: 18px;
            text-align: center;
            line-height: 18px;
            border-radius: 50%;
            box-shadow: 0 0 1px #333;
        }

        .color-accent {
            color: #FF9800;
        }

        .color-danger {
            color: #C0392B;
        }

        .color-info {
            color: #5499C7;
        }

        .cards-container {
            display: flex;
            justify-content: center;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            flex-flow: row wrap;
            width: 100%;
        }

        .bg-color-accent {
            background-color: #FF9800;
        }

        /* hover style just for information */
        label:hover:before {
            border: 2px solid #566573 !important;
        }

        @-webkit-keyframes rainbow {
            0% {
                background-position: 0% 82%
            }
            50% {
                background-position: 100% 19%
            }
            100% {
                background-position: 0% 82%
            }
        }

        @-moz-keyframes rainbow {
            0% {
                background-position: 0% 82%
            }
            50% {
                background-position: 100% 19%
            }
            100% {
                background-position: 0% 82%
            }
        }

        @-o-keyframes rainbow {
            0% {
                background-position: 0% 82%
            }
            50% {
                background-position: 100% 19%
            }
            100% {
                background-position: 0% 82%
            }
        }

        @keyframes rainbow {
            0% {
                background-position: 0% 82%
            }
            50% {
                background-position: 100% 19%
            }
            100% {
                background-position: 0% 82%
            }
        }
    </style>
    <style>
        .definite-select label {
            color: #4a4a4a;
            /*box-shadow: 0 0 0.4rem rgba(0,0,0,.12);*/
            background-color: #ebeef1;
        }

        .definite-select .btn:focus, .definite-select .btn.focus {
            outline: none !important;
            box-shadow: none !important;
        }

        .definite-select label.active {
            background: #FF9800;
            color: white;
        }

        @media all and (min-width: 500px) {
            .definite-select > .btn:first-child:not(:last-child) {
                border-left: none;
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
                border-top-right-radius: 0.1875rem !important;
                border-bottom-right-radius: 0.1875rem !important;
            }

            .definite-select > .btn:first-child.active, .definite-select > .btn:last-child.active {
                border-top-color: #FF9800;
                border-bottom-color: #FF9800;
            }

            .definite-select > .btn:first-child.active {
                border-left: none;
                border-right-color: #FF9800;
            }

            .definite-select > .btn:last-child.active {
                border-right: none;
                border-left-color: #FF9800;
            }

            .definite-select > .btn:last-child:not(:first-child) {
                border-bottom-left-radius: 0.1875rem !important;;
                border-top-left-radius: 0.1875rem !important;;
                border-bottom-right-radius: 0;
                border-top-right-radius: 0;
            }

            .definite-select > .btn:last-child:not(:first-child):not(:last-child) {
                border-radius: 0 !important;
            }
        }

        .definite-select .btn:not(.active) {
            cursor: pointer;
        }

        @media all and (max-width: 500px) {
            .definite-select {
                display: grid;
            }

            .definite-select .btn {
                border: 1px solid #d4dadf !important;
                border-radius: 0.1875rem !important;
            }
        }
    </style>
    <style>
        /*.c-card:not(.job-card) {*/
            /*padding: 2rem !important;*/
        /*}*/

        .c-card > a {
            color: inherit;
            text-decoration: inherit;
        }

        .c-card .header {
            padding: 1rem;
        }

        .c-card .no-logo {
            padding: 4.5rem 1rem;
        }

        .c-card article img.logo {
            width: 6rem;
            height: 6rem;
            box-shadow: 0 0.1rem 0.4rem #cbcbcb;
            border-radius: .4rem;
            /*margin-bottom: 1rem;*/
        }

        .c-card article {
            -webkit-transition: -webkit-box-shadow .3s;
            transition: -webkit-box-shadow .3s;
            -o-transition: box-shadow .3s;
            transition: box-shadow .3s;
            transition: box-shadow .3s, -webkit-box-shadow .3s;
        }

        .c-card article {
            padding: 1rem;
            border-radius: .4rem;
            -webkit-box-shadow: 0 0 0.4rem rgba(0, 0, 0, .12);
            box-shadow: 0 0 0.4rem rgba(0, 0, 0, .12);
            background-color: #fff;
            border: 1px solid transparent;

            overflow: hidden;
            cursor: pointer;
        }

        .c-card article:hover {
            box-shadow: 0 1.2rem 3rem rgba(0, 0, 0, .15);
            transition: box-shadow .3s;
            transition: box-shadow .3s, -webkit-box-shadow .3s;
            -webkit-transition: -webkit-box-shadow .3s;
            transition: -webkit-box-shadow .3s;
            -o-transition: box-shadow .3s;
        }
    </style>
    <style>
        .filter {
            border: 1px solid #e2e2e2;
            padding: .8rem;
            border-radius: .4rem;
            margin-bottom: .5rem
        }

        .nav-up {
            top: -4rem !important;
        }

        .always-on-top {
            z-index: 500;
        }

        #user-bar {
            height: auto !important;
            overflow: hidden;
            position: fixed;
            padding: .5rem 0;
            top: 0;
        }

        #nav-bar {
            height: auto !important;
            /*overflow: hidden;*/
            position: fixed;
            top: 0;
        }

        #nav-bar .container-alt {
            padding: .8rem;
        }

        #nav-bar .row {
            width: 100%;
        }

        #nav-bar .dropdown {
            border: none;
            box-shadow: none;
        }

        #nav-bar .alert-button {
            margin-left: auto;
            padding: .2rem 1rem;
            box-shadow: none;
            border: none;
            outline: none;
            cursor: pointer;
            background: transparent;
        }

        #nav-bar .alert-button i {
            display: inline-block;
            vertical-align: middle;
        }

        #nav-bar .menu-button {
            padding: .2rem 1rem;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, .05);
            outline: none;
            cursor: pointer;
            background-color: #ffffff;
            color: black;
            text-decoration: none;
            border-radius: .4rem;
            border: 1px solid #d4dadf;
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        #nav-bar .menu-button:hover {
            background: #fafafa;
            transition: all 0.2s ease-in-out !important;
        }

        #nav-bar .menu-button:not(:first-child) {
            margin-left: .5rem;
        }

        #nav-bar .menu-button span {
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        #nav-bar .menu-button i {
            display: inline-block;
            vertical-align: middle;
        }

        #nav-bar .links a {
            padding: 1rem;
            color: #566573;
            display: inline-block;
            font-family: Shabnam, Tahoma, sans-serif !important;
            transition: .3s;
            border-top: .2rem solid transparent;
        }

        #nav-bar .dropdown-item {
            font-family: Shabnam, Tahoma, sans-serif !important;
            font-size: .9rem;
            padding: .5rem .8rem;
            transition: all 0.2s ease-in-out !important;
        }

        .dropdown-item.active, .dropdown-item:active {
            color: #fff;
            text-decoration: none;
            background-color: #4a4a4a;
        }

        @media all and (max-width: 415px) {
            #nav-bar .links a {
                font-size: smaller;
            }
        }

        #nav-bar .links a.active {
            border-top: .2rem solid #2C3E50;
            color: #2C3E50;
        }

        #nav-bar .links a:hover {
            border-top: .2rem solid #2C3E50;
            color: #2C3E50;
        }

        #action-bar .container {
            padding-top: .5rem;
        }

        .general-title {
            font-weight: 300 !important;
        }

        .user-reviewer-name {
            font-family: Shabnam, Tahoma, sans-serif;
        }

        #info-container {
            background-color: rgba(255, 255, 255, .90);
            border-radius: .4rem;
            display: flex;
            flex-direction: row;
            width: 100%;
            box-shadow: 0 0 1rem 0.2rem rgba(0, 0, 0, .12);
        }

        .chart {
            width: 100%;
            height: 35rem;
        }

        @media all and (max-width: 1092px) {
            .chart {
                width: 100%;
                height: 30rem;
            }
        }

        .reply-btn {
            cursor: pointer;
        }

        .comment-overlay {
            width: 100%;
            opacity: .9;
            text-align: center;
            position: absolute;
            top: 2rem;
        }

        .review-comment-btn {
            cursor: pointer;
        }

        .user-comment {
            color: #27AE60;
        }

        .comment-pending-review {
            color: #FF9800;
            margin-right: .5rem;
        }

        .disabled-block {
            filter: blur(1.5px);
            pointer-events: none;
        }

        .rate-review-btn {
            align-items: center;
            padding: .2rem;
        }

        .rate-review-btn i, .rate-review-btn span {
            padding: .1rem .3rem;
        }

        .rate-review-btn.selected-btn {
            background-color: #f2f3f4;
            border-radius: .4rem;
        }

        .review-block {
            margin-top: .5rem;
        }

        .container-title {
            margin-top: 1rem;
        }

        .notes-row {
            margin-top: 1rem;
        }

        .normalized-link {
            color: inherit;
            text-decoration: none;
        }

        .reaction-btn {
            margin: .5rem;
            cursor: pointer;
        }

        @media all and (max-width: 1091px) {
            .reaction-btn {
                margin: .5rem 0;
            }
        }

        .review-block section {
            max-height: 50rem;
            position: relative;
            overflow: hidden;
        }

        .review-block .read-more {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            margin: 0;
            padding: 1rem 0;
            background: linear-gradient(rgba(255, 255, 255, 0), #F2F3F4);
        }

        .pagination-row a {
            color: inherit;
        }

        .pagination-btn {
            border: 1px solid transparent;
            cursor: pointer;
            padding: .5rem 2rem;
            transition: all 0.2s ease-in-out;
            margin: 0;
            display: inline-block;
            border-radius: .4rem;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, .05);
        }

        .pagination-btn .label {
            vertical-align: middle;
            display: inline-block;
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        .pagination-btn:hover {
            border: 1px solid #d4dadf;
        }

        .pagination-btn i {
            vertical-align: middle;
            display: inline-block;
            transition: all 0.2s ease-in-out;
        }

        .pagination-btn:hover i {
            color: #FF9800;
        }

        .desktop-footer {
            width: 100%;
            margin-top: 4rem;
            background-color: #fff;
            -webkit-box-shadow: 0 0.1rem 0.8rem rgba(0, 0, 0, .21);
            box-shadow: 0 0.1rem 0.8rem rgba(0, 0, 0, .21);
            z-index: 10;
            color: #4a4a4a;
            padding: 1rem;
        }

        .social-footer {
            /*display: inline-block;*/
            /*vertical-align: middle;*/
            /*text-align: center;*/
            width: 100%;
            /*padding: 2rem 0;*/
            background-color: #191919;
            color: #fff;
        }

        .actions-footer .sum {
            color: #9b9b9b;
            margin-right: 1rem;
        }

        .social-footer .copyright-note {
            color: #9b9b9b;
            font-family: Shabnam, Tahoma, sans-serif !important;
            margin-right: 1rem;
        }

        @media all and (max-width: 1092px) {
            .social-footer .copyright-note {
                margin: 0;
                font-size: .7rem;
            }

            .copyright-note-container {
                text-align: center;
                margin: .5rem 0;
            }

            .social-icon-container {
                margin: .5rem 0;
            }

            .desktop-footer .col-lg-4 {
                margin: .5rem 0;
            }
        }

        .form-control:focus {
            font-size: 1rem;
        }

        #head-bar .user-info {
            font-family: Shabnam, Tahoma, sans-serif !important;
            font-size: .8rem;
            margin: .2rem 1rem;
            color: #DF8908;
        }

        #head-bar .user-info .user-name {
            font-family: Shabnam, Tahoma, sans-serif !important;
        }

        .social-footer .social-icon {
            font-size: 1.5rem;
            margin: 0 .5rem;
            color: #9b9b9b;
        }

        .actions-footer .link-normal a {
            color: #9b9b9b;
        }

        /*.actions-footer {*/
        /*background-color: #202020;*/
        /*}*/

        .actions-footer a {
            margin: .4rem;
            font-size: .9rem;
        }

        .user-link {
            color: inherit;
        }

        .line-on-heading {
            font-size: 1.1rem;
            line-height: 0.5;
            text-align: left;
            color: #4A4A4A;
            margin: 1rem 0;
        }

        .line-on-heading {
            display: inline-block;
            position: relative;
        }

        /* use :after for right (if wanted) */
        .line-on-heading:before {
            content: "";
            border-bottom: .5px solid #4A4A4A;
            border-top: .5px solid #4A4A4A;
            opacity: .3;
            box-shadow: 0 0 0.4rem rgba(0, 0, 0, .12);
            position: absolute;
            top: 50%;
            right: 100%;
            margin-right: 15px;
            width: 100vw;
        }

        @supports (width: fit-content) {
            .width-fit-content {
                width: fit-content;
            }
        }

        @supports (width: -webkit-fit-content) {
            .width-fit-content {
                width: -webkit-fit-content;
            }
        }

        @supports (width: -moz-fit-content) {
            .width-fit-content {
                width: -moz-fit-content;
            }
        }





        .bell-ringing {
            -webkit-animation: ring 4s .7s ease-in-out infinite;
            -webkit-transform-origin: 50% 4px;
            -moz-animation: ring 4s .7s ease-in-out infinite;
            -moz-transform-origin: 50% 4px;
            animation: ring 4s .7s ease-in-out infinite;
            transform-origin: 50% 4px;
            color: #FF9800;
        }

        @-webkit-keyframes ring {
            0% { -webkit-transform: rotateZ(0); }
            1% { -webkit-transform: rotateZ(30deg); }
            3% { -webkit-transform: rotateZ(-28deg); }
            5% { -webkit-transform: rotateZ(34deg); }
            7% { -webkit-transform: rotateZ(-32deg); }
            9% { -webkit-transform: rotateZ(30deg); }
            11% { -webkit-transform: rotateZ(-28deg); }
            13% { -webkit-transform: rotateZ(26deg); }
            15% { -webkit-transform: rotateZ(-24deg); }
            17% { -webkit-transform: rotateZ(22deg); }
            19% { -webkit-transform: rotateZ(-20deg); }
            21% { -webkit-transform: rotateZ(18deg); }
            23% { -webkit-transform: rotateZ(-16deg); }
            25% { -webkit-transform: rotateZ(14deg); }
            27% { -webkit-transform: rotateZ(-12deg); }
            29% { -webkit-transform: rotateZ(10deg); }
            31% { -webkit-transform: rotateZ(-8deg); }
            33% { -webkit-transform: rotateZ(6deg); }
            35% { -webkit-transform: rotateZ(-4deg); }
            37% { -webkit-transform: rotateZ(2deg); }
            39% { -webkit-transform: rotateZ(-1deg); }
            41% { -webkit-transform: rotateZ(1deg); }

            43% { -webkit-transform: rotateZ(0); }
            100% { -webkit-transform: rotateZ(0); }
        }

        @-moz-keyframes ring {
            0% { -moz-transform: rotate(0); }
            1% { -moz-transform: rotate(30deg); }
            3% { -moz-transform: rotate(-28deg); }
            5% { -moz-transform: rotate(34deg); }
            7% { -moz-transform: rotate(-32deg); }
            9% { -moz-transform: rotate(30deg); }
            11% { -moz-transform: rotate(-28deg); }
            13% { -moz-transform: rotate(26deg); }
            15% { -moz-transform: rotate(-24deg); }
            17% { -moz-transform: rotate(22deg); }
            19% { -moz-transform: rotate(-20deg); }
            21% { -moz-transform: rotate(18deg); }
            23% { -moz-transform: rotate(-16deg); }
            25% { -moz-transform: rotate(14deg); }
            27% { -moz-transform: rotate(-12deg); }
            29% { -moz-transform: rotate(10deg); }
            31% { -moz-transform: rotate(-8deg); }
            33% { -moz-transform: rotate(6deg); }
            35% { -moz-transform: rotate(-4deg); }
            37% { -moz-transform: rotate(2deg); }
            39% { -moz-transform: rotate(-1deg); }
            41% { -moz-transform: rotate(1deg); }

            43% { -moz-transform: rotate(0); }
            100% { -moz-transform: rotate(0); }
        }

        @keyframes ring {
            0% { transform: rotate(0); }
            1% { transform: rotate(30deg); }
            3% { transform: rotate(-28deg); }
            5% { transform: rotate(34deg); }
            7% { transform: rotate(-32deg); }
            9% { transform: rotate(30deg); }
            11% { transform: rotate(-28deg); }
            13% { transform: rotate(26deg); }
            15% { transform: rotate(-24deg); }
            17% { transform: rotate(22deg); }
            19% { transform: rotate(-20deg); }
            21% { transform: rotate(18deg); }
            23% { transform: rotate(-16deg); }
            25% { transform: rotate(14deg); }
            27% { transform: rotate(-12deg); }
            29% { transform: rotate(10deg); }
            31% { transform: rotate(-8deg); }
            33% { transform: rotate(6deg); }
            35% { transform: rotate(-4deg); }
            37% { transform: rotate(2deg); }
            39% { transform: rotate(-1deg); }
            41% { transform: rotate(1deg); }

            43% { transform: rotate(0); }
            100% { transform: rotate(0); }
        }
    </style>
    @yield('head')
</head>

<body itemscope itemtype="http://schema.org/WebPage">

<nav id="head-bar" class="desktop-header fixed-top always-on-top">
    <div class="container-alt">
        <div class="left" style="align-self: center;">
            <div>
                <ul class="no-padding">
                    @if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true)
                        <li><a class="l-item" href="/account/login?redirect={{ $_SERVER['REQUEST_URI'] }}">Login or register</a></li>
                    @else
                        <li><a class="l-item" style="color: indianred;" href="/{{ $router->route('logout') }}">Log out</a></li>
                    @endif
                    <li><a class="l-item" href="/help">Help</a></li>
                </ul>
            </div>
            @if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true)
                <div>
                    <div class="user-info">
                        <a class="link user-link" href="/account/info">
                            <span><i class="fa fa-user align-middle"></i></span>
                            <span class="icon-label-left user-name">{{ $_SESSION['name'] }}</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
        <div class="right" style="align-self: center; margin-left: 1rem">
            <a href="/"><img style="width: 7.5rem; display: flex; align-self: center" src="/assets/img/veezee-logotype.svg"></a>
        </div>
    </div>
</nav>
<nav id="nav-bar" class="desktop-header fixed-top sticky-actions-bar" style="z-index: 20;">
    <div class="container-alt">
        <div class="row">
            <div class="col-lg-12 d-flex">
                <div class="dropdown">
                    <button type="button" class="menu-button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bars"></i>
                        <span style="margin-right: .3rem;">Menu</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">my link</a>
                        <a class="dropdown-item" href="#">my link</a>
                    </div>
                </div>

                @if(\App\Utils\AuthHelper::isLoggedIn())
                    @if(\App\Utils\AuthHelper::getUserType() == 'admin')
                        <a type="button" href="/{{ ADMIN_URL }}" class="menu-button">
                            <span>Admin Panel</span>
                        </a>
                    @elseif(\App\Utils\AuthHelper::getUserType() == 'normal')
                        <a type="button" class="menu-button">
                            <span>User Panel</span>
                        </a>
                    @endif
                @endif

            </div>
        </div>
    </div>
</nav>
@yield('action-bar')
<main>
    <div id="page-wrapper" class="container-fluid">
        @if(stripos($_SERVER['REQUEST_URI'], ADMIN_URL) !== false && \App\Utils\AuthHelper::isLoggedIn())
            <div class="row">
                <div class="col-lg-12" style="background-color: #E6E6E6; border-radius: .4rem;">
                    <nav class="text-center">
                        <a href="/{{ ADMIN_URL }}" class="btn btn-secondary">Dashboard</a>
                        <a href="/{{ ADMIN_URL }}/artists" class="btn btn-secondary">Artists</a>
                        <a href="/{{ ADMIN_URL }}/albums" class="btn btn-secondary">Albums</a>
                        <a href="/{{ ADMIN_URL }}/playlists" class="btn btn-secondary">Playlists</a>
                        <a href="/{{ ADMIN_URL }}/genres" class="btn btn-secondary">Genres</a>
                    </nav>
                </div>
            </div>
            <br>
        @endif
        @yield('body')
    </div>
</main>
<footer>
    <div class="desktop-footer actions-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    Footer note here
                </div>
            </div>
        </div>
    </div>
    <div class="social-footer">
        <div class="container">
            <div class="row" style="padding: .5rem 0">
                <div class="col-lg-8 align-self-center copyright-note-container">
                    <small class="copyright-note">All rights reserved.</small>
                </div>
                <div class="social-icon-container col-lg-4 align-self-center text-center">
                    <a title="telegram" class="social-icon d-inline-block align-middle"><i class="fa fa-telegram"></i></a>
                    <a title="facebook" class="social-icon d-inline-block align-middle"><i class="fa fa-facebook"></i></a>
                    <a title="twitter" class="social-icon d-inline-block align-middle"><i class="fa fa-twitter"></i></a>
                    <a title="instagram" class="social-icon d-inline-block align-middle"><i class="fa fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

</body>

<script src="/assets/js/bundle.jquery.tether.bootstrap.throttle.responsive-headers.min.js"></script>
<script src="/assets/js/croppie.min.js"></script>
<script src="/assets/js/axios.min.js"></script>

@include('/_components/job-card')
@include('/_components/custom-select')

<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    var navBar = $('#nav-bar');
    var actionBar = $('#action-bar');

    if (!actionBar.length)
        actionBar = null;

    var didScroll;
    var lastScrollTop = 0;
    var delta = 5;
    var navbarHeight = navBar.outerHeight();

    var actionBarTop = $('#head-bar').height();
    if (actionBar !== null)
        actionBarTop += actionBar.height();

    var extraSpecialPadding = 2 * 16;
    var smallWidthDevicesException = 0;

    if (window.outerWidth <= 1091) {
        smallWidthDevicesException = 1.5 * 16;
    }

    $("<style type='text/css'> .action-bar-after-nav-bar{ top: " + (actionBarTop - extraSpecialPadding - smallWidthDevicesException) + "px !important;} </style>").appendTo("head");

    $(window).scroll(function (event) {
        didScroll = true;
    });

    var initalActionExecuted = false;
    setInterval(function () {
        if (didScroll || !initalActionExecuted) {
            hasScrolled();
            didScroll = false;

            if (!initalActionExecuted) {
                navBar.removeClass('nav-up').addClass('nav-down');

                if (actionBar !== null)
                    actionBar.addClass('action-bar-after-nav-bar');
                initalActionExecuted = true;

                setTimeout(function () {

                    try {
                        var actionBar = document.getElementById('action-bar').getBoundingClientRect();
                        var pageWrapper = document.getElementById('page-wrapper').getBoundingClientRect();

                        var overlap = !(actionBar.right < pageWrapper.left ||
                            actionBar.left > pageWrapper.right ||
                            actionBar.bottom < pageWrapper.top ||
                            actionBar.top > pageWrapper.bottom);

                        // fix problems in Safari
                        if (overlap) {
                            $('#page-wrapper').css('margin-top', '3rem');
                        } else {
                            $('#page-wrapper').css('margin-top', '-2rem');
                        }
                    } catch (e) {

                    }

                }, 250);
            }
        }
    }, 250);

    function hasScrolled() {
        var st = $(this).scrollTop();

        // Make sure they scroll more than delta
        if (Math.abs(lastScrollTop - st) <= delta)
            return;

        // If they scrolled down and are past the navbar, add class .nav-up.
        // This is necessary so you never see what is "behind" the navbar.
        if (st > lastScrollTop && st > navbarHeight) {
            // Scroll Down
            navBar.removeClass('nav-down').addClass('nav-up');
            if (actionBar !== null)
                actionBar.removeClass('action-bar-after-nav-bar');
        } else {
            // Scroll Up
            if (st + $(window).height() < $(document).height()) {
                navBar.removeClass('nav-up').addClass('nav-down');
                if (actionBar !== null)
                    actionBar.addClass('action-bar-after-nav-bar');
            }
        }

        lastScrollTop = st;
    }
</script>
<script type="text/javascript">
    //    axios.defaults.baseURL = AppVars.ApiDomain;
    //    if (localStorage.getItem('auth_token')) {
    //        axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
    //    }
    axios.interceptors.response.use(function (response) {
        return response;
    }, function (error) {
        // Do something with response error
        if (error.response.status === 401) {
            window.location.href = '/account/login';
        }
        return Promise.reject(error);
    });

    // This Function will always return the initial font-size of the html element
    var rem = function rem() {
        var html = document.getElementsByTagName('html')[0];

        return function () {
            return parseInt(window.getComputedStyle(html)['fontSize']);
        }
    }();

    $(document).one('focus.auto-expand', 'textarea.auto-expand', function () {
        var savedValue = this.value;
        this.value = '';
        this.baseScrollHeight = this.scrollHeight;
        this.value = savedValue;
    }).on('input.auto-expand', 'textarea.auto-expand', function () {
        var minRows = this.getAttribute('data-min-rows') | 0, rows;
        this.rows = minRows;
        rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 17);
        this.rows = minRows + rows;
    });

    // This function will convert pixel to rem
    function toRem(length) {
        return (parseInt(length) / rem());
    }

    function smoothScrollTo(el) {
        document.querySelector(el).scrollIntoView({
            behavior: 'smooth'
        });
    }
</script>
<script>
    {{--@if(\App\Utils\AuthHelper::isLoggedIn())--}}
        {{--var updateUserLoginInterval = setInterval(function () {--}}
            {{--try {--}}
                {{--axios.post('/account/update-user-login', null).then(function (response) {--}}

                {{--}).catch(function (error) {--}}
                    {{--clearInterval(updateUserLoginInterval);--}}
                    {{--new Noty({--}}
                        {{--type: 'error',--}}
                        {{--timeout: 7000,--}}
                        {{--text: 'باید دوباره وارد حساب خود شوید.',--}}
                        {{--theme: 'rank30t'--}}
                    {{--}).show();--}}
                {{--}).then(function () {--}}
                {{--});--}}
            {{--} catch (e) {--}}

            {{--}--}}
        {{--}, 8000);--}}
    {{--@endif--}}
</script>
@yield('scripts')
</html>