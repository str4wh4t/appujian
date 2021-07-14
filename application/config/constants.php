<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

// CONSTANT DEFINED BY APP
require_once(APPPATH.'config/constants_depend_app_'. APP_INDEX .'.php');

defined('UPLOAD_DIR') OR define('UPLOAD_DIR', FCPATH . 'uploads/');

defined('ADMIN_GROUP_ID')      OR define('ADMIN_GROUP_ID', 1);
defined('DOSEN_GROUP_ID')      OR define('DOSEN_GROUP_ID', 2);
defined('MHS_GROUP_ID')      OR define('MHS_GROUP_ID', 3);
defined('PENGAWAS_GROUP_ID')      OR define('PENGAWAS_GROUP_ID', 4);
defined('PENYUSUN_SOAL_GROUP_ID')      OR define('PENYUSUN_SOAL_GROUP_ID', 5);
defined('KOORD_PENGAWAS_GROUP_ID')      OR define('KOORD_PENGAWAS_GROUP_ID', 6);
defined('TEMPLATE_LEMBAR_UJIAN')      OR define('TEMPLATE_LEMBAR_UJIAN', 'utbk');

defined('MHS_ID_LENGTH')      OR define('MHS_ID_LENGTH', 12);
defined('NIK_LENGTH')      OR define('NIK_LENGTH', 16);
defined('NO_BILLKEY_LENGTH')      OR define('NO_BILLKEY_LENGTH', 20);
defined('JML_KOLOM_EXCEL_IMPOR_PESERTA')      OR define('JML_KOLOM_EXCEL_IMPOR_PESERTA', 16);
defined('JML_KOLOM_EXCEL_IMPOR_SOAL')      OR define('JML_KOLOM_EXCEL_IMPOR_SOAL', 13);

defined('OPSI_SOAL')      OR define('OPSI_SOAL', ['a', 'b', 'c', 'd', 'e']);
defined('GEL_AVAIL')      OR define('GEL_AVAIL', [1, 2, 3, 4, 5]);
defined('SMT_AVAIL')      OR define('SMT_AVAIL', [1, 2]);
defined('KELOMPOK_UJIAN_AVAIL')      OR define('KELOMPOK_UJIAN_AVAIL', ['null' => 'Semua Kelompok', 0 => 'TIDAK ADA', 1 => 'SAINTEK', 2 => 'SOSHUM',  3 => 'CAMPURAN']);
defined('FLAG_AKTIF')      OR define('FLAG_AKTIF', 1);
defined('JML_SOAL_TUTORIAL')      OR define('JML_SOAL_TUTORIAL', 3);
defined('JML_WAKTU_TUTORIAL')      OR define('JML_WAKTU_TUTORIAL', 5);

defined('PASSWORD_MIN_LENGTH')      OR define('PASSWORD_MIN_LENGTH', 8);
defined('PASSWORD_MAX_LENGTH')      OR define('PASSWORD_MAX_LENGTH', 50);

defined('FOTO_DEFAULT_URL')      OR define('FOTO_DEFAULT_URL', 'assets/imgs/no_profile.jpg');

$regex_date = '^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$';
defined('REGEX_DATE_VALID')      OR define('REGEX_DATE_VALID', $regex_date ); // YYYY-MM-DD

defined('MEMBERSHIP_STTS_AKTIF')      OR define('MEMBERSHIP_STTS_AKTIF', 1);
defined('MEMBERSHIP_STTS_NON_AKTIF')      OR define('MEMBERSHIP_STTS_NON_AKTIF', 0);

defined('PAKET_STTS_AKTIF')      OR define('PAKET_STTS_AKTIF', 1);
defined('PAKET_STTS_NON_AKTIF')      OR define('PAKET_STTS_NON_AKTIF', 0);

defined('PAYMENT_ORDER_TELAH_DIPROSES')      OR define('PAYMENT_ORDER_TELAH_DIPROSES', 1);
defined('PAYMENT_ORDER_BELUM_DIPROSES')      OR define('PAYMENT_ORDER_BELUM_DIPROSES', 0);
defined('PAYMENT_ORDER_EXPIRED')      OR define('PAYMENT_ORDER_EXPIRED', 3);

defined('NON_REPORTED_SOAL')      OR define('NON_REPORTED_SOAL', 0);
defined('REPORTED_SOAL')      OR define('REPORTED_SOAL', 1);
defined('LOCKED_USER_ID')      OR define('LOCKED_USER_ID', 99);
defined('TIPE_SOAL_MCSA')      OR define('TIPE_SOAL_MCSA', 1);
defined('TIPE_SOAL_MCMA')      OR define('TIPE_SOAL_MCMA', 2);
defined('TIPE_SOAL_ESSAY')      OR define('TIPE_SOAL_ESSAY', 3);
defined('TIPE_SOAL')      OR define('TIPE_SOAL', [TIPE_SOAL_MCSA => 'MULTIPLE CHOICE SINGLE ANSWER (MCSA)', 
                                                    TIPE_SOAL_MCMA => 'MULTIPLE CHOICE MULTIPLE ANSWER (MCMA)',
                                                    TIPE_SOAL_ESSAY => 'ESSAY']);

