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
					{!! Form::text($locale."[name]", isset($country) ? $country->translate($locale)->name : null, ['class' => 'form-control' , 'placeholder' => trans('dashboard.'.$locale.'.name'),'id' => "modern-{{ $locale }}"]) !!}
				</div>
				<div class="form-group col-md-12">
					<label class="form-label" for="modern-{{ $locale }}">{{ trans('dashboard.'.$locale.'.nationality') }} </label>
					{!! Form::text($locale."[nationality]", isset($country) ? $country->translate($locale)->nationality : null, ['class' => 'form-control' , 'placeholder' => trans('dashboard.'.$locale.'.nationality'),'id' => "modern-{{ $locale }}"]) !!}
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

		<div class="col-12">
			<div class="form-group">
				<label>{{ trans('dashboard.country.key') }}
					<span class="text-danger">*</span>
				</label>
				{!! Form::text("phonecode", null, ['class' => 'form-control' , 'placeholder' => trans('dashboard.country.key')]) !!}
			</div>
		</div>

		<div class="row form-group col-12">
			<label class="form-label" for="modern-image">
				{{ trans('dashboard.general.image') }}
			</label>
			<div class="col-md-10">
					<div class="custom-file">
					<input type="file" name="image" class="custom-file-input" id="country_image" onchange="readUrl(this)">
					<label class="custom-file-label" for="country_image">Choose file</label>
				</div>
			</div>
			<div class="col-md-1">
				@if (isset($country))
				<img src="{{ $country->image }}" class="img-thumbnail image-preview" style="width: 100%; height: 100px;">
				@else
				<img src="{{ asset('dashboardAssets/images/backgrounds/placeholder_image.png') }}" class="img-thumbnail image-preview" style="width: 100%; height: 100px;">
				@endif
			</div>
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
