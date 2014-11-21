<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Erweitert jaeger_leicht.
 */
class jaeger_mittel extends jaeger_leicht {
	
	/**
	 * Bestimmt, welche Erkundungs-Funktion benutzt wird. Diese Funktion ist nur eine Wrapper-Funktion!
	 * Hier wird die Funktion schiffe_mittel::ermittleErkundungsZiel() verwendet.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das erkunden soll.
	 * returns: Die Rueckgabe der verwendeten Erkundungs-Funktion.
	 */
	function erkunde($schiff_id) {
		return schiffe_mittel::ermittleErkundungsZiel($schiff_id);
	}
	
	/**
	 * Prueft, ob es fuer den Jaeger sinnvoll ist, weiter Geleitschutz zu geben. Es wird ueberprueft, ob der 
	 * Jaeger ueberhaupt Geleitschutz gibt und ob das zu begleitende Schiffe nicht zu viel Schaden hat.
	 * arguments: $jaeger_id - Die Datenbank-ID des Jaegers, dessen Geleitschutz ueberprueft werden soll.
	 * returns: true, falls der Jaeger weiter Geleitschutz geben soll.
	 * 			false, sonst.
	 */
	function gibtWeiterGeleitschutz($jaeger_id) {
		$schiff_infos = @mysql_query("SELECT flug, zielid FROM skrupel_schiffe WHERE id='$jaeger_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		if($schiff_infos['flug'] != 4) return false;
		$ziel_schiff = $schiff_infos['zielid'];
		$ziel_infos = @mysql_query("SELECT schaden FROM skrupel_schiffe WHERE id='$ziel_schiff'");
		$ziel_infos = @mysql_fetch_array($ziel_infos);
		return ($ziel_infos['schaden'] <= eigenschaften::$jaeger_infos->max_jaeger_schaden);
	}
}
?>