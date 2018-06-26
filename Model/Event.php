<?php
	
	namespace Nectar\Model;

	class Event extends \Nectar\Core\NectarModel{

		public function __construct($data = []){
			parent::__construct($data);
		}

		public function scheduledDates(){
			//$start_date = $this->data()->start_time;
			//$end_date = $this->data()->end_time;

			$start_month = $this->start_time('F');
			$start_day = $this->start_time('j');

			$end_month = $this->end_time('F');
			$end_day = $this->end_time('j');

			$res = $start_month.' '.$start_day.' - '.$end_month.' '.$end_day;

			if($start_month === $end_month){

				$res = $start_month.' '.($start_day === $end_day ? $start_day : $start_day.'-'.$end_day);
			}

			if($res === 'TBD TBD') $res = 'TBD';

			return $res;
		}

		public function start_time($format = null){
			$date = $this->data()->start_time;

			return !is_null($date) ? (!is_null($format) ? $date->format($format) : $date) : 'TBD';
		}

		public function end_time($format = null){

			$date = $this->data()->end_time;

			return !is_null($date) ? (!is_null($format) ? $date->format($format) : $date) : 'TBD';

		}

		public function venue($get = false){

			$data = $this->data();

			$res = isset($data->venue) ? $data->venue : $this->relationship('venue');

			return $get ? $res->get() : $res;
		}

		public function venueInfo(){

			$venue = $this->venue();
			$venue = get_class($venue == 'Nectar\Core\NectarRelationship') ? $venue->get() : $venue;

			return is_object($venue) && get_class($venue) == 'Nectar\Model\Venue' ? $venue->information() : false;
		}
	}