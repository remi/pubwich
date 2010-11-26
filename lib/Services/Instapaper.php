<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Instapaper
	 * @description Fetch Instapaper archived items
	 * @version 1.0 (20101125)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	Pubwich::requireServiceFile( 'RSS' );
	class Instapaper extends RSS {

		public function __construct( $config ){
			$config['link'] = 'http://www.instapaper.com/';
			$config['url'] = sprintf( 'http://www.instapaper.com/archive/rss/%s/%s', $config['userid'], $config['token'] );
			parent::__construct( $config );
			$this->setItemTemplate('<li><a href="{{{link}}}">{{{title}}}</a></li>'."\n");
		}

	}

