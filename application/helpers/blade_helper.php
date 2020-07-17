<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Jenssegers\Blade\Blade;
use Illuminate\Database\Capsule\Manager as DB;

// Parameter pertama adalah nama view
// Parameter kedua adalah data dalam bentuk array
function view($view, $data = [])
{
    // Path folder views
    $viewDirectory = VIEWPATH;
    // Path folder cache
    $cacheDirectory = APPPATH . 'cache';
    
    $blade = new Blade($viewDirectory, $cacheDirectory);
    echo $blade->make($view, $data);
}

function csrf_name() {
	$csrf_name = "" ;
	$ci =& get_instance();
	$csrf_protection = $ci->config->item('csrf_protection');
	if($csrf_protection){
		$csrf_name = $ci->security->get_csrf_token_name();
	}
	return $csrf_name ;
}

function csrf_token() {
	$csrf_token = "" ;
	$ci =& get_instance();
	$csrf_protection = $ci->config->item('csrf_protection');
	if($csrf_protection){
		$csrf_token = $ci->security->get_csrf_hash();
	}
	return $csrf_token ;
}

function asset($asset_path) {
	return base_url() . $asset_path ;
}

function url($path) {
	return site_url() . $path ;
}

function flash_data($name){
    $ci =& get_instance();
    return !empty($ci->session->flashdata($name))?$ci->session->flashdata($name):null;
}

function session($key){
    $ci =& get_instance();
    return !empty($ci->session->userdata($key))?$ci->session->userdata($key):null;
}

function segment($number){
	$ci =& get_instance();
	return $ci->uri->segment($number);
}

/** UUID */
function uuid_create_from_integer($integer){
	$ci =& get_instance();
	$ci->load->library('Uuid_generator');
    return $ci->uuid_generator->create_to_uuid($integer);

}

function integer_read_from_uuid($uuid){
	$ci =& get_instance();
	$ci->load->library('Uuid_generator');
    return $ci->uuid_generator->read_from_uuid($uuid);

}


/** DB ELOQUENT */
function begin_db_trx(){
	$ci =& get_instance();
	$ci->db->trans_begin();
	DB::beginTransaction();
}

function rollback_db_trx(){
	$ci =& get_instance();
	$ci->db->trans_rollback();
    DB::rollBack();
}

function commit_db_trx(){
	$ci =& get_instance();
	$ci->db->trans_commit();
    DB::commit();
}

/** WEBSOCKET */
function ws_url(){
	$ci =& get_instance();
	return $ci->config->item('ws_url');
}
