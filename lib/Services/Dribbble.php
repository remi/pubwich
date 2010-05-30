<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Dribbble
	 * @description Fetch Dribbble shots
	 * @version 1.0 (20100530)
	 * @author Rémi Prévost (exomel.com)
	 * @methods DribbbleShots
	 */

	Pubwich::requireServiceFile( 'RSS' );
	class Dribbble extends RSS {

		public function __construct( $config ){
			parent::__construct( $config );
		}

		public function populateItemTemplate( &$item ) {
			$description = (string) $item->description;
			preg_match('/src=\"(http.*(jpg|jpeg|gif|png))/', $description, $matches);
			return parent::populateItemTemplate( $item ) + array(
				'image' => count($matches) > 1 ? $matches[1] : ''
			);
		}

	}

	class DribbbleShots extends Dribbble {

		public function __construct( $config ){
			$config['link'] = sprintf( 'http://dribbble.com/players/%s/', $config['username'].'/' );
			$config['url'] = sprintf( 'http://dribbble.com/players/%s/shots.rss', $config['username'] );
			parent::__construct( $config );
			$this->setItemTemplate('<li><a href="{{{link}}}"><strong>{{{title}}}</strong> <img src="{{{image}}}" alt="{{title}}" /></a></li>'."\n");
		}

	}
