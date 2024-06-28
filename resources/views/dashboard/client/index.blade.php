@extends('dashboard.layout.layout')

@section('content')
    <!-- Basic table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">

                <div class="card border-info bg-transparent">
                    <table class="datatable-new-ajax table table-hover-animation">
                        <thead>
                            <tr>
                                <th>
                                    <div class="vs-checkbox-con vs-checkbox-primary justify-content-right">
                                        <input type="checkbox" class="select_all_rows" value="${data.id}" onclick="toggle(this)"/>
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                    </div>
                                </th>
                                <th>{!! trans('dashboard.general.image') !!}</th>
                                <th>{!! trans('dashboard.general.name') !!}</th>
                                <th>{!! trans('dashboard.general.email') !!}</th>
                                <th>{!! trans('dashboard.general.phone') !!}</th>
                                <th>{!! trans('dashboard.order.finished_order_count') !!}</th>
                                <th>{!! trans('dashboard.general.added_date') !!}</th>
                                <th><i data-feather='list'></i></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!--/ Basic table -->
    @include('dashboard.layout.delete_modal')
    @include('dashboard.layout.notify_modal')
@endsection
@include('dashboard.client.styles')
@include('dashboard.client.scripts')
