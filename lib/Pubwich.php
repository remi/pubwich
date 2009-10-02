<?php

	define( 'PUBWICH_VERSION', 'trunk' );

	/**
	 * @classname Pubwich
	 */
	class Pubwich {

		/**
		 * @var $services
		 */
		static private $services;

		/**
		 * @var $classes
		 */
		static private $classes;

		/**
		 * @var $columns
		 */
		static private $columns;

		/**
		 * @var $theme_url
		 */
		static private $theme_url;

		/**
		 * @var $theme_path
		 */
		static private $theme_path;

		/**
		 * @var $gettext
		 */
		static private $gettext = null;

		/**
		 * Application initialisation
		 */
		static public function init() {

			// Let’s modify the `include_path`
			$path = dirname(__FILE__).'/';
			set_include_path( get_include_path() . PATH_SEPARATOR . $path );

			require_once( 'PEAR.php' );

			// Exception class
			require( 'PubwichErreur.php' );

			// Configuration files
			if ( !file_exists( dirname(__FILE__)."/../cfg/config.php" ) ) {
				throw new PubwichErreur( 'You must rename <code>/cfg/config.sample.php</code> to <code>/cfg/config.php</code> and edit the Web service configuration details.' );
			} else {
				require( dirname(__FILE__) . '/../cfg/config.php' );
			}

			// Internationalization class
			if ( defined('PUBWICH_LANG') && PUBWICH_LANG != '' ) {
				require( 'Gettext/streams.php' );
				require( 'Gettext/gettext.php' );
				self::$gettext = new gettext_reader( new FileReader( dirname(__FILE__).'/../lang/pubwich-'.PUBWICH_LANG.'.mo' ) );
			}

			// Events logger (and first message)
			require('PubwichLog.php');
			PubwichLog::log( 1, Pubwich::_("Pubwich object initialization") );

			// Theme
			self::$theme_url = PUBWICH_URL . 'themes/' . PUBWICH_THEME;
			self::$theme_path = dirname(__FILE__) . '/../themes/' . PUBWICH_THEME;
			require( 'PubwichTemplate.php' );

			// PHP objects creation
			self::setClasses();

			// Other classes
			require( 'FileFetcher.php' );
			require( 'CacheLite/Lite.php' );

			if ( !defined( 'PUBWICH_CRON' ) ) {
				require_once( 'Savant/Savant3.php' );
				require( 'Markup/Markdown/Markdown.php' );
				require( 'Markup/Smartypants/Smartypants.php' );
			}

		}

		/**
		 * Translate a string according to the defined locale/
		 *
		 * @param string $string 
		 * @return string
		 */
		public function _( $string ) {
			return (self::$gettext ) ? self::$gettext->translate( $string ) : $string;
		}

		/**
		 * @return void
		 */
		static public function setClasses() {
			require( 'Services/Service.php' );
			$columnCounter = 0;
			foreach ( self::getServices() as $column ) {
				$columnCounter++;
				self::$columns[$columnCounter] = array();
				foreach( $column as $service ) {

					list( $name, $variable, $config ) = $service;
					$service_instance = strtolower( $name . '_' . $variable );
					${$service_instance} = Pubwich::loadService( $name, $config );
					${$service_instance}->setVariable( $variable );
					self::$classes[] = ${$service_instance};
					self::$columns[$columnCounter][] = &${$service_instance};

				}
			}
		}

		/**
		 * @return void
		 */
		static public function renderTemplate() {

			// Création de l'objet Savant3
			$tpl =& new Savant3();
			$tpl->addPath( 'template', self::getThemePath() );

			if ( !file_exists(self::getThemePath()."/index.tpl.php") ) {
				throw new PubwichErreur( sprintf( Pubwich::_( 'The file <code>%s</code> was not found. It has to be there.' ), '/themes/'.PUBWICH_THEME.'/index.tpl.php' ) );
			}

			// Assignation des références aux objets pour utilisation dans le template
			foreach (self::$classes as &$classe) {
				// on récupère les données du service
				$classe->init();
				$tpl->assignRef( strtolower( $classe->variable ), $classe );
			}

			if ( file_exists( self::getThemePath()."/functions.php" ) ) {
				require( self::getThemePath()."/functions.php" );
				self::applyTheme();
			}

			// Affichage du template
			$tpl->display('index.tpl.php');
		}

		/**
		 * @return string
		 */
		static public function getThemePath() {
			return self::$theme_path;
		}

		/**
		 * @return string
		 */
		static public function getThemeUrl() {
			return self::$theme_url;
		}

		/**
		 * @param array $services
		 * @return void
		 */
		static public function setServices( $services = array() ) {
			self::$services = $services;
		}

		/**
		 * @return array
		 */
		static public function getServices( ) {
			return self::$services;
		}

		/**
		 * @param string $service Le nom du service (et de la classe)
		 * @param array $config Le tableau de configuration
		 * @return Service
		 */
		static public function loadService( $service, $config ) {
			PubwichLog::log( 1, "Chargement du service " . $service );

			if ( file_exists( dirname(__FILE__).'/Services/' . $service . '.php' ) ) {
				$fichier = 'Services/' . $service . '.php';
			} elseif ( file_exists( dirname(__FILE__).'/Services/Custom/' . $service . '.php' ) ) {
				$fichier = 'Services/Custom/' . $service . '.php';
			} else {
				throw new PubwichErreur( sprintf( Pubwich::_( 'You told Pubwich to use the %s service, but either the file <code>%s</code> or <code>%s</code> cannot be found.' ), $service, '/lib/Services/'.$service.'.php', '/lib/Services/Custom/'.$service.'.php' ) );
			}

			require_once( $fichier );
			
			$classname = ( $config['method'] ) ? $config['method'] : $service;
			if ( !class_exists( $classname ) ) {
				throw new PubwichErreur( sprintf( Pubwich::_( 'The class %s doesn\'t exist. Check your configuration file for inexistent services or methods.' ), $classname ) );
			}

			return new $classname( $config );
		}

		/**
		 * @return void
		 */
		static public function rebuildCache() {

			PubwichLog::log( 1, "Building application cache" );

			// On vide le contenu du dossier de cache
			$fichiers = scandir(CACHE_LOCATION);
			foreach ( $fichiers as $fichier ) {
				// on ne supprime pas les fichiers cachés...
				if ( substr( $fichier, 0, 1 ) != "." ) {
					unlink( CACHE_LOCATION . $fichier );
				}
			}

			// On rebâtit tout!
			foreach ( self::$classes as &$classe ) {
				$classe->buildCache();
			}

		}

		/**
		 * Applique les différents filtres du thème courant
		 *
		 * @return void
		 */
		static private function applyTheme() {

			if ( function_exists( 'boxTemplate' ) ) {
				$boxTemplate = call_user_func( 'boxTemplate' );
			}

			foreach( self::$classes as $classe ) {

				if ( !$classe->getBoxTemplate()->hasTemplate() && $boxTemplate ) {
					$classe->setBoxTemplate( $boxTemplate );
				}

				$boxFunction = get_parent_class( $classe ) . '_boxTemplate';
				if ( !$classe->getBoxTemplate()->hasTemplate() && function_exists( $boxFunction ) ) {
					$classe->setBoxTemplate( call_user_func( $boxFunction ) );
				}

				$boxVariableFunction = get_parent_class( $classe ) . '_' . $classe->getVariable() . '_boxTemplate';
				if ( !$classe->getBoxTemplate()->hasTemplate() && function_exists( $boxVariableFunction ) ) {
					$classe->setBoxTemplate( call_user_func( $boxVariableFunction ) );
				}

				$classFunction = get_parent_class( $classe ) . '_itemTemplate';
				if ( function_exists( $classFunction ) ) {
					$classe->setItemTemplate( call_user_func( $classFunction ) );
				}

				$variableFunction = get_parent_class( $classe ) . '_'.$classe->getVariable().'_itemTemplate';
				if ( function_exists( $variableFunction ) ) {
					$classe->setItemTemplate( call_user_func( $variableFunction ) );
				}
			}
		}

		/**
		 * Affiche les données
		 *
		 * @return string
		 */
		static public function getLoop() {
			$output = '';
			foreach( self::$columns as $col => $classes ) {
				$output .= '<div class="col'.$col.'">';	
				foreach( $classes as $classe ) {
					$output .= $classe->renderBox();
				}
				$output .= '</div>';
			}
			return $output;
		}

		/**
		 * time_since()
		 * Retourne une date en format relatif, si possible
		 *
		 * Basé sur: http://snippets.dzone.com/posts/show/5565
		 *
		 * @param $original Le timestamp
		 * @return string
		 *
		 */
		static public function time_since( $original ) {

			$original = strtotime( $original );

			$chunks = array(
				array( 60 * 60 * 24 * 365 , Pubwich::_('year') ),
				array( 60 * 60 * 24 * 30 , Pubwich::_('month' ) ),
				array( 60 * 60 * 24 * 7, Pubwich::_('week') ),
				array( 60 * 60 * 24 , Pubwich::_('day') ),
				array( 60 * 60 , Pubwich::_('hour') ),
				array( 60 , Pubwich::_('minute') ),
			);
			
			$today = time();
			$since = $today - $original;
		
			if ( $since < 60 ) {
				return sprintf( Pubwich::_('%d seconds ago'), $since );
			}
			
			if ( $since > ( 7 * 24 * 60 * 60 ) ) {
				$print =  strftime( Pubwich::_('%e %B at %H:%M'), $original ); 
				return $print;
			}
			
			for ( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) {
				$seconds = $chunks[$i][0];
				$name = $chunks[$i][1];
				if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
					break;
				}
			}

			$suffixe = "s";
			$print = ( $count == 1 ) ? '1&nbsp;'.$name : $count.'&nbsp;'.$name.$suffixe;

			return sprintf( Pubwich::_('%s ago'), $print );

		}

	}
