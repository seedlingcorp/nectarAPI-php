<?php
	
	namespace Nectar\Model;

	class CartItem extends \Nectar\Core\NectarModel{

		public function __construct($data = []){
			parent::__construct($data);
		}

		public function apiUrl(){

			$data = $this->data();

			return 'cart/'.(isset($data->id) ? $data->id : '');
		}

		public function create($model, $quantity = 1){

			$model_class_parts = explode('\\', get_class($model));
			$model_class = 'App\\'.end($model_class_parts);


			$res = $this->request()->post('cart/add', [
				'quantity' 			=> $quantity,
				'parentable_id' 	=> $model->data()->id,
				'parentable_type' 	=> $model_class,
			]);

			return $res->status() ? $res->results() : $res;
		}

		public function update($data = []){

			$update_data = array_merge($this->data(), $data);

			$res = $this->request()->post('cart/update/'.$this->data()->id, $update_data);

			return $res->status() ? $res->results() : $res;

		}

		public function delete(){

			$res = $this->request()->post('cart/delete/'.$this->data()->id);

			return $res->status() ? $res->results() : $res;
		}
	}