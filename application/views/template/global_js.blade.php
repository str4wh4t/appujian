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

let conn ;
const base_url = '{{ site_url() }}';
const enable_ping = {{ get_ping_interval() > 0 ? 'true' : 'false' }};
const stop_ping = false ;
const socket_enable = {{ is_enable_socket() ? 'true' : 'false' }};
const is_show_banner_ads = {{ is_show_banner_ads() ? (in_group(MHS_GROUP_ID) ? 'true' : 'false') : 'false' }};
const locked_user_id = {{ LOCKED_USER_ID }};

const csrf = {
    '{{ csrf_name() }}' : '{{ csrf_token() }}',
};

function print_page(data) {
    const mywindow = window.open('', 'new div', 'height=600,width=600');
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

window.mobileCheck = function() {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
};

const showPingInfo = function (latency) {
    // if mobileCheck   
    if(! mobileCheck()){
        toastr.info('', 'latency : ' + latency + 'ms', {positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', timeOut: 0});
    }
};

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
                'app_id': '{{ $_ENV['APP_ID'] }}',
            }));
        };

        conn.onmessage = function(e) {
            let data = jQuery.parseJSON(e.data);
            if(data.app_id == '{{ $_ENV['APP_ID'] }}') {
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
                'app_id': '{{ $_ENV['APP_ID'] }}',
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
    p.ping("{{ url('') }}", function(err, data) {
        sendmsg(JSON.stringify({
            'nim':'{{ get_logged_user()->username }}',
            'as':'{{ get_selected_role()->name }}',
            'cmd':'PING',
            'ip': ip,
            'app_id': '{{ $_ENV['APP_ID'] }}',
            'latency': data ,
        }));
        latency = data;
        showPingInfo(latency);
    });

    if(enable_ping){
        setInterval(function() {
            // let mctime = moment().valueOf();
            if(!stop_ping){
                p.ping("{{ url('') }}", function(err, data) {
                    // console.log('ping', data);
                    sendmsg(JSON.stringify({
                        'nim':'{{ get_logged_user()->username }}',
                        'as':'{{ get_selected_role()->name }}',
                        'cmd':'PING',
                        'ip': ip,
                        'app_id': '{{ $_ENV['APP_ID'] }}',
                        'latency': data ,
                    }));
                    latency = data;
                    toastr.remove();
                    showPingInfo(latency);
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
    {{--      'app_id': '{{ $_ENV['APP_ID'] }}',--}}
    {{--      'mctime': mctime ,--}}
    {{--  }));--}}

    
    @else
    
    /* JIKA USER SELAIN MHS */
    p.ping("{{ url() }}", function(err, data) {
        latency = data;
        showPingInfo(latency);
    });

    if(enable_ping){
        setInterval(function() {
            if(!stop_ping){
                p.ping("{{ url() }}", function(err, data) {
                    latency = data;
                    toastr.remove();
                    showPingInfo(latency);
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
