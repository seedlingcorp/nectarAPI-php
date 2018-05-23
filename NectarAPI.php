<?php

	namespace Seedling\Nectar;

	include_once 'Core/NectarFunctions.php';

	class NectarAPI {

		protected static $_config;

		public function __construct($assoc_key = null, $user_key = null, $token = null){

			if(get_class($this) == 'Seedling\Nectar\NectarAPI' && is_null(self::$_config)){

				//SET THE CONFIG VARIABLE
				self::$_config = new \Nectar\Core\NectarConfig($assoc_key, $user_key, $token);
			}
		}

		public function getToken(){
			return $this->config()->getToken();
		}

		public function association(){
			return $this->config()->association();
		}

		public function request(){
			return new \Nectar\Core\NectarRequest();
		}

		public function config(){
			return NectarAPI::$_config;
		}

		public function valid(){
			return !is_null($this->association());
		}
	}