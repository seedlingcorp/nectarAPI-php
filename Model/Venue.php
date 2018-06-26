<?php
	
	namespace Nectar\Model;

	class Venue extends \Nectar\Core\NectarModel{

		public function __construct($data = []){
			parent::__construct($data);
		}

		public function information($get = false){

			$data = $this->data();

			$res = isset($data->information) ? $data->information : $this->relationship('information');

			return $get ? $res->get() : $res;
		}

		
	}