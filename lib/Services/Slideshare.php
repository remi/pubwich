<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname SlideShare
	 * @description Retrieves data from SlideShare
	 * @version 1.0 (20100624)
	 * @author Rémi Prévost (exomel.com)
	 * @methods SlideShareUserSlideshows
	 */

	class SlideShare extends Service {

		public function __construct( $config ) {
			parent::__construct( $config );
		}

		public function generateTimestamp() {
			return round( time() / CACHE_LIMIT ) * CACHE_LIMIT;
		}

		public function setVariables( $config ) {
			$this->key = $config['key'];
			$this->secret = $config['secret'];
			$this->timestamp = $this->generateTimestamp();
			$this->hash = sha1( $this->secret . $this->timestamp );
		}

		public function populateItemTemplate( &$item ) {
			return array();
		}

	}

	class SlideShareUserSlideshows extends SlideShare {

		public function __construct( $config ) {
			parent::setVariables( $config );
			$this->setURL( sprintf( 'http://www.slideshare.net/api/2/get_slideshows_by_user?username_for=%s&api_key=%s&ts=%s&hash=%s', $config['username'], $this->key, $this->timestamp, $this->hash ) );
			$this->setURLTemplate( sprintf( 'http://www.slideshare.net/%s', $config['username'] ) );
			$this->setItemTemplate( '<li class="clearfix"><a href="{{{link}}}"><strong>{{{title}}}</strong> {{{date}}} <img src="{{thumbnail}}" alt="{{title}}" /></a></li>' );
			parent::__construct( $config );
		}

		public function getData() {
			return parent::getData()->Slideshow;
		}

		public function populateItemTemplate( &$item ) {
			return parent::populateItemTemplate( $items ) + array(
				'title' => $item->Title,
				'description' => $item->Description,
				'link' => $item->URL,
				'image' => $item->ThumbnailURL,
				'thumbnail' => $item->ThumbnailSmallURL,
				'embed' => $item->Embed,
				'date' => Pubwich::time_since( $item->Created ),
				'language' => $item->Language
			);
		}

	}
