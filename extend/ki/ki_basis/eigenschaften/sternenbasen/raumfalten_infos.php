<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zu Raumfalten.
 */
class raumfalten_infos {
	/**Bestimmt die minimale Menge an Lemin, die ein Schiff hat, bevor es per Raumfalte mit 
	 * Lemin versorgt werden muss.*/
	var $raumfalte_min_lemin;
	/**Bestimmt die Menge an Lemin, die ein Frachter per Raumfalte erhaelt.*/
	var $raumfalte_frachter_lemin;
	/**Bestimmt die Menge an Lemin, die ein Jaeger per Raumfalte erhaelt.*/
	var $raumfalte_jaeger_lemin;
	/**Bestimmt die maximale Menge an Lemin, die auf einem Planeten sein darf, sodass 
	 * Schiffe an diesem Planeten per Raumfalte mit Lemin versorgt werden.*/
	var $raumfalte_max_planeten_lemin;
	/**Bestimmt die minimale Menge an Cantox, die auf einem Sternenbasen-Planeten uebrig bleiben darf, wenn 
	 * von dort aus eine Raumfalte zur Versorgung anderer Sternenbasen-Planeten (oder neuen Basen-Planeten) 
	 * gesendet wird.*/
	var $raumfalte_min_cantox_basen;
	/**Bestimmt die minimale Menge an Baxterium, die auf einem Sternenbasen-Planeten uebrig bleiben darf, wenn 
	 * von dort aus eine Raumfalte zur Versorgung anderer Sternenbasen-Planeten (oder neuen Basen-Planeten) 
	 * gesendet wird.*/
	var $raumfalte_min_min1_basen;
	/**Bestimmt die minimale Menge an Rennurbin, die auf einem Sternenbasen-Planeten uebrig bleiben darf, wenn 
	 * von dort aus eine Raumfalte zur Versorgung anderer Sternenbasen-Planeten (oder neuen Basen-Planeten) 
	 * gesendet wird.*/
	var $raumfalte_min_min2_basen;
	/**Bestimmt die minimale Menge an Vomisaan, die auf einem Sternenbasen-Planeten uebrig bleiben darf, wenn 
	 * von dort aus eine Raumfalte zur Versorgung anderer Sternenbasen-Planeten (oder neuen Basen-Planeten) 
	 * gesendet wird.*/
	var $raumfalte_min_min3_basen;
	/**Bestimmt die minimale Menge an Lemin, die auf einem Sternenbasen-Planeten uebrig bleiben darf, wenn 
	 * von dort aus eine Raumfalte zur Versorgung anderer Sternenbasen-Planeten (oder neuen Basen-Planeten) 
	 * gesendet wird.*/
	var $raumfalte_min_lemin_basen;
	/**Bestimmt die maximale Menge an Cantox, die auf einem Sternenbasen-Planeten (oder einem neuen 
	 * Basen-Planeten) sein darf, damit dieser von anderen Sternenbasen mit Cantox versorgt wird.*/
	var $max_cantox_basen;
	/**Bestimmt die maximale Menge an Baxterium, die auf einem Sternenbasen-Planeten (oder einem neuen 
	 * Basen-Planeten) sein darf, damit dieser von anderen Sternenbasen mit Baxterium versorgt wird.*/
	var $max_min1_basen;
	/**Bestimmt die maximale Menge an Rennurbin, die auf einem Sternenbasen-Planeten (oder einem neuen 
	 * Basen-Planeten) sein darf, damit dieser von anderen Sternenbasen mit Rennurbin versorgt wird.*/
	var $max_min2_basen;
	/**Bestimmt die maximale Menge an Vomisaan, die auf einem Sternenbasen-Planeten (oder einem neuen
	 * Basen-Planeten) sein darf, damit dieser von anderen Sternenbasen mit Vomisaan versorgt wird.*/
	var $max_min3_basen;
	/**Bestimmt die maximale Menge an Lemin, die auf einem Sternenbasen-Planeten (oder einem neuen
	 * Basen-Planeten) sein darf, damit dieser von anderen Sternenbasen mit Lemin versorgt wird.*/
	var $max_lemin_basen;
	/**Bestimmt die maximale Anzahl an eingehenden Raumfalten pro Planet.*/
	var $max_raumfalten_pro_planet;
	
	/**
	 * Konstruktor fuer raumfalten_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($raumfalte_min_lemin, $raumfalte_frachter_lemin, $raumfalte_jaeger_lemin, 
						$raumfalte_max_planeten_lemin, $raumfalte_min_cantox_basen, 
						$raumfalte_min_min1_basen, $raumfalte_min_min2_basen, $raumfalte_min_min3_basen, 
						$raumfalte_min_lemin_basen, $max_cantox_basen, $max_min1_basen, $max_min2_basen, 
						$max_min3_basen, $max_lemin_basen, $max_raumfalten_pro_planet) {
		$this->raumfalte_min_lemin = $raumfalte_min_lemin;
		$this->raumfalte_frachter_lemin = $raumfalte_frachter_lemin;
		$this->raumfalte_jaeger_lemin = $raumfalte_jaeger_lemin;
		$this->raumfalte_max_planeten_lemin = $raumfalte_max_planeten_lemin;
		$this->raumfalte_min_cantox_basen = $raumfalte_min_cantox_basen;
		$this->raumfalte_min_min1_basen = $raumfalte_min_min1_basen;
		$this->raumfalte_min_min2_basen = $raumfalte_min_min2_basen;
		$this->raumfalte_min_min3_basen = $raumfalte_min_min3_basen;
		$this->raumfalte_min_lemin_basen = $raumfalte_min_lemin_basen;
		$this->max_cantox_basen = $max_cantox_basen;
		$this->max_min1_basen = $max_min1_basen;
		$this->max_min2_basen = $max_min2_basen;
		$this->max_min3_basen = $max_min3_basen;
		$this->max_lemin_basen = $max_lemin_basen;
		$this->max_raumfalten_pro_planet = $max_raumfalten_pro_planet;
	}
}
?>
