<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Reddit
	 * @description Fetch Reddit stories
	 * @version 1.0 (20100530)
	 * @author Rémi Prévost (exomel.com)
	 * @methods RedditLiked
	 */

	class Reddit extends Service {

		private $base = 'http://www.reddit.com';

		public function __construct( $config ){
			parent::__construct( $config );
			$this->total = $config['total'];
			$this->callback_function = array(Pubwich, 'json_decode');
		}

		public function getData() {
			return parent::getData()->data->children;
		}

		public function populateItemTemplate( &$item ) {
			$data = $item->data;
			return array(
				'base' => $this->base,
				'title' => $data->title,
				'link' => $this->base.$data->permalink,
				'url' => $data->url,
				'subreddit' => $data->subreddit,
				'author' => $data->author,
				'score' => $data->score,
				'comments' => $data->num_comments,
				'over_18' => $data->over_18 == 'true',
				'thumbnail' => $data->thumbnail,
				'domain' => $data->domain,
			);
		}

	}

	class RedditLiked extends Reddit {
		public function __construct( $config ){
			$this->setURLTemplate('http://www.reddit.com/user/'.$config['username'].'/liked/');
			$this->setURL( sprintf( 'http://www.reddit.com/user/%s/liked.json?feed=%s&user=%s', $config['username'], $config['key'], $config['username'] ) );
			$this->setItemTemplate('<li><a class="story" href="{{{url}}}">{{{title}}} <span>({{{domain}}})</span></a> <a href="{{{base}}}/r/{{{subreddit}}}/" class="subreddit">{{{subreddit}}} ∞</a> <a href="{{{link}}}" class="score">{{{score}}} ⇧</a> <a href="{{{link}}}" class="comments">{{{comments}}} ✎</a></li>'."\n");
			parent::__construct( $config );
		}
	}
