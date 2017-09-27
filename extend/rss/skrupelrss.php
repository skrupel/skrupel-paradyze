<?php

///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
////                                                                   ////
////   RSS-Erweiterung für Skrupel ist ©2006 by JANNiS und bansa.de.   ////
//// Alle Rechte vorbehalten (<- was bedeutet dieser Satz eigentlich?) ////
////                                                                   ////
//// Verbesserungsvorschläge im Skrupelforum (http://board.skrupel.de) ////
////                     oder an skrupel@bansa.de                      ////
////                                                                   ////
////      Danke für's Downloaden und viel Spass beim Skrupeln ;-)      ////
////                                                                   ////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

if (!defined('PRDYZ_INSIDE')) die('GO AWAY!!!');

//////////////////////
// Teil 1: Globales //
//////////////////////

$rss_root = "http://" . $_SERVER['SERVER_NAME'] . substr($_SERVER['SCRIPT_NAME'],0,-25);
$rss_pubdate = date("D, d M Y H:i:s O");
$rss_name = "nobody";
//Passwort ausgeklammert weil unnötig.
$rss_gibts = 0;
$rss_user = array();

$rss_error_start = "<html><head><title>$conf_game_title: RSS-Rundenstatus [[Fehler]]</title></head><body>";
$rss_error_ende = "</body></html>";

$rss_conn = @mysql_connect("$conf_database_server","$conf_database_login","$conf_database_password");
$rss_db = @mysql_select_db("$conf_database_database",$rss_conn);

if ($rss_db) {
/*start: if ($rss_db)*/

if ($_GET["name"]) {
  $rss_name = $_GET["name"];
}
else {
  echo "$rss_error_start Kein Name angegeben. (H&auml;ng an die URL einfach ein &quot;?name=DeinSkrupelUserName&quot; an. Also: Fragezeichen-name-Gleich-SkrupelName.) $rss_error_ende";
  exit;
}

$rss_zeiger = @mysql_query("SELECT * FROM skrupel_user where nick='$rss_name'");
$rss_anz = @mysql_num_rows($rss_zeiger);
$rss_user = @mysql_fetch_array($rss_zeiger);

if ($rss_anz!=1) {
  echo "$rss_error_start Name nicht gefunden. Bitte &uuml;berpr&uuml;fe nochmal die Schreibweise. $rss_error_ende";
  exit;
}
else {
  header("Content-type:text/xml");
  echo '<?xml version="1.0" encoding="UTF-8"?>';
}

$rss_spieler_id = $rss_user["id"];
$rss_zeiger2 = @mysql_query("SELECT * FROM skrupel_spiele where (spieler_1=$rss_spieler_id or spieler_2=$rss_spieler_id or spieler_3=$rss_spieler_id or spieler_4=$rss_spieler_id or spieler_5=$rss_spieler_id or spieler_6=$rss_spieler_id or spieler_7=$rss_spieler_id or spieler_8=$rss_spieler_id or spieler_9=$rss_spieler_id or spieler_10=$rss_spieler_id)");
$rss_anz2 = @mysql_num_rows($rss_zeiger2);

/*ende: if ($rss_db)*/
}
else {
  echo "$rss_error_start Verbindung zur Datenbank konnte nicht aufgebaut werden. Versuch es sp&auml;ter nochmal oder informiere deinen Skrupeladmin. $rss_error_ende";
  exit;
}


///////////////////////
// Teil 2: RSS-Start //
///////////////////////
?>

<rss version="2.0">

  <channel>
    <title><?php echo $conf_game_title; ?> - Rundenstatus</title>
    <link><?php echo $rss_root; ?></link>
    <description>Hier sieht man den Rundenstatus aller Spiele auf '<?php echo $conf_game_title; ?>', an denen <?php echo $rss_user['nick']; ?> teilnimmt.</description>
    <language>de-de</language>
    <copyright>IceFlame.net | RSS-Extension for Skrupel, Copyright ©2006 by JANNiS and bansa.de</copyright>
    <pubDate><?php echo $rss_pubdate; ?></pubDate>
    <image>
      <url><?php echo $rss_root."bilder/logo.png"; ?></url>
      <title>Spaze</title>
      <link><?php echo $rss_root; ?></link>
    </image>

<?php

/////////////////////////
// Teil 3: Nachrichten //
/////////////////////////

while ($rss_spiele = @mysql_fetch_array($rss_zeiger2)) {
  if ($rss_spiele["spieler_1"]==$rss_user["id"]) {$rss_spieler=1;}
  if ($rss_spiele["spieler_2"]==$rss_user["id"]) {$rss_spieler=2;}
  if ($rss_spiele["spieler_3"]==$rss_user["id"]) {$rss_spieler=3;}
  if ($rss_spiele["spieler_4"]==$rss_user["id"]) {$rss_spieler=4;}
  if ($rss_spiele["spieler_5"]==$rss_user["id"]) {$rss_spieler=5;}
  if ($rss_spiele["spieler_6"]==$rss_user["id"]) {$rss_spieler=6;}
  if ($rss_spiele["spieler_7"]==$rss_user["id"]) {$rss_spieler=7;}
  if ($rss_spiele["spieler_8"]==$rss_user["id"]) {$rss_spieler=8;}
  if ($rss_spiele["spieler_9"]==$rss_user["id"]) {$rss_spieler=9;}
  if ($rss_spiele["spieler_10"]==$rss_user["id"]) {$rss_spieler=10;}
  $rss_spiel = $rss_spiele["name"];
  $rss_fehlt = "";
  $rss_fehlt_anz = 0;
  $rss_i = 1;
  while ($rss_i <= 10) {
    if ($rss_i == $rss_spieler) {}
    elseif ($rss_spiele["spieler_".$rss_i."_zug"]==0 && $rss_spiele["spieler_".$rss_i]!=0) {
      $rss_zeiger3 = @mysql_query("SELECT * FROM skrupel_user where id=".$rss_spiele["spieler_".$rss_i]);
      $rss_fehlt_user = @mysql_fetch_array($rss_zeiger3);
      $rss_fehlt_anz ++;
      $rss_fehlt .= $rss_fehlt_user["nick"].", ";
    }
    $rss_i ++;
  }
  if($rss_fehlt!="") {
    $rss_fehlt = substr($rss_fehlt,0,-2);
  }
  $rss_autotick = $rss_spiele["lasttick"] + ($rss_spiele["autozug"] * 3600);
  $rss_autotick_tag = strftime("%d.%m.%Y", ($rss_spiele["lasttick"] > 0 ? $rss_spiele["lasttick"]+($rss_spiele["autozug"]*60*60) : $rss_spiele["start"]+($rss_spiele["autozug"]*60*60)));
  $rss_autotick_zeit = strftime("%T", ($rss_spiele["lasttick"] > 0 ? $rss_spiele["lasttick"]+($rss_spiele["autozug"]*60*60) : $rss_spiele["start"]+($rss_spiele["autozug"]*60*60)));
  if ($rss_spiele["phase"]==0) {
    if ($rss_spiele["spieler_".$rss_spieler."_zug"]==0) {
/* if: Zug nicht abgeschlossen */
?>

    <item>
      <title><?php echo $rss_spiel; ?>: Zug noch nicht abgeschlossen!</title>
      <description>Im Spiel "<?php echo $rss_spiel; ?>" hast du deinen Zug noch nicht abgeschlossen<?php if($rss_fehlt_anz>=2): ?>. Dabei bist du aber nicht der einzige, ausser dir gibt es noch <?php echo $rss_fehlt_anz; ?> weitere User, welche ihren Zug noch nicht gemacht haben: <?php echo $rss_fehlt; ?>.<?php elseif($rss_fehlt_anz==1): ?>. Ausser dir fehlt er auch noch von <?php echo $rss_fehlt; ?>.<?php else: ?>, du bist der einzige, von dem er noch fehlt!<?php endif; ?><?php if($rss_spiele["autozug"]!=0): ?><?php if($rss_autotick>=time()): ?> Du hast noch Zeit bis zum Autotick am <?php echo $rss_autotick_tag; ?> um <?php echo $rss_autotick_zeit; ?> Uhr.<?php else: ?> Leider liegt die Autotick-Zeit bereits seit dem <?php echo $rss_autotick_tag; ?> um <?php echo $rss_autotick_zeit; ?> in der Vergangenheit, beim Login beginnt also bereits die neue Runde. Dein Zug ist also verloren.<?php endif; ?><?php endif; ?></description>
      <link><?php echo $rss_root; ?></link>
      <author>Skrupel Tribute-Compilation &lt;<?php echo $absenderemail; ?>&gt;</author>
    </item>

<?php
    }
    elseif ($rss_spiele["spieler_".$rss_spieler."_zug"]==1 && ($rss_autotick>=time() || $rss_spiele["autotick"]==0)) {
/* if: Zug abgeschlossen, Autotick aus oder in Zukunft */
?>

    <item>
      <title><?php echo $rss_spiel; ?>: Zug abgeschlossen!</title>
      <description>Im Spiel "<?php echo $rss_spiel; ?>" hast du deinen Zug bereits abgeschlossen<?php if($rss_fehlt_anz>=2): ?>, im Gegensatz zu <?php echo $rss_fehlt_anz; ?> anderen Usern: <?php echo $rss_fehlt; ?>. <?php else: ?>. Allerdings hat <?php echo $rss_fehlt; ?> das noch nicht gemacht.<?php endif; ?><?php if($rss_spiele["autozug"]!=0): ?> Der Autotick läuft am <?php echo $rss_autotick_tag; ?> um <?php echo $rss_autotick_zeit; ?> Uhr.<?php endif; ?></description>
      <link><?php echo $rss_root; ?></link>
      <author>Skrupel Tribute-Compilation &lt;<?php echo $absenderemail; ?>&gt;</author>
    </item>

<?php
    }
    else {
/* if: Zug abgeschlossen, Autotick an und in Vergangenheit */
?>

    <item>
      <title><?php echo $rss_spiel; ?>: Autotick aktivierbar!</title>
      <description>Im Spiel "<?php echo $rss_spiel; ?>" kannst du den Autotick auslösen. <?php if($rss_fehlt_anz>=2): ?>Damit ist dann jedoch der Zug von <?php echo $rss_fehlt_anz; ?> Usern vorloren: <?php echo $rss_fehlt; ?>.<?php else: ?>Damit ist jedoch der Zug von <?php echo $rss_fehlt; ?> verloren.<?php endif; ?>Der Autotick ist seit dem <?php echo $rss_autotick_tag; ?> um <?php echo $rss_autotick_zeit; ?> Uhr bereit zum auslösen.</description>
      <link><?php echo $rss_root; ?></link>
      <author>Skrupel Tribute-Compilation &lt;<?php echo $absenderemail; ?>&gt;</author>
    </item>
<?php
    }
  }
}

///////////////////
// Teil 4: Ende! //
///////////////////
?>

  </channel>

</rss>
