<?php

	class Vimeo extends Service {
	
		private $url_template = 'http://vimeo.com/api/%s/clips.xml';
		public $username;

		public function __construct( $config ){
			list($username, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $username ) );
			$this->total = $total;
			$this->username = $username;
			parent::__construct();
		}

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->clip;
		}
			
	}

