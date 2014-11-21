<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 */
include("../extend/ki/ki_basis/ki_basis.php");

function filterKI($gefundene_KI_spieler, $gefundene_KIs, $nick, $uid) {
	if(in_array($uid, $gefundene_KI_spieler)) 
		return array('continue'=>true, 'kis'=>$gefundene_KIs, 'ki_spieler'=>$gefundene_KI_spieler);
	$ki_daten = ki_basis::ermittleKIDaten();
	$continue = false;
	foreach($ki_daten as $ki) {
		$comp_nick = substr($nick, 0, strlen($ki['nick']));
		if($comp_nick == $ki['nick']) {
			if(!(in_array($comp_nick, $gefundene_KIs))) {
				$gefundene_KIs[] = $comp_nick;
				$gefundene_KI_spieler[] = $uid;
			}
			else $continue = true;
			break;
		}
	}
	return array('continue'=>$continue, 'kis'=>$gefundene_KIs, 'ki_spieler'=>$gefundene_KI_spieler);
}
?>