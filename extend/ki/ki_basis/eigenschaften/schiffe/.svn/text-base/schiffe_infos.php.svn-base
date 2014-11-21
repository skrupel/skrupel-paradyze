<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zum Verhalten von Schiffen.
 */
class schiffe_infos {
	/**Bestimmt den Abstand, mit dem Schiffe Minenfelder, die im Weg sind, umfliegen.*/
	var $mindest_minenfeld_abstand;
	/**Bestimmt den maximalen Abstand eines Schiffes zu einem Minenfeld, sodass das Schiff zum Minenfeld 
	 * fliegt, um es zu raeumen.*/
	var $max_abstand_minenfeld_raeumen;
	/**Bestimmt den minimalen Abstand eines Schiffes zu einem sichtbaren Feindschiff, sodass das Schiff zu 
	 * einem Minenfeld fliegt, um dieses zu raeumen.*/
	var $min_abstand_minenfeld_raeumen_feindschiff;
	/**Bestimmt den Abstand, mit dem Schiffe Wurmloecher, die im Weg sind, umfliegen.*/
	var $mindest_wurmloch_abstand;
	/**Bestimmt die minimale Menge an Lemin, die abzueglich des fuer die Benutzung des Sprungtriebwerks
	 * erforderliche Menge nach dem Sprung uebrig bleiben darf, damit das Sprungtreibwerk ueberhaupt 
	 * aktiviert wird.*/
	var $min_restlemin_sprung;
	/**Bestimmt die Groesse der Sektoren in Lichtjahren, die bei der Berechnung eines Erkundungs-Kurses
	 * benutzt werden.*/
	var $sektor_erkundung_groesse;
	/**Bestimmt die maximale Entfernung fuer Wurmloecher von Schiffen, sodass Schiffe diese erkunden.*/
	var $max_wurmloch_erkundungs_reichweite;
	/**Bestimmt die maximale Menge an Lemin, die ein Schiff zu Beginn seines Fluges tanken darf.*/
	var $max_lemin_start;
	
	/**Bestimmt die minimale Menge an Rennurbin an Bord eines Schiffes, sodass Tarnung aktiviert wird.*/
	var $min_ren_tarnung;
	/**Bestimmt die maximale Menge an Rennurbin, die geladen wird, wenn ein Schiff Tarnung hat.*/
	var $max_ren_tarnung_start;
	
	/**
	 * Konstruktor fuer schiffe_infos. Die uebergebenen Werte werden einfach uebernommen.
	 */
	function __construct($mindest_minenfeld_abstand, $max_abstand_minenfeld_raeumen, 
						$min_abstand_minenfeld_raeumen_feindschiff, $mindest_wurmloch_abstand, 
						$min_restlemin_sprung, $sektor_erkundung_groesse, $min_ren_tarnung, 
						$max_ren_tarnung_start, $max_wurmloch_erkundungs_reichweite, $max_lemin_start) {
		$this->mindest_minenfeld_abstand = $mindest_minenfeld_abstand;
		$this->max_abstand_minenfeld_raeumen = $max_abstand_minenfeld_raeumen;
		$this->min_abstand_minenfeld_raeumen_feindschiff = $min_abstand_minenfeld_raeumen_feindschiff;
		$this->mindest_wurmloch_abstand = $mindest_wurmloch_abstand;
		$this->min_restlemin_sprung = $min_restlemin_sprung;
		$this->sektor_erkundung_groesse = $sektor_erkundung_groesse;
		$this->min_ren_tarnung = $min_ren_tarnung;
		$this->max_ren_tarnung_start = $max_ren_tarnung_start;
		$this->max_wurmloch_erkundungs_reichweite = $max_wurmloch_erkundungs_reichweite;
		$this->max_lemin_start = $max_lemin_start;
	}
}
?>