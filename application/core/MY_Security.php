<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Security extends CI_Security
{
    public function __construct()
    {
        parent::__construct();
    }

    public function csrf_show_error()
    {
        header("Location: //{$_SERVER['HTTP_HOST']}/expired_page",TRUE,301);
        die;
    }
}
