<?php
namespace Wsock;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $data_clients_mhs;
    protected $data_clients_mhs_ips;

    public function __construct() {
        // $this->clients = new \SplObjectStorage;

        // $this->data_clients_mhs = ['ujian.undip.ac.id' => [], 'cat.undip.ac.id' => []];
        // $this->data_clients_mhs_ips = ['ujian.undip.ac.id' => [], 'cat.undip.ac.id' => []];

		$this->clients = [];
		$this->admins = [];
		$this->data_clients_mhs = [];
        $this->data_clients_mhs_ips = [];
        
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        // $this->clients->attach($conn);
		$this->clients[$conn->resourceId] = $conn;
	    $this->_debug_msg("New connection! ({$conn->resourceId})");

		foreach($this->data_clients_mhs as $app_id => $array) {
	        foreach ($array as $nim => $resourceId) {
				$msg = 'nim : ' . $nim . ', rid : ' . $resourceId ;
				$this->_debug_msg($msg);
			}
		}
    
    }

    public function onMessage(ConnectionInterface $from, $req) {
        // $numRecv = count($this->clients) - 1;
        
		// $msg = sprintf('Connection %d sending req "%s" to %d other connection%s' . "\n"
        //     , $from->resourceId, $req, $numRecv, $numRecv == 1 ? '' : 's');

	    // $this->_debug_msg($msg);

	    $req = json_decode($req);
	    $absensi         = [];
	    $absensi_by_self = [];

		if(!isset($this->data_clients_mhs[$req->app_id]))
			$this->data_clients_mhs = [$req->app_id => []];

		if(!isset($this->data_clients_mhs_ips[$req->app_id]))
			$this->data_clients_mhs_ips = [$req->app_id => []];

	    if($req->cmd == 'OPEN'){
		    if($req->as == 'pengawas') {
	    	    // UNTUK PENGAWAS
//			    $users_groups    = Users_groups_orm::where([
//				    'user_id'  => $req->user_id,
//				    'group_id' => PENGAWAS_GROUP_ID
//			    ])->firstOrFail();
//
//			    $mahasiswa_ujian = Mhs_ujian_orm::where('ujian_id', $req->m_ujian_id)
//			                                    ->has('daftar_hadir')
//			                                    ->get();
//
//			    if ($mahasiswa_ujian->isNotEmpty()) {
//				    foreach ($mahasiswa_ujian as $mu) {
//					    $absensi[] = $mu->mhs_matkul->mhs->nim;
//					    if ($mu->daftar_hadir->absen_by == $users_groups->id) {
//						    $absensi_by_self[] = $mu->mhs_matkul->mhs->nim;
//					    }
//				    }
//			    }

				if(!isset($this->admins[$from->resourceId]))
					$this->admins[] = $from->resourceId;

			    $res = [
				    'cmd'             => $req->cmd,
				    'mhs_online' => $this->data_clients_mhs[$req->app_id],
				    'mhs_online_ips' => $this->data_clients_mhs_ips[$req->app_id],
				    'absensi'         => $absensi,
				    'absensi_by_self' => $absensi_by_self,
				    'user_id'     => $req->user_id,
				    'app_id'      => $req->app_id,
			    ];
			    
			    // foreach ($this->clients as $conn_id => $conn) {
				//     $conn->send(json_encode($res));
			    // }
				
				$msg = json_encode($res);
				$this->msg_to_admin($msg);

		    }else if($req->as == 'admin') {
		    	// UNTUK ADMIN
//		    	$mahasiswa_ujian = Mhs_ujian_orm::where('ujian_id', $req->m_ujian_id)
//			                                    ->has('daftar_hadir')
//			                                    ->get();
//		    	if ($mahasiswa_ujian->isNotEmpty()) {
//				    foreach ($mahasiswa_ujian as $mu) {
//					    $absensi[] = $mu->mhs_matkul->mhs->nim;
//				    }
//			    }

				if(!isset($this->admins[$from->resourceId]))
					$this->admins[] = $from->resourceId;

		    	$res = [
				    'cmd'             => $req->cmd,
				    'mhs_online' => $this->data_clients_mhs[$req->app_id],
				    'mhs_online_ips' => $this->data_clients_mhs_ips[$req->app_id],
				    'absensi'         => $absensi,
				    'absensi_by_self' => [],
				    'user_id'     => $req->user_id,
				    'app_id'      => $req->app_id,
			    ];
			    
			    // foreach ($this->clients as $conn_id => $conn) {
				//     $conn->send(json_encode($res));
			    // }
				
				$msg = json_encode($res);
				$this->msg_to_admin($msg);

		    }
	    }elseif($req->cmd == 'DO_ABSENSI'){
		    if($req->as == 'pengawas') {
	    	    // UNTUK PENGAWAS
//			    $users_groups = Users_groups_orm::where([
			    ////				    'user_id'  => $req->user_id,
			    ////				    'group_id' => PENGAWAS_GROUP_ID
			    ////			    ])->firstOrFail();
			    ////
			    ////			    $daftar_hadir = Daftar_hadir_orm::where([
			    ////				    'mahasiswa_ujian_id' => $req->mahasiswa_ujian_id,
			    ////			    ])->first();
//
//			    $ok = false;
//			    if (empty($daftar_hadir)) {
//				    $daftar_hadir                     = new Daftar_hadir_orm();
//				    $daftar_hadir->mahasiswa_ujian_id = $req->mahasiswa_ujian_id;
//				    $daftar_hadir->absen_by           = $users_groups->id;
//				    $ok                               = $daftar_hadir->save();
//			    }
			    $ok =  true;
			    $res = [
				    'cmd'     => $req->cmd,
				    'nim'     => $req->nim,
				    'user_id' => $req->user_id,
				    'ok'      => $ok,
				    'app_id'  => $req->app_id,
			    ];

			    // foreach ($this->clients as $conn_id => $conn) {
				//     $conn->send(json_encode($res));
			    // }
				
				$msg = json_encode($res);
				$this->msg_to_admin($msg);

		    }
	    }elseif($req->cmd == 'DO_ABSENSI_BATAL'){
	    	if($req->as == 'pengawas') {
//			    // UNTUK PENGAWAS
//			    $users_groups = Users_groups_orm::where([
//				    'user_id'  => $req->user_id,
//				    'group_id' => PENGAWAS_GROUP_ID
//			    ])->firstOrFail();
//
//			    $daftar_hadir = Daftar_hadir_orm::where([
//				    'mahasiswa_ujian_id' => $req->mahasiswa_ujian_id,
//				    'absen_by'           => $users_groups->id,
//			    ])->first();
//
//			    $ok           = false;
//			    if (!empty($daftar_hadir)) {
//				    $ok = $daftar_hadir->delete();
//			    }
			    $ok = true ;
			    $res = [
				    'cmd'     => $req->cmd,
				    'nim'     => $req->nim,
				    'user_id' => $req->user_id,
				    'ok'      => $ok,
				    'app_id'      => $req->app_id,
			    ];

			    // foreach ($this->clients as $conn_id => $conn) {
				//     $conn->send(json_encode($res));
			    // }
				
				$msg = json_encode($res);
				$this->msg_to_admin($msg);

		    }
	    }elseif($req->cmd == 'MHS_ONLINE'){
            $this->data_clients_mhs[$req->app_id][$req->nim] = $from->resourceId;
            $this->data_clients_mhs_ips[$req->app_id][$req->nim] = $req->ip;
//            $users = Users_orm::where('username', $req->nim)->first();
            $ok = true ;
//	        if(!empty($users)){
//			        $users->ip_address = $req->ip;
//			        $users->save();
//	        }
	    	$res = [
		        'cmd'         => $req->cmd,
		        'nim'         => $req->nim,
		        'ip'          => $req->ip,
		        'identifier'  => $req->identifier,
			    'ok'          => $ok,
		        'app_id'      => $req->app_id,
		    ];

	        // foreach ($this->clients as $conn_id => $conn) {
			//     $conn->send(json_encode($res));
		    // }

			$msg = json_encode($res);
			$this->msg_to_admin($msg);

	    }elseif($req->cmd == 'MHS_LOST_FOCUS'){
	    	$res = [
			    'cmd'             => $req->cmd,
			    'nim'             => $req->nim,
			    'app_id'      => $req->app_id,
		    ];

	        // foreach ($this->clients as $conn_id => $conn) {
			//     $conn->send(json_encode($res));
		    // }

			$msg = json_encode($res);
			$this->msg_to_admin($msg);

	    }elseif($req->cmd == 'MHS_GET_FOCUS'){
	    	$res = [
			    'cmd'             => $req->cmd,
			    'nim'             => $req->nim,
			    'app_id'      => $req->app_id,
		    ];
			
	        // foreach ($this->clients as $conn_id => $conn) {
			//     $conn->send(json_encode($res));
		    // }

			$msg = json_encode($res);
			$this->msg_to_admin($msg);

	    }elseif($req->cmd == 'DO_KICK'){
			if(($req->as == 'pengawas') || $req->as == 'admin'){
				$res = [
					'cmd'         => $req->cmd,
					'nim'         => $req->nim,
					'username'    => $req->username,
					'app_id'      => $req->app_id,
				];

				foreach ($this->clients as $conn_id => $conn) {
					$conn->send(json_encode($res));
				}

				// $msg = json_encode($res);
				// $this->msg_to_admin($msg);

			}
	    }elseif($req->cmd == 'MHS_START_UJIAN'){
		    $res = [
			    'cmd'             => $req->cmd,
			    'nim'             => $req->nim,
			    'app_id'      => $req->app_id,
		    ];

		    // foreach ($this->clients as $conn_id => $conn) {
			//     $conn->send(json_encode($res));
		    // }

			$msg = json_encode($res);
			$this->msg_to_admin($msg);

	    }elseif($req->cmd == 'MHS_STOP_UJIAN'){
	    	$res = [
			    'cmd'             => $req->cmd,
			    'nim'             => $req->nim,
			    'app_id'      => $req->app_id,
		    ];

	        // foreach ($this->clients as $conn_id => $conn) {
			//     $conn->send(json_encode($res));
		    // }

			$msg = json_encode($res);
			$this->msg_to_admin($msg);

	    }elseif($req->cmd == 'PING'){
	    	$res = [
			    'cmd'             => $req->cmd,
			    'nim'             => $req->nim,
			    'app_id'      => $req->app_id,
			    'ip'             => $req->ip,
//			    'latency'     => round((round(microtime(true), 3) * 1000 - intval($req->mctime)) / 1000),
			    'latency' => $req->latency,
		    ];

	        // foreach ($this->clients as $conn_id => $conn) {
			//     $conn->send(json_encode($res));
		    // }

			$msg = json_encode($res);
			$this->msg_to_admin($msg);

	    }elseif($req->cmd == 'UPDATE_TIME'){
			if(($req->as == 'pengawas') || $req->as == 'admin'){
				$res = [
					'cmd'         => $req->cmd,
					'nim'         => $req->nim,
					'app_id'      => $req->app_id,
				];

				foreach ($this->clients as $conn_id => $conn) {
					$conn->send(json_encode($res));
				}

				$msg = json_encode($res);
				$this->msg_to_admin($msg);

			}
	    }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
	    $this->_debug_msg("Conn ID ({$conn->resourceId}) has disconnected");
        // $this->clients->detach($conn);
		if(isset($this->clients[$conn->resourceId]))
			unset($this->clients[$conn->resourceId]);

		if(isset($this->admins[$conn->resourceId]))
			unset($this->admins[$conn->resourceId]);

        foreach($this->data_clients_mhs as $app_id => $array) {
	        foreach ($array as $nim => $resourceId) {
		        if ($resourceId == $conn->resourceId) {
//		        	echo $app_id . "||" . $nim ;
		        	if(isset($this->data_clients_mhs[$app_id][$nim])) {
				        unset($this->data_clients_mhs[$app_id][$nim]);
				        unset($this->data_clients_mhs_ips[$app_id][$nim]);
				        $res = [
					        'cmd' => 'MHS_OFFLINE',
					        'nim' => $nim,
				        ];
				        foreach ($this->clients as $conn_id => $conn) {
					        $conn->send(json_encode($res));
				        }
						return;
				        // break;
				        // break;
			        }
		        }
	        }
        }
        
        // echo "Connection {$conn->resourceId} has disconnected, with {". json_encode($res) ."}\n";
        
//        if(isset($this->data_clients[$conn->resourceId])) {
//	        $username = $this->data_clients[$conn->resourceId];
//	        unset($this->data_clients[$conn->resourceId]);
//	        $msg = [
//		        'username' => $username,
//		        'cmd'      => 'OFFLINE',
//	        ];
//	        if (!empty($this->clients)) {
//		        foreach ($this->clients as $conn) {
//			        // The sender is not the receiver, send to each client connected
//			        $client->send(json_encode($msg));
//		        }
//	        }
//	        $users = Users_orm::where('username', $username)->first();
//	        $users->is_online = 0;
//	        $users->save();
//        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
	    $this->_debug_msg("An error has occurred: {$e->getMessage()}");
        $conn->close();
    }
    
//    private function _send_msg(){
//    	foreach ($this->clients as $conn) {
//            if ($from !== $client) {
//                // The sender is not the receiver, send to each client connected
//                $client->send(json_encode($this->data_clients));
//            }
//        }
//	}

	private function msg_to_admin($msg){
		foreach($this->admins as $admin_conn_id){
			if(isset($this->clients[$admin_conn_id])){
				$this->clients[$admin_conn_id]->send($msg);
				$this->_debug_msg('msg : '. $msg .', send to admin : ' . $admin_conn_id);
			}
		}

	}
	
	private function _debug_msg($msg){
    	if(IS_DEBUG_SOCKET){
    		echo $msg."\n";
	    }
	}


}
