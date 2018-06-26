<?php

	namespace Nectar\Core;

	class NectarCollection extends \Seedling\Nectar\NectarAPI implements \Iterator{

		private $_data 		= [];
		private $_position 	= 0;

		public function __construct($data = []){
			foreach($data as $v) $this->push($v);
		}

		public function status(){
			return true;
		}

		public function rewind(){
			$this->_position = 0;
			return $this;
		}

		public function current(){
			return $this->_data[$this->_position];
		}

		public function key(){
			return $this->_position;
		}

		public function next(){
			++$this->_position;
		}

		public function valid($position = null){

			$position = is_null($position) ? $this->_position : $position;
			return isset($this->_data[$position]);
		}

		public function data(){
			return $this->_data;
		}

		public function push($data){		
			
			if(is_object($data) && isset($data->className)){

				$class_name = '\Nectar\Model\\'.str_replace('Nectar', '', $data->className);

				if(class_exists($class_name)){
					$data = new $class_name((array)$data->data);
				}
				else{
					$data = (array)$data->data;
				}

				
			}

			$this->_data[] = $data;
			return $this;
		}

		public function count(){
			return count($this->_data);
		}

		public function first(){
			return $this->_data[0];
		}

		public function last(){
			return $this->_data[$this->count()-1];
		}

		public function nth($position){
			return $this->_data[$position];
		}

		public function toArray(){
			$res = [];
			foreach($this->_data as $k => $v){
				if(is_object($v) && method_exists($v, 'toArray')){
					$res[$k] = $v->toArray();
					continue;
				}
				
				$res[$k] = $v;
			}
		}

		public function toArrayDeep($full = true){
			$res = [];

			foreach($this->_data as $k => $v){
				if(is_object($v)){
					if(method_exists($v, 'toArrayDeep')){
						$res[$k] = $v->toArrayDeep(true);
					}
					else{
						$res[$k] = json_decode(json_encode($v), true);
					}
				}
				else{
					$res[$k] = $v;
				}
			}

			return $res;
		}

		/*
		public function toJson(){
			
			$res = [];

			foreach($this->_data as $k => $v){
				if(is_object($v)){
					if(method_exists($v, 'toJson')){
						$res[$k] = json_decode($v, true);
					}
					else{
						$res[$k] = $v;
					}
				}
				elseif(is_array($v)){
					foreach($v as $x => $y){
						if(is_object($y)){
							if(method_exists($y, 'toJson')){
								$res[$k][$x] = json_decode($y->toJson(), true);
							}
							else{
								$res[$k][$x] = $y;
							}
						}
						else{

						}
					}
				}
			}
		}
		*/
	}