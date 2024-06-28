@extends('dashboard.auth.layout')

@section('content')
    <h4 class="card-title mb-1 text-center">{!! trans('dashboard.messages.welcome_login_msg',['project_name' => setting('project_name')]) !!}! 👋</h4>

    <form class="auth-login-form mt-2" action="{!! route('dashboard.post_login') !!}" method="POST">
        {!! csrf_field() !!}
        <div class="form-group">
            <label for="login-username" class="form-label">{!! trans('dashboard.auth.username') !!}</label>
            <input type="text" class="form-control" id="login-username" name="username" placeholder="{!! trans('dashboard.auth.username') !!}" aria-describedby="login-username" tabindex="1" autofocus />
        </div>

        <div class="form-group">
            <div class="d-flex justify-content-between">
                <label for="login-password">{!! trans('dashboard.general.password') !!}</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge" id="login-password" name="password" tabindex="2" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="login-password"/>
                <div class="input-group-append">
                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input" type="checkbox" name="remember" id="remember-me" tabindex="3" />
                <label class="custom-control-label" for="remember-me"> {!! trans('dashboard.auth.remember') !!} </label>
            </div>
        </div>
        <button class="btn btn-primary btn-block" tabindex="4">{!! trans('dashboard.auth.login') !!}</button>
    </form>

@endsection
