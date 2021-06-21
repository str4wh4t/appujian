<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Orm\Users_orm;
use Orm\Membership_orm;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;
use Orm\Trx_payment_orm;
use Orm\Paket_orm;
use Illuminate\Database\Capsule\Manager as DB;

use GuzzleHttp\Client;

class Payment extends MY_Controller
{

	public function __construct(){
        parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		}


        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('mahasiswa'))
            show_404();


    }

    public function index(){
        show_404();
    }

    // public function checkout($membership_id, $user_id = null){

    //     $user = $this->ion_auth->user()->row();
    //     if($membership_id < $user->membership_id){
    //         show_error('Terjadi kesalahan pembelian', 500, 'Perhatian');
    //     }

    //     if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('mahasiswa'))
    //         show_404();

    //     if($this->ion_auth->in_group('mahasiswa')){
    //         $user_beli = Users_orm::findOrFail($user->id);
    //     }else{
    //         $user_beli = Users_orm::findOrFail($user_id);
    //     }
    //     try {
    //         begin_db_trx();
    //         $user_beli->membership_id = $membership_id ;
    //         $user_beli->save();

    //         $where = [
    //             'users_id' => $user_beli->id,
    //             'stts' => MEMBERSHIP_STTS_AKTIF,
    //         ];

    //         $membership_history_before = Membership_history_orm::where($where)->firstOrFail();
    //         $membership_history_before->stts = MEMBERSHIP_STTS_NON_AKTIF ;
    //         $membership_history_before->save();
            
    //         $membership = Membership_orm::findOrFail($membership_id);

    //         $sisa_kuota_latihan_soal = 0;
    //         $expired_at  = null;

    //         if($membership->is_limit_by_kuota)
    //             $sisa_kuota_latihan_soal = $membership->kuota_latian_soal ;

    //         if($membership->is_limit_by_durasi)
    //             $expired_at = date('Y-m-d', strtotime("+". $membership->durasi ." months", strtotime(date('Y-m-d'))));

    //         $where = [
    //             'users_id' => $user_beli->id,
    //             'membership_id' => $membership_id,
    //         ];

    //         $membership_count = Membership_history_orm::where($where)->get()->count();

    //         $membership_history = new Membership_history_orm();
    //         $membership_history->users_id = $user_beli->id;
    //         $membership_history->membership_id = $membership_id ;
    //         $membership_history->upgrade_ke = $membership_count++ ;
    //         $membership_history->sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal ;
    //         $membership_history->expired_at = $expired_at ;
    //         $membership_history->stts =  MEMBERSHIP_STTS_AKTIF ;
    //         $membership_history->save();

    //         commit_db_trx();


    //         $message_rootpage = [
    //             'header' => 'Selamat',
    //             'content' => 'Pembelian membership berhasil, sekarang anda berada didalam membership ' . strtoupper($membership->name),
    //             'type' => 'success'
    //         ];

    //         $this->session->set_flashdata('message_rootpage', $message_rootpage);

    //         redirect('/membership/list', 'refresh');

    //     }catch(Exception $e){
    //         rollback_db_trx();
    //         show_error($e->getMessage(), 500, 'Perhatian');
    //     }

    // }

    public function beli($m_or_p, $membership_or_paket_id, $user_id = null){

        if(!in_array($m_or_p, ['M', 'P']))
            show_404();

        $user = $this->ion_auth->user()->row();

        $membership_or_paket_id = integer_read_from_uuid($membership_or_paket_id);

        if($this->ion_auth->in_group('mahasiswa')){
            $user_beli = Users_orm::findOrFail($user->id);
        }else{
            $user_beli = Users_orm::findOrFail($user_id);
        }

        $item = null ;

        if($m_or_p == 'M'){
            $membership = Membership_orm::findOrFail($membership_or_paket_id);

            $is_valid_order_membership = is_valid_order_membership($membership->id, $user_beli->mhs) ;

            if(!$is_valid_order_membership){
                show_error('Terjadi kesalahan pembelian', 500, 'Perhatian');
            }

            $item = $membership;

        }
        
        if($m_or_p == 'P'){
            $paket = Paket_orm::findOrFail($membership_or_paket_id);

            // $is_valid_paket = false ;
            // if(empty($user_beli->mhs->paket_history()->where('paket_id', $paket->id)->first())){
            //     $is_valid_paket = true;
            // }

            // if(!$is_valid_paket){
            //     show_error('Terjadi kesalahan pembelian', 500, 'Perhatian');
            // }
            
            $item = $paket;
        }


        $additional_cost = [];
        $va_provider = [];


        if(APP_UDID){
            
            $this->load->library('verification_jwt');
            $this->load->library('sso_udid_adapter', [
                'property' => [
                    'client_id'         => APP_UDID_ID,
                    'client_secret'     => APP_UDID_SECRET,
                    'redirect_uri'      => url('auth/cek_login_udid'),
                    'scope'             => 'User.Read User.Payment.Read User.Payment.Write',
                    'authorization_url' => 'https://login.undip.id/oauth2/authorize/login',
                    'access_token_url'  => 'https://login.undip.id/oauth2/authorize/access_token',
                ],
                'settings' => [
            //		'verify' => false, /*default*/
                ],
                'debug' => [
                    'exception' => true, /*debug all exception*/
                    'token'     => false, /*debug token request*/
                    'response'  => false, /*debug all response non token*/
                ],
            ]);

            $token = $this->session->userdata('token_udid');

            try {
                $result = $this->sso_udid_adapter->request([
                    'method'      => 'POST',
                    'endpoint'    => APP_UDID_API . '/api-payment/api/init_payment',
                    'body_params' => [
                        'form_params' => [
                            'nominal' => $item->price,
                        ],
                    ],
                    'header_params' => [
            //			'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ]
                ], $token);
                
                $notif = json_decode($result);
    
            }catch (Exception $e) {
                show_error($e->getMessage(), 500, 'Perhatian');
            }
            
            if($notif->status != 'ok'){
                show_error('Kesalahan data pada srv udid', 500, 'Perhatian');
            }
            
            $additional_cost = $notif->payload->additional_cost ;
            $va_provider = $notif->payload->va_provider ;
        }

        $data['info'] = $m_or_p . $membership_or_paket_id;
        $data['item'] = $item;
        $data['additional_cost'] = $additional_cost;
        $data['va_provider'] = $va_provider;
        $data['user']   = $user_beli;

        view('payment/beli', $data);

    }

    protected function _snap(){
        
        $user = $this->ion_auth->user()->row();
        $info =  $this->input->post('info');
        $va_provider_id =  $this->input->post('va_provider_id');

        $user_beli = Users_orm::findOrFail($user->id);
            
        $gross_amount = 0;
        $nett_amount = 0;

        $keterangan = '';

        $item = null;
        
        if(substr($info, 0, 1) == 'M'){
            // JIKA PEMBELIAN MEMBERSHIP
            $membership_id = substr($info, 1);
            $membership = Membership_orm::findOrFail($membership_id);

            $is_valid_order_membership = is_valid_order_membership($membership->id, $user_beli->mhs) ;

            if(!$is_valid_order_membership){
                show_error('Terjadi kesalahan pembelian', 500, 'Perhatian');
            }

            $item = $membership;

            $gross_amount = $membership->price;
            $nett_amount = $membership->price;
            $keterangan = 'Pembelian membership ' . strtoupper(get_membership_text(substr($info[1], 1))) ;

        }

        if(substr($info, 0, 1) == 'P'){
            // JIKA PEMBELIAN PAKET
            $paket_id = substr($info, 1);
            $paket = Paket_orm::findOrFail($paket_id);

            // $is_valid_paket = false ;
            // if(empty($user_beli->mhs->paket_history()->where('paket_id', $paket_id)->first())){
            //     $is_valid_paket = true;
            // }

            // if(!$is_valid_paket){
            //     show_error('Terjadi kesalahan pembelian', 500, 'Perhatian');
            // }

            $item = $paket;

            $gross_amount = $paket->price;
            $nett_amount = $paket->price;
            $keterangan = 'Pembelian paket ' . strtoupper($paket->name) ;

        }
        
        
        $order_number_pattern = $user_beli->username . '-' . $info . '-' . date('ymd') ;

        $trx_payment_existing_count = Trx_payment_orm::where(DB::raw('substr(order_number, 1, 22)'), '=', $order_number_pattern)->get()->count();

        $order_number = $order_number_pattern . '-' . ($trx_payment_existing_count++);

        $snapToken = null ;

        $status = 'ok' ;

        $msg = null ;

        if(APP_UDID){

            if(empty($va_provider_id)){
                show_error('VA provider tdk valid', 500, 'Perhatian');
            }

            $this->load->library('verification_jwt');
            $this->load->library('sso_udid_adapter', [
                'property' => [
                    'client_id'         => APP_UDID_ID,
                    'client_secret'     => APP_UDID_SECRET,
                    'redirect_uri'      => url('auth/cek_login_udid'),
                    'scope'             => 'User.Read User.Payment.Read User.Payment.Write',
                    'authorization_url' => 'https://login.undip.id/oauth2/authorize/login',
                    'access_token_url'  => 'https://login.undip.id/oauth2/authorize/access_token',
                ],
                'settings' => [
            //		'verify' => false, /*default*/
                ],
                'debug' => [
                    'exception' => true, /*debug all exception*/
                    'token'     => false, /*debug token request*/
                    'response'  => false, /*debug all response non token*/
                ],
            ]);
            
            $token = $this->session->userdata('token_udid');

            try {
                $result = $this->sso_udid_adapter->request([
                    'method'      => 'POST',
                    'endpoint'    => APP_UDID_API . '/api-payment/api/init_payment',
                    'body_params' => [
                        'form_params' => [
                            'nominal' => $gross_amount,
                        ],
                    ],
                    'header_params' => [
            //			'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ]
                ], $token);
                
                $notif = json_decode($result);
    
            }catch (Exception $e) {
                show_error($e->getMessage(), 500, 'Perhatian');
            }
            
            if($notif->status != 'ok'){
                show_error('Kesalahan data pada srv udid', 500, 'Perhatian');
            }
            
            $additional_cost = $notif->payload->additional_cost ;
            $va_provider = $notif->payload->va_provider ;

            if(!empty($va_provider)){
                $vp_list = [];
                foreach($va_provider as $vp){
                    $vp_list[] = $vp->virtual_account_provider_code;
                }
                if(!in_array($va_provider_id, $vp_list)){
                    show_error('VP ID salah');
                }
            }

            if(!empty($additional_cost)){
                foreach($additional_cost as $add_cost){
                    $gross_amount = (int)$gross_amount + (int)$add_cost->nominal;
                }
            }

            try {
                $result = $this->sso_udid_adapter->request([
                    'method'      => 'POST',
                    'endpoint'    => APP_UDID_API . '/api-payment/api/set_payment',
                    'body_params' => [
                        'form_params' => [
                            'nominal' => $gross_amount,
                            'invoice_code' => $order_number,
                            'description' => $keterangan,
                            'customer_id' => $user_beli->sso_udid_id,
                            'virtual_account_provider_code' => $va_provider_id,
                            'occurance' => 'once',
                            'installment' => 0,
                            'item_detail' => json_encode([
                                [
                                    'item' => $item->name,
                                    'amount' => 1,
                                    'nominal' => $item->price,
                                ]
                            ]),
                        ],
                    ],
                    'header_params' => [
            //			'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ]
                ], $token);
                
                $notif = json_decode($result);
    
                if($notif->status != 'ok'){
                    throw new Exception('SERVER UDID ERROR');
                }

            }catch (Exception $e) {
                show_error($e->getMessage(), 500, 'Perhatian');
            }
            

            // [order_id] => CKPWG8DQY00019MY60O7Q0YGY
            // [serialized_id] => CKPWG8DQY00019MY60O7Q0YGY.1623665022
            // [va_code] => 12345667
            // [nominal] => 202500

            // vdebug($notif);

            $notif_payment = (object)[
                'order_id' => $order_number,
                'transaction_status' => $notif->payload->status,
                'transaction_time' => $notif->payload->transaction_time,
                'gross_amount' => $notif->payload->nominal,
                'nett_amount' => $nett_amount,
                'order_id_udid' => $notif->payload->order_id,
            ];
            
            $this->load->model('payment_model');
            $return = $this->payment_model->exec_payment($notif_payment, 'udid_api');

            if($return != 'SUCCESS'){
                $status = 'ko';
                $msg = $return;
            }

        }else{

            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = true;
            
            $params = array(
                'transaction_details' => [
                    'order_id' => $order_number,
                    'gross_amount' => $gross_amount,
                ],
                'customer_details' => [
                    'first_name' => $user_beli->first_name,
                    'last_name' => $user_beli->last_name,
                    'email' => $user_beli->email,
                    'phone' => $user_beli->phone,
                ],
                // 'callbacks' => [
                //   'finish' => url('payment/history'),
                // ]
            );
                
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        }

        $this->_json(['token' => $snapToken, 'status' => $status, 'msg' => $msg]);
    }

    public function history($user_id = null){
        $user = $this->ion_auth->user()->row();

        if($this->ion_auth->in_group('mahasiswa'))
            $user = Users_orm::findOrFail($user->id);
        else
            $user = Users_orm::findOrFail($user_id);

        
        $trx_payment_list = $user->mhs->trx_payment->sortByDesc('id');

        $data['trx_payment_list']   = $trx_payment_list;

        view('payment/history', $data);

    }

    protected function _status(){
        $order_number = $this->input->post('id');

        if(APP_UDID){

            $trx_payment = Trx_payment_orm::where('order_number', $order_number)->firstOrFail();

            $this->load->library('verification_jwt');
            $this->load->library('sso_udid_adapter', [
                'property' => [
                    'client_id'         => APP_UDID_ID,
                    'client_secret'     => APP_UDID_SECRET,
                    'redirect_uri'      => url('auth/cek_login_udid'),
                    'scope'             => 'User.Read User.Payment.Read User.Payment.Write',
                    'authorization_url' => 'https://login.undip.id/oauth2/authorize/login',
                    'access_token_url'  => 'https://login.undip.id/oauth2/authorize/access_token',
                ],
                'settings' => [
            //		'verify' => false, /*default*/
                ],
                'debug' => [
                    'exception' => true, /*debug all exception*/
                    'token'     => false, /*debug token request*/
                    'response'  => false, /*debug all response non token*/
                ],
            ]);
            
            $token = $this->session->userdata('token_udid');

            try {
                $result = $this->sso_udid_adapter->request([
                    'method'      => 'POST',
                    'endpoint'    => APP_UDID_API . '/api-payment/api/check_payment',
                    'body_params' => [
                        'form_params' => [
                            'order_id' => $trx_payment->order_id_udid,
                        ],
                    ],
                    'header_params' => [
            //			'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ]
                ], $token);
                
                $notif = json_decode($result);

                if($notif->status == 'ok'){
                    $bank = $notif->payload->va_provider;
                    $va_number = $notif->payload->va_code;
                    $status = $notif->payload->status;
                    $payment_type = $notif->payload->transaction_type;
                    $order_id = $notif->payload->invoice_code; // $order_id = $this->input->post('order_id');
                    $transaction_time = $notif->payload->created_at;
                    $gross_amount = $notif->payload->nominal;

                }else{
                    throw new Exception('SERVER UDID ERROR');
                }

    
            }catch (Exception $e) {
                show_error($e->getMessage(), 500, 'Perhatian');
            }

            // vdebug($notif);


        }else{
            $client = new Client();
            $res = $client->request('GET', MIDTRANS_API_URL . $order_number . '/status', [
                'auth' => [MIDTRANS_SERVER_KEY, '']
            ]);

            // $res = $client->get(MIDTRANS_API_URL . $order_number . '/status', [
            //     'auth' => [
            //         MIDTRANS_SERVER_KEY, 
            //         ''
            //     ]
            // ]);

            // $credentials = base64_encode(MIDTRANS_SERVER_KEY . ':');
            // $res = $client->get(MIDTRANS_API_URL . $order_number . '/status', [
            //     'Authorization' => ['Basic '.$credentials]
            // ]);

            $notif = $res->getBody()->getContents();

            // vdebug($notif);

            $va_number = null ;
            $bank = 'lainnya' ;
    
            $notif = json_decode($notif);
    
            if(isset($notif->va_numbers)){
                $bank = $notif->va_numbers[0]->bank ;
                $va_number = $notif->va_numbers[0]->va_number ;
            }
    
            if(isset($notif->biller_code)){
                if($notif->biller_code == '70012'){
                    $bank = 'mandiri ('. $notif->biller_code .')';
                    $va_number = $notif->bill_key ;
                }
            }
    
            if($notif->payment_type == 'cstore'){
                if(isset($notif->payment_code)){
                    $bank = $notif->store;
                    $va_number = $notif->payment_code;
                }
            }
    
            if($notif->payment_type == 'qris'){
                // if(isset($notif->payment_code)){
                    $bank = $notif->acquirer;
                    // $va_number = $notif->payment_code;
                // }
            }

            $status = $notif->transaction_status;
            $payment_type = $notif->payment_type;
            $order_id = $notif->order_id;
            $transaction_time = $notif->transaction_time;
            $gross_amount = $notif->gross_amount;

        }

		// if(isset($notif->permata_va_number)){
		// 	$bank = 'permata';
		// 	$va_number = $notif->permata_va_number ;
		// }

        $data['bank'] = strtoupper($bank);
        $data['va_number'] = strtoupper($va_number);
        $data['status'] = strtoupper($status);
        $data['payment_type'] = strtoupper($payment_type);
        $data['order_id'] = $order_id;
        $data['transaction_time'] = $transaction_time;
        $data['gross_amount'] = $gross_amount;

        $this->_json($data);
    }

    public function order_list(){
        $this->_akses_admin();

        view('payment/order_list');
        
    }

    protected function _do_exec_payment(){
        
        $order_number = $this->input->post('id');
        $payment = Trx_payment_orm::where('order_number', $order_number)->firstOrFail();
        if(!$payment->stts){

            $this->load->model('payment_model');

            $order_number = $payment->order_number;

            $notif = null ;

            if(APP_UDID){

                $trx_payment = Trx_payment_orm::where('order_number', $order_number)->firstOrFail();

                $this->load->library('verification_jwt');
                $this->load->library('sso_udid_adapter', [
                    'property' => [
                        'client_id'         => APP_UDID_ID,
                        'client_secret'     => APP_UDID_SECRET,
                        'redirect_uri'      => url('auth/cek_login_udid'),
                        'scope'             => 'User.Read User.Payment.Read User.Payment.Write',
                        'authorization_url' => 'https://login.undip.id/oauth2/authorize/login',
                        'access_token_url'  => 'https://login.undip.id/oauth2/authorize/access_token',
                    ],
                    'settings' => [
                //		'verify' => false, /*default*/
                    ],
                    'debug' => [
                        'exception' => true, /*debug all exception*/
                        'token'     => false, /*debug token request*/
                        'response'  => false, /*debug all response non token*/
                    ],
                ]);
                
                $token = $this->session->userdata('token_udid');

                try {
                    $result = $this->sso_udid_adapter->request([
                        'method'      => 'POST',
                        'endpoint'    => APP_UDID_API . '/api-payment/api/check_payment',
                        'body_params' => [
                            'form_params' => [
                                'order_id' => $trx_payment->order_id_udid,
                            ],
                        ],
                        'header_params' => [
                //			'Content-Type' => 'application/json',
                            'Accept'       => 'application/json',
                        ]
                    ], $token);
                    
                    $notif_udid = json_decode($result);

                    if($notif_udid->status == 'ok'){
                        $notif = (object)[
                            'bank' => $notif_udid->payload->va_provider,
                            'va_number' => $notif_udid->payload->va_code,
                            'transaction_status' => $notif_udid->payload->status,
                            'payment_type' => $notif_udid->payload->transaction_type,
                            'order_id' => $notif_udid->payload->invoice_code, // $order_id = $this->input->post('order_id'),
                            'transaction_time' => $notif_udid->payload->created_at,
                            'gross_amount' => $notif_udid->payload->nominal,
                        ];

                    }else{
                        throw new Exception('SERVER UDID ERROR');
                    }
        
                }catch (Exception $e) {
                    show_error($e->getMessage(), 500, 'Perhatian');
                }

            }else{
                $client = new Client();
            
                $res = $client->request('GET', MIDTRANS_API_URL . $order_number . '/status', [
                    'auth' => [MIDTRANS_SERVER_KEY, '']
                ]);

                $notif = $res->getBody()->getContents();
                $notif = json_decode($notif);

            }

            $log_status = $this->payment_model->exec_payment($notif, 'admin');

            $this->_json(['log_status' => $log_status]);
        }
    }

    protected function _data_order_list(){
        $config = [
			'host'     => $this->db->hostname,
			'port'     => $this->db->port,
			'username' => $this->db->username,
			'password' => $this->db->password,
			'database' => $this->db->database,
		];

		$this->db->select('a.order_number, a.keterangan, a.tgl_order, a.tgl_bayar, a.jml_bayar, a.stts, "AKSI" AS aksi');
		$this->db->from('trx_payment AS a');

        $dt = new Datatables(new MySQL($config));

		$query = $this->db->get_compiled_select(); 

        $dt->query($query);

        $dt->edit('stts', function ($data) {
            $info = null ;
            if($data['stts'] == PAYMENT_ORDER_TELAH_DIPROSES)
                $info = '<span class="text-success"><b>Sudah Dibayar</b></span>' ;
            elseif($data['stts'] == PAYMENT_ORDER_BELUM_DIPROSES)
                $info = '<span class="text-danger"><b>Belum Dibayar</b></span>' ;
            elseif($data['stts'] == PAYMENT_ORDER_EXPIRED)
                $info = '<span class="text-warning"><b>Expired</b></span>' ;

            return $info;
        });

        $dt->edit('aksi', function ($data) {
			return '<button class="btn btn-outline-warning btn-sm bayar" data-stts="'. $data['stts'] .'" data-id="'. $data['order_number'] .'"><i class="fa fa-eye"></i> Cek</button>';
		});

        $this->_json($dt->generate(), false);

    }

}