<?php
    include_once "include/global.php";
    if (!$_SESSION["user_id"]) {
      header("Location: login.php");
      die();
    }
    include_once "include/header.php";

    $query = mysql_query("SELECT * FROM skrupel_spiele WHERE `id` = " . $_GET['id'] . " AND `spieler_1` = " . $_SESSION["user_id"] . " LIMIT 1");
    if (mysql_num_rows($query) == 1 or in_array($_SESSION["user_id"], $conf_admin_users)) {
		$error = 0;
		$spiel = $_GET["id"];
		if ($conf_enable_ai == true) {
			include("../extend/ki/ki_basis/spielLoeschenKI.php");
		}
		if(!mysql_query("DELETE FROM skrupel_spiele WHERE `id` = " . $_GET['id'] . " LIMIT 1")) $error++;
		if(!mysql_query("DELETE FROM skrupel_anomalien WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_begegnung WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_chat WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_forum_beitrag WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_forum_thema WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_huellen WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_kampf WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_konplaene WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_nebel WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_neuigkeiten WHERE `spiel_id` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_ordner WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_planeten WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_politik WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_politik_anfrage WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_scan WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_schiffe WHERE `spiel` = " . $_GET['id'])) $error++;
		if(!mysql_query("DELETE FROM skrupel_sternenbasen WHERE `spiel` = " . $_GET['id'])) $error++;
        if ($error == 0) {
			echo '<center><br><br>Das Spiel wurde gel&ouml;scht.<br><br><a href="javascript:history.back()">Zur&uuml;ck</a><br><br></center>';
		} else {
			echo '<center><br><br>Das Spiel konnte nicht gel&ouml;scht werden.<br><br><a href="javascript:history.back()">Zur&uuml;ck</a><br><br></center>';
		}
    } else {
        echo '<center><br><br>Das Spiel ist entweder nicht vorhanden oder es geh&ouml;rt dir nicht.<br><br><a href="javascript:history.back()">Zur&uuml;ck</a><br><br></center>';
    }

    require "inc/footer.php";
?>
