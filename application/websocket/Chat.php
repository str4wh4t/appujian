<?php
namespace Wsock;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {

    protected $clients;
    protected $data_clients_mhs;
    protected $data_clients_mhs_ips;
	protected $mhs_online_counter;

    public function __construct() {
        // $this->clients = new \SplObjectStorage;

		$this->clients = [];
		$this->admins = [];
		$this->data_clients_mhs = [];
        $this->data_clients_mhs_ips = [];
		$this->mhs_online_counter = 0;
        
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        // $this->clients->attach($conn);
		$this->clients[$conn->resourceId] = $conn;
	    $this->_debug_msg("New connection! ({$conn->resourceId})");

		// foreach($this->data_clients_mhs as $app_id => $array) {
	    //     foreach ($array as $nim => $resourceId) {
		// 		$msg = 'nim : ' . $nim . ', resourceId : ' . $resourceId ;
		// 		$this->_debug_msg($msg);
		// 	}
		// }

    }

    public function onMessage(ConnectionInterface $from, $req) {
        // $numRecv = count($this->clients) - 1;
        
		// $msg = sprintf('Connection %d sending req "%s" to %d other connection%s' . "\n"
        //     , $from->resourceId, $req, $numRecv, $numRecv == 1 ? '' : 's');

	    // $this->_debug_msg($msg);

	    $req = json_decode($req);

		// var_dump($req);
	    $absensi         = [];
	    $absensi_by_self = [];

		if(!isset($this->data_clients_mhs[$req->app_id]))
			$this->data_clients_mhs = [$req->app_id => []];

		if(!isset($this->data_clients_mhs_ips[$req->app_id]))
			$this->data_clients_mhs_ips = [$req->app_id => []];

	    if($req->cmd == 'OPEN'){ // INI SEPERTI MHS_ONLINE DI MHS TP UNTUK USER SELAIN MHS
		    if(($req->as == 'admin') || ($req->as == 'pengawas') || ($req->as == 'kood_pengawas') || ($req->as == 'dosen')) {

				if(!isset($this->admins[$from->resourceId]))
					$this->admins[] = $from->resourceId;

			    $res = [
				    'cmd'             => $req->cmd,
				    // 'mhs_online' => $this->data_clients_mhs[$req->app_id],
				    // 'mhs_online_ips' => $this->data_clients_mhs_ips[$req->app_id],
					'mhs_online' => [],
				    'mhs_online_ips' => [],
					'mhs_online_counter' => $this->mhs_online_counter,
				    'absensi'         => $absensi,
				    'absensi_by_self' => $absensi_by_self,
				    'user_id'     => $req->user_id,
				    'app_id'      => $req->app_id,
			    ];
			    
			    // foreach ($this->clients as $conn_id => $conn) {
				//     $conn->send(json_encode($res));
			    // }
				
				$msg = json_encode($res);
				$this->_msg_to_admin($msg, $from);
			}
		    // }else if($req->as == 'admin') {

			// 	if(!isset($this->admins[$from->resourceId]))
			// 		$this->admins[] = $from->resourceId;

		    // 	$res = [
			// 	    'cmd'             => $req->cmd,
			// 	    'mhs_online' => $this->data_clients_mhs[$req->app_id],
			// 	    'mhs_online_ips' => $this->data_clients_mhs_ips[$req->app_id],
			// 	    'absensi'         => $absensi,
			// 	    'absensi_by_self' => [],
			// 	    'user_id'     => $req->user_id,
			// 	    'app_id'      => $req->app_id,
			//     ];
				
			// 	$msg = json_encode($res);
			// 	$this->_msg_to_admin($msg, $from);
		    // }
	    }elseif($req->cmd == 'DO_ABSENSI'){
		    if($req->as == 'pengawas') {

			    $ok =  true;
			    $res = [
				    'cmd'     => $req->cmd,
				    'nim'     => $req->nim,
				    'user_id' => $req->user_id,
				    'ok'      => $ok,
				    'app_id'  => $req->app_id,
			    ];
				
				$msg = json_encode($res);
				$this->_msg_to_admin($msg, $from);
		    }
	    }elseif($req->cmd == 'DO_ABSENSI_BATAL'){
	    	if($req->as == 'pengawas') {
				
			    $ok = true ;
			    $res = [
				    'cmd'     => $req->cmd,
				    'nim'     => $req->nim,
				    'user_id' => $req->user_id,
				    'ok'      => $ok,
				    'app_id'  => $req->app_id,
			    ];
				
				$msg = json_encode($res);
				$this->_msg_to_admin($msg, $from);
		    }
	    }elseif($req->cmd == 'DO_BAPU'){
		    if($req->as == 'pengawas') {

			    $ok =  true;
			    $res = [
				    'cmd'     => $req->cmd,
					'bapu' 	  => $req->bapu,
				    'nim'     => $req->nim,
				    'user_id' => $req->user_id,
				    'ok'      => $ok,
				    'app_id'  => $req->app_id,
			    ];
				
				$msg = json_encode($res);
				$this->_msg_to_admin($msg, $from);
		    }
	    }elseif($req->cmd == 'MHS_ONLINE'){

			if(!isset($this->data_clients_mhs[$req->app_id][$req->nim])){
				$this->mhs_online_counter++;
			}

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
				'mhs_online_counter' => $this->mhs_online_counter,
		    ];

			$msg = json_encode($res);

			$this->_msg_to_all($msg, $from);
			// $this->_msg_to_admin($msg, $from);

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
			$this->_msg_to_admin($msg, $from);

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
			$this->_msg_to_admin($msg, $from);

	    }elseif($req->cmd == 'DO_KICK'){
			if(($req->as == 'admin') || ($req->as == 'pengawas') || ($req->as == 'koord_pengawas') || ($req->as == 'dosen')){
				$res = [
					'cmd'         => $req->cmd,
					'nim'         => $req->nim,
					'username'    => $req->username,
					'app_id'      => $req->app_id,
				];

				// foreach ($this->clients as $conn_id => $conn) {
				// 	$conn->send(json_encode($res));
				// }

				$msg = json_encode($res);
				
				$this->_msg_to_all($msg, $from);
				// $this->_msg_to_admin($msg, $from);
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
			$this->_msg_to_admin($msg, $from);

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
			$this->_msg_to_admin($msg, $from);

	    }elseif($req->cmd == 'PING'){
	    	$res = [
			    'cmd'             => $req->cmd,
			    'nim'             => $req->nim,
			    'app_id'      => $req->app_id,
				'mhs_online_counter' => $this->mhs_online_counter,
			    'ip'             => $req->ip,
//			    'latency'     => round((round(microtime(true), 3) * 1000 - intval($req->mctime)) / 1000),
			    'latency' => $req->latency,
		    ];

	        // foreach ($this->clients as $conn_id => $conn) {
			//     $conn->send(json_encode($res));
		    // }

			$msg = json_encode($res);
			$this->_msg_to_admin($msg, $from);

	    }elseif($req->cmd == 'UPDATE_TIME'){
			if(($req->as == 'pengawas') || $req->as == 'admin'){
				$res = [
					'cmd'         => $req->cmd,
					'nim'         => $req->nim,
					'app_id'      => $req->app_id,
				];

				// foreach ($this->clients as $conn_id => $conn) {
				// 	$conn->send(json_encode($res));
				// }

				$msg = json_encode($res);

				$this->_msg_to_all($msg, $from);
				// $this->_msg_to_admin($msg, $from);

			}
	    }elseif($req->cmd == 'TEST'){
	    	$res = [
			    'cmd'             => $req->cmd,
			    'app_id'      => $req->app_id,
		    ];

	        // foreach ($this->clients as $conn_id => $conn) {
			//     $conn->send(json_encode($res));
		    // }

			$msg = json_encode($res);
			$this->_msg_to_all($msg, $from);

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

						$this->mhs_online_counter--;

				        $msg = [
					        'cmd' => 'MHS_OFFLINE',
					        'nim' => $nim,
							'mhs_online_counter' => $this->mhs_online_counter,
							'app_id'      => $app_id,
				        ];
				        // foreach ($this->clients as $conn_id => $conn) {
					    //     $conn->send(json_encode($msg));
				        // }
						$msg = json_encode($msg);
						$this->_msg_to_admin($msg, $conn);
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
    
   	private function _msg_to_all($msg, $from){
   		// foreach ($this->clients as $conn) {
        //    if ($from !== $client) {
        //        // The sender is not the receiver, send to each client connected
        //        $client->send(json_encode($this->data_clients));
        //    }
       	// }

		foreach ($this->clients as $client_conn_id => $conn) {
			if($from->resourceId != $client_conn_id){
				$this->clients[$client_conn_id]->send($msg);
			}
		}
		$this->_debug_msg('msg : '. $msg .', send to all');
	}

	private function _msg_to_admin($msg, $from){
		foreach($this->admins as $admin_conn_id){
			if(isset($this->clients[$admin_conn_id])){
				if($from->resourceId != $admin_conn_id){
					$this->clients[$admin_conn_id]->send($msg);
					$this->_debug_msg('msg : '. $msg .', send to admin : ' . $admin_conn_id);
				}
			}
		}
	}
	
	private function _debug_msg($msg){
    	if(IS_DEBUG_SOCKET){
    		echo $msg."\n";
	    }
	}


}
