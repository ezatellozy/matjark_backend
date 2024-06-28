@extends('dashboard.layout.layout')

@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {!! trans('dashboard.manager.edit_manager') !!}
                        </h4>
                    </div>
                    <div class="card-body">
                        {!! Form::model($manager,['route' => ['dashboard.manager.update',$manager->id] , 'method' => 'PUT' , 'files' => true ]) !!}
                           @include('dashboard.manager.form',['btnSubmit' => trans('dashboard.general.edit'),'current' => trans('dashboard.manager.edit_manager')])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
