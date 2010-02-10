<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Gowalla
	 * @description Get last check-in from Gowalla
	 * @version 1.05 (20100210)
	 * @author Rémi Prévost (exomel.com)
	 * @methods none
	 */

	class Gowalla extends Service {

		public $base = 'http://gowalla.com';

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			$this->setURL( sprintf( 'http://%s:%s@api.gowalla.com/users/%s', $config['username'], $config['password'], $config['username'] ) );
			$this->username = $config['username'];
			$this->setItemTemplate( '<li class="clearfix"><span class="date">{%date%}</span><a class="spot" href="{%url%}"><strong>{%name%}</strong> <img src="{%image%}" alt="" /></a><span class="comment">{%comment%}</span></li>'."\n" );
			$this->setURLTemplate( $this->base.'/users/'.$config['username'].'/' );
			$this->callback_function = array( Pubwich, 'json_decode' );
			$this->http_headers = array(
				'Accept: application/json'
			);

			if ( $config['key'] ) {
				$this->http_headers[] = sprintf( 'X-Gowalla-API-Key: %s', $config['key'] );
			}

			parent::__construct( $config );
		}

		public function populateItemTemplate( &$item ) {
			return array(
				'comment' => $item->comment,
				'date' => Pubwich::time_since( $item->created_at ),
				'image' => $item->spot->image_url,
				'thumbnail' => $item->spot->small_image_url,
				'name' => $item->spot->name,
				'url' => $this->base.$item->spot->url,
			);
		}

		public function getData() {
			$data = parent::getData();
			return array( $data->last_visit );
		}

	}

	/**
	 * @TODO http://api.gowalla.com/users/<username>
	 */
	class GowallaUser extends Gowalla {
	}

	/**
	 * @TODO http://api.gowalla.com/users/<username>/stamps?limit=<total>
	 */
	class GowallaStamps extends Gowalla {
	}
