<?php
	
	function boxTemplate() {
		return '
			<div class="box {{{class}}}" id="{{{id}}}">
				<h2><a rel="me" href="{{{url}}}">{{{title}}}</a> <span>{{{description}}}</span></h2>
				<div class="boite-inner">
					<ul class="clearfix">
						{{{items}}}
					</ul>
				</div>
			</div>
	';
	}

	function Text_boxTemplate() {
		return '
			<div class="box {{{class}}}" id="{{{id}}}">
				<h2>{{{title}}} <span>{{{description}}}</span></h2>
				<div class="boite-inner">
					{{{items}}}
				</div>
			</div>
	';
	}

	/****************************************************
	 *
	 * Maybe these will be useful...
	 *
	 ****************************************************/
	/*

	function populateBoxTemplate( $item ) {
		return array(
		);
	}

	function TwitterUser_populateItemTemplate( $item ) {
		return array(
		);	
	}
	
	function RSS_ixmedia_itemTemplate() {
		return '<li><a href="{{link}}">{{{title}}}</a> {{{date}}} <div class="text">{{{description}}}</div></li>'."\n";
	}

	function Delicious_itemTemplate() {
		return '<li><a href="{{link}}">{{{title}}}</a><div class="desc">{{{description}}}</div></li>'."\n";
	}	

	function Vimeo_itemTemplate() {
		return '<li><a class="clearfix" href="{{link}}"><img src="{{{image_small}}}" alt="{{{title}}}" /><strong>{{{title}}}</strong> <span>{{{caption}}}</span></a></li>'."\n";
	}	

	function Youtube_itemTemplate() {
		return '<li class="clearfix"><a href="{{link}}"><img width="{{{size}}}" src="{{{image}}}" alt="{{{title}}}" /><strong>{{{title}}}</strong> <span>{{{description}}}</span></a></li>'."\n";
	}

	function Flickr_boxTemplate() {
		return '
			<div class="boite {{{class}}}" id="{{{id}}}">
				<h2><a rel="me" href="{{{url}}}">{{{title}}}</a> <span>{{{description}}}</span></h2>
				<div class="boite-inner">
					<ul class="clearfix">
			{{{items}}}
					</ul>
				</div>
			</div>';

	}
	
	function Flickr_FlickrUser_boxTemplate() {
		return '
			<div class="boite {{{class}}}" id="{{{id}}}">
				<h2><a rel="me" href="{{{url}}}">{{{title}}}</a> <span>{{{description}}}</span></h2>
				<div class="boite-inner">
					<ul class="clearfix">
			{{{items}}}
					</ul>
				</div>
			</div>';

	}

	function LastFM_LastFMRecentTracks_itemTemplate() {
		return '<li{{{classe}}}><a class="clearfix" href="{{link}}"><b>{{{artist}}}</b> — {{{track}}}</a></li>'."\n";
	}

	function Twitter_itemTemplate() {
		return '<li class="clearfix"><span class="date">AAAH! <a href="{{link}}">{{{date}}}</a></span>{{{text}}}</li>'."\n";
	}

	function Twitter_TwitterSearch_itemTemplate() {
		return '<li class="clearfix"><span class="date"><a href="{{{user_link}}}"><img src="{{{user_image}}}" alt="" /></a><br />{{{user_nickname}}}</span>{{{text}}}</li>'."\n";
	}

	function Flickr_itemTemplate() {
		return '<li{{{classe}}}><a href="{{link}}"><img src="{{{photo}}}" alt="{{{title}}}" /></a></li>'."\n";
	}

	function Flickr_FlickrUser_itemTemplate() {
		return '<li{{{classe}}}><a href="{{link}}">{{{title}}}<br /> <img src="{{{photo}}}" alt="{{{title}}}" /></a></li>'."\n";
	}

	 */
