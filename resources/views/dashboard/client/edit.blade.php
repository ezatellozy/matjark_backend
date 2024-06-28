@extends('dashboard.layout.layout')

@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {!! trans('dashboard.client.edit_client') !!}
                        </h4>
                    </div>
                    <div class="card-body">
                        {!! Form::model($client,['route' => ['dashboard.client.update',$client->id] , 'method' => 'PUT' , 'files' => true ]) !!}
                           @include('dashboard.client.form',['btnSubmit' => trans('dashboard.general.edit'),'current' => trans('dashboard.client.edit_client')])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
