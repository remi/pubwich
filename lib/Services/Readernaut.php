<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Readernaut
	 * @description Fetch Readernaut books
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods ReadernautBooks ReadernautNotes
	 */

	class Readernaut extends Service {

		public $size;

		public function buildCache() {
			parent::buildCache();
		}

		public function getData() {
			return parent::getData();
		}

		public function init() {
			parent::init();
		}

		public function setVariables( $config ) {
			$this->total = $config['total'];
			$this->username = $config['username'];
			$this->size = $config['size'];
		}

	}

	class ReadernautBooks extends Readernaut {

		/**
		 * @constructor
		 */
		public function __construct( $config ){
			parent::setVariables( $config );
			$this->setURL( sprintf( 'http://readernaut.com/api/v1/xml/%s/books/?order_by=-created', $config['username'] ) );
			$this->setItemTemplate('<li><a class="clearfix" href="{{{link}}}"><img src="{{{image}}}" width="{{{size}}}" alt="{{{title}}}" /><strong><span>{{{title}}}</span> {{{author}}}</strong></a></li>'."\n");
			$this->setURLTemplate('http://www.readernaut.com/'.$config['username'].'/books/');
			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->reader_book;
		}

		/**
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
						'created' => Pubwich::time_since( $item->book_edition->created ),
						'modified' => Pubwich::time_since( $item->book_edition->modified ),
						'isbn' => $item->book_edition->isbn,
						);
		}

	}

	class ReadernautNotes extends Readernaut {

		/**
		 * @constructor
		 */
		public function __construct( $config ){
			parent::setVariables( $config );
			$this->setURL( sprintf( 'http://readernaut.com/api/v1/xml/%s/notes/?order_by=-created', $config['username'] ) );
			$this->setItemTemplate('<li><a class="clearfix" href="{{{link}}}"><img src="{{{image}}}" width="{{{size}}}" alt="{{{title}}}" /><strong><span>{{{title}}}</span> {{{author}}}</strong></a>{{{body}}}</li>'."\n");
			$this->setURLTemplate('http://www.readernaut.com/'.$config['username'].'/notes/');
			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->note;
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			// yep, this is actually book_edtion. Readernaut's creator Nathan Borror has been notified about this typo :)
			return array(
						'id' => $item->reader_book_id,
						'link' => $item->book_edtion->permalink,
						'title' => $item->book_edtion->title,
						'subtitle' => $item->book_edtion->subtitle,
						'author' => $item->book_edtion->authors->author,
						'image' => $item->book_edtion->covers->cover_small,
						'image_medium' => $item->book_edtion->covers->cover_medium,
						'image_large' => $item->book_edtion->covers->cover_large,
						'size' => $this->size,
						'created' => Pubwich::time_since( $item->book_edtion->created ),
						'modified' => Pubwich::time_since( $item->book_edtion->modified ),
						'isbn' => $item->book_edtion->isbn,
						'body' => $item->body,
						);
		}

	}
