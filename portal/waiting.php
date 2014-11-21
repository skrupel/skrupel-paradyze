<?php
include_once "include/global.php";

include_once "include/header.php";

if ($_GET["action"] == "join" && is_numeric($_GET["game_id"])) {
    if ($_SESSION["user_id"]) {
        $sql = "SELECT * FROM skrupel_waiting_games WHERE id={$_GET["game_id"]}";
        $data = mysql_fetch_assoc(mysql_query($sql));
        if ($data["user_1"] != -1 && $data["user_2"] != -1 && $data["user_3"] != -1 && $data["user_4"] != -1 && $data["user_5"] != -1 &&
            $data["user_6"] != -1 && $data["user_7"] != -1 && $data["user_8"] != -1 && $data["user_9"] != -1 && $data["user_10"] != -1) {
            portal_error_message('Bei diesem Spiel ist kein Platz mehr frei.', 'waiting.php');
        } elseif ($data["user_1"] == $_SESSION["user_id"] || $data["user_2"] == $_SESSION["user_id"] || $data["user_3"] == $_SESSION["user_id"] || $data["user_4"] == $_SESSION["user_id"] ||
            $data["user_5"] == $_SESSION["user_id"] || $data["user_6"] == $_SESSION["user_id"] || $data["user_7"] == $_SESSION["user_id"] ||
            $data["user_8"] == $_SESSION["user_id"] || $data["user_9"] == $_SESSION["user_id"] || $data["user_10"] == $_SESSION["user_id"]) {
            portal_error_message('Du spielst bei diesem Spiel bereits mit.', 'waiting.php');
        } else {
            //Rassen einlesen
            $verzeichnis = $conf_root_path . "daten/";
            $handle = opendir("$verzeichnis");

            $zaehler = 0;
            while ($file = readdir($handle)) {
                if ((substr($file, 0, 1) <> '.') and (substr($file, 0, 7) <> 'bilder_') and (substr($file, strlen($file) - 4, 4) <> '.txt')) {

                    if (trim($file) == 'unknown') {
                        
                    } else {

                        $datei = $conf_root_path . 'daten/' . $file . '/daten.txt';
                        $fp = @fopen("$datei", "r");
                        if ($fp) {
                            $zaehler2 = 0;
                            while (!feof($fp)) {
                                $buffer = @fgets($fp, 4096);
                                $daten[$zaehler][$zaehler2] = $buffer;
                                $zaehler2++;
                            }
                            @fclose($fp);
                        }

                        $filename[$zaehler] = $file;

                        $zaehler++;
                    }
                }
            }
            closedir($handle);
?>
<form action="waiting.php?action=join2" method="post">
<input type="hidden" name="game_id" value="<?=$_GET["game_id"]?>">
<h1>Spiel betreten</h1>
<center>
Bitte w�hle nun deine Rasse.<br>
Sobald alle verf&uuml;gbaren Slots belegt sind, kann das Spiel gestartet werden.<br><br>
<select name="rasse">
<?php
   for ($n=0;$n<$zaehler;$n++) { ?>
     <option value="<?=$filename[$n]?>"><?=$daten[$n][0]?></option>
<? }  ?>
</select><br><br>
<input type="submit" value="Spiel betreten">
</center>
</form>
<?php
      }
    }
    else
    {
?>
<center><br><br>Du m&uuml;sst angemeldet und eingelogt sein um einem Spiel beizutreten.<br><br><a href="waiting.php">Zur&uuml;ck</a></center>
<?php
    }
  }
  elseif ( $_GET["action"] == "join2" && is_numeric($_POST["game_id"]) && $_POST["rasse"] )
  {
    if ($_SESSION["user_id"])
    {
      $sql = "SELECT * FROM {skrupel_waiting_games} WHERE id={$_POST["game_id"]}";
      $data = mysql_fetch_assoc( mysql_query($sql) );
      if ( $data["user_1"] != -1 &&
           $data["user_2"] != -1 &&
           $data["user_3"] != -1 &&
           $data["user_4"] != -1 &&
           $data["user_5"] != -1 &&
           $data["user_6"] != -1 &&
           $data["user_7"] != -1 &&
           $data["user_8"] != -1 &&
           $data["user_9"] != -1 &&
           $data["user_10"] != -1)
      {
?>
<center><br><br>Bei diesem Spiel ist kein Platz mehr frei.<br><br><a href="waiting.php">Zur&uuml;ck</a></center>
<?php
      }
      elseif ( $data["user_1"] == $_SESSION["user_id"] ||
               $data["user_2"] == $_SESSION["user_id"] ||
               $data["user_3"] == $_SESSION["user_id"] ||
               $data["user_4"] == $_SESSION["user_id"] ||
               $data["user_5"] == $_SESSION["user_id"] ||
               $data["user_6"] == $_SESSION["user_id"] ||
               $data["user_7"] == $_SESSION["user_id"] ||
               $data["user_8"] == $_SESSION["user_id"] ||
               $data["user_9"] == $_SESSION["user_id"] ||
               $data["user_10"] == $_SESSION["user_id"])
      {
?>
<center><br><br>Du spielst bei diesem Spiel bereits mit.<br><br><a href="waiting.php">Zur&uuml;ck</a></center>
<?php
      }
      else
      {
        for ($i = 1; $i <=10; $i++)
        {
          if ($data["user_$i"] == -1)
          {
            $sql = "UPDATE {skrupel_waiting_games} SET user_{$i}={$_SESSION["user_id"]}, rasse_{$i}=\"{$_POST["rasse"]}\" WHERE id = {$_POST["game_id"]}";
            mysql_query($sql);
?>
<center><br><br>Du bist erfolgreich dem Spiel beigetreten.<br><br><a href="waiting.php">Zur&uuml;ck</a></center>
<?php

            break;
          }
        }

        $sql = "SELECT * FROM {skrupel_waiting_games} WHERE id={$_POST["game_id"]}";
        $data = mysql_fetch_assoc( mysql_query($sql) );
        if ( $data["user_1"] != -1 &&
             $data["user_2"] != -1 &&
             $data["user_3"] != -1 &&
             $data["user_4"] != -1 &&
             $data["user_5"] != -1 &&
             $data["user_6"] != -1 &&
             $data["user_7"] != -1 &&
             $data["user_8"] != -1 &&
             $data["user_9"] != -1 &&
             $data["user_10"] != -1)
        {
          //Spiel ist voll
        }
      }
    }
  }
  else if ($_GET["action"] == "delete" )
  {
    $sql = "DELETE FROM skrupel_waiting_games WHERE id = {$_GET["game_id"]} AND spieler_admin = {$_SESSION["user_id"]}";
    mysql_query( $sql );
?>
<center><br><br>Spiel wurde wie gew�nscht gel�scht.<br><br><a href="waiting.php">Zur&uuml;ck</a></center>
<?php
  }
  else
  {

    $user = array();
    $sql   = "SELECT * FROM skrupel_user";
    $query = mysql_query( $sql );
    while ( $data = mysql_fetch_assoc($query) )
      $user[$data["id"]]=$data;

    $sql   = "SELECT *
              FROM ".skrupel_waiting_games."
              WHERE (
                                     (user_1 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )." OR
                                      user_2 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )." OR
                                      user_3 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )." OR
                                      user_4 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )." OR
                                      user_5 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )." OR

                                      user_6 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )." OR
                                      user_7 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )." OR
                                      user_8 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )." OR
                                      user_9 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )." OR
                                      user_10 ".( $_SESSION["user_id"] ? "in (-1, {$_SESSION["user_id"]} )" : "= -1" )."
                                     ) ". ( $_SESSION["user_id"] ? "or spieler_admin= {$_SESSION["user_id"]}" : "" ) ."
                                    )";
    $query = mysql_query( $sql );
?>
<script>
<!--
  var goals = new Array();
  goals[0] = 'Just for Fun';
  goals[1] = '&Uuml;berleben';
  goals[2] = 'Todfeind';
  goals[3] = 'Dominanz';
  goals[4] = 'King of the Planet';
  goals[5] = 'Spice';
  goals[6] = 'Team Todfeind';

  var goals_details = new Array();
  goals_details[0] ="Es wird gespielt, bis die Runde langweilig wird";
  goals_details[1] ="Spiel endet, sobald nur noch {1} Spieler existiert";
  goals_details[2] ="Jeder Spieler erh&auml;lt einen Todfeind, den es zu vernichten gilt";
  goals_details[3] ="";
  goals_details[4] ="";
  goals_details[5] ="{1}KT Vormissan m&uuml;ssen im freien Raum an Board der eigenen Flotte gesichert werden";
  goals_details[6] ="Jedes Team aus 2 Spielern erh&auml;lt 2 Todfeinde, welche es zu vernichten gilt";

  function fixdetails( i, xmlskrupelgame )
  {
    var temp;
    temp = goals_details[i];
    if ( i == 1 || i == 5 )
    {
      temp = temp.replace("{1}",xmlskrupelgame.getElementsByTagName('goal_info_'+i)[0].firstChild.nodeValue);
    }

    return temp;
  }
  function loadXMLDocument( url )
  {
    if (window.XMLHttpRequest)
    {
      //branch for native XMLHttpRequest
      req = new XMLHttpRequest();
      req.onreadystatechange = processReqChange;
      req.open("GET", url, true);
      req.send(null);
    }
    else if (window.ActiveXObject)
    {
      //branch for IE/Windows ActiveX version
      isIE = true;
      req = new ActiveXObject("Microsoft.XMLHTTP");
      if (req)
      {
        req.onreadystatechange = processReqChange;
        req.open("GET", url, true);
        req.send();
      }
    }
  }
  function processReqChange()
  {
    //only if req shows loadeidd
    if (req.readyState == 4)
    {
      //only if "OK"
      if (req.status == 200)
      {
        games = req.responseXML.getElementsByTagName("skrupelgame");
        text  = "<?php echo str_replace("{\$conf_root_path}", $conf_root_path, str_replace("\n", "", implode("", file("tpl/details_waiting.php")))); ?>";
		Shadowbox.open({
			content:    text,
			player:     "html",
			title:      "Details des Spiels: " + games[0].getElementsByTagName("name")[0].firstChild.nodeValue,
			height:     500,
			width:      750,
			options:	{animSequence:'sync'}
		});
      }
      else
      {
        alert("There was a problem retrieving the XML data:\n" + req.StatusText);
      }
    }
  }
  function showDetails( game_id )
  {
    loadXMLDocument('xml.php?action=details_waiting&game_id='+ game_id);
  }
  function hideDetails()
  {
    document.getElementById("details").style.display = 'none';
  }
-->
</script>
<h1>Wartende Spiele</h1>
<table style="text-align: center;" cellpadding="5" cellspacing="2" width="100%">
    <tr>
      <th class="headcell" width="30%">Spielname</th>
      <th class="headcell" width="20%">Spielziel</th>
      <th class="headcell" width="20%">Administrator</th>
      <th class="headcell" colspan="3">Aktion</th>
    </tr>
<?php
    if ( $data = mysql_fetch_assoc( $query ) )
    {
    do
    {
?>
  <tr>
      <td class="cell"><b><a href="javascript:showDetails(<?=$data["id"]?>)"><?=$data["spiel_name"]?></a></b></td>
      <td class="cell"><?php
          if ($data["siegbedingungen"] == 0 )
            echo "Just for Fun";   //es wird gespielt, bis die Runde langweilig wird
          elseif ($data["siegbedingungen"] == 1 )
            echo "&Uuml;berleben";    //Spiel endet, sobald nur noch {$data["zielinfo_1"]} Spieler existieren
          elseif ($data["siegbedingungen"] == 2 )
            echo "Todfeind";     //jeder Spieler erh&auml;lt einen Todfeind, den es zu vernichten gilt
          elseif ($data["siegbedingungen"] == 3 )
            echo "Dominanz";
          elseif ($data["siegbedingungen"] == 4 )
            echo "King of the Planet";
          elseif ($data["siegbedingungen"] == 5 )
            echo "Spice";        //{$data["zielinfo_5"]} KT Vomisaan m&uuml;ssen im freien Raum an Bord der eigenen Flotte gesichert werden
          elseif ($data["siegbedingungen"] == 6 )
            echo "Teamtodfeind"; //jedes Team aus 2 Spielern erh&auml;lt 2 Todfeinde, welche es zu vernichten gilt
          ?></td>
      <td class="cell"><?=$user[$data["spieler_admin"]]["nick"]?></td>
<?php
      if ( $data["user_1"] != -1 &&
           $data["user_2"] != -1 &&
           $data["user_3"] != -1 &&
           $data["user_4"] != -1 &&
           $data["user_5"] != -1 &&
           $data["user_6"] != -1 &&
           $data["user_7"] != -1 &&
           $data["user_8"] != -1 &&
           $data["user_9"] != -1 &&
           $data["user_10"] != -1 )
      {
        if ( in_array( $_SESSION["user_id"], array( $data["user_1"],
                                                    $data["user_2"],
                                                    $data["user_3"],
                                                    $data["user_4"],
                                                    $data["user_5"],
                                                    $data["user_6"],
                                                    $data["user_7"],
                                                    $data["user_8"],
                                                    $data["user_9"],
                                                    $data["user_10"] ) ) )
        {
           ?><td class="cell" style="text-align: center;" width="10%"><a href="start.php?action=prepare&game_id=<?=$data["id"]?>">Spiel starten</a></td><?php
        }
				else
        {
          ?><td class="cell" style="text-align: center;" width="10%">&nbsp;</td><?php
        }
/*
        if ($data["spieler_admin"] == $_SESSION["user_id"])
        {
?>      <td><a href="start.php?action=prepare&game_id=<?=$data["id"]?>"><b>Spiel starten</b></a></td>
<?php
        }
        else
        {
?>      <td colspan="2"><b>Spiel wartet darauf gestartet zu werden</b></td>
<?php
        }
*/
      }
      else
      {
        if ($data["spieler_admin"] == $_SESSION["user_id"])
        {
          ?><td class="cell" style="text-align: center;" width="10%"><a href="start.php?action=prepare&game_id=<?=$data["id"]?>">Starten</a></td><?php
        }
				else
        {
          ?><td class="cell" style="text-align: center;" width="10%">&nbsp;</td><?php
        }
      }
      if ($data["spieler_admin"] == $_SESSION["user_id"])
      {
?>      <td class="cell" style="text-align: center;" width="10%"><a href="waiting.php?action=delete&game_id=<?=$data["id"]?>">L&ouml;schen</a></td>
<?php
      } else {
?>      <td class="cell" style="text-align: center;" width="10%"><a href="waiting.php?action=delete&game_id=<?=$data["id"]?>">&nbsp;</a></td>
<?php
      }
?>
      <td class="cell" style="text-align: center;" width="10%"><a href="javascript:showDetails(<?=$data["id"]?>)">Mitspieler</a></td>
    </tr>
<?php
    } while ($data = mysql_fetch_assoc( $query ));
    }
    else
    {
?>  <tr>
     <td class="cell" style="text-align: center; padding: 20px;" colspan="6">Zur Zeit sind keine wartenden Spiele verf&uuml;gbar.<br /><br />Du kannst aber gerne selbst ein <a href="spiel_alpha.php">neues Spiel erstellen</a>.</td>
   </tr>
<?php
    }
?>
</table>
<?php
  }
  include_once "include/footer.php";
?>
