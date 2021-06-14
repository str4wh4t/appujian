<?php

use Carbon\Carbon;
use Firebase\JWT\JWT;


class Verification_jwt {

        public function extract_jwt($token){
            JWT::$leeway = 60; 
            // $public_key = openssl_pkey_get_public($_ENV['PUBLIC_KEY_PATH']);

            $public_key = openssl_pkey_get_public(file_get_contents($_ENV['PUBLIC_KEY_PATH']));

            // $hasil = [
            //     'pub' => $_ENV['PUBLIC_KEY_PATH'],
            //     'pub_exist' => file_exists($_ENV['PUBLIC_KEY_PATH']),
            //     'public_key' => $public_key,
            //     'public_key_in' => $public_key_in
            // ];

            // vdebug($hasil);
            
            // vdebug(file_exists($_ENV['PUBLIC_KEY_PATH']));
            try {
                $decoded = JWT::decode($token,$public_key,array('RS256'));
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'data' => null,
                    'error_message' => $e->getMessage()
                ];
            }
            // $expiration = Carbon::createFromTimestam($decoded)
            // die();

            return [
                'success' => true,
                'data' => $decoded
            ];
        }

        public function verify_jwt($token){
            $decoded = $this->extract_jwt($token);

            if ($decoded['success'] == false){
                return false;
            }

            $expiration = Carbon::createFromTimestamp($decoded['data']->exp);
            $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);

            if ($tokenExpired){
                return true;
            }else{
                return false;
            }

        }
        
    }