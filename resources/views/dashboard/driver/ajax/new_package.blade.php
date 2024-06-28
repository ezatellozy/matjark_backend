<div class="col-md-6 col-sm-12 profile-card-2 ">
    <div class="card border-info" style="height: 272.188px;" id="package_subscribe_{{ $sub_package->id }}">

        <div class="card-header mx-auto pb-0">
            <div class="row m-0">
                <div class="col-sm-12 text-center">
                    <h4>{{ $sub_package->name }}</h4>
                </div>
                <div class="col-sm-12 text-center">
                    <p class="">{{ @$sub_package->package_data_array['package_price'] }} {{ trans('dashboard.currency.rs') }}</p>
                </div>
            </div>
        </div>
        <div class="card-content">
            <div class="card-body text-center mx-auto">

                <div class="d-flex justify-content-between mt-2">
                    <div class="uploads">
                        <p class="font-weight-bold font-medium-1 mb-0">{{ optional($sub_package->subscribed_at)->format("Y-m-d") }}</p>
                        <span class="">{!! trans('dashboard.package.subscribed_at') !!}</span>
                    </div>
                    <div class="followers">
                        <p class="font-weight-bold font-medium-1 mb-0 package_{{ $sub_package->id }}">{{ optional($sub_package->end_at)->format("Y-m-d") }}</p>
                        <span class="">{{ trans('dashboard.package.end_at') }}</span>
                    </div>
                    <div class="following">
                        <p class="font-weight-bold font-medium-1 mb-0 {{ $sub_package->paid_status_css }} paid_status_css_{{ $sub_package->id }}">{{ trans('dashboard.package.paid_statuses.'.$sub_package->is_paid) }}</p>
                        <span class="">{!! trans('dashboard.package.paid_status') !!}</span>
                    </div>
                </div>
                <div class="badge badge-lg block {{ $sub_package->subscribe_status_css }} status_{{ $sub_package->id }} mt-3">
                    <span>
                        {{ $sub_package->subscribe_status }}
                    </span>
                </div>

            </div>
        </div>
    </div>
</div>
