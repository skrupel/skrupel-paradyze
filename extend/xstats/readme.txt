A Installation der Skrupel XStats
-------------------------------
A1. Entpacken
A2. Tabelle anlegen
A3. Aenderungen im Skrupel Code


A1. Entpacken
-------------
*Zip entpacken nach skrupel/extend/xstats, folgende Dateistuktur muss nach dem Entpacken existieren:
/skrupel
/skrupel/admin
/skrupel/bilder
/skrupel/daten
/skrupel/extend
/skrupel/extend/xstats
/skrupel/extend/xstats/amcharts-php <dir>
/skrupel/extend/xstats/phplot <dir>
/skrupel/extend/xstats/DisplaySingleGame.php
/skrupel/extend/xstats/DisplaySingleGameUtil.php
/skrupel/extend/xstats/graph.php
/skrupel/extend/xstats/index.php
/skrupel/extend/xstats/readme.txt
/skrupel/extend/xstats/xstatsCollect.php
/skrupel/extend/xstats/xstatsDeleteGame.php
/skrupel/extend/xstats/xstatsInitialize.php
/skrupel/extend/xstats/xstatsUtil.php


A2. Tabelle anlegen
------------------
Zum Browser wechseln und einmal die URL 
http://<host>/extend/xstats/xstatsInitialize.php
ausfuehren. Es gibt eine positive Meldung, dass alles angelegt wurde:
"Skrupel XStats initialized successfully."

Wenn man die URL mehrfach ausfuehrt, ist das kein Problem, weil er dann sagt, dass die Tabelle schon existiert:
"Skrupel XStats already initialized - no action performed."


A3.Aenderungen im Skrupel Code
-----------------------------
Es muessen einige Zeilen in verschiedene Skrupel Dateien eingefuegt werden.

----------------------------------
*Datei skrupel/inhalt/inc.host_orbitalkampf.php
* Zeile "xstats_shipVSPlanet( $shid, $besitzer, $p_id, $p_besitzer, 1);"
* einfuegen, fuer den Kampf Schiff gegen Planet

Dateiausschnitt nach der Modifikation:

if ($schaden>=100) {
    xstats_shipVSPlanet( $spiel, $shid, $besitzer, $kox, $koy, $p_id, $p_besitzer, 1);
    $zeiger_temp = mysql_query("DELETE FROM skrupel_schiffe where id=$shid and besitzer=$besitzer;");
    $zeiger_temp = mysql_query("DELETE FROM skrupel_anomalien where art=3 and extra like 's:$shid:%'");


----------------------------------
*Datei skrupel/admin/spiel_gamma.php:
*Zeile "include("../extend/xstats/xstatsDeleteGame.php");" einfuegen. Hiermit
*wird sichergestellt, dass die Statistikdaten geloescht werden, wenn das Spiel
*vom Administrator geloescht wird

Dateiausschnitt nach der Modifikation:

include ("inc.header.php");
if (($ftploginname==$admin_login) and ($ftploginpass==$admin_pass)) {

    $spiel = $_GET["slot_id"];
    if (@intval(substr($spiel_extend,1,1))==1){
	    include("../extend/ki/ki_basis/spielLoeschenKI.php");
    }
    include("../extend/xstats/xstatsDeleteGame.php");
    $zeiger = @mysql_query("DELETE FROM skrupel_planeten WHERE spiel=$spiel");
    $zeiger = @mysql_query("DELETE FROM skrupel_sternenbasen WHERE spiel=$spiel");
    $zeiger = @mysql_query("DELETE FROM skrupel_schiffe WHERE spiel=$spiel");
    $zeiger = @mysql_query("DELETE FROM skrupel_huellen WHERE spiel=$spiel");
----------------------------------
*Datei skrupel/inhalt/inc.host.php:
*Zeile "include("../extend/xstats/xstatsCollect.php");" am Anfang einfuegen

Dateiausschnitt nach der Modifikation:
<?php
include ('inc.host_func.php');

if ($main_verzeichnis!='../') $main_verzeichnis='';
define('DATADIR', $main_verzeichnis.'daten/');
define('INCLUDEDIR', $main_verzeichnis.'inhalt/');
define('LANGUAGEDIR', $main_verzeichnis.'lang/');

if (@intval(substr($spiel_extend,1,1))==1) {
    include("../extend/ki/ki_basis/berechneKI.php");
}
include("../extend/xstats/xstatsCollect.php");
///////////////////////////////Sprachinclude(nur die benoetigten) Anfang
$zeiger = mysql_query("SELECT * FROM skrupel_spiele WHERE id=$spiel");
$sprachtemp_1 = mysql_fetch_array($zeiger);


----------------------------------
*Datei skrupel/inhalt/inc.host.php:
*Zeile "xstats_collectAndStore( $sid, &$stat_schlacht,&$stat_schlacht_sieg,&$stat_kol_erobert,&$stat_lichtjahre);" einfuegen
*Hiermit werden die Statistikdaten nach jedem Zug gesammelt

Dateiausschnitt nach der Modifikation:

///////////////////////////////////////////////////////////////////////////////////////////////ZIEL ENDE
///////////////////////////////////////////////////////////////////////////////////////////////STATS AUSWERTUNG ANFANG

for ($m=1;$m<11;$m++) {
    if ($spieler_id_c[$m]>=1) {$zeiger = mysql_query("UPDATE skrupel_user set stat_sieg=stat_sieg+$stat_sieg[$m],stat_schlacht=stat_schlacht+$stat_schlacht[$m],stat_schlacht_sieg=stat_schlacht_sieg+$stat_schlacht_sieg[$m],stat_kol_erobert=stat_kol_erobert+$stat_kol_erobert[$m],stat_lichtjahre=stat_lichtjahre+$stat_lichtjahre[$m],stat_monate=stat_monate+1 where id=$spieler_id_c[$m]"); }
}
xstats_collectAndStore( $sid, &$stat_schlacht,&$stat_schlacht_sieg,&$stat_kol_erobert,&$stat_lichtjahre);
///////////////////////////////////////////////////////////////////////////////////////////////STATS AUSWERTUNG ENDE
/////////////////////////////////////////////////////////////////////////////////////////////// BENACHRICHTIGUNG ANFANG
----------------------------------


B Anzeige der Stats
--------------------
Eine Liste der Spiele kann man unter der URL
http://<host>/extend/xstats/index.php
sehen. Es werden dort alle Spiele angezeigt, die Statistiken und Uebersichten sieht man aber
nur fuer beendete.
