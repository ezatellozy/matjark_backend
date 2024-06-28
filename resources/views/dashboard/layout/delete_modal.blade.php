<!-- Basic modal -->
<div class="modal fade text-left" id="modal_default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger white">
        <h5 class="modal-title" id="myModalLabel120">{{ trans('dashboard.general.del_sure') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>

        <div class="modal-body" id="item" item-id="">
          <p>{{ trans('dashboard.general.del_sure_pharse') }} </p>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('dashboard.general.cancel') }} </button>
          <button type="button" class="btn btn-danger" onclick="deleteModal();">{{ trans('dashboard.general.del_sure') }}</button>
        </div>
    </div>
  </div>
</div>

<!-- /basic modal -->
