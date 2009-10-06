<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title><?=PUBWICH_TITLE?></title>
		<link rel="stylesheet" media="screen" href="<?=Pubwich::getThemeUrl()?>/style.css" type="text/css">
	</head>
	<body>
		<div id="wrap">
			<h1><a href="/" rel="me"><?=PUBWICH_TITLE?></a></h1>
			<hr>
			<div class="clearfix">

			<?=Pubwich::getLoop()?>

			</div>
			<div id="footer">
				<div class="footer-inner">
					<hr>
					<?=sprintf( Pubwich::_('All data is &copy; copyright %s. Proudly powered by <a class="pubwich" href="http://pubwich.org/">Pubwich</a>.'), date('Y') )?>
				</div>
			</div>
		</div>
	</body>
</html>
