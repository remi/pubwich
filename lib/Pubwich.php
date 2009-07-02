<?php

	define( 'PUBWICH_VERSION', 1.0 );

	/**
	 * @classname Pubwich
	 */
	class Pubwich {

		/**
		 * Contient le tableau des services utilisés sur Pubwich
		 *
		 * @var $services
		 */
		static private $services;

		/**
		 * Contient un tableau d'objets PHP représentant chaque service utilisé
		 *
		 * @var $classes
		 */
		static private $classes;

		static private $columns;

		static private $theme_url;
		static private $theme_path;

		/**
		 * Initialise l'application
		 */
		static public function init() {

			// Modification du `include_path`
			$path = dirname(__FILE__).'/';
			set_include_path(get_include_path() . PATH_SEPARATOR . $path);

			require_once('PEAR.php');

			// Classe d'exception personnalisée
			require('PubwichErreur.php');

			// Fichier de configuration
			if (!file_exists(dirname(__FILE__)."/../cfg/config.php")) {
				throw new PubwichErreur('Vous devez renommer le fichier <code>/cfg/config.sample.php</code> en <code>/cfg/config.php</code> et y adapter les URLs des services Web.');
			} else {
				require(dirname(__FILE__).'/../cfg/config.php');
			}

			// Logger d'évènements (et premier message)
			require('PubwichLog.php');
			PubwichLog::log( 1, "Initialisation de l'objet Pubwich" );

			// Assignation du thème
			self::$theme_url = PUBWICH_URL.'themes/'.PUBWICH_THEME;
			self::$theme_path = dirname(__FILE__).'/../themes/'.PUBWICH_THEME;
			require('PubwichTemplate.php');

			// Création des objets PHP
			self::setClasses();

			// Inclusion des autres classes externes
			require('FileFetcher.php');
			require('CacheLite/Lite.php');

			if ( !defined('PUBWICH_CRON') ) {
				require_once('Savant/Savant3.php');
				require('Markup/Markdown.php');
				require('Markup/Smartypants.php');
			}

		}

		/**
		 * Crée un tableau qui contient les instances des objets de Services
		 *
		 * @return void
		 */
		static public function setClasses() {
			require('Services/Service.php');
			$columnCounter = 0;
			foreach ( self::getServices() as $column ) {
				$columnCounter++;
				self::$columns[$columnCounter] = array();
				foreach( $column as $service ) {

					list($nom, $variable, $config) = $service;
					$service_instance = strtolower( $nom . '_' . $variable );
					${$service_instance} = Pubwich::loadService( $nom, $config );
					${$service_instance}->setVariable( $variable );
					self::$classes[] = ${$service_instance};
					self::$columns[$columnCounter][] = &${$service_instance};

				}
			}
		}

		/**
		 * Création et affichage du document HTML
		 *
		 * @return void
		 */
		static public function renderTemplate() {

			// Création de l'objet Savant3
			$tpl =& new Savant3();
			$tpl->addPath( 'template', self::getThemePath() );

			if ( !file_exists(self::getThemePath()."/index.tpl.php") ) {
				throw new PubwichErreur('Le fichier <code>/themes/'.PUBWICH_THEME.'/index.tpl.php</code> n\'a pas été trouvé. Il doit être présent.');
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
		 * Définit le chemin vers les fichiers du thème
		 *
		 * @return string
		 */
		static public function getThemePath() {
			return self::$theme_path;
		}

		/**
		 * Définit l'URL  vers les fichiers du thème
		 *
		 * @return string
		 */
		static public function getThemeUrl() {
			return self::$theme_url;
		}

		/**
		 * Définit les services utilisés sur Pubwich
		 *
		 * @param array $services Le tableau de services
		 * @return void
		 */
		static public function setServices( $services = array() ) {
			self::$services = $services;
		}

		/**
		 * Récupère les services utilisés sur Pubwich
		 *
		 * @return array
		 */
		static public function getServices( ) {
			return self::$services;
		}

		/**
		 * Charge la classe d'un service
		 *
		 * @param string $service Le nom du service (et de la classe)
		 * @return Service
		 */
		static public function loadService( $service, $config ) {
			PubwichLog::log( 1, "Chargement du service " . $service );

			if (!file_exists( dirname(__FILE__).'/Services/' . $service . '.php' ) ) {
				throw new PubwichErreur('Vous avez dit à Pubwich d\'utiliser le service '.$service.' mais le fichier <code>/lib/Services/'.$service.'.php</code> n\'a pu être trouvé.');
			}
			require_once( 'Services/' . $service . '.php' );
			return new $service( $config );
		}

		/**
		 * Reconstruit toute la cache de l'application
		 *
		 * @return void
		 */
		static public function rebuildCache() {

			PubwichLog::log( 1, "Reconstruction de la cache de l'application" );

			// On vide le contenu du dossier de cache
			$fichiers = scandir(CACHE_LOCATION);
			foreach ($fichiers as $fichier) {
				// on ne supprime pas les fichiers cachés...
				if (substr($fichier, 0, 1) != ".") {
					unlink(CACHE_LOCATION.$fichier);
				}
			}

			// On rebâtit tout!
			foreach (self::$classes as &$classe) {
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
				
				if ( $boxTemplate ) {
					$classe->setBoxTemplate( $boxTemplate );
				}

				$boxFunction = get_class( $classe ) . '_boxTemplate';
				if ( function_exists( $boxTemplate ) ) {
					$classe->setBoxTemplate( $boxFunction );
				}

				$classFunction = get_class( $classe ) . '_itemTemplate';
				if ( function_exists( $classFunction ) ) {
					$classe->setItemTemplate( call_user_func( $classFunction ) );
				}

				$variableFunction = get_class( $classe ) . '_'.$classe->getVariable().'_itemTemplate';
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
					$output .= self::renderBox( $classe );
				}
				$output .= '</div>';
			}
			return $output;
		}

		/*
		 * Affiche la boite d'une classe spécifique
		 *
		 * @return string
		 */
		static private function renderBox( &$classe ) {

			$items = '';
			foreach( $classe->getData() as $item ) {
				$compteur++;
				if ($classe->total && $compteur > $classe->total) { break; }  
				$classe->getItemTemplate()->populate( $classe->populateItemTemplate( $item ) );
				$items .= '		'.$classe->getItemTemplate()->output();
			}

			$data = array(
				'class' => strtolower(get_class($classe)),
				'id' => $classe->getVariable(),
				'url' => $classe->urlTemplate,
				'title' => $classe->title,
				'description' => $classe->description,
				'items' => $items	
			);

			$classe->getBoxTemplate()->populate( $data );
			return $classe->getBoxTemplate()->output();
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

			$original = strtotime($original);

			$chunks = array(
				array(60 * 60 * 24 * 365 , 'année'),
				array(60 * 60 * 24 * 30 , 'mois'),
				array(60 * 60 * 24 * 7, 'semaine'),
				array(60 * 60 * 24 , 'jour'),
				array(60 * 60 , 'heure'),
				array(60 , 'minute'),
			);
			
			$today = time();
			$since = $today - $original;
		
			if ($since < 60) {
				return 'il y a '.$since.' secondes';
			}
			
			if ($since > (7 * 24 * 60 * 60)) {
				$print =  strftime('%e %B à %H:%M', $original); 
				return $print;
			}
			
			for ($i = 0, $j = count($chunks); $i < $j; $i++) {
				$seconds = $chunks[$i][0];
				$name = $chunks[$i][1];
				if (($count = floor($since / $seconds)) != 0) {
					break;
				}
			}

			$suffixe = "";
			if ($name != "mois") { $suffixe = "s"; }

			$print = ($count == 1) ? '1&nbsp;'.$name : $count.'&nbsp;'.$name.$suffixe;

			return 'il y a '.$print;

		}

	}
