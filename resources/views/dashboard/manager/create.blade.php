@extends('dashboard.layout.layout')

@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {!! trans('dashboard.manager.add_manager') !!}
                        </h4>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'dashboard.manager.store' , 'method' => 'POST' , 'files' => true ]) !!}
                           @include('dashboard.manager.form',['btnSubmit' => trans('dashboard.general.save'),'current' => trans('dashboard.manager.add_manager')])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
