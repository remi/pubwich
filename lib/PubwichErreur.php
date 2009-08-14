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
			die('<strong>'.Pubwich::_('Erreur').'!</strong> '.$msg);
		}


	}

