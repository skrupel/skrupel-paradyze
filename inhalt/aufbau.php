<?php
include ("../inc.conf.php");
if (!$_GET["bildpfad"]) {$_GET["bildpfad"]='../bilder';}
if(!$_GET["sprache"]){$_GET["sprache"]=$language;}
$conn = @mysql_connect($conf_database_server, $conf_database_login, $conf_database_password);
$db = @mysql_select_db($conf_database_database, $conn);
$zeiger = @mysql_query("SELECT optionen FROM skrupel_user WHERE nick = '" . $_GET['user'] . "' LIMIT 1");
$array = @mysql_fetch_array($zeiger);
$spieler_optionen = $array['optionen'];
if ($_GET["fu"]==0) {
include ("../inc.conf.php");
?>
    <html>
        <head>
			<title><?=$conf_game_title?></title>
			<meta name="author" content="<?=$conf_meta_author?>">
			<meta name="keywords" content="<?=$conf_meta_keywords?>">
			<meta name="robots" content="index">
        </head>
        <body text="#000000" bgcolor="#000000" background="<?php echo $_GET["bildpfad"]; ?>/hintergrund.gif" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
        </body>
    </html>
<?php }
if (($_GET["fu"]>=1) and ($_GET["fu"]<=99)) {
    if ($_GET["fu"]==8) {
        $url="http://".$SERVER_NAME.$SCRIPT_NAME;
        $url=substr($url,0,strlen($url)-18);
?>
        <html>
            <head>
				<title><?=$conf_game_title?></title>
				<meta name="author" content="<?=$conf_meta_author?>">
				<meta name="keywords" content="<?=$conf_meta_keywords?>">
				<meta name="robots" content="index">
            </head>
            <body text="#000000" bgcolor="#afafaf" <?php if (!$ping_off==1) { ?>background="http://www.skrupel.de/ping.php?url=<?php echo $url; ?>"<?php } ?> link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
            </body>
        </html>
<?php
	} elseif ($_GET["fu"]==2 and intval(substr($spieler_optionen,17,1))==0) {
?>
        <html>
            <head>
				<title><?=$conf_game_title?></title>
				<meta name="author" content="<?=$conf_meta_author?>">
				<meta name="keywords" content="<?=$conf_meta_keywords?>">
				<meta name="robots" content="index">
				<link rel="stylesheet" href="player/nonverblaster.css" type="text/css" media="screen" charset="utf-8" />
				<script src="player/swfobject.js" type="text/javascript" charset="utf-8"></script>
				<script src="player/nonverblaster.js" type="text/javascript" charset="utf-8"></script>
				<script type="text/javascript">
					function hilfe(hid) {
						window.top.Mediabox.open('inhalt/hilfe.php?fu2='+hid+'&uid=<?php echo $_GET['uid']; ?>&sid=<?php echo $_GET['sid'];?>&sprache=<?php echo $_GET["sprache"]?>', '', '520 220');
					}
				</script>
            </head>
            <body text="#000000" bgcolor="#444444" background="<?php echo $_GET["bildpfad"]; ?>/aufbau/<?php echo $_GET["fu"]; ?>.gif" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="10px" marginwidth="0" marginheight="0" style="margin-top: 10px;">
				<a href="javascript:hilfe(66);" style="float: right;"><img src="<?php echo $_GET["bildpfad"]; ?>/icons/hilfe.gif" border="0" width="17" height="17"></a>
				<div id="audioPlayer" name="audioPlayer">
					To listen to this song, you need the latest <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank">Flash-Player</a> and active javascript in your browser.
				</div>
				<script type="text/javascript">
					var flashvars = {};
						flashvars.mediaURL = "music/Time_Traveller.mp3";
						flashvars.allowSmoothing = "true";
						flashvars.autoPlay = "true";
						flashvars.buffer = "6";
						flashvars.showTimecode = "true";
						flashvars.controlColor = "0xffffff";
						flashvars.controlBackColor = "0x000000";
						flashvars.scaleIfFullScreen = "true";
						flashvars.defaultVolume = "100";
						flashvars.showScalingButton = "true";
					var params = {};
						params.menu = "true";
						params.allowFullScreen = "false";
						params.allowScriptAccess = "always";
						params.wmode = "transparent";
					var attributes = {};
						attributes.id = "audioPlayer";
						attributes.name = "audioPlayer";
					swfobject.embedSWF("player/NonverBlaster.swf", "audioPlayer", "95%", "17", "9", "player/expressinstall.swf", flashvars, params, attributes);
					registerForJavaScriptCommunication("audioPlayer");
					var music = new Array("music/Time_Traveller.mp3", "music/Dark_Fallout.mp3");
					var musicID = 0;
					adjustPlaylistButtons();
					function nextMusic(){
						musicID ++;
						if(musicID == music.length){
							musicID = 0;
						}
						playMusic(musicID);
					}
					function playMusic($musicID){
						musicID = $musicID;
						sendToNonverBlaster("switch:" + music[musicID]);
						adjustPlaylistButtons();
					}
					function computeEnd(){
						nextMusic();
					}
				</script>
            </body>
        </html>
    <?php } else { ?>
        <html>
            <head>
				<title><?=$conf_game_title?></title>
				<meta name="author" content="<?=$conf_meta_author?>">
				<meta name="keywords" content="<?=$conf_meta_keywords?>">
				<meta name="robots" content="index">
            </head>
            <body text="#000000" bgcolor="#444444" background="<?php echo $_GET["bildpfad"]; ?>/aufbau/<?php echo $_GET["fu"]; ?>.gif" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
            </body>
        </html>
    <?php } ?>
	<script src="js/swfobject.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/nonverblaster.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="nonverblaster.css" type="text/css" media="screen" charset="utf-8" />
<?php }
if ($_GET["fu"]==100) {
    ?>
<html>
	<head>
      <title><?=$conf_game_title?></title>
      <meta name="author" content="<?=$conf_meta_author?>">
      <meta name="keywords" content="<?=$conf_meta_keywords?>">
      <meta name="robots" content="index">
      <style type="text/css">
        body, p, td {
            font-family: Verdana;
            font-size: <?php echo 10-$plus; ?>px;
            color: #ffffff;
            scrollbar-darkshadow-color: #444444;
            scrollbar-3dlight-color: #444444;
            scrollbar-track-color: #444444;
            scrollbar-face-color: #555555;
            scrollbar-shadow-color: #222222;
            scrollbar-highlight-color: #888888;
            scrollbar-arrow-color: #555555;
        }
        td.weissklein {
            font-family: Verdana;
            font-size: <?php echo 10-$plus; ?>px;
            color: #ffffff;
        }
        td.weissgross {
            font-family: Verdana;
            font-size: <?php echo 12-$plus; ?>px;
            color: #ffffff;
        }
        a {
            color: #aaaaaa;
            font-weight: normal;
            text-decoration: none;
        }
        a:hover {
            font-weight: normal;
            text-decoration: underline;
            color: #ffffff;
        }
        input,select {
            background-color: #555555;
            color: #ffffff;
            border-bottom-color: #222222;
            border-left-color: #888888;
            border-right-color: #222222;
            border-top-color: #888888;
            Border-style: solid;
            Border-width: 1px;
            font-family: Verdana;
            font-size: <?php echo 10-$plus; ?>px;
        }
        input.nofunc {
            background-color: #555555;
            color: #777777;
            border-bottom-color: #222222;
            border-left-color: #888888;
            border-right-color: #222222;
            border-top-color: #888888;
            Border-style: solid;
            Border-width: 1px;
            font-family: Verdana;
            font-size: <?php echo 10-$plus; ?>px;
        }
        input.eingabe {
            background-color: #555555;
            color: #ffffff;
            border-bottom-color: #888888;
            border-left-color: #222222;
            border-right-color: #888888;
            border-top-color: #222222;
            Border-style: solid;
            Border-width: 1px;
            font-family: Verdana;
            font-size: <?php echo 10-$plus; ?>px;
        }
        textarea {
            background-color: #555555;
            color: #ffffff;
            border-bottom-color: #888888;
            border-left-color: #222222;
            border-right-color: #888888;
            border-top-color: #222222;
            Border-style: solid;
            Border-width: 1px;
            font-family: Verdana;
            font-size: <?php echo 10-$plus; ?>px;
        }
      </style>
    <frameset framespacing="0" border="false" frameborder="0" rows="50,18,*,16,50">
        <frame name="rahmenk" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=0&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">

        <frameset framespacing="0" border="false" frameborder="0" cols="60,114,*,114,60">
            <frame name="rahmen0" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=0&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            <frame name="rahmen1" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=19&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            <frame name="rahmen2" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=20&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            <frame name="rahmen3" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=21&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            <frame name="rahmen4" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=0&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
        </frameset>

        <frameset framespacing="0" border="false" frameborder="0" cols="60,18,*,18,60">
            <frame name="rahmen10" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=0&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            
            <frameset framespacing="0" border="false" frameborder="0" rows="80,*,92">
                <frame name="rahmen15" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=25&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
                <frame name="rahmen16" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=26&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
                <frame name="rahmen17" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=27&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            </frameset>

          <frame name="rahmen12" scrolling="auto" marginwidth="0" marginheight="0" noresize src="<?php echo substr($_SERVER['QUERY_STRING'],13,strlen($_SERVER['QUERY_STRING'])-13); ?>" target="_self">

            <frameset framespacing="0" border="false" frameborder="0" rows="80,*,92">
                <frame name="rahmen18" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=28&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
                <frame name="rahmen19" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=29&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
                <frame name="rahmen20" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=30&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            </frameset>

            <frame name="rahmen14" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=0&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
        </frameset>

        <frameset framespacing="0" border="false" frameborder="0" cols="60,114,*,114,60">
            <frame name="rahmen5" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=0&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            <frame name="rahmen6" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=22&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            <frame name="rahmen7" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=23&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            <frame name="rahmen8" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=24&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
            <frame name="rahmen9" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=0&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
        </frameset>

        <frame name="rahmenk" scrolling="no" marginwidth="0" marginheight="0" noresize src="aufbau.php?fu=0&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']?>" target="_self">
    </frameset>

    <noframes>
    <body>
    </body>
<?php } ?>
