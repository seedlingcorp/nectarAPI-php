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

		public function profilePictureUrl($thumb = false){

			$data = $this->data();
			if(isset($data->image)){
				return $thumb ? $data->image->data()->thumbnail : $data->image->data()->fullUrl;
			}

			return '//placehold.it/40x40';
		}
	}