<?php

	class Readernaut extends Service {
		
		private $url_template	= 'http://readernaut.com/api/v1/xml/%s/books/?order_by=-created';

		public function __construct( $config ){
			list($username, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $username ) );
			$this->total = $total;
			parent::__construct();
		}

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->reader_book;
		}
			
	}
