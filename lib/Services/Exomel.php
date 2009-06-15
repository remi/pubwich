<?php

	class Exomel extends Service {

		private $url_template = 'http://exomel.com/atom/';

		public function __construct( $config ){
			list($total) = $config;
			$this->setURL( sprintf( $this->url_template ) );
			$this->total = $total;
			parent::__construct();
		}

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->entry;
		}

		/**
		 * Retourne l'image du projet
		 *
		 * @param SimpleXMLElement $projet Le projet
		 * @return string
		 */
		public function getImage( $projet ) {
			$attr = $projet->content->div->p[1]->a->img->attributes();
			return $attr['src'];
		}
		
		/**
		 * Retourne le lien vers le  projet
		 *
		 * @param SimpleXMLElement $projet Le projet
		 * @return string
		 */
		public function getLink( $projet ) {
			$attr = $projet->content->div->p[1]->a->attributes();
			return $attr['href'];
		}			
	}
