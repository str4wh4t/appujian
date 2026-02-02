<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*** UJIAN */


defined('APP_ID')                   or define('APP_ID', 'ujian.undip.ac.id');
// defined('APP_AUTHOR')               OR define('APP_AUTHOR', 'universitas diponegoro');
defined('APP_TYPE')                 or define('APP_TYPE', 'ujian');
defined('EMAIL_ACTIVATION')         or define('EMAIL_ACTIVATION',  false);

defined('APP_NAME')                 or define('APP_NAME', 'UJIAN UNDIP');
defined('APP_DESC')                 or define('APP_DESC', 'Aplikasi untuk ujian online');
defined('APP_COLOR_NAV')            or define('APP_COLOR_NAV', '#6061b3');
defined('APP_LOGO')                 or define('APP_LOGO', 'logo_undip.png');
defined('APP_BG_LOGIN')             or define('APP_BG_LOGIN', 'img_big_login.jpg');
defined('APP_FAVICON_APPLE')        or define('APP_FAVICON_APPLE', 'apple-touch-icon-undip.png');
defined('APP_FAVICON')              or define('APP_FAVICON', 'favicon_undip.ico');

defined('MEMBERSHIP_ID_DEFAULT')    or define('MEMBERSHIP_ID_DEFAULT', 0);
defined('PAKET_MATERI_MEMBERSHIP')  or define('PAKET_MATERI_MEMBERSHIP', [
    1 => [],
    2 => [], // 2 => MEMBERSHIP ID; 3, 4 => PAKET ID
    3 => []
]);
defined('PREFIX_ID_MHS')            or define('PREFIX_ID_MHS', 3);
defined('JML_DIGIT_ID_MHS')         or define('JML_DIGIT_ID_MHS', 10);

// defined('SOCKET_ENABLE')      OR define('SOCKET_ENABLE', true);
defined('IS_DEBUG_SOCKET')      or define('IS_DEBUG_SOCKET', false);
// defined('PING_INTERVAL')      OR define('PING_INTERVAL', 30000 ); // IN MS

// MAILER
defined('ADMIN_EMAIL')              or define('ADMIN_EMAIL', 'cat@undip.ac.id');
defined('EMAIL_CONFIG')      or define('EMAIL_CONFIG', [
    // 'mailtype' => 'html',
    'protocol'    => 'smtp',
    'smtp_host'   => 'smtp.office365.com',
    'smtp_port'   => '587',
    'smtp_user'   => 'ujian@undip.ac.id',
    'smtp_pass'   => '',
    'smtp_crypto' => 'tls',
    'mailtype'    => 'html',
    'charset'     => 'utf-8',
    'newline'     => "\r\n",
    'crlf'        => "\r\n"
]);

defined('LOGO_EMAIL_MSG')      or define('LOGO_EMAIL_MSG', 'logo_undip_kecil.png');

defined('PRODI_TXT_DEFAULT')      or define('PRODI_TXT_DEFAULT', 'UMUM'); // IN MS
defined('PRODI_KODE_DEFAULT')      or define('PRODI_KODE_DEFAULT', 11); // IN MS

defined('RECAPTCHA_V3_SITE_KEY')    or define('RECAPTCHA_V3_SITE_KEY', ''); // LOCALHOST
defined('RECAPTCHA_V3_SECRET_KEY')  or define('RECAPTCHA_V3_SECRET_KEY', ''); // LOCALHOST

defined('MIDTRANS_IS_PRODUCTION')       or define('MIDTRANS_IS_PRODUCTION', false);
defined('MIDTRANS_MERCHANT_ID')         or define('MIDTRANS_MERCHANT_ID', 'G340292419');
defined('MIDTRANS_CLIENT_KEY')          or define('MIDTRANS_CLIENT_KEY', ''); // SANDBOX
defined('MIDTRANS_SERVER_KEY')          or define('MIDTRANS_SERVER_KEY', ''); // SANDBOX
defined('MIDTRANS_SNAP_JS_URL')         or define('MIDTRANS_SNAP_JS_URL', 'https://app.sandbox.midtrans.com/snap/snap.js'); // SANDBOX
defined('MIDTRANS_API_URL')             or define('MIDTRANS_API_URL', 'https://api.sandbox.midtrans.com/v2/'); // SANDBOX

defined('APP_CONFIG')      or define('APP_CONFIG', [
    'ws_url' => 'wss://bb.undip.ac.id/wss', //'wss://ujian.undip.ac.id/wss2/NNN', // 'wss://cat.undip.ac.id/wss2/NNN'
    'csrf_token_name' => 'ujian_undip_token',
    'csrf_cookie_name' => 'ujian_undip_cookie',
    'sess_driver' => 'files',
    'sess_cookie_name' => 'ujian_undip_session',
    'sess_save_path' => sys_get_temp_dir(),
]);


//defined('APP_CONFIG')      OR define('APP_CONFIG', [
//                                                    'ws_url' => 'wss://bb.undip.ac.id/wss', //'wss://ujian.undip.ac.id/wss2/NNN', // 'wss://cat.undip.ac.id/wss2/NNN'
//                                                    'csrf_token_name' => 'ujian_undip_token',
//                                                    'csrf_cookie_name' => 'ujian_undip_cookie',
//                                                    'sess_driver' => 'redis',
//                                                    'sess_cookie_name' => 'ujian_undip_session',
//                                                    'sess_save_path' => 'tcp://127.0.0.1:6379?auth=ujol-redis'
//                                                ]);


defined('PAYMENT_METHOD')              or define('PAYMENT_METHOD', 'midtrans'); // midtrans, udid_va
defined('APP_UDID')              or define('APP_UDID', false);
defined('APP_UDID_SECRET')              or define('APP_UDID_SECRET', '');
defined('APP_UDID_ID')              or define('APP_UDID_ID', '');
defined('APP_UDID_API')              or define('APP_UDID_API', '');

defined('SHOW_MEMBERSHIP')              or define('SHOW_MEMBERSHIP', false);
defined('SHOW_PAKET')              or define('SHOW_PAKET', false);
