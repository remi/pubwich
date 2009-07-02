<?php

	class Youtube extends Service {
	
		public $username;

		public function __construct( $config ){
			$this->setURL( sprintf( 'http://gdata.youtube.com/feeds/api/users/%s/uploads?v=2', $config['username'] ) );
			$this->total = $config['total'];
			$this->username = $config['username'];

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li class="clearfix"><a href="{%link%}"><img src="{%image%}" alt="{%title%}" /><span>{%title%}</span></a></li>'."\n");
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

		public function populateItemTemplate( &$item ) {
			$media = $item->children('http://search.yahoo.com/mrss/');
			$attrs = $media->group->thumbnail[0]->attributes();
			$title = (string) $media->group->title;
			$url_attrs = $media->group->player->attributes();
			$url = $url_attrs['url'];
	
			return array(
						'link' => htmlspecialchars( $url ),
						'title' => htmlspecialchars( SmartyPants( $title ) ),
						'date' => Pubwich::time_since( $item->published ),
						'image' => $attrs['url'],
			);
		}


	}

