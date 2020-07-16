<?php
namespace Wsock;

use Orm\Users_orm;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Orm\Daftar_hadir_orm;
use Orm\Users_groups_orm;
use Orm\Login_log_orm;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $data_clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->data_clients = [];
        $this->data_absensi = [];
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

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

	    $msg = json_decode($msg);
	    if($msg->cmd == 'ABSENSI'){
	    	$users_groups = Users_groups_orm::where(['user_id' => $msg->user, 'group_id' => PENGAWAS_GROUP_ID])->first();
	    	$daftar_hadir = Daftar_hadir_orm::where(['mahasiswa_ujian_id' => $msg->id, 'absen_by' => $users_groups->id])->first();
	    	if(empty($daftar_hadir)){
		        $daftar_hadir = new Daftar_hadir_orm();
		        $daftar_hadir->mahasiswa_ujian_id = $msg->id;
		        $daftar_hadir->absen_by = $users_groups->id;
		        $daftar_hadir->save();
		        $msg = [
				    'nim'      => $msg->nim,
				    'cmd'   => $msg->cmd
			    ];
		        if(!isset($this->data_absensi[$daftar_hadir->id])){
		            $this->data_absensi[$daftar_hadir->id] = $msg['nim'] ;
			    }
		    }
	    }else {
		    $this->data_clients[$from->resourceId] = $msg->username;
		    $msg = [
			    'username' => $msg->username,
			    'cmd'      => $msg->cmd,
		    ];
	    }
	    foreach ($this->clients as $client) {
		    if ($from !== $client) {
			    // The sender is not the receiver, send to each client connected
			    $client->send(json_encode($msg));
		    }
	    }
        $users = Users_orm::where('username', $msg['username'])->first();
        $users->is_online = 1;
        $users->save();
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
        if(isset($this->data_clients[$conn->resourceId])) {
	        $username = $this->data_clients[$conn->resourceId];
	        unset($this->data_clients[$conn->resourceId]);
	        $msg = [
		        'username' => $username,
		        'cmd'      => 'OFFLINE',
	        ];
	        if (!empty($this->clients)) {
		        foreach ($this->clients as $client) {
			        // The sender is not the receiver, send to each client connected
			        $client->send(json_encode($msg));
		        }
	        }
	        $users = Users_orm::where('username', $username)->first();
	        $users->is_online = 0;
	        $users->save();
        }
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
