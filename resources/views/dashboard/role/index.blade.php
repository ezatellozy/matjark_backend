@extends('dashboard.layout.layout')

@section('content')
<div class="card invoice-list-wrapper border-info bg-transparent">
    <div class="card-body">
        <div class="d-flex justify-content-center">
            {!! $roles->links() !!}
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-custom table table-hover-animation" data-title="{{ trans('dashboard.role.roles') }}" data-create_title="{{ trans('dashboard.role.add_role') }}" data-create_link="{{ route('dashboard.role.create') }}">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        @foreach (config('translatable.locales') as $locale)
                        <th>{!! trans('dashboard.'.$locale.'.name') !!}</th>
                        @endforeach
                        <th>{!! trans('dashboard.role.manager_count') !!}</th>
                        <th>{!! trans('dashboard.general.added_date') !!}</th>
                        <th>{!! trans('dashboard.general.control') !!}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr class="{{ $role->id }} text-center">
                            <td>{{ $loop->iteration }}</td>
                            @foreach (config('translatable.locales') as $locale)
                            <td>{{ $role->translate($locale)->name }}</td>
                            @endforeach
                            <td>{{ $role->users->count() }}</td>
                            <td>
                                <div class="badge badge-primary badge-md mr-1 mb-1">
                                    {{ $role->created_at->format("Y-m-d") }}
                                </div>
                            </td>
                            <td class="justify-content-center">
                                <a onclick="deleteItem('{{ $role->id }}' , '{{ route('dashboard.role.destroy',$role->id) }}')" class="text-danger" title="{!! trans('dashboard.general.delete') !!}">
                                    <i data-feather='trash-2' class="font-medium-3"></i>
                                </a>
                                <a href="{!! route('dashboard.role.edit',$role->id) !!}" class="text-primary mr-2">
                                    <i data-feather='edit' title="{!! trans('dashboard.general.edit') !!}" class="font-medium-3"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {!! $roles->links() !!}
        </div>
    </div>
</div>
@include('dashboard.layout.delete_modal')
@endsection
@include('dashboard.role.scripts')
