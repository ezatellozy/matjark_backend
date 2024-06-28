@extends('dashboard.layout.layout')

@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {!! trans('dashboard.driver.edit_driver') !!}
                        </h4>
                    </div>
                    <div class="card-body">
                        {!! Form::model($driver,['route' => ['dashboard.driver.update',$driver->id] , 'method' => 'PUT' , 'files' => true ]) !!}
                           @include('dashboard.driver.form',['btnSubmit' => trans('dashboard.general.edit'),'current' => trans('dashboard.driver.edit_driver')])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
