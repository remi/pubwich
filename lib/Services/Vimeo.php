<?php

	class Vimeo extends Service {
	
		private $url_template = '';
		public $username;

		public function __construct( $config ){
			$this->setURL( sprintf( 'http://vimeo.com/api/%s/clips.xml', $config['username'] ) );
			$this->total = $config['total'];
			$this->username = $config['username'];

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li class="clearfix"><a href="{%link%}"><img src="{%image_small%}" alt="{%title%}" /><span>{%title%}</span></a></li>'."\n");
			$this->setURLTemplate('http://www.vimeo.com/'.$config['username'].'/');

			parent::__construct();
		}

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->clip;
		}

		public function populateItemTemplate( &$item ) {
			return array(
						'link' => htmlspecialchars( $item->url ),
						'title' => htmlspecialchars( SmartyPants( $item->title ) ),
						'date' => Pubwich::time_since( $item->uploaded_date ),
						'image_small' => $item->thumbnail_small,
						'image_medium' => $item->thumbnail_medium,
						'image_large' => $item->thumbnail_large,
			);
		}

			
	}

