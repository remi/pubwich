<?php

	class LastFM extends Service {

		private $url_template = 'http://ws.audioscrobbler.com/2.0/?method=user.getweeklyalbumchart&api_key=%s&user=%s';
		public $username;

		public function __construct( $config ){
			list($key, $username, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $key, $username ) );
			$this->total = $total;
			$this->username = $username;
			parent::__construct();
		}

		/**
		 * @var $albumdata Contient les informations relatives aux albums
		 */
		public $albumdata;

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->weeklyalbumchart->album;
		}

		/**
		 * Retourne l'image d'un album. Si image non-disponible, retourne
		 * une image alternative.
		 *
		 * @param SimpleXMLElement $album
		 * @return string
		 */	
		public function getImage( $album ) {
			$id = $this->buildAlbumId( $album );
			$image = $this->albumdata[$id]->album->image[1];
			return ( $image == '' ) ? Pubwich::getThemeUrl().'/img/cover.png' : $image;
		}

		/**
		 * Récupère la date à laquelle un album a été lancé
		 *
		 * @param SimpleXMLElement $album
		 * @return string
		 */
		public function getYear( $album ) {
			$id = $this->buildAlbumId( $album );
			$date = $this->albumdata[$id]->album->releasedate;
			return $date;
		}

		/**
		 * Surcharge la méthode parent::init() en construisant
		 * l'objet qui contient les données relatives aux albums
		 * lors de l'initialisation de l'objet
		 *
		 * @param string $url
		 * @return LastFM
		 */
		public function init() {
			parent::init();
			$this->buildAlbumCache( false );
			return $this;
		}

		/**
		 * Construit l'identificateur d'album (pour la cache)
		 *
		 * @param SimpleXMLElement $album
		 * @return string
		 */
		public function buildAlbumId($album) {
			return urlencode( $album->artist . "|" . $album->name );
		}

		/**
		 * Récupère les informations détaillées d'un album
		 * 
		 * @param SimpleXMLElement $album
		 * [@param bool $rebuildCache Si vrai, force la reconstruction du cache]
		 * @return void
		 */
		public function fetchAlbum($album, $rebuildCache=false) {
			$Cache_Lite = new Cache_Lite( parent::getCacheOptions() );
			$id = $this->buildAlbumId( $album );
			if ( !$rebuildCache && $data = $Cache_Lite->get( $id ) ) {
				$this->albumdata[$id] = simplexml_load_string( $data );
			} else {
				$Cache_Lite->get( $id );
				$url = sprintf( "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=%s&artist=%s&album=%s", LASTFM_KEY, urlencode( $album->artist ), urlencode( $album->name ) );
				$data = FileFetcher::get( $url );
				$Cache_Lite->save( $data );
				$this->albumdata[$id] = simplexml_load_string( $data );
			}

		}

		/**
		 * Construit la cache des pochettes d'album
		 *
		 * @param bool $rebuildCache Si vrai, force la reconstruction du cache
		 * @return void
		 */
		public function buildAlbumCache( $rebuildCache ) {
			$data = $this->getData();
			if ( $data ) {
				foreach ( $this->getData() as $album ) {
					$this->fetchAlbum( $album, $rebuildCache );
				}
			}
		}

		/**
		 * Construit la cache des albums lors de l'appel de cache global
		 *
		 * @param string $url
		 * @return void
		 */
		public function buildCache() {
			parent::buildCache();
			$this->buildAlbumCache( true );
		}
					
	}
