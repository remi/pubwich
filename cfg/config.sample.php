<?php

	// Renommer ce fichier en config.php

	// À commenter en production
	error_reporting(E_ALL ^ E_NOTICE);
	// error_reporting(0);

	define('PUBWICH_LOGLEVEL', 0);
	define('PUBWICH_LOGTOFILE', false);

	define('PUBWICH_URL', 'http://localhost/pubwich/');
	define('PUBWICH_THEME', 'default');
	define('PUBWICH_TITLE', 'Mon site propulsé par Pubwich');

	// Localisation
	date_default_timezone_set( 'America/Montreal' );
	setlocale( LC_ALL, 'fr_CA.UTF8', 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8' );

	// Configuration de Pubwich et des services utilisés
	/*
	 * Syntaxe à utiliser pour setServices
	 *
	 * Pubwich::setServices(
	 *		array(
	 *
	 *			// colonne 1
	 *			array(
	 *				array('Flickr', 'photos', array(
	 *					'title' => 'Flickr',
	 *					'description' => 'Mes photos',
	 *					'key' => '',
	 *					...
	 *				)
	 *			),
	 *
	 *			// colonne 2 
	 *			array(
	 *				...
	 *			),
	 *
	 *			// colonne 3
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

				array( 'Texte', 'intro', array(
						'title' => 'Introduction',
						'text' => 'Ceci est un petit texte d’introduction. Pour ne pas voir le titre "Introduction", il suffit de ne pas configurer l’item `title` de l’élément <strong>Texte</strong>.',
					)
				),

				array( 'Flickr', 'photos', array( 
						'key' => 'FLICKR_KEY_HERE',
						'userid' => 'FLICKER_USERID_HERE', // http://www.idgettr.com/
						'username' => 'FLICKR_USERNAME_HERE',
						'total' => 12,
						'title' => 'Flick<em>r</em>',
						'description' => 'dernières photos ajoutées',
						'row' => 4,
					)
				),

				array( 'Vimeo', 'videos', array(
						'username' => 'VIMEO_USERNAME_HERE',
						'total' => 4,
						'title' => 'Vimeo',
						'description' => 'derniers vidéos ajoutés'
					)
				),

				array( 'Youtube', 'youtube', array(
						'username' => 'YOUTUBE_USERNAME_HERE',
						'total' => 4,
						'size' => 120,
						'title' => 'Youtube',
						'description' => 'derniers vidéos ajoutés'
					)
				),
				
			),
			array(
				array( 'Twitter', 'etats', array(
						'id' => 'TWITTER_USERID_HERE',
						'username' => 'TWITTER_USERNAME_HERE',
						'password' => 'TWITTER_PASSWORD_HERE',
						'total' => 10,
						'title' => 'Twitter',
						'description' => 'derniers états'
					)
				),

				array( 'Delicious', 'liens', array(
						'username' => 'DELICIOUS_USERNAME_HERE',
						'total' => 5,
						'title' => 'del.icio.us',
						'description' => 'derniers liens publiés',
					)
				),

				array( 'Facebook', 'status', array(
						'id' => 'FACEBOOK_USERID_HERE',
						'key' => 'FACEBOOK_KEY_HERE',
						'username' => 'FACEBOOK_USERNAME_HERE',
						'total' => 5,
						'title' => 'Facebook',
						'description' => 'derniers états postés',
					)
				),

				array( 'RSS', 'ixmedia', array(
						'url' => 'http://feeds2.feedburner.com/ixmediablogue',
						'link' => 'http://blogue.ixmedia.com/',
						'total' => 5,
						'title' => 'Blogue iXmédia',
						'description' => 'derniers billets'
					)
				),
			),
			array(

				array( 'Atom', 'effair', array(
						'url' => 'http://remiprevost.com/atom/',
						'link' => 'http://remiprevost.com/',
						'total' => 5,
						'title' => 'Effair',
						'description' => 'derniers billets'
					)
				),

				array( 'Readernaut', 'livres', array(
						'username' => 'READERNAUT_USERNAME_HERE',
						'total' => 9,
						'size' => 50,
						'title' => 'Readernaut',
						'description' => 'derniers livres'
					)
				),

				array( 'Lastfm', 'albums', array(
						'key' => 'LASTFM_KEY_HERE',
						'username' => 'LASTFM_USERNAME_HERE',
						'total' => 5,
						'size' => 64,
						'title' => 'Last.fm',
						'description' => 'top albums de la dernière semaine',
					) 
				),
			),

		)
	);

	// Système de cache
	define( 'CACHE_LOCATION', dirname(__FILE__) . '/../cache/' );
	define( 'CACHE_LIMIT', 20 * 60 );
