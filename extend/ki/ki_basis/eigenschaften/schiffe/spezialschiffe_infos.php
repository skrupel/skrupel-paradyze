<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zu Schiffen mit Spezialmissionen.
 */
class spezialschiffe_infos {
	/**Bestimmt die minimale Menge an Lemin, die ein Planet haben muss, damit Schiffe 
	 * mit Subpartikelcluster diesen deaktivieren, damit dortige Schiffe mit Quarksreorganisatoren aktiv sind.*/
	var $min_lemin_cluster_off;
	/**Bestimmt die maximale Menge an Lemin, die ein Planet haben darf, damit Schiffe mit Quarksreorganisatoren
	 * diesen anlassen.*/
	var $max_lemin_quark_on;
	
	/**Bestimmt die minimale Anzahl an Planeten, die noetig sind, um einen neuen
	 * warmen Terraformer zu bauen.*/
	var $min_planeten_pro_terra_warm;
	/**Bestimmt die minimale Anzahl an Planeten, die noetig sind, um einen neuen
	 * kalten Terraformer zu bauen.*/
	var $min_planeten_pro_terra_kalt;
	/**Bestimmt die maximale Anzahl an warmen Terraformer-Schiffen.*/
	var $max_terra_warm;
	/**Bestimmt die maximale Anzahl an kalten Terraformer-Schiffen.*/
	var $max_terra_kalt;
	
	/**Bestimmt den maximalen Prozentsatz an Schiffen, welche Subraumverzerrer haben, die diesen
	 * auch benutzen.*/
	var $max_srv_prozent;
	/**Bestimmt die maximale Anzahl an Schiffen, welche Subraumverzerrer haben, die diesen
	 * auch benutzen.*/
	var $max_srv_anzahl;
	/**Bestimmt die maximale Reichweite von Schiffen mit Subraumverzerrern.*/
	var $max_srv_reichweite;
	
	/**Bestimmt die maximale Anzahl an Schiffen mit viraler Invasion.*/
	var $max_viral_anzahl;
	/**Bestimmt die minimale Anzahl an nativen Einwohnern, sodass diese durch virale Invasion 
	 * ausgerottet werden.*/
	var $min_native_invasion;
	
	/**Bestimmt den maximalen Prozentsatz von aktiven Schiffen mit Struktur-Tastern im Vergleich zum Rest 
	 * aller Schiffe der KI.*/
	var $max_aktive_taster_prozent;
	/**Bestimmt die maximale Anzahl an aktiven Taster-Schiffen.*/
	var $max_aktive_taster_anzahl;
	/**Bestimmt den maximalen Prozentsatz von Schiffen mit Struktur-Tastern im Vergleich zum Rest 
	 * aller Schiffe der KI.*/
	var $max_taster_prozent;
	
	/**Gibt die minimale Menge an Cantox an, die auf einem Planeten mit Sternenbasis sein muss, damit 
	 * dort angefangen wird, auf die 10. Stufe der Ruempfe zu sparen (sofern Stufe 9 schon erreicht wurde).*/
	var $min_cantox_rumpf10_sparen;
	/**Gibt die minimale Anzahl von Jaegern der KI an, damit auf einem Planeten mit Sternenbasis angefangen wird, 
	 * auf die 10. Stufe der Ruempfe zu sparen (sofern Stufe 9 schon erreicht wurde) */
	var $min_jaeger_rumpf10_sparen;
	/**Bestimmt die Rumpf-Stufe, aber der die KI (bei genuegend Cantox und bei genuegend Jaegern) fuer die 
	 * naechste Rumpf-Stufe sparen soll.*/
	var $min_rumpf_sparen;
	
	/**
	 * Konstruktor fuer spezialschiffe_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($min_lemin_cluster_off, $min_planeten_pro_terra_warm, $min_planeten_pro_terra_kalt, 
						$max_terra_warm, $max_terra_kalt, $max_srv_prozent, $max_srv_anzahl, $max_srv_reichweite, 
						$max_viral_anzahl, $max_aktive_taster_prozent, $max_aktive_taster_anzahl, 
						$max_taster_prozent, $max_lemin_quark_on, $min_native_invasion) {
		$this->min_lemin_cluster_off = $min_lemin_cluster_off;
		
		$this->min_planeten_pro_terra_warm = $min_planeten_pro_terra_warm;
		$this->min_planeten_pro_terra_kalt = $min_planeten_pro_terra_kalt;
		$this->max_terra_warm = $max_terra_warm;
		$this->max_terra_kalt = $max_terra_kalt;
		$this->terra_warm_ids = array();
		$this->terra_kalt_ids = array();
		
		$this->max_srv_prozent = $max_srv_prozent;
		$this->srv_anzahl = $max_srv_anzahl;
		$this->max_srv_reichweite = $max_srv_reichweite;
		
		$this->max_viral_anzahl = $max_viral_anzahl;
		
		$this->max_aktive_taster_prozent = $max_aktive_taster_prozent;
		$this->max_aktive_taster_anzahl = $max_aktive_taster_anzahl;
		$this->max_taster_prozent = $max_taster_prozent;
		
		$this->max_lemin_quark_on = $max_lemin_quark_on;
		$this->min_native_invasion = $min_native_invasion;
	}
}
?>
