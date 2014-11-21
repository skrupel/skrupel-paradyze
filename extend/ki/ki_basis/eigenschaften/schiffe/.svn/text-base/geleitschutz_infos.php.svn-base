<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zu Geleitschutz.
 */
class geleitschutz_infos {
	/**Bestimmt die maximale Entfernung von Schiffen zueinander, sodass diese Geleitschutz 
	 * aktivieren.*/
	var $max_distanz_geleitschutz;
	/**Bestimmt die minimale Menge an Lemin, die ein Geleitschutz-gebendes Schiff haben darf, sodass das 
	 * Geleitschutz-bekommende Schiff wartet.*/
	var $min_lemin_warten;
	/**Bestimmt die minimale Menge an Lemin, die ein Schiff haben darf, damit es von anderen Schiffen 
	 * Geleitschutz bekommen darf.*/
	var $min_lemin_geleit_geben;
	
	/**
	 * Konstruktor fuer geleitschutz_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($max_distanz_geleitschutz, $min_lemin_warten, $min_lemin_geleit_geben) {
		$this->max_distanz_geleitschutz = $max_distanz_geleitschutz;
		$this->min_lemin_warten = $min_lemin_warten;
		$this->min_lemin_geleit_geben = $min_lemin_geleit_geben;
	}
}
?>
