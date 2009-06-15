<?php

	/**
	 * Classe de service générale
	 *
	 * @className Service
	 */ 
	class Service {
		
		public $data, $cache_id, $cache_options;
		private $url;

		/**
		 * @constructor
		 */ 
		public function __construct() {
			PubwichLog::log( 2, "Création de la classe " . get_class( $this ) );
			
			//$id = strtolower( get_class($this) );
			$id = urlencode( $this->getURL() ); 
			$this->cache_id = $id; 
			$this->cache_options = array( 
				'cacheDir' => CACHE_LOCATION, 
				'lifeTime' => CACHE_LIMIT,
				'errorHandlingAPIBreak' => CACHE_LITE_ERROR_RETURN
			);
		}

		/**
		 * Retourne les options de cache pour le service
		 *
		 * @return array
		 */
		public function getCacheOptions() {
			return $this->cache_options;
		}

		/**
		 * Retourne l'URL à récupérer pour ce service
		 *
		 * @return string
		 */
		private function getURL() {
			return $this->url;
		}

		public function setURL( $url ) {
			$this->url = $url;
		}

		/**
		 * Initialise le service
		 *
		 * @param string $url L'URL à récupérer
		 * @return Service
		 */ 	
		public function init() {
			PubwichLog::log( 2, "Initialisation de la classe " . get_class( $this ) );
			$url = $this->getURL();
			$Cache_Lite = new Cache_Lite( $this->cache_options );
			
			// Si les données existent dans la cache
			if ($data = $Cache_Lite->get( $this->cache_id) ) {
				$this->data = simplexml_load_string( $data );
			}
			// Sinon
			else {
				$this->buildCache( $Cache_Lite );
			}
			return $this;
		}

		/**
		 * Récupère les données du service et les met en cache
		 *
		 * @param string $url L'URL à récupérer
		 * [@param Cache_Lite $Cache_Lite Un objet Cache_Lite si existant]
		 * @return void
		 */
		public function buildCache( $Cache_Lite = null ) {
			PubwichLog::log( 2, "Reconstruction de la cache du service " . get_class( $this ) );
			$url = $this->getURL();
			if ( !$Cache_Lite ) {
				$Cache_Lite = new Cache_Lite( $this->cache_options );
				$Cache_Lite->get( $this->cache_id );
			}
			$content = FileFetcher::get( $url );
			if ( $content !== false ) {
				$Cache_Lite->save( $content );
				$this->data = simplexml_load_string( $content );
			} else {
				$this->data = false;
			}
		}

		/**
		 * Retourne les données du service
		 *
		 * @return string
		 */	
		public function getData() {
			return $this->data;
		}

		public function getVariable() {
			return $this->variable;
		}
	
	}
