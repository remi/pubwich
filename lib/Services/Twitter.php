<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Twitter
	 * @description Retrieves statuses from Twitter
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods TwitterUser TwitterSearch
	 */

	require_once( dirname(__FILE__) . '/../OAuth/OAuth.php' );
	class Twitter extends Service {

		private $oauth;

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
			$this->oauth = $config['oauth'];
		}

		/**
		 * @return string
		 */
		public function filterContent( $text ) {
			$text = strip_tags( $text );
			$text = preg_replace( '/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $text );
			$text = preg_replace( '/\#([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '<a href="http://search.twitter.com/search?q=%23\\1">#\\1</a>', $text );
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

		public function oauthRequest( $params=array() ) {
			$method = $params[0];
			$additional_params = isset( $params[1] ) ? $params[1] : array();

			$sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
			$consumer = new OAuthConsumer( $this->oauth['app_consumer_key'], $this->oauth['app_consumer_secret'] );
			$token = new OAuthConsumer( $this->oauth['user_access_token'], $this->oauth['user_access_token_secret'] );
			
			$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', 'http://api.twitter.com/1/'.$method.'.json', $additional_params);
			$request->sign_request($sha1_method, $consumer, $token);

			return FileFetcher::get($request->to_url());
		}

	}

	class TwitterUser extends Twitter {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->callback_getdata = array( array($this, 'oauthRequest'), array( 'statuses/user_timeline', array('count'=>$config['total']) ) );
			$this->setURL('http://twitter.com/'.$config['username'].'/'.$config['total']);
			$this->username = $config['username'];
			$this->setItemTemplate('<li class="clearfix"><span class="date"><a href="{{{link}}}">{{{date}}}</a></span>{{{text}}}</li>'."\n");
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
					'in_reply_to_screen_name' => $item->in_reply_to_screen_name,
			);
		}

	}

	class TwitterSearch extends Twitter {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->callback_getdata = array( array($this, 'oauthRequest'), array( 'search', array('q'=>$config['terms'], 'rpp'=>$config['total'] ) ) );
			$this->setURL('http://search.twitter.com/'.$config['terms'].'/'.$config['total']);
			$this->setItemTemplate( '<li class="clearfix"><span class="image"><a href="{{{user_link}}}"><img width="48" src="{{{user_image}}}" alt="{{{user_nickname}}}" /></a></span>{{{text}}}<p class="date"><a href="{{{link}}}">{{{date}}}</a></p></li>'."\n" );
			$this->setURLTemplate( 'http://search.twitter.com/search?q='.$config['terms'] );

			parent::__construct( $config );
		}

		public function getData() {
			$data = parent::getData();
			return $data->results;
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
