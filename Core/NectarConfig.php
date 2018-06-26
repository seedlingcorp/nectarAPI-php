<?php

	namespace Nectar\Core;

	class NectarConfig extends\Seedling\Nectar\NectarAPI{

		protected $_dev_mode 		= true;
		protected $_token;
		protected $_memberToken;
		protected $_data 			= [];
		protected $_prod_api_url 	= 'https://my.nectarmembers.com/api/';
		protected $_dev_api_url 	= 'https://nectar.itulstaging.com/api/';
		protected $_association;

		public function __construct($assoc_key, $user_key, $token = null){
			
			define('NECTAR_PATH', realpath(__DIR__.'/../').'/');
			define('NECTAR_CORE', NECTAR_PATH.'Core/');
			define('NECTAR_MODEL', NECTAR_PATH.'Model/');
			$this->set(['assoc_key' => $assoc_key, 'user_key' => $user_key]);

			if(!is_null($token)) $this->setToken($token);
		}

		public function url($path){

			//if($this->_memberToken && strpos($path, 'association') === false) $path = 'authMember/'.$path;
			return ($this->_dev_mode ? $this->_dev_api_url : $this->_prod_api_url).$path;
		}

		public function setToken($token){
			
			//SET THE TOKEN AND CONVERT A STRING INTO MODEL IF NEEDED
			$this->_token = is_string($token) ? new \Nectar\Model\ApiToken(['token' => $token]) : $token;

			//SET THE ASSOCIATION
			$this->_association = $this->request()->get('association')->results();

			//RETURN THE CONFIG OBJECT
			return $this;
		}

		public function setMemberToken($token){

			$this->_memberToken = is_string($token) ? new \Nectar\Model\ApiToken(['token' => $token]) : $token;
			$this->set(['member_token' => $this->_memberToken]);

			//pr($this->request());
			//exit;

			//SET THE ASSOCIATION
			$this->_association = $this->request()->get('association')->results();

			return $this;
		}

		public function authFromtoken($token){

			return $this->setToken($token);
		}

		public function getMemberToken($email, $password, $assoc_api_key = null){

			$res = $this->request()->post('request-token', [
				'member' 			=> true,
				'member_email' 		=> $email,
				'member_password' 	=> $password,
				'assoc_api_key' 	=> $assoc_api_key
			], function($res){
				$this->setToken($res);
			});

			return $res->status() ? $this->token() : $res;
		}

		public function getToken(){

			//REQUEST THE TOKEN
			$res = $this->request()->post('request-token', [
				'user_api_key' 	=> $this->get('user_key'), 
				'assoc_api_key' => $this->get('assoc_key')
			], function($res){
				$this->setToken($res);
			});

			return $res->status() ? $this->token() : $res;
		}

		public function token(){
			return $this->_token;
		}

		public function association(){
			return $this->_association;
		}

		public function tokenHeader(){
			return $this->_token ? 'X-Nectar-Token: '.$this->_token->_data['token'] : false;
		}

		public function memberTokenHeader(){
			return $this->_memberToken ? 'X-Nectar-Member-Token: '.$this->_memberToken->_data['token'] : false;
		}

		public function set($name, $value = null){

			if(is_array($name)){
				foreach($name as $k => $v) $this->_data[$k] = $v;
			}
			else{
				$this->_data[$name] = $value;
			}

			
			return $this;
		}

		public function get($name){
			return $this->_data[$name];
		}
	}