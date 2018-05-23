<?php
	
	namespace Nectar\Model;

	class Invoice extends \Nectar\Core\NectarModel{

		public function __construct($data = []){
			parent::__construct($data);
		}

		public function download(){
			header("Location: {$this->data()->url}");
			exit;
		}
	}