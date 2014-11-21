<?php
    include_once 'inc.conf.php';
    include_once PRDYZ_DIR_INCLUDE.'/inc.hilfsfunktionen.php';
    
    if (array_key_exists('sprache', $_GET) && preg_match('/[abc]+/', $_GET['sprache']) && is_dir("lang/" . $_GET['sprache'])) {
        include("lang/" . $_GET['sprache'] . "/lang.index.php");
    } else {
        include("lang/" . $language . "/lang.index.php");
    }
    
    if (!db_connect()) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	  <title><?php echo $conf_game_title; ?> Client</title>
	  <meta name="author" content="<?php echo $conf_meta_author; ?>">
	  <meta name="robots" content="index">
	  <meta name="keywords" content="<?php echo $conf_meta_keywords; ?>">
	  <meta http-equiv="imagetoolbar" content="no">
	</head>
	<body text="#000000" scroll="no" bgcolor="#000000" background="<?php echo $bildpfad; ?>/hintergrund.gif" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	  <center>
		<table border="0" height="100%" cellspacing="0" cellpadding="0">
		  <tr><td style="font-family:Verdana;font-size:10px;color:#ffffff;"><nobr><?php echo $lang['index']['fehler']; ?></nobr></td></tr>
		</table>
	  </center>
	</body>
</html>
<?php
        die();
    }
    
    compressed_output();
    $zeiger = @mysql_query("SELECT version, extend, serial FROM skrupel_info");
    $array = @mysql_fetch_array($zeiger);
    $spiel_version = $array['version'];
    $spiel_extend = $array['extend'];
    $spiel_serial = $array['serial'];
    $spieler = 0;
    
    if (!preg_match('/Skrupel/i', getEnv("HTTP_USER_AGENT"))) {
        $bildpfad = 'bilder';
    }
    if (($tmp = str_get('pic_path')) !== FALSE) {
        $bildpfad = $tmp;
    } elseif (($tmp = str_post('pic_path')) !== FALSE) {
        $bildpfad = $tmp;
    }
    $login_f = mysql_real_escape_string($_REQUEST['login_f']);
    $pass_f = mysql_real_escape_string($_REQUEST['passwort_f']);
    $spiel_slot = intval($_REQUEST['spiel_slot']);
    
    if (($hash_f = str_get('hash')) !== FALSE) {
        $zeiger = @mysql_query("SELECT * FROM skrupel_spiele WHERE
  spieler_1_hash = '$hash_f' or
  spieler_2_hash = '$hash_f' or
  spieler_3_hash = '$hash_f' or
  spieler_4_hash = '$hash_f' or
  spieler_5_hash = '$hash_f' or
  spieler_6_hash = '$hash_f' or
  spieler_7_hash = '$hash_f' or
  spieler_8_hash = '$hash_f' or
  spieler_9_hash = '$hash_f' or
  spieler_10_hash = '$hash_f'");
        if (@mysql_num_rows($zeiger) == 1) {
            $array = @mysql_fetch_array($zeiger);
            $spiel_slot = $array['id'];
            for ($m = 1; $m <= 10; $m++) {
                $tmpstr = 'spieler_' . $m;
                if ($array[$tmpstr . '_hash'] == $hash_f) {
                    $benutzer_id = $array[$tmpstr];
                    $zeiger = @mysql_query("SELECT nick,passwort FROM skrupel_user where id=$benutzer_id");
                    $array = @mysql_fetch_array($zeiger);
                    $login_f = $array['nick'];
                    $pass_f = $array['passwort'];
                    break;
                }
            }
        }
    }
    
    $fehler = "";
    if ((strlen($login_f) >= 1) and (strlen($pass_f) >= 1)) {
        $zeiger = @mysql_query("SELECT * FROM skrupel_user WHERE nick='$login_f' and passwort='$pass_f'");
        if (@mysql_num_rows($zeiger) == 1) {
            $array = @mysql_fetch_array($zeiger);
            $spieler_id = $array['id'];
            $spieler_name = $array['nick'];
            $_GET["sprache"] = $array['sprache'];
            if ($_GET["sprache"] == "") {
                $_GET["sprache"] = $language;
            }
            $zeiger2 = @mysql_query("SELECT * FROM skrupel_spiele WHERE (spieler_1=$spieler_id or spieler_2=$spieler_id or spieler_3=$spieler_id or spieler_4=$spieler_id or spieler_5=$spieler_id or spieler_6=$spieler_id or spieler_7=$spieler_id or spieler_8=$spieler_id or spieler_9=$spieler_id or spieler_10=$spieler_id) and id=$spiel_slot");
            if (@mysql_num_rows($zeiger2) == 1) {
                $array2 = @mysql_fetch_array($zeiger2);
                $sid = $array2['sid'];
                $phase = $array2['phase'];
                $spiel = $array2['id'];
                for ($sp = 1; $sp <= 10; $sp++) {
                    if ($spieler_id == $array2['spieler_' . $sp]) {
                        $spieler = $sp;
                    }
                }
                $uid = random_string();
                @mysql_query("UPDATE skrupel_user SET uid='$uid' WHERE id=$spieler_id;");
            } else {
                $fehler = $lang['index']['spielnichtfuerdich'];
            }
        } else {
            $fehler = $lang['index']['falscheZugangsdaten'];
        }
    }
    
    if ($spieler > 0 || in_array($_SESSION["user_id"], $conf_admin_users)) {
        if ($phase == 1) {
            header("Location: inhalt/runde_ende.php?fu=1&spiel=$spiel&bildpfad=$bildpfad&sprache=" . $_GET["sprache"]);
            exit;
        }
        
        $zeiger_temp = @mysql_query("SELECT * FROM skrupel_spiele WHERE phase=0 AND id=$spiel_slot ORDER BY id;");
        $array22 = @mysql_fetch_array($zeiger_temp);
        $autozug = $array22['autozug'];
        $nebel = $array22['nebel'];
        $spiel = $array22['id'];
        $module = @explode(':', $array22['module']);
        $start = $array22['start'];
        $lasttick = $array22['lasttick'];
        $spieleranzahl = $array22['spieleranzahl'];
        $ziel_id = $array22['ziel_id'];
        $ziel_info = $array22['ziel_info'];
        $aktuell = time();
        $spieler_1 = $array22["spieler_1"];
        $spieler_2 = $array22["spieler_2"];
        $spieler_3 = $array22["spieler_3"];
        $spieler_4 = $array22["spieler_4"];
        $spieler_5 = $array22["spieler_5"];
        $spieler_6 = $array22["spieler_6"];
        $spieler_7 = $array22["spieler_7"];
        $spieler_8 = $array22["spieler_8"];
        $spieler_9 = $array22["spieler_9"];
        $spieler_10 = $array22["spieler_10"];
        for ($sp = 1; $sp <= 10; $sp++) {
            $tmpstr = 'spieler_' . $sp;
            $spieler_id_c[$sp] = $array22[$tmpstr];
            $spieler_ziel_c[$sp] = $array22[$tmpstr . '_ziel'];
            $spieler_rasse_c[$sp] = $array22[$tmpstr . '_rasse'];
            $spieler_raus_c[$sp] = $array22[$tmpstr . '_raus'];
        }
        $plasma_wahr = $array22['plasma_wahr'];
        $plasma_max = $array22['plasma_max'];
        $plasma_lang = $array22['plasma_lang'];
        $piraten_mitte = $array22['piraten_mitte'];
        $piraten_aussen = $array22['piraten_aussen'];
        $piraten_min = $array22['piraten_min'];
        $piraten_max = $array22['piraten_max'];
        $spiel_name = $array22['name'];
        $nebel = $array22['nebel'];
        $runde = $array22['runde'];
        $spieleranzahl = $array22['spieleranzahl'];
        $umfang = $array22['umfang'];
        $aufloesung = $array22['aufloesung'];
        $spiel_out = $array22['oput'];
        if ($autozug > 0 and $runde > 1) {
            $lasttick = ($lasttick > 0 ? $lasttick : $start);
            $interval = 3600 * $autozug;
            if ($aktuell >= $lasttick + $interval) {
                $lasttick = time();
                $zeiger = mysql_query("UPDATE skrupel_spiele SET lasttick='$lasttick' WHERE id=$spiel;");
                $main_verzeichnis = '';
                include('inhalt/inc.host.php');
                $zeiger = mysql_query("UPDATE skrupel_spiele set spieler_1_zug=0,spieler_2_zug=0,spieler_3_zug=0,spieler_4_zug=0,spieler_5_zug=0,spieler_6_zug=0,spieler_7_zug=0,spieler_8_zug=0,spieler_9_zug=0,spieler_10_zug=0 where id=$spiel;");
            }
        }
        $nachricht = "$spieler_name hat das Spiel betreten.";
        $zeiger = @mysql_query("INSERT INTO skrupel_chat (spiel,datum,text,an,von,farbe) values ($spiel_slot,'$aktuell','$nachricht',0,'System','000000');");
        if ($bildpfad == 'bilder') {
            $bildpfad = '../bilder';
        }
        $zeiger = @mysql_query("UPDATE skrupel_user set bildpfad='$bildpfad' where id=$spieler_id;");
    
        @mysql_close();
        
        if ($_GET['spielbrett'] == 1) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
        "http://www.w3.org/TR/html4/frameset.dtd">
    <html>
      <head>
          <title><?php echo $conf_game_title; ?> Client</title>
          <meta name="author" content="<?php echo $conf_meta_author; ?>">
          <meta name="robots" content="index">
          <meta name="keywords" content="<?php echo $conf_meta_keywords; ?>">
          <meta http-equiv="imagetoolbar" content="no">
      </head>
      <frameset framespacing="0" border="false" frameborder="0" rows="41,*,13,107,10">
        <frameset framespacing="0" border="false" frameborder="0" cols="348,*,402">
          <frame name="obenlinks" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=1&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          <frame name="obenmitte" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=2&user=<?php echo $_GET['login_f']; ?>&bildpfad=<?php echo $bildpfad; ?>&uid=<?php echo $uid; ?>&sid=<?php echo $sid; ?>&sprache=<?php echo $_GET["sprache"]; ?>" target="_self">
          <frame name="obenrechts" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=3&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
        </frameset>
        <frameset framespacing="0" border="false" frameborder="0" cols="57,*,7">
          <frameset framespacing="0" border="false" frameborder="0" rows="339,*,40">
            <frame name="mittelinksoben" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/menu.php?fu=1&uid=<?php echo $uid; ?>&sid=<?php echo $sid; ?>&sprache=<?php echo $_GET["sprache"]; ?>" target="_self">
            <frame name="mittelinksmitte" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=4&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
            <frame name="mittelinksunten" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=5&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          </frameset>
          <frame name="mittemitte" scrolling="auto" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=100&query=uebersicht_uebersicht.php?fu=1&uid=<?php echo $uid; ?>&sid=<?php echo $sid; ?>&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          <frameset framespacing="0" border="false" frameborder="0" rows="233,*,146">
            <frame name="mitterechtsoben" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=7&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
            <frame name="mitterechtssmitte" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=8&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
            <frame name="mitterechtsunten" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=9&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          </frameset>
        </frameset>
        <frameset framespacing="0" border="false" frameborder="0" cols="387,*,364">
          <frame name="mitte2links" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=10&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          <frame name="mitte2mitte" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=11&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          <frame name="mitte2mitte" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=12&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
        </frameset>
        <frameset framespacing="0" border="false" frameborder="0" cols="56,*,19">
          <frame name="untenlinks" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/menu.php?fu=2&uid=<?php echo $uid; ?>&sid=<?php echo $sid; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          <frame name="untenmitte" scrolling="auto" marginwidth="0" marginheight="0" noresize src="inhalt/uebersicht.php?fu=1&uid=<?php echo $uid; ?>&sid=<?php echo $sid; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          <frame name="untenrechts" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=15&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
        </frameset>
        <frameset framespacing="0" border="false" frameborder="0" cols="389,*,361">
          <frame name="unten2links" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=16&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          <frame name="unten2mitte" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=17&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
          <frame name="unten2rechts" scrolling="no" marginwidth="0" marginheight="0" noresize src="inhalt/aufbau.php?fu=18&bildpfad=<?php echo $bildpfad; ?>&sprache=<?php echo $_GET['sprache']; ?>" target="_self">
        </frameset>
      </frameset>
    </html>
<?php
        } else {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
	  <title><?php echo $conf_game_title; ?> Client</title>
	  <meta name="author" content="<?php echo $conf_meta_author; ?>">
	  <meta name="keywords" content="<?php echo $conf_meta_keywords; ?>">
	  <meta name="robots" content="index">
	  <meta http-equiv="imagetoolbar" content="no">
	  <link rel="stylesheet" href="inhalt/js/mediabox/css/black.css" type="text/css" media="screen" />
	  <script src="inhalt/js/mediabox/mootools.js" type="text/javascript"></script>
	  <script src="inhalt/js/mediabox/mediaboxadv.js" type="text/javascript"></script>
	  <script type="text/javascript">
		function hilfe(hid) {
			Mediabox.open('inhalt/hilfe.php?fu2=' + hid + '&uid=<?php echo $uid; ?>&sid=<?php echo $sid; ?>&sprache=<?php echo $_GET["sprache"] ?>', '', '520 220');
		}
		function update_spielbrett() {
			if (typeof(window.innerWidth) == 'number') {
				// Non-IE
				width = window.innerWidth;
				height = window.innerHeight;
			} else if (document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight)) {
				// IE 6+ in 'standards compliant mode'
				width = document.documentElement.clientWidth;
				height = document.documentElement.clientHeight;
			} else if (document.body && ( document.body.clientWidth || document.body.clientHeight)) {
				// IE 4 compatible
				width = document.body.clientWidth;
				height = document.body.clientHeight;
			}
			document.getElementById('spielbrett').height = height;
			document.getElementById('spielbrett').width = width;
			document.getElementById('loading').style.display = 'none';
			document.getElementById('spielbrett').style.display = 'inline';
		}
		window.onload = update_spielbrett;
		window.onresize = update_spielbrett;
	  </script>
  </head>
  <body style="padding: 0; margin: 0; height: 100%; width: 100%; background: #000 url(bilder/splash<?php echo mt_rand(1, 4); ?>.jpg) top center;">
	  <div style="position: absolute; bottom: 25%; width: 100%;" id="loading">
		<p style="text-align: center;"><img src="bilder/loading.gif" alt="" /></p>
	  </div>
	  <iframe src="<?php echo $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '&spielbrett=1'; ?>" width="100%" marginheight="0" marginwidth="0" frameborder="0" id="spielbrett" style="display: none;" />
  </body>
</html>
<?php
        }
    } else {
        header("Location: portal/");
    }