<?php
    include_once "include/global.php";

    include_once "include/header.php";

    $user = array();
    $sql = "SELECT * FROM skrupel_user";
    $query = mysql_query($sql);
    while ($data = mysql_fetch_assoc($query))
        $user[$data["id"]] = $data;
    if ($_SESSION["user_id"] == ADMIN && $_GET["remind_user"] && $_GET["remind_game"] && is_numeric($_GET["remind_user"]) && is_numeric($_GET["remind_game"])) {
        $sql = "SELECT nick, email FROM skrupel_user WHERE id = {$_GET["remind_user"]}";
        $query = mysql_query($sql);
        $userd = mysql_fetch_assoc($query);
        $sql = "SELECT name, lasttick FROM skrupel_spiele WHERE id = {$_GET["remind_game"]}";
        $query = mysql_query($sql);
        $game = mysql_fetch_assoc($query);
        $msg = "Hallo {$userd["nick"]},\n\nDu hast dich f�r das Spiel \"{$game["name"]}\" eingetragen. Das Spiel wurde schon vor einiger Zeit gestartet, aber deine Mitspieler warten immernoch darauf, dass du deinen Zug abgibst.\n\nSolltest du weiterhin Interesse an diesem Spiel haben, gib bitte m�glichst bald deinen Zug ab. Falls du nicht mehr mitspielen willst, gib wenigstens kurz bescheid, damit wir uns um einen Ersatz f�r dich k�mmern k�nnen und das Spiel nicht weiter blockiert wird.\n\n---\nDies ist eine automatisch generierte E-Mail\nBitte nicht antworten.";
        if ($conf_mail_extra_params)
            @mail($userd["email"], "$conf_game_name - Erinnerung", $msg, "From: $absenderemail\r\n" . "Reply-To: tiramon@tiramon.de\r\n" . "X-Mailer: PHP/" . phpversion(), "-f $absenderemail {$userd["email"]}");
        else
            @mail($userd["email"], "$conf_game_name - Erinnerung", $msg, "From: $absenderemail\r\n" . "Reply-To: tiramon@tiramon.de\r\n" . "X-Mailer: PHP/" . phpversion());
    }
    $sql = "SELECT * FROM skrupel_spiele WHERE phase = 0 ORDER BY lasttick DESC";
    $query = mysql_query($sql);
?>
<script>
<!--



  function fixdetails( i, xmlskrupelgame )
  {
    var temp;
    temp = goals_details[i];
    if ( i == 1 || i == 5 )
    {
      temp = temp.replace("{1}",xmlskrupelgame.getElementsByTagName('goal_info')[0].firstChild.nodeValue);
    }

    return temp;
  }
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
  goals_details[2] ="Jeder Spieler erh�lt einen Todfeind, den es zu vernichten gilt";
  goals_details[3] ="";
  goals_details[4] ="";
  goals_details[5] ="{1} KT Vormissan m&uuml;ssen im freien Raum an Board der eigenen Flotte gesichert werden";
  goals_details[6] ="Jedes Team aus 2 Spielern erh�lt 2 Todfeinde, welche es zu vernichten gilt";
-->
</script>
<script>
<!--
  function loadXMLDocument( url )
  {
    var processReqChange = function() {
      //only if req shows loadeidd
      if (req.readyState == 4) {
        //only if "OK"
        if (req.status == 200) {
          games = req.responseXML.getElementsByTagName("skrupelgame");
          text  = "<?php echo str_replace("{\$conf_root_path}", $conf_root_path, str_replace("\n", "", implode("", file("tpl/details_normal.tpl")))); ?>";
                Shadowbox.open({
                        content:    text,
                        player:     "html",
                        title:      "Details des Spiels: " + games[0].getElementsByTagName("name")[0].firstChild.nodeValue,
                        height:     500,
                        width:      750,
                        options:        {animSequence:'sync'}
                });
        } else {
          alert("There was a problem retrieving the XML data:\n" + req.StatusText);
        }
      }
    }
    
    if (window.XMLHttpRequest) {
      //branch for native XMLHttpRequest
      req = new XMLHttpRequest();
      req.onreadystatechange = processReqChange;
      req.open("GET", url, true);
      req.send(null);
    } else if (window.ActiveXObject) {
      //branch for IE/Windows ActiveX version
      isIE = true;
      req = new ActiveXObject("Microsoft.XMLHTTP");
      if (req) {
        req.onreadystatechange = processReqChange;
        req.open("GET", url, true);
        req.send();
      }
    }
  }
  function showDetails( game_id )
  {
    loadXMLDocument('xml.php?action=details_active&game_id='+ game_id);
  }
  function hideDetails()
  {
    document.getElementById("details").style.display = 'none';
  }
-->
</script>
<h1>Aktive Spiele</h1>
<table cellpadding="5" cellspacing="2" width="100%">
<?php
    if (mysql_num_rows($query) > 0) {
?>
  <tr>
    <th class="headcell" width="25%">Spielname</th>
    <th class="headcell" width="5%">Runde</th>
    <th class="headcell" width="15%">Letzte Auswertung</th>
    <th class="headcell" width="15%">N&auml;chster Autozug</th>
    <th class="headcell" colspan="3">Aktion</th>
  </tr>
<?php
        while ($data = mysql_fetch_assoc($query)) {
?>
  <tr>
    <td class="cell"><b><a href="javascript:void();" onclick="showDetails(<?=$data["id"]?>);"><?=$data["name"]?></a></b></td>
    <td class="cell" style="text-align: center;"><?=$data["runde"]?></td>
    <td class="cell" style="text-align: center;"><?=($data["lasttick"] > 0 ? strftime("%d.%m.%Y %H:%M",$data["lasttick"]) : "Bisher keine" )?></td>
    <td class="cell" style="text-align: center;"><?=($data["autozug"] > 0 ? ($data["lasttick"] > 0 ? strftime("%d.%m.%Y %H:%M",$data["lasttick"]+($data["autozug"]*60*60)) : strftime("%d.%m.%Y %T",$data["start"]+($data["autozug"]*60*60))) : "Kein Autozug" )?></td>
    <td class="cell" style="text-align: center;" width="10%"><?php
            if ($data["spieler_1"] == $_SESSION["user_id"] || $data["spieler_2"] == $_SESSION["user_id"] || $data["spieler_3"] == $_SESSION["user_id"] || $data["spieler_4"] == $_SESSION["user_id"] || $data["spieler_5"] == $_SESSION["user_id"] || $data["spieler_6"] == $_SESSION["user_id"] || $data["spieler_7"] == $_SESSION["user_id"] || $data["spieler_8"] == $_SESSION["user_id"] || $data["spieler_9"] == $_SESSION["user_id"] || $data["spieler_10"] == $_SESSION["user_id"]) {
?>
    <a href="<?=$conf_root_path?>index.php?login_f=<?=$_SESSION["name"]?>&amp;spiel_slot=<?=$data["id"]?>&amp;passwort_f=<?=$_SESSION["pass"]?>">Login</a>
<?php
                } else
                {
?>&nbsp;<?php
                }
?></td>
    <td class="cell" style="text-align: center;" width="10%"><?php
                if ($data["spieler_1"] == $_SESSION["user_id"] || in_array($_SESSION["user_id"], $conf_admin_users)) {
?><a onclick="if (confirm('Soll das Spiel wirklich gel�scht werden?')) window.location.href='delete.php?id=<?=$data["id"]?>';">L&ouml;schen</a><?php
                }
?></td>
    <td class="cell" style="text-align: center;" width="10%">
      <a href="#game_<?=$data["id"]?>" rel="shadowbox[members];width=750;height=500;options={animSequence:'sync'}" title="Mitspielerliste: <?=$data["name"]?>">Mitspieler</a>
      <div id="game_<?=$data["id"]?>" style="display:none;">
         <table style="text-align: center;" cellpadding="5" cellspacing="2" width="100%">
         <tr>
           <td class="cell" style="text-align: center;" width="10%"><b>Platz</b></td>
           <td class="cell" style="text-align: center;" width="30%"><b>Spieler</b></td>
           <td class="cell" style="text-align: center;" width="30%"><b>Rasse</b></td>
           <td class="cell" style="text-align: center;" width="30%"><b>Zug</b></td>
         </tr>
         <?php
                for ($i = 1; $i <= 10; $i++) {
                    if ($data["spieler_{$i}"] > 0) {
?>  <tr>
           <td class="cell" style="text-align: center;"><?=$data["spieler_{$i}_platz"]?></td>
           <td class="cell" style="text-align: center;"><?=$user[$data["spieler_{$i}"]]["nick"]?></td>
           <td class="cell" style="text-align: center;"><?=$data["spieler_{$i}_rassename"]?></td>
           <td class="cell" style="text-align: center;"><?=($data["spieler_{$i}_zug"] == 1 ? "Zug abgegeben" : "&nbsp;" )?></td>
         </tr>
         <?php
                    }
                }
?>
         </table>
      </div>
    </td>
  </tr>
<?php
            }
        } else {
?>
  <tr>
    <td colspan="8" style="text-align: center;">Es gibt keine aktiven Spiele!</td>
  </tr>
<?php
        }
?>
</table>
<?php
        require "inc/footer.php";
?>
