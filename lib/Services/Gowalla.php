<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Gowalla
	 * @description Get last check-in from Gowalla
	 * @version 1.0 (20100209)
	 * @author Rémi Prévost (exomel.com)
	 * @methods none
	 */

	class Gowalla extends Service {

		public $http_headers;

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			$this->setURL( sprintf( 'http://%s:%s@api.gowalla.com/users/%s', $config['username'], $config['password'], $config['username'] ) );
			$this->username = $config['username'];
			$this->setItemTemplate( '<li class="clearfix"><span class="date">{%date%}</span><a class="spot" href="{%url%}"><strong>{%name%}</strong> <img src="{%image%}" alt="" /></a><span class="comment">{%comment%}</span></li>'."\n" );
			$this->setURLTemplate( 'http://gowalla.com/users/'.$config['username'].'/' );
			$this->callback_function = array( Pubwich, 'json_decode' );
			$this->http_headers = array(
				'Accept: application/json'
			);
			parent::__construct( $config );
		}

		public function populateItemTemplate( &$item ) {
			return array(
				'comment' => $item->comment,
				'date' => $item->created_at,
				'image' => $item->spot->image_url,
				'name' => $item->spot->name,
				'url' => 'http://gowalla.com/'.$item->spot->url,
			);
		}

		public function getData() {
			$data = parent::getData();
			return array( $data->last_visit );
		}

	}
