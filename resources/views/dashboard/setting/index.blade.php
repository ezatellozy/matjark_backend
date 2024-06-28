@extends('dashboard.layout.layout')

@section('content')
<section id="nav-filled">
    <div class="row match-height">
        <!-- Filled Tabs starts -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{!! trans('dashboard.setting.setting') !!}</h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['route'=>'dashboard.setting.store','method'=>'POST','files'=>true,'class'=>'form-horizontal']) !!}
                    <!-- Nav tabs -->


                    <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#main-fill" role="tab" aria-controls="main-fill" aria-selected="true">{!! trans('dashboard.setting.main_setting') !!}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#mail-fill" role="tab" aria-controls="mail-fill" aria-selected="false">{!! trans('dashboard.setting.mail_setting') !!}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="messages-tab-fill" data-toggle="tab" href="#sms-fill" role="tab" aria-controls="sms-fill" aria-selected="false">{!! trans('dashboard.setting.sms_setting') !!}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab-fill" data-toggle="tab" href="#about-fill" role="tab" aria-controls="about-fill" aria-selected="false">{!! trans('dashboard.setting.setting_about') !!}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab-fill" data-toggle="tab" href="#social-fill" role="tab" aria-controls="social-fill" aria-selected="false">{!! trans('dashboard.social.social') !!}</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content pt-1">
                        <div class="tab-pane active" id="main-fill" role="tabpanel" aria-labelledby="main-tab-fill">
                            <div class="form-group mt-4">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.project_name') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('project_name', setting('project_name') ? setting('project_name'):old('project_name'), ['class'=>'form-control','id'=>'project_name','placeholder'=>trans('dashboard.setting.project_name')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.general.email') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('email', setting('email') ? setting('email'):old('email'), ['class'=>'form-control','id'=>'email','placeholder'=>trans('dashboard.general.email')]) !!}
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.general.phones') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::text('phones', setting('phones') ? setting('phones'):old('phones'), ['class'=>'form-control tagsinput-custom-tag-class','placeholder'=>trans('dashboard.setting.phones')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.map_api') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::text('map_api', setting('map_api') ? setting('map_api'):old('map_api'), ['class'=>'form-control','placeholder'=>trans('dashboard.setting.map_api')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.general.logo') !!}</label>
                                    <div class="col-lg-9">
                                        <div class="custom-file">
                                            <input type="file" name="logo" class="custom-file-input" id="inputGroupFile02" onchange="readUrl(this)">
                                            <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        @if (setting('logo'))
                                        <img src="{{ setting('logo') }}" class="img-thumbnail image-preview" style="width: 100%; height: 100px;;">
                                        @else
                                        <img src="{{ asset('dashboardAssets') }}/images/logo/logo_{{ app()->getLocale() }}.png" class="img-thumbnail image-preview" style="width: 100%; height: 100px;">

                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="mail-fill" role="tabpanel" aria-labelledby="mail-tab-fill">
                            <div class="form-group mt-4">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.driver_mail') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('driver', setting('driver') ? setting('driver'):old('driver'), ['class'=>'form-control','id'=>'driver','placeholder'=>trans('dashboard.setting.driver_mail')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.host') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('host', setting('host') ? setting('host'):old('host'), ['class'=>'form-control','id'=>'host','placeholder'=>trans('dashboard.setting.host')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.from_address') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('from_address', setting('from_address') ? setting('from_address'):old('from_address'), ['class'=>'form-control','id'=>'from_address','placeholder'=>trans('dashboard.setting.from_address')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.from_name',['name'=>setting('project_name')]) !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('from_name', setting('from_name') ? setting('from_name'):old('from_name'),
                                        ['class'=>'form-control','id'=>'from_name','placeholder'=>trans('dashboard.setting.from_name',['name'=>setting('project_name')])])
                                        !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.username') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('username', setting('username') ? setting('username'):old('username'), ['class'=>'form-control','id'=>'username','placeholder'=>trans('dashboard.setting.username')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.general.password') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::password('password', ['class'=>"form-control", 'id'=>"password", 'placeholder'=>trans('dashboard.general.password')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.port') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('port', setting('port') ? setting('port'):old('port'), ['class'=>'form-control','id'=>'port','placeholder'=>trans('dashboard.setting.port')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.encry') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::select('encry', ['tls'=>'tls','ssl'=>'ssl'],setting('encry') ? setting('encry') : old('encry'), ['class'=>'form-control select-search', 'id'=>"encry", 'placeholder'=>trans('dashboard.setting.encry')])
                                        !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="sms-fill" role="tabpanel" aria-labelledby="sms-tab-fill">
                            <div class="form-group row mt-4">
                                <label class="col-form-label col-lg-2">{{ trans('dashboard.setting.use_sms_service') }}</label>
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="custom-control custom-control-success custom-radio col-md-6">
                                            {!! Form::radio('use_sms_service', "enable" , setting('use_sms_service') == 'enable' ? 'checked' : null,['class' => 'custom-control-input' , 'id' => 'sms_service_enable']) !!}

                                            <label class="custom-control-label" for="sms_service_enable">{!! trans('dashboard.setting.sms_service_enable') !!}</label>
                                        </div>
                                        <div class="custom-control custom-control-danger custom-radio">
                                            {!! Form::radio('use_sms_service', "disable" , setting('use_sms_service') == 'disable' || ! setting('use_sms_service') ? 'checked' : null,['class' => 'custom-control-input', 'id' => 'sms_service_disable'])
                                            !!}

                                            <label class="custom-control-label" for="sms_service_disable">{!! trans('dashboard.setting.sms_service_disable') !!}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{ trans('dashboard.setting.sms_provider') }}</label>
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="custom-control custom-control-success custom-radio col-md-4">
                                            {!! Form::radio('sms_provider', "hisms" , setting('sms_provider') == 'hisms' || ! setting('sms_provider') ? 'checked' : null,['class' => 'custom-control-input', 'id' => 'sms_service_hisms']) !!}

                                            <label class="custom-control-label" for="sms_service_hisms">{!! trans('dashboard.setting.sms_service_hisms') !!}</label>

                                        </div>
                                        <div class="custom-control custom-control-success custom-radio col-md-4">
                                            {!! Form::radio('sms_provider', "net_powers" , setting('sms_provider') == 'net_powers' ? 'checked' : null,['class' => 'custom-control-input', 'id' => 'sms_service_net_powers']) !!}

                                            <label class="custom-control-label" for="sms_service_net_powers">{!! trans('dashboard.setting.sms_service_net_powers') !!}</label>
                                        </div>
                                        <div class="custom-control custom-control-success custom-radio">
                                            {!! Form::radio('sms_provider', "sms_gateway" , setting('sms_provider') == 'sms_gateway' ? 'checked' : null,['class' => 'custom-control-input', 'id' => 'sms_service_sms_gateway']) !!}

                                            <label class="custom-control-label" for="sms_service_sms_gateway">{!! trans('dashboard.setting.sms_service_sms_gateway') !!}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.sms_sender_name') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::text('sms_sender_name', setting('sms_sender_name') ? setting('sms_sender_name'):old('sms_sender_name'),
                                        ['class'=>'form-control','id'=>'sms_sender_name','placeholder'=>trans('dashboard.setting.sms_sender_name')])
                                        !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.sms_username') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::text('sms_username', setting('sms_username') ? setting('sms_username'):old('sms_username'), ['class'=>'form-control','id'=>'sms_username','placeholder'=>trans('dashboard.setting.sms_username')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.setting.sms_password') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::text('sms_password', setting('sms_password') ? setting('sms_password'):old('sms_password'), ['class'=>'form-control','id'=>'sms_password','placeholder'=>trans('dashboard.setting.sms_password')]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="about-fill" role="tabpanel" aria-labelledby="about-tab-fill">
                            <div class="form-group mt-4">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.ar.policy') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::textarea('policy_ar', setting('policy_ar') ? setting('policy_ar'):old('policy_ar'), ['class'=>'form-control editor','placeholder'=>trans('dashboard.ar.policy')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.en.policy') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::textarea('policy_en', setting('policy_en') ? setting('policy_en'):old('policy_en'), ['class'=>'form-control editor','id' => 'full-container','placeholder'=>trans('dashboard.en.policy')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.ar.terms') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::textarea('terms_ar', setting('terms_ar') ? setting('terms_ar'):old('terms_ar'), ['class'=>'form-control editor ','placeholder'=>trans('dashboard.ar.terms')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.en.terms') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::textarea('terms_en', setting('terms_en') ? setting('terms_en'):old('terms_en'), ['class'=>'form-control editor','id' => 'full-container','placeholder'=>trans('dashboard.en.terms')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.ar.desc') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::textarea('desc_ar', setting('desc_ar') ? setting('desc_ar'):old('desc_ar'), ['class'=>'form-control editor','placeholder'=>trans('dashboard.ar.desc')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.en.desc') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::textarea('desc_en', setting('desc_en') ? setting('desc_en'):old('desc_en'), ['class'=>'form-control editor','placeholder'=>trans('dashboard.en.desc')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.ar.meta') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::textarea('meta_ar', setting('meta_ar') ? setting('meta_ar'):old('meta_ar'), ['class'=>'form-control editor','placeholder'=>trans('dashboard.ar.meta')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.en.meta') !!}</label>
                                    <div class="col-md-10">
                                        {!! Form::textarea('meta_en', setting('meta_en') ? setting('meta_en'):old('meta_en'), ['class'=>'form-control editor','placeholder'=>trans('dashboard.en.meta')]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="social-fill" role="tabpanel" aria-labelledby="social-tab-fill">
                            <div class="form-group mt-4">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.whatsapp') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('whatsapp', setting('whatsapp') ? setting('whatsapp'):old('whatsapp'), ['class'=>'form-control','id'=>'whatsapp','placeholder'=>trans('dashboard.social.whatsapp')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.sms_message') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('sms_message', setting('sms_message') ? setting('sms_message'):old('sms_message'), ['class'=>'form-control','id'=>'sms_message','placeholder'=>trans('dashboard.social.sms_message')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.facebook') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('facebook', setting('facebook') ? setting('facebook'):old('facebook'), ['class'=>'form-control','id'=>'facebook','placeholder'=>trans('dashboard.social.facebook')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.twitter') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('twitter', setting('twitter') ? setting('twitter'):old('twitter'), ['class'=>'form-control','id'=>'twitter','placeholder'=>trans('dashboard.social.twitter')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.youtube') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('youtube', setting('youtube') ? setting('youtube'):old('youtube'), ['class'=>'form-control','id'=>'youtube','placeholder'=>trans('dashboard.social.youtube')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.instagram') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('instagram', setting('instagram') ? setting('instagram'):old('instagram'), ['class'=>'form-control','id'=>'instagram','placeholder'=>trans('dashboard.social.instagram')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.linkedin') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('linkedin', setting('linkedin') ? setting('linkedin'):old('linkedin'), ['class'=>'form-control','id'=>'linkedin','placeholder'=>trans('dashboard.social.linkedin')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.gmail') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('gmail', setting('gmail') ? setting('gmail'):old('gmail'), ['class'=>'form-control','id'=>'gmail','placeholder'=>trans('dashboard.social.gmail')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.g_play_app') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('g_play_app', setting('g_play_app') ? setting('g_play_app'):old('g_play_app'), ['class'=>'form-control','id'=>'g_play_app','placeholder'=>trans('dashboard.social.g_play_app')]) !!}
                                    </div>
                                    <label class="font-medium-1 col-md-2">{!! trans('dashboard.social.app_store_app') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text('app_store_app', setting('app_store_app') ? setting('app_store_app'):old('app_store_app'),
                                        ['class'=>'form-control','id'=>'app_store_app','placeholder'=>trans('dashboard.social.app_store_app')]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{ trans('dashboard.general.save') }} <i class="{{ app()->getLocale() == 'ar' ? 'icon-arrow-left13' : 'icon-arrow-right13'}} position-right"></i></button>
                    </div>

                    {!! form::close() !!}
                </div>
            </div>
        </div>
        <!-- Filled Tabs ends -->
    </div>
</section>
@endsection


@include('dashboard.setting.scripts')
