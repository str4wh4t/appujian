<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extended CI controller for application.
 *
 */

class MY_Controller extends CI_Controller
{
    public function ajax($method = '')
    {
        /**
         * CALL AJAX REQUEST
         * PASSING PARAMETER ONLY USING POST
         */

        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (method_exists($this, '_' . $method)) {
            $this->{'_' . $method}();
        } else {
            show_404();
        }
    }

    protected function _json($data, $encode = true, $status_header = 200)
    {
        if ($encode) {
            $data = json_encode($data);
        }
        $this->output->set_content_type('application/json')->set_output($data)->set_status_header($status_header);
    }

    protected function _akses_admin()
    {
        if ( !is_admin() ) {
            show_error('Halaman ini khusus untuk admin', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_dosen()
    {
        if ( !in_group(DOSEN_GROUP_ID) ) {
            show_error('Halaman ini khusus untuk dosen', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_koord_pengawas()
    {
        if ( !in_group(KOORD_PENGAWAS_GROUP_ID) ) {
            show_error('Halaman ini khusus untuk koord pengawas', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_pengawas()
    {
        if ( !in_group(PENGAWAS_GROUP_ID) ) {
            show_error('Halaman ini khusus untuk pengawas', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_penyusun_soal()
    {
        if ( !in_group(PENYUSUN_SOAL_GROUP_ID) ) {
            show_error('Halaman ini khusus untuk penyusun soal', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_mahasiswa()
    {
        if ( !in_group(MHS_GROUP_ID) ) {
            show_error('Halaman ini khusus untuk mahasiswa', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_selain_mahasiswa()
    {
        if ( in_group(MHS_GROUP_ID) ) {
            show_error('Halaman ini khusus untuk selain mahasiswa', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_selain_mahasiswa_dan_penyusun_soal()
    {
        if ( in_group(MHS_GROUP_ID) || in_group(PENYUSUN_SOAL_GROUP_ID) ) {
            show_error('Halaman ini khusus untuk selain mahasiswa dan penyusun soal', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_admin_dan_dosen()
    {
        if (!is_admin() && !in_group(DOSEN_GROUP_ID)) {
            show_error('Halaman ini khusus untuk selain admin dan dosen', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_admin_dan_koord_pengawas()
    {
        if (!is_admin() && !in_group(KOORD_PENGAWAS_GROUP_ID)) {
            show_error('Halaman ini khusus untuk selain admin dan koord pengawas', 403, 'Akses Terlarang');
        }
    }

    protected function _akses_admin_dosen_dan_penyusun_soal()
    {
        if (!is_admin() && !in_group(DOSEN_GROUP_ID) && !in_group(PENYUSUN_SOAL_GROUP_ID)) {
            show_error('Halaman ini khusus untuk selain admin, dosen dan penyusun soal', 403, 'Akses Terlarang');
        }
    }
}
