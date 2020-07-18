<!-- BEGIN VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN VENDOR JS-->

<!-- BEGIN PAGE VENDOR JS (GLOBAL)-->
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/extensions/sweetalert.min.js') }}"></script>--}}
<script src="{{ asset('assets/bower_components/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script src="{{ asset('assets/plugins/ifvisible.js') }}"></script>
<!-- END PAGE VENDOR JS (GLOBAL)-->

<!-- BEGIN PAGE VENDOR JS (PAGE LEVEL)-->
@stack('page_vendor_level_js')
<!-- END PAGE VENDOR JS (PAGE LEVEL)-->

<!-- BEGIN ROBUST JS-->
<script src="{{ asset('assets/template/robust/app-assets/js/core/app-menu.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/js/core/app.min.js') }}"></script>
<!-- END ROBUST JS-->

<!-- BEGIN PAGE LEVEL JS (PAGE LEVEL)-->
@stack('page_level_js')
<!-- END PAGE LEVEL JS (PAGE LEVEL)-->


<script type="text/javascript">

let base_url = '{{ site_url('/') }}';
let conn ;

function print_page(data) {
    let mywindow = window.open('', 'new div', 'height=600,width=600');
    mywindow.document.write('<html><head><title></title>');
    mywindow.document.write('<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/vendors.css') }}">');
    mywindow.document.write('<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/app.css') }}">');
    mywindow.document.write('<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/assets/css/style.css') }}">');
    mywindow.document.write('</head><body>');
    mywindow.document.write(data);
    mywindow.document.write('</body></html>');
    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/
    setTimeout(function () {
               mywindow.print();
               mywindow.close();
           }, 1000);

    return false;
}

function ajaxcsrf() {
    let csrfname = '{{ csrf_name() }}';
    let csrfhash = '{{ csrf_token() }}';
    let csrf = {};
    csrf[csrfname] = csrfhash;
    $.ajaxSetup({
        "data": csrf,
    });
}

function ajx_overlay(show){
    if(show)
        $.LoadingOverlay("show");
    else
        $.LoadingOverlay("hide");
}

function reload_ajax(){
    ajx_overlay(true);
    table.ajax.reload(function(){
        ajx_overlay(false);
    }, false);
}

$(document).ready(function(){

    swal.setDefaults({ heightAuto : false }); // remove heightAuto in swal2 because broken the height of page when appeaer

    if (typeof init_page_level == 'function') {
		/**
		 * call init page level function
		 */
		init_page_level();
	}

    @if(flash_data('message_rootpage'))
        @php($msg = flash_data('message_rootpage'))
        swal({
           title: "{{ $msg['header'] }}",
           text: "{{ $msg['content'] }}",
           type: "{{ $msg['type'] }}"
        });
    @endif

    @if(in_group('mahasiswa'))
    // let conn = new WebSocket('wss://ujian.undip.ac.id/wss2/NNN');
    conn = new WebSocket('{{ ws_url() }}');
    conn.onopen = function(e) {
        // console.log("Connection established!");
        conn.send(JSON.stringify({
            'nim':'{{ get_logged_user()->username }}',
            'as':'{{ get_selected_role()->name }}',
            'cmd':'MHS_ONLINE'
        }));
    };

    conn.onmessage = function(e) {
        // console.log(e.data);
        let data = jQuery.parseJSON(e.data);
        if (data.cmd == 'MHS_ONLINE') {
            if((data.nim == '{{ get_logged_user()->username }}') && (!data.ok)){
                location.href = '{{ url('auth/not_valid_login') }}';
            }
        }else if (data.cmd == 'MHS_KICKED') {
            if(data.nim == '{{ get_logged_user()->username }}'){
                // location.href = '{{ url('ujian/not_valid_login') }}';
            }
        }
    };

    conn.onclose = function(e) {
        console.log(e.data);
        conn.send(JSON.stringify({
            'nim':'{{ get_logged_user()->username }}',
            'as':'{{ get_selected_role()->name }}',
            'cmd':'MHS_OFFLINE'
        }));

    };

    @endif

});






</script>
