<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 */
include_once("eigenschaften/eigenschaften.php");
include_once("eigenschaften/planeten_infos.php");
include_once("eigenschaften/schiffe/schiffe_infos.php");
include_once("eigenschaften/schiffe/geleitschutz_infos.php");
include_once("eigenschaften/schiffe/jaeger_infos.php");
include_once("eigenschaften/schiffe/scouts_infos.php");
include_once("eigenschaften/schiffe/frachter/frachter_infos.php");
include_once("eigenschaften/schiffe/frachter/frachter_kolo_infos.php");
include_once("eigenschaften/schiffe/frachter/frachter_route_infos.php");
include_once("eigenschaften/schiffe/spezialschiffe_infos.php");
include_once("eigenschaften/sternenbasen/basen_ausbau_infos.php");
include_once("eigenschaften/sternenbasen/basen_neu_infos.php");
include_once("eigenschaften/sternenbasen/basen_schiffbau_infos.php");
include_once("eigenschaften/sternenbasen/raumfalten_infos.php");

include_once("planeten_basis/planeten_basis.php");
include_once("politik_basis/politik_basis.php");
include_once("schiffe_basis/schiffe_basis.php");
include_once("schiffe_basis/frachter_basis.php");
include_once("schiffe_basis/jaeger_basis.php");
include_once("schiffe_basis/scouts_basis.php");
include_once("schiffe_basis/geleitschutz_basis.php");
include_once("schiffe_basis/wurmloecher_basis.php");
include_once("sternenbasen_basis/sternenbasen_basis.php");
include_once("sternenbasen_basis/basen_schiffbau_basis.php");

/**
 * Diese abstrakte Klasse beinhaltet die meisten nuetzlichen Funktionen und Datenfelder.
 * Sie sollte nicht veraendert werden, ohne sich im klaren zu sein, was sie fuer die erbenden
 * Klassen die Konsquenzen sein werden.
 */
abstract class ki_basis {
	var $planeten;
	var $politik;
	var $frachter;
	var $jaeger;
	var $scouts;
	var $spezialschiffe;
	var $sternenbasen;
	var $geleitschutz;
	
	/**
	 * Dies ist die wichtigste Funktion einer KI-Klasse. Sie wird in /inhalt/zugende.php aufgerufen, nachdem 
	 * das KI-Objekt erstellt wurde. Erbende KI-Klassen muessen diese Funktion implementieren.
	 */
	abstract function berechneZug();
	
	/**
	 * Konstructor fuer ki_basis. Es wird einfach ein eigenschaften-Objekt erstellt.
	 */
	function __construct($sid, $id, $nick, $schiffe_infos, $jaeger_infos, $scouts_infos, $frachter_infos, 
						$frachter_kolo_infos, $frachter_route_infos, $spezialschiffe_infos, $basen_neu_infos, 
						$basen_ausbau_infos, $basen_schiffbau_infos, $raumfalten_infos, $planeten_infos, 
						$geleitschutz_infos) {
		$spiel_id = $this->ermittleSpielID($sid);
		$comp_id = $this->ermittleCompID($id, $sid);
		$tick = $this->ermittleTick($spiel_id);
		$rasse = $this->ermittleRasse($comp_id, $sid);
		eigenschaften::init($tick, $comp_id, $rasse, $nick, $sid, $spiel_id, 
						$schiffe_infos, $jaeger_infos, $scouts_infos, $frachter_infos, $frachter_kolo_infos, 
						$frachter_route_infos, $spezialschiffe_infos, $basen_neu_infos, $basen_ausbau_infos, 
						$basen_schiffbau_infos, $raumfalten_infos, $planeten_infos, $geleitschutz_infos);
		wurmloecher_basis::ermittleSichtbareWurmloecher();
		wurmloecher_basis::ermittleGeseheneWurmloecher();
		wurmloecher_basis::ermittleBekannteWurmloecher();
		wurmloecher_basis::ermittleInstabileWurmloecher();
	}
	
	/**
	 * Ermittelt die Datenbank-ID des aktuellen Spieles und gibt diese zurueck.
	 * arguments: $sid - Die temporaere Datenbank-ID des aktuellen Spieles.
	 * returns: die Datenbank-ID des aktuellen Spieles.
	 */
	function ermittleSpielID($sid) {
		$id = @mysql_query("SELECT id FROM skrupel_spiele WHERE sid='$sid'");
		$id = @mysql_fetch_array($id);
		return $id['id'];
	}
	
	/**
	 * Ermittelt die Spieler-Nummer des KI-Spieler im aktuellen Spiel und gibt diese zurueck.
	 * arguments: $id - Die Spieler-ID der KI.
	 * 			  $sid - Die temporaere Datenbank-ID des aktuellen Spieles.
	 * returns: Die Spieler-Nummer des KI-Spieler im aktuellen Spiel.
	 */
	static function ermittleCompID($id, $sid) {
		$k = 1;
		for(; $k <= 10; $k++) {
			$comp_spieler_id = @mysql_query("SELECT spieler_{$k} FROM skrupel_spiele WHERE sid='$sid'");
			$comp_spieler_id = @mysql_fetch_array($comp_spieler_id);
			$comp_spieler_id = $comp_spieler_id['spieler_'.$k];
			if($comp_spieler_id == $id) break;
		}
		return $k;
	}
	
	/**
	 * Ermittelt die aktuelle Runde des aktuellen Spiels und gibt diese zurueck.
	 * arguments: $spiel_id - Die Datenbank-ID des aktuellen Spieles.
	 * returns: Die aktuelle Runde im aktuellen Spiel.
	 */
	function ermittleTick($spiel_id) {
		$tick = @mysql_query("SELECT runde FROM skrupel_spiele WHERE id='$spiel_id'");
		$tick = @mysql_fetch_array($tick);
		return $tick['runde'];
	}
	
	/**
	 * Ermittelt die Rasse des KI-Spielers und gibt diese zurueck.
	 * arguments: $comp_id - Die Spieler-Nummer im aktuellen Spiel.
	 * 			  $sid - Die temporaere Datenbank-ID des aktuellen Spieles.
	 * returns: Die Rasse deas KI-Spielers.
	 */
	function ermittleRasse($comp_id, $sid) {
		$rasse = @mysql_query("SELECT spieler_{$comp_id}_rasse FROM skrupel_spiele WHERE sid='$sid'");
		$rasse = @mysql_fetch_array($rasse);
		return $rasse['spieler_'.$comp_id.'_rasse'];
	}
	
	/**
	 * Ermittelt die Optimale Temperatur der Rasse des KI-Spielers
	 * returns: Die Optimale Temperatur in Grad Celsius.
	 * 			0, falls die Rasse keine Optimale Temperatur hat.
	 */
	static function ermittleOptimaleTemp() {
		$rassen_daten_pfad = "./../daten/".eigenschaften::$rasse."/daten.txt";
		$rassen_daten = file($rassen_daten_pfad);
		$rassen_attribute = explode(':', $rassen_daten[2]);
		if ($rassen_attribute[0] == 0) return 0;
		return $rassen_attribute[0] - 35;
	}
	
	/**
	 * Zieht eine bestimmte Menge einer Resource auf einem Planeten ab.
	 * arguments: $resource - Die Resource, von der etwas abgezogen werden soll.
	 * 			  $menge - Die Menge, die abgezogen werden soll.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, auf dem Resourcen abgezogen werden.
	 */
	static function zieheResourcenAb($resource, $menge, $planeten_id) {
		$aktuelle_menge = @mysql_query("SELECT $resource FROM skrupel_planeten WHERE id='$planeten_id'");
		$aktuelle_menge = @mysql_fetch_array($aktuelle_menge);
		$neue_menge = $aktuelle_menge[$resource] - $menge;
		@mysql_query("UPDATE skrupel_planeten SET $resource='$neue_menge' WHERE id='$planeten_id'");
	}

	/**
	 * Ermittelt die naechst-groessere freie ID der uebergebenen Tabelle.
	 * arguments: $tabelle - Der Name der Tabelle.
	 * returns: die naechst-groessere freie ID der Tabelle.
	 */
	static function ermittleNaechsteID($tabelle) {
		$groesste_id = @mysql_query("SELECT max(id) FROM $tabelle");
		$groesste_id = @mysql_fetch_array($groesste_id);
		return $groesste_id['max(id)'] + 1;
	}
	
	/**
	 * Prueft, ob in der Tabelle skrupel_ki_spezialschiffe Schiffe eingetragen sind, die eigentlich nicht mehr 
	 * existieren. Diese Eintraege werden geloescht.
	 */	
	static function pruefeSpezialSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		$geloeschte_schiffe = @mysql_query("SELECT k.id FROM skrupel_ki_spezialschiffe k WHERE (NOT EXISTS 
			(SELECT * FROM skrupel_schiffe s WHERE (s.id=k.schiff_id) AND (s.spiel='$spiel_id')))");
		while($schiff = @mysql_fetch_array($geloeschte_schiffe)) {
			$spezialschiff_id = $schiff['id'];
			@mysql_query("DELETE FROM skrupel_ki_spezialschiffe WHERE id='$spezialschiff_id'");
		}
	}
	
	/**
	 * Ermittelt alle Informationen zu Schiffen, die durch die Rasse gebaut werden koennen und speichert diese 
	 * in eigenschaften::$schiff_arrays. Es werden auch jene Schiffe erfasst, die durch Strukturtaster kopiert 
	 * wurden. An jedes Array wird die zum Schiff gehoeriges Rasse angehaengt.
	 */
	function ermittleSchiffArrays() {
		$schiff_datei_pfad = "./../daten/".eigenschaften::$rasse."/schiffe.txt";
		$schiffe = file($schiff_datei_pfad);
		$schiff_arrays = array();
		foreach($schiffe as $schiff_zeile) {
			$array = explode(':', $schiff_zeile);
			$array[] = eigenschaften::$rasse;
			$schiff_arrays[] = $array;
		}
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$alle_daten = @mysql_query("SELECT klasse, klasse_id, techlevel, sonstiges, rasse 
				FROM skrupel_konplaene WHERE (spiel='$spiel_id') AND (besitzer='$comp_id')");
		while($daten = @mysql_fetch_array($alle_daten)) {
			$relevante_daten = array();
			$relevante_daten[] = $daten['klasse'];
			$relevante_daten[] = $daten['klasse_id'];
			$relevante_daten[] = $daten['techlevel'];
			$sonstiges_array = explode(':', $daten['sonstiges']);
			foreach($sonstiges_array as $info) {
				$relevante_daten[] = $info;
			}
			$relevante_daten[] = $daten['rasse'];
			$schiff_arrays[] = $relevante_daten;
		}
		eigenschaften::$schiff_arrays = $schiff_arrays;
	}
	
	/**
	 * Ermittelt alle Angriffe eines Gegners auf einen von der KI bewohnten Planeten bzw. Schiffe. Ein Schiff 
	 * zaehlt als Angreifer, falls es Kurs auf einen Planeten oder Schiff der KI gelegt hat und es vom 
	 * KI-Spieler gesehen wird.
	 */
	function ermittleGegnerAngriffe() {
		eigenschaften::$gegner_angriffe = array();
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		
		foreach(eigenschaften::$sichtbare_gegner_schiffe as $gegner_schiff_daten) {
			$id = $gegner_schiff_daten['id'];
			$schiff_daten = @mysql_query("SELECT zielid, status, kox, koy FROM skrupel_schiffe WHERE id='$id'");
			$schiff_daten = @mysql_fetch_array($schiff_daten);
			$ziel_id = $schiff_daten['zielid'];
			
			if($ziel_id == 0) {
				$status = $schiff_daten['status'];
				if($status == 2) {
					$x_pos = $schiff_daten['kox'];
					$y_pos = $schiff_daten['koy'];
					$planeten_daten = @mysql_query("SELECT id FROM skrupel_planeten 
							WHERE x_pos='$x_pos' AND y_pos='$y_pos' AND spiel='$spiel_id' AND besitzer='$comp_id'");
					$planeten_daten = @mysql_fetch_array($planeten_daten);
					if($planeten_daten == null || $planeten_daten['id'] == 0) continue;
					$ziel_id = $planeten_daten['id'];
				}
			}
			if(in_array($ziel_id, eigenschaften::$kolonien_ids) || in_array($ziel_id, eigenschaften::$schiff_ids)) 
				eigenschaften::$gegner_angriffe[] = $gegner_schiff_daten;
			
		}
	}
	
	/**
	 * Ermittelt die Koordinaten und die Datenbank-ID aller sichtbaren gegnerischen Planeten und 
	 * schreibt diese Infos in eigenschaften::$gegner_planeten_daten und in die Tabelle skrupel_ki_planeten.
	 */
	function ermittleSichtbareGegnerPlaneten() {
		$sicht = "sicht_".eigenschaften::$comp_id;
		$comp_id = eigenschaften::$comp_id;
		$spiel_id = eigenschaften::$spiel_id;
		$gegner_planeten = @mysql_query("SELECT x_pos, y_pos, id FROM skrupel_planeten WHERE ($sicht=1) 
					AND (NOT ((besitzer='$comp_id') OR (besitzer=0))) AND (spiel='$spiel_id') AND NOT EXISTS 
					(SELECT * FROM skrupel_politik WHERE (spiel='$spiel_id') AND 
					((partei_a='$comp_id') || (partei_b='$comp_id')))");
		while($planeten_infos = @mysql_fetch_array($gegner_planeten)) {
			if($planeten_infos == null || $planeten_infos['id'] == 0) continue;
			eigenschaften::$gegner_planeten_daten[] = array('x'=>$planeten_infos['x_pos'], 
													  'y'=>$planeten_infos['y_pos'], 'id'=>$planeten_infos['id']);
			self::updateSchlechtePlaneten($planeten_infos['id'], 2);
		}
	}
	
	/**
	 * Ermittelt die Koordinaten und Datenbank-IDs der sichtbaren gegnerischen Schiff und schreibt diese Infos 
	 * in eigenschaften::$sichtbare_gegner_schiffe.
	 */
	function ermittleGegnerSchiffe() {
		$sicht = "sicht_".eigenschaften::$comp_id;
		$comp_id = eigenschaften::$comp_id;
		$spiel_id = eigenschaften::$spiel_id;
		$gegner_schiffe = @mysql_query("SELECT kox, koy, id FROM skrupel_schiffe WHERE ($sicht=1) 
					AND (NOT (besitzer='$comp_id')) AND (spiel='$spiel_id') AND (NOT EXISTS 
					(SELECT * FROM skrupel_politik WHERE (spiel='$spiel_id') AND 
					((partei_a='$comp_id') || (partei_b='$comp_id'))))");
		while($schiff_id =  mysql_fetch_array($gegner_schiffe)) {
			eigenschaften::$sichtbare_gegner_schiffe[] = array ('x'=>$schiff_id['kox'], 'y'=>$schiff_id['koy'], 
																'id'=>$schiff_id['id']);
		}
	}
	
	/**
	 * Ermittelt den genauen Platz, den der uebergebene Spieler in einer bestimmten Disziplin im 
	 * uebergebenen Spiel hat.
	 * arguments: $comp_id - Die spielinterne Spieler-ID des Spielers, dessen Rang ermittelt wird.
	 * 			  $rang_art - Die Art des Ranges, die ermittelt werden soll.
	 * 			  $spiel_id - Die Datenbank-ID des Spieles.
	 * returns: Den Platz, den der Spieler in der uebergebenen Disziplin belegt.
	 */
	static function ermittleRangVonSpieler($comp_id, $rang_art) {
		$rang_string = "spieler_".$comp_id."_".$rang_art;
		$spiel_id = eigenschaften::$spiel_id;
		$rang = @mysql_query("SELECT $rang_string FROM skrupel_spiele WHERE spiel='$spiel_id'");
		$rang = @mysql_fetch_array($rang);
		return $rang[$rang_string];
	}
	
	/**
	 * Ermittelt alle Spieler-Nummern von gegnerischen Spielern im aktuellen Spiel und 
	 * schreibt diese in eigenschaften::$gegner_ids.
	 */
	function ermittleGegner() {
		eigenschaften::$gegner_ids = array();
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		for($k = 1; $k <= 10; $k++) {
			if($k == eigenschaften::$comp_id) continue;
			$spieler_id = @mysql_query("SELECT spieler_{$k} FROM skrupel_spiele WHERE (id='$spiel_id') 
				AND NOT EXISTS (SELECT * FROM skrupel_politik WHERE (spiel='$spiel_id') AND 
				((partei_a='$comp_id') || (partei_b='$comp_id')))");
			$spieler_id = @mysql_fetch_array($spieler_id);
			if($spieler_id['spieler_'.$k] != 0) eigenschaften::$gegner_ids[] = $k;
		}
	}
	
	/**
	 * Ermittelt alle Kolonien des KI-Spielers und schreibt diese in eigenschaften::$kolonien_ids. Falls genug 
	 * Kolonien vorhanden sind, wird die Menge an Kolonisten, die pro Planet zur Kolonisieren mit genommen wird, 
	 * erhoeht.
	 */
	function ermittleKolonien() {
		$comp_id = eigenschaften::$comp_id;
		$spiel_id = eigenschaften::$spiel_id;
		$kolonien = @mysql_query("SELECT id FROM skrupel_planeten 
						WHERE (besitzer='$comp_id') AND (spiel='$spiel_id')");
		while($planeten_id = @mysql_fetch_array($kolonien)) {
			eigenschaften::$kolonien_ids[] = $planeten_id['id'];
		}
		if(count(eigenschaften::$kolonien_ids) <= eigenschaften::$frachter_kolo_infos->max_kolonien_wenig_leute) 
		eigenschaften::$frachter_kolo_infos->kolo_leute = eigenschaften::$frachter_kolo_infos->kolo_leute_anfangs;
	}
	
	/**
	 * Aktualisiert die Tabelle skrupel_ki_neuebasen. Ist $aktion=0, so wird der uebergebene Planet von 
	 * der Tabelle geloescht, ist $aktion=1 wird der Planet hinzugefuegt.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, der hinzugefuegt oder geloescht werden soll.
	 * 			  $aktion - Der Planet wird bei 0 aus der Tabelle geloescht und bei 1 hinzugefuegt.
	 */
	static function updateNeueBasenPlaneten($planeten_id, $aktion) {
		switch($aktion) {
			case 0: {
				@mysql_query("DELETE FROM skrupel_ki_neuebasen WHERE planeten_id='$planeten_id'");
				break;
			}
			case 1: {
				$planet = @mysql_query("SELECT id FROM skrupel_ki_neuebasen WHERE planeten_id='$planeten_id'");
				$planet = @mysql_fetch_array($planet);
				if($planet != null && $planet['id'] != null && $planet['id'] != 0) return;
				$neue_id = ki_basis::ermittleNaechsteID("skrupel_ki_neuebasen");
				@mysql_query("INSERT INTO skrupel_ki_neuebasen (id, planeten_id) 
						VALUES ('$neue_id', '$planeten_id')");
				break;
			}
		}
	}
	
	/**
	 * Ermittelt alle Planeten, wo eine Sternenbasis gebaut werden soll und schreibt diese 
	 * in eigenschaften::$neue_basis_planeten.
	 */
	function ermittleNeueBasenPlaneten() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$neue_basen_planeten = @mysql_query("SELECT n.planeten_id, n.id FROM skrupel_ki_neuebasen n, 
				skrupel_planeten p WHERE (p.id=n.planeten_id) AND (p.spiel='$spiel_id') 
				AND (p.besitzer='$comp_id')");
		eigenschaften::$neue_basis_planeten = array();
		while($id = @mysql_fetch_array($neue_basen_planeten)) {
			$planeten_id = $id['planeten_id'];
			$n_id = $id['id'];
			if(self::planetWirdAngegriffen($planeten_id)) 
				@mysql_query("DELETE FROM skrupel_ki_neuebasen WHERE id='$n_id'");
			else {
				eigenschaften::$neue_basis_planeten[] = $planeten_id;
				eigenschaften::$wichtige_planeten_ids[] = $planeten_id;
			}
		}
	}
	
	function planetWirdAngegriffen($planeten_id) {
		$planeten_daten = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_daten = @mysql_fetch_array($planeten_daten);
		$x_planet = $planeten_daten['x_pos'];
		$y_planet = $planeten_daten['y_pos'];
		
		foreach(eigenschaften::$gegner_angriffe as $schiff_daten) {
			$id = $schiff_daten['id'];
			$schiff_daten = @mysql_query("SELECT zielid, status, kox, koy FROM skrupel_schiffe WHERE id='$id'");
			$schiff_daten = @mysql_fetch_array($schiff_daten);
			$ziel_id = $schiff_daten['zielid'];
			
			if($planeten_id == $ziel_id) return true;
			
			if($ziel_id == 0) {
				$status = $schiff_daten['status'];
				if($status == 2) {
					$x_schiff = $schiff_daten['kox'];
					$y_schiff = $schiff_daten['koy'];
					if($x_planet == $x_schiff && $y_planet == $y_schiff) return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Ermittelt alle bekannten, sich fuer die Kolonisierung nicht lohnenden Planeten-IDs.
	 * returns: Ein Array mit den Datenbank-IDs der "schlechten" Planeten.
	 */
	static function ermittleSchlechtePlaneten() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$schlechte_planeten = @mysql_query("SELECT p.id FROM skrupel_ki_planeten k, skrupel_planeten p 
						WHERE (p.id=k.planeten_id) AND (p.spiel='$spiel_id') AND (k.comp_id='$comp_id')");
		$planeten = array();
		while($planet = @mysql_fetch_array($schlechte_planeten)) {
			$planeten[] = $planet['id'];
		}
		return $planeten;
	}
	
	/**
	 * Aktualisiert die Tabelle skrupel_ki_planeten mit den uebergebenen Werten.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, der sich fuer die Kolonisierung nicht lohnt.
	 * 			  $extra - Der Grund, warum der Planet sich nicht lohnt.
	 */
	static function updateSchlechtePlaneten($planeten_id, $extra) {
		$planeten_ids = self::ermittleSchlechtePlaneten();
		if(in_array($planeten_id, $planeten_ids)) return;
		$id = self::ermittleNaechsteID("skrupel_ki_planeten");
		$comp_id = eigenschaften::$comp_id;
		@mysql_query("INSERT INTO skrupel_ki_planeten (id, planeten_id, comp_id, extra) 
						VALUES('$id', '$planeten_id', '$comp_id', '$extra')");
	}
	
	/**
	 * Ermittelt die ID und die Anzahl der dominanten Spezies des uebergebenen Planeten und gibt diese 
	 * Informationen zurueck.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, dessen dominante Spezies ermittelt werden soll.
	 * returns: Ein Array aus der ID und der Anzahl der dominanten Spezies.
	 * 			null, falls es keine dominante Spezies auf dem Planeten gibt.
	 */
	static function ermittleDomSpezies($planeten_id) {
		$planeten_daten = @mysql_query("SELECT native_id, native_kol FROM skrupel_planeten 
			WHERE id='$planeten_id'");
		$planeten_daten = @mysql_fetch_array($planeten_daten);
		$planeten_einwohner = $planeten_daten['native_id'];
		$planeten_einwohner_anzahl = $planeten_daten['native_kol'];
		if($planeten_einwohner_anzahl == 0) return null;
		return array('id'=> $planeten_einwohner, 'anzahl'=>$planeten_einwohner_anzahl);
	}
	
	/**
	 * Checkt, ob sich der angegebene Planet zur kolonisierung lohnt. Es wird ueberprueft, ob die Temperatur 
	 * des Planeten um mehr als 30 C von der optimalen Temperatur der Rasse abweicht oder eine aggressive oder 
	 * eine diebische dominante Spezies auf dem Planeten ist. Falls das der Fall ist, wird dieser Planet in 
	 * die skrupel_ki_planeten-Tabelle hinzugefuegt.
	 * arguments: $planeten_id - Die Datenbank-ID des zu ueberpruefenden Planeten.
	 * returns: true - Falls der Planet kolonisiert werden sollte.
	 * 			false - Sonst.
	 */
	static function PlanetLohntSich($planeten_id) {
		$schlechte_planeten = self::ermittleSchlechtePlaneten();
		if(in_array($planeten_id, $schlechte_planeten)) return false;
		$optimale_temp = self::ermittleOptimaleTemp();
		$planeten_daten = @mysql_query("SELECT temp, native_id, native_kol FROM skrupel_planeten 
			WHERE id='$planeten_id'");
		$planeten_daten = @mysql_fetch_array($planeten_daten);
		$planeten_temp = $planeten_daten['temp'] - 35;
		$planeten_einwohner = $planeten_daten['native_id'];
		$planeten_einwohner_anzahl = $planeten_daten['native_kol'];
		if($optimale_temp != 0 && abs($planeten_temp - $optimale_temp) >= 30) {
			self::updateSchlechtePlaneten($planeten_id, 0);
			return false;
		}
		if(($planeten_einwohner == 8 || $planeten_einwohner == 15 
		|| $planeten_einwohner == 31 || $planeten_einwohner == 36) 
		&& $planeten_einwohner_anzahl >= eigenschaften::$spezialschiffe_infos->min_native_invasion) {
			self::updateSchlechtePlaneten($planeten_id, 1);
			return false;
		}
		return true;
	}
	
	/**
	 * Ermittelt alle bekannten gegnerischen Planeten und speichert deren Daten in 
	 * eigenschaften::$gegner_planeten_daten
	 */
	static function ermittleGegnerPlaneten() {
		$comp_id = eigenschaften::$comp_id;
		$spiel_id = eigenschaften::$spiel_id;
		$planeten_infos = @mysql_query("SELECT p.id, p.x_pos, p.y_pos FROM skrupel_planeten p, 
						skrupel_ki_planeten k WHERE (p.id=k.planeten_id) 
						AND (p.spiel='$spiel_id') AND (k.comp_id='$comp_id') AND (k.extra=2)");
		eigenschaften::$gegner_planeten_daten = array();
		while($planet = @mysql_fetch_array($planeten_infos)) {
			eigenschaften::$gegner_planeten_daten[] = array('x'=>$planet['x_pos'], 'y'=>$planet['y_pos'], 
															'id'=>$planet['id']);
		}
	}
	
	/**
	 * Ermittelt alle fuer die KI sichtbaren feindlichen Minenfelder und gibt diese zurueck.
	 * returns: Ein Array aus allen Datenbank-IDs und Koordinaten von sichtbaren feindlichen Minenfelder.
	 */
	static function ermittleFeindlicheMinenfelder() {
		if(!(self::ModulAktiv("minenfelder"))) return array();
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$minenfelder = @mysql_query("SELECT id, x_pos, y_pos, extra FROM skrupel_anomalien 
			WHERE (spiel='$spiel_id') AND (art=5) AND (sicht_".$comp_id."=1)");
		$minenfeld_array = array();
		while($minenfeld = @mysql_fetch_array($minenfelder)) {
			$extra_array = explode(':', $minenfeld['extra']);
			$besitzer = $extra_array[0];
			if(!in_array($besitzer, eigenschaften::$gegner_ids)) continue;
			$minenfeld_eintrag = array('x'=>$minenfeld['x_pos'], 'y'=>$minenfeld['y_pos'], 
									   'id'=>$minenfeld['id']);
			$minenfeld_array[] = $minenfeld_eintrag;
		}
		return $minenfeld_array;
	}
	
	/**
	 * Ermittelt den Besitzer des uebergebenen Minenfeldes.
	 * arguments: $anomalie_id - Die Datenbank-ID des Minenfeldes, dessen Besitzer gefragt wird.
	 * returns: Die Spieler-ID im aktuellen Spiel des Besitzers des Minenfeldes.
	 */
	static function ermittleMinenfeldBesitzer($anomalie_id) {
		$besitzer = @mysql_query("SELECT extra FROM skrupel_anomalien WHERE id='$anomalie_id'");
		$besitzer = @mysql_fetch_array($besitzer);
		$besitzer = explode(':', $besitzer['extra']);
		return $besitzer[0];
	}
	
	/**
	 * Ermittelt die Menge der uebergebene Resource auf dem uebergebenen Planeten.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, von dem eine Resource abgefragt wird.
	 * 			  $resource - Die Resource, dessen Menge abgefragt wird.
	 * returns: Die Menge der gefragten Resource des Planeten.
	 */
	static function ermittleResource($planeten_id, $resource) {
		$res = @mysql_query("SELECT $resource FROM skrupel_planeten WHERE id='$planeten_id'");
		$res = @mysql_fetch_array($res);
		return $res[$resource];
	}
	
	/**
	 * Ueberprueft, ob das aktuelle Spiel das uebergebene Modul aktiviert hat.
	 * arguments: $modul_string - Das zu ueberpruefende Modul.
	 * returns: true, falls das Spiel das Modul aktiviert hat.
	 * 			false, sonst.
	 */
	static function ModulAktiv($modul_string) {
		$spiel_id = eigenschaften::$spiel_id;
		$module = @mysql_query("SELECT module FROM skrupel_spiele WHERE id='$spiel_id'");
		$module = @mysql_fetch_array($module);
		$modul_array = explode(':', $module['module']);
		switch($modul_string) {
			case "minenfelder": return $modul_array[2]+0 != 0;
			case "taktik": return $modul_array[3]+0 != 0;
		}
		return false;
	}
	
	/**
	 * Ermittelt alle Ordner innerhalb des ki-Ordners, in denen installierte KIs sind und gibt diese zurueck.
	 * returns: Ein Array aus allen Ordner-Namen der installierten KIs.
	 */
	static function ermittleKIOrdner() {
		$root_path = "../extend/ki/";
		$directories = openDir($root_path);
		$dir_array = array();
		$ausnahmen = array("dokumentation", "ki_basis", ".", "..");
		while($dir = readdir($directories)){
			if(in_array($dir, $ausnahmen)) continue;
			$dir_array[] = $dir;
		}
		closeDir($directories);
		return $dir_array;
	}
	
	/**
	 * Ermittelt alle Ordner und deren Computer-Namen aller installierten KIs und gibt diese als Array zurueck.
	 * returns: Ein Array aus allen Daten der installierten KIs.
	 */
	static function ermittleKIDaten() {
		$ki_ordner = ki_basis::ermittleKIOrdner();
		$ki_daten = array();
		foreach($ki_ordner as $ki) {
			include("../extend/ki/".$ki."/holeDaten.php");
		}
		return $ki_daten;
	}
	
	/**
	 * Ermittlet alle Nicknames von KI-Spieler vom uebergebenen Spiel und gibt diese als Array zurueck.
	 * arguments: $sid - Die temporaere Datenbank-ID des Spiels, dessen KI-Spieler gesucht werden.
	 * returns: Die Nicknames der KI-Spieler des Spiels.
	 * Mitwirkende Autoren: myrbel
	 */	
	static function ermittleComputerSpieler($sid) {
		$computer_spieler = array();
		for($k=1; $k<=10; $k++) {
	        $spieler = @mysql_query("SELECT spieler_{$k}, spieler_{$k}_raus FROM skrupel_spiele 
	        	WHERE sid='$sid'");
	        $spieler = @mysql_fetch_array($spieler);
	        $spieler_id = $spieler['spieler_'.$k];
	        $spieler_raus = $spieler['spieler_'.$k.'_raus'];
	        if($spieler_id != 0 && $spieler_raus == 0) {
                $spieler_nick = @mysql_query("SELECT nick FROM skrupel_user WHERE id='$spieler_id'");
                $spieler_nick = @mysql_fetch_array($spieler_nick);
                $spieler_nick = $spieler_nick['nick'];
                if($spieler_nick != null) {
                    foreach(ki_basis::ermittleKIDaten() as $daten) {
                    	$nick = substr($spieler_nick, 0, strlen($daten['nick']));
                    	if($daten['nick'] == $nick) $computer_spieler[] = $spieler_id;
                    }
                }
	        }
		}
		return $computer_spieler;
	}
	
	/**
	 * Prueft, ob alle menschlichen Spieler im Spiel aufgegeben haben oder zerstoert wurden.
	 * returns: true, falls alle Menschen aufgegeben haben oder zerstoert wurden.
	 * 			false, sonst.
	 */
	static function alleMenschenRaus($sid) {
		$spieler_anzahl = @mysql_query("SELECT spieleranzahl FROM skrupel_spiele WHERE sid='$sid'");
		$spieler_anzahl = @mysql_fetch_array($spieler_anzahl);
		return ($spieler_anzahl['spieleranzahl'] == count(self::ermittleComputerSpieler($sid)));
	}
	
	/**
	 * Ueberprueft, ob alle Spieler, die nicht KI-Spieler sind, ihren Zug beendet haben.
	 * arguments: $sid - Die temporaere Datenbank-ID des Spiels.
	 * returns: true, falls alle menschlichen Spieler ihren Zug beendet haben.
	 * 			false, sonst.
	 */
	static function alleMenschenGezogen($sid) {
		$menschen_spieler_fertig = 0;
		for($k=1; $k<=10; $k++) {
			$spieler_k_zug = "spieler_".$k."_zug";
			$spieler_infos = @mysql_query("SELECT $spieler_k_zug, spieler_{$k} FROM skrupel_spiele 
				WHERE sid='$sid'");
			$spieler_infos = @mysql_fetch_array($spieler_infos);
			$spieler_gezogen = $spieler_infos[$spieler_k_zug];
			$spieler_id = $spieler_infos[1];
			if($spieler_id != 0) {
				$spieler_nick = @mysql_query("SELECT nick FROM skrupel_user WHERE id='$spieler_id'");
				$spieler_nick = @mysql_fetch_array($spieler_nick);
				if($spieler_nick != null && $spieler_nick['nick'] != null) {
					$spieler_nick = $spieler_nick['nick'];
					foreach(ki_basis::ermittleKIDaten() as $daten) {
						if($daten['nick'] != $spieler_nick) $menschen_spieler_fertig++;
					}
				}
			}
		}
		$spieler_anzahl = @mysql_query("SELECT spieleranzahl FROM skrupel_spiele WHERE sid='$sid'");
		$spieler_anzahl = @mysql_fetch_array($spieler_anzahl);
		$spieler_anzahl = $spieler_anzahl['spieleranzahl'];
		$menschen_spieler_anzahl = $spieler_anzahl - self::ermittleComputerSpieler($sid);
		return ($menschen_spieler_anzahl == $menschen_spieler_fertig);
	}
	
	/**
	 * Ermittelt den Winkel zwischen 3 Punkten.
	 * arguments: $a_x, $a_y - Die Koordinaten des ersten Punktes.
	 * 			  $mitte_x, $mitte_y - Die Koordinaten des mittleren Punktes.
	 * 			  $b_x, $b_y - Die Koordinaten des dritten Punktes.
	 * returns: Den Winkel zwischen den 3 Punkten in Grad.
	 */
	static function ermittleWinkel($a_x, $a_y, $mitte_x, $mitte_y, $b_x, $b_y) {
		$mitte_a_x = $a_x - $mitte_x;
		$mitte_a_y = $a_y - $mitte_y;
		$mitte_b_x = $b_x - $mitte_x;
		$mitte_b_y = $b_y - $mitte_y;
		$zaehler = ($mitte_a_x * $mitte_b_x) + ($mitte_a_y * $mitte_b_y);
		$nenner_1 = sqrt(($mitte_a_x * $mitte_a_x) + ($mitte_a_y * $mitte_a_y));
		$nenner_2 = sqrt(($mitte_b_x * $mitte_b_x) + ($mitte_b_y * $mitte_b_y));
		if($nenner_1 == 0 || $nenner_2 == 0) return null;
		$cos_a_mitte_b = $zaehler / ($nenner_1 * $nenner_2);
		return rad2deg(acos($cos_a_mitte_b));
	}
	
	/**
	 * Berechnet die Strecke zwischen den Start- und Ziel-Koordinaten.
	 * arguments: $x_start - Die X-Koordinate des Start-Punkts.
	 * 			  $y_start - Die Y-Koordinate des Start-Punkts.
	 * 			  $x_ziel - Die X-Koordinate des Ziel-Punkts.
	 * 			  $y_ziel - Die Y-Koordinate des Ziel-Punkts.
	 * returns: Die Strecke (in Lichtjahren) zwischen Start- und Ziel-Punkt
	 */
	static function berechneStrecke($x_start, $y_start, $x_ziel, $y_ziel) {
		$x_laenge = $x_ziel - $x_start;
		$y_laenge = $y_ziel - $y_start;
		return sqrt(($x_laenge * $x_laenge) + ($y_laenge * $y_laenge));
	}
	
	/**
	 * Berechnet den normalisierten Vektor fuer den uebergebenen Vektor und gibt diesen zurueck.
	 * arguments: $vektor_x - Die X-Komponente des Vektors.
	 * 			  $vektor_y - Die Y-Komponente des Vektors.
	 * returns: Ein Array aus den normalisierten Vektor-Komponenten.
	 */
	static function normiereVektor($vektor_x, $vektor_y) {
		if($vektor_x == 0 && $vektor_y == 0) return array('vektor_x'=>0, 'vektor_y'=>0);
		$vektor_faktor = 1 / sqrt(($vektor_x * $vektor_x) + ($vektor_y * $vektor_y));
		$vektor_x = $vektor_x * $vektor_faktor;
		$vektor_y = $vektor_y * $vektor_faktor;
		return array('vektor_x'=>$vektor_x, 'vektor_y'=>$vektor_y);
	}
	
	/**
	 * Berechnet den Punkt, den man erreicht, wenn man den uebegebenen Vektor um die 
	 * uebergebene Strecke verlaengert und gibt diesen als Array zurueck.
	 * arguments: $strecke - Die Strecke vom Start-Punkt zum Punkt, der berechnet werden soll.
	 * 			  $x_start - Die X-Koordinate des Start-Punkts.
	 * 			  $y_start - Die y-Koordinate des Start-Punkts.
	 * 			  $x_ziel - Die X-Koordinate des Punkts, in dessen Richtung der Vektor verlaeuft.
	 * 			  $y_ziel - Die Y-Koordinate des Punkts, in dessen Richtung der Vektor verlaeuft.
	 * returns: Den resultierenden Punkt als Array aus X- und Y-Koordinate.
	 */
	static function berechneWegPunkt($strecke, $x_start, $y_start, $x_ziel, $y_ziel) {
		$vektor_x = $x_ziel - $x_start;
		$vektor_y = $y_ziel - $y_start;
		$vektor_normal = ki_basis::normiereVektor($vektor_x, $vektor_y);
		$vektor_x = $vektor_normal['vektor_x'] * $strecke;
		$vektor_y = $vektor_normal['vektor_y'] * $strecke;
		return array($vektor_x + $x_start, $vektor_y + $y_start);
	}
	
	/**
	 * Prueft, ob die uebergebenen Koordinaten ausserhalb der Karte liegen.
	 * arguments: $x - Die X-Koordinate.
	 * 			  $y - Die Y-Koordinate.
	 * returns: true, falls der Punkt ausserhalb liegt.
	 * 			false, sonst.
	 */
	static function PunktAusserhalb($x, $y) {
		if($x < 0 || $y < 0) return true;
		$spiel_id = eigenschaften::$spiel_id;
		$umfang = @mysql_query("SELECT umfang FROM skrupel_spiele WHERE id='$spiel_id'");
		$umfang = @mysql_fetch_array($umfang);
		$umfang = $umfang['umfang'];
		return ($x > $umfang || $y > $umfang);
	}
	
	/**
	 * Ermittelt den naechst-gelegenen, wichtigen Planeten ausgehend von den Start-Koordinaten und
	 * den uebergebenen wichtigen Planeten.
	 * arguments: $x_start - Die X-Koordinate des Start-Punkts.
	 * 			  $y_start - Die Y-Koordinate des Start-Punkts.
	 * 			  $wichtige_planeten_ids - Die Datenbank-IDs der moeglichen Ziel-Planeten als Array.
	 * returns: Die X- und Y-Koordinaten sowie die Datenbank-ID des naechsten wichtigen Planeten als Array, 
	 * 			falls ein solcher Planet gefunden wurde.
	 * 			null, sonst.
	 */
	static function ermittleNahenWichtigenPlaneten($x_start, $y_start, $wichtige_planeten_ids) {
		$planeten_koords = array();
		foreach($wichtige_planeten_ids as $id) {
			$planeten_pos = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$id'");
			$planeten_pos = @mysql_fetch_array($planeten_pos);
			$planeten_koords[] = array('x'=>$planeten_pos['x_pos'], 'y'=>$planeten_pos['y_pos']);
		}
		$planeten_anzahl = count($wichtige_planeten_ids);
		$kurze_strecke = 9999;
		$x_nahe = 0; $y_nahe = 0;
		foreach($planeten_koords as $koords) {
			if($koords == null) continue;
			$x = $koords['x'];
			$y = $koords['y'];
			if($x == null || $y == null) continue;
			$strecke = ki_basis::berechneStrecke($x_start, $y_start, $x, $y);
			if($kurze_strecke > $strecke) {
				$kurze_strecke = $strecke;
				$x_nahe = $x;
				$y_nahe = $y;
			}
		}
		if($x_nahe == 0 && $y_nahe == 0) return null;
		$spiel_id = eigenschaften::$spiel_id;
		$planeten_id = @mysql_query("SELECT id FROM skrupel_planeten WHERE (spiel='$spiel_id') 
			AND (x_pos='$x_nahe') AND (y_pos='$y_nahe')");
		$planeten_id = @mysql_fetch_array($planeten_id);
		$planeten_id = $planeten_id['id'];
		if($planeten_id == null || $planeten_id == 0) return null;
		return array('x'=>$x_nahe, 'y'=>$y_nahe, 'id'=>$planeten_id);
	}
	
	/**
	 * Ermittelt den naechst-gelegenen Planeten ausgehend von den Start-Koordinaten.
	 * arguments: $x_start - Die X-Koordinate des Start-Punkts.
	 * 			  $y_start - Die Y-Koordinate des Start-Punkts.
	 * 			  $comp_id - Die Spieler-Nummer des KI-Spielers (1-10).
	 * 			  $ausnahmen - Die Datenbank-IDs der Planeten, die ignoriert werden sollen, als Array.
	 * 			  			   null, falls keine Ausnahmen gemacht werden sollen.
	 * 			  $spiel_id - Die Datenbank-ID des zugehoerigen Spiels.
	 * 			  $muss_bewohnt_sein - true, falls der naechste Planet vom uebergebenen Spieler besitzt 
	 * 								   werden soll.
	 * 								   false, sonst.
	 * 			  $sichtbar - true, falls der naechste Planet fuer den uebergebenen Spieler sichtbar sein soll.
	 * 						  false, sonst.
	 * returns: Die X- und Y-Koordinaten sowie die Datebank-ID des Ziels als Array, falls eine Ziel 
	 * 			gefunden wurde.
	 * 			null, falls kein Ziel gefunden wurde.
	 */
	static function ermittleNahenPlaneten($x_start, $y_start, $ausnahmen, $muss_bewohnt_sein, $sichtbar) {
		$ausnahmen_string = "";
		$besitzer = 0;
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		if($muss_bewohnt_sein) $besitzer = $comp_id;
		if($ausnahmen != null) {
			foreach($ausnahmen as $ausnahme) {
				$ausnahmen_string = $ausnahmen_string."(NOT (id='$ausnahme')) AND";
			}
		}
		$sicht_string = "";
		if($sichtbar) $sicht_string = "(sicht_".$comp_id."_beta=1) AND ";
		$temp_string = "SELECT x_pos, y_pos, id FROM skrupel_planeten WHERE ".$sicht_string.$ausnahmen_string." 
			(besitzer='$besitzer') AND (spiel='$spiel_id')";
		$nahe_planeten = @mysql_query($temp_string);
		$nahe_planeten_daten = array();
		while($planet = @mysql_fetch_array($nahe_planeten)) {
			$nahe_planeten_daten[] = array('x'=>$planet['x_pos'], 'y'=>$planet['y_pos'], 'id'=>$planet['id']);
		}
		$kurze_strecke = 9999;
		$naher_planet = null;
		foreach($nahe_planeten_daten as $daten) {
			if($daten == null) continue;
			if($daten['x'] == null || $daten['y'] == null) continue;
			$strecke = ki_basis::berechneStrecke($x_start, $y_start, $daten['x'], $daten['y']);
			if($kurze_strecke > $strecke) {
				$kurze_strecke = $strecke;
				$naher_planet = $daten;
			}
		}
		return $naher_planet;
	}
	
	/**
	 * Ermittelt das naechst-gelegene Ziel (Planet oder Schiff) ausgehend von den Start-Koordinaten.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen naechstes Ziel bestimmt werden soll.
	 * 			  $ziele_daten - Die Datenbank-IDs sowie die X- und Y-Koordinaten von moeglichen Zielen.
	 * 			  $spiel_id - Die Datenbank-ID des zugehoerigen Spiels.
	 * returns: Die X- und Y-Koordinaten und die Datenbank-ID des Ziels als Array.
	 * 			null, sonst.
	 */
	static function ermittleNahesZiel($schiff_id, $ziele_daten, $wurmloch_daten) {
		if($ziele_daten == null) return null;
		$schiff_koords = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_koords = @mysql_fetch_array($schiff_koords);
		$x_start = $schiff_koords['kox'];
		$y_start = $schiff_koords['koy'];
		$kurze_strecke = 99999;
		$nahes_ziel = null;
		$x_wurmloch = 0; 
		$y_wurmloch = 0; 
		$id_wurmloch = 0;
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		foreach($ziele_daten as $ziel) {
			if($ziel == null) continue;
			$x = $ziel['x'];
			$y = $ziel['y'];
			$id = $ziel['id'];
			if($x == null || $y == null) continue;
			$direkte_strecke = ki_basis::berechneStrecke($x_start, $y_start, $x, $y);
			$wurmloch_strecke = 99999;
			//Hier werden die uebergebenen Wurmloecher-Koordinaten benutzt:
			if($wurmloch_daten != null) {
				foreach($wurmloch_daten as $wurmloch) {
					if($wurmloch == null) continue;
					$wurmloch_x = $wurmloch['x'];
					$wurmloch_y = $wurmloch['y'];
					$wurmloch_id = $wurmloch['id'];
					$wurmloch_ziel = wurmloecher_basis::ermittleWurmlochZiel($wurmloch_id);
					if($wurmloch_ziel == null || $wurmloch_ziel['id'] == null) continue;
					$wurmloch_ziel_x = $wurmloch_ziel['x'];
					$wurmloch_ziel_y = $wurmloch_ziel['y'];
					if($wurmloch_ziel_x == null || $wurmloch_ziel_y == null) continue;
					$strecke_wurmloch1 = ki_basis::berechneStrecke($x_start, $y_start, 
																   $wurmloch_x, $wurmloch_y);
					$strecke_wurmloch2 = ki_basis::berechneStrecke($wurmloch_ziel_x, 
																   $wurmloch_ziel_y, $x, $y);
					$wurmloch_strecke_temp = $strecke_wurmloch1 + $strecke_wurmloch2;
					if($strecke_wurmloch1 == 0 || $strecke_wurmloch2 == 0) continue;
					if($wurmloch_strecke_temp < $wurmloch_strecke) {
						$wurmloch_strecke = $wurmloch_strecke_temp;
						$x_wurmloch = $wurmloch_x;
						$y_wurmloch = $wurmloch_y;
						$id_wurmloch = $wurmloch_id;
					}
				}
			}
			//Nun wird entschieden, ob ein kuerzerer weg gefunden wurde:
			if($kurze_strecke > $direkte_strecke && $wurmloch_strecke > $direkte_strecke) {
				$kurze_strecke = $direkte_strecke;
				$nahes_ziel = array('x'=>$x, 'y'=>$y, 'id'=>$id);
				$x_wurmloch = 0; $y_wurmloch = 0; $id_wurmloch = 0;
			}
			elseif($kurze_strecke > $wurmloch_strecke && $direkte_strecke > $wurmloch_strecke) {
				$kurze_strecke = $wurmloch_strecke;
				$nahes_ziel = array('x'=>$x, 'y'=>$y, 'id'=>$id);
			} else { $x_wurmloch = 0; $y_wurmloch = 0; $id_wurmloch = 0; }
		}
		//Wurde kein Ziel gefunden, wird null zurueckgegeben.
		if($nahes_ziel == null || $nahes_ziel['id'] == null || $nahes_ziel['id'] == 0 
		|| ($nahes_ziel['x'] == 0 && $nahes_ziel['y'] == 0)) return null;
		//Soll ein Wurmloch benutzt werden, so wird dies in der Datenbank vermerkt.
		if($id_wurmloch != 0) {
			$routing_koord = "".$x_wurmloch.":".$y_wurmloch.":".$id_wurmloch.":"
							 .$nahes_ziel['x'].":".$nahes_ziel['y'].":".$nahes_ziel['id']."";
			@mysql_query("UPDATE skrupel_schiffe SET routing_schritt=2, routing_koord='$routing_koord' 
				WHERE id='$schiff_id'");
			$nahes_ziel = array('x'=>$x_wurmloch, 'y'=>$y_wurmloch, 0);
		}
		else @mysql_query("UPDATE skrupel_schiffe SET routing_schritt=0, routing_koord='' WHERE id='$schiff_id'");
		return $nahes_ziel;
	}
	
	/**
	 * Ermittelt die Koordinaten fuer die Flucht des uebergebenen Schiffes von den uebergebenen 
	 * Flucht-Koordinaten.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das fluechtet.
	 * 			  $flucht_x - Die X-Koodinaten des Punktes, von dem gefluechtet wird.
	 * 			  $flucht_y - Die Y-Koodinaten des Punktes, von dem gefluechtet wird.
	 * returns: Die Ziel-Koordinaten als Array, zu denen gefluechtet wird.
	 * Mitwirkende Autoren: Jonu.
	 */
	static function ermittleFluchtKoordinaten($schiff_id, $flucht_x, $flucht_y) {
		$schiff_daten = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$schiff_x = $schiff_daten['kox'];
		$schiff_y = $schiff_daten['koy'];
		$spiel_id = eigenschaften::$spiel_id;
		$spiel_umfang = @mysql_query("SELECT umfang FROM skrupel_spiele WHERE id='$spiel_id'");
		$spiel_umfang = @mysql_fetch_array($spiel_umfang);
		$spiel_umfang = $spiel_umfang['umfang'];
		$abstand_x = abs($flucht_x - $schiff_x);
		$abstand_y = abs($flucht_y - $schiff_y);
		$faktor = ceil(100 / sqrt($abstand_x * $abstand_x + $abstand_y * $abstand_y));
		if($schiff_x < $flucht_x) $flucht_ziel_x = $schiff_x - $faktor * $abstand_x;
		else $flucht_ziel_x = $schiff_x + $faktor * $abstand_x;
		if($schiff_y < $flucht_y) $flucht_ziel_y = $schiff_y - $faktor * $abstand_y;
		else $flucht_ziel_y = $schiff_y + $faktor * $abstand_y;
		if($flucht_ziel_x < 0) $flucht_ziel_x = 0;
		elseif($flucht_ziel_x > $spiel_umfang) $flucht_ziel_x = $spiel_umfang;
		if($flucht_ziel_y < 0) $flucht_ziel_y = 0;
		elseif($flucht_ziel_y > $spiel_umfang) $flucht_ziel_y = $spiel_umfang;
		return array('x'=>$flucht_ziel_x, 'y'=>$flucht_ziel_y);
	}
}
?>