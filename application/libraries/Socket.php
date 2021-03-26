<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Wsock\Chat;
use Ratchet\Client;

class Socket
{

	public function __construct()
	{
        
	}

    public function run(){
        $wsServer = new WsServer(new Chat());
		
		$server = IoServer::factory(
		    new HttpServer(
		        $wsServer
		    ),
		    SOCKET_PORT
		);
		
		$wsServer->enableKeepAlive($server->loop, 30);
		
		$server->run();
    }

    public function notif_ws($send_msg = ''){
		Client\connect(ws_url())->then(function($conn) use ($send_msg){
	        $conn->on('message', function($msg) use ($conn) {
	            // echo "Received: {$msg}\n";
	            $conn->close();
	        });
	        $conn->send($send_msg);
	    }, function ($e) {
	        echo "Could not connect: {$e->getMessage()}\n";
	    });
	}
}