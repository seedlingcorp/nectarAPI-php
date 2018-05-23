<?php

	namespace Nectar\Core;

	class NectarConfig extends\Seedling\Nectar\NectarAPI{

		protected $_dev_mode 		= true;
		protected $_token;
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

		public function getToken(){

			//REQUEST THE TOKEN
			$this->request()->post('request-token', [
				'user_api_key' 	=> $this->get('user_key'), 
				'assoc_api_key' => $this->get('assoc_key')
			], function($res){
				$this->setToken($res);
			});

			return $this->token();
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