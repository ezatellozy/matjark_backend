@if(optional($driver->car)->exists())
<a href="{!! route('dashboard.car.show',$driver->car->id) !!}">{{ optional(@$driver->car->carModel)->name }} - {{ optional(@$driver->car->brand)->name }}</a></td>
@endif
