<?php

	class Twitter extends Service {

		public $username;

		public function __construct( $config ){
			$this->setURL( sprintf( 'http://twitter.com/statuses/user_timeline/%s.xml?count=%d', $config['id'], $config['total'] ) );
			$this->username = $config['username'];

			$this->title = $config['title'];
			$this->description = $config['description'];
			$this->setItemTemplate('<li class="clearfix"><span class="date"><a href="{%link%}">{%date%}</a></span>{%text%}</li>'."\n");
			$this->setURLTemplate('http://www.twitter.com/'.$config['username'].'/');

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

		public function populateItemTemplate( &$item ) {
			return array(
						'link' => sprintf( 'http://www.twitter.com/%s/statuses/%s/', $this->username, $item->id ),
						'text' => $this->filterContent( $item->text ),
						'date' => Pubwich::time_since( $item->created_at )
						);
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
