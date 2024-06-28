<span class="text-success span_driver_type_{{ $driver->id }}">{{ trans('dashboard.driver.driver_types.'.optional($driver->driver)->driver_type) }}</span>

<div class="mb-1 justify-content-center">
    <div class="btn-group" role="group" aria-label="Basic example">
        <button onclick="changeDriverType('{{ $driver->id }}','both')" class="btn btn-sm btn-primary font-small-3 text-bold-600 both_btn_{{ $driver->id }}">{{ trans('dashboard.driver.driver_types.both') }}</button>
        <button onclick="changeDriverType('{{ $driver->id }}','delivery')" class="btn btn-sm btn-success font-small-3 text-bold-600 delivery_btn_{{ $driver->id }}">{{ trans('dashboard.driver.driver_types.delivery') }}</button>
        <button onclick="changeDriverType('{{ $driver->id }}','ride')" class="btn btn-sm btn-info font-small-3 text-bold-600 ride_btn_{{ $driver->id }}">{{ trans('dashboard.driver.driver_types.ride') }}</button>

    </div>
</div>
