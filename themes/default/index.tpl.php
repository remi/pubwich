<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Mon site propulsé par Pubwich</title>
		<link rel="stylesheet" media="screen" href="<?=Pubwich::getThemeUrl()?>/style.css" type="text/css">
		
	</head>
	<body>
		<div id="wrap">
			<h1><a href="/" rel="me">Mon site propulsé par Pubwich</a></h1>
			<hr>
			<div class="clearfix">

			<?=Pubwich::getLoop()?>

			</div>
			<div id="footer">
				<div class="footer-inner">
					<hr>
					Toutes ces données sont &copy; copyright <?=date('Y')?>. Propulsé fièrement par <a class="pubwich" href="http://www.pubwich.com/">Pubwich</a>.
				</div>
			</div>
		</div>
	</body>
</html>
