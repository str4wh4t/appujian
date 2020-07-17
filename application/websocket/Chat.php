<?php
namespace Wsock;

use Orm\Mhs_ujian_orm;
use Orm\Users_orm;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Orm\Daftar_hadir_orm;
use Orm\Users_groups_orm;
use Orm\Login_log_orm;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $data_clients;
    protected $data_clients_mhs;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->data_clients = [];
        $this->data_absensi = [];
        $this->data_clients_mhs = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        foreach ($this->clients as $client) {
                // The sender is not the receiver, send to each client connected
	            $msgs = [];
	            if(!empty($this->data_clients)) {
		            foreach ($this->data_clients as $resourceId => $username) {
			            $msg    = [
				            'username' => $username,
				            'cmd'      => 'ONLINE',
			            ];
			            $msgs[] = $msg;
		            }
		            $client->send(json_encode($msgs));
	            }
        }
        
        if(!empty($this->data_absensi)) {
        	    $msgs = [];
	            foreach ($this->data_absensi as $daftar_hadir_id => $nim) {
		            $msg    = [
			            'nim' => $nim,
			            'cmd'      => 'LIST ABSENSI',
		            ];
		            $msgs[] = $msg;
	            }
	            $client->send(json_encode($msgs));
            }
        
    }

    public function onMessage(ConnectionInterface $from, $req) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending req "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $req, $numRecv, $numRecv == 1 ? '' : 's');

	    $req = json_decode($req);
	    if($req->cmd == 'OPEN'){
	    	// UNTUK PENGAWAS
		    if($req->as == 'pengawas') {
			    $users_groups    = Users_groups_orm::where([
				    'user_id'  => $req->user_id,
				    'group_id' => PENGAWAS_GROUP_ID
			    ])->firstOrFail();
			    
			    $mahasiswa_ujian = Mhs_ujian_orm::where('ujian_id', $req->m_ujian_id)
			                                    ->has('daftar_hadir')
			                                    ->get();
			    $absensi         = [];
			    $absensi_by_self = [];
			    if (!empty($mahasiswa_ujian)) {
				    foreach ($mahasiswa_ujian as $mu) {
					    $absensi[] = $mu->mhs_matkul->mhs->nim;
					    if ($mu->daftar_hadir->absen_by == $users_groups->id) {
						    $absensi_by_self[] = $mu->mhs_matkul->mhs->nim;
					    }
				    }
			    }
			    $res = [
				    'cmd'             => $req->cmd,
				    'mhs_online' => $this->data_clients_mhs,
				    'absensi'         => $absensi,
				    'absensi_by_self' => $absensi_by_self,
			    ];
			    foreach ($this->clients as $client) {
				    $client->send(json_encode($res));
			    }
		    }
	    }elseif($req->cmd == 'DO_ABSENSI'){
	    	// UNTUK PENGAWAS
	    	$users_groups    = Users_groups_orm::where([
			    'user_id'  => $req->user_id,
			    'group_id' => PENGAWAS_GROUP_ID
		    ])->firstOrFail();
		    $daftar_hadir = Daftar_hadir_orm::where([
			    'mahasiswa_ujian_id' => $req->mahasiswa_ujian_id,
			    'absen_by'           => $users_groups->id,
		    ])->get();
	        if($daftar_hadir->isEmpty()) {
		        $daftar_hadir                     = new Daftar_hadir_orm();
		        $daftar_hadir->mahasiswa_ujian_id = $req->mahasiswa_ujian_id;
		        $daftar_hadir->absen_by           = $users_groups->id;
		        $daftar_hadir->save();
	        }
	    }elseif($req->cmd == 'MHS_ONLINE'){
            $this->data_clients_mhs[$req->nim] = $from->resourceId;
	    	$res = [
			    'cmd'             => $req->cmd,
			    'nim'             => $req->nim,
		    ];
	        foreach ($this->clients as $client) {
			    $client->send(json_encode($res));
		    }
	    }elseif($req->cmd == 'MHS_NEW_TAB'){
	    	$res = [
			    'cmd'             => $req->cmd,
			    'nim'             => $req->nim,
		    ];
	        foreach ($this->clients as $client) {
			    $client->send(json_encode($res));
		    }
	    }
//	    elseif($req->cmd == 'MHS_OFFLINE'){
//            unset($this->data_clients_mhs[$req->nim]);
//	    	$res = [
//			    'cmd'             => $req->cmd,
//			    'nim'             => $req->nim,
//		    ];
//	        foreach ($this->clients as $client) {
//			    $client->send(json_encode($res));
//		    }
//	    }
//	    else if($req->cmd == 'ABSENSI'){
//	    	$users_groups = Users_groups_orm::where(['user_id' => $req->user, 'group_id' => PENGAWAS_GROUP_ID])->first();
//	    	$daftar_hadir = Daftar_hadir_orm::where(['mahasiswa_ujian_id' => $req->id, 'absen_by' => $users_groups->id])->first();
//	    	if(empty($daftar_hadir)){
//		        $daftar_hadir = new Daftar_hadir_orm();
//		        $daftar_hadir->mahasiswa_ujian_id = $req->id;
//		        $daftar_hadir->absen_by = $users_groups->id;
//		        $daftar_hadir->save();
//		        $req = [
//				    'nim'      => $req->nim,
//				    'cmd'   => $req->cmd
//			    ];
//		        if(!isset($this->data_absensi[$daftar_hadir->id])){
//		            $this->data_absensi[$daftar_hadir->id] = $req['nim'] ;
//			    }
//
//		    }
//	    }else {
//		    $this->data_clients[$from->resourceId] = $req->username;
//		    $req = [
//			    'username' => $req->username,
//			    'cmd'      => $req->cmd,
//		    ];
//	    }
//	    foreach ($this->clients as $client) {
//		    if ($from !== $client) {
//			    // The sender is not the receiver, send to each client connected
//			    $client->send(json_encode($req));
//		    }
//	    }
//        $users = Users_orm::where('username', $req['username'])->first();
//        $users->is_online = 1;
//        $users->save();
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        $nim = null;
        foreach($this->data_clients_mhs as $cnim => $resourceId){
        	if($resourceId == $conn->resourceId){
        		$nim = $cnim;
        		break;
	        }
        }
        if(!empty($nim)) {
	        unset($this->data_clients_mhs[$nim]);
	        $res = [
		        'cmd' => 'MHS_OFFLINE',
		        'nim' => $nim,
	        ];
	        foreach ($this->clients as $client) {
		        $client->send(json_encode($res));
	        }
        }
        
        echo "Connection {$conn->resourceId} has disconnected, with {". json_encode($res) ."}\n";
        
//        if(isset($this->data_clients[$conn->resourceId])) {
//	        $username = $this->data_clients[$conn->resourceId];
//	        unset($this->data_clients[$conn->resourceId]);
//	        $msg = [
//		        'username' => $username,
//		        'cmd'      => 'OFFLINE',
//	        ];
//	        if (!empty($this->clients)) {
//		        foreach ($this->clients as $client) {
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
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
    
//    private function _send_msg(){
//    	foreach ($this->clients as $client) {
//            if ($from !== $client) {
//                // The sender is not the receiver, send to each client connected
//                $client->send(json_encode($this->data_clients));
//            }
//        }
//	}
}
