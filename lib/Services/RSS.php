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
			$comments_count = $item->children('http://purl.org/rss/1.0/modules/slash/')->comments;
			return array(
						'link' => htmlspecialchars( $item->link ),
						'title' => SmartyPants( $item->title ),
						'date' => Pubwich::time_since( $item->pubDate ),
						'comments_link' => $item->comments,
						'comments_count' => $comments_count,
						'description' => SmartyPants( Markdown( $item->description ) )
			);
		}


	}
