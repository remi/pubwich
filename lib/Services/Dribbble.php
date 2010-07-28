<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Dribbble
	 * @description Fetch Dribbble shots
	 * @version 1.1 (20100728)
	 * @author Rémi Prévost (exomel.com)
	 * @methods DribbbleShots
	 */

	class Dribbble extends Service {

		public function __construct( $config ){
			parent::__construct( $config );
			$this->callback_function = array( Pubwich, 'json_decode' );
		}

		public function populateItemTemplate( &$item ) {
			return array(
				'player_avatar_url' => $item->player->avatar_url,
				'player_name' => $item->player->name,
				'player_location' => $item->player->location,
				'player_url' => $item->player->url,
				'player_id' => $item->player->id,
				'id' => $item->id,
				'title' => $item->title,
				'url' => $item->url,
				'image_url' => $item->image_url,
				'image_teaser_url' => $item->image_teaser_url,
				'height' => $item->height,
				'width' => $item->width,
			);
		}

	}

	class DribbbleShots extends Dribbble {

		public function __construct( $config ){
			parent::__construct( $config );
			$this->callback_function = array( Pubwich, 'json_decode' );
			$this->setURL( sprintf('http://api.dribbble.com/players/%s/shots', $config['username']));
			$this->setURLTemplate(sprintf('http://dribbble.com/players/%s', $config['username']));
			$this->setItemTemplate('<li><a href="{{url}}"><strong>{{{title}}}</strong> <img src="{{image_teaser_url}}" alt="{{title}}" /></a></li>'."\n");
		}

		public function getData() {
			return parent::getData()->shots;
		}

	}
