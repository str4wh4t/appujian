<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Setting;

function get_banner_ads_link() {
    $banner_ads_link = '';
    $setting = Setting::where(['variabel' => 'banner_ads_link', 'flag' => '1'])->first();
    if(!empty($setting))
        $banner_ads_link = $setting->nilai;
    
    return $banner_ads_link ;
}

function is_show_banner_ads() {
    $is_show_banner_ads = 0;
    $setting = Setting::where(['variabel' => 'is_show_banner_ads', 'flag' => '1'])->first();
    if(!empty($setting))
        $is_show_banner_ads = $setting->nilai;
    
    return $is_show_banner_ads ;
}

function is_enable_socket() {
    $is_enable_socket = 0;
    $setting = Setting::where(['variabel' => 'is_enable_socket', 'flag' => '1'])->first();
    if(!empty($setting))
        $is_enable_socket = $setting->nilai;
    
    return $is_enable_socket ;
}

function get_ping_interval() {
    $ping_interval = 0;
    $setting = Setting::where(['variabel' => 'ping_interval', 'flag' => '1'])->first();
    if(!empty($setting))
        $ping_interval = $setting->nilai;
    
    return $ping_interval ;
}

function get_api_auth_username() {
    $ping_interval = 0;
    $setting = Setting::where(['variabel' => 'api_auth_username', 'flag' => '1'])->first();
    if(!empty($setting))
        $api_auth_username = $setting->nilai;
    
    return $api_auth_username ;
}

function get_api_auth_password() {
    $ping_interval = 0;
    $setting = Setting::where(['variabel' => 'api_auth_password', 'flag' => '1'])->first();
    if(!empty($setting))
        $api_auth_password = $setting->nilai;
    
    return $api_auth_password ;
}

function get_app_author() {
    $ping_interval = 0;
    $setting = Setting::where(['variabel' => 'app_author', 'flag' => '1'])->first();
    if(!empty($setting))
        $app_author = $setting->nilai;
    
    return $app_author ;
}

function get_app_author_desc() {
    $ping_interval = 0;
    $setting = Setting::where(['variabel' => 'app_author_desc', 'flag' => '1'])->first();
    if(!empty($setting))
        $app_author_desc = $setting->nilai;
    
    return $app_author_desc ;
}

function get_app_logo_cert() {
    $ping_interval = 0;
    $setting = Setting::where(['variabel' => 'app_logo_cert', 'flag' => '1'])->first();
    if(!empty($setting))
        $app_logo_cert = $setting->nilai;
    
    return $app_logo_cert ;
}

function is_show_registration() {
    $is_show_registration = 0;
    $setting = Setting::where(['variabel' => 'is_show_registration', 'flag' => '1'])->first();
    if(!empty($setting))
        $is_show_registration = $setting->nilai;
    
    return $is_show_registration ;
}

function is_show_detail_hasil() {
    $is_show_detail_hasil = 0;
    $setting = Setting::where(['variabel' => 'is_show_detail_hasil', 'flag' => '1'])->first();
    if(!empty($setting))
        $is_show_detail_hasil = $setting->nilai;
    
    return $is_show_detail_hasil ;
}

function is_enable_tambah_mhs() {
    $is_enable_tambah_mhs = 0;
    $setting = Setting::where(['variabel' => 'is_enable_tambah_mhs', 'flag' => '1'])->first();
    if(!empty($setting))
        $is_enable_tambah_mhs = $setting->nilai;
    
    return $is_enable_tambah_mhs ;
}

function is_show_tata_tertib() {
    $is_show_tata_tertib = 0;
    $setting = Setting::where(['variabel' => 'is_show_tata_tertib', 'flag' => '1'])->first();
    if(!empty($setting))
        $is_show_tata_tertib = $setting->nilai;
    
    return $is_show_tata_tertib ;
}

function is_show_warning_saat_ujian() {
    $is_show_warning_saat_ujian = 0;
    $setting = Setting::where(['variabel' => 'is_show_warning_saat_ujian', 'flag' => '1'])->first();
    if(!empty($setting))
        $is_show_warning_saat_ujian = $setting->nilai;
    
    return $is_show_warning_saat_ujian ;
}

function is_show_membership() {
    $is_show_membership = 0;
    $setting = Setting::where(['variabel' => 'is_show_membership', 'flag' => '1'])->first();
    if(!empty($setting))
        $is_show_membership = $setting->nilai;
    
    return $is_show_membership ;
}

function is_show_paket() {
    $is_show_paket = 0;
    $setting = Setting::where(['variabel' => 'is_show_paket', 'flag' => '1'])->first();
    if(!empty($setting))
        $is_show_paket = $setting->nilai;
    
    return $is_show_paket ;
}

function getEloquentSqlWithBindings($query) : string
{
    return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
        return is_numeric($binding) ? $binding : "'{$binding}'";
    })->toArray());
}