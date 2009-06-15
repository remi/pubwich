<?php

	/**
	 * @classname PubwichErreur
	 * @extends Exception
	 */
	class PubwichErreur extends Exception {

		/**
		 * @constructor
		 */
		public function __construct($msg) {
			die('<strong>Erreur!</strong> '.$msg);
		}


	}

