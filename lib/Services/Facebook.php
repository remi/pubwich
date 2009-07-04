<?php

	class Facebook extends Service {
	
		public function __construct( $config ){
			$this->setURL( sprintf( 'http://www.facebook.com/feeds/status.php?id=%d&viewer=%d&key=%s&format=rss20', $config['id'], $config['id'], $config['key'] ) );
			$this->total = $config['total'];
			$this->username = $config['username'];

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li><a href="{%link%}">{%title%}</a> {%date%}</li>'."\n");
			$this->setURLTemplate('http://www.facebook.com/'.$config['username'].'/');

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
						'title' => $item->title,
						'date' => Pubwich::time_since( $item->pubDate ),
						'author' => $item->author,
						);
		}
			
	}
