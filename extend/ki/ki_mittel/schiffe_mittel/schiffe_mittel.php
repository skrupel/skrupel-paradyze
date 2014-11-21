<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Enthaelt Funktionen, die fuer ki_mittel wichtig sind. Ausserdem werden einige Funktionen aus 
 * schiffe_basis ueberschrieben.
 */
abstract class schiffe_mittel extends schiffe_basis {
	
	/**
	 * Ermittelt aus den uebergebenen Koordinaten die erstbesten, die nicht in der Naehe von den uebergebenen 
	 * schlechten Planeten sind und gibt diese zurueck.
	 * arguments: $koordinaten - Die Koordinaten auf der Karte, von denen die erstbesten zurueckgeben werden.
	 * 			  $schlechte_planeten - Die Koordinaten von Planeten, die gemieden werden sollen.
	 * returns: Ein Array aus den X- und Y-Koordinaten einer guten Stelle auf der Karte.
	 */
	static function ermittleBesteKoordinaten($koordinaten, $schlechte_planeten) {
		$wenigste_planeten = 9999;
		$bester_sektor = null;
		foreach($koordinaten as $koords) {
			$anzahl_schlechter_planeten = 0;
			foreach($schlechte_planeten as $planet) {
				$distanz = floor(ki_basis::berechneStrecke($koords['x'], $koords['y'], 
														   $planet['x'], $planet['y']));
				if($distanz <= eigenschaften::$schiffe_infos->sektor_erkundung_groesse * 2) 
					$anzahl_schlechter_planeten++;
			}
			if($wenigste_planeten > $anzahl_schlechter_planeten) {
				$wenigste_planeten = $anzahl_schlechter_planeten;
				$bester_sektor = $koords;
			}
		}
		return $bester_sektor;
	}
	
	/**
	 * Diese Funktion ermittelt einen Planeten fuer ein Schiff, zu den er fliegen soll. Zuerst wird ueberprueft, 
	 * ob es ein (noch nicht von anderen eigenen Schiffen angeflogenes) Wurmloch gibt, was nocht nicht erkunden 
	 * wurde. Danach werden Planeten angeflogen, die nicht sichtbar sind und die genug entfernt von sichtbaren  
	 * Planeten sind. Gibt es keine nicht-sichtbaren Planeten, die weit genug von sichtbaren weg sind, wird ein 
	 * Planet genommen, der weit genug von den eigenen Planeten entfernt ist.
	 * arguments: $schiff_id - Die Datenbank-ID des Jaegers, der erkunden soll.
	 * returns: Ein Array aus X- und Y-Koordinate sowie der Datenbank-ID des Ziels.
	 * 			null, falls kein Ziel-Planet gefunden wurde.
	 */
	static function ermittleErkundungsZiel($schiff_id) {
		//Zuerst wird versucht, ein sichtbares Wurmloch zu erkunden. Es wird nur erkundet, falls es noch 
		//unbekannt ist oder bekannt ist, dass es instabil ist.
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$schiff_koords = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_koords = @mysql_fetch_array($schiff_koords);
		$x = $schiff_koords['kox'];
		$y = $schiff_koords['koy'];
		foreach(eigenschaften::$gesehene_wurmloecher_daten as $wurmloch_daten) {
			if(in_array($wurmloch_daten, eigenschaften::$bekannte_wurmloch_daten) 
			|| in_array($wurmloch_daten, eigenschaften::$bekannte_instabile_wurmloch_daten)) continue;
			//Falls das Wurmloch zu weit entfernt ist, wird es nicht angeflogen.
			$wurmloch_x = $wurmloch_daten['x'];
			$wurmloch_y = $wurmloch_daten['y'];
			$entfernung = floor(ki_basis::berechneStrecke($x, $y, $wurmloch_x, $wurmloch_y));
			if($entfernung > eigenschaften::$schiffe_infos->max_wurmloch_erkundungs_reichweite) continue;
			$schiffe_zum_wurmloch = @mysql_query("SELECT sum(id) FROM skrupel_schiffe 
				WHERE (besitzer='$comp_id') AND (spiel='$spiel_id') AND (zielx='$wurmloch_x') 
				AND (ziely='$wurmloch_y]')");
			$schiffe_zum_wurmloch = @mysql_fetch_array($schiffe_zum_wurmloch);
			if($schiffe_zum_wurmloch['sum(zielid)'] == null || $schiffe_zum_wurmloch['sum(zielid)'] == 0) 
				return array('x'=>$wurmloch_daten['x'], 'y'=>$wurmloch_daten['y'], 0);
		}
		//Ansonsten wird ein Ziel festgelegt, dass weiter weg ist.
		$spiel_umfang = @mysql_query("SELECT umfang FROM skrupel_spiele WHERE id='$spiel_id'");
		$spiel_umfang = @mysql_fetch_array($spiel_umfang);
		$spiel_umfang = $spiel_umfang['umfang'];
		$sektor_anzahl = floor($spiel_umfang / eigenschaften::$schiffe_infos->sektor_erkundung_groesse);
		$sektor_anzahl = $sektor_anzahl * $sektor_anzahl;
		$koordinaten = array();
		$x = eigenschaften::$schiffe_infos->sektor_erkundung_groesse / 2;
		$y = $x;
		for($i=1; $i <= $sektor_anzahl; $i++) {
			if($x + eigenschaften::$schiffe_infos->sektor_erkundung_groesse >= $spiel_umfang) {
				$x = eigenschaften::$schiffe_infos->sektor_erkundung_groesse / 2;
				$y += eigenschaften::$schiffe_infos->sektor_erkundung_groesse;
			}
			if($y > $spiel_umfang) break;
			$sektor_koords = array('x'=>$x, 'y'=>$y);
			$koordinaten[] = $sektor_koords;
			$x += eigenschaften::$schiffe_infos->sektor_erkundung_groesse;
		}
		shuffle($koordinaten);
		$nicht_sichtbare_planeten = @mysql_query("SELECT COUNT(id) FROM skrupel_planeten WHERE 
			(spiel='$spiel_id') AND (sicht_".$comp_id."=0) AND (sicht_".$comp_id."_beta=0)");
		$nicht_sichtbare_planeten = @mysql_fetch_array($nicht_sichtbare_planeten);
		$nicht_sichtbare_planeten = $nicht_sichtbare_planeten['COUNT(id)'];
		if($nicht_sichtbare_planeten != 0) {
			$sichtbare_planeten = array();
			$planeten_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE (spiel='$spiel_id') 
				AND ((sicht_".$comp_id."=1) OR (sicht_".$comp_id."_beta=1))");
			while($koords = @mysql_fetch_array($planeten_koords)) {
				$sichtbare_planeten[] = array('x'=>$koords['x_pos'], 'y'=>$koords['y_pos']);
			}
			$ziel = self::ermittleBesteKoordinaten($koordinaten, $sichtbare_planeten);
			if($ziel != null) array('x'=>$ziel['x'], 'y'=>$ziel['y'], 0);
		}
		$planeten_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE (spiel='$spiel_id') 
			AND (besitzer='$comp_id')");
		$eigene_planeten = array();
		while($koords = @mysql_fetch_array($planeten_koords)) {
			$eigene_planeten[] = array('x'=>$koords['x_pos'], 'y'=>$koords['y_pos']);
		}
		$ziel = self::ermittleBesteKoordinaten($koordinaten, $eigene_planeten);
		return array('x'=>$ziel['x'], 'y'=>$ziel['y'], 0);
	}
}
?>
