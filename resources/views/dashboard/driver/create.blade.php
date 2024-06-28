@extends('dashboard.layout.layout')

@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {!! trans('dashboard.driver.add_driver') !!}
                        </h4>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'dashboard.driver.store' , 'method' => 'POST' , 'files' => true ]) !!}
                           @include('dashboard.driver.form',['btnSubmit' => trans('dashboard.general.save'),'current' => trans('dashboard.driver.add_driver')])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
