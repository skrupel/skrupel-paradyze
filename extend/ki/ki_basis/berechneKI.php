<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Hier werden die Zuege der KI-Spieler berechnet. Sind alle menschlichen Spieler aus 
 * dem Spiel ausgeschieden, wird das Spiel beendet.
 */

include("../extend/ki/ki_basis/ki_basis.php");
$computerSpieler = ki_basis::ermittleComputerSpieler($sid);
$ki_daten = ki_basis::ermittleKIDaten();
foreach($computerSpieler as $id) {
	$comp_nick = @mysql_query("SELECT nick FROM skrupel_user WHERE id='$id'");
	$comp_nick = @mysql_fetch_array($comp_nick);
	$comp_nick = $comp_nick['nick'];
	foreach($ki_daten as $ki) {
		$nick = substr($comp_nick, 0, strlen($ki['nick']));
		if($ki['nick'] == $nick) include("../extend/ki/".$ki['ordner']."/init.php");
	}
}

?>
