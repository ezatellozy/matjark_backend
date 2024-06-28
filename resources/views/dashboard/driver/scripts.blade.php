@section('vendor_scripts')
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/datatables.checkboxes.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/jszip.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/pdfmake.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/vfs_fonts.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/buttons.html5.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/buttons.print.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/tables/datatable/dataTables.rowGroup.min.js"></script>
    <script src="{{ asset('dashboardAssets') }}/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
@endsection

@section('page_scripts')
    {{-- <script src="{{ asset('dashboardAssets') }}/js/scripts/tables/table-datatables-basic.js"></script> --}}

    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

    <script>
        $(function() {
            getData();
         });

        function getData() {
            $(".datatable-new-ajax").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.driver.index') }}?"+$.param(@json(request()->query())),
                    type: 'GET',
                    dataSrc: 'data'
                },
                columnDefs: [
                      {
                         orderable: false,
                         targets: 0,
                         checkboxes: {
                            selectRow: true
                         }
                      }
                  ],
                columns: [{
                    data: function (data) {
                        return  `<div class="vs-checkbox-con vs-checkbox-primary justify-content-center"><input type="checkbox" class="check_list" value="${data.id}" name="driver_list[]"/><span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span></div>`;
                    }
                },
                {
                    class : "product-img sorting_1",
                    data: function(info) {
                        return `<a href="${ info.avatar }" data-fancybox="gallery">
                        <div class="avatar">
                        <img src="${ info.avatar }" alt="" style="width:60px; height:60px;" class="img-thumbnail rounded">
                        <span class="avatar-status-busy avatar-status-md" id="online_${ info.id }"></span>
                        </div>
                        </a>`;
                    }
                },
                {
                    class: 'text-center',
                    data: "fullname"
                },
                {
                    class: 'text-center',
                    data: "email"
                },
                {
                    class: 'text-center',
                    data: "phone"
                },
                {
                    class: 'text-center',
                    data: function(info) {
                        return `<div class="badge badge-success font-medium-1 badge-md mr-1 mb-1">${info.finished_order_count}</div>`;
                    }
                },
                {
                    class: 'text-center',
                    data: function(info) {
                        return `<div class="badge badge-primary display-1 badge-md mr-1 mb-1">${info.created_at}</div>`;
                    }
                },
                {
                    class : "text-center font-weight-bolder",
                    data: function(data) {
                        return `<a href="${data.edit_link}" class="text-success" title="{!! trans('dashboard.general.edit') !!}"><i data-feather='edit' class="font-medium-3"></i></a>
                        <a href="${data.show_link}" class="text-info" title="{!! trans('dashboard.general.show') !!}"><i data-feather='monitor' class="font-medium-3"></i></a>
                        <a onclick="deleteItem('${data.id}' , '${data.destroy_link}')" class="text-danger" title="{!! trans('dashboard.general.delete') !!}">
                        <i data-feather='trash-2' class="font-medium-3"></i>
                        </a><a class="text-success" onclick="notify('${data.id}','${data.notify_link}','driver')" title="{!! trans('dashboard.general.notify') !!}">
                        <i data-feather='bell' class="font-medium-3"></i>
                        </a>`;
                    }
                }
                ],
                dom:
                  '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-right"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                buttons: [
                  {
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle mr-2',
                    text: feather.icons['share'].toSvg({ class: 'font-small-4 mr-50' }) + 'Export',
                    buttons: [
                      {
                        extend: 'print',
                        text: feather.icons['printer'].toSvg({ class: 'font-small-4 mr-50' }) + 'Print',
                        className: 'dropdown-item',
                        exportOptions: { columns: ':not(:last-child)' }
                      },
                      {
                        extend: 'csv',
                        text: feather.icons['file-text'].toSvg({ class: 'font-small-4 mr-50' }) + 'Csv',
                        className: 'dropdown-item',
                        exportOptions: { columns: ':not(:last-child)' }
                      },
                      {
                        extend: 'excel',
                        text: feather.icons['file'].toSvg({ class: 'font-small-4 mr-50' }) + 'Excel',
                        className: 'dropdown-item',
                        exportOptions: { columns: ':not(:last-child)' }
                      },
                      {
                        extend: 'pdf',
                        text: feather.icons['clipboard'].toSvg({ class: 'font-small-4 mr-50' }) + 'Pdf',
                        className: 'dropdown-item',
                        exportOptions: { columns: ':not(:last-child)' }
                      },
                      {
                        extend: 'copy',
                        text: feather.icons['copy'].toSvg({ class: 'font-small-4 mr-50' }) + 'Copy',
                        className: 'dropdown-item',
                        exportOptions: { columns: ':not(:last-child)' }
                      }
                    ],
                    init: function (api, node, config) {
                      $(node).removeClass('btn-secondary');
                      $(node).parent().removeClass('btn-group');
                      setTimeout(function () {
                        $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex');
                      }, 50);
                    }
                    },
                    {
                      text: feather.icons['plus'].toSvg({ class: 'mr-1 font-small-2' }) + '{{ trans('dashboard.driver.add_driver') }}',
                      className: 'create-new btn btn-primary',
                      attr: {
                        'onclick': 'location.href="{{ route('dashboard.driver.create') }}"',
                      },
                      init: function (api, node, config) {
                        $(node).removeClass('btn-secondary');
                      }
                    }
                ],
                lengthMenu: [[10,25, 100], [10,25, 100]],
                pageLength: 10,
                createdRow: function(row, data) {
                    $(row).addClass(`${data.id}`);
                    $('a.fancybox', row).fancybox();
                },
                language: {
                  paginate: {
                    // remove previous & next text from pagination
                    previous: '&nbsp;',
                    next: '&nbsp;',
                  }
                }
            });
            $('div.head-label').html('<h4 class="mb-0">{{ trans('dashboard.driver.drivers') }}</h4>');
        }

        function toggle(source) {
    		checkboxes = document.getElementsByClassName('check_list');
    		if (source.checked) {
    			for (var i = 0, n = checkboxes.length; i < n; i++) {
    				checkboxes[i].checked = source.checked;
    			}
    		} else {
    			for (var i = 0, n = checkboxes.length; i < n; i++) {
    				checkboxes[i].checked = source.checked;
    			}
    		}
    	}
    </script>
@endsection
