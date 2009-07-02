<?php

	function boxTemplate() {
		$output  = '';
		$output .= '<div class="boite {%class%}" id="{%id%}"'."\n";
		$output .= '	<h2><a rel="me" href="{%url%}">{%title%}</a> <span>{%description%}</span></h2>'."\n";
		$output .= '	<ul class="clearfix">'."\n";
		$output .= '		{%items%}'."\n";
		$output .= '	</ul>'."\n";
		$output .= '</div>'."\n\n";
		return $output;
	}

	function Delicious_itemTemplate() {
		return '<li><a href="{%link%}">TESTTEST {%title%}</a></li>'."\n";
	}	

	function Delicious_liens_itemTemplate() {
		return '<li><a href="{%link%}">BLABLATESTTEST {%title%}</a></li>'."\n";
	}	

	//function Atom_effair_itemTemplate() {
	//	return '<li><strong>{%title%}</strong></li>';
	//}

