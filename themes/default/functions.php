<?php
	
	/**
	 *
	 * Fonctions personnalisées du thème
	 * ------------------------------------------------------------------------
	 * - Gabarit par défaut d’une boite : boxTemplate
	 * - Gabarit d’une boite d’un service : <Service>_boxTemplate (eg. Facebook_boxTemplate)
	 * - Gabarit d’une boite d’une instance d’un service : <Service>_<variable>_boxTemplate (eg. Facebook_etats_boxTemplate)
	 * - Gabarit d’un item d’un service : <Service>_itemTemplate (eg. Delicious_itemTemplate)
	 * - Gabarit d’un item d’une instance de service : <Service>_<variable>_itemTemplate (eg. RSS_monblogue_itemTemplate) 
	 *
	 * 
	 * Tags à utiliser dans les gabarits
	 * ------------------------------------------------------------------------
	 *
	 * Gabarits de boites :
	 *
	 *   - {%class%}
	 *   - {%id%}
	 *   - {%url%}
	 *   - {%title%}
	 *   - {%description%}
	 *   - {%items%}
	 *
	 * Gabarits d’items
	 *
	 *   - Différents selon le service
	 *
	 */

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
