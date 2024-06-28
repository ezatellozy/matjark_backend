<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="{{ setting('desc_'.app()->getLocale()) }}">
    <meta name="keywords" content="{{ setting('desc_'.app()->getLocale()) }}">
    <meta name="author" content="PIXINVENT">
    <title>{!! trans('dashboard.general.coming_soon') !!}</title>
    <link rel="apple-touch-icon" href="{{ asset('dashboardAssets') }}/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="@if(setting('logo')) {{setting('logo')}} @else{{ asset('dashboardAssets') }}/images/icons/logo_sm.png @endif">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/vendors-rtl.min.css">
    @else
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/vendors.min.css">
    @endif
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/colors.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/components.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/pages/coming-soon.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/custom-rtl.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/custom_assets/css/style-rtl.css">
    @else
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/custom_assets/css/style.css">

    @endif
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern dark-layout 1-column  navbar-floating footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column" data-layout="dark-layout">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- coming soon flat design -->
                <section>
                    <div class="row d-flex vh-100 align-items-center justify-content-center">
                        <div class="col-xl-5 col-md-8 col-sm-10 col-12 px-md-0 px-2">
                            <div class="card text-center w-100 mb-0">
                                <div class="card-header justify-content-center pb-0">
                                    <div class="card-title">
                                        <h2 class="mb-0">We are launching soon</h2>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-body pt-0">
                                        <img src="{{ asset('dashboardAssets') }}/images/cover/cover_sm.png" class="img-responsive block width-350 mx-auto" width="350" height="250" alt="bg-img">
                                        <div id="clockFlat" class="card-text text-center getting-started pt-2 d-flex justify-content-center flex-wrap"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--/ coming soon flat design -->

            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('dashboardAssets') }}/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('dashboardAssets') }}/vendors/js/coming-soon/jquery.countdown.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('dashboardAssets') }}/js/core/app-menu.js"></script>
    <script src="{{ asset('dashboardAssets') }}/js/core/app.js"></script>
    <script src="{{ asset('dashboardAssets') }}/js/scripts/components.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{ asset('dashboardAssets') }}/js/scripts/pages/coming-soon.js"></script>
    {{-- <script src="{{ asset('dashboardAssets') }}/custom_assets/js/timer.js"></script> --}}
    <!-- END: Page JS-->
    <script>
        $(function(){
            countDown("{{ now()->addDays(now()->diffInDays("2020-06-20 23:59:59"))->format("Y-m-d H:i:s") }}");
        });
    </script>

</body>
<!-- END: Body-->

</html>
