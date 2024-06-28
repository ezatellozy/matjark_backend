
<div class="card-header mx-auto pb-0">
    <div class="row m-0">
        <div class="col-sm-12 text-center">
            <h4>{{ $package->name }}</h4>
        </div>
        <div class="col-sm-12 text-center">
            <p class="">{{ @$package->package_data_array['package_price'] }} {{ trans('dashboard.currency.rs') }}</p>
        </div>
    </div>
</div>
<div class="card-content">
    <div class="card-body text-center mx-auto">

        <div class="d-flex justify-content-between mt-2">
            <div class="uploads">
                <p class="font-weight-bold font-medium-1 mb-0">{{ optional($package->subscribed_at)->format("Y-m-d") }}</p>
                <span class="">{!! trans('dashboard.package.subscribed_at') !!}</span>
            </div>
            <div class="followers">
                <p class="font-weight-bold font-medium-1 mb-0 package_{{ $package->id }}">{{ optional($package->end_at)->format("Y-m-d") }}</p>
                <span class="">{{ trans('dashboard.package.end_at') }}</span>
            </div>
            <div class="following">
                <p class="font-weight-bold font-medium-1 mb-0 {{ $package->paid_status_css }} paid_status_css_{{ $package->id }}">{{ trans('dashboard.package.paid_statuses_modal.'.($package->is_paid ? 'paid' : 'not_paid')) }}</p>
                <span class="">{!! trans('dashboard.package.paid_status') !!}</span>
            </div>
        </div>
        <div class="badge badge-lg block {{ $package->subscribe_status_css }} status_{{ $package->id }} mt-3">
            <span>
                {{ $package->subscribe_status }}
            </span>
        </div>

    </div>
</div>
