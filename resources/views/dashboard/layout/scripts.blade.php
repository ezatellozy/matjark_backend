<script src="{{ asset('dashboardAssets') }}/vendors/js/vendors.min.js"></script>
<script src="{{ asset('dashboardAssets') }}/vendors/js/ui/jquery.sticky.js"></script>
<script src="{{ asset('dashboardAssets') }}/vendors/js/extensions/toastr.min.js"></script>

@yield('vendor_scripts')
<!-- BEGIN: Theme JS-->
<script src="{{ asset('dashboardAssets') }}/js/core/app-menu.js"></script>
<script src="{{ asset('dashboardAssets') }}/js/core/app.js"></script>
<script src="{{ asset('dashboardAssets') }}/js/scripts/customizer.js"></script>
<script src="{{ asset('dashboardAssets') }}/js/scripts/footer.min.js"></script>
<!-- END: Theme JS-->

<script src="{{ asset('dashboardAssets') }}/js/scripts/extensions/ext-component-toastr.js"></script>
@yield('page_scripts')
<script src="{{ asset('dashboardAssets') }}/js/custom/scripts.js"></script>

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
