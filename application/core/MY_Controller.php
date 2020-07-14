<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extended CI controller for application.
 *
 */

class MY_Controller extends CI_Controller {

	public function ajax($method = ''){
		/**
		 * CALL AJAX REQUEST
		 * PASSING PARAMETER ONLY USING POST
		 */
		if (!$this->input->is_ajax_request()) {
		   show_404();
		}
//		if (empty($method)) {
//			show_404();
//		}
		if(method_exists($this, '_'.$method)){
    		$this->{'_'.$method}();
    	}else{
			show_404();
		}
	}
	
	protected function _json($data, $encode = true)
	{
		if ($encode) $data = json_encode($data);
		$this->output->set_content_type('application/json')->set_output($data);
	}
	
	protected function _akses_dosen()
    {
        if ( !$this->ion_auth->in_group('dosen') ){
			show_error('Halaman ini khusus untuk dosen', 403, 'Akses Terlarang');
		}
    }

    protected function _akses_mahasiswa()
    {
        if ( !$this->ion_auth->in_group('mahasiswa') ){
			show_error('Halaman ini khusus untuk mahasiswa', 403, 'Akses Terlarang');
		}
    }
    
    protected function _akses_admin()
    {
        if ( !$this->ion_auth->in_group('admin') ){
			show_error('Halaman ini khusus untuk admin', 403, 'Akses Terlarang');
		}
    }
	
}
