<?php

	define( 'CRON', true );

	require( dirname(__FILE__) . '/../lib/Pubwich.php');
	Pubwich::init();
	Pubwich::rebuildCache();

?>
