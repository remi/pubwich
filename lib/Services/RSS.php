<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname RSS
	 * @description Fetch RSS feeds
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	class RSS extends Service {

		private $dateFormat;

		public function __construct( $config ){
			$this->setURL( $config['url'] );
			$this->total = $config['total'];
			$this->dateFormat = $config['date_format'];
			$this->setItemTemplate('<li><a href="{{{link}}}">{{{title}}}</a> {{{date}}}</li>'."\n");
			$this->setURLTemplate( $config['link'] );
			$this->setHeaderLink( array( 'url' => $config['url'], 'type' => 'application/rss+xml' ) );
			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->channel->item;
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$comments_count = $item->children('http://purl.org/rss/1.0/modules/slash/')->comments;
			$content = $item->children('http://purl.org/rss/1.0/modules/content/')->encoded;
			return array(
						'link' => htmlspecialchars( $item->link ),
						'title' => trim( $item->title ),
						'date' => Pubwich::time_since( $item->pubDate ),
						'absolute_date' => date($this->dateFormat, strtotime($item->pubDate)),
						'comments_link' => $item->comments,
						'comments_count' => $comments_count,
						'description' => $item->description,
						'content' => $content,
						'author' => $item->author,
			);
		}

	}
