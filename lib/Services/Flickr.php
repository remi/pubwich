<?php
	/**
	 *
	 * @classname Flickr
	 * @description Retreives photos from Flickr
	 * @version 1.1 (20090927)
	 * @author Rémi Prévost (exomel.com)
	 * @methods user* group
	 *
	 */

	class Flickr extends Service {

		private $compteur, $row;

		public function __construct( $config ){
			$this->compteur = 0;
			$this->method = isset( $config['method'] ) ? $config['method'] : 'user';

			if ( $this->method == 'user' ) {
				$this->setURL( sprintf( 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=%s&user_id=%s&per_page=%d', $config['key'], $config['userid'], $config['total'] ) );
			} elseif ( $this->method == 'group' ) {
				$this->setURL( sprintf( 'http://api.flickr.com/services/rest/?method=flickr.groups.pools.getPhotos&api_key=%s&group_id=%s&per_page=%d', $config['key'], $config['groupid'], $config['total'] ) );
			}

			$this->username = $config['username'];
			$this->row = $config['row'];

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li{%classe%}><a href="{%link%}"><img src="{%photo%}" alt="{%title%}" /></a></li>'."\n");
			$this->setURLTemplate('http://www.flickr.com/photos/'.$config['username'].'/');

			parent::__construct();
		}

		/**
		 * Overcharge parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->photos->photo;
		}

		/**
		 * Return an array of key->value using the item data
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$this->compteur++;
			if ( $this->method == 'group' ) {
				$link = $this->urlTemplate . $item['id'].'/';
			}
			if ( $this->method == 'group' ) {
				$link = 'http://www.flickr.com/photos/'.$item['owner'].'/'.$item['id'];
			}
			return array(
						'link' => $link,
						'title' => Smartypants( $item['title'] ),
						'photo' => $this->getAbsoluteUrl( $item ),
						'classe' => ($this->compteur % $this->row == 0 ) ? ' class="derniere"' : ''
						);
		}

		/**
		 * Return a Flickr photo URL
		 * @param array $photo Photo item
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
