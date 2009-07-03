<?php

	class Readernaut extends Service {
		
		public $username;
		private $size;

		public function __construct( $config ){
			$this->setURL( sprintf( 'http://readernaut.com/api/v1/xml/%s/books/?order_by=-created', $config['username'] ) );
			$this->total = $config['total'];
			$this->username = $config['username'];
			$this->size = $config['size'];

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li><a class="clearfix" href="{%link%}"><img src="{%image%}" width="{%size%}" alt="{%title%}" /><strong><span>{%title%}</span> {%author%}</strong></a></li>'."\n");
			$this->setURLTemplate('http://www.readernaut.com/'.$config['username'].'/');

			parent::__construct();
		}

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->reader_book;
		}

		/**
		 * Retourne un item formatté selon le gabarit
		 *
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			return array(
						'link' => $item->book_edition->permalink, 
						'title' => SmartyPants( $item->book_edition->title ),
						'author' => SmartyPants( $item->book_edition->authors->author ), 
						'image' => $item->book_edition->covers->cover_small,
						'size' => $this->size
						);
		}
			
	}
