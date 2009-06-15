<?php

	class RSS extends Service {
	
		private $url_template = '%s';

		public function __construct( $config ){
			list($url, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $url ) );
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

