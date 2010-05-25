<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname PubwichTemplate
	 *
	 */
	class PubwichTemplate {

		private $template = null, $output;

		/**
		 * @constructor
		 */
		public function __construct( $template=null ) {
			if ( $template ) {
				$this->setTemplate($template);
			}
		}

		/**
		 * Définit la chaine qui sera utilisée comme template
		 *
		 * @param string $template La chaine
		 * @return void
		 */
		public function setTemplate( $template ) {
			$this->template = $template;
		}

		/**
		 * Retourne si un template a une chaine d'assignée
		 *
		 * @return bool
		 */
		public function hasTemplate() {
			return ( $this->template !== null );
		}

		/**
		 * Remplit le template avec des données
		 *
		 * @param array $data Les données sous forme de tableau
		 * @return void
		 */
		public function populate( $data ) {
			$m = new Mustache;
			$this->output = $m->render($this->template, $data);
		}

		/**
		 * Retourne le résultat du template rempli
		 *
		 * @return string
		 */
		public function output() {
			return $this->output;
		}

	}

?>
