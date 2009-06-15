<?php

	class Flickr extends Service {

		private $url_template = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=%s&user_id=%s&per_page=%d';

		public function __construct( $config ){
			list($key, $id, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $key, $id, $total ) );
			parent::__construct();
		}

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->photos->photo;
		}

		/**
		 * Retourne l'URL absolu d'une photo sur le site de Flickr
		 * 
		 * @param array $photo La photo
		 * @return string
		 */	
		public function getAbsoluteUrl( $photo ) {
			return sprintf( 'http://farm%d.static.flickr.com/%s/%s_%s_s.jpg',
				$photo['farm'],
				$photo['server'],
				$photo['id'],
				$photo['secret']
			);
		}
	}
