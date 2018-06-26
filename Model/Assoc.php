<?php
	
	namespace Nectar\Model;

	class Assoc extends \Nectar\Core\NectarModel{

		public function __construct($data = []){
			parent::__construct($data);
			$this->_orm_data->_api_url = 'association';
			$this->_api_url = 'association';
		}

		public function members(){
			return $this->relationship('members');
		}

		public function invoices(){
			return $this->relationship('invoices');
		}

		public function event(){
			return $this->relationship('events');
		}
	}