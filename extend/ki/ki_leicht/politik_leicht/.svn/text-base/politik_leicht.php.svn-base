<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Impementierung von politik_basis.
 */
class politik_leicht extends politik_basis {
	
	/**
	 * Ueberprueft, ob ein anderer Spieler im aktuellen Spiel staerker ist oder nicht. Es wird einfach 
	 * gecheckt, ob der aktuelle Platz hoeher ist oder nicht.
	 * return: true, falls der Spieler einen hoeheren Platz hat, 
	 * 		   false, sonst.
	 */
	function spielerIstStaerker($comp_id) {
		$spiel_id = eigenschaften::$spiel_id;
		$platz = ki_basis::ermittleRangVonSpieler($comp_id, "platz", $spiel_id);
		$eigener_platz = ki_basis::ermittleRangVonSpieler(eigenschaften::$comp_id, "platz", $spiel_id);
		return $platz > $eigener_platz;
	}
	
	/**
	 * Hier wird auf alle Politik-Anfragen reagiert, die der KI-Spieler erhaelt. Anfragen weren in dieser 
	 * Implementierung nur angenommen, wenn der andere Spieler staerker ist (siehe $this->spielerIstStaerker()) 
	 * und wenn es mindestens einen Spieler gibt, mit dem kein Buendnis besteht.
	 */
	function verwaltePolitikAnfragen() {
		$anfragen = $this->ermittlePolitikAnfragen();
		$nur_ein_gegner = count(eigenschaften::$gegner_ids) == 1;
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$eigener_platz = ki_basis::ermittleRangVonSpieler($comp_id, "platz", $spiel_id);
		foreach($anfragen as $anfrage) {
			//Ist nur ein Gegner da, so wird keinerlei Buendnis mit ihm eingegangen.
			if($nur_ein_gegner) {
				$this->lehneAnfrageAb($anfrage[0]);
				continue;
			}
			$sender = $anfrage[1];
			$art = $anfrage[2];
			$sender_platz = ki_basis::ermittleRangVonSpieler($sender, "platz", $spiel_id);
			//Ist  der Spieler momentan schwaecher als die KI selbst, ist er fuer Politik uninteressant.
			if(!($this->spielerIstStaerker($sender))) $this->lehneAnfrageAb($anfrage[0]);
			else {
				$politik_status = $this->ermittlePolitikStatus();
				//Ist nur noch ein Spieler uebrig, mit dem kein Buendnis besteht, wird abgelehnt.
				if(count($politik_status) == count(eigenschaften::$gegner_ids) - 1) 
					$this->lehneAnfrageAb($anfrage[0]);
				else $this->nimmAnfrageAn($anfrage);
			}
		}
	}
}
?>
