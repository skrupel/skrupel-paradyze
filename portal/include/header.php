<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	  <title><?php echo $conf_meta_title; ?></title>
	  <meta name="author" content="<?php echo $conf_meta_author; ?>" />
	  <meta name="description" content="<?php echo $conf_meta_description; ?>" />
	  <meta name="keywords" content="<?php echo $conf_meta_keywords; ?>" />
	  <meta name="robots" content="index,follow" />
	  <link rel="stylesheet" type="text/css" href="styles/styles.php" />
      <link href="../inhalt/js/flexcroll/standard_grey.css" rel="stylesheet" type="text/css" />
	  <script type="text/javascript" src="../inhalt/js/shadowbox/shadowbox-base.js"></script>
	  <script type="text/javascript" src="../inhalt/js/shadowbox/shadowbox.js"></script>
	  <script type="text/javascript">
		Shadowbox.loadSkin('classic', '../inhalt/js/shadowbox/skin');
		Shadowbox.loadLanguage('en', '../inhalt/js/shadowbox/lang');
		Shadowbox.loadPlayer(['iframe', 'img', 'html', 'swf'], '../inhalt/js/shadowbox/player');
		window.onload = function(){
			Shadowbox.init();
		};
	  </script>
	  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
	  <script type="text/javascript" src="../inhalt/js/jqueryslidemenu.js"></script>
      <script type="text/javascript" src="../inhalt/js/flexcroll/flexcroll.js"></script>
  </head>
  <body scroll="auto">
<?php
    $sql = "SELECT version FROM skrupel_info";
    $version = mysql_fetch_assoc(mysql_query($sql));
?>
    <div id="container">
        <div id="header">
          <div style="float: right;">
            <script type="text/javascript"><!--
            google_ad_client = "ca-pub-0097662298562592";
            /* Banner Paradyze */
            google_ad_slot = "9905788198";
            google_ad_width = 468;
            google_ad_height = 60;
            //-->
            </script>
            <script type="text/javascript"
            src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
          </div>
          <a href="http://www.paradyze.org/portal"><img src="../bilder/logo.png" alt="<?php echo $conf_game_title; ?>" /></a>
        </div>
        <div id="header-menu" class="jqueryslidemenu">
            <ul>
            <li><a href="#">Das Spiel</a>
              <ul>
<?php if ($_SESSION['user_id']): ?>
              <li><a href="index.php">�bersicht</a></li>
              <li><a href="create.php">Spiel erstellen</a></li>
              <li><a href="waiting.php">Wartende Spiele</a></li>
              <li><a href="active.php">Aktive Spiele</a></li>
              <li><a href="finished.php">Beendete Spiele</a></li>
              <li><a href="optionen.php?fu=1">Optionen</a></li>
<?php else: ?>
              <li><a href="login.php">Login</a></li>
              <li><a href="register.php">Registrieren</a></li>
<?php endif; ?>
              </ul>
            </li>
            <li><a href="#">Community</a>
              <ul>
              <li><a href="stats.php">Statistik</a></li>
              <li><a href="feeds.php">Userfeeds</a></li>
              <li><a href="http://www.iceflame.net/blog">Newsblog</a></li>
              <li><a href="http://www.iceflame.net/forum">Forum</a></li>
              </ul>
            </li>
            <li><a href="#">Informationen</a>
              <ul>
              <li><a href="rassen.php">Die Rassen</a></li>
              <li><a href="screens.php">Screenshots</a></li>
              <li><a href="http://wiki.iceflame.net/paradyze">Hilfewiki</a></li>
              </ul>
            </li>
            </ul>
            <span style="float: right; padding: 8px 15px;">
<?php if ($_SESSION['user_id']): ?>
                <span style="font-weight: normal;">Willkommen, <?php echo $_SESSION['name']; ?>!</span> &nbsp; <a href="logout.php">Logout</a>
<?php else: ?>
                <span style="font-weight: normal;">Du bist nicht angemeldet.</span> &nbsp; <a href="login.php">Login</a>
<?php endif; ?>
            </span>
            <div style="clear: left"></div>
        </div>
        <div id="content">
