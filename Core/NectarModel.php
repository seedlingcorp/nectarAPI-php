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

		//PARSE THE DATA AND LOAD MODELS AS NEEDED
		private function parseSetData($data = []){
			
			//START THE RESULT ARRAY
			$res = [];

			//LOOP THROUGH THE DATA
			foreach($data as $k => $v){

				//CHECK FOR OBJECT WITH CLASS NAME
				if(is_object($v) && isset($v->className)){

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

			//SEND BACK THE MODEL DATA
			return (object)$this->_data;
		}

		//HANDLE STATUS CHECKS TO SIMULATE API REQUESTS
		public function status(){
			return true;
		}
	}