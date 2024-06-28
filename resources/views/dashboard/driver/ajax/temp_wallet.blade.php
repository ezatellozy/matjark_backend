<!-- Basic modal -->
<div class="modal fade text-left" id="modal_temp_balance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success white">
                <h5 class="modal-title">{{ trans('dashboard.user.add_wallet') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="item" user-type="">


                <div class="row form-group">
                    <label class="control-label col-lg-2">{{ trans('dashboard.user.amount') }}</label>
                    <div class="col-lg-10">
                        {!! Form::text("amount", null, ['class'=>"form-control",'placeholder'=>trans('dashboard.user.amount')]) !!}
                    </div>
                </div>

                <div class="row form-group">
                    <label class="font-medium-1 col-md-2">{{ trans('dashboard.user.date.start_balance') }} </label>
                    <div class="col-md-4">
                        {!! Form::text("start_at", null , ['class' => 'form-control expire_date' , 'placeholder' => trans('dashboard.user.date.start_balance')])
                        !!}
                    </div>
                    <label class="font-medium-1 col-md-2">{{ trans('dashboard.user.date.end_balance') }} </label>
                    <div class="col-md-4">
                        {!! Form::text("end_at", null , ['class' => 'form-control expire_date' , 'placeholder' => trans('dashboard.user.date.end_balance')]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('dashboard.general.cancel') }}</button>
                <a class="btn btn-success btn-print mb-1 mb-md-0 ml-3 text-white" onclick="saveTempWalletAmount()"><i class="feather icon-plus" title="{{ trans('dashboard.general.add') }}"></i> {{ trans('dashboard.general.add') }}</a>
            </div>
        </div>
    </div>
</div>

<!-- /basic modal -->
