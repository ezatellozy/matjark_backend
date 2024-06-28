@extends('dashboard.layout.layout')

@section('content')

<!-- Search field -->
<div class="card border-info">
    <div class="card-body">
        <section id="search-bar">
            {{-- <div class="search-bar"> --}}
                <form action="{{ url('dashboard/search') }}" method="GET">
                    <div class="col-12 mb-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon-search1"><i class="icofont-search"></i></span>
                                    </div>
                                </div>

                                {!! Form::text('search', null , ['class' => 'form-control' ,'aria-describedby' => "button-addon2" , 'placeholder' => trans('dashboard.general.search') , 'autocomplete' => 'off',"aria-describedby" => "basic-addon-search1"]) !!}

                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="submit">{{ trans('dashboard.general.search') }}</button>
                                </div>
                            </div>
                        </div>
                    {{-- <ul class="search-list search-list-main search_section_page"></ul> --}}
                </form>
            {{-- </div> --}}
        </section>
    </div>
</div>


<div class="card border-info">
    <div class="card-header">
        <div class="alert alert-info alert-styled-left alert-dismissible p-2" style="width: 100%;">
            <span class="font-weight-semibold"></span>
            {{ trans('dashboard.general.search_result_about' , ['query' => $keyword , 'count' => $total_count]) }} .
        </div>
        @if (!$total_count)
            {{-- <div class="search-results-list d-flex justify-content-center"> --}}
                <h4 class="text-center" style="width: 100%;">
                    {{ trans('dashboard.messages.no_search_result') }}
                </h4>
            {{-- </div> --}}
        @else
            <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                @if ($clients->count())
                    <li class="nav-item">
                        <a href="#clients" class="nav-link legitRipple {{  $search_type == 'client' ? 'show active' : ''}}" data-toggle="tab">
                            {{ trans('dashboard.client.clients') }}<span class="badge badge-success badge-pill ml-2">{{ $clients_count }}</span>
                        </a>
                    </li>
                    @endif
                    @if ($admins->count())
                        <li class="nav-item">
                            <a href="#admins" class="nav-link legitRipple {{  $search_type == 'admin' ? 'show active' : ''}}" data-toggle="tab">
                                {{ trans('dashboard.admin.admins') }}
                                <span class="badge badge-success badge-pill ml-2">{{ $admins_count }}</span>
                            </a>
                        </li>
                        @endif
                    @if ($drivers->count())
                    <li class="nav-item">
                        <a href="#drivers" class="nav-link legitRipple {{  $search_type == 'driver' ? 'show active' : ''}}" data-toggle="tab">
                            {{ trans('dashboard.driver.drivers') }}
                            <span class="badge badge-success badge-pill ml-2">{{ $drivers_count }}</span>
                        </a>
                    </li>
                    @endif
                 

                </ul>
                    @endif
    </div>
    <div class="card-body">
        <div class="tab-content">
            @if ($clients->count())
            <div class="tab-pane fade {{ $search_type == 'client' ? 'show active' : ''}}" id="clients">
                <div class="row mt-4">
                    @foreach ($clients as $client)
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card text-white bg-transparent border-info text-center">
                            <div class="card-content d-flex">
                                <div class="card-body">
                                    <img src="{{ $client->image }}" style="height:100px; width:100px;" class="float-left img-thumbnail mt-2 ml-2">
                                    <h4 class="card-title text-white mt-3">{{ $client->fullname }}</h4>
                                    <p class="card-text">{!! trans('dashboard.order.order_count') !!} : {{ $client->clientOrders->count() }}</p>
                                    <a href="{{ route('dashboard.client.edit',$client->id) }}" class="font-medium-2 text-bold btn btn-success mt-1 waves-effect waves-light"><i class="icofont-edit"></i></a>
                                    <a href="{{ route('dashboard.client.show',$client->id) }}" class="font-medium-2 text-bold btn btn-info mt-1 waves-effect waves-light"><i class="icofont-maximize"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center">
                    {!! $clients->appends(['search' => $keyword])->links() !!}
                </div>

            </div>
            @endif

            @if ($admins->count())
            <div class="tab-pane fade {{  $search_type == 'admin' ? 'show active' : ''}}" id="admins">
                <div class="row mt-4">
                    @foreach ($admins as $admin)
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card text-white bg-transparent border-info text-center">
                            <div class="card-content d-flex">
                                <div class="card-body">
                                    <img src="{{ $admin->image }}" style="height:100px; width:100px;" class="float-left img-thumbnail mt-2 ml-2">
                                    <h4 class="card-title text-white mt-3">{{ $admin->fullname }}</h4>
                                    <p class="card-text"><i class="feather icon-mail"></i> {{ $admin->email }}</p>
                                    <a href="{{ route('dashboard.manager.edit',$admin->id) }}" class="font-medium-2 text-bold btn btn-success mt-1 waves-effect waves-light"><i class="icofont-edit"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center">
                    {!! $admins->appends(['search' => $keyword])->links() !!}

                </div>
            </div>
            @endif

            @if ($drivers->count())
            <div class="tab-pane fade {{  $search_type == 'driver' ? 'show active' : ''}}" id="drivers">
                <div class="row mt-4">
                    @foreach ($drivers as $driver)
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card text-white bg-transparent border-info text-center">
                            <div class="card-content d-flex">
                                <div class="card-body">
                                    <img src="{{ $driver->image }}" style="height:100px; width:100px;" class="float-left img-thumbnail mt-2 ml-2">
                                    <h4 class="card-title text-white mt-3">{{ $driver->fullname }}</h4>
                                    <p class="card-text">{!! trans('dashboard.order.order_count') !!} : {{ $driver->driverOrders->count() }}</p>
                                    <a href="{{ route('dashboard.driver.edit',$driver->id) }}" class="font-medium-2 text-bold btn btn-success mt-1 waves-effect waves-light"><i class="icofont-edit"></i></a>
                                    <a href="{{ route('dashboard.driver.show',$driver->id) }}" class="font-medium-2 text-bold btn btn-info mt-1 waves-effect waves-light"><i class="icofont-maximize"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center">
                    {!! $drivers->appends(['search' => $keyword])->links() !!}
                </div>
            </div>
            @endif

           

        
        


            {{-- <div class="text-center" style="display:none">
                <p class="text-center"><i class="icon-spinner9 spinner"></i></p>
            </div> --}}
        </div>
    </div>
</div>
<!-- /search field -->
@endsection
@include('dashboard.search.scripts')
