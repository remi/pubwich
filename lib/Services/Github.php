<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname GitHub
	 * @description Fetch GitHub user public activity feed
	 * @version 1.1 (20100317)
	 * @author Rémi Prévost (exomel.com)
	 * @methods GithubRecentActivity GithubRepositories
	 */

	Pubwich::requireServiceFile( 'Atom' );
	class GithubRecentActivity extends Atom {

		/**
		 * @constructor
		 */
		public function __construct( $config ){
			$config['url'] = sprintf( 'http://github.com/%s.atom', $config['username'] );
			$config['link'] = 'http://github.com/'.$config['username'].'/';
			parent::__construct( $config );
			$this->setItemTemplate('<li class="clearfix"><a href="{%link%}">{%title%}</a></li>'."\n");
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			return parent::populateItemTemplate( $item ) + array(
			);
		}

		/**
		 * Adds "github" to the box HTML class attribute
		 * @return array
		 */
		public function populateBoxTemplate( $data ) {
			return parent::populateBoxTemplate( $data ) + array( 'class' => $data['class'].' github');
		}

	}

	class GithubRepositories extends Service {

		/**
		 * @constructor
		 */
		public function __construct( $config ){
			$this->setURL( sprintf( 'http://github.com/api/v2/xml/repos/show/%s', $config['username'] ) );
			$this->setURLTemplate( 'http://github.com/'.$config['username'].'/' );
			$this->setItemTemplate('<li class="clearfix"><div class="metadata"><a href="{%url%}"><strong>{%name%}</strong></a></div><div class="infos">{%description%}</div></li>'."\n");
			parent::__construct( $config );
		}

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			return array(
				'description' => $item->description,
				'forks' => $item->forks,
				'name' => $item->name,
				'watchers' => $item->watchers,
				'private' => $item->private,
				'url' => $item->url,
				'fork' => $item->fork,
				'owner' => $item->owner,
				'homepage' => $item->homepage,
			);
		}

		/**
		 * @return array
		 */
		public function getData() {
			return parent::getData()->repository;
		}

		/**
		 * Adds "github" to the box HTML class attribute
		 * @return array
		 */
		public function populateBoxTemplate( $data ) {
			return parent::populateBoxTemplate( $data ) + array( 'class' => $data['class'].' github');
		}

	}
