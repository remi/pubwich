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
				echo $msg . "<br />\n";
			}
		}

	}

?>
