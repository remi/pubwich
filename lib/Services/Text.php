<?php

	class Text extends Service {
	
		public function __construct( $config ){
			$this->text = $config['text'];

			$this->setItemTemplate('{%text%}'."\n");
			$this->setBoxTemplate('
			<div class="boite {%class%}'.(($this->title)?'':' no-title').'" id="{%id%}">
				'. (($this->title)?'<h2>{%title%} <span>{%description%}</span></h2>':'').'
				<div class="boite-inner">
					{%items%}
				</div>
			</div>');

			parent::__construct( $config );
		}

		/**
		 * Surcharge de parent::getData()
		 * @return SimpleXMLElement
		 */
		public function getData() {
			return array( $this->text );
		}

		/**
		 * Retourne un item formatté selon le gabarit
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			return array(
				'text' => $this->text
			);
		}

	}

