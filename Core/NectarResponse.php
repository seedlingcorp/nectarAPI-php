<?php

	namespace Nectar\Core;

	class NectarResponse extends\Seedling\Nectar\NectarAPI{

		protected $status 	= 'error';
		protected $code 	= 500;
		protected $results;

		public function __construct($data = null){
			$this->results = new \Nectar\Core\NectarCollection;
			return $this->parseData(json_decode($data));
		}

		public function status(){
			return $this->status == 'success';
		}

		public function errors(){
			return $this->status() ? new \Nectar\Core\NectarCollection : $this->results();
		}

		public function results(){
			return $this->results;
		}

		public function data(){
			return $this->_data;
		}

		private function parseData($data = null){
			
			if($data && is_object($data)){
				$this->status 		= $data->status;
				$this->code 		= $data->code;

				if(isset($data->errors)){
					foreach($data->errors as $error) $this->results->push($error);
				}
				else{

					//HANDLE COUNTING
					if(is_array($data->results) && count($data->results) === 1 && $data->results[0]->className === 'NectarApiModelCount'){
						$this->results = $data->results[0]->data->count;
					}

					//ADD THE RESULTS
					else{

						if(!is_array($data->results)){
							$this->results = $this->results->push($data->results)->first();
						}
						else{

							foreach($data->results as $k => $v) $this->results->push($v);
						}
					}
				}
			}
			else{
				$this->results->push("An unknown error occurred.");
			}

			return $this->status() ? $this->results() : $this;

		}
	}