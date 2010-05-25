<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	* @classname Goodreads
	* @description Fetch Googreads books (from user's shelves: all)
	* @version 0.1 (20100411)
	* @author Szabolcs Pap
	*/
	class Goodreads extends Service {
		public $size;
		public $userid;

		public function __construct( $config ) {
			$this->setVariables( $config );
			$this->setUrl( sprintf( 'http://www.goodreads.com/review/list_rss/%d', $config['userid'] ));
			$this->setItemTemplate('<li><a class="clearfix" href="{{{link}}}"><img src="{{{image}}}" width="{{{size}}}" alt="{{{title}}}" /><strong><span>{{{title}}}</span> {{{author}}}</strong></a></li>'."\n");
			$this->setUrlTemplate( sprintf( 'http://www.goodreads.com/user/show/%d', $config['userid'] ));
			parent::__construct( $config );
		}

		public function setVariables( $config ) {
			$this->total  = $config['total'];
			$this->userid = $config['userid'];
			$this->size   = $config['size'];
		}

		public function getData() {
			$data = parent::getData();

			return $data->channel->item;
		}

		public function populateItemTemplate( &$item ) {
			return array(
					'id'             => (int)$item->book_id,
					'link'           => 'http://goodreads.com/book/show/'.(int)$item->book_id,
					'title'          => trim($item->title),
					'author'         => trim($item->author_name),
					'isbn'           => trim($item->isbn),
					'pages'          => trim($item->book->num_pages),
					'average_rating' => trim($item->average_rating),
					'published'      => trim($item->book_published),
					'image'          => trim($item->book_small_image_url),
					'image_small'    => trim($item->book_small_image_url),
					'image_medium'   => trim($item->book_medium_image_url),
					'image_large'    => trim($item->book_large_image_url),
					'size'           => trim($this->size),
					'user_rating'    => trim($item->user_rating),
					'user_read_at'   => trim($item->user_read_at),
					'added'          => trim($item->user_date_added),
					'created'        => trim($item->user_date_created),
					'shelves'        => trim($item->user_shelves),
				);
		}

	}


	/**
	 * for later
	 */
	class GoodreadsBooks extends Goodreads
	{}

