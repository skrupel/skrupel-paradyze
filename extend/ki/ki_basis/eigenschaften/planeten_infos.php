<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zum Verhalten von Schiffen mit Terraformern.
 */
class planeten_infos {
	/**Bestimmt die maximale Anzahl der Minen auf den Planeten. Fuer kein Limit setzt man hier 0.*/
	var $minen_limit;
	/**Bestimmt die maximale Anzahl der Minen des Start-Planeten. Fuer kein Limit setzt man hier 0.*/
	var $minen_limit_start_planet;
	/**Bestimmt die Anzahl der Runden, in denen kein Automatischer Minen-/Fabrikenbau ist.*/
	var $kein_auto_bau_limit;
	/**Bestimmt die minimale Anzahl an Mitgliedern einer dominanten Spezies, sodass fuer diese auf dem Planeten 
	 * ein Reservat gebaut wird.*/
	var $min_dom_spezies_reservat;
	
	/**
	 * Konstruktor fuer planeten_infos.
	 */
	function __construct($minen_limit, $kein_auto_bau_limit, $minen_limit_start_planet, 
						$min_dom_spezies_reservat) {
		$this->minen_limit = $minen_limit;
		$this->kein_auto_bau_limit = $kein_auto_bau_limit;
		$this->minen_limit_start_planet = $minen_limit_start_planet;
		$this->min_dom_spezies_reservat = $min_dom_spezies_reservat;
	}
}
?>
