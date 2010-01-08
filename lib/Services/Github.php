<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname GitHub
	 * @description Fetch GitHub user public activity feed
	 * @version 1.0 (20100107)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	Pubwich::requireServiceFile( 'Atom' );
	class Github extends Atom {
	
		/**
		 * @constructor
		 */
		public function __construct( $config ){
			$config['url'] = sprintf( 'http://github.com/%s.atom', $config['username'] );
			$config['link'] = 'http://github.com/'.$config['username'].'/';
			parent::__construct( $config );
			$this->setItemTemplate('<li class="clearfix"><a href="{%link%}">{%title%}</a></li>'."\n");
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			return parent::populateItemTemplate( $item ) + array(
			);
		}

	}
