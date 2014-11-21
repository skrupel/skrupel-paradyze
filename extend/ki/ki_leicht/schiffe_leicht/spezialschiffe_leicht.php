<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Klasse fuer die Verwaltung von Schiffen mit speziellen Faehigkeiten.
 */
class spezialschiffe_leicht extends schiffe_basis {
	
	/**
	 * Diese Implementierung aktiviert fuer alle Schiff mit Subpartikelcluster die Spezialmission 
	 * (Resourcen werden dabei automatisch aufs Schiff gebeamt).
	 */
	function verwalteClusterSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		foreach(eigenschaften::$cluster_ids as $cluster_id) {
			$planeten_info = @mysql_query("SELECT p.lemin, p.min1, p.min2, p.min3, p.id FROM skrupel_planeten p, 
				skrupel_schiffe s WHERE (s.id='$cluster_id') AND (s.kox=p.x_pos) AND (s.koy=p.y_pos) 
				AND (s.spiel='$spiel_id')");
			$planeten_info = @mysql_fetch_array($planeten_info);
			$lemin = $planeten_info['lemin'];
			$min1 = $planeten_info['min1'];
			$min2 = $planeten_info['min2'];
			$min3 = $planeten_info['min3'];
			if($lemin < eigenschaften::$spezialschiffe_infos->min_lemin_cluster_off 
			&& $min1 > 113 && $min2 > 113 && $min3 > 113) {
				@mysql_query("UPDATE skrupel_schiffe SET spezialmission=0 WHERE id='$cluster_id'");
				$this->leereFrachtRaum($cluster_id, $planeten_info['id']);
			}
			else @mysql_query("UPDATE skrupel_schiffe SET spezialmission=27 WHERE id='$cluster_id'");
		}
	}
	
	/**
	 * Diese Implementierung aktiviert fuer alle Schiff mit Quarkreorganisator die Spezialmission 
	 * (Resourcen werden dabei automatisch aufs Schiff gebeamt).
	 */
	function verwalteQuarkSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		foreach(eigenschaften::$quark_ids as $quark_id) {
			$planeten_info = @mysql_query("SELECT p.lemin, p.id FROM skrupel_planeten p, skrupel_schiffe s 
				WHERE (s.id='$quark_id') AND (s.kox=p.x_pos) AND (s.koy=p.y_pos) 
				AND (s.spiel='$spiel_id')");
			$planeten_info = @mysql_fetch_array($planeten_info);
			$lemin = $planeten_info['lemin'];
			if($lemin > eigenschaften::$spezialschiffe_infos->max_lemin_quark_on) {
				@mysql_query("UPDATE skrupel_schiffe SET spezialmission=0 WHERE id='$quark_id'");
				$this->leereFrachtRaum($quark_id, $planeten_info['id']);
			}
			else @mysql_query("UPDATE skrupel_schiffe SET spezialmission=26 WHERE id='$quark_id'");
		}
	}
	
	/**
	 * Verwaltet alle Schiffe mit Cybernrittnikk.
	 */
	function verwalteCyberSchiffe() {
		foreach(eigenschaften::$cyber_ids as $cyber_id) {
			@mysql_query("UPDATE skrupel_schiffe SET spezialmission=28 WHERE id='$cyber_id'");
		}
	}
	
	/**
	 * Verwaltet alle Schiffe mit Terraformer. Je nach Eingabe werden alle warmen oder alle kalten Terraformer 
	 * betrachtet. Warme Terraformer suchen sich den Planeten mit der niedrigesten Temperatur, der im Besitz
	 * der KI ist. Kalte Terraformer suchen sich entsprechend den waermsten.
	 * arguments: $kalt_warm - Bei true werden alle warmen Terraformer verwaltet, bei false alle kalten.
	 */
	function verwalteTerraSchiffe($warm) {
		$optimale_temp = ki_basis::ermittleOptimaleTemp();
		if($optimale_temp == 0 || (count(eigenschaften::$terra_warm_ids) == 0 && $warm) 
		|| (count(eigenschaften::$terra_kalt_ids) == 0 && !$warm)) return;
		$terra_id = null;
		$terra_id_alt = null;
		$terra_ids = null;
		$neuer_planet = null;
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		if($warm) {
			$terra_id = $this->ermittleTerraWarmID(); $terra_id = $terra_id['id'];
			$terra_id_alt = $this->ermittleTerraKaltID(); $terra_id_alt = $terra_id_alt['id'];
			$terra_ids = eigenschaften::$terra_warm_ids;
			$neuer_planet = @mysql_query("SELECT p.x_pos, p.y_pos, p.id, p.temp FROM skrupel_planeten p 
				WHERE p.besitzer='$comp_id' AND p.spiel='$spiel_id' 
				AND (NOT EXISTS (SELECT * FROM skrupel_schiffe s WHERE (s.klasseid='$terra_id' 
				OR s.klasseid='$terra_id_alt') AND ((s.kox=p.x_pos AND s.koy=p.y_pos) OR (s.zielx=p.x_pos 
				AND s.ziely=p.y_pos)) AND s.besitzer='$comp_id' AND s.spiel='$spiel_id')) ORDER BY p.temp ASC");
		} else {
			$terra_id = $this->ermittleTerraKaltID(); $terra_id = $terra_id['id'];
			$terra_id_alt = $this->ermittleTerraWarmID(); $terra_id_alt = $terra_id_alt['id'];
			$terra_ids = eigenschaften::$terra_kalt_ids;
			$neuer_planet = @mysql_query("SELECT p.x_pos, p.y_pos, p.id, p.temp FROM skrupel_planeten p 
				WHERE p.besitzer='$comp_id' AND p.spiel='$spiel_id' 
				AND (NOT EXISTS (SELECT * FROM skrupel_schiffe s WHERE (s.klasseid='$terra_id' 
				OR s.klasseid='$terra_id_alt') AND ((s.kox=p.x_pos AND s.koy=p.y_pos) OR (s.zielx=p.x_pos 
				AND s.ziely=p.y_pos)) AND s.besitzer='$comp_id' AND s.spiel='$spiel_id')) ORDER BY p.temp DESC");
		}
		if($terra_id == null || $terra_ids == null) return;
		foreach($terra_ids as $terraschiff_id) {
			@mysql_query("UPDATE skrupel_schiffe SET spezialmission=0 WHERE id='$terraschiff_id'");
			$terra_daten = @mysql_query("SELECT kox, koy, status FROM skrupel_schiffe 
				WHERE id='$terraschiff_id'");
			$terra_daten = @mysql_fetch_array($terra_daten);
			$x_pos = $terra_daten['kox'];
			$y_pos = $terra_daten['koy'];
			$status = $terra_daten['status'];
			if($status == 2) {
				$planeten_daten = @mysql_query("SELECT temp, id FROM skrupel_planeten WHERE x_pos='$x_pos' 
					AND y_pos='$y_pos' AND spiel='$spiel_id' AND besitzer='$comp_id'");
				$planeten_daten = @mysql_fetch_array($planeten_daten);
				$planeten_temp = $planeten_daten['temp'] - 35;
				$planeten_id = $planeten_daten['id'];
				$this->tankeLeminStart($terraschiff_id, $planeten_id);
				$temp_bedingung = false;
				if($warm) $temp_bedingung = $planeten_temp >= $optimale_temp;
				else $temp_bedingung = $planeten_temp <= $optimale_temp;
				if($temp_bedingung) {
					@mysql_query("UPDATE skrupel_schiffe SET spezialmission=0 WHERE id='$terraschiff_id'");
					$neuer_planet = @mysql_fetch_array($neuer_planet);
					if($neuer_planet == null || $neuer_planet[2] == null || $neuer_planet[2] == 0) continue;
					$neue_temp = $neuer_planet['temp'] - 35;
					$temp_bedingung = false;
					if($warm) $temp_bedingung = $neue_temp >= $optimale_temp;
					else $temp_bedingung = $neue_temp <= $optimale_temp;
					if($temp_bedingung) continue;
					$warp = $this->ermittleMaximumWarp($terraschiff_id);
					$this->fliegeSchiff($terraschiff_id, $neuer_planet['x_pos'], $neuer_planet['y_pos'], 
										$warp, $neuer_planet['id']);
				} else @mysql_query("UPDATE skrupel_schiffe SET spezialmission=5 WHERE id='$terraschiff_id'");
			}
		}
	}
	
	/**
	 * Aktualisiert die Tabelle skrupel_ki_spezialschiffe mit allen noch nicht eingetragenen 
	 * viralen Invasion-Schiffen. 
	 */
	function updateViraleInvasionSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		$spezial_schiffe_datenbank = @mysql_query("SELECT s.id FROM skrupel_schiffe s, 
			skrupel_ki_spezialschiffe sp WHERE (s.id=sp.schiff_id) AND (s.spiel='$spiel_id') 
			AND (sp.spezial_mission=1)");
		$spezial_schiffe = array();
		while($virale_id = @mysql_fetch_array($spezial_schiffe_datenbank)) {
			$spezial_schiffe[] = $virale_id['id'];
		}
		foreach(eigenschaften::$virale_ids as $virale_id) {
			if(!(in_array($virale_id, $spezial_schiffe))) {
				$next_id = ki_basis::ermittleNaechsteID("skrupel_ki_spezialschiffe");
				@mysql_query("INSERT INTO skrupel_ki_spezialschiffe (id, schiff_id, spezial_mission, aktiv) 
						VALUES ('$next_id', '$virale_id', '1', '1')");
			}
		}
	}
	
	/**
	 * Ermittelt alle nicht-aktiven Schiffe mit viraler Invasion, weist diese den Jaegern zu und 
	 * gibt sie als Array zurueck.
	 * returns: Die X- und Y-Koordinaten sowie die Datenbank-IDs aller nicht-aktiven Schiffe 
	 * mit viraler Invasion.
	 */
	function ermittleNichtAktiveViraleInvasionSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$nichtaktive_virale_Schiffe = @mysql_query("SELECT s.id, s.kox, s.koy FROM skrupel_schiffe s, 
				skrupel_ki_spezialschiffe sp WHERE (s.id=sp.schiff_id) AND (s.spiel='$spiel_id') 
				AND (sp.spezial_mission=1) AND (sp.aktiv=0) AND (s.besitzer='$comp_id')");
		$schiffe_infos = array();
		while($virales_schiff = @mysql_fetch_array($nichtaktive_virale_Schiffe)) {
			eigenschaften::$jaeger_ids[] = $virales_schiff['id'];
			$schiffe_infos[] = array('x'=>$virales_schiff['kox'], 'y'=>$virales_schiff['koy'], 
									 'id'=>$virales_schiff['id']);
		}
		return $schiffe_infos;
	}
	
	/**
	 * Ermittelt alle aktiven Schiffe mit viraler Invasion und gibt diese als Array zurueck.
	 * returns: Die Datenbank-IDs aller viralen Invasion-Schiffe, die aktiv sind.
	 */
	function ermittleAktiveViraleInvasionSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$aktive_virale_Schiffe = @mysql_query("SELECT s.id FROM skrupel_schiffe s, 
				skrupel_ki_spezialschiffe sp WHERE (s.id=sp.schiff_id) AND (s.spiel='$spiel_id') 
				AND (sp.spezial_mission=1) AND (sp.aktiv=1) AND (s.besitzer='$comp_id')");
		$aktive_schiffe = array();
		while($virale_id = @mysql_fetch_array($aktive_virale_Schiffe)) {
			$aktive_schiffe[] = $virale_id['id'];
		}
		return $aktive_schiffe;
	}
	
	/**
	 * Schaltet das virale Invasion-Schiff ab und fuegt das Schiff zu den Jaegern hinzu.
	 * arguments: $virale_id - Die Datenbank-ID des Schiffs, dass abgeschaltet werden soll.
	 */
	function schalteViraleInvasionSchiffAb($virale_id) {
		$this->deaktiviereSpezialschiff($virale_id, 0);
		eigenschaften::$jaeger_ids[] = $virale_id;
		@mysql_query("UPDATE skrupel_schiffe SET spezialmisson=0 WHERE id='$virale_id'");
	}
	
	/**
	 * Waehlt den naechst-gelegenden Planet aus den uebergebenen Planeten aus und setzt einen Kurs auf 
	 * diesen. Ausserdem werden dessen Koordinaten aus dem uebergebenen Planeten-Array entfernt und 
	 * dieses neue Array zurueckgegeben.
	 * arguments: $virale_id - Die Datenbank-ID des Schiffs, das einen Kurs zu einem der uebergebenen 
	 * 						   Planeten bekommen soll.
	 * 			  $planeten_daten - Array mit den Datenbank-IDs Koordinaten von Planeten mit unerwuenschten 
	 * 								Spezies.
	 * returns: Das Planeten-Array ohne die Koordinaten, die das Schiff anfliegt.
	 * 			Dasselbe Array, falls kein Kurs gesetzt wurde.
	 */
	function fliegeViraleInvasionSchiff($virale_id, $planeten_daten) {
		$schiff_daten = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$virale_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$x_pos = $schiff_daten['kox'];
		$y_pos = $schiff_daten['koy'];
		$nahes_ziel = ki_basis::ermittleNahesZiel($virale_id, $planeten_daten, null);
		if($nahes_ziel == null || $nahes_ziel['id'] == null || $nahes_ziel['id'] == 0) {
			$this->schalteViraleInvasionSchiffAb($virale_id);
			return $planeten_daten;
		}
		$warp = $this->ermittleMaximumWarp($virale_id);
		$this->fliegeSchiff($virale_id, $nahes_ziel['x'], $nahes_ziel['y'], $warp, $nahes_ziel['id']);
		$index = 0;
		for(; $index < count($planeten_daten); $index++) {
			if($planeten_daten[$index]['x'] == $nahes_ziel['x'] 
			&& $planeten_daten[$index]['y'] == $nahes_ziel['y']) break;
		}
		unset($planeten_daten[$index]);
		return array_values($planeten_daten);
	}
	
	/**
	 * Verwaltet alle Schiffe mit viraler Invasion.
	 */
	function verwalteViraleInvasionSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$this->updateViraleInvasionSchiffe();
		$ziel_planeten = schiffe_basis::ermittlePlanetenFuerViraleInvasionSchiffe();
		$planeten_daten = array();
		//Hier werden alle Koordinaten fuer die Ziel-Planeten ermittelt, damit der naheste Planet 
		//ermittelt werden kann.
		if($ziel_planeten != null) {
			foreach($ziel_planeten as $planeten_id) {
				$daten = @mysql_query("SELECT x_pos, y_pos, id FROM skrupel_planeten WHERE id='$planeten_id'");
				$daten = @mysql_fetch_array($daten);
				$planeten_daten[] = array('x'=>$daten['x_pos'], 'y'=>$daten['y_pos'], 'id'=>$daten['id']);
			}
		}
		$nicht_aktive_schiffe = $this->ermittleNichtAktiveViraleInvasionSchiffe();
		$aktive_virale_ids = $this->ermittleAktiveViraleInvasionSchiffe();
		//Falls keine viralen Schiffe aktiv sind, es aber Bedarf fuer eines gibt, wird das naechst-gelegene
		//virale Schiffe ermittelt und reaktiviert.
		if(count($aktive_virale_ids) == 0 && count($ziel_planeten) > 0) {
			foreach($planeten_daten as $koord) {
				$kleinste_strecke = 99999;
				$beste_schiff_koords = null;
				foreach($nicht_aktive_schiffe as $schiff) {
					$strecke = floor(ki_basis::berechneStrecke($koord['x'], $koord['y'], 
															   $schiff['x'], $schiff['y']));
					if($kleinste_strecke > $strecke) {
						$beste_schiff_koords = $schiff;
						$kleinste_strecke = $strecke;
					}
					$schiff_id = $beste_schiff_koords['id'];
					@mysql_query("UPDATE skrupel_ki_spezialschiffe SET aktiv=1 WHERE schiff_id='$schiff_id'");
					$aktive_virale_ids[] = $schiff_id;
				}
			}
		}
		foreach($aktive_virale_ids as $virale_id) {
			//Falls alle Ziel-Planeten schon von anderen viralen Invasion-Schiffen angeflogen werden, 
			//wird die virale Invasion von diesem Schiff deaktiviert.
			if($ziel_planeten == null || count($planeten_daten) == 0) {
				$this->schalteViraleInvasionSchiffAb($virale_id);
				continue;
			}
			$schiff_daten = @mysql_query("SELECT status, lemin, leminmax, kox, koy FROM skrupel_schiffe 
					WHERE id='$virale_id'");
			$schiff_daten = @mysql_fetch_array($schiff_daten);
			$status = $schiff_daten['status'];
			$lemin_schiff = $schiff_daten['lemin'];
			$lemin_max = $schiff_daten['leminmax'];
			$x_pos = $schiff_daten['kox'];
			$y_pos = $schiff_daten['koy'];
			$this->scanneUmgebung($virale_id);
			if($status == 2) {
				$planeten_daten = @mysql_query("SELECT id, native_kol, sternenbasis FROM skrupel_planeten 
						WHERE (spiel='$spiel_id') AND (x_pos='$x_pos') AND (y_pos='$y_pos')");
				$planeten_daten = @mysql_fetch_array($planeten_daten);
				$planeten_id = $planeten_daten['id'];
				$planeten_native = $planeten_daten['native_kol'];
				$sternenbasis = $planeten_daten['sternenbasis'];
				if(in_array($planeten_id, schiffe_basis::ermittlePlanetenFuerViraleInvasionSchiffe())) {
					if($planeten_native >= eigenschaften::$spezialschiffe_infos->min_native_invasion) 
						@mysql_query("UPDATE skrupel_schiffe SET spezialmission=18 WHERE id='$virale_id'");
					else {
						@mysql_query("UPDATE skrupel_schiffe SET spezialmission=0 WHERE id='$virale_id'");
						@mysql_query("DELETE FROM skrupel_ki_planeten WHERE (planeten_id='$planeten_id') 
								AND (comp_id='$comp_id')");
						$planeten_daten = $this->fliegeViraleInvasionSchiff($virale_id, $planeten_daten);
						//Lemin wird obligatorisch getankt.
						$this->tankeLemin($virale_id, $planeten_id);
					}
					continue;
				}
				$planeten_daten = $this->fliegeViraleInvasionSchiff($virale_id, $planeten_daten);
				//Lemin wird obligatorisch getankt.
				if($lemin_schiff == 0 && $sternenbasis == 2) 
					$this->tankeLeminStart($virale_id, $planeten_id);
				else $this->tankeLemin($virale_id, $planeten_id);//Lemin wird obligatorisch getankt.
				if($lemin_schiff == 0 && $sternenbasis == 2) 
					$this->tankeLeminStart($virale_id, $planeten_id);
				else $this->tankeLemin($virale_id, $planeten_id);
				continue;
			}
			$strecken_lemin = $this->ermittleStreckenVerbrauch($virale_id);
			if($lemin_schiff < (eigenschaften::$jaeger_infos->min_jaeger_lemin_prozent * $strecken_lemin / 100) 
			&& $status != 2) {
				$this->fliegeTanken($virale_id);
				@mysql_query("UPDATE skrupel_schiffe SET routing_schritt=0, routing_koord='' 
					WHERE id='$virale_id'");
				continue;
			}
			$ziel_koords = @mysql_query("SELECT zielx, ziely FROM skrupel_schiffe WHERE id='$virale_id'");
			$ziel_koords = @mysql_fetch_array($ziel_koords);
			if($ziel_koords['zielx'] == 0 && $ziel_koords['ziely'] == 0) 
				$planeten_daten = $this->fliegeViraleInvasionSchiff($virale_id, $planeten_daten);
		}
	}
	
	/**
	 * Aktualisiert die Tabelle skrupel_ki_spezialschiffe mit allen noch nicht eingetragenen 
	 * Taster-Schiffen.
	 */
	function updateTasterSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$spezial_schiffe_datenbank = @mysql_query("SELECT k.schiff_id FROM skrupel_schiffe s, 
				skrupel_ki_spezialschiffe k WHERE (s.id=k.schiff_id) AND (s.spiel='$spiel_id') 
				AND (k.spezial_mission=2) AND (s.besitzer='$comp_id')");
		$spezial_schiffe = array();
		while($spezial_schiff = @mysql_fetch_array($spezial_schiffe_datenbank)) {
			$spezial_schiffe[] = $spezial_schiff['schiff_id'];
		}
		foreach(eigenschaften::$taster_ids as $taster_id) {
			if(!(in_array($taster_id, $spezial_schiffe))) {
				$next_id = ki_basis::ermittleNaechsteID("skrupel_ki_spezialschiffe");
				@mysql_query("INSERT INTO skrupel_ki_spezialschiffe (id, schiff_id, spezial_mission, aktiv) 
						VALUES ('$next_id', '$taster_id', '2', '1')");
			}
		}
	}
	
	/**
	 * Bestimmt das Taster-Schiff aus den uebergebenen Schiffen (Array mit ID, X- und Y-Koordinaten), das die 
	 * kuerzeste Entfernung zu einem wichtigen Planten hat, entfernt dies aus dem uebergebenen Array und 
	 * deaktiviert das Taster-Schiff.
	 * arguments: $aktive_schiffe - Ein Array mit Datenbank-IDs, X- und Y-Koordinaten von Taster-Schiffen.
	 */
	function ermittleNaechstesTasterSchiff($taster_schiffe) {
		$kleinste_strecke = 99999;
		$bestes_schiff = null;
		foreach(eigenschaften::$wichtige_planeten_ids as $planeten_id) {
			$planeten_infos = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$planeten_id'");
			$planeten_infos = @mysql_fetch_array($planeten_infos);
			$x_pos = $planeten_infos['x_pos'];
			$y_pos = $planeten_infos['y_pos'];
			foreach($taster_schiffe as $schiff) {
				$strecke = floor(ki_basis::berechneStrecke($x_pos, $y_pos, $schiff['x'], $schiff['y']));
				if($kleinste_strecke > $strecke) {
					$bestes_schiff = $schiff;
					$kleinste_strecke = $strecke;
				}
			}
		}
		$this->deaktiviereSpezialschiff($bestes_schiff['id'], 0);
	}
	
	/**
	 * Ermittelt alle aktiven Schiffe mit Strukturtaster und gibt diese als Array zurueck.
	 * returns: Die Datenbank-IDs aller Taster-Schiffe, die aktiv sind.
	 */
	function ermittleAktiveTasterSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$aktive_taster_Schiffe = @mysql_query("SELECT s.id, s.kox, s.koy FROM skrupel_schiffe s, 
				skrupel_ki_spezialschiffe k WHERE (s.id=k.schiff_id) AND (s.spiel='$spiel_id') 
				AND (k.spezial_mission=2) AND (k.aktiv=1) AND (s.besitzer='$comp_id')");
		$aktive_schiffe = array();
		while($taster_infos = @mysql_fetch_array($aktive_taster_Schiffe)) {
			$aktive_schiffe[] = array('x'=>$taster_infos['kox'], 'y'=>$taster_infos['koy'], 
									  'id'=>$taster_infos['id']);
		}
		$aktive_anzahl = count($aktive_schiffe);
		$zaehler = 0;
		if(count(eigenschaften::$schiff_ids) == 0) return null;
		while($aktive_anzahl / count(eigenschaften::$schiff_ids) * 100 
		> eigenschaften::$spezialschiffe_infos->max_aktive_taster_prozent) {
			if($zaehler == count($aktive_schiffe)) break;
			$this->ermittleNaechstesTasterSchiff($aktive_schiffe);
			$zaehler++;
			$aktive_anzahl--;
		}
		$aktive_taster_Schiffe = @mysql_query("SELECT s.id FROM skrupel_schiffe s, 
				skrupel_ki_spezialschiffe k WHERE (s.id=k.schiff_id) AND (s.spiel='$spiel_id') 
				AND (k.spezial_mission=2) AND (k.aktiv=1) AND (s.besitzer='$comp_id')");
		$aktive_schiffe = array();
		while($taster_infos = @mysql_fetch_array($aktive_taster_Schiffe)) {
			$aktive_schiffe[] = $taster_infos['id'];
		}
		return $aktive_schiffe;
	}
	
	/**
	 * Setzt einen Kurs fuer das uebergebene Taster-Schiff. Zuerst werden sichtbare feindliche Schiffe 
	 * angesteuert. Sind keine Feind-Schiffe in Sicht, so wird ein Erkundungskurs gewaehlt.
	 * arguments: $taster_id - Die Datenbank-ID des Taster-Schiffes, das einen Kurs bekommen soll.
	 */
	function fliegeTasterSchiff($taster_id) {
		$warp = $this->ermittleMaximumWarp($taster_id);
		$schiff_pos = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$subraum_id'");
		$schiff_pos = @mysql_fetch_array($schiff_pos);
		$x_start = $schiff_pos['kox'];
		$y_start = $schiff_pos['koy'];
		$gegner_ziel = null;
		//Zuerst werden alle anderen sichtbaren Feindschiffe ueberprueft.
		if(count(eigenschaften::$sichtbare_gegner_schiffe) > 0) {
			$gegner_ziel = ki_basis::ermittleNahesZiel($taster_id, eigenschaften::$sichtbare_gegner_schiffe, 
							eigenschaften::$bekannte_wurmloch_daten);
		}
		if($gegner_ziel != null && $gegner_ziel['id'] != null && $gegner_ziel['id'] != 0) {
			$this->fliegeSchiff($taster_id, $gegner_ziel['x'], $gegner_ziel['y'], $warp, $gegner_ziel['id']);
			return;
		}
		$gegner_ziel = null;
		//Da keine passenden Ziele gefunden wurden, wird nun ein Erkundungsziel gesetzt.
		$gegner_ziel = $this->erkunde($taster_id);
		$this->fliegeSchiff($taster_id, $gegner_ziel['x'], $gegner_ziel['y'], $warp, $gegner_ziel['id']);
	}
	
	/**
	 * Verwaltet alle Schiffe mit Strukturtaster.
	 */
	function verwalteTasterSchiffe() {
		$spiel_id = eigenschaften::$spiel_id;
		$this->updateTasterSchiffe();
		$aktive_taster_ids = $this->ermittleAktiveTasterSchiffe();
		if($aktive_taster_ids == null) return;
		foreach($aktive_taster_ids as $taster_id) {
			$this->setzeAggressivitaet($taster_id, 9);
			$this->setzeTaktik($taster_id, 1);
			@mysql_query("UPDATE skrupel_schiffe SET routing_status=0, routing_id='' WHERE id='$taster_id'");
			$taster_infos = @mysql_query("SELECT kox, koy, lemin, status FROM skrupel_schiffe 
				WHERE id='$taster_id'");
			$taster_infos = @mysql_fetch_array($taster_infos);
			$warp = $this->ermittleMaximumWarp($taster_id);
			$x_pos = $taster_infos['kox'];
			$y_pos = $taster_infos['koy'];
			$schiff_lemin = $taster_infos['lemin'];
			$schiff_status = $taster_infos['status'];
			$this->scanneUmgebung($taster_id);
			if($this->reagiereAufWurmloch($taster_id)) continue;
			if($schiff_status == 2) {
				$planeten_infos = @mysql_query("SELECT id, besitzer, sternenbasis FROM skrupel_planeten 
					WHERE (spiel='$spiel_id') AND (x_pos='$x_pos') AND (y_pos='$y_pos')");
				$planeten_infos = @mysql_fetch_array($planeten_infos);
				$planeten_id = $planeten_infos['id'];
				$planeten_besitzer = $planeten_infos['besitzer'];
				$sternenbasis = $planeten_infos['sternenbasis'];
				if($planeten_besitzer == eigenschaften::$comp_id) 
					$this->leereFrachtRaum($taster_id, $planeten_id);
				$this->fliegeTasterSchiff($taster_id);
				if($schiff_lemin == 0 && $sternenbasis == 2) 
					$this->tankeLeminStart($taster_id, $planeten_id);
				else $this->tankeLemin($taster_id, $planeten_id);
				continue;
			}
			$strecken_lemin = $this->ermittleStreckenVerbrauch($taster_id);
			if($schiff_lemin < (eigenschaften::$jaeger_infos->min_jaeger_lemin_prozent * $strecken_lemin / 100) 
			&& $schiff_status != 2) {
				$this->fliegeTanken($taster_id);
				continue;
			}
			//Hat das Schiff aus welch Gruenden auch immer kein Ziel, so wird eins bestimmt.
			$flug_daten = @mysql_query("SELECT zielx, ziely, kox, koy FROM skrupel_schiffe 
				WHERE id='$taster_id'");
			$flug_daten = @mysql_fetch_array($flug_daten);
			if(($flug_daten['zielx'] == 0 && $flug_daten['ziely'] == 0) 
			|| ($flug_daten['zielx'] == $flug_daten['kox'] && $flug_daten['ziely'] == $flug_daten['koy'])) 
				$this->fliegeTasterSchiff($taster_id);
		}
	}
	
	/**
	 * Verwaltet die Spezialschiffe der KI.
	 */
	function verwalteSpezialSchiffe() {
		$this->verwalteClusterSchiffe();
		$this->verwalteQuarkSchiffe();
		$this->verwalteCyberSchiffe();
		$this->verwalteTasterSchiffe();
		$this->verwalteTerraSchiffe(true);
		$this->verwalteTerraSchiffe(false);
		$this->verwalteViraleInvasionSchiffe();
	}
}
?>