<?php

	class RSS extends Service {
	
		private $url_template = '%s';
		public $feed_url;

		public function __construct( $config ){
			list($url, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $url ) );
			$this->feed_url = $url;
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
