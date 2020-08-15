<!-- BEGIN VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN VENDOR JS-->

<!-- BEGIN PAGE VENDOR JS (GLOBAL)-->
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/extensions/sweetalert.min.js') }}"></script>--}}
<script src="{{ asset('assets/bower_components/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script src="{{ asset('assets/plugins/ifvisible.js') }}"></script>
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment-with-locales.min.js" integrity="sha512-qSnlnyh7EcD3vTqRoSP4LYsy2yVuqqmnkM9tW4dWo6xvAoxuVXyM36qZK54fyCmHoY1iKi9FJAUZrlPqmGNXFw==" crossorigin="anonymous"></script>--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/locale/id.min.js" integrity="sha512-he8U4ic6kf3kustvJfiERUpojM8barHoz0WYpAUDWQVn61efpm3aVAD8RWL8OloaDDzMZ1gZiubF9OSdYBqHfQ==" crossorigin="anonymous"></script>--}}
<script src="{{ asset('assets/plugins/ping.min.js') }}"></script>
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

    /* [START] WEBSOCKET BOOTSTRAP */
    @if(in_group('mahasiswa'))
        @php($identifier = mt_rand())
        @php($ip = get_client_ip())
        conn = new WebSocket('{{ ws_url() }}');
        conn.onopen = function(e) {
            conn.send(JSON.stringify({
                'nim':'{{ get_logged_user()->username }}',
                'as':'{{ get_selected_role()->name }}',
                'identifier': '{{ $identifier }}',
                'ip': '{{ $ip }}',
                'cmd':'MHS_ONLINE',
                'app_id': '{{ APP_ID }}',
            }));
        };

        conn.onmessage = function(e) {
            let data = jQuery.parseJSON(e.data);
            if (data.cmd == 'MHS_ONLINE') {
                if(data.nim == '{{ get_logged_user()->username }}'){
                    if (data.identifier != '{{ $identifier }}') {
                        // JIKA YANG MENGAKSES BERBEDA DENGAN MHS YG ONLINE
                        location.href = '{{ url('auth/not_valid_login') }}';
                    }
                }
            }else if (data.cmd == 'DO_KICK') {
                if(data.nim == '{{ get_logged_user()->username }}'){
                    // location.href = '{{ url('ujian/not_valid_login') }}';
                    if (typeof selesai == 'function') {
                        ended_by = '{{ get_logged_user()->username }}';
                        selesai();
                    }
                }
            }
        };

        conn.onclose = function(e) {
            conn.send(JSON.stringify({
                'nim':'{{ get_logged_user()->username }}',
                'as':'{{ get_selected_role()->name }}',
                'cmd':'MHS_OFFLINE',
                'app_id': '{{ APP_ID }}',
            }));
        };

        let p ;
        setInterval(function() {
            // let mctime = moment().valueOf();
            p = new Ping();
            p.ping("https://{{ APP_ID }}", function(err, data) {
                // console.log('ping', data);
                conn.send(JSON.stringify({
                    'nim':'{{ get_logged_user()->username }}',
                    'as':'{{ get_selected_role()->name }}',
                    'cmd':'PING',
                    'ip': '{{ $ip }}',
                    'app_id': '{{ APP_ID }}',
                    'latency': data ,
                }));
            });
        },5000);


          // Also display error if err is returned.
          // let mctime = moment().valueOf();
          {{--  conn.send(JSON.stringify({--}}
          {{--      'nim':'{{ get_logged_user()->username }}',--}}
          {{--      'as':'{{ get_selected_role()->name }}',--}}
          {{--      'cmd':'PING',--}}
          {{--      'app_id': '{{ APP_ID }}',--}}
          {{--      'mctime': mctime ,--}}
          {{--  }));--}}


    @endif
    /* [END] WEBSOCKET BOOTSTRAP */

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

});






</script>
