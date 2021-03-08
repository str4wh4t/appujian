<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Registrasi user untuk ujian meliputi ujian masuk UM, UTBK, SBMPTN dan UJIAN LAIN secara online di lingkungan Universitas Diponegoro Semarang">
{{--    <meta name="keywords" content="">--}}
    <meta name="author" content="Universitas Diponegoro">

    <meta name="{{ csrf_name() }}" content="{{ csrf_token() }}">

    <title>{{ APP_NAME }}</title>
    <link rel="apple-touch-icon" href="{{ asset('assets/icon/'. APP_FAVICON_APPLE .'.png') }}">
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/icon/'. APP_FAVICON .'.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CMuli:300,400,500,700" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/vendors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/ui/jquery-ui.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/icheck.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/custom.css') }}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/app.css') }}">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/core/menu/menu-types/vertical-compact-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/core/colors/palette-gradient.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/pages/login-register.css') }}">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/assets/css/style.css') }}">
    <!-- END Custom CSS-->

    <style type="text/css">
        html body.bg-full-screen-image {
            background: url({{ asset('assets/imgs/'. APP_BG_LOGIN .'.jpg') }}) no-repeat center center fixed;
            webkit-background-size: cover; /** */
            background-size: cover;
            /** **/
        }
    </style>


  </head>
  <body class="vertical-layout vertical-compact-menu 1-column  bg-full-screen-image menu-expanded blank-page blank-page" data-open="click" data-menu="vertical-compact-menu" data-col="1-column">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="app-content content">
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
<section class="flexbox-container">
    <div class="col-12 d-flex align-items-center justify-content-center">
        <div class="col-md-4 col-10 box-shadow-2 p-0">
            <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                <div class="card-header border-0">
                    <h6 class="card-subtitle text-muted text-center font-small-3 font-large-1">
                        <span>Lupa Password</span>
                    </h6>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if(flash_data('error_resend_password_msg'))
                        <div class="alert bg-warning">{{ flash_data('error_resend_password_msg') }}</div>
                        @endif

                        @if(flash_data('success_resend_password_msg'))
                        <div class="alert bg-success"><i class="fa fa-check-circle"></i> {{ flash_data('success_resend_password_msg') }}</div>
                        @endif
                        <?= form_open("auth/resend_password", ['id'=>'form','class'=>'form-horizontal','novalidate'=>'','method'=>'POST']);?>
                            <fieldset class="form-group position-relative has-icon-left">
                                <div class="form-control-position" style="line-height: 2.8rem;">
                                    <i class="fa fa-envelope-o"></i>
                                </div>
                                <input type="text" name="identity" value="" id="identity" placeholder="Email anda" autofocus="autofocus" class="form-control" autocomplete="off">
                                <span class="help-block"></span>
                            </fieldset>
                            <button type="submit" class="btn btn-outline-info btn-block" id="btn_submit_registrasi">Kirim Password</button>
                            <a href="{{ url('login') }}" class="btn btn-info btn-block" id="btn_link_login"><i class="fa fa-chevron-left"></i> Login</a>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

        </div>
      </div>
    </div>

    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <!-- BEGIN VENDOR JS-->
    <script src="{{ asset('assets/template/robust/app-assets/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->

    <script src="{{ asset('assets/template/robust/app-assets/js/core/libraries/jquery_ui/jquery-ui.min.js') }}"></script>

    <script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN ROBUST JS-->
    <script src="{{ asset('assets/template/robust/app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('assets/template/robust/app-assets/js/core/app.js') }}"></script>
    <!-- END ROBUST JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{ asset('assets/template/robust/app-assets/js/scripts/forms/form-login-register.js') }}"></script>
{{--    <script src="https://rawgit.com/RobinHerbots/Inputmask/5.x/dist/jquery.inputmask.js"></script>--}}
    <script src="{{ asset('assets/yarn/node_modules/inputmask/dist/jquery.inputmask.min.js') }}"></script>
{{--    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>--}}
    <script src="{{ asset('assets/yarn/node_modules/jquery-validation/dist/jquery.validate.min.js') }}"></script>
    <!-- END PAGE LEVEL JS-->
    <script type="text/javascript">

        jQuery.validator.addMethod("valid_email", function(value, element) {
            // allow any non-whitespace characters as the host part
            return this.optional( element ) || /\S+@\S+\.\S+/.test( value );
        }, 'Please enter a valid email address.');

        let validator = $("#form").validate({
            debug: false,
            ignore: [],
            rules: {
                'identity': {required: true, valid_email: true},
                // 'identity': {required: true},
            },
            messages: {
                'identity': {
                    required: "tidak boleh kosong",
                    valid_email: "email yg dimasukan salah"
                }
            },
            errorElement: "small",
            // <p class="badge-default badge-danger block-tag text-right"><small class="block-area white">Helper aligned to right</small></p>
            errorPlacement: function ( error, element ) {
                error.addClass("badge-default badge-danger block-tag pl-2");
                // error.css('display','block');
                if ( element.prop("type") === "radio" ) {
                    error.appendTo(element.siblings(".error_radio"));
                } else if ( element.hasClass("only_input_select2multi")) {
                    // error.insertAfter(element.parent().parent().parent().siblings(".error_select2"));
                    error.css('display','block');
                    error.insertAfter(element.siblings(".error_select2"));
                    // error.insertAfter(element);
                } else if ( element.hasClass("only_input_select2single")) {
                    // error.insertAfter(element.parent().parent().parent().siblings(".error_select2"));
                    error.css('display','block');
                    error.insertAfter(element.siblings(".error_select2"));
                    // error.insertAfter(element);
                } else if ( element.prop("type") === "checkbox" ) {
                    error.appendTo(element.siblings(".error_checkbox"));
                } else {
                    error.insertAfter(element);
                    element.addClass('border-danger');
                }
            },
            highlight: function ( element, errorClass, validClass ) {
                // $(element).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
                $(element).addClass('border-danger');
            },
            unhighlight: function (element, errorClass, validClass) {
                // $(element).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
                $(element).removeClass('border-danger');
            },
            submitHandler: function(form) {
                // if(confirm('Yakin akan mensubmit jawaban ?')){
                    form.submit();
                // }
            }
        });

        $(document).ready(function(){
            $('#btn_submit_registrasi').show();
            $('#btn_link_login').show();
        });
        </script>
  </body>
</html>
