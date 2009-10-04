<?php
	/**
	 * @classname Delicious
	 * @description Fetch Del.icio.us bookmarks
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	require_once( dirname(__FILE__) . '/RSS.php' );
	class Delicious extends RSS {
	
		public function __construct( $config ){
			$config['link'] = 'http://del.icio.us/'.$config['username'].'/';
			$config['url'] = sprintf( 'http://feeds.delicious.com/v2/rss/%s?count=%s', $config['username'], $config['total'] );
			parent::__construct( $config );
			$this->setItemTemplate('<li><a href="{%link%}">{%title%}</a></li>'."\n");
		}

	}

