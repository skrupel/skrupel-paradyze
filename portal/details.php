<?php
include_once "include/global.php";

switch ($_GET['action']) {
    case 'active':
        $sql = "SELECT spiele.*, user1.nick as spieler_1_name FROM skrupel_spiele as spiele LEFT JOIN skrupel_user as user1 on spiele.spieler_1 = user1.id WHERE spiele.id = {$_GET["game_id"]}";
        $query = mysql_query($sql);
        $data = mysql_fetch_assoc($query);
?>
<center>
<table style='width: 95%;'>
  <tr>
    <td colspan='2'><p><h3>Allgemein</h3></p></td>
  </tr>
  <tr>
    <td width='40%'>Spielname:</td>
    <td width='60%'><?php echo htmlentities($data["name"]); ?></td>
  </tr>
  <tr>
    <td>Spielziel:</td>
    <td>" + goals[games[0].getElementsByTagName("goal")[0].firstChild.nodeValue] + " [" + fixdetails( games[0].getElementsByTagName("goal")[0].firstChild.nodeValue, games[0] ) + "]</td>
  </tr>
  <tr>
    <td>Spieler scheidet aus:</td>
    <td>" + (
             games[0].getElementsByTagName("out")[0].firstChild.nodeValue == 3
             ? "Verlust des Heimatplaneten"
             : (
                games[0].getElementsByTagName("out")[0].firstChild.nodeValue == 2
                ? "Verlust aller Basen"
                : (
                   games[0].getElementsByTagName("out")[0].firstChild.nodeValue == 1
                   ? "Verlust aller Kolonien"
                   : "Verlust aller Kolonien<br>und kompletter Flotte"
                  )
               )
            ) + "</td>
  </tr>
  <tr>
    <td>Module:</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2'><p><h3>Galaxie</h3></p></td>
  </tr>
  <tr>
    <td>Gr�&szlig;e</td>
    <td>" + games[0].getElementsByTagName("umfang")[0].firstChild.nodeValue + "x" + games[0].getElementsByTagName("umfang")[0].firstChild.nodeValue + " Lichtjahre</td>    
  </tr>
  <tr> 
    <td colspan='2'><p><h3>Plasmast&uuml;rme</h3></p></td>
  </tr>
  <tr>   
    <td>Maximal gleichzeitig</td> 
    <td>" + games[0].getElementsByTagName("max")[0].firstChild.nodeValue + "</td>
  </tr>
  <tr>
    <td>Wahrscheinlichkeit des Auftretens</td>
    <td>" + games[0].getElementsByTagName("wahr")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Anzahl Runden</td>
    <td>" + games[0].getElementsByTagName("lang")[0].firstChild.nodeValue + "</td>
  </tr>
  <tr>
    <td colspan='2'><p><h3>Piraten</h3></p></td>
  </tr>
  <tr> 
    <td>Wahrscheinlichkeit im Zentrum</td>
    <td>" + games[0].getElementsByTagName("pirates_center")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Wahrscheinlichkeit am Rand</td>
    <td>" + games[0].getElementsByTagName("pirates_outer")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Minimale Beute der Piraten</td>
    <td>" + games[0].getElementsByTagName("pirates_min")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Maximale Beute der Piraten</td>
    <td>" + games[0].getElementsByTagName("pirates_max")[0].firstChild.nodeValue + "%</td>
  </tr>
</table>
</center>
<?php
        break;

    default:
        break;
}

if ($_GET["action"] == "details_active" && is_numeric($_GET["game_id"])):
    header("Content-type: text/xml");
    header("Content-Encoding: UTF-8");
    echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

    $sql = "SELECT spiele.*, user1.nick as spieler_1_name FROM skrupel_spiele as spiele LEFT JOIN skrupel_user as user1 on spiele.spieler_1 = user1.id WHERE spiele.id = {$_GET["game_id"]}";
    $query = mysql_query($sql);
    $data = mysql_fetch_assoc($query);
    ?>
    <skrupelgame>
        <id><?= $_GET["game_id"] ?></id>
        <name></name>
        <goal><?= $data["ziel_id"] ?></goal>
        <goal_info><?= $data["ziel_info"] ?></goal_info>
        <out><?= utf8_encode($data["oput"] != null ? $data["oput"] : $data["out"]) ?></out>
        <user_1><?= utf8_encode($data["spieler_1"]) ?></user_1>
        <user_2><?= utf8_encode($data["spieler_2"]) ?></user_2>
        <user_3><?= utf8_encode($data["spieler_3"]) ?></user_3>
        <user_4><?= utf8_encode($data["spieler_4"]) ?></user_4>
        <user_5><?= utf8_encode($data["spieler_5"]) ?></user_5>
        <user_6><?= utf8_encode($data["spieler_6"]) ?></user_6>
        <user_7><?= utf8_encode($data["spieler_7"]) ?></user_7>
        <user_8><?= utf8_encode($data["spieler_8"]) ?></user_8>
        <user_9><?= utf8_encode($data["spieler_9"]) ?></user_9>
        <user_10><?= utf8_encode($data["spieler_10"]) ?></user_10>
        <race_1><?= utf8_encode($data["spieler_1_rassename"]) ?></race_1>
        <race_2><?= utf8_encode($data["spieler_2_rassename"]) ?></race_2>
        <race_3><?= utf8_encode($data["spieler_3_rassename"]) ?></race_3>
        <race_4><?= utf8_encode($data["spieler_4_rassename"]) ?></race_4>
        <race_5><?= utf8_encode($data["spieler_5_rassename"]) ?></race_5>
        <race_6><?= utf8_encode($data["spieler_6_rassename"]) ?></race_6>
        <race_7><?= utf8_encode($data["spieler_7_rassename"]) ?></race_7>
        <race_8><?= utf8_encode($data["spieler_8_rassename"]) ?></race_8>
        <race_9><?= utf8_encode($data["spieler_9_rassename"]) ?></race_9>
        <race_10><?= utf8_encode($data["spieler_10_rassename"]) ?></race_10>
        <admin><?= utf8_encode($data["spieler_admin"]) ?></admin>
        <max><?= $data["plasma_max"] ?></max>
        <wahr><?= $data["plasma_wahr"] ?></wahr>
        <lang><?= $data["plasma_lang"] ?></lang>
        <umfang><?= $data["umfang"] ?></umfang>
        <?php foreach (explode(':', $data["module"]) as $id => $mod): ?>
            <module<?= $id ?>><?= $mod ?></module<?= $id ?>>
        <?php endforeach; ?>
        <fogofwar><?= $data["nebel"] ?></fogofwar>
        <pirates_center><?= $data["piraten_mitte"] ?></pirates_center>
        <pirates_outer><?= $data["piraten_aussen"] ?></pirates_outer>
        <pirates_min><?= $data["piraten_min"] ?></pirates_min>
        <pirates_max><?= $data["piraten_max"] ?></pirates_max>
        <spieler_1_name><?= $data["spieler_1_name"] ?></spieler_1_name>
    </skrupelgame>
    <?php
elseif ($_GET["action"] == "details_waiting" && is_numeric($_GET["game_id"])):
    header("Content-type: text/xml");
    header("Content-Encoding: UTF-8");
    echo '<?xml version="1.0" encoding="UTF-8" ?>';

    $query = mysql_query("SELECT * FROM skrupel_waiting_games WHERE id = {$_GET["game_id"]}");
    $data = mysql_fetch_assoc($query);

    function get_racename_by_raceid($player) {
        global $data;
        switch ($data["rasse_$player"]) {
            case "borg":
                $racename = "Die Borg";
                break;
            case "eldar":
                $racename = "Eldanesh";
                break;
            case "empire":
                $racename = "Empire";
                break;
            case "erdallianz":
                $racename = "Die Erdallianz";
                break;
            case "foederation":
                $racename = "F�deration der Vereinigten Planeten";
                break;
            case "kuatoh":
                $racename = "Kuatoh";
                break;
            case "replikator":
                $racename = "Replikatoren";
                break;
            case "romulan":
                $racename = "Das Romulanische Imperium";
                break;
            case "schatten":
                $racename = "Schatten";
                break;
            case "silverstarag":
                $racename = "Silver Star AG";
                break;
            case "zylonen":
                $racename = "Zylonisches Imperium";
                break;
            case "trooper":
                $racename = "Independent Space Federation";
                break;
            default:
                $racename = "[ Vom Spieler gew�hlt ]";
                break;
        }
        return $racename;
    }

    function get_usernick_by_userid($player) {
        global $data, skrupel_user;
        switch ($data["user_$player"]) {
            case 0:
                return 0;
                break;
            case -1:
                return "[ Freier Slot ]";
                break;
            default:
                $query = mysql_query("SELECT nick FROM skrupel_user WHERE id = " . $data["user_$player"]);
                $get = mysql_fetch_row($query);
                return $get[0];
                break;
        }
    }
    ?>
    <skrupelgame>
        <id><?= $_GET["game_id"] ?></id>
        <name><?= utf8_encode($data["spiel_name"]) ?></name>
        <goal><?= utf8_encode($data["siegbedingungen"]) ?></goal>
        <goal_info_1><?= utf8_encode($data["zielinfo_1"]) ?></goal_info_1>
        <goal_info_2><?= utf8_encode($data["zielinfo_2"]) ?></goal_info_2>
        <goal_info_3><?= utf8_encode($data["zielinfo_3"]) ?></goal_info_3>
        <goal_info_4><?= utf8_encode($data["zielinfo_4"]) ?></goal_info_4>
        <goal_info_5><?= utf8_encode($data["zielinfo_5"]) ?></goal_info_5>
        <out><?= utf8_encode($data["oput"] != null ? $data["oput"] : $data["out"]) ?></out>
        <user_1><?= utf8_encode(get_usernick_by_userid(1)) ?></user_1>
        <user_2><?= utf8_encode(get_usernick_by_userid(2)) ?></user_2>
        <user_3><?= utf8_encode(get_usernick_by_userid(3)) ?></user_3>
        <user_4><?= utf8_encode(get_usernick_by_userid(4)) ?></user_4>
        <user_5><?= utf8_encode(get_usernick_by_userid(5)) ?></user_5>
        <user_6><?= utf8_encode(get_usernick_by_userid(6)) ?></user_6>
        <user_7><?= utf8_encode(get_usernick_by_userid(7)) ?></user_7>
        <user_8><?= utf8_encode(get_usernick_by_userid(8)) ?></user_8>
        <user_9><?= utf8_encode(get_usernick_by_userid(9)) ?></user_9>
        <user_10><?= utf8_encode(get_usernick_by_userid(10)) ?></user_10>
        <race_1><?= utf8_encode(get_racename_by_raceid(1)) ?></race_1>
        <race_2><?= utf8_encode(get_racename_by_raceid(2)) ?></race_2>
        <race_3><?= utf8_encode(get_racename_by_raceid(3)) ?></race_3>
        <race_4><?= utf8_encode(get_racename_by_raceid(4)) ?></race_4>
        <race_5><?= utf8_encode(get_racename_by_raceid(5)) ?></race_5>
        <race_6><?= utf8_encode(get_racename_by_raceid(6)) ?></race_6>
        <race_7><?= utf8_encode(get_racename_by_raceid(7)) ?></race_7>
        <race_8><?= utf8_encode(get_racename_by_raceid(8)) ?></race_8>
        <race_9><?= utf8_encode(get_racename_by_raceid(9)) ?></race_9>
        <race_10><?= utf8_encode(get_racename_by_raceid(10)) ?></race_10>
        <admin><?= utf8_encode($data["spieler_admin"]) ?></admin>
        <startposition><?= $data["startposition"] ?></startposition>
        <imperiumgroesse><?= $data["imperiumgroesse"] ?></imperiumgroesse>
        <geldmittel><?= $data["geldmittel"] ?></geldmittel>
        <mineralienhome><?= $data["mineralienhome"] ?></mineralienhome>
        <sternendichte><?= $data["sternendichte"] ?></sternendichte>
        <mineralien><?= $data["mineralien"] ?></mineralien>
        <species><?= $data["spezien"] ?></species>
        <max><?= $data["max"] ?></max>
        <wahr><?= $data["wahr"] ?></wahr>
        <lang><?= $data["lang"] ?></lang>
        <instabil><?= $data["instabil"] ?></instabil>
        <stabil><?= $data["stabil"] ?></stabil>
        <leminvorkommen><?= $data["leminvorkommen"] ?></leminvorkommen>
        <umfang><?= $data["umfang"] ?></umfang>
        <struktur><?= $data["struktur"] ?></struktur>
        <modul_0><?= $data["modul_0"] ?></modul_0>
        <modul_2><?= $data["modul_2"] ?></modul_2>
        <modul_3><?= $data["modul_3"] ?></modul_3>
        <team1><?= $data["team1"] ?></team1>
        <team2><?= $data["team2"] ?></team2>
        <team3><?= $data["team3"] ?></team3>
        <team4><?= $data["team4"] ?></team4>
        <team5><?= $data["team5"] ?></team5>
        <team6><?= $data["team6"] ?></team6>
        <team7><?= $data["team7"] ?></team7>
        <team8><?= $data["team8"] ?></team8>
        <team9><?= $data["team9"] ?></team9>
        <team10><?= $data["team10"] ?></team10>
        <fogofwar><?= $data["nebel"] ?></fogofwar>
        <pirates_center><?= $data["piraten_mitte"] ?></pirates_center>
        <pirates_outer><?= $data["piraten_aussen"] ?></pirates_outer>
        <pirates_min><?= $data["piraten_min"] ?></pirates_min>
        <pirates_max><?= $data["piraten_max"] ?></pirates_max>
        <playable><?= $data["playable"] ?></playable>
    </skrupelgame>
    <?php
else:
    header("HTTP/1.0 404 Not Found");
endif;
?>
