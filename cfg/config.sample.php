<?php
	defined('PUBWICH') or die('No direct access allowed.');

	// Rename this file to config.php

	error_reporting(E_ALL ^ E_NOTICE); // uncomment this line in development environment
	// error_reporting(0); // uncomment this line in production environment (prevent errors from showing up)

	// Localisation
	date_default_timezone_set( 'America/Montreal' );
	define('PUBWICH_LANG', ''); // leave to '' to keep Pubwich in english
	setlocale( LC_ALL, 'en_CA.UTF8' ); // for date methods

	// General site informations
	define('PUBWICH_URL', 'http://localhost/pubwich/');
	define('PUBWICH_THEME', 'default');
	define('PUBWICH_TITLE', 'My Pubwich-powered site');

	// Logging configuration (you should not have to edit this)
	define('PUBWICH_LOGLEVEL', 0);
	define('PUBWICH_LOGTOFILE', false);

	// Pubwich services configuration
	/*
	 * setServices syntax to use
	 *
	 * Pubwich::setServices(
	 *		array(
	 *
	 *			// column 1
	 *			array(
	 *				array('Flickr', 'photos', array(
	 *					'method' => 'FlickrUser',
	 *					'title' => 'Flickr',
	 *					'description' => 'My pictures',
	 *					'key' => '',
	 *					...
	 *				)
	 *			),
	 *
	 *			// column 2 
	 *			array(
	 *				...
	 *			),
	 *
	 *			// column 3
	 *			array(
	 *				...
	 *			)
	 *		)
	 * );
	 *
	 */	
	Pubwich::setServices(
		array(
			array(

				array( 'Text', 'intro', array(
						'title' => 'Introduction',
						'text' => 'This is a short introduction text. To hide the "Introduction" title, all you have to is not specify a "title" item for the <strong>Text</strong> box.',
					)
				),

				array( 'Flickr', 'photos', array( 
						'method' => 'FlickrUser',
						'key' => 'FLICKR_KEY_HERE',
						'userid' => 'FLICKER_USERID_HERE', // use http://www.idgettr.com to find it
						'username' => 'FLICKR_USERNAME_HERE',
						'total' => 12,
						'title' => 'Flick<em>r</em>',
						'description' => 'latest photos',
						'row' => 4,
					)
				),

				array( 'Vimeo', 'videos', array(
						'username' => 'VIMEO_USERNAME_HERE',
						'total' => 4,
						'title' => 'Vimeo',
						'description' => 'latest videos'
					)
				),

				array( 'Youtube', 'youtube', array(
						'method' => 'YoutubeVideos',
						'username' => 'YOUTUBE_USERNAME_HERE',
						'total' => 4,
						'size' => 120,
						'title' => 'Youtube',
						'description' => 'latest videos'
					)
				),
				
			),
			array(
				array( 'Twitter', 'etats', array(
						'method' => 'TwitterUser',
						'username' => 'TWITTER_USERNAME_HERE',
						'oauth' => array(
							// You have to create a new application at http://dev.twitter.com/apps to get these keys
							// See the tutorial at http://pubwich.org/wiki/Using_Twitter_with_Pubwich
							'app_consumer_key' => '',
							'app_consumer_secret' => '',
							'user_access_token' => '',
							'user_access_token_secret' => ''
						),
						'total' => 10,
						'title' => 'Twitter',
						'description' => 'latest statuses'
					)
				),

				array( 'Delicious', 'liens', array(
						'username' => 'DELICIOUS_USERNAME_HERE',
						'total' => 5,
						'title' => 'del.icio.us',
						'description' => 'latest bookmarks',
					)
				),

				array( 'Facebook', 'status', array(
						'id' => 'FACEBOOK_USERID_HERE',
						'key' => 'FACEBOOK_KEY_HERE',
						'username' => 'FACEBOOK_USERNAME_HERE',
						'total' => 5,
						'title' => 'Facebook',
						'description' => 'latest statuses',
					)
				),

				array( 'RSS', 'ixmedia', array(
						'url' => 'http://feeds2.feedburner.com/ixmediablogue',
						'link' => 'http://blogue.ixmedia.com/',
						'total' => 5,
						'title' => 'Blogue iXmédia',
						'description' => 'latest atom blog entries'
					)
				),
			),
			array(

				array( 'Atom', 'effair', array(
						'url' => 'http://remiprevost.com/atom/',
						'link' => 'http://remiprevost.com/',
						'total' => 5,
						'title' => 'Effair',
						'description' => 'latest rss blog entries'
					)
				),

				array( 'Readernaut', 'livres', array(
						'method' => 'ReadernautBooks',
						'username' => 'READERNAUT_USERNAME_HERE',
						'total' => 9,
						'size' => 50,
						'title' => 'Readernaut',
						'description' => 'latest books'
					)
                ),

				array( 'Lastfm', 'albums', array(
						'method' => 'LastFMWeeklyAlbums',
						'key' => 'LASTFM_KEY_HERE',
						'username' => 'LASTFM_USERNAME_HERE',
						'total' => 5,
						'size' => 64,
						'title' => 'Last.fm',
						'description' => 'weekly top albums',
					) 
				),
			),

		)
	);

	// Caching system
	define( 'CACHE_LOCATION', dirname(__FILE__) . '/../cache/' );
	define( 'CACHE_LIMIT', 20 * 60 );
