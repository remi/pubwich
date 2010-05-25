<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Delicious
	 * @description Fetch Del.icio.us bookmarks
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	Pubwich::requireServiceFile( 'RSS' );
	class Delicious extends RSS {

		public function __construct( $config ){
			$config['link'] = 'http://del.icio.us/'.$config['username'].'/';
			$config['url'] = sprintf( 'http://feeds.delicious.com/v2/rss/%s?count=%s', $config['username'], $config['total'] );
			parent::__construct( $config );
			$this->setItemTemplate('<li><a href="{{{link}}}">{{{title}}}</a></li>'."\n");
		}

	}

