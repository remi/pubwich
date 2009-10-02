<?php
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
			$this->setItemTemplate('<li><a href="{%link%}">{%title%}</a> {%date%}</li>'."\n");
			$this->setURLTemplate( $config['link'] );
			parent::__construct( $config );
		}

		/**
		 * Surcharge de parent::getData()
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->entry;
		}

		/**
		 * Retourne un item formatté selon le gabarit
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$link = $item->link->attributes();
			$link = $link->href;
			return array(
						'link' => htmlspecialchars( $link ),
						'title' => trim( SmartyPants( $item->title ) ),
						'date' => Pubwich::time_since( $item->published ),
						'content' => SmartyPants( Markdown( $item->content ) ),
			);
		}

	}
