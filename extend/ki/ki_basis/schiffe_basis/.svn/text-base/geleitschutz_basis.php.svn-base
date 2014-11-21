<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Abstrakte Oberklasse fuer Geleitschutz-Verhalten im allgemeinen.
 */
abstract class geleitschutz_basis extends schiffe_basis {
	
	/**
	 * Abstrakte Funktion fuer das Verwalten von Geleitschutze.
	 */
	abstract function verwalteGeleitschutz();
	
	/**
	 * Prueft, ob das uebergebene Schiff einem anderen Schiff Begleitschutz gibt.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Begleitschutz ueberprueft werden soll.
	 * returns: true, falls das Schiff einem anderen Schiff Begleitschutz gibt.
	 * 			false, sonst.
	 */
	function gibtBegleitschutz($schiff_id) {
		$schiff_infos = @mysql_query("SELECT flug, zielid FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		return ($schiff_infos['flug'] == 4 && $schiff_infos['zielid'] != 0);
	}
	
	/**
	 * Ermittelt alle Schiff-IDs, die dem uebergebenen Schiff Begleitschutz geben.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Begleit-Schiffe ermittelt werden sollen.
	 * returns: Alle Begleit-Schiffe als Array aus Datenbank-IDs.
	 */
	function ermittleBegleitSchiffe($schiff_id) {
		$comp_id = eigenschaften::$comp_id;
		$spiel_id = eigenschaften::$spiel_id;
		$schiffe_db = @mysql_query("SELECT id FROM skrupel_schiffe WHERE (spiel='$spiel_id') AND (flug=4) 
				AND (besitzer='$comp_id') AND (zielid='$schiff_id')");
		$begleit_schiffe = array();
		while($schiff = @mysql_fetch_array($schiffe_db)) {
			$begleit_schiffe[] = $schiff['id'];
		}
		return $begleit_schiffe;
	}
	
	/**
	 * Prueft, ob das uebergebene Schiff dem anderen uebergebenen Schiff Begleitschutz geben kann (bzw. ob es 
	 * Sinn machen wuerde).
	 * arguments: $schiff_gibt - Die Datenbank-ID des Schiffs, das dem anderen Schiff Begleitschutz geben soll.
	 * 			  $schiff_bekommt - Die Datenbank-ID des Schiffs, das Begleitschutz erhalten soll.
	 * returns: true, falls Begleitschutz sinnvoll ist.
	 * 			false, sonst.
	 */
	function kannBegleitschutzGeben($schiff_gibt, $schiff_bekommt) {
		$schiff_infos = @mysql_query("SELECT techlevel FROM skrupel_schiffe WHERE id='$schiff_bekommt'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$tech_level = $schiff_infos['techlevel'];
		$max_unterstuetzung = $this->ermittleMaxWaffenAnzahl($schiff_bekommt);
		$begleiter_anzahl = count($this->ermittleBegleitSchiffe($schiff_bekommt));
		if($begleiter_anzahl >= $max_unterstuetzung) return false;
		$schiff_infos = @mysql_query("SELECT techlevel FROM skrupel_schiffe WHERE id='$schiff_gibt'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$tech_level_begleit = $schiff_infos['techlevel'];
		$max_unterstuetzung_begleit = $this->ermittleMaxWaffenAnzahl($schiff_gibt);
		return ($tech_level_begleit >= $tech_level - 2 && $max_unterstuetzung_begleit >= 
				$max_unterstuetzung / 2);
	}
	
	/**
	 * Ermittelt das staerkste Schiff von den uebergebenen Schiff-IDs und gibt dies zurueck. Die Kriterien sind 
	 * die maximale Anzahl an Waffen sowie der Tech-Level und die Masse.
	 * arguments: $schiff_ids - Ein Array aus Datenbank-IDs von Schiffen.
	 * returns: Die Datenbank-ID des staerksten Schiffs aus dem Array.
	 */
	function ermittleStaerkstesSchiff($schiff_ids) {
		$max_waffen = 0;
		$max_techlevel = 0;
		$max_masse = 0;
		$staerkstes_schiff = null;
		foreach($schiff_ids as $schiff_id) {
			$schiff_infos = @mysql_query("SELECT techlevel, masse, lemin FROM skrupel_schiffe WHERE 
				id='$schiff_id'");
			$schiff_infos = @mysql_fetch_array($schiff_infos);
			$techlevel = $schiff_infos['techlevel'];
			$masse = $schiff_infos['masse'];
			$lemin = $schiff_infos['lemin'];
			if($lemin <= eigenschaften::$geleitschutz_infos->min_lemin_geleit_geben) continue;
			$waffen = $this->ermittleMaxWaffenAnzahl($schiff_id);
			if($waffen > $max_waffen || ($waffen == $max_waffen && $techlevel > $max_techlevel) 
			|| ($waffen == $max_waffen && $techlevel == $max_techlevel && $masse > $max_masse)) {
				$max_waffen = $waffen;
				$max_techlevel = $techlevel;
				$max_masse = $masse;
				$staerkstes_schiff = $schiff_id;
			}
		}
		return $staerkstes_schiff;
	}
	
	/**
	 * Aktiviert den Begleitschutz des uebergebenen Schiffs fuer das andere uebergebene Schiff.
	 * arguments: $schiff_gibt - Die Datenbank-ID des Schiffs, das dem anderen Schiff Begleitschutz geben soll.
	 * 			  $schiff_bekommt - Die Datenbank-ID des Schiffs, das Begleitschutz erhalten soll.
	 */
	function begleite($schiff_gibt, $schiff_bekommt) {
		$koords = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_bekommt'");
		$koords = @mysql_fetch_array($koords);
		$x = $koords['kox'];
		$y = $koords['koy'];
		$warp = $this->ermittleMaximumWarp($schiff_gibt);
		@mysql_query("UPDATE skrupel_schiffe SET flug=4, warp='$warp', zielx='$x', ziely='$y', 
			zielid='$schiff_bekommt' WHERE id='$schiff_gibt'");
	}
	
	/**
	 * Ermittelt alle Schiff-IDs, die dem uebergebenen Schiff Begleitschutz geben, und welche noch nicht 
	 * an der Position des uebergebenen Schiffs sind.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Begleit-Schiffe ermittelt werden sollen.
	 * returns: Alle kommenden Begleit-Schiffe als Array aus Datenbank-IDs.
	 */
	function ermittleKommendeBegleitSchiffe($schiff_id) {
		$koords = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$koords = @mysql_fetch_array($koords);
		$x_pos = $koords['kox'];
		$y_pos = $koords['koy'];
		$min_lemin = eigenschaften::$geleitschutz_infos->min_lemin_warten;
		$schiffe_db = @mysql_query("SELECT id FROM skrupel_schiffe WHERE (flug=4) AND (zielid='$schiff_id') 
			AND (NOT ((kox='$x_pos') AND (koy='$y_pos'))) AND (lemin>'$min_lemin')");
		$begleit_schiffe = array();
		while($schiff = @mysql_fetch_array($schiffe_db)) {
			$begleit_schiffe[] = $schiff['id'];
		}
		return $begleit_schiffe;
	}
}
?>
