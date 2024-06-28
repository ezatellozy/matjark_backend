
@forelse ($collection as $result)
    <li class="auto-suggestion d-flex align-items-center cursor-pointer">
        <a class="d-flex align-items-center justify-content-between py-50 w-100" href="{{ LaravelLocalization::localizeUrl('dashboard/search?search='.($result->name ? $result->name : $result->phone)) }}">
            <div class="d-flex align-items-center">
                <div class="avatar mr-50"><img src="{{ $result->image ?? asset('dashboardAssets/images/cover/cover_sm.png') }}" alt="{{ $result->name }}" style="width:32px; height:32px;"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">{{ $result->name ? $result->name : $result->phone}}</p>
                    <small class="text-muted">{{ $result->email ?? str_limit($result->desc,10) }}</small>
                </div>
            </div>
        </a>
    </li>
@empty
    <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start">
                <span class="mr-75 feather icon-alert-circle"></span>
                <span>{{ trans('dashboard.messages.no_search_result') }}.</span>
            </div>
        </a>
    </li>
@endforelse
