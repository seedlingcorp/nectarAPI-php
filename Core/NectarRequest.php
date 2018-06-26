<?php

	namespace Nectar\Core;

	class NectarRequest extends\Seedling\Nectar\NectarAPI{

		public function __construct(){

		}

		private function url($path){
			return rtrim($this->config()->url($path), '/');
		}

		private function isEncodedUrlData($url){
			return substr($url, 0, 15) === '_NECTARURLBS64.';
		}

		private function parseEncodedUrlData($url){
			return json_decode(base64_decode(substr($url, 15)), true);
		}

		public function get($url, $data = [], $callback = null){

			$encoded = false;
			if($this->isEncodedUrlData($url)){
				$encoded = true;
				$parts = $this->parseEncodedUrlData($url);
				$url = $parts['url'];
				$data = $parts['filters'];
			}		
			
			$ch = $this->loadCurl();
			curl_setopt($ch, CURLOPT_URL, $this->url($url).'?'.http_build_query($data));
			$res = curl_exec($ch);
			curl_close($ch);

			if(!json_decode($res)){
				pr($this->url($url));
				pr($res);
			}

			return $this->parseResponse($res, $callback, $encoded);
		}

		public function post($url, $data = [], $callback = null, $debug = false){

			$encoded = false;
			if($this->isEncodedUrlData($url)){
				$encoded = true;
				$parts = $this->parseEncodedUrlData($url);
				$url = $parts['url'];
				$data = $parts['filters'];
			}

			$ch = $this->loadCurl();
			curl_setopt($ch, CURLOPT_URL, $this->url($url));
			curl_setopt($ch, CURLOPT_POST, 1);
			if(count($data)) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			$res = curl_exec($ch);
			curl_close($ch);

			

			//if($debug){
				//error_reporting(E_ALL);
			
				if(!json_decode($res)){
					pr($res);
				}
			//}

			
			
			

			return $this->parseResponse($res, $callback, $encoded);
		}

		public function put($url, $data = [], $callback = null){

		}

		public function delete($url, $data = [], $callback = null){

		}

		private function getHeaders($ch){
			$headers = [];

			if($this->config()->tokenHeader()) $headers[] = $this->config()->tokenHeader();
			if($this->config()->memberTokenHeader()) $headers[] = $this->config()->memberTokenHeader();

			if(!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			return $ch;
		}

		private function loadCurl(){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			return $this->getHeaders($ch);
		}

		private function parseResponse($res, $callback = null, $encoded = false){
			//pr('here');
			//pr($res);

			$res = new \Nectar\Core\NectarResponse($res);

			if($res->status() && is_callable($callback)) $callback($res->results());

			$res = $res->status() && $encoded ? $res->results() : $res;
		
			if($res->status() && $encoded && get_class($res->first()) == 'Nectar\Model\Pagination'){
				$res = $res->first();
			}

			return $res;
		}
	}