<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Wird nur bei installierter, aktiver KI ausgefuehrt. Es wird zunaechst ueberprueft, ob alle 
 * menschlichen Spieler ihren Zug beendet haben, damit die KI ihren Zug berechnen kann. Ist dies 
 * der Fall, so wird fuer jeden KI-Spieler im aktuellen Spiel ein KI-Objekt erstellt, welches dann 
 * den Zug des jeweiligen Spielers berechnet. 
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
		if($ki['nick'] == $nick) {
			$comp_id = ki_basis::ermittleCompID($id, $sid);
			$spalte = "spieler_".$comp_id."_zug";
			$spieler_zug_c[$comp_id] = 1;
			@mysql_query("UPDATE skrupel_spiele SET $spalte=1 WHERE sid='$sid'");
		}
	}
}

?>
