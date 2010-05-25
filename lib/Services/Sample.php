<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Sample
	 * @description Sample Pubwich Service
	 * @version 1.0 (20091207)
	 * @author Rémi Prévost (exomel.com)
	 * @methods SampleDays SampleRandom
	 */

	class Sample extends Service {

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			parent::__construct( $config );
		}

	}

	class SampleDays extends Sample {

		public function __construct( $config ) {
			$this->setURL( sprintf('http://api.pubwich.org/days/?total=%d', $config['total'] ) );
			$this->setURLTemplate('http://api.pubwich.org/days/');
			$this->setItemTemplate('<li>{{{year}}}/{{{month}}}/{{{day}}}</li>');
			parent::__construct( $config );
		}

		public function getData() {
			$data = parent::getData();
			return $data->days;
		}

		public function populateItemTemplate( &$item ) {
			return array(
				'year' => $item->year,
				'month' => $item->month,
				'day' => $item->day,
			);
		}

	}

	class SampleRandom extends Sample {

		public function __construct( $config ) {
			$this->setURL( sprintf('http://api.pubwich.org/random/?min=%d&max=%d', $config['min'], $config['max'] ) );
			$this->setURLTemplate('http://api.pubwich.org/random/');
			$this->setItemTemplate( '<li><strong>{{{number}}}</strong></li>' );
			parent::__construct( $config );
		}

		public function getData() {
			$data = parent::getData();
			return $data->number;
		}

		public function populateItemTemplate( &$item ) {
			return array(
				'number' => $item,
			);
		}

	}
