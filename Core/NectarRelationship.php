<?php

	namespace Nectar\Core;

	class NectarRelationship extends \Seedling\Nectar\NectarApi {

		public function __construct($url){

			$this->_api_url = $url;

			$this->_orm_data = (object)[
				'_where' 	=> [],
				'_whereRaw' => [],
				'_orderBy' 	=> [],
				'_limit' 	=> false,
				'_skip'		=> false,
				'_paginate' => false,
				'_count' 	=> false,
			];
		}

		public function where($field_name, $modifier, $value){
			$this->_orm_data->_where[] = [
				'field_name' 	=> $field_name,
				'modifier' 		=> $modifier,
				'value' 		=> $value
			];

			return $this;
		}

		public function whereRaw($query){
			$this->_orm_data->_whereRaw[] = $query;
			return $this;
		}

		public function orderBy($field_name, $direction = 'ASC'){
			$this->_orm_data->_orderBy[] = [
				'field_name' 	=> $field_name,
				'direction' 	=> $direction,
			];

			return $this;
		}

		public function limit($val){
			$this->_orm_data->_limit = $val;

			return $this;
		}

		public function skip($val){
			$this->_orm_data->_skip = $val;

			return $this;
		}

		public function count($val = true){
			$this->_orm_data->_count = $val;

			return $this;
		}

		public function paginate($val = 20, $page = 1){
			if($val === false){
				$this->_orm_data->_paginate = false;

				return $this;
			}

			$this->_orm_data->_paginate = [
				'show' => $val,
				'page' => $page
			];

			return $this;
		}

		public function get(){

			//INITIALIZE SEND DATA ARRAY
			$send_data = [];

			//BUILD SEND DATA ARRAY
			if(is_array($this->_orm_data->_where) && !empty($this->_orm_data->_where)) $send_data['where'] = $this->_orm_data->_where;
			if(is_array($this->_orm_data->_whereRaw) && !empty($this->_orm_data->_whereRaw)) $send_data['whereRaw'] = $this->_orm_data->_whereRaw;
			if(is_array($this->_orm_data->_orderBy) && !empty($this->_orm_data->_orderBy)) $send_data['orderBy'] = $this->_orm_data->_orderBy;
			if($this->_orm_data->_limit) $send_data['limit'] = $this->_orm_data->_limit;
			if($this->_orm_data->_skip) $send_data['skip'] = $this->_orm_data->_skip;
			if(is_array($this->_orm_data->_paginate) && !empty($this->_orm_data->_paginate)) $send_data['paginate'] = $this->_orm_data->_paginate;
			if($this->_orm_data->_count) $send_data['count'] = $this->_orm_data->_count;

			//PERFORM THE REQUEST
			$res = $this->request()->post($this->_api_url, $send_data);

			//CHECK IF THE REQUEST WAS SUCCESSFULL
			if($res->status()){

				//SET THE RETURN OBJECT TO BE THE RESULTS
				$res = $res->results();

				//HANDLE PAGINATION RESULTS
				if(is_array($this->_orm_data->_paginate)) $res = $res->first();
			}

			//RETURN THE RESULTS
			return $res;
		}
	}