<?php

	class Atom extends Service {
	
		public function __construct( $config ){
			$this->setURL( $config['url'] );
			$this->total = $config['total'];

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li><a href="{%link%}">{%title%}</a> {%date%}</li>'."\n");
			$this->setURLTemplate( $config['link'] );

			parent::__construct();
		}

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->entry;
		}

		/**
		 * Retourne un item formatté selon le gabarit
		 *
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
