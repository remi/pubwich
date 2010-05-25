<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Text
	 * @description Display a simple text block
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */
	class Text extends Service {

		public function __construct( $config ){
			$this->text = $config['text'];
			$this->setItemTemplate('{{{text}}}'."\n");
			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			return array( $this->text );
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			return array(
				'text' => $this->text
			);
		}

		/**
		 * @return array
		 */
		public function populateBoxTemplate( ) {
		 	return array(
				'class' => $this->title ? '' : 'no-title'
			) + parent::populateBoxTemplate();
		}

	}

