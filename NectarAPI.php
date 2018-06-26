<?php

	namespace Seedling\Nectar;

	include_once 'Core/NectarFunctions.php';

	class NectarAPI {

		protected static $_config;
		protected static $_member;

		public function __construct($assoc_key = null, $user_key = null, $token = null){

			$assoc_key 	= $assoc_key === false ? null : $assoc_key;
			$user_key 	= $user_key === false ? null : $user_key;
			$token 		= $token === false ? null : $token;

			if(get_class($this) == 'Seedling\Nectar\NectarAPI'){

				//SET THE MEMBER TOKEN
				if(is_string($assoc_key) && substr($assoc_key, 0, 4) == 'NTM_'){
					$this->config()->setMemberToken($assoc_key);
					
				} 
				else{
					//SET THE CONFIG VARIABLE
					if(is_null(self::$_config) && (!is_null($assoc_key) && !is_null($user_key))) self::$_config = new \Nectar\Core\NectarConfig($assoc_key, $user_key, $token);
				}

				
			}
		}

		public function prepJson($obj = null, $load = true){

			if(is_null($obj) && $load) return $this->prepJson($this);

			if(is_object($obj)){
				if(method_exists($obj, 'prepJson')){

					if(get_class($obj) == 'Nectar\Core\NectarCollection') return $this->prepJson($obj->data());

					$classparts = explode('\\', get_class($obj));
					$classname 	= end($classparts);
					$classname 	= 'Nectar'.str_replace('Nectar', '', $classname);					

					$res = [
						'className' => $classname,
						'data' 		=> [],
					];

					foreach($obj->data() as $k => $v) $res['data'][$k] = $this->prepJson($v, false);
					
				}
				elseif(get_class($obj) == 'DateTime'){
					$res = json_decode(json_encode($obj), true);
				}
				else{
					$res = [];
					foreach($obj as $k => $v){
						$res[$k] = $this->prepJson($v, false);
					}
				}
			}
			elseif(is_array($obj)){
				$res = [];
				foreach($obj as $k => $v){
					$res[$k] = $this->prepJson($v, false);
				}
			}
			else{
				$res = $obj;
			}

			return $res;
		}

		public function toJson(){

			return addslashes(json_encode($this->prepJson()));
		}

		public function isModel(){
			return false;
		}

		//GET THE MEMBER TOKEN
		public function getMemberToken($email, $password, $assoc_key = null){
			return $this->config()->getMemberToken($email, $password, $assoc_key);
		}

		//GET THE API TOKEN
		public function getToken(){
			return $this->config()->getToken();
		}

		//LOAD THE ASSOCIATION OBJECT
		public function association(){
			return $this->config()->association();
		}

		//LOAD THE REQUEST OBJECT
		public function request(){
			return new \Nectar\Core\NectarRequest();
		}

		//LOAD THE CONFIG OBJECT
		public function config(){

			NectarAPI::$_config = !NectarAPI::$_config ? new \Nectar\Core\NectarConfig() : NectarAPI::$_config;

			return NectarAPI::$_config;
		}

		//CHECK IF THIS IS VALID
		public function valid(){
			return !is_null($this->association());
		}

		public function load($assoc_key = null, $user_key = null, $token = null){
			self::__construct($assoc_key, $user_key, $token);

			return $this;
		}

		public function member(){
			//if(!self::$_member){
				$res = $this->request()->post('info');;

				if($res->status()){
					self::$_member = $res->results();
				}
				else{
					self::$_member = false;
				}
			//}
			return self::$_member;


		}
	}