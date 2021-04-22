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
    {{-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CMuli:300,400,500,700" rel="stylesheet"> --}}
    <link href="{{ asset('assets/yarn/node_modules/typeface-muli/index.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/yarn/node_modules/typeface-open-sans/index.css') }}" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/vendors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/ui/jquery-ui.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
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

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/yarn/node_modules/bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css') }}">

    <style type="text/css">
        html body.bg-full-screen-image {
            background: url({{ asset('assets/imgs/'. APP_BG_LOGIN .'.jpg') }}) no-repeat center center fixed;
            webkit-background-size: cover; /** */
            background-size: cover;
            /** **/
        }
        #ul_error{
            margin-bottom: 0
        }
        #ul_error li{
            list-style-type: disc;
        }
        .select2-selection__rendered{
            padding-left: 2rem !important;
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
        <div class="col-md-6 col-sm-12 p-0">
            <div class="card border-grey border-lighten-3">
                <div class="card-header border-0 mt-1">
                    <h6 class="card-subtitle text-muted text-center font-small-3 font-large-1">
                        <span>Registrasi</span>
                    </h6>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if(flash_data('error_registrasi_msg'))
                        <div class="alert alert-warning">{{ flash_data('error_registrasi_msg') }}</div>
                        @endif

                        @if(flash_data('success_registrasi_msg'))
                        <div class="alert bg-success"><i class="ft-check-circle"></i> {{ flash_data('success_registrasi_msg') }}</div>
                        @endif

                        @if(!empty(validation_errors()))
                        <div class="alert alert-warning"><ul id="ul_error">{!! validation_errors() !!}</ul></div>
                        @endif
                        
                        @if(!empty($error_register_user))
                        <div class="alert alert-warning">{{ $error_register_user }}</div>
                        @endif

                        <?= form_open("auth/registrasi", ['id'=>'form','class'=>'form-horizontal','novalidate'=>'','method'=>'POST'], ['token' => '', 'action' => '']);?>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" name="full_name" value="{{ set_value('full_name') }}" id="full_name" placeholder="Nama lengkap" autofocus="autofocus" class="form-control" autocomplete="off">
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-chevron-right"></i>
                                    </div>
                                    <span class="help-block"></span>
                                </fieldset>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" name="nik" value="{{ set_value('nik') }}" id="nik" placeholder="NIK" autofocus="autofocus" class="form-control" autocomplete="off">
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-chevron-right"></i>
                                    </div>
                                    <span class="help-block"></span>
                                </fieldset>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" name="email" value="{{ set_value('email') }}" id="email" placeholder="Email" autofocus="autofocus" class="form-control" autocomplete="off">
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-chevron-right"></i>
                                    </div>
                                    <span class="help-block"></span>
                                </fieldset>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" name="telp" value="{{ set_value('telp') }}" id="telp" placeholder="No. telp / WA" autofocus="autofocus" class="form-control" autocomplete="off">
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-chevron-right"></i>
                                    </div>
                                    <span class="help-block"></span>
                                </fieldset>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control select2 only_input_select2single">
                                        {{-- <option value="" {{ empty(set_value('jenis_kelamin')) ? 'selected' : '' }} hidden>Jenis kelamin</option> --}}
                                        <option value="" {{ empty(set_value('jenis_kelamin')) ? 'selected' : '' }} disabled>Jenis kelamin</option>
                                        <option value="L" {{ set_value('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ set_value('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-chevron-right"></i>
                                    </div>
                                    <span class="help-block error_select2"></span>
                                </fieldset>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <fieldset class="form-group position-relative has-icon-left">
                                    <select name="kota_asal" id="kota_asal" class="form-control select2 only_input_select2single">
                                        <option value="" disabled {{ empty(set_value('kota_asal')) ? 'selected' : '' }}>- Pilih kota asal -</option>
                                        @foreach ($kota_kab_list as $item)
                                        <option value="{{ $item->kota_kab }}" {{ set_value('kota_asal') == $item->kota_kab ? 'selected="selected"' : '' }}>{{ $item->kota_kab }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-chevron-right"></i>
                                    </div>
                                    <span class="help-block error_select2"></span>
                                </fieldset>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <select name="tmp_lahir" id="tmp_lahir" class="form-control select2 only_input_select2single">
                                        <option value="" disabled {{ empty(set_value('tmp_lahir')) ? 'selected' : '' }}>- Pilih tempat lahir -</option>
                                        @foreach ($kota_kab_list as $item)
                                        <option value="{{ $item->kota_kab }}" {{ set_value('tmp_lahir') == $item->kota_kab ? 'selected="selected"' : '' }}>{{ $item->kota_kab }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-chevron-right"></i>
                                    </div>
                                    <span class="help-block error_select2"></span>
                                </fieldset>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" name="tgl_lahir" value="{{ set_value('tgl_lahir') }}" id="tgl_lahir" placeholder="Tgl lahir" class="datetimepicker form-control">
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-chevron-right"></i>
                                    </div>
                                    <span class="help-block"></span>
                                </fieldset>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="password" name="password" value="" id="password" placeholder="Password" autofocus="autofocus" class="form-control" autocomplete="off">
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-lock"></i>
                                    </div>
                                    <span class="help-block"></span>
                                </fieldset>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="password" name="password_confirm" value="" id="password_confirm" placeholder="Password confirm" autofocus="autofocus" class="form-control" autocomplete="off">
                                    <div class="form-control-position" style="line-height: 2.8rem;">
                                        <i class="ft-lock"></i>
                                    </div>
                                    <span class="help-block"></span>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-2 col-md-8 col-sm-12">
                                {{-- {{ no_captcha()->input('g-recaptcha-response') }} --}}
                                <button type="submit" class="btn btn-outline-info btn-block" id="btn_submit_registrasi">Daftar</button>
                                <a href="{{ url('login') }}" class="btn btn-info btn-block" id="btn_link_login"><i class="fa fa-chevron-left"></i> Login</a>
                            </div>
                        </div>
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

    <script src="{{ asset('assets/template/robust/app-assets/js/core/libraries/jquery.min.js') }}"></script>

    <!-- BEGIN VENDOR JS-->
    <script src="{{ asset('assets/template/robust/app-assets/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{ asset('assets/template/robust/app-assets/js/core/libraries/jquery_ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('assets/yarn/node_modules/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/yarn/node_modules/bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/yarn/node_modules/gasparesganga-jquery-loading-overlay/dist/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
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

    <script src="https://www.google.com/recaptcha/api.js?render={{ RECAPTCHA_V3_SITE_KEY }}"></script>

    <script type="text/javascript">

        // $("#identity").inputmask("email");

        jQuery.validator.addMethod("valid_email", function(value, element) {
            // allow any non-whitespace characters as the host part
            return this.optional( element ) || /\S+@\S+\.\S+/.test( value );
        }, 'Please enter a valid email address.');

        jQuery.validator.addMethod("valid_no_telp", function(value, element) {
            // allow any non-whitespace characters as the host part
            return value.substring(0,2) == '08';
        }, 'Please enter a valid no telp.');

        jQuery.validator.addMethod("valid_date", function(value, element) {
            // allow any non-whitespace characters as the host part
            return this.optional( element ) || /{{ REGEX_DATE_VALID }}/.test( value );
        }, 'Please enter a valid date');

        let validator = $("#form").validate({
            debug: false,
            ignore: [],
            rules: {
                'full_name': {required: true},
                'nik': {required: true, minlength: {{ NIK_LENGTH }}, maxlength: {{ NIK_LENGTH }}, digits: true},
                'email': {required: true, valid_email: true},
                'telp': {required: true, digits: true, minlength: 10, valid_no_telp: true},
                'jenis_kelamin': {required: true},
                'kota_asal': {required: true},
                'tmp_lahir': {required: true},
                'tgl_lahir': {required: true, valid_date: true},
                'password': {required: true, minlength: {{ PASSWORD_MIN_LENGTH }}, maxlength: {{ PASSWORD_MAX_LENGTH }}},
                'password_confirm': {required: true, equalTo: "#password"},
            },
            messages: {
                'full_name': {
                    required: "tidak boleh kosong",
                },
                'nik': {
                    required: "tidak boleh kosong",
                    digits: "hanya berupa angka",
                },
                'email': {
                    required: "tidak boleh kosong",
                    valid_email: "email yg dimasukan salah"
                },
                'telp': {
                    required: "tidak boleh kosong",
                    digits: "hanya berupa angka",
                    minlength: "no. telp tidak valid",
                    valid_no_telp: "no. telp diawali angka 08xxx"
                },
                'jenis_kelamin': {
                    required: "tidak boleh kosong",
                },
                'kota_asal': {
                    required: "tidak boleh kosong",
                },
                'tmp_lahir': {
                    required: "tidak boleh kosong",
                },
                'tgl_lahir': {
                    required: "tidak boleh kosong",
                },
                'password': {
                    required: "tidak boleh kosong",
                    minlength: "password min 8 karakter",
                },
                'password_confirm': {
                    required: "tidak boleh kosong",
                    equalTo: "password tidak sama",
                },
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
                    // form.submit();
                // }

                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ RECAPTCHA_V3_SITE_KEY }}', {action: 'submit_registration'}).then(function(token) {
                        $('input[name="token"]').val(token);
                        $('input[name="action"]').val('submit_registration');
                        $('#btn_submit_registrasi').prop('disabled', true);
                        $.LoadingOverlay("show");
                        sleep(2000).then(() => {
                            // Do something after the sleep!
                            form.submit();
                        });
                    });;
                });

            }
        });

        $(document).ready(function(){
            $('#btn_submit_registrasi').show();
            $('#btn_link_login').show();
            $('.select2').select2();

            $('.datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                // Your Icons
                // as Bootstrap 4 is not using Glyphicons anymore
                icons: {
                    time: 'fa fa-clock-o',
                    date: 'fa fa-calendar',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-check',
                    clear: 'fa fa-trash',
                    close: 'fa fa-times'
                }
            });
        });


        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        </script>
  </body>
</html>
