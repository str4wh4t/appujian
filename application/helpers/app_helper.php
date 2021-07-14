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