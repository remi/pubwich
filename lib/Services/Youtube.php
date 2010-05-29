<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Youtube
	 * @description Fetch Youtube videos
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	Pubwich::requireServiceFile( 'Atom' );
	class Youtube extends Atom {

		private $size;

		public function __construct( $config ){
			$this->size = $config['size'];
			parent::__construct( $config );
			$this->setItemTemplate('<li class="clearfix"><a href="{{{link}}}"><img src="{{{image}}}" alt="{{{title}}}" /><strong>{{{title}}}</strong></a></li>'."\n");
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$media = $item->children('http://search.yahoo.com/mrss/');

			if ( $media->group->thumbnail ) {
				$attrs = $media->group->thumbnail[0]->attributes();
				$title = (string) $media->group->title;
				$description = (string) $media->group->description;
				$url_attrs = $media->group->player->attributes();
				$url = $url_attrs['url'];
			}

			return parent::populateItemTemplate( $item ) + array(
				'image' => $attrs ? $attrs['url'] : '',
				'size' => $item->size,
			);
		}

	}

	class YoutubeVideos extends Youtube {

		public function __construct( $config ){
			$config['url'] = sprintf( 'http://gdata.youtube.com/feeds/api/users/%s/uploads?v=2', $config['username'] );
			$config['link'] = 'http://www.youtube.com/user/'.$config['username'].'/';
			parent::__construct( $config );
		}

	}

	class YoutubeFavorites extends Youtube {

		public function __construct( $config ){
			$config['url'] = sprintf( 'http://gdata.youtube.com/feeds/api/users/%s/favorites?v=2', $config['username'] );
			$config['link'] = 'http://www.youtube.com/user/'.$config['username'].'#p/f';
			parent::__construct( $config );
		}

	}
