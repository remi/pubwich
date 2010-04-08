<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Twitter
	 * @description Retrieves statuses from Twitter
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods TwitterUser TwitterSearch
	 */

	class Twitter extends Service {

		public $auth;

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			parent::__construct( $config );
		}

		/**
		 * @param array $config
		 * @return void
		 */
		public function setVariables( $config ) {
			$this->auth = $config['authenticate'] ? $config['username'].':'.$config['password'].'@' : '';
		}

		/**
		 * @return string
		 */
		public function filterContent( $text ) {
			$text = strip_tags( $text );
			$text = preg_replace( '/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $text );
			$text = preg_replace( '/\#([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '<a href="http://twitter.com/search?q=%23\\1">#\\1</a>', $text );
			$text = preg_replace( '/\@([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '@<a href="http://twitter.com/\\1">\\1</a>', $text );
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

	class TwitterUser extends Twitter {

		public function getData() {
			$data = parent::getData();
			return $data->status;
		}

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->setURL( sprintf( 'http://'.$this->auth.'twitter.com/statuses/user_timeline/%s.xml?count=%d', $config['id'], $config['total'] ) );
			$this->username = $config['username'];
			$this->setItemTemplate('<li class="clearfix"><span class="date"><a href="{%link%}">{%date%}</a></span>{%text%}</li>'."\n");
			$this->setURLTemplate('http://www.twitter.com/'.$config['username'].'/');

			parent::__construct( $config );
		}

		public function populateItemTemplate( &$item ) {
			return parent::populateItemTemplate( $item ) + array(
					'link' => sprintf( 'http://www.twitter.com/%s/statuses/%s/', $this->username, $item->id ),
					'user_image' => $item->user->profile_image_url,
					'user_name' => $item->user->name,
					'user_nickname' => $item->user->screen_name,
					'user_link' => sprintf( 'http://www.twitter.com/%s/', $item->user->screen_name ),
			);
		}


	}

	class TwitterSearch extends Twitter {

		public function getData() {
			$data = parent::getData();
			return $data->results;
		}

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->setURL( sprintf( 'http://'.$this->auth.'search.twitter.com/search.json?q=%s&rpp=%d', $config['terms'], $config['total'] ) );
			$this->setItemTemplate( '<li class="clearfix"><span class="image"><a href="{%user_link%}"><img width="48" src="{%user_image%}" alt="{%user_nickname%}" /></a></span>{%text%}<p class="date"><a href="{%link%}">{%date%}</a></p></li>'."\n" );
			$this->setURLTemplate( 'http://search.twitter.com/search?q='.$config['terms'] );

			$this->callback_function = array(Pubwich, 'json_decode');

			parent::__construct( $config );
		}

		public function populateItemTemplate( &$item ) {
			return parent::populateItemTemplate( $item ) + array(
						'link' => sprintf( 'http://www.twitter.com/%s/statuses/%s/', $item->from_user, $item->id ),
						'user_image' => $item->profile_image_url,
						'user_nickname' => $item->from_user,
						'user_link' => sprintf( 'http://www.twitter.com/%s/', $item->from_user ),
			);
		}

	}
