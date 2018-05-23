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

		public function push($data){		
			
			if(is_object($data) && isset($data->className)){

				$class_name = '\Nectar\Model\\'.str_replace('Nectar', '', $data->className);

				$data = new $class_name((array)$data->data);
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
	}