<!-- Basic modal -->
<div class="modal fade text-left" id="modal_notify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success white">
        <h5 class="modal-title">@lang('dashboard.notification.notification')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="item" item-id="" route="" user-type="">
          {{-- <div class="form-group row">
              <label class="control-label col-lg-2">@lang('dashboard.setting.sending_type')</label>
              <div class="vs-radio-con vs-radio-success col-md-5">
                  {!! Form::radio('send_type', "fcm" ,'checked') !!}
                  <span class="vs-radio">
                      <span class="vs-radio--border"></span>
                      <span class="vs-radio--circle"></span>
                  </span>
                  <span class="">{{ trans('dashboard.setting.FCM') }}</span>

              </div>
              <div class="vs-radio-con vs-radio-success">
                  {!! Form::radio('send_type', "sms") !!}
                  <span class="vs-radio">
                      <span class="vs-radio--border"></span>
                      <span class="vs-radio--circle"></span>
                  </span>
                  <span class="">{{ trans('dashboard.setting.SMS') }}</span>
              </div>
          </div> --}}
          
          {!! Form::hidden('status', request('status')) !!}

          <div class="form-group row">
            <label class="control-label col-lg-2">@lang('dashboard.chat.type_title')</label>
            <div class="col-lg-10">
              {!! Form::text("title", null, ['class'=>"form-control",'placeholder'=>trans('dashboard.chat.type_title')]) !!}
            </div>
          </div>
          <div class="form-group row">
            <label class="control-label col-lg-2">@lang('dashboard.chat.type_message')</label>
            <div class="col-lg-10">
              {!! Form::textarea("body", null, ['class'=>"form-control",'rows' => 4,'placeholder'=>trans('dashboard.chat.type_message')]) !!}
            </div>
          </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-dismiss="modal">@lang('dashboard.general.cancel') </button>
        <a class="btn btn-success btn-print mb-1 mb-md-0 ml-3 text-white" onclick="editModal()"><b><i class="feather icon-bell mr-2" title="{{ trans('dashboard.general.send') }}"></i> </b>@lang('dashboard.general.send')</a>
      </div>
    </div>
  </div>
</div>

<!-- /basic modal -->
