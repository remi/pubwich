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

				<div class="col1">
					<div class="boite wtf">
						<h2>Introduction</h2>
						<div> <?php 
echo SmartyPants('
							<p>
								Voici mon petit texte d’introduction amusant.
							</p>
							');
?>

						</div>
					</div>

					<div class="boite flickr" id="flickr">
						<h2><a rel="me" href="http://flickr.com/photos/<?=$this->photos->username?>/">Flick<em>r</em></a> <span>dernières photos postées</span></h2>
<?php 
						if ( !$this->photos->getData() ) { ?>
						<div class="erreur">
							L’API public de <a href="http://flickr.com/">flickr.com</a> semble éprouver des problèmes.
						</div>				
<?	
						} else {
?>
						<ul class="clearfix">
<?
							$compteur = 0;
							foreach ($this->photos->getData() as $photo) {
								$compteur++;
							?>
							<li<?=($compteur%4 == 0)?' class="derniere"':''?>><a title="<?=$photo['title']?>" href="http://www.flickr.com/photos/<?=$this->photos->username?>/<?=$photo['id']?>"><img alt="<?=$photo['title']?>" src="<?=$this->photos->getAbsoluteUrl($photo, 's')?>" width="75" height="75"></a></li>
<? } ?>
						</ul>
<?
						}
?>
					</div>

					<div class="boite lastfm" id="lastfm">
						<h2><a rel="me" href="http://last.fm/user/<?=$this->albums->username?>/">last.fm</a> <span>albums les plus écoutés durant la dernière semaine</span></h2>
<?php 
						if ( !$this->albums->getData() ) { ?>
						<div class="erreur">
							L’API public de <a href="http://last.fm/">last.fm</a> semble éprouver des problèmes.
						</div>				
<?	
						} else {
?>
						<ul>
<?php
						$classes = array('premier', 'deuxieme', 'troisieme', 'quatrieme');
							$compteur = 0;
							foreach ( $this->albums->getData() as $album ) {
								$compteur++;
								if ( $compteur > $this->albums->total ) { break; }
								$image = $this->albums->getImage($album);
							?>
							<li class="<?=$classes[$compteur-1]?>">
								<a href="<?=$album->url?>"><img height="64" width="64" src="<?=$image?>" alt=""><strong><span><?=Smartypants($album->artist)?></span> <?=Smartypants($album->name)?></strong></a>
							</li>
<?							} ?>
						</ul>
<? } ?>
					</div>
				</div>

				<div class="col2">
					<div class="boite twitter" id="twitter">
						<h2><a rel="me" href="http://twitter.com/<?=$this->etats->username?>/">Twitter</a> <span>derniers états postés</span></h2>
<?php 
						if ( !$this->etats->getData() ) { ?>
						<div class="erreur">
							L’API public de <a href="http://twitter.com/">Twitter</a> semble éprouver des problèmes.
						</div>				
<?
						} else {
?>
						<ul class="clearfix">
<?php
							foreach ( $this->etats->getData() as $etat ) {
        						$date = Pubwich::time_since( $etat->created_at );
        						$text = $this->etats->filterContent( $etat->text );
								?>
							<li class="clearfix">
								<span class="date"><a href="http://twitter.com/<?=$this->etats->username?>/statuses/<?=$etat->id?>"><?=$date?></a></span>
								<?=$text?>
							</li>
<? } ?>
						</ul>
<? } ?>
					</div>

					<div class="boite rss" id="rss-1">
						<h2><a rel="me" href="<?=$this->billets->feed_url?>">Fil RSS</a> <span>derniers billets</span></h2>
<?php 
						if ( !$this->billets->getData() ) { ?>
						<div class="erreur">
							Le fil RSS <?=$this->billets->feed_url?> semble éprouver des problèmes.
						</div>				
<?	} else { ?>
						<ul>
<?php
							$compteur = 0;
							foreach ( $this->billets->getData() as $billet ) {
								$compteur++;
								if ( $compteur > $this->billets->total ) { break; }
							?>
							<li>
							<a href="<?=$billet->link?>"><?=$billet->title?></a> <small>(<?=Pubwich::time_since( $billet->published )?>)</small>
							</li>
<? } ?>
						</ul>
<? } ?>
					</div>
				</div>

				<div class="col3">
					<div class="boite delicious" id="delicious">
						<h2><a rel="me" href="http://del.icio.us/<?=$this->liens->username?>/">del.icio.us</a> <span>derniers liens partagés</span></h2>
<?php 
						if ( !$this->liens->getData() ) { ?>
						<div class="erreur">
							L’API public de <a href="http://delicious.com/">del.icio.us</a> semble éprouver des problèmes.
						</div>				
<?	
						} else {
?>
						<ul>
<?php						foreach ( $this->liens->getData() as $lien ) { ?>
							<li><a href="<?=htmlspecialchars( $lien->link )?>"><?=$lien->title ?></a></li>
<?							} ?>
						</ul>
<?							} ?>
					</div>

					<div class="boite readernaut" id="readernaut">
						<h2><a rel="me" href="http://readernaut.com/<?=$this->livres->username?>/">Readernaut</a> <span>derniers livres ajoutés</span></h2>
<?php 
						if ( !$this->livres->getData() ) { ?>
						<div class="erreur">
							L'API public de <a href="http://readernaut.com/">Readernaut</a> semble éprouver des problèmes.
						</div>				
<?	
						} else {
?>
						<ul class="clearfix">
<?php
								$compteur = 0;
								foreach ( $this->livres->getData() as $livre ) {
									$compteur++;
									if ( $compteur > $this->livres->total ) { break; }
							?>
							<li>
								<a href="<?=$livre->book_edition->permalink?>"><img src="<?=$livre->book_edition->covers->cover_small?>" width="50" alt=""><strong><span><?=Smartypants($livre->book_edition->title)?></span> <?=Smartypants($livre->book_edition->authors->author)?></strong></a>
							</li>
<? } ?>
						</ul>
<? } ?>
					</div>
					<div class="boite youtube" id="youtube">
						<h2><a rel="me" href="http://youtube.com/user/<?=$this->videos->username?>/">Youtube</a> <span>derniers vidéos ajoutés</span></h2>
<?php 
						if ( !$this->videos->getData() ) { ?>
						<div class="erreur">
							L’API public de <a href="http://youtube.com/">Youtube</a> semble éprouver des problèmes.
						</div>				
<?	
						} else {
?>
						<ul class="clearfix">
<?php
								$compteur = 0;
								foreach ( $this->videos->getData() as $video ) {
									$compteur++;
									if ( $compteur > $this->videos->total ) { break; }
							?>
							<li>
							<? 
								$media = $video->children('http://search.yahoo.com/mrss/');
								$attrs = $media->group->thumbnail[0]->attributes();
								$img = $attrs['url'];
								$title = (string) $media->group->title;
								$url_attrs = $media->group->player->attributes();
								$url = $url_attrs['url'];
							?>
								<a href="<?=$url?>"><img src="<?=$img?>" alt="<?=htmlspecialchars($title)?>" /><strong><?=$title?></strong></a>
							</li>
<? } ?>
						</ul>
<? } ?>
					</div>
					<div class="boite credits">
						<h2>Crédits</h2>
						<p>Propulsé par <a href="http://pubwich.com/" class="pubwich"><strong>Pubwich</strong></a>, <a href="http://php.net">PHP&nbsp;5</a>, <a href="http://ca2.php.net/simplexml">SimpleXML</a>, <a href="http://phpsavant.com/yawiki/index.php?area=Savant3">Savant3</a>, <a href="http://pear.php.net/package/Cache_Lite/">Cache_Lite</a>, <a href="http://michelf.com/projets/php-smartypants/">PHP Smartypants</a> et <a href="http://michelf.com/projets/php-markdown/">PHP Markdown</a>.
					</div>
				</div>

			</div>
			<div id="footer">
				<div class="footer-inner">
					<hr>
					Toutes ces données sont &copy; copyright <?=date('Y')?>. Propulsé fièrement par <a class="pubwich" href="http://www.pubwich.com/">Pubwich <?=PUBWICH_VERSION?></a>.
				</div>
			</div>
		</div>
	</body>
</html>
