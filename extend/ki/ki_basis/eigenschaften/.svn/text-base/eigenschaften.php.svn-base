<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Eine Kontainer-Klasse, die allgemeine Informationen zum jeweiligen Spiel enthaelt sowie alle gesammelten 
 * Daten der KI enthaelt, die ueberall gebraucht werden.
 */
class eigenschaften {
	//Allgemeine Informationen zum Spiel:
	/**Die aktuelle Runde des Spiels, in der sich die Klasse befindet.*/
	static $tick;
	/**Die Spieler-Nummer im aktuellen Spiel (1-10).*/
	static $comp_id;
	/**Die Rasse der KI im aktuellen Spiel.*/
	static $rasse;
	/**Der KI-Name.*/
	static $nick;
	/**Die temporaer Spiel-ID fuer das aktuelle Spiel.*/
	static $sid;
	/**Die Datenbank-ID des aktuellen Spiels.*/
	static $spiel_id;
	
	//Kontainer zu Infos:
	/**Enthaelt allgemeine Informationen zum Verhalten von Schiffen.*/
	static $schiffe_infos;
	/**Enthaelt Informationen zum Verhalten von Jaegern.*/
	static $jaeger_infos;
	/**Enthaelt Informationen zum Verhalten  von Scouts*/
	static $scouts_infos;
	/**Enthaelt Informationen zum Verhalten von Frachtern.*/
	static $frachter_infos;
	/**Enthaelt Informationen zum Verhalten von Frachtern, die kolonisieren.*/
	static $frachter_kolo_infos;
	/**Enthaelt Informationen zu Verhalten von Frachtern, die Sammel-Routen abfliegen.*/
	static $frachter_route_infos;
	/**Enthaelt Informationen zu Verhalten von Schiffen mit Spezialmissionen.*/
	static $spezialschiffe_infos;
	/**Enthaelt Informationen zum Ausbau von Sternenbasen.*/
	static $basen_ausbau_infos;
	/**Enthaelt Informationen zum Bau neuer Sternenbasen.*/
	static $basen_neu_infos;
	/**Enthaelt Informationen zum Bau von Schiffen.*/
	static $basen_schiffbau_infos;
	/**Enthaelt Informationen zum Raumfalten.*/
	static $raumfalten_infos;
	/**Enthaelt Informationen zum Verhalten der KI bei ihren Planeten.*/
	static $planeten_infos;
	/**Enthaelt Informationen zum Verhalten von Geleitschutz*/
	static $geleitschutz_infos;
	
	//=================
	//Gesammelte Daten:
	//=================
	//Eigene Schiffe:
	/**Enthaelt alle Datenbank-IDs der Schiffe der KI.*/
	static $schiff_ids;
	/**Enthaelt alle Datenbank-IDs von Frachter-Schiffe der KI.*/
	static $frachter_ids;
	/**Enthaelt alle Datenbank-IDs von Jaeger-Schiffe der KI.*/
	static $jaeger_ids;
	/**Enthaelt alle Datenbank-IDs von Scout-Schiffen der KI*/
	static $scout_ids;
	/**Enthaelt alle Datenbank-IDs von Schiffen mit Subpartikelcluster der KI.*/
	static $cluster_ids;
	/**Enthaelt alle Datenbank-IDs von Schiffen mit Quarksreorganisator der KI.*/
	static $quark_ids;
	/**Enthaelt alle Datenbank-IDs von Schiffen mit Cybernrittnikk.*/
	static $cyber_ids;
	/**Enthaelt alle Datenbank-IDs von Schiffen mit warmen Terraformern.*/
	static $terra_warm_ids;
	/**Enthaelt alle Datenbank-IDs von Schiffen mit kalten Terraformern.*/
	static $terra_kalt_ids;
	/**Enthaelt alle Datenbank-IDs von Schiffen mit viraler Invasion.*/
	static $virale_ids;
	/**Enthaelt alle Datenbank-IDs von Schiffe mit Strukturtastern.*/
	static $taster_ids;
	
	//Planeten-Infos:
	/** Die Datenbak-IDs der Planeten, die von einem Schiff in einer Route angesteuert werden.
	 * Hiermit wird verhindert, dass Schiffe die gleichen Planeten in der Route haben.*/
	static $frachter_routen;
	/**Die Datenbank-IDs der Planeten der KI.*/
	static $kolonien_ids;
	/**Die Datenbank-IDs fuer wichtige Planeten (Planeten mit Sternenbasis oder Planeten, an denen eine 
	 * Sternenbasis gebaut werden soll). Wichtig fuer Frachter, damit sie diese Planeten mit Resourcen 
	 * versorgen.*/
	static $wichtige_planeten_ids;
	
	//Sternbasen-Infos:
	/**Die Datenbank-IDs der Planeten mit Sternenbasis, die im Besitz der KI sind.*/
	static $basen_planeten_ids;
	/**Die Datenbank-IDs der Planeten, die eine neue Sternenbasis erhalten sollen, auf denen
	 * aber noch nicht genug Resourcen vorhanden sind.*/
	static $neue_basis_planeten;
	/**Enthaelt alle Schiff-Informationen, die an den Sternenbasen der Rasse gebaut werden koennen (auch jene, 
	 * die durch Strukturtaster kopiert wurden).*/
	static $schiff_arrays;
	
	//Gegner-Infos:
	/**Die Datenbank-IDs und Koordinaten aller im Moment fuer die KI sichtbaren gegnerischen Schiffe. */
	static $sichtbare_gegner_schiffe;
	/**Die Spieler-Nummern aller gegnerischen Spieler im aktuellen Spiel.*/
	static $gegner_ids;
	/**Die Datenbank-IDs und Koordinaten der bekannten (schon gesehenen) gegnerischen Planeten.*/
	static $gegner_planeten_daten;
	/**Die Datenbank-IDs und Koordinaten aller Angriffe auf eigene Planeten und Schiffen.*/
	static $gegner_angriffe;
	
	//Verbuendeten-Infos:
	/**Die Spieler-Nummern aller verbuendeten Spieler im aktuellen Spiel. */
	static $freunde_ids;
	
	//Wurmloecher:
	/**Enthaelt alle Datenbank-IDs und Koordinaten von sichtbaren oder schon gesehenen Wurmloechern.*/
	static $gesehene_wurmloecher_daten;
	/**Enthaelt alle Datenbank-IDs und Koordinaten von Wurmloechern, deren Ziele bekannt sind.*/
	static $bekannte_wurmloch_daten;
	/**Enthaelt alle Datenbank-IDs und Koordinaten von bekannten instabilen Wurmloechern.*/
	static $bekannte_instabile_wurmloch_daten;
		
	/**
	 * Initialisieurng fuer das Singleton Eigenschaften. Die uerbergebenen Werte werden uebernommen und 
	 * alle Arrays werden initialisiert.
	 */
	function init($tick, $comp_id, $rasse, $nick, $sid, $spiel_id, 
						$schiffe_infos, $jaeger_infos, $scouts_infos, $frachter_infos, $frachter_kolo_infos, 
						$frachter_route_infos, $spezialschiffe_infos, $basen_neu_infos, 
						$basen_ausbau_infos, $basen_schiffbau_infos, $raumfalten_infos, 
						$planeten_infos, $geleitschutz_infos) {
		self::$tick = $tick;
		self::$comp_id = $comp_id;
		self::$rasse = $rasse;
		self::$nick = $nick;
		self::$sid = $sid;
		self::$spiel_id = $spiel_id;
		self::$schiffe_infos = $schiffe_infos;
		self::$jaeger_infos = $jaeger_infos;
		self::$scouts_infos = $scouts_infos;
		self::$frachter_infos = $frachter_infos;
		self::$frachter_kolo_infos = $frachter_kolo_infos;
		self::$frachter_route_infos = $frachter_route_infos;
		self::$spezialschiffe_infos = $spezialschiffe_infos;
		self::$basen_neu_infos = $basen_neu_infos;
		self::$basen_ausbau_infos = $basen_ausbau_infos;
		self::$basen_schiffbau_infos = $basen_schiffbau_infos;
		self::$raumfalten_infos = $raumfalten_infos;
		self::$planeten_infos = $planeten_infos;
		self::$geleitschutz_infos = $geleitschutz_infos;
		
		self::$frachter_routen = array();
		self::$schiff_ids = array();
		self::$kolonien_ids = array();
		self::$frachter_ids = array();
		self::$jaeger_ids = array();
		self::$scout_ids = array();
		self::$cluster_ids = array();
		self::$quark_ids = array();
		self::$cyber_ids = array();
		self::$terra_warm_ids = array();
		self::$terra_kalt_ids = array();
		self::$virale_ids = array();
		self::$taster_ids = array();
		self::$sichtbare_gegner_schiffe = array();
		self::$wichtige_planeten_ids = array();
		self::$basen_planeten_ids = array();
		self::$neue_basis_planeten = array();
		self::$schiff_arrays = array();
		self::$gegner_ids = array();
		self::$gegner_planeten_daten = array();
		self::$freunde_ids = array();
		self::$gesehene_wurmloecher_daten = array();
		self::$bekannte_wurmloch_daten = array();
		self::$bekannte_instabile_wurmloch_daten = array();
	}
}
?>
