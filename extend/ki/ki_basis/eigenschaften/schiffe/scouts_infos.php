<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zum Verhalten von Scouts.
 */
class scouts_infos {
	/**Die Untergrenze fuer die Anzahl der Waffensysteme, ab der ein Schiff als Scout gilt.*/
	var $min_scout_waffen;
	/**Die Obergrenze fuer die Anzahl der Waffensysteme, ab der ein Schiff als Scout gilt.*/
	var $max_scout_waffen;
	/**Die Untergrenze in Prozent im Vergleich zum Streckenverbrauch, ab der ein Scout tanken muss.*/
	var $min_scout_lemin_prozent;
	/**Die maximale Reichweite eines Scouts bei gegnerischen Schiffen.*/
	var $max_scout_reichweite_schiffe;
	/**Bestimmt die maximale Anzahl an Scouts.*/
	var $max_scout_anzahl;
	/**Bestimmt den maximalen Prozentsatz von Scouts im Vergleich zum Rest aller Schiffe der KI.*/
	var $max_scout_prozent;
	
	/**
	 * Konstruktor fuer scouts_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($min_scout_waffen, $max_scout_waffen, $min_scout_lemin_prozent, 
						$max_scout_reichweite_schiffe, $max_scout_anzahl, $max_scout_prozent) {
		$this->min_scout_waffen = $min_scout_waffen;
		$this->max_scout_waffen = $max_scout_waffen;
		$this->min_scout_lemin_prozent = $min_scout_lemin_prozent;
		$this->max_scout_reichweite_schiffe = $max_scout_reichweite_schiffe;
		$this->max_scout_anzahl = $max_scout_anzahl;
		$this->max_scout_prozent = $max_scout_prozent;
	}
}
?>