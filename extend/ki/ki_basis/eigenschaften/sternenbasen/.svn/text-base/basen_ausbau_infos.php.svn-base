<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zum Ausbau von Sternenbasen.
 */
class basen_ausbau_infos {
	/**Die Obere Grenze, mit dem in den Sternenbasen die Waffensysteme ausgebaut werden.*/
	var $waffen_tech_limit;
	/**Die Obere Grenze, mit dem in den Sternenbasen die Antriebssysteme ausgebaut werden.*/
	var $antrieb_tech_limit;
	/**In den ersten Runden kann es sinnvoll sein, keine Waffen auszubauen, um Cantox
	 * zu sparen. Wie lange keine Waffen ausgebaut werden, haengt hiervon ab.*/
	var $kein_waffenausbau_limit;
	/**Bestimmt das Limit, bis zu dem die Rumpf-Technik am Anfang ausgebaut werden darf.*/
	var $rumpf_anfangs_limit;
	/**Bestimmt die Runden-Anzahl, in denen das Limit fuer den Rumpf-Ausbau aktiv ist.*/
	var $rumpf_anfangs_limit_ticks;
	
	/**Bestimmt die minimale Rumpf-Stufe, aber fuer die naechste Rumpf-Stufe gespart werden kann.*/
	var $rumpf_sparen_min_stufe;
	/**Bestimmt die minimale Anzahl an Jaegern, die die KI haben muss, damit fuer die naechste Rumpf-Stufe an 
	 * einer Sternenbasis gespart wird.*/
	var $rumpf_sparen_min_jaeger;
	/**Bestimmt die Anzahl an Jaegern, die eine KI pro Rumpf-Stufe mehr haben muss, damit auf die naechste 
	 * Rumpf-Stufe gespart wird.*/
	var $rumpf_sparen_zusatz_jaeger;
	
	/**
	 * Konstruktor fuer basen_ausbau_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($waffen_tech_limit, $antrieb_tech_limit, $kein_waffenausbau_limit, 
						$rumpf_anfangs_limit, $rumpf_anfangs_limit_ticks, $rumpf_sparen_min_stufe, 
						$rumpf_sparen_min_jaeger, $rumpf_sparen_zusatz_jaeger) {
		$this->waffen_tech_limit = $waffen_tech_limit;
		$this->antrieb_tech_limit = $antrieb_tech_limit;
		$this->kein_waffenausbau_limit = $kein_waffenausbau_limit;
		$this->rumpf_anfangs_limit = $rumpf_anfangs_limit;
		$this->rumpf_anfangs_limit_ticks = $rumpf_anfangs_limit_ticks;
		
		$this->rumpf_sparen_min_stufe = $rumpf_sparen_min_stufe;
		$this->rumpf_sparen_min_jaeger = $rumpf_sparen_min_jaeger;
		$this->rumpf_sparen_zusatz_jaeger = $rumpf_sparen_zusatz_jaeger;
	}
}
?>
