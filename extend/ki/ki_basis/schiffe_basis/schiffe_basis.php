<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Abstrakte Oberklasse fuer Schiffe im allgemeinen.
 */
abstract class schiffe_basis {
	
	/**
	 * Setzt fuer das uebergebene Schiff den uebergebenen Aggressivitaets-Wert.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Aggressivitaet gesetzt wird.
	 * 			  $aggro - Die zu setzende Aggressivitaet (0-9).
	 */
	function setzeAggressivitaet($schiff_id, $aggro) {
		@mysql_query("UPDATE skrupel_schiffe SET aggro='$aggro' WHERE id='$schiff_id'");
	}
	
	/**
	 * Setzt fuer das uebergebene Schiff den uebergebenen Taktik-Wert. Der Wert wird natuerlich nur veraendert, 
	 * wenn das Taktik-Modul im Spiel aktiv ist.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Taktik gesetzt wird.
	 * 			  $taktik - Der zusetzende Taktik-Wert (0: Ueberlegt, 1: Aggressiv, 2: Defensiv).
	 */
	function setzeTaktik($schiff_id, $taktik) {
		if(ki_basis::ModulAktiv("taktik")) 
			@mysql_query("UPDATE skrupel_schiffe SET mission='$taktik' WHERE id='$schiff_id'");
	}
	
	/**
	 * Ueberprueft, ob es fuer das uebergebene Schiff im Moment Sinn macht, sich zu tarnen.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffes, dass sich tarnen soll.
	 */
	function aktiviereTarnung($schiff_id) {
		//Ist nicht genug Rennurbin auf dem Schiff, so wird keine Tarnung aktiviert.
		if($this->ermittleSchiffResource($schiff_id, "min2") < eigenschaften::$schiffe_infos->min_ren_tarnung) 
			return;
		foreach(eigenschaften::$gegner_ids as $gegner_id) {
			$sicht = "sicht_".$gegner_id;
			$sicht_beta = "sicht_".$gegner_id."_beta";
			$schiff_sicht = @mysql_query("SELECT $sicht, $sicht_beta FROM skrupel_schiffe 
				WHERE id='$schiff_id'");
			$schiff_sicht = @mysql_fetch_array($schiff_sicht);
			$sicht1 = $schiff_sicht[$sicht];
			$sicht2 = $schiff_sicht[$sicht_beta];
			if($sicht1 == 1 && $sicht2 == 0) {
				@mysql_query("UPDATE skrupel_schiffe SET spezialmission=8 WHERE id='$schiff_id'");
				return;
			}
		}
		@mysql_query("UPDATE skrupel_schiffe SET spezialmission=0 WHERE id='$schiff_id'");
	}
	
	/**
	 * Ermittelt das Waffensystem des Schiffes, das die groesste Anzahl hat und gibt diese Anzahl zurueck.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffes, dessen maximale Waffenanzahl ermittelt wird.
	 * returns: Die maximale Waffen-Anzahl des Schiffs.
	 */
	function ermittleMaxWaffenAnzahl($schiff_id) {
		$waffen = @mysql_query("SELECT energetik_anzahl, projektile_anzahl, hanger_anzahl FROM skrupel_schiffe 
				WHERE id='$schiff_id'");
		$waffen = @mysql_fetch_array($waffen);
		if($waffen['energetik_anzahl'] >= $waffen['projektile_anzahl'] 
		&& $waffen['energetik_anzahl'] >= $waffen['hanger_anzahl']) return $waffen['energetik_anzahl'];
		elseif($waffen['projektile_anzahl'] >= $waffen['energetik_anzahl'] 
		&& $waffen['projektile_anzahl'] >= $waffen['hanger_anzahl']) return $waffen['projektile_anzahl'];
		else return $waffen['hanger_anzahl'];
	}
	
	/**
	 * Ermittelt alle Planeten, die bekannt fuer ihre unerwuenschte Dominante Spezies sind, aber deren 
	 * Temperatur fuer die Rasse der KI noch in Ordnng ist und gibt deren Datenbank-IDs als Array zurueck.
	 * returns: Alle Datenbank-IDs als Array von Planeten mit unerwuenschter Spezies, aber akzeptabler 
	 * Temperatur.
	 */
	static function ermittlePlanetenFuerViraleInvasionSchiffe() {
		$comp_id = eigenschaften::$comp_id;
		$spiel_id = eigenschaften::$spiel_id;
		$schlechte_planeten = @mysql_query("SELECT p.id, p.temp FROM skrupel_ki_planeten k, 
				skrupel_planeten p WHERE (k.extra=1) AND (k.comp_id='$comp_id') AND 
				(k.planeten_id=p.id) AND (p.spiel='$spiel_id')");
		$planeten = array();
		$optimale_temp = ki_basis::ermittleOptimaleTemp();
		while($planeten_info = @mysql_fetch_array($schlechte_planeten)) {
			$temp = $planeten_info['temp'] - 35;
			if($optimale_temp == 0 || abs($temp - $optimale_temp) < 30) $planeten[] = $planeten_info['id'];
		}
		return $planeten;
	}
	
	/**
	 * Bestimmt, welche Erkundungs-Funktion benutzt wird. Diese Funktion ist nur eine Wrapper-Funktion!
	 * Hier wird die Funktion schiffe_basis::ermittleErkundungsZiel() verwendet.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das erkunden soll.
	 * returns: Die Rueckgabe der verwendeten Erkundungs-Funktion.
	 */
	function erkunde($schiff_id) {
		return self::ermittleErkundungsZiel($schiff_id);
	}
	
	/**
	 * Diese Funktion ermittelt einen Planeten fuer ein Schiff, zu den er fliegen soll.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, der erkunden soll.
	 * returns: Ein Array aus X- und Y-Koordinate sowie der Datenbank-ID (falls es nicht nur Koordinaten sind) 
	 * 			des Ziels.
	 * 			null, falls kein Ziel-Planet gefunden wurde.
	 */
	static function ermittleErkundungsZiel($schiff_id) {
		//Zuerst wird versucht, ein sichtbares Wurmloch zu erkunden. Es wird nur erkundet, falls es noch 
		//unbekannt ist oder bekannt ist, dass es instabil ist.
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$schiff_koords = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_koords = @mysql_fetch_array($schiff_koords);
		$x = $schiff_koords['kox'];
		$y = $schiff_koords['koy'];
		foreach(eigenschaften::$gesehene_wurmloecher_daten as $wurmloch_daten) {
			if(in_array($wurmloch_daten, eigenschaften::$bekannte_wurmloch_daten) 
			|| in_array($wurmloch_daten, eigenschaften::$bekannte_instabile_wurmloch_daten)) continue;
			//Falls das Wurmloch zu weit entfernt ist, wird es nicht angeflogen.
			$wurmloch_x = $wurmloch_daten['x'];
			$wurmloch_y = $wurmloch_daten['y'];
			$entfernung = floor(ki_basis::berechneStrecke($x, $y, $wurmloch_x, $wurmloch_y));
			if($entfernung > eigenschaften::$schiffe_infos->max_wurmloch_erkundungs_reichweite) continue;
			$schiffe_zum_wurmloch = @mysql_query("SELECT sum(id) FROM skrupel_schiffe 
				WHERE (besitzer='$comp_id') AND (spiel='$spiel_id') AND (zielx='$wurmloch_x') 
				AND (ziely='$wurmloch_y]')");
			$schiffe_zum_wurmloch = @mysql_fetch_array($schiffe_zum_wurmloch);
			if($schiffe_zum_wurmloch['sum(zielid)'] == null || $schiffe_zum_wurmloch['sum(zielid)'] == 0) 
				return array('x'=>$wurmloch_daten['x'], 'y'=>$wurmloch_daten['y'], 0);
		}
		//Ansonsten wird ein Ziel festgelegt, dass weiter weg ist.
		$spiel_umfang = @mysql_query("SELECT umfang FROM skrupel_spiele WHERE id='$spiel_id'");
		$spiel_umfang = @mysql_fetch_array($spiel_umfang);
		$spiel_umfang = $spiel_umfang['umfang'];
		$sektor_anzahl = floor($spiel_umfang / eigenschaften::$schiffe_infos->sektor_erkundung_groesse);
		$sektor_anzahl = $sektor_anzahl * $sektor_anzahl;
		$koordinaten = array();
		$x = eigenschaften::$schiffe_infos->sektor_erkundung_groesse / 2;
		$y = $x;
		for($i=1; $i <= $sektor_anzahl; $i++) {
			if($x + eigenschaften::$schiffe_infos->sektor_erkundung_groesse >= $spiel_umfang) {
				$x = eigenschaften::$schiffe_infos->sektor_erkundung_groesse / 2;
				$y += eigenschaften::$schiffe_infos->sektor_erkundung_groesse;
			}
			if($y > $spiel_umfang) break;
			$sektor_koords = array('x'=>$x, 'y'=>$y);
			$koordinaten[] = $sektor_koords;
			$x += eigenschaften::$schiffe_infos->sektor_erkundung_groesse;
		}
		shuffle($koordinaten);
		$planeten_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE (spiel='$spiel_id') 
			AND (besitzer='$comp_id')");
		$eigene_planeten = array();
		while($koords = @mysql_fetch_array($planeten_koords)) {
			$eigene_planeten[] = array('x'=>$koords['x_pos'], 'y'=>$koords['y_pos']);
		}
		$wenigste_planeten = 9999;
		$bester_sektor = null;
		foreach($koordinaten as $koords) {
			$anzahl_eigener_planeten = 0;
			foreach($eigene_planeten as $planet) {
				$distanz = floor(ki_basis::berechneStrecke($koords['x'], $koords['y'], 
														   $planet['x'], $planet['y']));
				if($distanz <= eigenschaften::$schiffe_infos->sektor_erkundung_groesse * 2) 
					$anzahl_eigener_planeten++;
			}
			if($wenigste_planeten > $anzahl_eigener_planeten) {
				$wenigste_planeten = $anzahl_eigener_planeten;
				$bester_sektor = $koords;
			}
		}
		return array('x'=>$bester_sektor['x'], 'y'=>$bester_sektor['y'], 0);
	}
	
	/**
	 * Ermittelt die Menge der uebergebenen Resource an Bord des uebergebenen Schiffs.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs.
	 * 			  $resource - Der (Datenbank-)Name des Resource.
	 * returns: Die Menge der Resource am Bord des Schiffs.
	 */
	function ermittleSchiffResource($schiff_id, $resource) {
		$resource = "fracht_".$resource;
		$res = @mysql_query("SELECT $resource FROM skrupel_schiffe WHERE id='$schiff_id'");
		$res = @mysql_fetch_array($res);
		return $res[$resource];
	}
	
	/**
	 * Belaedt das uebergebene Schiff mit der uebergebene Menge an Resourcen.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffes, das beladen werden soll.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, an dem das Schiff ist.
	 * 			  $resource - Die Resource, die geladen werden soll.
	 * 			  $menge - Die Menge der Resource, die gelanden werden soll.
	 */
	function ladeResourceAufSchiff($schiff_id, $planeten_id, $resource, $menge) {
		$schiff_daten = @mysql_query("SELECT frachtraum, fracht_leute, fracht_vorrat, fracht_min1, fracht_min2, 
			fracht_min3 FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$frachtraum = $schiff_daten['frachtraum'];
		$fracht_masse = ($schiff_daten['fracht_leute'] / 100) + $schiff_daten['fracht_vorrat'] 
			+ $schiff_daten['fracht_min1'] + $schiff_daten['fracht_min2'] + $schiff_daten['fracht_min3'];
		$frachtraum -= $fracht_masse;
		if($frachtraum == 0) return;
		$planet_res = "planet_".$resource;
		$planet_menge = @mysql_query("SELECT $planet_res FROM skrupel_planeten WHERE id='$planeten_id'");
		$planet_menge = @mysql_fetch_array($planet_menge);
		$planet_menge = $planet_menge[$planet_res];
		if($planet_menge == 0) return;
		$schiff_menge = $menge;
		if($schiff_menge > $frachtraum) $schiff_menge = $frachtraum;
		if($schiff_menge > $planet_menge) $schiff_menge = $planet_menge;
		$planet_menge = $planet_menge - $schiff_menge;
		$fracht_string = "fracht_".$resource;
		@mysql_query("UPDATE skrupel_schiffe SET $fracht_string='$schiff_menge' WHERE id='$schiff_id'");
		@mysql_query("UPDATE skrupel_planeten SET $planet_res='$planet_menge' WHERE id='$planeten_id'");
	}
	
	/**
	 * Ermittelt, ob das uebergebene Schiff die uebergebene Fertigkeit besitzt.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das auf eine Spezialfaehigkeit hin 
	 * 						   ueberprueft wird.
	 * 			  $fertigkeit - Die Fertigkeit, die ueberprueft werden soll.
	 * returns: true, falls das Schiff die Fertigkeit hat.
	 * 			false, sonst.
	 */
	static function SchiffHatFertigkeit($schiff_id, $fertigkeit) {
		$schiff_fertigkeiten = @mysql_query("SELECT fertigkeiten FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_fertigkeiten = @mysql_fetch_array($schiff_fertigkeiten);
		$schiff_fertigkeiten = $schiff_fertigkeiten['fertigkeiten'];
		switch($fertigkeit) {
			case "srv" : return substr($schiff_fertigkeiten,23,1)+0 != 0;
			case "sensorenphalanx" : return substr($schiff_fertigkeiten,24,1)+0 == 1;
			case "labor" : return substr($schiff_fertigkeiten,24,1)+0 == 2;
			case "lloyd" : return substr($schiff_fertigkeiten,38,2)+0 != 0;
			case "cluster" : return substr($schiff_fertigkeiten,0,2)+0 != 0;
			case "quark" : return substr($schiff_fertigkeiten,7,1)+0 != 0;
			case "cyber" : return substr($schiff_fertigkeiten,48,2)+0 != 0;
			case "terrawarm" : return substr($schiff_fertigkeiten,5,1)+0 != 0;
			case "terrakalt" : return substr($schiff_fertigkeiten,6,1)+0 != 0;
			case "sprungtriebwerk" : return substr($schiff_fertigkeiten,11,3)+0 != 0;
			case "viraleinvasion" : return substr($schiff_fertigkeiten,43,3)+0 != 0;
			case "destabilisator" : return substr($schiff_fertigkeiten,50,2)+0 != 0;
			case "taster" : return substr($schiff_fertigkeiten,52,1)+0 != 0;
			case "tarnung" : return substr($schiff_fertigkeiten,22,1)+0 != 0;
		}
		return false;
	}
	
	/**
	 * Prueft, ob das uebergebene Schiff in der Datenbank als aktives Spezialschiff aufgefuehrt wird und 
	 * gibt dies zurueck.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das ueberprueft werden soll.
	 * returns: true, falls das Schiff eine aktivierte Spezialmission hat.
	 * 			false, sonst.
	 */
	function istAktivesSpezialSchiff($schiff_id) {
		$ist_aktiv = @mysql_query("SELECT aktiv FROM skrupel_ki_spezialschiffe WHERE schiff_id='$schiff_id'");
		$ist_aktiv = @mysql_fetch_array($ist_aktiv);
		$ist_aktiv = $ist_aktiv['aktiv'];
		if($ist_aktiv == null) return false;
		return ($ist_aktiv == 1);
	}
	
	/**
	 * Ermittelt Information ueber die uebergebene Schiffs-Klasse.
	 * arguments: $klassen_id - Die Klassen-ID des Schiffs.
	 * 			  $info_index - Der Index im Schiffs-Array, der auf die gewuenschte Information zeigt.
	 * returns: Die Information an der Stelle im Array des Schiffes.
	 */
	static function ermittleSchiffInfo($klassen_id, $info_index) {
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			if($klassen_id == $schiff_array[1]) return $schiff_array[$info_index];
		}
	}
	
	/**
	 * Prueft, ob das Schiff mit der uebergebenen Klassen-ID der uebergebenen Rasse ein Schiff mit viraler 
	 * Invasion ist.
	 * arguments: $klassen_id - Die Klassen-ID des zu ueberpruefenden Schiffs.
	 * 			  $rasse - Die Rasse des zu ueberpruefenden Schiffs.
	 * returns: true, falls das uebergebene Schiff virale Invasion hat.
	 * 			false, sonst.
	 */
	static function istViralesSchiff($klassen_id, $rasse) {
		$viral_array = self::ermittleViraleInvasionIDs();
		foreach($viral_array as $virale_infos) {
			if($virale_infos['id'] == $klassen_id && $virale_infos['rasse'] == $rasse) return true;
		}
		return false;
	}
	
	/**
	 * Ermittelt alle Datenbank-IDs der Schiffe des KI-Spielers. Alle IDs werden in eigenschaften::$schiff_ids 
	 * geschrieben und je nach Schiff-Typ werden sie in eigenschaften::$cluster_ids, 
	 * eigenschaften::$quark_ids, eigenschaften::$frachter_ids oder eigenschaften::$jaeger_ids 
	 * geschrieben.
	 */
	function ermittleSchiffIDs() {
		$comp_id = eigenschaften::$comp_id;
		$spiel_id = eigenschaften::$spiel_id;
		$schiffe = @mysql_query("SELECT id, frachtraum, energetik_anzahl, projektile_anzahl, hanger_anzahl, 
			klasseid, fertigkeiten, volk FROM skrupel_schiffe WHERE (besitzer='$comp_id') AND 
			(spiel='$spiel_id')");
		$cluster_schiff = $this->ermittleClusterSchiffID();
		$quark_schiff = $this->ermittleQuarkSchiffID(); 
		$cyber_schiff = $this->ermittleCyberSchiffID();
		$terra_warm = $this->ermittleTerraWarmID();
		$terra_kalt = $this->ermittleTerraKaltID();
		while($schiff = @mysql_fetch_array($schiffe)) {
			eigenschaften::$schiff_ids[] = $schiff['id'];
			$waffen = $schiff['energetik_anzahl'] + $schiff['projektile_anzahl'] + $schiff['hanger_anzahl'];
			if($this->istViralesSchiff($schiff['klasseid'], $schiff['volk'])) 
				eigenschaften::$virale_ids[] = $schiff['id'];
			elseif(substr($schiff['fertigkeiten'],52,1) != 0) eigenschaften::$taster_ids[] = $schiff['id'];
			if($schiff['klasseid'] == $cluster_schiff['id'] && $schiff['volk'] == $cluster_schiff['rasse']) 
				eigenschaften::$cluster_ids[] = $schiff['id'];
			elseif($schiff['klasseid'] == $quark_schiff['id'] && $schiff['volk'] == $quark_schiff['rasse']) 
				eigenschaften::$quark_ids[] = $schiff['id'];
			elseif($schiff['klasseid'] == $cyber_schiff['id'] && $schiff['volk'] == $cyber_schiff['rasse']) 
				eigenschaften::$cyber_ids[] = $schiff['id'];
			elseif($schiff['klasseid'] == $terra_warm['id'] && $schiff['volk'] == $terra_warm['rasse']) 
				eigenschaften::$terra_warm_ids[] = $schiff['id'];
			elseif($schiff['klasseid'] == $terra_kalt['id'] && $schiff['volk'] == $terra_kalt['rasse']) 
				eigenschaften::$terra_kalt_ids[] = $schiff['id'];
			elseif($schiff['frachtraum'] >= eigenschaften::$frachter_infos->min_frachtraum_frachter 
			&& $waffen <= eigenschaften::$frachter_infos->max_frachter_waffen) 
				eigenschaften::$frachter_ids[] = $schiff['id'];
			elseif($waffen >= eigenschaften::$scouts_infos->min_scout_waffen 
			&& $waffen <= eigenschaften::$scouts_infos->max_scout_waffen) 
				eigenschaften::$scout_ids[] = $schiff['id'];
			elseif($waffen >= eigenschaften::$jaeger_infos->min_jaeger_waffen) 
				eigenschaften::$jaeger_ids[] = $schiff['id'];
		}
	}
	
	/**
	 * Ermittelt die Klassen-ID und die Rasse des Schiffs, welches die Spezialfaehigkeit
	 * SubpartikelCluster hat.
	 * returns: Die Klassen-ID und die Rasse des gesuchten Schiffs.
	 * 			null, falls es kein Subpartikelcluster-Schiff gibt.
	 */
	static function ermittleClusterSchiffID() {
		$cluster_schiff_id = null;
		$rasse = null;
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$spezial_string = $schiff_array[17];
			$substring = substr($spezial_string, 0, 2);
			if($substring != 0) {
				$cluster_schiff_id = $schiff_array[1];
				$rasse_index = count($schiff_array) - 1;
				$rasse = $schiff_array[$rasse_index];
				break;
			}
		}
		if($cluster_schiff_id == null) return null;
		return array('id'=>$cluster_schiff_id, 'rasse'=>$rasse);
	}
	
	/**
	 * Ermittelt die Klassen-ID und die Rasse des Schiffs, welches die Spezialfaehigkeit
	 * Quarksreorganisator hat.
	 * returns: Die Klassen-ID und die Rasse des gesuchten Schiffs.
	 * 			null, falls es kein Quarksreorganisator-Schiff gibt.
	 */
	static function ermittleQuarkSchiffID() {
		$quark_schiff_id = null;
		$rasse = null;
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$spezial_string = $schiff_array[17];
			$sub_1 = substr($spezial_string, 7, 1);
			if($sub_1 != 0) {
				$quark_schiff_id = $schiff_array[1];
				$rasse_index = count($schiff_array) - 1;
				$rasse = $schiff_array[$rasse_index];
				break;
			}
		}
		if($quark_schiff_id == null) return null;
		return array('id'=>$quark_schiff_id, 'rasse'=>$rasse);
	}
	
	/**
	 * Ermittelt die Klassen-ID und die Rasse des Schiffs, welches die Spezialfaehigkeit 
	 * Cybernrittnikk hat.
	 * returns: Die Klassen-ID und die Rasse des gesuchten Schiffs.
	 * 			null, falls es kein Cyber-Schiff gibt.
	 */
	static function ermittleCyberSchiffID() {
		$cyber_schiff_id = null;
		$rasse = null;
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$spezial_string = $schiff_array[17];
			$sub_1 = substr($spezial_string, 48, 2);
			if($sub_1 != 0) {
				$cyber_schiff_id = $schiff_array[1];
				$rasse_index = count($schiff_array) - 1;
				$rasse = $schiff_array[$rasse_index];
				break;
			}
		}
		if($cyber_schiff_id) return null;
		return array('id'=>$cyber_schiff_id, 'rasse'=>$rasse);
	}
	
	/**
	 * Ermittelt die Klassen-ID und die Rasse eines Schiffes mit Terraformer (Temperatur-Erhoehend).
	 * returns: Die Klassen-ID und die Rasse eines Schiffes mit warmen Terraformer.
	 * 			null, falls es kein Schiff mit warmen Terraformer gibt.
	 */
	static function ermittleTerraWarmID() {
		$terra_warm_schiff_id = null;
		$rasse = null;
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$spezial_string = $schiff_array[17];
			$sub_string = substr($spezial_string, 5, 1)+0;
			if($sub_string != 0) {
				$terra_warm_schiff_id = $schiff_array[1];
				$rasse_index = count($schiff_array) - 1;
				$rasse = $schiff_array[$rasse_index];
				break;
			}
		}
		if($terra_warm_schiff_id == null) return null;
		return array('id'=>$terra_warm_schiff_id, 'rasse'=>$rasse);;
	}
	
	/**
	 * Ermittelt die Klassen-ID und die Rasse eines Schiffes mit Terraformer (Temperatur-Senkend).
	 * returns: Die Klassen-ID und die Rasse eines Schiffes mit kalten Terraformer.
	 * 			null, falls es kein Schiff mit warmen Terraformer gibt.
	 */
	static function ermittleTerraKaltID() {
		$terra_kalt_schiff_id = null;
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$spezial_string = $schiff_array[17];
			$sub_string = substr($spezial_string, 6, 1)+0;
			if($sub_string != 0) {
				$terra_kalt_schiff_id = $schiff_array[1];
				$rasse_index = count($schiff_array) - 1;
				$rasse = $schiff_array[$rasse_index];
				break;
			}
		}
		if($terra_kalt_schiff_id == null) return null;
		return array('id'=>$terra_kalt_schiff_id, 'rasse'=>$rasse);
	}
	
	/**
	 * Ermittelt alle Klassen-IDs von Schiffen mit viraler Invasion und gibt diese als 
	 * Array zurueck.
	 * returns: Alle Klassen-IDs und Rassen von viraler Invasion-Schiffen als Array.
	 */
	static function ermittleViraleInvasionIDs() {
		$virale_ids = array();
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$spezial_string = $schiff_array[17];
			$sub_string = substr($spezial_string, 43, 3);
			if($sub_string+0 != 0) {
				$rasse_index = count($schiff_array) - 1;
				$rasse = $schiff_array[$rasse_index];
				$viral_array = array('id'=>$schiff_array[1], 'rasse'=>$rasse);
				$virale_ids[] = $viral_array;
			}
		}
		return $virale_ids;
	}
	
	/**
	 * Aktiviert oder deaktiviert ein Schiff in dder Tabelle skrupel_ki_spezialschiffe.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffes, dessen Aktivierung geaendert werden soll.
	 * 			  $aktiv - 0, falls es deaktiviert werden soll.
	 * 					   1, sonst.
	 */
	static function deaktiviereSpezialschiff($schiff_id, $aktiv) {
		@mysql_query("UPDATE skrupel_ki_spezialschiffe SET aktiv='$aktiv' WHERE schiff_id='$schiff_id'");
	}
	
	/**
	 * Ermittelt den freien Frachtraum des uebergebenen Schiffs.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen verfuegbarer Frachtraum ermittelt wird.
	 * returns: Den freien Frachtraum des Schiffs.
	 */
	function ermittleFreienFrachtraum($schiff_id) {
		$schiff_infos = @mysql_query("SELECT fracht_vorrat, fracht_min1, fracht_min2, fracht_min3, 
			fracht_leute, frachtraum FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$frachtraum = $schiff_infos['frachtraum'];
		if($frachtraum == 0) return 0;
		$schiffvorrat = $schiff_infos['fracht_vorrat'];
		$schiffmin1 = $schiff_infos['fracht_min1'];
		$schiffmin2 = $schiff_infos['fracht_min2'];
		$schiffmin3 = $schiff_infos['fracht_min3'];
		$schiffleute = $schiff_infos['fracht_leute'];
		return $frachtraum - ($schiffvorrat + $schiffmin1 + $schiffmin2 + $schiffmin3 + $schiffleute / 100);
	}
	
	/**
	 * Ermittelt die maximale Warp-Geschwindigkeit eines Schiffs.
	 * arguments: $schiff_id - Die Datenbank-ID des relevanten Schiffs.
	 * returns: Die maximale Warp-Geschwindigkeit des Schiffs.
	 */
	static function ermittleMaximumWarp($schiff_id) {
		$antrieb_stufe = @mysql_query("SELECT antrieb FROM skrupel_schiffe WHERE id='$schiff_id'");
		$antrieb_stufe = @mysql_fetch_array($antrieb_stufe);
		$antrieb_stufe = $antrieb_stufe['antrieb'];
		if($antrieb_stufe > 8) $antrieb_stufe--;
		return $antrieb_stufe;
	}
	
	/**
	 * Ermittelt die Scanner-Reichweite des uebergebenen Schiffes.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Scanner-Reichweite gesucht wird.
	 * returns: Die Scanner-Reichweite des Schiffs.
	 */
	function ermittleSchiffScanweite($schiff_id) {
		$sensor = @mysql_query("SELECT scanner FROM skrupel_schiffe WHERE id='$schiff_id'");
		$sensor = @mysql_fetch_array($sensor);
		$sensor = $sensor['scanner'];
		if($sensor == 0) return 47;
		if($sensor == 1) return 83;
		else return 116;
	}
	
	/**
	 * Prueft, ob das uebergebene Schiff zu weit vom uebergebenen Minenfeld entfernt, um es zu raeumen und 
	 * setzt gegebenenfalls einen Kurs, damit das Schiff das Minenfeld raeumen kann.
	 * Es wird nicht ueberprueft, ob sich das Schiff innerhalb das Minenfeldes befindet!
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das ein Minenfeld raeumen soll.
	 * 			  $minen_feld_id - Die Datenbank-ID des zu raeumenden Minenfeldes.
	 * returns: true, falls ein Kurs gesetzt wurde.
	 * 			false, sonst.
	 */
	function fliegeZuMinenfeldRand($schiff_id, $minen_feld_id) {
		$schiff_koords = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_koords = @mysql_fetch_array($schiff_koords);
		$schiff_x = $schiff_koords['kox'];
		$schiff_y = $schiff_koords['koy'];
		$minenfeld_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_anomalien 
			WHERE id='$minen_feld_id'");
		$minenfeld_koords = @mysql_fetch_array($minenfeld_koords);
		$minenfeld_x = $minenfeld_koords['x_pos'];
		$minenfeld_y = $minenfeld_koords['y_pos'];
		if($minenfeld_x == null || $minenfeld_y == null || ($minenfeld_x == 0 && $minenfeld_y == 0)) 
			return false;
		$entfernung = ki_basis::berechneStrecke($schiff_x, $schiff_y, $minenfeld_x, $minenfeld_y);
		if($entfernung > 95) {
			$vektor_x = $minenfeld_x - $schiff_x;
			$vektor_y = $minenfeld_y - $schiff_y;
			$vektor_normal = ki_basis::normiereVektor($vektor_x, $vektor_y);
			$ziel_entfernung = $entfernung - 95;
			$vektor_x = $vektor_normal['vektor_x'] * $ziel_entfernung;
			$vektor_y = $vektor_normal['vektor_y'] * $ziel_entfernung;
			$ziel_x = $vektor_x + $schiff_x;
			$ziel_y = $vektor_y + $schiff_y;
			$spiel_id = eigenschaften::$spiel_id;
			$planeten_id = @mysql_query("SELECT id FROM skrupel_planeten WHERE (spiel='$spiel_id') 
				AND (x_pos='$ziel_x') AND (y_pos='$ziel_y')");
			$planeten_id = @mysql_fetch_array($planeten_id);
			if($planeten_id != null && $planeten_id['id'] != null && $planeten_id != 0) {
				$ziel_x += 2;
				$ziel_y += 2;
			}
			$warp = $this->ermittleMaximumWarp($schiff_id);
			$this->fliegeSchiff($schiff_id, $ziel_x, $ziel_y, $warp, null);
			return true;
		}
		return false;
	}
	
	/**
	 * Ermittelt den Planeten, an dem das angegebene Schiff als letztes war, wenn das Schiff eine Ernte-Route
	 * abfliegt.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen letzter Routen-Planet gesucht wird.
	 * returns: Die Datenbank-ID des Planeten, bei dem das angegebene Schiff als letztes innerhalb der Route war.
	 * 			null, falls kein solcher Planet vorhanden ist.
	 */
	function ermittleLetztenRoutenPlaneten($schiff_id) {
		$logbuch = @mysql_query("SELECT logbuch FROM skrupel_schiffe WHERE id='$schiff_id'");
		$logbuch = @mysql_fetch_array($logbuch);
		return $logbuch['logbuch'];
	}
	
	/**
	 * Ermittelt den naechsten Planeten des uebergebenen Schiffs, den es in seiner Route ausgehend vom 
	 * uebergebenen Planeten als naechstens anfliegen soll.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen naechster Routen-Planet ermittelt wird.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, der als letztes vom Schiff innerhalb der Route
	 * 							 angeflogen wurde.
	 * returns: Ein Array aus X- und Y-Koordinate und der Datenbank-ID des Ziel-Planeten.
	 * 			null, falls das Schiff keine Route hat.
	 */
	function ermittleNaechstenRoutenPlaneten($schiff_id, $planeten_id) {
		$routen_planeten_ids = @mysql_query("SELECT routing_id FROM skrupel_schiffe WHERE id='$schiff_id'");
		$routen_planeten_ids = @mysql_fetch_array($routen_planeten_ids);
		$routen_planeten_ids = explode(':', $routen_planeten_ids['routing_id']);
		$routen_laenge = count($routen_planeten_ids);
		if($routen_laenge == null || $routen_laenge == 0) return null;
		$neue_zielid = null;
		for($k=0; $k < $routen_laenge-1; $k++) {
			if($routen_planeten_ids[$k] == $planeten_id) {
				if($routen_planeten_ids[$k+1] != null) $neue_zielid = $routen_planeten_ids[$k+1];
				else $neue_zielid = $routen_planeten_ids[0];
				break;
			}
		}
		if($neue_zielid == null) return null;
		$planet_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$neue_zielid'");
		$planet_koords = @mysql_fetch_array($planet_koords);
		$planet_koords = array('x'=>$planet_koords['x_pos'], 'y'=>$planet_koords['y_pos'], 'id'=>$neue_zielid);
		return $planet_koords;
	}
	
	/**
	 * Prueft, ob das uebergebene Schiff zu einem Wurmloch fliegt.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Ziel ueberprueft werden soll.
	 * returns: true, falls das Schiff zu einem Wurmloch fliegt.
	 * 			false, sonst.
	 */
	function fliegtZuWurmloch($schiff_id) {
		$schiff_infos = @mysql_query("SELECT zielx, ziely, zielid FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$ziel_x = $schiff_infos['zielx'];
		$ziel_y = $schiff_infos['ziely'];
		$ziel_id = $schiff_infos['zielid'];
		if(($ziel_x == 0 && $ziel_y == 0) || $ziel_id != 0) return false;
		foreach(eigenschaften::$gesehene_wurmloecher_daten as $wurmloch) {
			if($ziel_x == $wurmloch['x'] && $ziel_y == $wurmloch['y']) return true;
		}
		return false;
	}
	
	/**
	 * Ermittlet die Menge an Lemin, die das uebergebene Schiff zum Beginn seiner Reise tanken sollte.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen initiale Tankefuellung ermittelt wird.
	 * returns: Die initiale Tankfuellung fuer das Schiff.
	 */
	function ermittleStreckenVerbrauch($schiff_id) {
		$schiff_infos = @mysql_query("SELECT masse, kox, koy, zielx, ziely, zielid, lemin, leminmax, flug, 
			routing_status, routing_id FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$schiff_masse = $schiff_infos['masse'];
		$x_pos = $schiff_infos['kox'];
		$y_pos = $schiff_infos['koy'];
		$ziel_x = $schiff_infos['zielx'];
		$ziel_y = $schiff_infos['ziely'];
		$ziel_id = $schiff_infos['zielid'];
		$lemin_schiff = $schiff_infos['lemin'];
		$lemin_max = $schiff_infos['leminmax'];
		$schiff_flug = $schiff_infos['flug'];
		$routing_status = $schiff_infos['routing_status'];
		$routing_planeten = $schiff_infos['routing_id'];
		$warp = $this->ermittleMaximumWarp($schiff_id);
		$verbrauch_prozent = 100;
		switch($warp) {
			case 2: $verbrauch_prozent = 107.5;
			case 3: $verbrauch_prozent = 107.78;
			case 4: $verbrauch_prozent = 106.25;
			case 5: $verbrauch_prozent = 104;
			case 6: $verbrauch_prozent = 103.69;
			case 7: $verbrauch_prozent = 108.16;
			case 8: $verbrauch_prozent = 109.38;
		}
		if($routing_status == 2) {
			$alter_planet = $this->ermittleLetztenRoutenPlaneten($schiff_id);
			$planeten_id = $this->ermittleNaechstenRoutenPlaneten($schiff_id, $alter_planet);
			$planeten_id = $planeten_id['id'];
			$planeten_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$planeten_id'");
			$planeten_koords = @mysql_fetch_array($planeten_koords);
			$ziel_x = $planeten_koords['x_pos'];
			$ziel_y = $planeten_koords['y_pos'];
		}
		$flug_strecke = ki_basis::berechneStrecke($x_pos, $y_pos, $ziel_x, $ziel_y);
		$lemin = ceil($verbrauch_prozent * $schiff_masse / 100000 * $flug_strecke);
		if(($routing_status == 2 && $lemin_schiff == 0) || $schiff_flug == 3 
		|| $this->fliegtZuWurmloch($schiff_id)) $lemin = $lemin * 3;
		if($lemin_max < $lemin) $lemin = $lemin_max;
		if(in_array($schiff_id, eigenschaften::$jaeger_ids) 
		&& $lemin < eigenschaften::$jaeger_infos->min_lemin_jaeger_tank_start) 
			$lemin = eigenschaften::$jaeger_infos->min_lemin_jaeger_tank_start;
		return $lemin;
	}
	
	/**
	 * Tankt das Lemin vom uebergebenen Planeten auf das gegebene Schiff, sodass eine bestimmte Obergrenze
	 * nicht ueberschritten wird. Es findet keine Pruefung statt, ob das Schiff auch bei diesem Planeten ist!
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das aufgetankt werden soll.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, von dem Lemin abgetankt werden soll.
	 */
	function tankeLeminStart($schiff_id, $planeten_id) {
		$schiff_daten = @mysql_query("SELECT lemin, leminmax FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$planeten_res = @mysql_query("SELECT lemin FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_res = @mysql_fetch_array($planeten_res);
		$lemin_schiff = $schiff_daten['lemin'];
		$lemin_max_schiff = $schiff_daten['leminmax'];
		$lemin_planet = $planeten_res['lemin'];
		$lemin_planet_abzug = 0;
		$obergrenze = $this->ermittleStreckenVerbrauch($schiff_id);
		if($this->SchiffHatFertigkeit($schiff_id, "sprungtriebwerk")) {
			$fertigkeiten = @mysql_query("SELECT fertigkeiten FROM skrupel_schiffe WHERE id='$schiff_id'");
			$fertigkeiten = @mysql_fetch_array($fertigkeiten);
			$sprung_lemin = substr($fertigkeiten['fertigkeiten'], 11, 3)+0;
			$obergrenze += $sprung_lemin;
		}
		if($obergrenze > $lemin_max_schiff) $obergrenze = $lemin_max_schiff;
		if($lemin_schiff + $lemin_planet > $obergrenze) {
			$lemin_planet_abzug = $obergrenze - $lemin_schiff;
			$lemin_schiff = $obergrenze;
		} else { 
			$lemin_schiff += $lemin_planet;
			$lemin_planet_abzug = $lemin_planet;
		}
		ki_basis::zieheResourcenAb("lemin", $lemin_planet_abzug, $planeten_id);
		@mysql_query("UPDATE skrupel_schiffe SET lemin='$lemin_schiff' WHERE id='$schiff_id'");
	}
	
	/**
	 * Tankt das Lemin vom uebergebenen Planeten auf das gegebene Schiff. Es findet keine Pruefung 
	 * statt, ob das Schiff auch bei diesem Planeten ist!
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das aufgetankt werden soll.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, von dem Lemin abgetankt werden soll.
	 */
	function tankeLemin($schiff_id, $planeten_id) {
		$schiff_fracht = @mysql_query("SELECT lemin, leminmax FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_fracht = @mysql_fetch_array($schiff_fracht);
		$planeten_res = @mysql_query("SELECT lemin FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_res = @mysql_fetch_array($planeten_res);
		$lemin_schiff = $schiff_fracht['lemin'];
		$lemin_schiff_max = $schiff_fracht['leminmax'];
		$lemin_planet = $planeten_res['lemin'];
		$lemin_planet_abzug = 0;
		if($lemin_schiff + $lemin_planet > $lemin_schiff_max) {
			$lemin_planet_abzug = $lemin_schiff_max - $lemin_schiff;
			$lemin_schiff = $lemin_schiff_max;
		}
		else { 
			$lemin_schiff += $lemin_planet;
			$lemin_planet_abzug = $lemin_planet;
		}
		ki_basis::zieheResourcenAb("lemin", $lemin_planet_abzug, $planeten_id);
		@mysql_query("UPDATE skrupel_schiffe SET lemin='$lemin_schiff' WHERE id='$schiff_id'");
	}
	
	/**
	 * Leert den Frachtraum des uebegebenen Schiffs auf den uebegebenen Planeten. Es wird nicht geprueft, 
	 * ob das Schiff wirklich beim Planeten ist!
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, des Frachtraum geleert werden soll.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, auf das die Fracht geladen werden soll.
	 */
	static function leereFrachtRaum($schiff_id, $planeten_id) {
		$schiff_fracht = @mysql_query("SELECT fracht_cantox, fracht_vorrat, fracht_min1, 
			fracht_min2, fracht_min3, lemin, fracht_leute FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_fracht = @mysql_fetch_array($schiff_fracht);
		$planeten_res = @mysql_query("SELECT cantox, vorrat, min1, min2, min3, lemin, kolonisten 
			FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_res = @mysql_fetch_array($planeten_res);
		$planet_cantox = $planeten_res['cantox'] + $schiff_fracht['fracht_cantox'];
		$planet_vorrat = $planeten_res['vorrat'] + $schiff_fracht['fracht_vorrat'];
		$planet_min1 = $planeten_res['min1'] + $schiff_fracht['fracht_min1'];
		$planet_min2 = $planeten_res['min2'] + $schiff_fracht['fracht_min2'];
		$planet_min3 = $planeten_res['min3'] + $schiff_fracht['fracht_min3'];
		$planet_lemin = $planeten_res['lemin'] + $schiff_fracht['lemin'];
		$planet_leute = $planeten_res['kolonisten'] + $schiff_fracht['fracht_leute'];
		if($planet_cantox < 0) $planet_cantox = 0;
		if($planet_vorrat < 0) $planet_vorrat = 0;
		if($planet_min1 < 0) $planet_min1 = 0;
		if($planet_min2 < 0) $planet_min2 = 0;
		if($planet_min3 < 0) $planet_min3 = 0;
		if($planet_lemin < 0) $planet_lemin = 0;
		if($planet_leute < 0) $planet_leute = 0;
		@mysql_query("UPDATE skrupel_planeten SET cantox='$planet_cantox', vorrat='$planet_vorrat', 
			min1='$planet_min1', min2='$planet_min2', min3='$planet_min3', lemin='$planet_lemin', 
			kolonisten='$planet_leute' WHERE id='$planeten_id'");
		@mysql_query("UPDATE skrupel_schiffe SET fracht_cantox=0, fracht_vorrat=0, fracht_min1=0, 
			fracht_min2=0, fracht_min3=0, lemin=0, fracht_leute=0 WHERE id='$schiff_id'");
	}
	
	/**
	 * Baut alle Projektile fuer die Projektil-Waffen des uebergebenen Schiffs, sofern die Resourcen des 
	 * Planeten ausreichen.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, bei dem Projektile gebaut werden sollen.
	 */
	function baueProjektile($schiff_id) {
		$schiff_infos = @mysql_query("SELECT kox, koy, projektile_anzahl, projektile 
			FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$x_pos = $schiff_infos['kox'];
		$y_pos = $schiff_infos['koy'];
		$max_projektile = $schiff_infos['projektile_anzahl'] * 5;
		$vorhandene_projektile = $schiff_infos['projektile'];
		if($max_projektile == 0) return;
		$spiel_id = eigenschaften::$spiel_id;
		$planeten_res =  @mysql_query("SELECT cantox, min1, min2 FROM skrupel_planeten 
			WHERE (x_pos='$x_pos') AND (y_pos='$y_pos') AND (spiel='$spiel_id')");
		$planeten_res = @mysql_fetch_array($planeten_res);
		$planet_cantox = $planeten_res['cantox'];
		$planet_min1 = $planeten_res['min1'];
		$planet_min2 = $planeten_res['min2'];
		if($planet_cantox < 35 || $planet_min1 < 2 || $planet_min2 < 1) return;
		$mehr_projektile = true;
		while($planet_cantox >= 35 && $planet_min1 >= 2 && $planet_min2 >= 1 
		&& $vorhandene_projektile < $max_projektile) {
			$vorhandene_projektile++;
			$planet_cantox -= 35;
			$planet_min1 -= 2;
			$planet_min2 -= 1;
		}
		@mysql_query("UPDATE skrupel_schiffe SET projektile='$vorhandene_projektile', projektile_auto=1 
				WHERE id='$schiff_id'");
		@mysql_query("UPDATE skrupel_planeten SET cantox='$planet_cantox', min1='$planet_min1', 
			min2='$planet_min2' WHERE (x_pos='$x_pos') AND (y_pos='$y_pos') AND (spiel='$spiel_id')");
	}
	
	/**
	 * Fliegt das uebegebene Schiff zu den uebergebenen Ziel-Koordinaten mit der uebegebenen Ziel-ID und 
	 * der uebergebenen Warp-Geschwindigkeit. Hat das Schiff ein Sprungtriebwerk, ist die Entfernung 
	 * groesser als die maximale Sprung-Reichweite und bleibt nach dem Sprung noch genuegend Lemin uebrig, 
	 * so wird das Sprungtriebwerk aktiviert.
	 * arguments: $schiff_id - Die Datenbank-ID des zu fliegenden Schiffs.
	 * 			  $ziel_x - Die X-Koordinaten des Ziels.
	 * 			  $ziel_y - Die Y-Koordinaten des Ziels.
	 * 			  $warp - Die Warp-Geschwindigkeit, mit der geflogen wird.
	 * 			  $ziel_id - Die Datenbank-ID des Ziels, falls das Ziel ein Schiff oder ein Planet ist.
	 */
	function fliegeSchiff($schiff_id, $ziel_x, $ziel_y, $warp, $ziel_id) {
		if($ziel_x == 0 && $ziel_y == 0) return;
		if($ziel_id != null && $ziel_id != 0) {
			$spiel_id = eigenschaften::$spiel_id;
			$comp_id = eigenschaften::$comp_id;
			$feind_schiff_id = @mysql_query("SELECT id FROM skrupel_schiffe WHERE (NOT (besitzer='$comp_id')) 
				AND (kox='$ziel_x') AND (koy='$ziel_y') AND (id='$ziel_id') AND (spiel='$spiel_id')");
			$feind_schiff_id = @mysql_fetch_array($feind_schiff_id);
			$flug = 2;
			if($feind_schiff_id['id'] != null && $feind_schiff_id['id'] == $ziel_id) $flug = 3;
			@mysql_query("UPDATE skrupel_schiffe SET zielx='$ziel_x', ziely='$ziel_y', 
				warp='$warp', flug='$flug', zielid='$ziel_id' WHERE id='$schiff_id'");
		}
		else @mysql_query("UPDATE skrupel_schiffe SET zielx='$ziel_x', ziely='$ziel_y', zielid=0, 
			warp='$warp', flug=1 WHERE id='$schiff_id'");
		if(!schiffe_basis::SchiffHatFertigkeit($schiff_id, "sprungtriebwerk")) return;
		$schiff_infos = @mysql_query("SELECT fertigkeiten, lemin, kox, koy FROM skrupel_schiffe 
			WHERE id='$schiff_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$fertigkeiten = $schiff_infos['fertigkeiten'];
		$lemin_schiff = $schiff_infos['lemin'];
		$x_pos = $schiff_infos['kox'];
		$y_pos = $schiff_infos['koy'];
		$sprung_lemin = substr($fertigkeiten, 11, 3)+0;
		if(($lemin_schiff - $sprung_lemin) < eigenschaften::$schiffe_infos->min_restlemin_sprung) return;
		$sprung_min_strecke = substr($fertigkeiten, 14, 4)+0;
		$sprung_max_strecke = substr($fertigkeiten, 18, 4)+0;
		//Falls es moeglich ist, dass das Schiff durch das Sprungtriebwerk ausserhalb der Karte landet, 
		//wird das Sprungtriebwerk nicht aktiviert.
		$weitester_zielpunkt = ki_basis::berechneWegPunkt($sprung_max_strecke, $x_pos, $y_pos, 
							   $ziel_x, $ziel_y);
		if(ki_basis::PunktAusserhalb($weitester_zielpunkt[0], $weitester_zielpunkt[1])) return;
		$min_strecke = ($sprung_min_strecke + $sprung_max_strecke) / 2;
		$ziel_entfernung = floor(ki_basis::berechneStrecke($x_pos, $y_pos, $ziel_x, $ziel_y));
		if($ziel_entfernung != 0 && $min_strecke != 0 && $ziel_entfernung >= $min_strecke) 
			@mysql_query("UPDATE skrupel_schiffe SET spezialmission=7 WHERE id='$schiff_id'");
	}
	
	/**
	 * Fliegt das uebergebene Schiff zum naechsten wichtigen Planeten.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das geflogen werden soll.
	 */
	function zuWichtigenPlaneten($schiff_id) {
		$schiff_daten = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$x_start = $schiff_daten['kox']; 
		$y_start = $schiff_daten['koy'];
		$antrieb_stufe = $this->ermittleMaximumWarp($schiff_id);
		$wichtige_koords = array();
		foreach(eigenschaften::$wichtige_planeten_ids as $id) {
			$planet = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$id'");
			$planet = @mysql_fetch_array($planet);
			$wichtige_koords[] = array('x'=>$planet['x_pos'], 'y'=>$planet['y_pos'], 'id'=>$id);
		}
		$naher_planet = ki_basis::ermittleNahesZiel($schiff_id, $wichtige_koords, 
													eigenschaften::$bekannte_wurmloch_daten);
		$x_ziel = $naher_planet['x'];
		$y_ziel = $naher_planet['y'];
		$id_ziel = $naher_planet['id'];
		$this->fliegeSchiff($schiff_id, $x_ziel, $y_ziel, $antrieb_stufe, $id_ziel);
	}
	
	/**
	 * Setzt einen Kurs fuer das gegebene Schiff, sodass um ein gegebenes Objekt (zb. Wurmloch oder Minenfeld)
	 * herumgeflogen wird.
	 * arguments: $schiff_x - Die X-Koordinaten des Schiffs, dass gegebenenfalls ein Objekt umfliegen soll.
	 * 			  $schiff_y - Die Y-Koordinaten des Schiffs, dass gegebenenfalls ein Objekt umfliegen soll.
	 * 			  $schiff_id - Die Datenbank-ID des Schiffs, dass gegebenenfalls ein Objekt umfliegen soll.
	 * 			  $objekt_x - Die X-Koordinaten des Objekts, dass umflogen werden soll.
	 * 			  $objekt_y - Die Y-Koordinaten des Objekts, dass umflogen werden soll.
	 * 			  $schiff_ziel_x - Die X-Koordinaten des Ziels, zu dem das Schiff eigentlich hin soll.
	 * 			  $schiff_ziel_y - Die Y-Koordinaten des Ziels, zu dem das Schiff eigentlich hin soll.
	 * 			  $schiff_ziel_id - Die Datenbank-ID des Ziels, zu dem das Schiff eigentlich hin soll.
	 * 			  $mindestabstand - Der Abstand (in Lj), der zum Objekt, das umflogen werden soll, genommen 
	 * 								werden soll.
	 * returns: true - Falls das Objekt umflogen werden muss und dabei gleich der Kurs gesetzt wurde.
	 * 			false - Sonst. Dabei wurde kein Kurs gesetzt.
	 */
	function umfliegeObjekt($schiff_x, $schiff_y, $schiff_id, $objekt_x, $objekt_y, 
							$schiff_ziel_x, $schiff_ziel_y, $schiff_ziel_id , $mindestabstand) {
		//Zielvektor fuer die direkte Flugrichtung:
		$zielvek_x = $schiff_ziel_x - $schiff_x;
		$zielvek_y = $schiff_ziel_y - $schiff_y;
		//Vektor zum objekt:
		$wurmvek_x = $objekt_x - $schiff_x;
		$wurmvek_y = $objekt_y - $schiff_y;
		
		//Der Zielvektor wird normiert:
		$zielvek_betrag = sqrt($zielvek_x * $zielvek_x + $zielvek_y * $zielvek_y);
		if($zielvek_betrag == 0) return false;
		$zielnvek_x = $zielvek_x / $zielvek_betrag;
		$zielnvek_y = $zielvek_y / $zielvek_betrag;
		
		//Projektion des Vektors zum objekt auf Zielstrecke:
		$proj = $wurmvek_x * $zielnvek_x + $wurmvek_y * $zielnvek_y;
		//Falls der eingeschlossene Winkel der Vektoren groesser als 90 Grad ist, so ist $proj negativ 
		//und ein Ausweichen unnoetig.
		if($proj >= 0) {
			//Der Fusspunkt ist der Punkt der direkten Flugstrecke, der dem objekt am naechsten kommt.
			$fuss_x = $schiff_x + $proj * $zielnvek_x;
			$fuss_y = $schiff_y + $proj * $zielnvek_y;
			
			//Senkrecht zur direkten Flugstrecke stehender Vektor mit Laenge des min. Abstands zum objekt
			$wurmabstandvek_x = $fuss_x - $objekt_x;
			$wurmabstandvek_y = $fuss_y - $objekt_y;
			//Falls das objekt (fast) genau auf Zielstrecke ist:
      		if((abs($wurmabstandvek_x) < 1) && (abs($wurmabstandvek_y) < 1)) {
				//Hier wird der senkrecht zur Flugstrecke stehende (normierte) Ausweichvektor generiert:	
      			$wurmabstandvek_x = $zielnvek_y;
				$wurmabstandvek_y = -$zielnvek_x;
      		}
      		$wurmabstandvek_betrag = sqrt($wurmabstandvek_x * $wurmabstandvek_x + 
      									  $wurmabstandvek_y * $wurmabstandvek_y);
      		//Falls ein ausreichender objektabstand beim Direktflug doch gegeben ist:
      		if($wurmabstandvek_betrag >= $mindestabstand) return false;
			$faktor = $mindestabstand / $wurmabstandvek_betrag;
			
			//Umgehungswegpunkt zur objektmeidung
			$zwischen_x = round($objekt_x + $faktor * $wurmabstandvek_x);
			$zwischen_y = round($objekt_y + $faktor * $wurmabstandvek_y);
			
			//Nun wird der Kurs zum Ausweich-Punkt gesetzt. Ausserdem werden die relevanten Daten in die 
			//Datenbank geschrieben.
			$warp = $this->ermittleMaximumWarp($schiff_id);
			$this->fliegeSchiff($schiff_id, $zwischen_x, $zwischen_y, $warp, null);
			$umflug_daten = "".$zwischen_x.":".$zwischen_y.":".$schiff_ziel_x.":".$schiff_ziel_y.
							":".$schiff_ziel_id;
			@mysql_query("UPDATE skrupel_schiffe SET routing_schritt=1, routing_koord='$umflug_daten' 
				WHERE id='$schiff_id'");
			return true;
		}
		return false;
	}
	
	/** 
	 * Prueft, ob das gegebene Schiff ein nahes Objekt umfliegen muss. Falls ja, wird ein entsprechender 
	 * Kurs gesetzt.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dass gegebenenfalls ein Objekt umfliegen soll.
	 * returns: true, falls ein Objekt umflogen werden muss. Dabei wurde ein Kurs gesetzt.
	 * 			false, sonst. Dan wurde kein Kurs gesetzt.
	 * Mitwirkende Autoren: Jonu.
	 */
	function mussObjektUmfliegen($schiff_id) {
		$schiff_daten = @mysql_query("SELECT kox, koy, zielx, ziely, zielid FROM skrupel_schiffe 
			WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$schiff_x = $schiff_daten['kox'];
		$schiff_y = $schiff_daten['koy'];
		$schiff_ziel_x = $schiff_daten['zielx'];
		$schiff_ziel_y = $schiff_daten['ziely'];
		$schiff_ziel_id = $schiff_daten['zielid'];
		foreach(eigenschaften::$gesehene_wurmloecher_daten as $wurmloch) {
			if($wurmloch['x'] == $schiff_ziel_x && $wurmloch['y'] == $schiff_ziel_y) return false;
		}
		$anomalien_koords = array();
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$anomalien = @mysql_query("SELECT x_pos, y_pos, id, art FROM skrupel_anomalien 
			WHERE (spiel='$spiel_id') AND (sicht_".$comp_id."=1) AND ((art=1) OR (art=2) OR (art=5))");
		while($anomalie = @mysql_fetch_array($anomalien)) {
			if($anomalie['art'] == 5) {
				$besitzer = ki_basis::ermittleMinenfeldBesitzer($anomalie['id']);
				if($besitzer == eigenschaften::$comp_id) continue;
			}
			$anomalien_koords[] = array('x'=>$anomalie['x_pos'], 'y'=>$anomalie['y_pos'], 'id'=>$anomalie['id']);
		}
		//Erstmal wird die naechste Anomoalie gesucht, die ein Wurmloch, ein Sprungtor oder ein Minenfeld ist.
		$nahe_anomalie = ki_basis::ermittleNahesZiel($schiff_id, $anomalien_koords, null);
		//Wurde keine solche Anomalie gefunden, so wird auch keine umflogen.
		if($nahe_anomalie == null || $nahe_anomalie['id'] == 0) return false;
		$anomalie_x = $nahe_anomalie['x'];
		$anomalie_y = $nahe_anomalie['y'];
		$anomalie_art = @mysql_query("SELECT art FROM skrupel_anomalien WHERE (x_pos='$anomalie_x') AND 
			(y_pos='$anomalie_y') AND (spiel='$spiel_id')");
		$anomalie_art = @mysql_fetch_array($anomalie_art);
		$anomalie_art = $anomalie_art['art'];
		$abstand = 100;
		//Je nach Art des Objekts wird ein anderer Sicherheits-Abstand gewaehlt. Plasma-Stuerme werden 
		//nicht umflogen.
		if($anomalie_art == 5) $abstand = eigenschaften::$schiffe_infos->mindest_minenfeld_abstand;
		elseif($anomalie_art == 1 || $anomalie_art == 2) 
			$abstand = eigenschaften::$schiffe_infos->mindest_wurmloch_abstand;
		else return false;
		return $this->umfliegeObjekt($schiff_x, $schiff_y, $schiff_id, $nahe_anomalie['x'], $nahe_anomalie['y'], 
									 $schiff_ziel_x, $schiff_ziel_y, $schiff_ziel_id , $abstand);
	}
	
	/**
	 * Prueft, ob das uebergebene Schiff von einem gegnerischen Schiff angegriffen wird und laesst das Schiff
	 * gegebenenfalls fluechten. Dabei wird zu einem Planeten gefluechtet, der in die entgegengesetzte Richtung
	 * des Angreifers liegt.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das vieleicht fluechten soll.
	 * returns: true - Falls das Schiff angegriffen wird und fluechtet.
	 * 			false - Sonst.
	 */
	function flieheVorSchiff($schiff_id) {
		$schiff_daten = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$schiff_x = $schiff_daten['kox'];
		$schiff_y = $schiff_daten['koy'];
		$kleine_entfernung = eigenschaften::$frachter_infos->max_gegner_frachter_naehe;
		$naher_gegner_id = 0;
		$naher_gegner_x = 0;
		$naher_gegner_y = 0;
		foreach(eigenschaften::$sichtbare_gegner_schiffe as $gegner_schiff) {
			$gegner_x = $gegner_schiff['x'];
			$gegner_y = $gegner_schiff['y'];
			$entfernung = floor(ki_basis::berechneStrecke($schiff_x, $schiff_y, $gegner_x, $gegner_y));
			if($entfernung < $kleine_entfernung) {
				$kleine_entfernung = $entfernung;
				$naher_gegner_x = $gegner_x;
				$naher_gegner_y = $gegner_y;
			}
		}
		if($naher_gegner_x != 0 || $naher_gegner_y != 0) {
			$flucht_koords = ki_basis::ermittleFluchtKoordinaten($schiff_id, $naher_gegner_x, $naher_gegner_y);
			$warp = $this->ermittleMaximumWarp($schiff_id);
			$this->fliegeSchiff($schiff_id, $flucht_koords['x'], $flucht_koords['y'], $warp, null);
			return true;
		}
		return false;
	}
	
	/**
	 * Ermittelt den naechst-gelegenen Planeten und fliegt dort hin. Es wird dabei nicht geprueft, ob der 
	 * naechste Planet feindlich ist oder nicht.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das tanken fliegen soll.
	 */
	function fliegeTanken($schiff_id) {
		$schiff_pos = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_pos = @mysql_fetch_array($schiff_pos);
		$x_pos = $schiff_pos['kox'];
		$y_pos = $schiff_pos['koy'];
		$warp = $this->ermittleMaximumWarp($schiff_id);
		$ziel_planet = ki_basis::ermittleNahenPlaneten($x_pos, $y_pos, null, false, true);
		$this->fliegeSchiff($schiff_id, $ziel_planet['x'], $ziel_planet['y'], $warp, $ziel_planet['id']);
	}
	
	/**
	 * Scannt die Umgebung nach Planeten, um Planeten ins Logbuch des Schiffs zu speichern, die sich nicht fuer 
	 * die Kolonisierung lohnen. Ist das Schiff ein Frachter mit Kolonisten, so wird das Schiff sofort wo 
	 * anders hin geflogen. In dieser Implementierung wird hoechstens ein Planet erfasst!
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das seine Umgebung nach Planeten scant.
	 * returns: true - Falls das Schiff ein Frachter mit Kolonisten ist und dieser umgeleitet wurde.
	 * 			false - Falls keine Flug-Aenderung vorgenommen wurde bzw. das Schiff keine Kolonisten an 
	 * 					Bord hat.
	 */
	function scanneUmgebung($schiff_id) {
		$schiff_daten = @mysql_query("SELECT kox, koy, fracht_leute, status, lemin FROM skrupel_schiffe 
				WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$x_pos = $schiff_daten['kox'];
		$y_pos = $schiff_daten['koy'];
		$fracht_leute = $schiff_daten['fracht_leute'];
		$schiff_status = $schiff_daten['status'];
		$schiff_lemin = $schiff_daten['lemin'];
		//Kolonien der KI werden nicht beruecksichtigt.
		$naher_planet = ki_basis::ermittleNahenPlaneten($x_pos, $y_pos, eigenschaften::$kolonien_ids, 
						false, true);
		if($naher_planet != null && $naher_planet['id'] != null && $naher_planet['id'] != 0) {
			$entfernung = floor(ki_basis::berechneStrecke($x_pos, $y_pos, $naher_planet['x'], 
														  $naher_planet['y']));
			if($entfernung <= $this->ermittleSchiffScanweite($schiff_id)) {
				if(!(ki_basis::PlanetLohntSich($naher_planet['id']))) {
					if($fracht_leute >= eigenschaften::$frachter_kolo_infos->kolo_leute) {
						frachter_basis::fliegeKolonieSchiff($schiff_id);
						if($schiff_status == 2) $this->tankeLemin($schiff_id, $naher_planet['id']);
						elseif($this->ermittleStreckenVerbrauch($schiff_id) > $schiff_lemin) {
							$warp = $this->ermittleMaximumWarp($schiff_id);
							$this->fliegeSchiff($schiff_id, $naher_planet['x'], $naher_planet['y'], 
												$warp, $naher_planet['id']);
						}
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Ueberprueft, ob das gegebene Schiff gerade durch ein stabiles Wurmloch geflogen ist.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, bei dem geprueft werden soll, ob es durch ein 
	 * 						   Wurmloch geflogen ist.
	 * returns: Die Datenbank-ID sowie die X- und Y-Koordinate des Wurmlochs, durch das das Schiff gekommen 
	 * 			ist, falls das Schiff durch ein Wurmloch flog.
	 * 			null, sonst.
	 */
	function durchWurmlochGeflogen($schiff_id) {
		$schiff_daten = @mysql_query("SELECT kox, koy, zielx, ziely, zielid, status, flug FROM skrupel_schiffe 
			WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$x_pos = $schiff_daten['kox'];
		$y_pos = $schiff_daten['koy'];
		$ziel_x = $schiff_daten['zielx'];
		$ziel_y = $schiff_daten['ziely'];
		$ziel_id = $schiff_daten['zielid'];
		if($ziel_id != 0 || $ziel_x != 0 || $ziel_y != 0) return null;
		foreach(eigenschaften::$gesehene_wurmloecher_daten as $wurmloch_daten) {
			$wurmloch_x = $wurmloch_daten['x'];
			$wurmloch_y = $wurmloch_daten['y'];
			$wurmloch_id = $wurmloch_daten['id'];
			if($wurmloch_id == null || $wurmloch_id == 0) continue;
			$wurmloch_entfernung = floor(ki_basis::berechneStrecke($x_pos, $y_pos, $wurmloch_x, $wurmloch_y));
			if($wurmloch_entfernung > 20) continue;
			$spiel_id = eigenschaften::$spiel_id;
			$extra = @mysql_query("SELECT extra FROM skrupel_anomalien WHERE id='$wurmloch_id'");
			$extra = @mysql_fetch_array($extra);
			$extra = $extra['extra'];
			//Hat das Wurmloch keine Ziel-Informationen, so ist es instabil.
			if($extra == null || $extra == "" || $extra == '') {
				wurmloecher_basis::updateInstabileWurmloecher($wurmloch_id);
				return null;
			}
			wurmloecher_basis::updateBekannteWurmloecher($wurmloch_id);
			return wurmloecher_basis::ermittleWurmlochZiel($wurmloch_id);
		}
		return null;
	}
	
	/**
	 * Verwaltet das Verhalten von Schiffen mit Wurmloechern. Ist das Schiff durch ein Wurmloch geflogen, so 
	 * wird ein Kurs gesetzt und das Wurmloch gegebenenfalls durchflogen.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Wurmloch-Verhalten bestimmt wird.
	 * returns: true, falls das Schiff mit einem Wurmloch interagiert.
	 * 			false, sonst.
	 */
	function reagiereAufWurmloch($schiff_id) {
		$schiff_daten = @mysql_query("SELECT status, flug, kox, koy, fracht_leute, routing_schritt, 
			routing_koord FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		if($schiff_daten['status'] == 2 && $schiff_daten['flug'] == 0) return false;
		$warp = $this->ermittleMaximumWarp($schiff_id);
		$x_pos = $schiff_daten['kox'];
		$y_pos = $schiff_daten['koy'];
		$schiff_flug = $schiff_daten['flug'];
		$schiff_status = $schiff_daten['status'];
		$fracht_leute = $schiff_daten['fracht_leute'];
		$wurmloch_flug_status = $schiff_daten['routing_schritt'];
		$wurmloch_flug_daten = $schiff_daten['routing_koord'];
		//Ist das Schiff im freien Raum und steht still, so wird ueberprueft, ob es gerade durch ein Wurmloch
		//geflogen ist. Falls ja, wird gegebenenfalls das Wurmloch umflogen.
		if($schiff_status == 1 && $schiff_flug == 0) {
			$wl_daten = $this->durchWurmlochGeflogen($schiff_id);
			if($wl_daten != null && $wl_daten['id'] != null && $wl_daten['id'] != 0) {
				if($this instanceof frachter_basis) {
					if($fracht_leute < eigenschaften::$frachter_kolo_infos->kolo_leute) 
						$this->zuWichtigenPlaneten($schiff_id);
					else $this->fliegeKolonieSchiff($schiff_id);
				} elseif($this instanceof jaeger_basis) $this->fliegeJaeger($schiff_id);
				elseif($this instanceof scouts_basis) $this->fliegeScout($schiff_id);
				elseif($this instanceof spezialschiffe_leicht) $this->fliegeTasterSchiff($schiff_id);
				$ziel_daten = @mysql_query("SELECT zielx, ziely, zielid FROM skrupel_schiffe 
					WHERE id='$schiff_id'");
				$ziel_daten = @mysql_fetch_array($ziel_daten);
				if($this->umfliegeObjekt($x_pos, $y_pos, $schiff_id, $wl_daten['x'], $wl_daten['y'], 
										 $ziel_daten['zielx'], $ziel_daten['ziely'], $ziel_daten['zielid'], 
										 eigenschaften::$schiffe_infos->mindest_wurmloch_abstand))
					return true;
			}
			if($schiff_flug == 0) {
				if($this instanceof frachter_basis) {
					if($fracht_leute < eigenschaften::$frachter_kolo_infos->kolo_leute) 
						$this->zuWichtigenPlaneten($schiff_id);
					else $this->fliegeKolonieSchiff($schiff_id);
				} elseif($this instanceof jaeger_basis) $this->fliegeJaeger($schiff_id);
				elseif($this instanceof scouts_basis) $this->fliegeScout($schiff_id);
				elseif($this instanceof spezialschiffe_leicht) $this->fliegeTasterSchiff($schiff_id);
				return true;
			}
		}
		//Ist das Schiff gerade dabei, ein Wurmloch zu umfliegen, das er gerade passiert hatte, so wird 
		//geprueft, ob es es schon weit genug umflogen hat, sodass er nun direkt zum urspruenglichen Ziel
		//fliegen kann.
		if($wurmloch_flug_status == 1) {
			$wurmloch_flug_daten = explode(':', $wurmloch_flug_daten);
			$ziel_x = $wurmloch_flug_daten[0];
			$ziel_y = $wurmloch_flug_daten[1];
			if($ziel_x == $x_pos && $ziel_y == $y_pos) {
				@mysql_query("UPDATE skrupel_schiffe SET routing_schritt=0, routing_koord='' 
						WHERE id='$schiff_id'");
				$this->fliegeSchiff($schiff_id, $wurmloch_flug_daten[2], $wurmloch_flug_daten[3], 
									$warp, $wurmloch_flug_daten[4]);
			}
			return true;
		}
		//Hier wird ueberprueft, ob das Schiff auf dem Weg zu einem bekannten Wurmloch ist. Falls es schon 
		//durch das Wurmloch geflogen ist, so wird das Ausgangs-Wurmloch gegebenenfalls umflogen.
		if($wurmloch_flug_status == 2) {
			$wurmloch_flug_daten = explode(':', $wurmloch_flug_daten);
			$wurmloch_x = $wurmloch_flug_daten[0];
			$wurmloch_y = $wurmloch_flug_daten[1];
			$wurmloch_id = $wurmloch_flug_daten[2];
			$wurmloch_ziel_id = wurmloecher_basis::ermittleWurmlochZiel($wurmloch_id);
			if($wurmloch_ziel_id != null && $wurmloch_ziel_id['id'] != null && $wurmloch_ziel_id['id'] != 0) {
				$wurmloch_ziel_id = $wurmloch_ziel_id['id'];
				$wurmloch_ziel_daten2 = $this->durchWurmlochGeflogen($schiff_id);
				$wurmloch_ziel_id2 = $wurmloch_ziel_daten2['id'];
				//Falls das Schiff schon durch das Wurmloch geflogen ist, wird gecheckt, ob das Wurmloch 
				//umflogen werden muss.
				if($wurmloch_ziel_id == $wurmloch_ziel_id2 && 
				$wurmloch_ziel_id != null && $wurmloch_ziel_id != 0 && 
				$wurmloch_ziel_id2 != null && $wurmloch_ziel_id2 != 0) {
					$umflug_x = $wurmloch_ziel_daten2['x'];
					$umflug_y = $wurmloch_ziel_daten2['y'];
					$zielx = $wurmloch_flug_daten[3];
					$ziely = $wurmloch_flug_daten[4];
					$zielid = $wurmloch_flug_daten[5];
					if(!($this->umfliegeObjekt($x_pos, $y_pos, $schiff_id, $umflug_x, $umflug_y, $zielx, 
					$ziely, $zielid, eigenschaften::$schiffe_infos->mindest_wurmloch_abstand))) {
						@mysql_query("UPDATE skrupel_schiffe SET routing_schritt=0, routing_koord='' 
								WHERE id='$schiff_id'");
						$this->fliegeSchiff($schiff_id, $zielx, $ziely, $warp, $zielid);
						return true;
					}
				}
				$this->fliegeSchiff($schiff_id, $wurmloch_x, $wurmloch_y, $warp, 0);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Ueberprueft, ob beim uebergebenen Planeten ein Schiff mit der uebergebenen Klassen-Id ist.
	 * arguments: $planeten_id - Die Datenbank-ID des zu ueberpruefenden Planeten.
	 * 			  $klassen_id - Die Klassen-ID des gesuchten Schiffs.
	 * returns: true, falls ein solches Schiff am Planeten ist.
	 * 			false, sonst.
	 */
	static function ermittleSchiffanPlaneten($planeten_id, $klassen_id) {
		$spiel_id = eigenschaften::$spiel_id;
		$planeten_daten = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_daten = @mysql_fetch_array($planeten_daten);
		$x_pos = $planeten_daten['x_pos'];
		$y_pos = $planeten_daten['y_pos'];
		$schiff_daten = @mysql_query("SELECT klasseid FROM skrupel_schiffe WHERE (kox='$x_pos') 
			AND (koy='$y_pos') AND (spiel='$spiel_id')");
		while($schiff_klasse = @mysql_fetch_array($schiff_daten)) {
			if($schiff_klasse['klasseid'] == $klassen_id) return true;
		}
		return false;
	}
}
?>