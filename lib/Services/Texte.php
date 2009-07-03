<?php

	class Texte extends Service {
	
		public $username, $size;

		public function __construct( $config ){
			$this->text = $config['text'];
			$this->title = $config['title'];
			$this->description = $config['description'];

			$this->setItemTemplate('{%text%}'."\n");
			$this->setBoxTemplate('
			<div class="boite {%class%}'.(($this->title)?'':' no-title').'" id="{%id%}">
				'. (($this->title)?'<h2>{%title%} <span>{%description%}</span></h2>':'').'
				<div class="boite-inner">
					{%items%}
				</div>
			</div>');

			parent::__construct();
		}

		/**
		 * Surcharge de parent::getData()
		 *
		 * @return SimpleXMLElement
		 */
		public function getData() {
			return array( $this->text );
		}

		public function populateItemTemplate( &$item ) {
			return array(
				'text' => $this->text
			);
		}


	}

