<div class="bs-stepper-header">
	<div class="step" data-target="#step_locales">
		<button type="button" class="step-trigger">
			<span class="bs-stepper-box">
				<i data-feather="flag" class="font-medium-3"></i>
			</span>
			<span class="bs-stepper-label">
				<span class="bs-stepper-title">{!! trans('dashboard.general.locales') !!}</span>
				{{-- <span class="bs-stepper-subtitle">Setup Account Details</span> --}}
			</span>
		</button>
	</div>
	<div class="line">
		<i data-feather="chevron-right" class="font-medium-2"></i>
	</div>
	<div class="step" data-target="#step_public_data">
		<button type="button" class="step-trigger">
			<span class="bs-stepper-box">
				<i data-feather="settings" class="font-medium-3"></i>
			</span>
			<span class="bs-stepper-label">
				<span class="bs-stepper-title">{{ trans('dashboard.general.public_data') }}</span>
				{{-- <span class="bs-stepper-subtitle">Setup Account Details</span> --}}
			</span>
		</button>
	</div>
	{{-- <div class="line">
		<i data-feather="chevron-right" class="font-medium-2"></i>
	</div> --}}
</div>

<div class="bs-stepper-content">
	<div id="step_locales" class="content">
		<div class="content-header">
			<h5 class="mb-0">{!! trans('dashboard.general.locales') !!}</h5>
		</div>
		<div class="row">
			@foreach (array_chunk(config('translatable.locales'),1) as $chunk)
			<div class="form-group col-md-6">
				@foreach ($chunk as $locale)
				<div class="form-group col-md-12">
					<label class="form-label" for="modern-{{ $locale }}">{{ trans('dashboard.'.$locale.'.name') }} <span class="text-danger">*</span></label>
					{!! Form::text($locale."[name]", isset($role) ? $role->translate($locale)->name : null, ['class' => 'form-control' , 'placeholder' => trans('dashboard.'.$locale.'.name'),'id' => "modern-{{ $locale }}"]) !!}
				</div>
				<div class="form-group col-md-12">
					<label class="form-label" for="modern-{{ $locale }}">{{ trans('dashboard.'.$locale.'.desc') }} </label>
					{!! Form::textarea($locale."[desc]", isset($role) ? $role->translate($locale)->desc : null, ['class' => 'form-control' , 'placeholder' => trans('dashboard.'.$locale.'.desc'),'id' => "modern-{{ $locale }}"]) !!}
				</div>
				@endforeach
			</div>
			@endforeach
		</div>
		<div class="d-flex justify-content-end">
			<a class="btn btn-primary btn-next">
				<span class="align-middle d-sm-inline-block d-none">{!! trans('dashboard.general.next') !!}</span>
				<i data-feather="arrow-right" class="align-middle ml-sm-25 ml-0"></i>
			</a>
		</div>
	</div>

	<div id="step_public_data" class="content">
		<div class="content-header">
			<h5 class="mb-0">{{ trans('dashboard.general.public_data') }}</h5>
		</div>
		<div class="row">
			<div class="form-group d-flex justify-content-center offset-md-6">
				<div class="custom-control custom-switch custom-switch-success mr-2 mb-1">
					<input type="checkbox" onclick="toggle(this)" class="custom-control-input" id="customSwitch_all" />

					<label class="custom-control-label" for="customSwitch_all">
						<span class="switch-icon-left"><i class="feather icon-check"></i></span>
						<span class="switch-icon-right"><i class="feather icon-x"></i></span>
					</label>
					<label class="font-medium-1 ml-1" for="customSwitch_all">{{ trans('dashboard.general.check_all') }}</label>
				</div>
			</div>
		</div>
			<div class="routes_div">
				@foreach ($routes as $route)
				@continue(in_array($route,$public_routes))
				<div class="form-group">
					<div class="row">
						<label class="font-medium-1 col-md-2">{{ $route == 'home' ? trans('dashboard.general.home') :trans('dashboard.'.$route.".".str_plural($route)) }} </label>
						<div class="col-md-10">
							<div class="row">
								<div class="custom-control custom-switch custom-switch-success mr-2 mb-1 col-md-3 {{ $route == 'dashboard' ? 'offset-md-6' : '' }}">
									{!! Form::checkbox("permissions[$loop->index][][route_name]", $route.".index", isset($role) && $role->permissions && $role->permissions->contains('route_name',$route.".index")? true :false , ['class' =>
									'custom-control-input permissions','id' => "customSwitch_".$loop->index. "_". $route."_read"]) !!}

									<label class="custom-control-label" for="customSwitch_{{ $loop->index }}_{{ $route }}_read">
										<span class="switch-icon-left"><i class="feather icon-check"></i></span>
										<span class="switch-icon-right"><i class="feather icon-x"></i></span>
									</label>
									<label class="font-medium-1 ml-1" for="customSwitch_{{ $loop->index }}_{{ $route }}_read">{{ trans('dashboard.general.read') }}</label>

								</div>
							@if ($route !='home')
								<div class="custom-control custom-switch custom-switch-success mr-2 mb-1 col-md-3">
									{!! Form::checkbox("permissions[$loop->index][][route_name]", $route.".store", isset($role) && $role->permissions && $role->permissions->contains('route_name',$route.".store")? true :false , ['class' =>
									'custom-control-input
									permissions','id' => "customSwitch_".$loop->index. "_". $route."_save"]) !!}

									<label class="custom-control-label" for="customSwitch_{{ $loop->index }}_{{ $route }}_save">
										<span class="switch-icon-left"><i class="feather icon-check"></i></span>
										<span class="switch-icon-right"><i class="feather icon-x"></i></span>
									</label>
									<label class="font-medium-1 ml-1" for="customSwitch_{{ $loop->index }}_{{ $route }}_save">{{ trans('dashboard.general.save') }}</label>
								</div>
								<div class="custom-control custom-switch custom-switch-success mr-2 mb-1 col-md-2">
									{!! Form::checkbox("permissions[$loop->index][][route_name]", $route.".update", isset($role) && $role->permissions && $role->permissions->contains('route_name',$route.".update")? true :false , ['class' =>
									'custom-control-input
									permissions','id' => "customSwitch_".$loop->index. "_". $route."_edit"]) !!}

									<label class="custom-control-label" for="customSwitch_{{ $loop->index }}_{{ $route }}_edit">
										<span class="switch-icon-left"><i class="feather icon-check"></i></span>
										<span class="switch-icon-right"><i class="feather icon-x"></i></span>
									</label>
									<label class="font-medium-1 ml-1" for="customSwitch_{{ $loop->index }}_{{ $route }}_edit">{{ trans('dashboard.general.edit') }}</label>
								</div>
								<div class="custom-control custom-switch custom-switch-success mr-2 mb-1 col-md-2">
									{!! Form::checkbox("permissions[$loop->index][][route_name]", $route.".destroy", isset($role) && $role->permissions && $role->permissions->contains('route_name',$route.".destroy")? true :false , ['class' =>
									'custom-control-input permissions','id' => "customSwitch_".$loop->index. "_". $route."_delete"]) !!}

									<label class="custom-control-label" for="customSwitch_{{ $loop->index }}_{{ $route }}_delete">
										<span class="switch-icon-left"><i class="feather icon-check"></i></span>
										<span class="switch-icon-right"><i class="feather icon-x"></i></span>
									</label>
									<label class="font-medium-1 ml-1" for="customSwitch_{{ $loop->index }}_{{ $route }}_delete">{{ trans('dashboard.general.delete') }}</label>
								</div>
							@endif
						</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		<div class="d-flex justify-content-between">
			<a class="btn btn-primary btn-prev">
				<i data-feather="arrow-left" class="align-middle mr-sm-25 mr-0"></i>
				<span class="align-middle d-sm-inline-block d-none">{!! trans('dashboard.general.previous') !!}</span>
			</a>
			<button class="btn btn-primary btn-next" type="submit">
				<span class="align-middle d-sm-inline-block d-none">{!! $btnSubmit !!}</span>
				<i data-feather="arrow-right" class="align-middle ml-sm-25 ml-0"></i>
			</button>
		</div>
	</div>
</div>

@section('vendor_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/forms/wizard/bs-stepper.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/forms/select/select2.min.css">
@endsection
@section('page_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/core/menu/menu-types/horizontal-menu.css">
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/plugins/forms/form-validation.css">
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/css/{{ LaravelLocalization::getCurrentLocaleDirection() }}/plugins/forms/form-wizard.css">
@endsection
@section('vendor_scripts')
<script src="{{ asset('dashboardAssets') }}/vendors/js/ui/jquery.sticky.js"></script>
<script src="{{ asset('dashboardAssets') }}/vendors/js/forms/wizard/bs-stepper.min.js"></script>
<script src="{{ asset('dashboardAssets') }}/vendors/js/forms/select/select2.full.min.js"></script>
<script src="{{ asset('dashboardAssets') }}/vendors/js/forms/validation/jquery.validate.min.js"></script>
@endsection
@section('page_scripts')
<script src="{{ asset('dashboardAssets') }}/js/scripts/forms/form-wizard.js"></script>
<script>
	function toggle(source) {
		checkboxes = document.getElementsByClassName('permissions');
		if (source.checked) {
			for (var i = 0, n = checkboxes.length; i < n; i++) {
				checkboxes[i].checked = source.checked;
			}
		} else {
			for (var i = 0, n = checkboxes.length; i < n; i++) {
				checkboxes[i].checked = source.checked;
			}
		}
	}
</script>
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
@endsection
