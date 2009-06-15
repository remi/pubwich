<?php

	class Delicious extends Service {
	
		private $url_template = 'http://feeds.delicious.com/v2/rss/%s?count=%s';

		public function __construct( $config ){
			list($user, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $user, $total ) );
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

