@extends('dashboard.layout.layout')

@section('content')
<div class="card invoice-list-wrapper border-info bg-transparent">

    <div class="card-body">
        <div class="d-flex justify-content-center">
            {!! $managers->links() !!}
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-custom table table-hover-animation" data-title="{{ trans('dashboard.manager.managers') }}" data-create_title="{{ trans('dashboard.manager.add_manager') }}" data-create_link="{{ route('dashboard.manager.create') }}">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>{!! trans('dashboard.general.image') !!}</th>
                        <th>{!! trans('dashboard.general.name') !!}</th>
                        <th>{!! trans('dashboard.general.email') !!}</th>
                        <th>{!! trans('dashboard.role.role') !!}</th>
                        <th>{!! trans('dashboard.general.added_date') !!}</th>
                        <th>{!! trans('dashboard.general.control') !!}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($managers as $manager)
                        <tr class="{{ $manager->id }} text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td class="product-img sorting_1">
                                <a href="{{ $manager->avatar }}" data-fancybox="gallery">
                                    <div class="avatar">
                                        <img src="{{ $manager->avatar }}" alt="" style="width:60px; height:60px;" class="img-thumbnail rounded">
                                    <span class="avatar-status-busy avatar-status-md" id="online_{{ $manager->id }}"></span>
                                </div>
                                </a>
                            </td>
                            <td>{{ $manager->fullname }}</td>
                            <td>{{ $manager->email }}</td>
                            <td>{{ optional($manager->role)->name }}</td>
                            <td><div class="badge badge-primary badge-md mr-1 mb-1">{{ $manager->created_at->format("Y-m-d") }}</div> </td>
                            <td class="justify-content-center">
                                <a onclick="deleteItem('{{ $manager->id }}','{{ route('dashboard.manager.destroy',$manager->id) }}')" class="text-danger" title="{!! trans('dashboard.general.delete') !!}">
                                    <i data-feather='trash-2' class="font-medium-3"></i>
                                </a>
                                <a href="{!! route('dashboard.manager.edit',$manager->id) !!}" class="text-primary mr-2" title="{!! trans('dashboard.general.edit') !!}">
                                    <i data-feather='edit' class="font-medium-3"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {!! $managers->links() !!}
        </div>
    </div>
</div>
@include('dashboard.layout.delete_modal')
@endsection


@include('dashboard.manager.scripts')
