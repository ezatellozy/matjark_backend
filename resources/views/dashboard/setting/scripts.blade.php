@section('vendor_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets') }}/vendors/css/vendors.min.css">
@endsection

@section('page_styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />

<style media="screen">
	body.dark-layout .card .note-toolbar,
	body.dark-layout .card .note-toolbar {
		color: #fff;
		background-color: #55565c;
	}

	.note-editor .btn-toolbar button[data-event="showImageDialog"] {
        display: none !important;
    }

    .note-editor .btn-toolbar button[data-event="showVideoDialog"] {
        display: none !important;
    }

	.note-btn-group .note-btn {
		border-color: rgba(209, 110, 110, 0.2);
		padding: 0.50rem .75rem;
		font-size: 17px;
	}

	.note-btn-group .note-btn {
		color: #a412ff;
	}

	.note-btn-group .note-btn .note-current-fontname {
		font-family: 'Cairo';
		color: #a417fe;
	}

	.note-editor.note-airframe,
	.note-editor.note-frame {
		border: 1px solid ;
		border-color: #a417fe;
	}
	.note-resize button span{
		color: #a417fe;
	}
</style>
@endsection
@section('page_scripts')

<script src="{{ asset('dashboardAssets') }}/js/scripts/components/components-navs.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/lang/summernote-ar-AR.min.js"></script>
<script>
	$('.editor').summernote({
		// airMode: true,
		tabsize: 10,
		height: 250,
		lang: "{{ app()->getLocale() == 'ar' ? 'ar-AR' : '' }}"
	});
</script>
<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
</script>
@endsection
