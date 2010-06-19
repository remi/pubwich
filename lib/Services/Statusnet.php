<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname StatusNet
	 * @description Retrieves statuses from StatusNet
	 * @version 1.0 (20100619)
	 * @author Rémi Prévost (exomel.com)
	 * @methods StatusNetUser StatusNetSearch
	 */

	class StatusNet extends Service {

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			parent::__construct( $config );
			$this->callback_function = array(Pubwich, 'json_decode');
		}

		/**
		 * @param array $config
		 * @return void
		 */
		public function setVariables( $config ) {
			$this->auth = $config['username'].':'.$config['password'];
			$this->root = $config['root'] ? $config['root'] : 'http://identi.ca';
		}

		public function getAuthRoot() {
			return preg_replace('/\/\//', '//' . $this->auth.'@', $this->root );
		}

		/**
		 * @return string
		 */
		public function filterContent( $text ) {
			$text = strip_tags( $text );
			$text = preg_replace( '/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $text );
			$text = preg_replace( '/\#([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '<a href="'.$this->root.'/search/notice?q=%23\\1">#\\1</a>', $text );
			$text = preg_replace( '/\@([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '@<a href="'.$this->root.'/\\1">\\1</a>', $text );
			$text = '<p>' . $text . '</p>';
			return $text;
		}

		public function populateItemTemplate( &$item ) {
			return array(
						'text' => $this->filterContent( $item->text ),
						'date' => Pubwich::time_since( $item->created_at ),
						'location' => $item->user->location,
						'source' => $item->source,
						);
		}

	}

	class StatusNetUser extends StatusNet {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->setURL( $this->getAuthRoot().'/api/statuses/user_timeline.json');
			$this->username = $config['username'];
			$this->setItemTemplate('<li class="clearfix"><span class="date"><a href="{{{link}}}">{{{date}}}</a></span>{{{text}}}</li>'."\n");
			$this->setURLTemplate($this->root.$config['username'].'/');

			parent::__construct( $config );
		}

		public function populateItemTemplate( &$item ) {
			return parent::populateItemTemplate( $item ) + array(
					'link' => sprintf( $this->root.'/notice/%s/', $item->id ),
					'user_image' => $item->user->profile_image_url,
					'user_name' => $item->user->name,
					'user_nickname' => $item->user->screen_name,
					'user_link' => sprintf( $this->root.'/%s/', $item->user->screen_name ),
					'in_reply_to_screen_name' => $item->in_reply_to_screen_name,
			);
		}

	}

	class StatusNetSearch extends StatusNet {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->setURL( $this->getAuthRoot().'/api/search.json?q='.$config['terms']);
			$this->setItemTemplate( '<li class="clearfix"><span class="image"><a href="{{{user_link}}}"><img width="48" src="{{{user_image}}}" alt="{{{user_nickname}}}" /></a></span>{{{text}}}<p class="date"><a href="{{{link}}}">{{{date}}}</a></p></li>'."\n" );
			$this->setURLTemplate( $this->root.'/search/notice?q='.$config['terms'] );

			parent::__construct( $config );
		}

		public function getData() {
			$data = parent::getData();
			return $data->results;
		}

		public function populateItemTemplate( &$item ) {
			return parent::populateItemTemplate( $item ) + array(
						'link' => sprintf( $this->root.'/notice/%s/', $item->id ),
						'user_image' => $item->profile_image_url,
						'user_nickname' => $item->from_user,
						'user_link' => sprintf( $this->root.'/%s/', $item->from_user ),
			);
		}

	}
