<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Gowalla
	 * @description Get last check-ins from Gowalla
	 * @version 1.1 (20100210)
	 * @author Rémi Prévost (exomel.com)
	 * @methods GowallaUser GowallaUserStamps
	 */

	class Gowalla extends Service {

		public $base = 'http://gowalla.com';

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			$this->username = $config['username'];
			$this->setURLTemplate( $this->base.'/'.$config['username'].'/' );
			$this->callback_function = array( Pubwich, 'json_decode' );
			$this->http_headers = array(
				'Accept: application/json'
			);

			if ( $config['key'] ) {
				$this->http_headers[] = sprintf( 'X-Gowalla-API-Key: %s', $config['key'] );
			}

			parent::__construct( $config );
		}

	}

	class GowallaUser extends Gowalla {

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			$this->setURL( sprintf( 'http://%s:%s@api.gowalla.com/users/%s', $config['username'], $config['password'], $config['username'] ) );
			$this->setItemTemplate( '<li class="clearfix"><span class="date">{{{date}}}</span><a class="spot" href="{{{url}}}"><strong>{{{name}}}</strong> <img src="{{{image}}}" alt="" /></a><span class="comment">{{{comment}}}</span></li>'."\n" );
			parent::__construct( $config );
		}

		public function getData() {
			$data = parent::getData();
			return array( $data->last_visit );
		}

		public function populateItemTemplate( &$item ) {
			$last_spot = $item->last_checkin[0];
			return array(
				'first_name' => $item->first_name,
				'last_name' => $item->last_name,
				'comment' => $last_spot->message,
				'date' => Pubwich::time_since( $last_spot->created_at ),
				'image' => $last_spot->image_url,
				'name' => $last_spot->name,
				'url' => $this->base.$last_spot->url,
			);
		}

	}

	class GowallaUserStamps extends Gowalla {

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			$this->total = $config['total'];
			$this->setURL( sprintf( 'http://%s:%s@api.gowalla.com/users/%s/stamps?limit=%d', $config['username'], $config['password'], $config['username'], $config['total'] ) );
			$this->setItemTemplate( '<li><a href="{{{url}}}"><img src="{{{image}}}" width="20" alt="" /><strong>{{{name}}}</strong><small class="date">{{{date}}}</small></a></li>'."\n" );
			parent::__construct( $config );
		}

		public function getData() {
			return parent::getData()->stamps;
		}

		public function populateItemTemplate( &$item ) {
			return array(
				'date' => Pubwich::time_since( $item->last_checkin_at ),
				'image' => $item->spot->image_url,
				'name' => $item->spot->name,
				'url' => $this->base.$item->spot->url,
				'visits' => $item->checkins_count,
			);
		}

	}

	class GowallaUserTopSpots extends Gowalla {

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			$this->total = $config['total'];
			$this->setURL( sprintf( 'http://%s:%s@api.gowalla.com/users/%s/top_spots', $config['username'], $config['password'], $config['username'] ) );
			$this->setItemTemplate( '<li class="clearfix"><span class="visits">{{{visits}}}</span><a class="spot" href="{{{url}}}"><strong>{{{name}}}</strong> <img src="{{{image}}}" alt="" /></a></li>'."\n" );
			parent::__construct( $config );
		}

		public function getData() {
			return parent::getData();
		}

		public function populateItemTemplate( &$item ) {
			return array(
				'image' => $item->image_url,
				'name' => $item->name,
				'url' => $this->base.$item->url,
				'visits' => $item->user_checkins_count,
			);
		}

	}
