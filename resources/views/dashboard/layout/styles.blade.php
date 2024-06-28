@if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/vendors-rtl.min.css">
@else
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/vendors.min.css">
@endif

<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/extensions/toastr.min.css">

@yield('vendor_styles')

<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/bootstrap-extended.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/colors.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/components.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/themes/dark-layout.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/themes/bordered-layout.min.css">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">

<!-- BEGIN: Page CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/core/menu/menu-types/vertical-menu.min.css">

@yield('page_styles')

<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/plugins/extensions/ext-component-toastr.min.css">
<!-- END: Page CSS-->

<style>
    * {
        font-family: 'Cairo', sans-serif;
        font-weight: normal;
        font-style: normal;
        /* font-size: 14px; */
    }

    .model{
       background: rgba( 255, 255, 255, .8 ) url("{{ asset('dashboardAssets') }}/global/images/loader/loader.gif")
       50% 50%
       no-repeat;
    }

    body.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub>a:not(.mm-next):after {
        -webkit-transform: rotate(180deg);
        -ms-transform: rotate(180deg);
        transform: rotate(180deg);
    }

    body.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.open>a:not(.mm-next):after {
        -webkit-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        transform: rotate(90deg);
    }
</style>
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/custom/styles.css">
