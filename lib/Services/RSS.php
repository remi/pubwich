<?php

	class RSS extends Service {
	
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
			return $data->channel->item;
		}

		/**
		 * Retourne un item formatté selon le gabarit
		 *
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			return array(
						'link' => htmlspecialchars( $item->link ),
						'title' => SmartyPants( $item->title ),
						'date' => Pubwich::time_since( $item->pubDate ),
						'description' => Markdown( $item->description )
			);
		}


	}
