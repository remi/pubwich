<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Atom
	 * @description Fetch Atom feeds
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	class Atom extends Service {

		public function __construct( $config ){
			$this->total = $config['total'];
			$this->setURL( $config['url'] );
			$this->setHeaderLink( array( 'url' => $config['url'], 'type' => 'application/atom+xml' ) );
			$this->setItemTemplate('<li><a href="{%link%}">{%title%}</a> {%date%}</li>'."\n");
			$this->setURLTemplate( $config['link'] );
			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->entry;
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$link = $item->link->attributes()->href;
			return array(
						'link' => htmlspecialchars( $link ),
						'title' => trim( $item->title ),
						'date' => Pubwich::time_since( $item->published ),
						'content' => $item->content,
			);
		}

	}
