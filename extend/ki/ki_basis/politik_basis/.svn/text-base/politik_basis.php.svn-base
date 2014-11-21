<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Abstrakte Oberklasse fuer politisches Verhalten.
 */
abstract class politik_basis {
	
	/**
	 * Hier wird auf alle Politik-Anfragen reagiert, die der KI-Spieler erhaelt.
	 */
	abstract function verwaltePolitikAnfragen();
	
	/**
	 * Ermittelt alle politischen Anfragen von anderen Spielern an die KI und 
	 * gibt diese zurueck.
	 * returns: Alle Poltik-Anfragen an den KI-Spieler.
	 */
	function ermittlePolitikAnfragen() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$anfragen_array = array();
		$anfragen = @mysql_query("SELECT id, partei_b, art FROM skrupel_politik_anfrage 
				WHERE (spiel='$spiel_id') AND (partei_a='$comp_id')");
		while($anfrage = @mysql_fetch_row($anfragen)) {
			$anfragen_array[] = $anfrage;
		}
		return $anfragen_array;
	}
	
	/**
	 * Lehnt die politische Anfrage der uebergebenen ID ab (dh. die Anfrage wird aus der Datenbank geloescht).
	 * arguments: $anfrage_id - Die Datenbank-ID der politischen Anfrage, die abgelehnt wird.
	 */
	function lehneAnfrageAb($anfrage_id) {
		@mysql_query("DELETE FROM skrupel_politik_anfrage WHERE id='$anfrage_id'");
	}
	
	/**
	 * Nimmt die uerbergebene politische Anfrage an.
	 * arguements: $anfrage - Array, das aus der Datenbank-ID der Anfrage, dem Sender der Anfrage 
	 * 						  und der Art der Anfrage besteht.
	 */
	function nimmAnfrageAn($anfrage) {
		$neue_ID = ki_basis::ermittleNaechsteID("skrupel_politik");
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		@mysql_query("INSERT INTO skrupel_politik (id, partei_a, partei_b, status, optionen, spiel) 
			VALUES ('$neue_ID', '$anfrage[1]', '$comp_id', '$anfrage[2]', '0', '$spiel_id')");
		@mysql_query("DELETE FROM skrupel_politik_anfrage WHERE id='$anfrage[0]'");
	}
		
	/**
	 * Ermittelt die politischen Verhaeltnisse zu allen anderen Spielern im aktuellen Spiel und gibt diese zurueck.
	 * returns: Alle politischen Verhaeltnisse zu den anderen Spielern.
	 */
	function ermittlePolitikStatus() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$politik_array = array();
		$politik_status = @mysql_query("SELECT partei_a, status FROM skrupel_politik WHERE (spiel='$spiel_id') 
				AND (partei_b='$comp_id')");
		while($status = @mysql_fetch_row($politik_status)) {
			$politik_array[] = $status;
		}
		$politik_status = @mysql_query("SELECT partei_b, status FROM skrupel_politik WHERE (spiel='$spiel_id') 
				AND (partei_a='$comp_id')");
		while($status = @mysql_fetch_row($politik_status)) {
			$politik_array[] = $status;
		}
		return $politik_array;
	}
	
	/**
	 * Ermittelt das politische Verhaeltnis zum uebergebenen Spieler.
	 * arguments: $andere_comp_id - Die ID des Spielers (spielintern), dessen politisches Verhaeltnis 
	 * 								zum KI-Spieler ermittelt werden soll.
	 * returns: Den Status des politischen Verhaeltnisses zum uebergebenen Spieler.
	 */
	function ermittlePolitikVerhaeltnis($andere_comp_id, $comp_id, $spiel_id) {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$politik_status = @mysql_query("SELECT status FROM skrupel_politik WHERE (spiel='$spiel_id') 
				AND (((partei_a='$andere_comp_id') AND (partei_b='$comp_id')) 
				OR ((partei_a='$comp_id') AND (partei_b='$andere_comp_id')))");
		$politik_status = @mysql_fetch_array($politik_status);
		return $politik_status['status'];
	}
}
?>
