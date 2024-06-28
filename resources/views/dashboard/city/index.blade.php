@extends('dashboard.layout.layout')

@section('content')
    <div class="card border-info bg-transparent">

        <div class="card-body">
            <div class="d-flex justify-content-center">
                {!! $cities->links() !!}
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-custom table table-hover-animation"
                    data-title="{{ trans('dashboard.city.cities') }}"
                    data-create_title="{{ trans('dashboard.city.add_city') }}"
                    data-create_link="{{ route('dashboard.city.create') }}">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>{!! trans('dashboard.city.city') !!}</th>
                            <th>{!! trans('dashboard.country.country') !!}</th>
                            <th>{!! trans('dashboard.general.added_date') !!}</th>
                            <th>{!! trans('dashboard.general.control') !!}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($cities as $city)
                            <tr class="{{ $city->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $city->name }}</td>
                                <td>{{ $city->country->name }}</td>
                                <td>
                                    <div class="badge badge-primary badge-md mr-1 mb-1">
                                        {{ $city->created_at->format('Y-m-d') }}</div>
                                </td>
                                <td class="justify-content-center">
                                    <a onclick="deleteItem('{{ $city->id }}' , '{{ route('dashboard.city.destroy', $city->id) }}')"
                                        class="text-danger" title="{!! trans('dashboard.general.delete') !!}">
                                        <i class="fas fa-trash font-medium-3 "></i>
                                    </a>
                                    <a href="{!! route('dashboard.city.edit', $city->id) !!}" class="text-primary mr-2"
                                        title="{!! trans('dashboard.general.edit') !!}">
                                        <i class="far fa-edit font-medium-3"></i> </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {!! $cities->links() !!}
            </div>
        </div>
    </div>
    @include('dashboard.layout.delete_modal')
@endsection


@include('dashboard.city.scripts')
