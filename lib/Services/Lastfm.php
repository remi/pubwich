<?php
	/**
	 * @classname LastFM
	 * @description Fetch data from Last.fm
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods LastFMWeeklyAlbums LastFMRecentTracks
	 */

	class LastFM extends Service {

		public $username, $size, $key, $classes, $compteur;

		public function setVariables( $config ) {
			$this->size = $config['size'];
			$this->compteur = 0;
			$this->username = $config['username'];
			$this->key = $config['key'];
			$this->total = $config['total'];
		}


		public function buildCache() {
			parent::buildCache();
		}

		public function getData() {
			return parent::getData();
		}

		public function init() {
			parent::init();
		}

					
	}

	class LastFMRecentTracks extends LastFM {
		
		public function __construct( $config ) {
			parent::setVariables( $config );	

			$this->setURL( sprintf( 'http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&api_key=%s&user=%s', $this->key, $this->username ) );

			$this->setItemTemplate('<li{%classe%}><a class="clearfix" href="{%link%}">{%artist%} — {%track%}</a></li>'."\n");
			$this->setURLTemplate('http://www.last.fm/user/'.$config['username'].'/');

			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->recenttracks->track;
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$album = Smartypants( $item->album );
			$artist = Smartypants( $item->artist );
			$title= Smartypants( $item->name );
			$this->compteur++;
			return array(
						'link' => htmlspecialchars( $item->url ),
						'artist' => $artist,
						'album' => $album,
						'track' => $title,
						'release_date' => $item->release_date,
						'listeners' => $item->listeners,
						'classe' => isset($this->classes[$this->compteur-1]) ? ' class="'.$this->classes[$this->compteur-1].'"' : '',
						);
		}

	}

	class LastFMWeeklyAlbums extends LastFM {

		public function __construct( $config ){
			parent::setVariables( $config );	

			$this->setURL( sprintf( 'http://ws.audioscrobbler.com/2.0/?method=user.getweeklyalbumchart&api_key=%s&user=%s', $this->key, $this->username ) );
			$this->classes = array( 'premier', 'deuxieme', 'troisieme', 'quatrieme' );

			$this->setItemTemplate('<li{%classe%}><a class="clearfix" href="{%link%}"><img src="{%image%}" width="{%size%}" height="{%size%}" alt="{%title%}"><strong><span>{%artist%}</span> {%album%}</strong></a></li>'."\n");
			$this->setURLTemplate('http://www.last.fm/user/'.$config['username'].'/');

			parent::__construct( $config );
		}

		/**
		 * @param string $url
		 * @return void
		 */
		public function buildCache() {
			parent::buildCache();
			$this->buildAlbumCache( true );
		}

		/**
		 * @param bool $rebuildCache Si vrai, force la reconstruction du cache
		 * @return void
		 */
		public function buildAlbumCache( $rebuildCache ) {
			$data = $this->getData();
			$compteur = 0;
			if ( $data ) {
				foreach ( $this->getData() as $album ) {
					$compteur++;
					if ($compteur > $this->total) { break; }
					$this->fetchAlbum( $album, $rebuildCache );
				}
			}
		}

		/**
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
				PubwichLog::log( 2, 'Reconstruction de la cache d’un album de Last.fm' );
				$url = sprintf( "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=%s&artist=%s&album=%s", $this->key, urlencode( $album->artist ), urlencode( $album->name ) );
				$data = FileFetcher::get( $url );
				$cacheWrite = $Cache_Lite->save( $data );
				if ( PEAR::isError($cacheWrite) ) {
					//var_dump( $cacheWrite );
				}
				$this->albumdata[$id] = simplexml_load_string( $data );
			}

		}

		/**
		 * @param SimpleXMLElement $album
		 * @return string
		 */
		public function buildAlbumId($album) {
			return md5( $album->artist . "|" . $album->name );
		}

		/**
		 * @param SimpleXMLElement $album
		 * @return string
		 */	
		public function getImage( $album ) {
			$id = $this->buildAlbumId( $album );
			$image = $this->albumdata[$id]->album->image[1];
			return ( $image == '' ) ? Pubwich::getThemeUrl().'/img/cover.png' : $image;
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$album = Smartypants( $item->name );
			$artist = Smartypants( $item->artist );
			$this->compteur++;
			return array(
						'link' => htmlspecialchars( $item->url ),
						'title' => ( $artist . ' — ' . $album ),
						'artist' => $artist,
						'release_date' => $item->release_date,
						'listeners' => $item->listeners,
						'playcount' => $item->playcount,
						'album' => $album,
						'image' => $this->getImage( $item ),
						'size' => $this->size,
						'classe' => isset($this->classes[$this->compteur-1]) ? ' class="'.$this->classes[$this->compteur-1].'"' : '',
						);
		}

		/**
		 * @var $albumdata Contient les informations relatives aux albums
		 */
		public $albumdata;

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->weeklyalbumchart->album;
		}

		/**
		 * @param SimpleXMLElement $album
		 * @return string
		 */
		public function getYear( $album ) {
			$id = $this->buildAlbumId( $album );
			$date = $this->albumdata[$id]->album->releasedate;
			return $date;
		}

		/**
		 * @param string $url
		 * @return LastFMWeeklyAlbums
		 */
		public function init() {
			parent::init();
			$this->buildAlbumCache( false );
			return $this;
		}


	}
