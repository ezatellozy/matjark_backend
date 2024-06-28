<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="{{ LaravelLocalization::getCurrentLocaleDirection() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="{{ setting('desc_'.app()->getLocale()) }}">
    <meta name="keywords" content="{{ setting('desc_'.app()->getLocale()) }}">
    <meta name="author" content="PIXINVENT">
    <title>{!! trans('dashboard.auth.login') !!}</title>
    <link rel="apple-touch-icon" href="@if(setting('logo')) {{setting('logo')}} @else{{ asset('dashboardAssets') }}/images/icons/logo_sm.png @endif">
    <link rel="shortcut icon" type="image/x-icon" href="@if(setting('logo')) {{setting('logo')}} @else {{ asset('dashboardAssets') }}/images/icons/logo_sm.png @endif" >
    <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/extensions/toastr.min.css">

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/bootstrap-extended.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/colors.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/components.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/themes/dark-layout.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/themes/bordered-layout.min.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/core/menu/menu-types/vertical-menu.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/pages/page-auth.min.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/plugins/extensions/ext-component-toastr.min.css">
    <!-- END: Custom CSS-->

    <style media="screen">
        *{
            font-family: 'Cairo', sans-serif;
            font-weight: normal;
            font-style: normal;
            /* font-size: 14px; */
        }
    </style>
    <script>
        window.isRtl = document.getElementsByTagName("HTML")[0].getAttribute("data-textdirection") === 'rtl' ;
    </script>
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
            @include('dashboard.layout.alert')
            <div class="content-body">
                <div class="auth-wrapper auth-v1 px-2">
                    <div class="auth-inner py-2">
                        <!-- Login v1 -->
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="javascript:void(0);" class="brand-logo">
                                    <img src="@if(setting('logo')) {{setting('logo')}} @else{{ asset('dashboardAssets') }}/images/icons/logo_sm.png @endif" alt="" style="width: 95.5px;height: 53px;padding: 1px 1px;">
                                </a>

                                @yield('content')


                            </div>
                        </div>
                        <!-- /Login v1 -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('dashboardAssets') }}/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->
    <script src="{{ asset('dashboardAssets') }}/vendors/js/extensions/toastr.min.js"></script>

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('dashboardAssets') }}/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('dashboardAssets') }}/js/core/app-menu.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/js/core/app.min.js"></script>
    <!-- END: Theme JS-->
    <script src="{{ asset('dashboardAssets') }}/js/scripts/extensions/ext-component-toastr.js"></script>

    <!-- BEGIN: Page JS-->
    <script src="{{ asset('dashboardAssets') }}/js/scripts/pages/page-auth-login.js"></script>
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
    @yield('notify')
</body>
<!-- END: Body-->

</html>
