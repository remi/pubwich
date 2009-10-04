<?php
	/**
	 * @classname Facebook
	 * @description Fetch Facebook statuses
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	require_once( dirname(__FILE__) . '/RSS.php' );
	class Facebook extends RSS {
	
		public function __construct( $config ){
			$config['link'] = 'http://www.facebook.com/'.$config['username'].'/';
			$config['url'] = sprintf( 'http://www.facebook.com/feeds/status.php?id=%d&viewer=%d&key=%s&format=rss20', $config['id'], $config['id'], $config['key'] );
			parent::__construct( $config );
			$this->setItemTemplate('<li><a href="{%link%}">{%title%}</a> {%date%}</li>'."\n");
		}

	}
