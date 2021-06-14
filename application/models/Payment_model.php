<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Mhs_orm;
use Orm\Mhs_matkul_orm;
use Orm\Mhs_ujian_orm;
use Orm\Trx_payment_orm;
use Orm\Paket_orm;
use Orm\Trx_midtrans_orm;
use Orm\Membership_orm;
use Orm\Membership_history_orm;
use Orm\Paket_history_orm;


use Carbon\Carbon;
use GuzzleHttp\Client;

class Payment_model extends CI_Model {


    public function exec_payment(object $notif, $trx_by):string {

        /**
         * untuk logika disini jika $notif->transaction_status adalah pending maka yang meng exec
         * fungsi ini adalah dari midtrans, admin hanya di logic untuk mentrigger untuk exec 
         * $notif->transaction_status == settlement
        */

        try{
			begin_db_trx();

			$info = explode('-', $notif->order_id);
			$username = $info[0] ; // 
			$mhs = Mhs_orm::where('nim', $username)->firstOrFail();

			$now = Carbon::now()->toDateTimeString();

            $nett_amount = $notif->gross_amount ;
            if(APP_UDID){
                // NET AMOUNT HANYA ADA DI UDID, KRN UDID MENAMBAHKAN ADD FEE
                $nett_amount = $notif->nett_amount;
            }

			if($notif->transaction_status == 'pending'){

				// JIKA TERJADI PEMESANAN

				$trx_payment = Trx_payment_orm::where('order_number', $notif->order_id)->first();

				if(empty($trx_payment)){
					$trx_payment = new Trx_payment_orm();
					$trx_payment->mahasiswa_id = $mhs->id_mahasiswa;
					$trx_payment->order_number = $notif->order_id;
					$trx_payment->stts = PAYMENT_ORDER_BELUM_DIPROSES;
					$trx_payment->tgl_order = $notif->transaction_time;
					$trx_payment->jml_bayar = $notif->gross_amount;
                    $trx_payment->jml_bayar_nett = $nett_amount;

					$keterangan = '';
					if(substr($info[1], 0, 1) == 'M'){
						$keterangan = 'Pembelian membership ' . strtoupper(get_membership_text(substr($info[1], 1))) ;
					}

					if(substr($info[1], 0, 1) == 'P'){
						$paket = Paket_orm::findOrFail(substr($info[1], 1));
						$keterangan = 'Pembelian paket ' . strtoupper($paket->name) ;
					}

					$trx_payment->keterangan = $keterangan;
                    $trx_payment->trx_by = $trx_by;
                    $trx_payment->order_id_udid = $notif->order_id_udid;

					$trx_payment->save();
				}

                //[START] LOGIC UNTUK EXPIRE KAN ORDER SEBELUMNYA

				$term = null ;
				if(substr($info[1], 0, 1) == 'M'){
                    // SET EXPIRE UNTUK TRX MIDTRANS PENDING SEBELUMNYA JIKA TRX TSB UNTUK MEMBERSHIP APAPUN
					$term = substr($notif->order_id, 0, 14); // SAMPE HURUF M, contoh : 210423223442-M
				}
					
				if(substr($info[1], 0, 1) == 'P'){
                    // SET EXPIRE UNTUK TRX MIDTRANS PENDING SEBELUMNYA JIKA TRX TSB UNTUK PAKET YANG SAMA
					$term = $info[0] . '-' . $info[1]; // SETALAH PAKET ID, contoh : 210423223442-P2
				}

                
                if(PAYMENT_METHOD == 'midtrans'){
                    
                    $trx_midtrans_before = Trx_midtrans_orm::where('transaction_status', 'pending')
                                                        ->where('order_id', 'like', $term . '%')
                                                        ->where('is_expire_processed', 0)
                                                        ->get();

                    if($trx_midtrans_before->isNotEmpty()){
                        $client = new Client();
                        foreach($trx_midtrans_before AS $trx){
                            $order_number = $trx->order_id;
                            $client->request('POST', MIDTRANS_API_URL . $order_number . '/expire', [
                                'auth' => [MIDTRANS_SERVER_KEY, '']
                            ]);
                            $trx->is_expire_processed = 1;
                            $trx->save();
                            // echo $res->getBody()->getContents(); die;
                        }
                    }
                }

                

                // SET EXPIRE FOR TRX_PAYMENT 

                $trx_payment_before = Trx_payment_orm::where('stts', PAYMENT_ORDER_BELUM_DIPROSES)
                                                        ->where('order_number', 'like', $term . '%')
                                                        ->where('order_number', '!=', $notif->order_id)
                                                        ->get();
                
                if($trx_payment_before->isNotEMpty()){

                    if(APP_UDID){
                        // JIKA APP_UDID
    
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
    
    
                    }

                    foreach($trx_payment_before as $trx){
                        $trx->stts = PAYMENT_ORDER_EXPIRED;
						$trx->save();

                        if(APP_UDID){
                            // LANJUTAN DARI LOGIC APP_UDID DIATAS
                            try {
                                $result = $this->sso_udid_adapter->request([
                                    'method'      => 'POST',
                                    'endpoint'    => APP_UDID_API . '/api-payment/api/cancel_payment',
                                    'body_params' => [
                                        'form_params' => [
                                            'order_id' => $trx->order_id_udid,
                                        ],
                                    ],
                                    'header_params' => [
                            //			'Content-Type' => 'application/json',
                                        'Accept'       => 'application/json',
                                    ]
                                ], $token);
                    
                            }catch (Exception $e) {
                                show_error($e->getMessage(), 500, 'Perhatian');
                            }
                        }
                    }
                }

                //[END] LOGIC UNTUK EXPIRE KAN ORDER SEBELUMNYA

			}
			
			if ($notif->transaction_status == 'settlement'){

				// JIKA TERJADI PEMBAYARAN

				$trx_payment = Trx_payment_orm::where('order_number', $notif->order_id)->where('stts', PAYMENT_ORDER_BELUM_DIPROSES)->first();

                if(!empty($trx_payment)){
                // if(!empty($trx_payment)){

                    //[START] JIKA ORDER BELUM DI PROSES

                    $membership_history_id = null ;
                    $paket_history_id = null ;
    
                    if(substr($info[1], 0, 1) == 'M'){
                        // JIKA PEMBELIAN MEMBERSHIP
                        $membership_id = substr($info[1], 1);
    
                        $membership = Membership_orm::findOrFail($membership_id);
            
                        $where = [
                            'mahasiswa_id' => $mhs->id_mahasiswa,
                            'stts' => MEMBERSHIP_STTS_AKTIF,
                        ];
                        $membership_history_before = Membership_history_orm::where($where)->first();
                        $membership_expiration_date = date('Y-m-d', strtotime("-1 days", strtotime(date('Y-m-d'))));
    
                        if(!empty($membership_history_before)){
                            $membership_history_before->stts = MEMBERSHIP_STTS_NON_AKTIF ;
    
                            $membership_expiration_date = !empty($membership_history_before->expired_at) ? $membership_history_before->expired_at : $membership_expiration_date;
                            
                            $membership_history_before->save();
                        }
            
                        $where = [
                            'mahasiswa_id' => $mhs->id_mahasiswa,
                            // 'membership_id' => $membership->id,
                        ];
                
                        $membership_count = Membership_history_orm::where($where)->get()->count();
    
                        $today = date('Y-m-d');
    
                        if($today > $membership_expiration_date){
                            $membership_expiration_date = date('Y-m-d', strtotime("+". $membership->durasi ." months", strtotime(date('Y-m-d'))));
                        }else{
                            $membership_expiration_date = date('Y-m-d', strtotime("+". $membership->durasi ." months", strtotime($membership_expiration_date)));
                        }
                
                        $membership_history = new Membership_history_orm();
                        $membership_history->mahasiswa_id = $mhs->id_mahasiswa;
                        $membership_history->membership_id = $membership_id ;
                        $membership_history->upgrade_ke = $membership_count++ ;
                        // $membership_history->sisa_kuota_latihan_soal = $membership_sisa_kuota_latihan_soal ;
                        $membership_history->expired_at = $membership_expiration_date ;
                        $membership_history->stts =  MEMBERSHIP_STTS_AKTIF ;
                        $membership_history->save();
            
                        $membership_history_id = $membership_history->id;
    
                        // ASIGN MEMBERSHIP KE PAKET BONUS NYA 
                        $paket_bonus_membership  = get_paket_bonus_membership($membership) ;
                        if(!empty($paket_bonus_membership)){
                            $matkul_ids_exist = Mhs_matkul_orm::where([
                                                                    'mahasiswa_id' => $mhs->id_mahasiswa
                                                                ])->pluck('matkul_id')->toArray();
						    $ujian_ids_exist = Mhs_ujian_orm::where([
                                                                    'mahasiswa_id' => $mhs->id_mahasiswa
                                                                ])->pluck('ujian_id')->toArray();
                            foreach($paket_bonus_membership as $paket){
    
                                $where = [
                                    'mahasiswa_id' => $mhs->id_mahasiswa,
                                    'paket_id' => $paket->id,
                                    'stts' => PAKET_STTS_AKTIF,
                                ];
            
                                $paket_history_before = Paket_history_orm::where($where)->first();
            
                                if(!empty($paket_history_before)){
                                    $paket_history_before->stts = PAKET_STTS_NON_AKTIF ;
                                    $paket_history_before->save();
                                }
    
                                $where = [
                                    'mahasiswa_id' => $mhs->id_mahasiswa,
                                    'paket_id' => $paket->id,
                                ];
                        
                                $paket_count = Paket_history_orm::where($where)->get()->count();
    
                                $paket_history = new Paket_history_orm();
                                $paket_history->mahasiswa_id = $mhs->id_mahasiswa;
                                $paket_history->paket_id = $paket->id ;
                                $paket_history->upgrade_ke = $paket_count++ ;
                                $paket_history->stts =  PAKET_STTS_AKTIF ;
                                $paket_history->save();
    
                                // foreach($paket->matkul as $matkul){
                                //     $sisa_kuota_latihan_soal = $paket->kuota_latihan_soal ;
                                //     if(empty($mhs->mhs_matkul()->where('matkul_id', $matkul->id_matkul)->first())){ 
                                //         // CHEK MHS SUDAH DIASIGN MATKUL APA BELUM, BISA JADI KEMUNGKINAN MATKUL TSB DI PILIH DI PAKET YG LAIN
                                //         $mhs_matkul_orm = new Mhs_matkul_orm();
                                //     }else{
                                //         $mhs_matkul_orm = $mhs->mhs_matkul()->where('matkul_id', $matkul->id_matkul)->first();
                                //         $sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal + $mhs_matkul_orm->sisa_kuota_latihan_soal ;
                                //     }
                                //     $mhs_matkul_orm->mahasiswa_id = $mhs->id_mahasiswa;
                                //     $mhs_matkul_orm->matkul_id = $matkul->id_matkul;
                                //     $mhs_matkul_orm->sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal ;
                                //     $mhs_matkul_orm->save();
    
                                //     // [START] JIKA UJIAN SOURCE DARI MATERI
                                //     if($matkul->m_ujian->isNotEmpty()){
                                //         $ujian_existing_ids = $mhs->mhs_ujian()->pluck('ujian_id')->toArray();
                                //         $ujian_ids = $matkul->m_ujian()->pluck('id_ujian')->toArray();
                                //         $ujian_ids_insert = array_diff($ujian_ids, $ujian_existing_ids);
                                //         if(!empty($ujian_ids_insert)){
                                //             $insert = [];
                                //             foreach($ujian_ids_insert as $ujian_id){
                                //                 $insert[] = [
                                //                     'mahasiswa_id' => $mhs->id_mahasiswa,
                                //                     'ujian_id' => $ujian_id,
                                //                     'created_at' => $now,
                                //                 ];
                                //             }
                                //             Mhs_ujian_orm::insert($insert);
                                //         }
                                //     }
                                //     // [END] JIKA UJIAN SOURCE DARI MATERI
    
                                //     // [START] JIKA UJIAN SOURCE DARI BUNDLE
                                //     $existing_mhs_ujian = $mhs->mhs_ujian()->pluck('ujian_id')->toArray();
    
                                //     if($matkul->m_ujian_enable->isNotEmpty()){
                                //         $mhs_ujian_ids = $matkul->m_ujian_enable()->pluck('ujian_id')->toArray();
                                //         $mhs_ujian_ids_insert = array_diff($mhs_ujian_ids, $existing_mhs_ujian);
                                //         if(!empty($mhs_ujian_ids_insert)){
                                //             $insert = [];
                                //             foreach($mhs_ujian_ids_insert as $m_ujian_id){
                                //                 $insert[] = [
                                //                     'mahasiswa_id' => $mhs->id_mahasiswa,
                                //                     'ujian_id'	=> $m_ujian_id,
                                //                     'created_at' => $now,
                                //                 ];
                                //             }
                                //             Mhs_ujian_orm::insert($insert);
                                //         }
                                //     }
                                //     // [END] JIKA UJIAN SOURCE DARI BUNDLE
                                // }

                                if($paket->m_ujian->isNotEmpty()){
                                    $ujian_ids = $paket->m_ujian()->pluck('id_ujian')->toArray();
                                    $insert = [];
                                    foreach($ujian_ids as $ujian_id){
                                        if(!in_array($ujian_id, $ujian_ids_exist)){
                                            $ujian_ids_exist[] = $ujian_id;
                                            $insert[] = [
                                                'mahasiswa_id' => $mhs->id_mahasiswa,
                                                'ujian_id' => $ujian_id,
                                                'sisa_kuota_latihan_soal' => $paket->kuota_latihan_soal,
                                                'created_at' => $now,
                                            ];
                                        }else{
                                            // JIKA SUDAH ADA MAKA SISA KUOTA LATIHAN SOAL DITAMBAHKAN
                                            $mhs_ujian_exist = Mhs_ujian_orm::where([
                                                'mahasiswa_id' => $mhs->id_mahasiswa,
                                                'ujian_id' => $ujian_id,
                                            ])->first();
                                            $kuota_latihan_soal_exist = $mhs_ujian_exist->sisa_kuota_latihan_soal;
                                            $mhs_ujian_exist->sisa_kuota_latihan_soal = $kuota_latihan_soal_exist + $paket->kuota_latihan_soal;
                                            $mhs_ujian_exist->save();
                                        }
                                    }
                                    if(!empty($insert))
                                        Mhs_ujian_orm::insert($insert);
                                    
                                    foreach($paket->m_ujian as $m_ujian){
                                        if(!empty($m_ujian->matkul)){
                                            // [START] JIKA UJIAN SOURCE DARI MATERI
                                            if(!in_array($m_ujian->matkul->id_matkul, $matkul_ids_exist)){
                                                $matkul_ids_exist[] = $m_ujian->matkul->id_matkul;
                                                $mhs_matkul_orm = new Mhs_matkul_orm();
                                                $mhs_matkul_orm->mahasiswa_id = $mhs->id_mahasiswa;
                                                $mhs_matkul_orm->matkul_id = $m_ujian->matkul->id_matkul;
                                                $mhs_matkul_orm->sisa_kuota_latihan_soal = 0 ;
                                                $mhs_matkul_orm->save();
                                            }
                                            // [END] JIKA UJIAN SOURCE DARI MATERI
                                        }
                                        if($m_ujian->matkul_enable->isNotEmpty()){
                                            // [START] JIKA UJIAN SOURCE DARI BUNDLE
                                            $insert = [];
                                            foreach($m_ujian->matkul_enable as $matkul){
                                                if(!in_array($matkul->id_matkul, $matkul_ids_exist)){
                                                    $matkul_ids_exist[] = $matkul->id_matkul;
                                                    $insert[] = [
                                                        'mahasiswa_id' => $mhs->id_mahasiswa,
                                                        'matkul_id' => $matkul->id_matkul,
                                                        'sisa_kuota_latihan_soal' => 0,
                                                        'created_at' => $now,
                                                    ];
                                                }
                                            }
                                            if(!empty($insert))
                                                Mhs_matkul_orm::insert($insert);
                                            // [END] JIKA UJIAN SOURCE DARI BUNDLE
                                        }
                                    }
                                }
                            }
                        }
            
                    }
                    
                    if(substr($info[1], 0, 1) == 'P'){
                        // JIKA PEMBELIAN PAKET
                        $paket_id = substr($info[1], 1);
    
                        $paket = Paket_orm::findOrFail($paket_id);
            
                        $where = [
                            'mahasiswa_id' => $mhs->id_mahasiswa,
                            'paket_id' => $paket->id,
                            'stts' => PAKET_STTS_AKTIF,
                        ];
    
                        $paket_history_before = Paket_history_orm::where($where)->first();
    
                        if(!empty($paket_history_before)){
                            $paket_history_before->stts = PAKET_STTS_NON_AKTIF ;
                            $paket_history_before->save();
                        }
            
                        $where = [
                            'mahasiswa_id' => $mhs->id_mahasiswa,
                            'paket_id' => $paket->id,
                        ];
                
                        $paket_count = Paket_history_orm::where($where)->get()->count();
                
                        $paket_history = new Paket_history_orm();
                        $paket_history->mahasiswa_id = $mhs->id_mahasiswa;
                        $paket_history->paket_id = $paket->id ;
                        $paket_history->upgrade_ke = $paket_count++ ;
                        $paket_history->stts =  PAKET_STTS_AKTIF ;
                        $paket_history->save();
            
                        $paket_history_id = $paket_history->id;

                        $matkul_ids_exist = Mhs_matkul_orm::where([
                                                                'mahasiswa_id' => $mhs->id_mahasiswa
                                                            ])->pluck('matkul_id')->toArray();
                        $ujian_ids_exist = Mhs_ujian_orm::where([
                                                                'mahasiswa_id' => $mhs->id_mahasiswa
                                                            ])->pluck('ujian_id')->toArray();
    
                        // // ASSIGN USER TO MHS_MATKUL BERDASARKAN PAKET DIBELI
                        // foreach($paket->matkul as $matkul){
                        //     $sisa_kuota_latihan_soal = $paket->kuota_latihan_soal ;
                        //     if(empty($mhs->mhs_matkul()->where('matkul_id', $matkul->id_matkul)->first())){ 
                        //         // CHEK MHS SUDAH DIASIGN MATKUL APA BELUM, BISA JADI KEMUNGKINAN MATKUL TSB DI PILIH DI PAKET YG LAIN
                        //         $mhs_matkul_orm = new Mhs_matkul_orm();
                        //     }else{
                        //         $mhs_matkul_orm = $mhs->mhs_matkul()->where('matkul_id', $matkul->id_matkul)->first();
                        //         $sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal + $mhs_matkul_orm->sisa_kuota_latihan_soal ;
                        //     }
                            
                        //     $mhs_matkul_orm->mahasiswa_id = $mhs->id_mahasiswa;
                        //     $mhs_matkul_orm->matkul_id = $matkul->id_matkul;
                        //     $mhs_matkul_orm->sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal ;
                        //     $mhs_matkul_orm->save();
                            
                        //     // [START] JIKA UJIAN SOURCE DARI MATERI
                        //     if($matkul->m_ujian->isNotEmpty()){
                        //         $ujian_existing_ids = $mhs->mhs_ujian()->pluck('ujian_id')->toArray();
                        //         $ujian_ids = $matkul->m_ujian()->pluck('id_ujian')->toArray();
                        //         $ujian_ids_insert = array_diff($ujian_ids, $ujian_existing_ids);
                        //         if(!empty($ujian_ids_insert)){
                        //             $insert = [];
                        //             foreach($ujian_ids_insert as $ujian_id){
                        //                 $insert[] = [
                        //                     'mahasiswa_id' => $mhs->id_mahasiswa,
                        //                     'ujian_id' => $ujian_id,
                        //                     'created_at' => $now,
                        //                 ];
                        //             }
                        //             Mhs_ujian_orm::insert($insert);
                        //         }
                        //     }
                        //     // [END] JIKA UJIAN SOURCE DARI MATERI
    
                        //     // [START] JIKA UJIAN SOURCE DARI BUNDLE
                        //     $existing_mhs_ujian = $mhs->mhs_ujian()->pluck('ujian_id')->toArray();
    
                        //     if($matkul->m_ujian_enable->isNotEmpty()){
                        //         $mhs_ujian_ids = $matkul->m_ujian_enable()->pluck('ujian_id')->toArray();
                        //         $mhs_ujian_ids_insert = array_diff($mhs_ujian_ids, $existing_mhs_ujian);
                        //         if(!empty($mhs_ujian_ids_insert)){
                        //             $insert = [];
                        //             foreach($mhs_ujian_ids_insert as $m_ujian_id){
                        //                 $insert[] = [
                        //                     'mahasiswa_id' => $mhs->id_mahasiswa,
                        //                     'ujian_id'	=> $m_ujian_id,
                        //                     'created_at' => $now,
                        //                 ];
                        //             }
                        //             Mhs_ujian_orm::insert($insert);
                        //         }
                        //     }
                        //     // [END] JIKA UJIAN SOURCE DARI BUNDLE
                        // }

                        if($paket->m_ujian->isNotEmpty()){
                            $ujian_ids = $paket->m_ujian()->pluck('id_ujian')->toArray();
                            $insert = [];
                            foreach($ujian_ids as $ujian_id){
                                if(!in_array($ujian_id, $ujian_ids_exist)){
                                    $ujian_ids_exist[] = $ujian_id;
                                    $insert[] = [
                                        'mahasiswa_id' => $mhs->id_mahasiswa,
                                        'ujian_id' => $ujian_id,
                                        'sisa_kuota_latihan_soal' => $paket->kuota_latihan_soal,
                                        'created_at' => $now,
                                    ];
                                }else{
                                    // JIKA SUDAH ADA MAKA SISA KUOTA LATIHAN SOAL DITAMBAHKAN
                                    $mhs_ujian_exist = Mhs_ujian_orm::where([
                                        'mahasiswa_id' => $mhs->id_mahasiswa,
                                        'ujian_id' => $ujian_id,
                                    ])->first();
                                    $kuota_latihan_soal_exist = $mhs_ujian_exist->sisa_kuota_latihan_soal;
                                    $mhs_ujian_exist->sisa_kuota_latihan_soal = $kuota_latihan_soal_exist + $paket->kuota_latihan_soal;
                                    $mhs_ujian_exist->save();
                                }
                            }
                            if(!empty($insert))
                                Mhs_ujian_orm::insert($insert);
                            
                            /*
                             * UNTUK SEKARANG TIDAK PERLU MEN-SET MHS KE MATERI UJIAN
                             * 
                            foreach($paket->m_ujian as $m_ujian){
                                if(!empty($m_ujian->matkul)){
                                    // [START] JIKA UJIAN SOURCE DARI MATERI
                                    if(!in_array($m_ujian->matkul->id_matkul, $matkul_ids_exist)){
                                        $matkul_ids_exist[] = $m_ujian->matkul->id_matkul;
                                        $mhs_matkul_orm = new Mhs_matkul_orm();
                                        $mhs_matkul_orm->mahasiswa_id = $mhs->id_mahasiswa;
                                        $mhs_matkul_orm->matkul_id = $m_ujian->matkul->id_matkul;
                                        $mhs_matkul_orm->sisa_kuota_latihan_soal = 0 ;
                                        $mhs_matkul_orm->save();
                                    }
                                    // [END] JIKA UJIAN SOURCE DARI MATERI
                                }
                                if($m_ujian->matkul_enable->isNotEmpty()){
                                    // [START] JIKA UJIAN SOURCE DARI BUNDLE
                                    $insert = [];
                                    foreach($m_ujian->matkul_enable as $matkul){
                                        if(!in_array($matkul->id_matkul, $matkul_ids_exist)){
                                            $matkul_ids_exist[] = $matkul->id_matkul;
                                            $insert[] = [
                                                'mahasiswa_id' => $mhs->id_mahasiswa,
                                                'matkul_id' => $matkul->id_matkul,
                                                'sisa_kuota_latihan_soal' => 0,
                                                'created_at' => $now,
                                            ];
                                        }
                                    }
                                    if(!empty($insert))
                                        Mhs_matkul_orm::insert($insert);
                                    // [END] JIKA UJIAN SOURCE DARI BUNDLE
                                }
                            }
                             */
                        }
            
                    }
            
                    $trx_payment->stts = PAYMENT_ORDER_TELAH_DIPROSES;
                    $trx_payment->membership_history_id = $membership_history_id;
                    $trx_payment->paket_history_id = $paket_history_id;
                    $trx_payment->tgl_bayar = $notif->transaction_time;
                    $trx_payment->trx_by = $trx_by;
                    $trx_payment->save();

                    //[END] JIKA ORDER BELUM DIPROSES

                }


			}

			commit_db_trx();
			$log_status = "SUCCESS";

		}catch(Exception $e){
			rollback_db_trx();
			$log_status = "FAIL : " . $e->getMessage();
		}

        return $log_status ;

    }


}
