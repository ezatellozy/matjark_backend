{{-- <div class="custom-control toggle-switch custom-switch custom-switch-success mr-2 mb-1">
    <input id="check_active_{{ $driver->id }}" {{ optional($driver->driver)->is_admin_accept ? 'checked' : '' }} class="custom-control-input" onchange="toggleAdminAccept('{{ $driver->id }}')" type="checkbox">
    <label for="check_active_{{ $driver->id }}"></label>
    <label class="custom-control-label" for="check_active_{{ $driver->id }}">
        <span class="switch-icon-left"><i class="feather icon-check"></i></span>
        <span class="switch-icon-right"><i class="feather icon-x"></i></span>
    </label>
</div> --}}
<span class="{{ optional($driver->driver)->is_admin_accept ? 'text-success' : 'text-danger' }} span_driver_{{ $driver->id }}">{{ optional($driver->driver)->is_admin_accept ? trans('dashboard.driver.admin_accept') : trans('dashboard.driver.admin_refuse') }}</span>

<div class="mb-1 justify-content-center">
    <div class="btn-group" role="group" aria-label="Basic example">
        <button onclick="toggleAdminAccept('{{ $driver->id }}')" class="btn btn-sm btn-success font-small-3 text-bold-600 accept_btn_{{ $driver->id }}">{{ trans('dashboard.driver.accept_data') }}</button>
        <button onclick="openRefuseReasonModal('{{ $driver->id }}')" class="btn btn-sm btn-danger font-small-3 text-bold-600 refuse_btn_{{ $driver->id }}">{{ trans('dashboard.driver.refuse_data') }}</button>
    </div>
</div>
