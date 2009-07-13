<?php

	class Youtube extends Service {
	
		private $size;

		public function __construct( $config ){
			$this->setURL( sprintf( 'http://gdata.youtube.com/feeds/api/users/%s/uploads?v=2', $config['username'] ) );
			$this->total = $config['total'];
			$this->username = $config['username'];
			$this->size = $config['size'];

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li class="clearfix"><a href="{%link%}"><img src="{%image%}" alt="{%title%}" /><strong>{%title%}</strong></a></li>'."\n");
			$this->setURLTemplate('http://www.youtube.com/user/'.$config['username'].'/');

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
			$media = $item->children('http://search.yahoo.com/mrss/');
			$attrs = $media->group->thumbnail[0]->attributes();
			$title = (string) $media->group->title;
			$description = (string) $media->group->description;
			$url_attrs = $media->group->player->attributes();
			$url = $url_attrs['url'];
	
			return array(
						'link' => htmlspecialchars( $url ),
						'title' => SmartyPants( $title ),
						'description' => SmartyPants( $description ),
						'date' => Pubwich::time_since( $item->published ),
						'image' => $attrs['url'],
						'size' => $this->size,
			);
		}

	}
