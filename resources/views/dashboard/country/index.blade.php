@extends('dashboard.layout.layout')

@section('content')
<div class="card border-info bg-transparent">

    <div class="card-body">
        <div class="d-flex justify-content-center">
            {!! $countries->links() !!}
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-custom table table-hover-animation" data-title="{{ trans('dashboard.country.countries') }}" data-create_title="{{ trans('dashboard.country.add_country') }}" data-create_link="{{ route('dashboard.country.create') }}">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>{!! trans('dashboard.general.image') !!}</th>
                        <th>{!! trans('dashboard.country.country') !!}</th>
                        <th>{!! trans('dashboard.country.nationality') !!}</th>
                        <th>{!! trans('dashboard.country.key') !!}</th>
                        <th>{!! trans('dashboard.city.city_count') !!}</th>
                        <th>{!! trans('dashboard.general.added_date') !!}</th>
                        <th>{!! trans('dashboard.general.control') !!}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($countries as $country)
                    <tr class="{{ $country->id }} text-center">
                        <td>{{ $loop->iteration }}</td>
                        <td class="product-img sorting_1">
                            <a href="{{ $country->image }}" data-fancybox="gallery">
                                <img src="{{ $country->image }}" alt="" style="width:60px; height:60px;" class="img-preview rounded">
                            </a>
                        </td>
                        <td>{{ $country->name }}</td>
                        <td>{{ $country->nationality }}</td>
                        <td>{{ $country->phonecode }}</td>
                        <td>{{ $country->cities->count() }}</td>
                        <td>
                            <div class="badge badge-primary badge-md mr-1 mb-1">{{ $country->created_at->format("Y-m-d") }}</div>
                        </td>
                        <td class="justify-content-center">
                            <a onclick="deleteItem('{{ $country->id }}' , '{{ route('dashboard.country.destroy',$country->id) }}')" class="text-danger" title="{!! trans('dashboard.general.delete') !!}">
                                <i class="fas fa-trash font-medium-3 "></i>
                            </a>
                            <a href="{!! route('dashboard.country.edit',$country->id) !!}" class="text-primary mr-2" title="{!! trans('dashboard.general.edit') !!}">
                                <i  class="far fa-edit font-medium-3"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {!! $countries->links() !!}
        </div>
    </div>
</div>
@include('dashboard.layout.delete_modal')
@endsection


@include('dashboard.country.scripts')
