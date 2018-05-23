<?php

	namespace Nectar\Core;

	class NectarRequest extends\Seedling\Nectar\NectarAPI{

		public function __construct(){

		}

		private function url($path){
			return $this->config()->url($path);
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

			return $this->parseResponse($res, $callback, $encoded);
		}

		public function post($url, $data = [], $callback = null){

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

			return $this->parseResponse($res, $callback, $encoded);
		}

		public function put($url, $data = [], $callback = null){

		}

		public function delete($url, $data = [], $callback = null){

		}

		private function loadCurl(){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if($this->config()->tokenHeader()) curl_setopt($ch, CURLOPT_HTTPHEADER, [$this->config()->tokenHeader()]);

			return $ch;
		}

		private function parseResponse($res, $callback = null, $encoded = false){

			$res = new \Nectar\Core\NectarResponse($res);

			if($res->status() && is_callable($callback)) $callback($res->results());

			$res = $res->status() && $encoded ? $res->results() : $res;
		
			if($res->status() && $encoded && get_class($res->first()) == 'Nectar\Model\Pagination'){
				$res = $res->first();
			}

			return $res;
		}
	}