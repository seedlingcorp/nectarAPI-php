<?php
	
	namespace Nectar\Model;

	class VenueInformation extends \Nectar\Core\NectarModel{

		public function __construct($data = []){
			parent::__construct($data);
		}

		public function mapsImageSrc(){

			return 'https://maps.googleapis.com/maps/api/staticmap?center='.$this->streetAddressEncoded().'&zoom=13&scale=2&size=600x300&maptype=roadmap&format=png&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:1%7C'.$this->streetAddressEncoded().'&key=AIzaSyDJvksJLohl4Ss98t3gHcCWJVadhu2-L2E';
		}

		public function streetAddress($encode = false){

			if($encode) return $this->streetAddressEncoded();

			$parts = [
				'street' => implode(' ', array_filter([$this->data()->street_one, $this->data()->street_two])),
				'city_state' => $this->data()->city.' '.$this->data()->state.', '.$this->data()->zip,
				//'zip' => $this->data()->zip
			];

			return implode(', ', array_filter($parts));
		}

		public function streetAddressEncoded(){
			return str_replace('%2C', ',', urlencode($this->streetAddress()));
		}
	}