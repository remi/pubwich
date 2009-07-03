<?php

	class Flickr extends Service {

		public $username;
		private $compteur, $row;

		public function __construct( $config ){
			$this->compteur = 0;
			$this->setURL( sprintf( 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=%s&user_id=%s&per_page=%d', $config['key'], $config['userid'], $config['total'] ) );
			$this->username = $config['username'];
			$this->row = $config['row'];

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li{%classe%}><a href="{%link%}"><img src="{%photo%}" alt="{%title%}" /></a></li>'."\n");
			$this->setURLTemplate('http://www.flickr.com/photos/'.$config['username'].'/');

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
		 * Retourne un item formatté selon le gabarit
		 *
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$this->compteur++;
			return array(
						'link' => $this->urlTemplate . $item['id'].'/',
						'title' => htmlspecialchars( $item['title'] ),
						'photo' => $this->getAbsoluteUrl( $item ),
						'classe' => ($this->compteur % $this->row == 0 ) ? ' class="derniere"' : ''
						);
		}

		/**
		 * Retourne l'URL absolu d'une photo sur le site de Flickr
		 * 
		 * @param array $photo La photo
		 * @return string
		 */	
		public function getAbsoluteUrl( $photo, $taille = 's' ) {
			return sprintf( 'http://farm%d.static.flickr.com/%s/%s_%s_%s.jpg',
				$photo['farm'],
				$photo['server'],
				$photo['id'],
				$photo['secret'],
				$taille
			);
		}
	}
