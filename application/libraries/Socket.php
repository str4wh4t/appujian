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

    public function run($port, $is_secure = false, $local_cert = null, $local_pk = null ): void{

		if($is_secure){
		
			$loop   = \React\EventLoop\Factory::create();
			$webSock = new \React\Socket\SecureServer(
				new \React\Socket\Server('0.0.0.0:' . $port, $loop),
				$loop,
				array(
					'local_cert'        => $local_cert, // path to your cert
					'local_pk'          => $local_pk, // path to your server private key
					'allow_self_signed' => TRUE, // Allow self signed certs (should be false in production)
					'verify_peer' => FALSE
				)
			);

			$wsServer = new WsServer(new Chat());

			// Ratchet magic
			$server = new IoServer(
				new HttpServer(
					$wsServer
				),
				$webSock
			);

			// $wsServer->enableKeepAlive($server->loop, 30);

			// $loop->run();
			$server->run();
		}else{

			$wsServer = new WsServer(new Chat());
			
			$server = IoServer::factory(
				new HttpServer(
					$wsServer
				),
				$port
			);
			
			$wsServer->enableKeepAlive($server->loop, 30);
			
			$server->run();
		}

    }

    public function notif_ws($msg = ''){
		if(SOCKET_ENABLE){
			Client\connect(ws_url())->then(function($conn) use ($msg){
				$conn->on('message', function($msg) use ($conn) {
					// echo "Received: {$msg}\n";
					$conn->close(); // <== KONEKSI DI CLOSE SETELAH KIRIM PESAN
				});
				$conn->send($msg);
			}, function ($e) {
				echo "Could not connect: {$e->getMessage()}\n";
			});
		}
	}
}