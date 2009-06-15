<?php

	class Facebook extends Service {
	
		private $url_template = 'http://www.facebook.com/feeds/status.php?id=%d&viewer=%d&key=%s&format=rss20';
		public $total = 0;

		public function __construct( $config ){
			list($id, $key, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $id, $id, $key ) );
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
			return $data->channel->item;
		}
			
	}

