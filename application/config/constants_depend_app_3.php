<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*** TRYOUT */

defined('APP_ID')                   OR define('APP_ID', 'cat.undip.id');
defined('APP_TYPE')                 OR define('APP_TYPE', 'tryout');
defined('EMAIL_ACTIVATION')         OR define('EMAIL_ACTIVATION',  true);

defined('APP_NAME')                 OR define('APP_NAME', 'UNDIP TRYOUT');
defined('APP_COLOR_NAV')            OR define('APP_COLOR_NAV', '#9f2b2b');
defined('APP_BG_LOGIN')             OR define('APP_BG_LOGIN', 'img_big_login_tryout');
defined('APP_FAVICON_APPLE')        OR define('APP_FAVICON_APPLE', 'apple-touch-icon');
defined('APP_FAVICON')              OR define('APP_FAVICON', 'favicon');
defined('APP_ICON')                 OR define('APP_ICON', 'android-chrome-512x512');
defined('ADMIN_EMAIL')              OR define('ADMIN_EMAIL', 'ujian@undip.ac.id');
defined('MEMBERSHIP_ID_DEFAULT')    OR define('MEMBERSHIP_ID_DEFAULT', 1);
defined('PAKET_MATERI_MEMBERSHIP')  OR define('PAKET_MATERI_MEMBERSHIP', [1 => [1], 2 => [5], 3 => [5]]);
defined('SHOW_REGISTRATION')        OR define('SHOW_REGISTRATION', true);
defined('SHOW_DETAIL_HASIL')        OR define('SHOW_DETAIL_HASIL', true);
defined('ENABLE_TAMBAH_MHS')        OR define('ENABLE_TAMBAH_MHS', false);
defined('PREFIX_ID_MHS')            OR define('PREFIX_ID_MHS', 1);
defined('JML_DIGIT_ID_MHS')         OR define('JML_DIGIT_ID_MHS', 10);

defined('RECAPTCHA_V3_SITE_KEY')    OR define('RECAPTCHA_V3_SITE_KEY', '6Lfr6WsaAAAAANagzqQC1oyxtj2aWwnK5VnCa6Nm'); // LOCALHOST
defined('RECAPTCHA_V3_SECRET_KEY')  OR define('RECAPTCHA_V3_SECRET_KEY', '6Lfr6WsaAAAAAILW941zZHzNZ3ZhAZ76fLTRs2Ie'); // LOCALHOST

// defined('RECAPTCHA_V3_SITE_KEY')     OR define('RECAPTCHA_V3_SITE_KEY', '6Ldnzn4aAAAAAKFVt4QCsp5Uj3zRpY6MiQjFBXbA'); // LIVE
// defined('RECAPTCHA_V3_SECRET_KEY')   OR define('RECAPTCHA_V3_SECRET_KEY', '6Ldnzn4aAAAAAKJcpTxW5g7D0rKBAwL705r8AG5s'); // LIVE

defined('MIDTRANS_IS_PRODUCTION')       OR define('MIDTRANS_IS_PRODUCTION', false);
defined('MIDTRANS_MERCHANT_ID')         OR define('MIDTRANS_MERCHANT_ID', 'G340292419');
defined('MIDTRANS_CLIENT_KEY')          OR define('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-Qtk4qV3gJ4YRKwLf');
defined('MIDTRANS_SERVER_KEY')          OR define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-OwjiojNTQ2Qz7Njbnw3BvO2n');
defined('MIDTRANS_SNAP_JS_URL')         OR define('MIDTRANS_SNAP_JS_URL', 'https://app.sandbox.midtrans.com/snap/snap.js'); // SANDBOX
// defined('MIDTRANS_SNAP_JS_URL')      OR define('MIDTRANS_SNAP_JS_URL', 'https://app.midtrans.com/snap/snap.js'); // PRODUCTION
defined('MIDTRANS_API_URL')             OR define('MIDTRANS_API_URL', 'https://api.sandbox.midtrans.com/v2/');
// defined('MIDTRANS_API_URL')          OR define('MIDTRANS_API_URL', 'https://api.midtrans.com/v2/');