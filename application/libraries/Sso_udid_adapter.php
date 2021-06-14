<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class Sso_udid_adapter{
	
	/**
	 * @var false|mixed
	 */
	private $debug;
	/**
	 * @var CI_Controller
	 */
	private $ci;
	/**
	 * @var array|mixed
	 */
	private $property;
	/**
	 * @var array|false[]|mixed
	 */
	private $settings;
	
	public function __construct($params)
    {
        $this->ci =& get_instance();
	
	    $property = $params['property'];
	    $settings = $params['settings'];
	    $debug    = $params['debug'] ?? [
			    'exception' => TRUE,
			    'token'     => FALSE,
			    'response'  => FALSE,
		    ];
     
	    $this->debug    = $debug;
	    $this->property = !empty($property) ? $property : [
		    'client_id'         => '',
		    'client_secret'     => '',
		    'redirect_uri'      => '',
		    'scope'             => '',
		    'authorization_url' => '',
		    'access_token_url'  => '',
	    ];
	    $this->settings = !empty($settings) ? $settings : [
		    'verify' => FALSE,
	    ];
    }
	
	private function _gen_uuid(): string
	{
		try {

		    $uuid1 = Uuid::uuid1() ;
		    return $uuid1->toString() ;

		} catch (UnsatisfiedDependencyException $e) {
		    echo 'Caught exception: ' . $e->getMessage() . "\n";
		}
	}
 
	/**
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws Exception
	 */
	public function auth($params = [])
	{
		$state = $this->_gen_uuid() ;
		
		if(!@$_GET['code']){
			try {
				
				$client = new Client();

				$requestBody = [
					'response_type' => 'code',
					'client_id'     => $this->property['client_id'],
					'scope'         => $this->property['scope'],
					'redirect_uri'  => $this->property['redirect_uri'],
					'state'         => $state,
				];
				
				if ($params){
					$requestBody = array_merge($requestBody, $params);
				}

				$onRedirect = function(
					RequestInterface $request,
					ResponseInterface $response,
					UriInterface $uri
				) use ($state){
					$_SESSION['oauth2state'] = $state;
					header('Location: '.$uri);
					exit;
				};
				
				$res = $client->request('GET', $this->property['authorization_url'], [
						'query'           => $requestBody,
						'allow_redirects' => [
												'track_redirects' => true,
												'on_redirect'     => $onRedirect,
											],
						'verify' => $this->settings['verify'],
				]);
				
			} catch (RequestException $e) {
				if ($this->debug['exception']){
					echo $e->getResponse()->getBody(); die;
				}else{
					throw new Exception('Authorization code request failed.');
				}
			} catch (Exception $e) {
				if ($this->debug['exception']){
					vdebug($e->getMessage());
				}else{
					throw new Exception('Authorization code request failed.');
				}
			}
		}elseif(empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])){
			if ($this->debug['exception']) {
				echo '$GET_STATE <br>';
				echo '<pre>';
				print_r($_GET['state']);
				echo '</pre>';
				
				echo '<br>';
				echo '<br>';
				
				echo '$GET_STATE <br>';
				echo '<pre>';
				print_r($_SESSION['oauth2state']);
				echo '</pre>';
				
				die;
			} else {
				throw new Exception("Error State");
			}
		}else{

			try {
				$code = urldecode($_GET['code']);
				$code = $_GET['code'];
				
				$form_param = [
					'grant_type'    => 'authorization_code',
					'client_id'     => $this->property['client_id'],
					'client_secret' => $this->property['client_secret'],
					'redirect_uri'  => $this->property['redirect_uri'],
					'code'          => $code,
				];

				$client = new Client();
				$res = $client->request('POST', $this->property['access_token_url'], [
						'form_params' => $form_param,
						'verify'      => $this->settings['verify'],
					]);
				
				
//				vdebug($res->getBody()->getContents());
				if ($res->getStatusCode() == 200) {
					if ($this->debug['token']) {
						vdebug($res->getBody()->getContents());
					}
					
					$body    = $res->getBody();
					$content = json_decode($body->getContents());
					$token   = $content->access_token;
					return $token;
				}else{
					if ($this->debug['exception']) {
						vdebug($res->getBody()->getContents());
					}else{
						throw new Exception("Error Request");
					}
				}
				
				
			} catch (RequestException $e) {
				if ($this->debug['exception']){
					echo $e->getResponse()->getBody(); die;
				}else{
					throw new Exception('Authorization code request failed.');
				}
			} catch (Exception $e) {
				if ($this->debug['exception']) {
					vdebug($e->getMessage());
				}else{
					throw new Exception("Error Request");
				}
			}
		}
	}
	
	public function request($params,$access_token = NULL){
//		vdebug($this->debug['exception);
		try {
			$client = new Client();
			$default_header = [];
			
			if ($access_token){
				$default_header = [
					'Authorization' => "Bearer " . $access_token,
				];
			}
			
			$body_params = !empty(@$params['body_params']) ? $params['body_params'] : null;
			
			$request_params = [
				'headers' => !empty(@$params['header_params']) ? array_merge($params['header_params'], $default_header) : $default_header,
				'verify'  => $this->settings['verify'],
			];
			if ($body_params) {
				$request_params = array_merge($body_params, $request_params);
			}
//			vdebug($request_params);
			
			$res    = $client->request($params['method'], $params['endpoint'], $request_params);
			
			if ($res->getStatusCode() >= 200 && $res->getStatusCode() < 300) {
				if ($this->debug['response']) {
					vdebug($res->getBody()->getContents());
				}
				return $res->getBody()->getContents();
			}else{
				if ($this->debug['exception']) {
					vdebug($res->getBody()->getContents());
				}else{
					throw new Exception("Error Request");
				}
			}
			
		} catch (RequestException $e) {
			if ($this->debug['exception']){
				echo $e->getResponse()->getBody(); die;
			}else{
				if ($e->getResponse()->getStatusCode() == 401){
					$content = json_decode($e->getResponse()->getBody());
					if ($content->message == 'Unauthorized'){
						redirect('sso/logout');
					}
				}else{
					throw new Exception('Unauthorized Exception.');
				}
			}
		} catch (Exception $e) {
			if ($this->debug['exception']){
				vdebug($e->getMessage());
			}else{
				throw new Exception('Unauthorized Exception.');
			}
		}
	}
	
}


/* End of file Payment.php */
/* Location: ./application/libraries/Payment.php */
