<?php

	// @@@ Renommer ce fichier en `config.php`

	// À changer en production
	error_reporting( E_ALL ^ E_NOTICE );
	// error_reporting( 0 )

	define( 'PUBWICH_LOGLEVEL', 0 );

	define('PUBWICH_URL', 'http://localhost/pubwich/');
	define('PUBWICH_THEME', 'default');

	// Localisation
	date_default_timezone_set( 'America/Montreal' );
	setlocale( LC_ALL, 'fr_CA.UTF8', 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8' ); 

	// Configuration de Pubwich et des services utilisés

	define( 'FLICKR_KEY',            '__________________' );
	define( 'FLICKR_USERID',         '__________________' );
	define( 'FLICKR_TOTAL',          12 );

	define( 'LASTFM_KEY',            '__________________' );
	define( 'LASTFM_USERNAME',       '__________________' );
	define( 'LASTFM_TOTAL',          10 );

	define( 'DELICIOUS_USERNAME',    '__________________' );
	define( 'DELICIOUS_TOTAL',       10 );

	define( 'TWITTER_USERID',        '__________________' ); // cliquer sur le lien "RSS feed of _____'s updates" pour trouver votre identifiant numérique
	define( 'TWITTER_TOTAL',         10 ); 

	define( 'READERNAUT_USERNAME',   '__________________' );
	define( 'READERNAUT_TOTAL',      10 );

	define( 'YOUTUBE_USERNAME',      '__________________' );
	define( 'YOUTUBE_TOTAL',         10 );

	Pubwich::setServices(
		array(
			array('Flickr', 'photos', array( FLICKR_KEY, FLICKR_USERID, FLICKR_TOTAL ) ),
			array('Lastfm', 'albums', array ( LASTFM_KEY, LASTFM_USERNAME, LASTFM_TOTAL ) ),
			array('Delicious', 'liens', array( DELICIOUS_USERNAME, DELICIOUS_TOTAL ) ),
			array('Twitter', 'etats', array( TWITTER_USERID, TWITTER_TOTAL ) ),
			array('Readernaut', 'livres', array( READERNAUT_USERNAME, READERNAUT_TOTAL ) ),
			array('Atom', 'billets', array( 'http://remiprevost.com/atom/', 10 ) ),
			array('Youtube', 'videos', array( YOUTUBE_USERNAME, YOUTUBE_TOTAL ) ),
		)
	);

	// Système de cache
	define( 'CACHE_LOCATION', dirname(__FILE__) . '/../cache/' );
	define( 'CACHE_LIMIT', 20 * 60 ); // On définit une limite de temps pour la cache, au cas où la cronjob ne fonctionnerait pas

