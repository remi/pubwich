<?php
	defined('PUBWICH') or die('No direct access allowed.');

	define( 'PUBWICH_VERSION', '1.4' );

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
		 * @var $header_links
		 */
		static private $header_links;

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
			$path_pear = dirname(__FILE__).'/PEAR/';
			set_include_path( $path . PATH_SEPARATOR . $path_pear . PATH_SEPARATOR . get_include_path() );

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
				self::$gettext = new gettext_reader( new FileReader( dirname(__FILE__).'/../lang/'.PUBWICH_LANG.'/pubwich-'.PUBWICH_LANG.'.mo' ) );
			}

			// JSON support
			if ( !function_exists( 'json_decode' ) ) {
				require_once( dirname(__FILE__) . '/../Zend/Json.php' );
			}
			// Events logger (and first message)
			require('PubwichLog.php');
			PubwichLog::init();
			PubwichLog::log( 1, Pubwich::_("Pubwich object initialization") );

			// Theme
			self::$theme_url = PUBWICH_URL . 'themes/' . PUBWICH_THEME;
			self::$theme_path = dirname(__FILE__) . '/../themes/' . PUBWICH_THEME;
			require( 'PubwichTemplate.php' );

			// PHP objects creation
			self::setClasses();

			// Other classes
			require( 'FileFetcher.php' );
			require( 'Cache/Lite.php' );

			if ( !defined( 'PUBWICH_CRON' ) ) {
				require_once( 'Mustache.php/Mustache.php' );
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

			if ( !file_exists(self::getThemePath()."/index.tpl.php") ) {
				throw new PubwichErreur( sprintf( Pubwich::_( 'The file <code>%s</code> was not found. It has to be there.' ), '/themes/'.PUBWICH_THEME.'/index.tpl.php' ) );
			}

			foreach (self::$classes as &$classe) {
				$classe->init();
			}

			if ( file_exists( self::getThemePath()."/functions.php" ) ) {
				require( self::getThemePath()."/functions.php" );
				self::applyTheme();
			}

			include (self::getThemePath() . '/index.tpl.php' );
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
		 * @return bool
		 */
		static public function requireServiceFile( $service ) {
			$files = array(
				// theme-specific service
				self::$theme_path . '/lib/Services/' . $service . '.php',
				// pubwich custom service
				dirname(__FILE__).'/Services/Custom/' . $service . '.php',
				// pubwich default service
				dirname(__FILE__).'/Services/' . $service . '.php'
			);

			$file_included = false;
			foreach( $files as $file ) {
				if ( file_exists( $file ) ) {
					require_once( $file );
					$file_included = true;
					break;
				}
			}
			return $file_included;
		}

		/**
		 * @param string $service Le nom du service (et de la classe)
		 * @param array $config Le tableau de configuration
		 * @return Service
		 */
		static public function loadService( $service, $config ) {
			PubwichLog::log( 1, sprintf( Pubwich::_('Loading %s service'), $service ) );

			$file_included = self::requireServiceFile( $service );

			if ( !$file_included ) {
				throw new PubwichErreur( sprintf( Pubwich::_( 'You told Pubwich to use the %s service, but the file <code>%s</code> couldn’t be found.' ), $service, $service.'.php' ) );
			}

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

			PubwichLog::log( 1, Pubwich::_("Building application cache") );

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
			} else {
				throw new PubwichErreur( Pubwich::_('You must define a boxTemplate function in your theme\'s functions.php file.') );
			}

			foreach( self::$classes as $classe ) {

				$functions = array();
				$parent = get_parent_class( $classe );
				$classname = get_class( $classe );
				$variable = $classe->getVariable();

				if ( !$classe->getBoxTemplate()->hasTemplate() && $boxTemplate ) {
					$classe->setBoxTemplate( $boxTemplate );
				}

				if ( $parent != 'Service' ) {
					$functions = array(
						$parent,
						$parent . '_' . $classname,
						$parent . '_' . $classname . '_' . $variable,
					);
				} else {
					$functions = array(
						$classname,
						$classname . '_' . $variable,
					);
				}

				foreach ( $functions as $f ) {
					$box_f = $f . '_boxTemplate';
					$item_f = $f . '_itemTemplate';

					if ( function_exists( $box_f ) ) {
						$classe->setBoxTemplate( call_user_func( $box_f ) );
					}

					if ( function_exists( $item_f ) ) {
						$classe->setItemTemplate( call_user_func( $item_f ) );
					}
				}
			}
		}

		/**
		 * Affiche les données
		 *
		 * @return string
		 */
		static public function getLoop() {

			$columnTemplate = function_exists( 'columnTemplate' ) ? call_user_func( 'columnTemplate' ) : '<div class="col{{{number}}}">{{{content}}}</div>';
			$layoutTemplateDefined = false;

			if ( function_exists( 'layoutTemplate' ) ) {
				$layoutTemplate = call_user_func( 'layoutTemplate' );
				$layoutTemplateDefined = true;
			} else {
				$layoutTemplate = '';
			}

			$output_columns = array();
			$m = new Mustache;
			foreach( self::$columns as $col => $classes ) {
				$boxes = '';
				foreach( $classes as $classe ) {
					$boxes .= $classe->renderBox();
				}
				$output_columns['col'.$col] = $m->render($columnTemplate, array('number'=>$col, 'content'=>$boxes));

				if ( !$layoutTemplateDefined ) {
					$layoutTemplate .= '{{{col'.$col.'}}} ';
				}
			}
			return $m->render($layoutTemplate, $output_columns);
		}

		static public function getHeader() {
			$output = '';
			foreach ( self::$classes as $class ) {
				$link = $class->getHeaderLink();
				if ( $link ) {
					$output .= '		<link rel="alternate" title="'.$class->title.' - '.$class->description.'" href="'.htmlspecialchars( $link['url'] ).'" type="'.$link['type'].'">'."\n";
				}
			}
			return $output;
		}

		static public function getFooter() {
			return '';
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

			if ( $since < 0 ) {
				return sprintf( Pubwich::_('just moments ago'), $since );
			}

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

		/**
		 * @param string $str JSON-encoded object
		 * @return object PHP object
		 */
		public function json_decode( $str ) {
			if ( function_exists( 'json_decode' ) ) {
				return json_decode( $str );
			} else {
				return Zend_Json::decode( $str, Zend_Json::TYPE_OBJECT );
			}
		}

	}
