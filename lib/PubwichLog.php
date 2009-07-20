<?php

	/**
	 * @classname PubwichLog
	 *
	 * Niveau 1 : Informations générales
	 * Niveau 2 : Informations détaillées
	 * Niveau 3 : ???
	 */
	class PubwichLog {

		static public function log( $level, $msg ) {
			
			if ( $level <= PUBWICH_LOGLEVEL ) {
				if ( !PUBWICH_LOGTOFILE ) {
					echo $msg . "<br />\n";
				} else {
					$log_file = dirname(__FILE__).'/../logs/pubwich-'.date('Y-m-d').'.log';
					$fh = fopen($log_file, 'a') or die("can't open file");
					$stringData = Date('Y d j h:i s').' '. $msg ."\n";
					fwrite($fh, $stringData);
					fclose($fh);
				}
			}

		}

	}

?>
