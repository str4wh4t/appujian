<!-- BEGIN VENDOR CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/vendors.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/icheck.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/custom.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/extensions/sweetalert.css') }}">
@stack('page_vendor_level_css')
<!-- END VENDOR CSS-->

<!-- BEGIN ROBUST CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/app.css') }}">
<!-- END ROBUST CSS-->

<!-- BEGIN PAGE LEVEL CSS (GLOBAL)-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/core/menu/menu-types/vertical-compact-menu.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/core/colors/palette-gradient.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/core/colors/palette-climacon.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/pages/users.css') }}">
<!-- END PAGE LEVEL (GLOBAL)-->

<!-- BEGIN PAGE LEVEL CSS (PAGE LEVEL)-->
@stack('page_level_css')
<!-- END PAGE LEVEL CSS (PAGE LEVEL)-->

<!-- BEGIN CUSTOM CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/assets/css/style.css') }}">
<!-- END CUSTOM CSS-->

<!-- BEGIN PAGE CUSTOM CSS-->
@stack('page_custom_css')
<!-- END PAGE CUSTOM CSS-->

<style type="text/css">
    .header-navbar {
        background-color: #60a5b3 !important;
    }
    .select2-container .select2-search--inline .select2-search__field {
        margin-top: 3px;
        margin-bottom: 3px;
        padding-left: 8px;
    }
    .help-block {
        display: block;
        margin-top: 5px;
        margin-bottom: 10px;
        color: #737373;
    }
    .form-group.has-error .help-block {
        color: #dd4b39;
    }
    .form-group.has-error .form-control, .form-group.has-error .input-group-addon {
        border-color: #dd4b39;
        box-shadow: none;
    }
    .has-error .select2-selection {
        border: 1px solid #dd4b39 !important;
    }
    body { &.swal2-shown { &.swal2-shown { height: 100% !important; } } }

</style>
