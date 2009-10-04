<?php
	/**
	 * @classname Readernaut
	 * @description Fetch Readernaut books
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	class Readernaut extends Service {
		
		private $size;

		public function __construct( $config ){
			$this->setURL( sprintf( 'http://readernaut.com/api/v1/xml/%s/books/?order_by=-created', $config['username'] ) );
			$this->total = $config['total'];
			$this->username = $config['username'];
			$this->size = $config['size'];

			$this->setItemTemplate('<li><a class="clearfix" href="{%link%}"><img src="{%image%}" width="{%size%}" alt="{%title%}" /><strong><span>{%title%}</span> {%author%}</strong></a></li>'."\n");
			$this->setURLTemplate('http://www.readernaut.com/'.$config['username'].'/');

			parent::__construct( $config );
		}

		/**
		 * Surcharge de parent::getData()
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->reader_book;
		}

		/**
		 * Retourne un item formatté selon le gabarit
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			return array(
						'id' => $item->reader_book_id,
						'link' => $item->book_edition->permalink, 
						'title' => $item->book_edition->title,
						'subtitle' => $item->book_edition->subtitle,
						'author' => $item->book_edition->authors->author, 
						'image' => $item->book_edition->covers->cover_small,
						'image_medium' => $item->book_edition->covers->cover_medium,
						'image_large' => $item->book_edition->covers->cover_large,
						'size' => $this->size,
						'created' => Pubwich::time_since( $this->book_edition->created ),
						'modified' => Pubwich::time_since( $this->book_edition->modified ),
						'isbn' => $this->book_edition->isbn,
						);
		}
			
	}
