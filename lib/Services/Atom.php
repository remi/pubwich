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

		private $dateFormat;

		public function __construct( $config ){
			$this->total = $config['total'];
			$this->dateFormat = $config['date_format'];
			$this->setURL( $config['url'] );
			$this->setHeaderLink( array( 'url' => $config['url'], 'type' => 'application/atom+xml' ) );
			$this->setItemTemplate('<li><a href="{{link}}">{{{title}}}</a> {{{date}}}</li>'."\n");
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
			$date = $item->published ? $item->published : $item->updated;
			return array(
						'link' => htmlspecialchars( $link ),
						'title' => trim( $item->title ),
						'date' => Pubwich::time_since( $date ),
						'absolute_date' => date($this->dateFormat, strtotime($date)),
						'content' => $item->content,
			);
		}

	}
