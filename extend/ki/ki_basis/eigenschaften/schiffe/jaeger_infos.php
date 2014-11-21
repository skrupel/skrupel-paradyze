<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zum Verhalten von Jaegern.
 */
class jaeger_infos {
	/**Die Untergrenze fuer die Anzahl der Waffensysteme, ab der ein Schiff als Jaeger gilt.*/
	var $min_jaeger_waffen;
	/**Die Untergrenze in Prozent im Vergleich zum Streckenverbrauch, ab der ein Jaeger tanken muss.*/
	var $min_jaeger_lemin_prozent;
	/**Bestimmt die minimale Menge an Lemin, die ien Jaeger zu Beginn seiner Reise tanken muss.*/
	var $min_lemin_jaeger_tank_start;
	/**Die maximale Reichweite eines Jaegers bei gegnerischen Schiffen.*/
	var $max_jaeger_reichweite_schiffe;
	/**Die maximale Reichweite eines Jaegers bei gegnerischen Planeten.*/
	var $max_jaeger_reichweite_planeten;
	/**Die maximale Reichweite eines Jaegers bei angreifenden Schiffen.*/
	var $max_jaeger_reichweite_angriff;
	/**Bestimmte den maximalen Schaden, den ein Jaeger haben darf, bevor er zur naechsten 
	 * Sternenbasis zurueckfliegt, um repariert zu werden.*/
	var $max_jaeger_schaden;
	/**Bestimmt die minimale Anzahl an aktuell im Jaeger vorhandenen Projektilen fuer den Bau eines Minenfeldes*/
	var $min_projektile_minen_legen;
	/**Bestimmt den minimalen Abstand von Feinden, sodass ein Jaeger ein Minenfeld legen kann.*/
	var $min_feind_abstand_minen_legen;
	/**Bestimmt den maximalen Abstand von Feinden, sodass ein Jaeger ein Minenfeld legen kann.*/
	var $max_feind_abstand_minen_legen;
	/**Bestimmt die mindest-Anzahl an Kolonisten, die auf einem Feind-Planeten sein muessen, damit dieser per 
	 * Destabilisator vernichtet wird.*/
	var $min_kolonisten_destabilisator;
	/**Bestimmt den Prozent-Satz der Wahrscheinlichkeit eines Destabilisators, der mindestes sein muss, damit 
	 * dieser aktiviert wird.*/
	var $min_destabilisator_prozent;
	
	/**
	 * Konstruktor fuer jaeger_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($min_jaeger_waffen, $min_jaeger_lemin_prozent, $min_lemin_jaeger_tank_start, 
						$max_jaeger_reichweite_schiffe, $max_jaeger_reichweite_planeten, 
						$max_jaeger_reichweite_angriff, $max_jaeger_schaden, $min_projektile_minen_legen, 
						$min_feind_abstand_minen_legen, $max_feind_abstand_minen_legen, 
						$min_kolonisten_destabilisator, $min_destabilisator_prozent) {
		$this->min_jaeger_waffen = $min_jaeger_waffen;
		$this->min_jaeger_lemin_prozent = $min_jaeger_lemin_prozent;
		$this->min_lemin_jaeger_tank_start = $min_lemin_jaeger_tank_start;
		$this->max_jaeger_reichweite_schiffe = $max_jaeger_reichweite_schiffe;
		$this->max_jaeger_reichweite_planeten = $max_jaeger_reichweite_planeten;
		$this->max_jaeger_reichweite_angriff = $max_jaeger_reichweite_angriff;
		$this->max_jaeger_schaden = $max_jaeger_schaden;
		$this->min_projektile_minen_legen = $min_projektile_minen_legen;
		$this->min_feind_abstand_minen_legen = $min_feind_abstand_minen_legen;
		$this->max_feind_abstand_minen_legen = $max_feind_abstand_minen_legen;
		$this->min_kolonisten_destabilisator = $min_kolonisten_destabilisator;
		$this->min_destabilisator_prozent = $min_destabilisator_prozent;
	}
}
?>