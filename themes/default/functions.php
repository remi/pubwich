<?php
	
	function boxTemplate() {
		return '
			<div class="boite {%class%}" id="{%id%}">
				<h2><a rel="me" href="{%url%}">{%title%}</a> <span>{%description%}</span></h2>
				<div class="boite-inner">
					<ul class="clearfix">
			{%items%}
					</ul>
				</div>
			</div>';

	}

	/****************************************************
	 *
	 * Peut-être que ces fonctions vous seront utiles...
	 *
	 ****************************************************/
	/*
	function RSS_ixmedia_itemTemplate() {
		return '<li><a href="{%link%}">{%title%}</a> {%date%} <div class="text">{%description%}</div></li>'."\n";
	}

	function Delicious_itemTemplate() {
		return '<li><a href="{%link%}">{%title%}</a><div class="desc">{%description%}</div></li>'."\n";
	}	

	function Vimeo_itemTemplate() {
		return '<li><a class="clearfix" href="{%link%}"><img src="{%image_small%}" alt="{%title%}" /><strong>{%title%}</strong> <span>{%caption%}</span></a></li>'."\n";
	}	

	function Youtube_itemTemplate() {
		return '<li class="clearfix"><a href="{%link%}"><img width="{%size%}" src="{%image%}" alt="{%title%}" /><strong>{%title%}</strong> <span>{%description%}</span></a></li>'."\n";
	}

	function Twitter_itemTemplate() {
		return '<li class="clearfix"><span class="date"><a href="{%link%}">{%date%}</a></span>{%text%}</li>'."\n";
	}
	 */
