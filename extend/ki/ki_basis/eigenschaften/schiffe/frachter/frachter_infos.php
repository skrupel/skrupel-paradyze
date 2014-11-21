<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zum Verhalten von Frachtern.
 */
class frachter_infos {
	/**Die Untergrenze in Prozent im Vergleich zum Streckenverbrauch, ab der ein Frachter tanken muss.*/
	var $min_frachter_lemin_prozent;
	/**Bestimmt die minimale Frachtkapazitaet, ab der ein Schiff als Frachter gilt.*/
	var $min_frachtraum_frachter;
	/**Bestimmt die maximale Anzahl der Waffensysteme, aber der ein Schiff als Frachter gilt.*/
	var $max_frachter_waffen;
	/**Die maximale Reichweite eines Frachters. Ist ein Planet in Reichweite, auf dem eine neue 
	 * Sternenbasis gebaut werden soll, fliegt der Frachter auf jedenfall dort hin.*/
	var $max_frachter_reichweite;
	/**Bestimmte den maximalen Schaden, den ein Frachter haben darf, bevor er zur naechsten 
	 * Sternenbasis zurueckfliegt, um repariert zu werden.*/
	var $max_frachter_schaden;
	/**Bestimmt die maximale Naehe eines gegnerischen Schiffes zu einem Frachter. Wird dieser 
	 * Abstand unterschritten, flieht der Frachter.*/
	var $max_gegner_frachter_naehe;
	
	/**
	 * Konstruktor fuer frachter_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($min_frachtraum_frachter, $max_frachter_waffen, $min_frachter_lemin_prozent, 
						$max_frachter_reichweite, $max_frachter_schaden, $max_gegner_frachter_naehe) {
		$this->min_frachtraum_frachter = $min_frachtraum_frachter;
		$this->max_frachter_waffen = $max_frachter_waffen;
		$this->min_frachter_lemin_prozent = $min_frachter_lemin_prozent;
		$this->max_frachter_reichweite = $max_frachter_reichweite;
		$this->max_frachter_schaden = $max_frachter_schaden;
		$this->max_gegner_frachter_naehe = $max_gegner_frachter_naehe;
	}	
}
?>
