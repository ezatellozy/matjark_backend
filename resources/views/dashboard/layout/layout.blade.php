<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="{{ LaravelLocalization::getCurrentLocaleDirection() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    @include('dashboard.layout.meta')
    <title>{!! trans('dashboard.general.cpanel',['title' => $title?? '']) !!}</title>
    <link rel="apple-touch-icon" href="{{ asset('dashboardAssets') }}/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="@if(setting('logo')) {{setting('logo')}} @else{{ asset('dashboardAssets') }}/images/icons/logo_sm.png @endif">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    @include('dashboard.layout.styles')

    <script>
        window.isRtl = document.getElementsByTagName("HTML")[0].getAttribute("data-textdirection") === 'rtl' ;
    </script>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern dark-layout  navbar-floating footer-static  @yield('body','2-columns')" data-open="click" data-menu="vertical-menu-modern" data-col="@yield('body_col','2-columns')" data-layout="dark-layout">

    @include('dashboard.layout.header')
    @include('dashboard.layout.sidebar')
    @include('dashboard.layout.alert')

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                @yield('content_header')
            </div>
            <div class="content-body">
                @yield('content')
            </div>
            <div class="content-area-wrapper">
                @yield('content_area')
            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    @include('dashboard.layout.customizer')
    @include('dashboard.layout.footer')
    @include('dashboard.layout.scripts')
    @yield('notify')
    <div class="model"></div>
</body>
<!-- END: Body-->

</html>
