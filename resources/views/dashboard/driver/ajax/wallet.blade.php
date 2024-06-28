<!-- Basic modal -->
<div class="modal fade text-left" id="modal_wallet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success white">
        <h5 class="modal-title">@lang('dashboard.user.add_wallet')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="item" user-type="">
          <div class="form-group row">
            <label class="control-label col-lg-2">@lang('dashboard.user.amount')</label>
            <div class="col-lg-10">
              {!! Form::text("amount", null, ['class'=>"form-control",'placeholder'=>trans('dashboard.user.amount')]) !!}
            </div>
          </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-dismiss="modal">@lang('dashboard.general.cancel') </button>
        <a class="btn btn-success btn-print mb-1 mb-md-0 ml-3 text-white" onclick="saveWalletAmount()"><i class="feather icon-plus" title="{{ trans('dashboard.general.add') }}"></i> @lang('dashboard.general.add')</a>
      </div>
    </div>
  </div>
</div>

<!-- /basic modal -->
