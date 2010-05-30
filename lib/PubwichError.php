<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname PubwichError
	 * @extends Exception
	 */
	class PubwichError extends Exception {

		/**
		 * @constructor
		 */
		public function __construct($msg) {
			die('<strong>'.Pubwich::_('Error!').'</strong> '.$msg);
		}


	}

