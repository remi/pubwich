<?php

	class LastFM extends Service {

		public $username, $size;
		private $compteur, $key, $classes;

		public function __construct( $config ){
			$this->setURL( sprintf( 'http://ws.audioscrobbler.com/2.0/?method=user.getweeklyalbumchart&api_key=%s&user=%s', $config['key'], $config['username'] ) );
			$this->total = $config['total'];
			$this->username = $config['username'];
			$this->size = $config['size'];
			$this->key = $config['key'];
			$this->compteur = 0;
			$this->classes = array( 'premier', 'deuxieme', 'troisieme', 'quatrieme' );

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li{%classe%}><a class="clearfix" href="{%link%}"><img src="{%image%}" width="{%size%}" height="{%size%}" alt="{%title%}"><strong><span>{%artist%}</span> {%album%}</strong></a></li>'."\n");
			$this->setURLTemplate('http://www.last.fm/user/'.$config['username'].'/');

			parent::__construct();
		}

		/**
		 * Retourne un item formatté selon le gabarit
		 *
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$album = Smartypants( $item->name );
			$artist = Smartypants( $item->artist );
			$this->compteur++;
			return array(
						'link' => htmlspecialchars( $item->url ),
						'title' => htmlspecialchars( $artist . ' — ' . $album ),
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
			return md5( $album->artist . "|" . $album->name );
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
		 * Construit la cache des pochettes d'album
		 *
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
