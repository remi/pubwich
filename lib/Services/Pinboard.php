<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Pinboard
	 * @description Fetch Pinboard bookmarks
	 * @version 1.0 (20110115)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	Pubwich::requireServiceFile( 'RSS' );
	class Pinboard extends RSS {

		public function __construct( $config ){
			$config['link'] = 'http://pinboard.in/u:'.$config['username'].'/';
			$config['url'] = sprintf( 'http://feeds.pinboard.in/rss/secret:%s/u:%s/', $config['secret'], $config['username'] );
			parent::__construct( $config );
			$this->setItemTemplate('<li><a href="{{{link}}}">{{{title}}}</a></li>'."\n");
		}

		// This is RSS 1.0 (RDF), not RSS 2.0…
		public function getData() {
			$data = parent::getParentData();
			return $data->item;
		}

	}

