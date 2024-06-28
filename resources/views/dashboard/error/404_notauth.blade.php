<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="{{ LaravelLocalization::getCurrentLocaleDirection() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
<!-- BEGIN: Head-->

<head>
    @include('dashboard.layout.meta')
    <title>404 - {{ setting('project_name') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('dashboardAssets') }}/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon"@if(setting('logo')) href="{{setting('logo')}}" @else href="{{asset('dashboardAssets') }}/images/icons/logo_sm.png" @endif>
    <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/colors.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/components.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/themes/bordered-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/pages/page-misc.css">
    <!-- END: Page CSS-->

    <style media="screen">
        *{
            font-family: 'Cairo', sans-serif;
            font-weight: normal;
            font-style: normal;
            /* font-size: 14px; */
        }
    </style>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern dark-layout blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page" data-layout="dark-layout">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Error page-->
                <div class="misc-wrapper"><a class="brand-logo" href="javascript:void(0);">
                        <img width="50px" height="50px" src="@if(setting('logo')) {{setting('logo')}} @else {{ asset('dashboardAssets') }}/images/icons/logo_sm.png @endif"  alt="">
                        <h1 class="brand-text text-primary ml-1">{{ setting('project_name') }}</h1>
                    </a>
                    <div class="misc-inner p-2 p-sm-3">
                        <div class="w-100 text-center">
                            <h2 class="mb-1">{!! trans('dashboard.error.404') !!} 🕵🏻‍♀️</h2>
                            <p class="mb-2">Oops! 😖 {!! trans('dashboard.error.404_msg') !!}.</p><img class="img-fluid" src="{{ asset('dashboardAssets') }}/images/pages/error-dark.svg" alt="404" />
                        </div>
                    </div>
                </div>
                <!-- / Error page-->
            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('dashboardAssets') }}/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('dashboardAssets') }}/js/core/app-menu.js"></script>
    <script src="{{ asset('dashboardAssets') }}/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body-->

</html>
