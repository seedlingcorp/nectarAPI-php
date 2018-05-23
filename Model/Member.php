<?php
	
	namespace Nectar\Model;

	class Member extends \Nectar\Core\NectarModel{

		public function __construct($data = []){
			parent::__construct($data);
		}

		public function invoices(){
			return $this->relationship('invoices');
		}

		public function organizations(){
			return $this->relationship('organizations');
		}
	}