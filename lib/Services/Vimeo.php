<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Vimeo
	 * @description Fetch Vimeo videos
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	class Vimeo extends Service {

		public function __construct( $config ){
			$this->setURL( sprintf( 'http://vimeo.com/api/%s/clips.xml', $config['username'] ) );
			$this->total = $config['total'];
			$this->username = $config['username'];

			$this->setItemTemplate('<li><a class="clearfix" href="{{{link}}}"><img src="{{{image_small}}}" alt="{{{title}}}" /><span>{{{title}}}</span></a></li>'."\n");
			$this->setURLTemplate('http://www.vimeo.com/'.$config['username'].'/');

			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->clip;
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			return array(
						'link' => htmlspecialchars( $item->url ),
						'title' => $item->title,
						'date' => Pubwich::time_since( $item->uploaded_date ),
						'caption' => $item->caption,
						'duration' => $item->duration,
						'width' => $item->width,
						'height' => $item->height,
						'image_small' => $item->thumbnail_small,
						'image_medium' => $item->thumbnail_medium,
						'image_large' => $item->thumbnail_large,
			);
		}

	}

