<?php
	
	namespace Nectar\Model;

	class Pagination extends \Nectar\Core\NectarModel implements \Iterator{

		//SET THE OBJECT VARS
		protected $_position = 0;

		//RUN AT BOOT
		public function __construct($data = []){
			
			//RUN PARENT CONSTRUCTOR METHOD
			parent::__construct($data);

			//REMOVE THE ORM DATA
			unset($this->_orm_data);

			//CONVERT PAGES TO A COLLECTION
			$this->_data['navigation']->pages = new \Nectar\Core\NectarCollection($this->_data['navigation']->pages);
	
		}

		//GET THE RECORDS
		public function records(){
			return $this->data()->records;
		}

		//GET THE NAVIGATION
		public function navigation(){
			return $this->data()->navigation;
		}

		//GET THE NAVIGATION PAGES
		public function navPages(){
			return $this->navigation()->pages;
		}

		//GO BACK TO THE BEGINNING
		public function rewind(){
			$this->_position = 0;
			return $this;
		}

		//GET THE CURRENT OBJECT
		public function current(){
			return $this->records()->nth($this->_position);
		}

		//GET THE CURRENT KEY
		public function key(){
			return $this->_position;
		}

		//MOVE FORWARD TO THE NEXT KEY
		public function next(){
			++$this->_position;
		}

		//MAKE SURE THAT THE REQUESTED RECORD EXISTS
		public function valid(){
			return $this->records()->valid($this->_position);
		}
	}