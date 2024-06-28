@extends('dashboard.layout.layout')

@section('content')

<!-- Container -->
<div class="flex-fill">

    <!-- Error title -->
    <div class="text-center mb-3 mt-20">
        <h1 class="error-title">{!! trans('dashboard.error.404') !!}</h1>
        <h5>{!! trans('dashboard.error.404_msg') !!}</h5>
    </div>
    <!-- /error title -->


</div>
<!-- /container -->

@endsection
@section('page_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/{{ LaravelLocalization::getCurrentLocaleDirection() }}/css/pages/error.css">
@endsection
