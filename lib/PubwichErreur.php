<?php
	defined('PUBWICH') or die('No direct access allowed.');

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

