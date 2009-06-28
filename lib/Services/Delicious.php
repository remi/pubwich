<?php

	class Delicious extends Service {
	
		private $url_template = 'http://feeds.delicious.com/v2/rss/%s?count=%s';
		public $username;

		public function __construct( $config ){
			list($username, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $username, $total ) );
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
			return $data->channel->item;
		}
			
	}

