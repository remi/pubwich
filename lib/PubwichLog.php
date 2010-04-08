<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname PubwichLog
	 * @author remi (exomel.com)
	 *
	 * Niveau 1 : Informations générales
	 * Niveau 2 : Informations détaillées
	 * Niveau 3 : ???
	 */
	class PubwichLog {

		static private $file;

		static public function init() {

			if ( PUBWICH_LOGTOFILE === true && PUBWICH_LOGLEVEL > 0 ) {
				$log_dir = dirname(__FILE__).'/../logs';
				if ( !is_dir( $log_dir  ) ) {
					mkdir( $log_dir );
				}
				$log_file = $log_dir . '/pubwich-'.date('Y-m-d').'.log';
				self::$file = fopen($log_file, 'a+');
				// what will happen if several sessions are started simultaneously?
				//self::log( 0, '----[ Log for Pubwich session ('.date('Y-m-d h:i:s').') ]-----------------------------', true );
			}

		}

		static public function log( $level, $msg, $nodate=false ) {

			if ( $level <= PUBWICH_LOGLEVEL ) {
				if ( !PUBWICH_LOGTOFILE ) {
					echo $msg . "<br />\n";
				} else {
					$stringData = ($nodate === true) ? $msg ."\n" : Date('Y-m-d h:i:s').' '. $msg ."\n";
					fwrite(self::$file, $stringData);
				}
			}
		}

	}

?>
