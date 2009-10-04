<?php

	/**
	 * @classname Service
	 */ 
	class Service {

		public $data, $cache_id, $cache_options, $title, $description, $urlTemplate, $username, $total, $method, $callback_function;
		private $url, $itemTemplate, $tmpTemplate, $boxTemplate, $tmpBoxTemplate;

		/**
		 * @constructor
		 */ 
		public function __construct( $config=null ) {
			PubwichLog::log( 2, sprintf( Pubwich::_("Creating an instance of %s"), get_class( $this ) ) );

			$this->title = $config['title'];
			$this->description = $config['description'];

			$id = md5( $this->getURL() ); 
			$this->cache_id = $id; 

			if ( !$this->callback_function ) {
				$this->callback_function = 'simplexml_load_string';
			}

			$this->cache_options = array( 
				'cacheDir' => CACHE_LOCATION, 
				'lifeTime' => CACHE_LIMIT,
				'readControl' => true,
				'readControlType' => 'strlen',
				'errorHandlingAPIBreak' => true,
				'automaticSerialization' => false
			);

			$this->itemTemplate = new PubwichTemplate();
			if ( $this->tmpTemplate ) {
				$this->setItemTemplate( $this->tmpTemplate );
				$this->tmpTemplate = null;
			}

			$this->boxTemplate = new PubwichTemplate();
			if ( $this->tmpBoxTemplate ) {
				$this->setBoxTemplate( $this->tmpBoxTemplate );
				$this->tmpBoxTemplate = null;
			}
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
		public function getURL() {
			return $this->url;
		}

		/**
		 * Définit l'URL du service
		 *
		 * @param string $url
		 * @return void
		 */
		public function setURL( $url ) {
			PubwichLog::log( 3, sprintf( Pubwich::_("Setting the URL for %s: %s"), get_class( $this ), $url ) );
			$this->url = $url;
		}

		/**
		 * Initialise le service
		 *
		 * @param string $url L'URL à récupérer
		 * @return Service
		 */ 	
		public function init() {
			PubwichLog::log( 2, sprintf( Pubwich::_("Initializing instance of %s"), get_class( $this ) ) );
			$url = $this->getURL();
			$Cache_Lite = new Cache_Lite( $this->cache_options );

			if ($data = $Cache_Lite->get( $this->cache_id) ) {
				libxml_use_internal_errors( true );
				$this->data = call_user_func( $this->callback_function, $data );
				libxml_clear_errors();
			}
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
				$cacheWrite = $Cache_Lite->save( $content );
				if ( PEAR::isError($cacheWrite) ) {
					/*var_dump( $cacheWrite->getMessage() );*/
				}
				libxml_use_internal_errors( true );
				$this->data = call_user_func( $this->callback_function, $content );
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

		/**
		 * Retourne le nom de la variable de l'instance
		 *
		 * return string
		 */
		public function getVariable() {
			return $this->variable;
		}

		/**
		 * Définit la variable de l'instance
		 *
		 * @param string $variable Le nom de la variable
		 * @return void
		 */
		public function setVariable( $variable ) {
			//$this->cache_id = $variable;
			$this->variable = $variable;
		}

		/**
		 * Définit le template de l'URL de profil du service
		 *
		 * @param string $template Le template
		 * @return void
		 */
		public function setURLTemplate( $template ) {
			$this->urlTemplate = $template;
		}

		/**
		 * Définit le template à utiliser lors de l'affichage d'un item de ce service
		 *
		 * @param string $template Le template
		 * @return void
		 */
		public function setItemTemplate( $template ) {
			if ( !$this->itemTemplate ) {
				$this->tmpTemplate = $template;
			} else {
				$this->itemTemplate->setTemplate( $template );
			}
		}

		/**
		 * Retourne le template des items
		 *
		 * @return PubwichTemplate
		 */
		public function getItemTemplate() {
			return $this->itemTemplate;
		}

		/**
		 * Définit le template à utiliser lors de l'affichage de ce service
		 *
		 * @param string $template Le template
		 */
		public function setBoxTemplate( $template ) {
			if ( !$this->boxTemplate ) {
				$this->tmpBoxTemplate = $template;
			} else {	
				$this->boxTemplate->setTemplate( $template );
			}
		}

		/**
		 * Retourne le template du service
		 *
		 * @return PubwichTemplate
		 */
		public function getBoxTemplate() {
			return $this->boxTemplate;
		}

		/*
		 * Affiche la boite d'une classe spécifique
		 *
		 * @param Service &$classe La référence de l’instancedu service à afficher
		 * @return string
		 */
		public function renderBox( ) {

			$items = '';
			$classData = $this->getData();

			$htmlClass = strtolower( get_class( $this ) ).' '.( get_parent_class( $this ) != 'Service' ? strtolower( get_parent_class( $this ) ) : '' );
			if ( !$classData ) {
				$items = '<li class="nodata">'.sprintf( Pubwich::_('An error occured with the %s API. The data is therefore unavailable.'), get_class( $this ) ).'</li>';
				$htmlClass .= ' nodata';
			} else {
				foreach( $classData as $item ) {
					$compteur++;
					if ($this->total && $compteur > $this->total) { break; }  
					$this->getItemTemplate()->populate( $this->populateItemTemplate( $item ) );
					$items .= '		'.$this->getItemTemplate()->output();
				}
			}

			$data = array(
				'class' => $htmlClass,
				'id' => $this->getVariable(),
				'url' => $this->urlTemplate,
				'title' => $this->title,
				'description' => $this->description,
				'items' => $items	
			);

			$this->getBoxTemplate()->populate( $data );
			return $this->getBoxTemplate()->output();
		}

	}
