<?php

	namespace Nectar\Core;

	class NectarModel extends\Nectar\Core\NectarRelationship{

		//DEFINE MODEL VARS
		public $_api_url;
		public $_data;
		public $_orm_data;
		

		public function __construct($data = []){
			
			//RUN PARENT CONSTRUCTOR
			parent::__construct();
			
			//SET THE DATA
			$this->_data = $this->parseSetData($data);

			//SET THE API URL
			$this->_api_url = $this->apiUrl();
		}

		public function isModel(){
			return true;
		}

		public function toArray(){
			return $this->data();
		}

		public function toArrayDeep($full = false){
			
			//return json_decode(json_encode($this->_data), true);

			$res = [];


			$d = $full ? $this : $this->_data;
			foreach($d as $k => $v){
				if(is_object($v)){

					if(method_exists($v, 'toArrayDeep')){
						if($v->isModel() && $full){

						}
						//$res[$k] = get_class($v);
						$res[$k] = $v->toArrayDeep(true);
					}
					else{
						//$res[$k] = json_decode(json_encode($v), true);
						$res[$k] = $v;
					}
				}
				else{
					$res[$k] = $v;
				}
			}

			return $res;
		}

		/*
		public function toJsonOld(){
			
			$res = [];

			foreach($this->data() as $k => $v){
				$res[$k] = is_object($v) && method_exists($v, 'toArray') ? $v->toArray() : $v;
			}

			return json_encode($res);
		}
		*/

		//PARSE THE DATA AND LOAD MODELS AS NEEDED
		private function parseSetData($data = []){
			
			//START THE RESULT ARRAY
			$res = [];

			//LOOP THROUGH THE DATA
			foreach($data as $k => $v){

				if(is_object($v) && isset($v->date) && isset($v->timezone_type) && isset($v->timezone)){
					$v = new \DateTime($v->date);
				}

				//CHECK FOR OBJECT WITH CLASS NAME
				elseif(is_object($v) && isset($v->className)){

					//FIND THE CLASSNAME
					$class_name = '\Nectar\Model\\'.str_replace('Nectar', '', $v->className);

					//LOAD THE CLASS IF IT EXISTS
					if(class_exists($class_name)) $v = new $class_name((array)$v->data);
				}
				elseif(is_array($v)){

					//ASSUME THE ARRAY IS ALL MODELS
					$all_models = true;

					//CHECK IF THE ARRAY IS ALL MODELS
					foreach($v as $x => $y) if(!is_object($y) || is_object($y) && !isset($y->className)) $all_models = false;

					//IF THE ARRAY IS STILL ALL MODELS TURN IT INTO A COLLECTION
					if($all_models) $v = new \Nectar\Core\NectarCollection($v);
				}

				//ADD THE RESULT
				$res[$k] = $v;
			}

			//RETURN THE RESULT
			return $res;
		}

		//DEFINE THE API URL
		public function apiUrl(){

			//PARSE THE URL FROM EXISTING DATA
			return strtolower(end(explode('\\', get_class($this)))).'/'.$this->data()->id;
		}

		//CREATE A RELATIONSHIP
		public function relationship($url){

			//INSTANTIATE A NEW RELATIONSHIP WITH THE URL
			return new \Nectar\Core\NectarRelationship($this->_orm_data->_api_url.'/'.$url);
		}

		//OBTAIN THE MODEL DATA
		public function data(){

			//return new \Nectar\Core\NectarModelData($this->_data);

			//SEND BACK THE MODEL DATA
			return (object)$this->_data;
		}

		/*
		public function toJson(){

			$res = [];

			foreach($this as $k => $v){
				if(is_string($v)){
					$res[$k] = $v;
				}
				elseif(is_object($v)){
					if(method_exists($v, 'isModel')){
						if($v->isModel()){
							$res[$k] = json_decode($v->toJson(), true);
						}
						elseif(get_class($v) === 'Nectar\Core\NectarCollection'){
							$res[$k] = json_decode($v->toJson(), true);
						}
					}
				}
			}
		}
		*/

		//HANDLE STATUS CHECKS TO SIMULATE API REQUESTS
		public function status(){
			return true;
		}
	}