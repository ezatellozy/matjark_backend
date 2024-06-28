@extends('dashboard.layout.layout')

@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {!! trans('dashboard.client.add_client') !!}
                        </h4>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'dashboard.client.store' , 'method' => 'POST' , 'files' => true ]) !!}
                           @include('dashboard.client.form',['btnSubmit' => trans('dashboard.general.save'),'current' => trans('dashboard.client.add_client')])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
