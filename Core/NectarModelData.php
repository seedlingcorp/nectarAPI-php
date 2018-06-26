<?php
	
	namespace Nectar\Core;

	class NectarModelData {

		public function __construct($data){
			if(is_object($data) || is_array($data)){
				foreach($data as $k => $v){
					$this->$k = $v;
				}
			}
		}
	}