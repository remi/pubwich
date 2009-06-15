<?php

	class Twitter extends Service {

		private $url_template = 'http://twitter.com/statuses/user_timeline/%s.xml?count=%d';

		public function __construct( $config ){
			list($id, $total) = $config;
			$this->setURL( sprintf( $this->url_template, $id, $total ) );
			parent::__construct();
		}

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->status;
		}

		/**
		 * Filtre le contenu d'un tweet. Effectue le traitement suivant:
		 *
		 * 1. Enlève tous les tags HTML que Twitter pourrait inclure (?)
		 * 2. Rends les liens hypertextes cliquables
		 * 3. Remplace "@username" par un lien cliquable vers le profil de "username"
		 * 4. Passe le contenu dans Smartypants puis Markdown
		 *
		 * @param string $text Le contenu du tweet
		 *
		 * @return string
		 */
		public function filterContent( $text ) {
			$text = strip_tags( $text );
			$text = preg_replace( '/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $text );
			$text = preg_replace( '/\@([^\s\ \:\.\;\-\!\)\(\"]+)/', '@<a href="http://twitter.com/\\1">\\1</a>', $text );
			$text = '<p>' . ( Smartypants( $text ) ) . '</p>';
			
			return $text;
		}
		
	}
