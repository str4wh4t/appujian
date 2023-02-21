<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    private $user;

    public function __construct()
    {
        parent::__construct();
        if (! $this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->user = $this->ion_auth->user()->row();
    }

    public function index()
    {
        $user = $this->user;
        $user_groups = $this->ion_auth->get_users_groups($user->id)->result();
        $data = [
            'user' 			=> $user,
            'user_groups'	=> $user_groups,
            'judul'			=> 'Dashboard',
            'subjudul'		=> 'Data Aplikasi',
        ];

        if (is_admin()) {
            $box = [
                [
                    'box' 		=> 'olive',
                    'total' 	=> $this->dashboard->total('matkul'),
                    'title'		=> 'Materi Ujian',
                    'link'		=> 'matkul',
                    'icon'		=> 'object-ungroup',
                ],
                [
                    'box' 		=> 'olive',
                    'total' 	=> $this->dashboard->total('topik'),
                    'title'		=> 'Topik',
                    'link'		=> 'topik',
                    'icon'		=> 'object-group',
                ],
                [
                    'box' 		=> 'olive',
                    'total' 	=> $this->dashboard->total('mahasiswa'),
                    'title'		=> 'Peserta Ujian',
                    'link'		=> 'mahasiswa',
                    'icon'		=> 'user',
                ],
                [
                    'box' 		=> 'olive',
                    'total' 	=> $this->dashboard->total('bundle'),
                    'title'		=> 'Bundle',
                    'link'		=> 'soal/bundle_soal',
                    'icon'		=> 'folder-o',
                ],
                [
                    'box' 		=> 'olive',
                    'total' 	=> $this->dashboard->total('tb_soal'),
                    'title'		=> 'Soal',
                    'link'		=> 'soal',
                    'icon'		=> 'bookmark',
                ],
                [
                    'box' 		=> 'olive',
                    'total' 	=> $this->dashboard->total('m_ujian'),
                    'title'		=> 'Ujian',
                    'link'		=> 'ujian/master',
                    'icon'		=> 'book',
                ],
            ];
            $info_box = json_decode(json_encode($box), false);

            $data['info_box'] = $info_box;
        } elseif (in_group(KOORD_PENGAWAS_GROUP_ID) || in_group(PENGAWAS_GROUP_ID) || in_group(PENYUSUN_SOAL_GROUP_ID)) {
            $data['user'] = $user;
        } elseif (in_group(DOSEN_GROUP_ID)) {
            $data['dosen'] = Orm\Dosen_orm::where('nip', $user->username)->firstOrFail();
        } else {
            $data['mahasiswa'] = Orm\Mhs_orm::where('nim', $user->username)->firstOrFail(); // $this->dashboard->get_where('mahasiswa a', 'nim', $user->username, $join)->row();
        }

        if (APP_TYPE == 'tryout') {
            if (in_group(MHS_GROUP_ID)) {
                view('dashboard/tryout/mhs/index', $data);
            } else {
                view('dashboard/index', $data);
            }
        } else {
            view('dashboard/index', $data);
        }
    }
}
