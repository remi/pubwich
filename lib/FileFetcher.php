<?php
	defined('PUBWICH') or die('No direct access allowed.');

    /**
	 * Récupérateur de contenu provenant de fichiers à distance
	 *
	 * @classname FileFetcher
	 */
	class FileFetcher {
		private $url;

		/**
		 * Récupère le contenu d'un fichier
		 *
		 * @param string $url L'URL du fichier
		 * @return mixed Le contenu du fichier en cas de succès. FALSE en cas d'échec
		 */
		static function get($url) {
			if ( function_exists('curl_init') ) {
				return self::getCurl($url);
			}
			if ( ini_get('allow_url_fopen') ) {
				return self::getRemote($url);
			}
			return false;
		}

		/**
		 * Récupère le contenu à l'aide `file_get_contents`
		 *
		 * @param string $url L'URL du fichier
		 * @return string Le contenu du fichier
		 */
		static function getRemote($url) {
			return file_get_contents($url);
		}

		/**
		 * Récupère le contenu à l'aide de l'extension cURL
		 *
		 * @param string $url L'URL du fichier
		 * @return string Le contenu du fichier
		 */
		static function getCurl($url) {
			$ch = curl_init();
			$timeout = 10;
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt ($ch, CURLOPT_USERAGENT, "Pubwich ".PUBWICH_VERSION." - http://www.pubwich.com/");
			$file_contents = curl_exec($ch);
			curl_close($ch);
			return $file_contents; 
		}
	}
