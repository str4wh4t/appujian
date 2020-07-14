<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Uuid_generator
{
	protected $ci;

	public function __construct()
	{
        $this->ci =& get_instance();
	}


	public function create_to_uuid($integer){
		if (!preg_match('/^[0-9]+$/',$integer)) {
			// IF entried ID is not natural number
			throw new Exception('Entried ID salah');
		}
		$uuid3 = Uuid::uuid3(Uuid::NAMESPACE_DNS, $integer);
    	return $integer . '-' . $uuid3->toString() ;
	}

	public function read_from_uuid($uuid){
		$uuid_array = explode('-', $uuid);
		$return_text = $uuid_array[0] ;
		$uuid_to_check = '';

		unset($uuid_array[0]);
		foreach($uuid_array as $uuid){
			$uuid_to_check = $uuid_to_check . $uuid . '-' ;
		}

		$uuid_to_check = substr($uuid_to_check, 0, -1);

		$uuid3 = Uuid::uuid3(Uuid::NAMESPACE_DNS, $return_text);

    	return $uuid3 == $uuid_to_check ? $return_text : '' ;
	}

}

/* End of file Uuid_generator.php */
/* Location: ./application/libraries/Uuid_generator.php */
