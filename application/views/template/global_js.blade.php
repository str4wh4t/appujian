<!-- BEGIN VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN VENDOR JS-->

<!-- BEGIN PAGE VENDOR JS (GLOBAL)-->
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/extensions/sweetalert.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/bower_components/sweetalert2/sweetalert2.all.min.js') }}"></script>--}}
<script src="{{ asset('assets/yarn/node_modules/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/gasparesganga-jquery-loading-overlay/dist/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('assets/plugins/ifvisible.js') }}"></script>
<script src="{{ asset('assets/plugins/ping.min.js') }}"></script>
<!-- END PAGE VENDOR JS (GLOBAL)-->

<!-- BEGIN PAGE VENDOR JS (PAGE LEVEL)-->
@stack('page_vendor_level_js')
<!-- END PAGE VENDOR JS (PAGE LEVEL)-->

<!-- BEGIN ROBUST JS-->
<script src="{{ asset('assets/template/robust/app-assets/js/core/app-menu.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/js/core/app.min.js') }}"></script>
<!-- END ROBUST JS-->

<script type="text/javascript">

let base_url = '{{ site_url('/') }}';
let conn ;
let enable_ping = {{ get_ping_interval() > 0 ? 'true' : 'false' }};
let stop_ping = false ;
let socket_enable = {{ is_enable_socket() ? 'true' : 'false' }};
let is_show_banner_ads = {{ is_show_banner_ads() ? (in_group(MHS_GROUP_ID) ? 'true' : 'false') : 'false' }};
let locked_user_id = {{ LOCKED_USER_ID }};

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

let i_reconnect_socket = 0;
let batas_reconnect_socket = 5;

const sendmsg = function (message, callback) {
    if(socket_enable){
        waitForConnection(function () {
            conn.send(message);
            if (typeof callback !== 'undefined') {
                callback();
            }
        }, 5000); // <== 5 DETIK
    }
};

const waitForConnection = function (callback, interval) {
    // console.log('conn.readyState', conn.readyState);
    if (typeof conn !== 'undefined') { // JIKA conn TELAH DI INISIASI SBG obj WEBSOCKET
        if (conn.readyState === 1) {
            callback();
        } else {
            if(i_reconnect_socket > batas_reconnect_socket){
                socketConnectionFailure();
            }else{
                setTimeout(function () {
                    waitForConnection(callback, interval);
                }, interval);
            }
            // i_reconnect_socket++; // <== DIMATIKAN BIAR EVERLASTING LOOPING
        }
    }else{
        // socketConnectionFailure();
        waitForConnection(callback, interval);
    }
};

const socketConnectionFailure = function (){
    stop_ping = true;
    // Swal.fire({
    //     title: "Perhatian",
    //     text: "Koneksi ke server terputus",
    //     icon: "warning",
    //     confirmButtonText: "Refresh",
    //     allowOutsideClick: false,
    //     allowEscapeKey: false,
    // }).then(result => {
    //     if (result.value) {
    //         window.location.href = '{{ current_url() }}';
    //     }
    // });
};

let latency = 0;
let p = new Ping();
let ip = '{{ '-' }}' ; <?php /* get_client_ip() ;  */ ?>

$(document).ready(function(){

    // swal.setDefaults({ heightAuto : false }); // remove heightAuto in swal2 because broken the height of page when appeaer

    @if(flash_data('message_rootpage'))
        @php($msg = flash_data('message_rootpage'))
        Swal.fire({
           title: "{{ $msg['header'] }}",
           text: "{{ $msg['content'] }}",
           icon: "{{ $msg['type'] }}",
        //    heightAuto: false,
        });
    @endif

    /* [START] WEBSOCKET BOOTSTRAP */
    @if(in_group('mahasiswa'))
    /* JIKA USER MHS */
    @php($identifier = mt_rand())
    
    if(socket_enable){
        conn = new WebSocket('{{ ws_url() }}');
        conn.onopen = function(e) {
            sendmsg(JSON.stringify({
                'nim':'{{ get_logged_user()->username }}',
                'as':'{{ get_selected_role()->name }}',
                'identifier': '{{ $identifier }}',
                'ip': ip,
                'cmd':'MHS_ONLINE',
                'app_id': '{{ APP_ID }}',
            }));
        };

        conn.onmessage = function(e) {
            let data = jQuery.parseJSON(e.data);
            if(data.app_id == '{{ APP_ID }}') {
                if (data.cmd == 'MHS_ONLINE') {
                    if (data.nim == '{{ get_logged_user()->username }}') {
                        if (data.identifier != '{{ $identifier }}') {
                            // JIKA YANG MENGAKSES BERBEDA DENGAN MHS YG ONLINE
                            location.href = '{{ url('auth/not_valid_login') }}';
                        }
                    }
                } else if (data.cmd == 'DO_KICK') {
                    if (data.nim == '{{ get_logged_user()->username }}') {
                        if (typeof selesai == 'function') {
                            ended_by = data.username;
                            selesai(ended_by);
                        }
                    }
                } else if (data.cmd == 'UPDATE_TIME') {
                    if (data.nim == '{{ get_logged_user()->username }}') {
                        if (typeof setting_up_view == 'function'){
                            clearInterval(refreshIntervalId);
                            setting_up_view();
                        }
                    }
                }
            }
        };

        conn.onclose = function(e) {
            sendmsg(JSON.stringify({
                'nim':'{{ get_logged_user()->username }}',
                'as':'{{ get_selected_role()->name }}',
                'cmd':'MHS_OFFLINE',
                'app_id': '{{ APP_ID }}',
            }));
        };
    }
    @else

    // UNTUK WEBSOOCKET SELAIN MHS CUMA BERADA DI PAGE MONITORING
    
    @endif
    /* [END] WEBSOCKET BOOTSTRAP */

    if (typeof init_page_level == 'function') {
		/**
		 * call init page level function
		 */
		init_page_level();
	}

    /* [START] PINGER */
    @if(in_group('mahasiswa'))
    /* JIKA USER MHS */
    p.ping("{{ url('/') }}", function(err, data) {
        sendmsg(JSON.stringify({
            'nim':'{{ get_logged_user()->username }}',
            'as':'{{ get_selected_role()->name }}',
            'cmd':'PING',
            'ip': ip,
            'app_id': '{{ APP_ID }}',
            'latency': data ,
        }));
        latency = data;
        toastr.info('', 'latency : ' + latency + 'ms', {positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', timeOut: 0});
    });

    if(enable_ping){
        setInterval(function() {
            // let mctime = moment().valueOf();
            if(!stop_ping){
                p.ping("{{ url('/') }}", function(err, data) {
                    // console.log('ping', data);
                    sendmsg(JSON.stringify({
                        'nim':'{{ get_logged_user()->username }}',
                        'as':'{{ get_selected_role()->name }}',
                        'cmd':'PING',
                        'ip': ip,
                        'app_id': '{{ APP_ID }}',
                        'latency': data ,
                    }));
                    latency = data;
                    toastr.remove();
                    toastr.info('', 'latency : ' + latency + 'ms', {positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', timeOut: 0});
                });
            }
        },{{ get_ping_interval() }});
    }


    // Also display error if err is returned.
    // let mctime = moment().valueOf();
    {{--  sendmsg(JSON.stringify({--}}
    {{--      'nim':'{{ get_logged_user()->username }}',--}}
    {{--      'as':'{{ get_selected_role()->name }}',--}}
    {{--      'cmd':'PING',--}}
    {{--      'app_id': '{{ APP_ID }}',--}}
    {{--      'mctime': mctime ,--}}
    {{--  }));--}}

    
    @else
    /* JIKA USER SELAIN MHS */
    p.ping("{{ url('/') }}", function(err, data) {
        latency = data;
        toastr.info('', 'latency : ' + latency + 'ms', {positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', timeOut: 0});
    });

    if(enable_ping){
        setInterval(function() {
            if(!stop_ping){
                p.ping("{{ url('/') }}", function(err, data) {
                    latency = data;
                    toastr.remove();
                    toastr.info('', 'latency : ' + latency + 'ms', {positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', timeOut: 0});
                });
            }
        },{{ get_ping_interval() }});
    }

    @endif
    /* [STOP] PINGER */
    
});

// $(window).on('beforeunload', function(){
//     if(socket_enable)
//         init_socket(); 
// });

</script>

<!-- BEGIN PAGE LEVEL JS (PAGE LEVEL)-->
@stack('page_level_js')
<!-- END PAGE LEVEL JS (PAGE LEVEL)-->
