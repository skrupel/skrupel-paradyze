<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Impementierung von frachter_basis.
 */
class frachter_leicht extends frachter_basis {
	
	/**
	 * Prueft, ob es sinnvoll oder erforderlich ist, weiter zu kolonisieren. In den ersten Runden sollte
	 * kolonisiert werden. Es wird spaeter geprueft, ob der KI-Spieler angegriffen wird oder bei Planeten
	 * auf Rang 1 ist.
	 * returns: true, falls weiter kolonisiert werden sollte.
	 * 			false, sonst.
	 */
	static function mehrKolonien() {
		if(eigenschaften::$tick <= eigenschaften::$frachter_kolo_infos->kolo_anfangs_runden 
		|| eigenschaften::$frachter_kolo_infos->min_kolonien > count(eigenschaften::$kolonien_ids)) 
			return true;
		if(count(eigenschaften::$gegner_angriffe) > eigenschaften::$frachter_kolo_infos->min_angriffe) 
			return false;
		$planeten_string = "spieler_".eigenschaften::$comp_id."_planeten";
		$spiel_id = eigenschaften::$spiel_id;
		$planeten_rang = @mysql_query("SELECT $planeten_string FROM skrupel_spiele WHERE id='$spiel_id'");
		$planeten_rang = @mysql_fetch_array($planeten_rang);
		return ($planeten_rang[$planeten_string] != 1);
	}
	
	/**
	 * Ermittelt den wichtigen Rohstoff des uebergebenen Planeten. Diese Implementierung ermittelt nur den 
	 * Rohstoff, der am wenigsten auf den Planeten ist.
	 * arguments: $planeten_id - Die Datenbank-ID, dessen wichtiger Rohstoff ermittelt werden soll.
	 * returns: 0 - Baxterium | 1 - Renurbin | 2 - Vormisaan.
	 */
	function ermittleWichtigenRohstoff($planeten_id) {
		$planeten_res = @mysql_query("SELECT min1, min2, min3 FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_res = @mysql_fetch_array($planeten_res);
		if($planeten_res['min1'] <= $planeten_res['min2'] 
		&& $planeten_res['min1'] <= $planeten_res['min3']) return 0;
		if($planeten_res['min2'] <= $planeten_res['min1'] 
		&& $planeten_res['min2'] <= $planeten_res['min3']) return 1;
		return 2;
	}
	
	/**
	 * Belaedt den uebergebenen Frachter mit den Resourcen des uebergebenen Planeten. Es wird nicht ueberprueft, 
	 * ob sich der Frachter bei diesem Planeten befindet!
	 * arguments: $schiff_id - Die Datenbank-ID des Frachters, dass die Resourcen des Planeten abernten soll.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, der vom Schiff abgeerntet werden soll.
	 * returns: Den nach des Beladevorgangs uebrige leere Frachtraum des Schiffes. Ist 0, falls das Schiff voll 
	 * 			beladen wurde.
	 */
	function beladeFrachter($schiff_id, $planeten_id) {
		$schiff_infos = @mysql_query("SELECT fracht_cantox, fracht_vorrat, fracht_min1, fracht_min2, fracht_min3, 
			fracht_leute, frachtraum, routing_status, routing_id, kox, koy FROM skrupel_schiffe 
			WHERE id='$schiff_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$frachtraum = $schiff_infos['frachtraum'];
		if($frachtraum == 0) return 0;
		$schiffcantox = $schiff_infos['fracht_cantox'];
		$schiffvorrat = $schiff_infos['fracht_vorrat'];
		$schiffmin1 = $schiff_infos['fracht_min1'];
		$schiffmin2 = $schiff_infos['fracht_min2'];
		$schiffmin3 = $schiff_infos['fracht_min3'];
		$schiffleute = $schiff_infos['fracht_leute'];
		$routing_status = $schiff_infos['routing_status'];
		$abgabe_planet = null;
		if($routing_status == 2) {
			$routing_ids = $schiff_infos['routing_id'];
			$routing_ids = explode(':', $routing_ids);
			$routen_laenge = count($routing_ids) - 2;
			$abgabe_planet = $routing_ids[$routen_laenge];
		} else {
			$abgabe_planet = ki_basis::ermittleNahenWichtigenPlaneten($schiff_infos['kox'], $schiff_infos['koy'], 
																	  eigenschaften::$wichtige_planeten_ids);
			$abgabe_planet = $abgabe_planet['id'];
		}
		$freier_frachtraum = $frachtraum - ($schiffvorrat + $schiffmin1 + $schiffmin2 + $schiffmin3 
											+ $schiffleute / 100);
		if($freier_frachtraum == 0) return 0;
		$planeten_res = @mysql_query("SELECT cantox, vorrat, min1, min2, min3, fabriken FROM skrupel_planeten 
			WHERE id='$planeten_id'");
		$planeten_res = @mysql_fetch_array($planeten_res);
		$planetcantox = $planeten_res['cantox'];
		$planetvorrat = $planeten_res['vorrat'];
		$planetmin1 = $planeten_res['min1'];
		$planetmin2 = $planeten_res['min2'];
		$planetmin3 = $planeten_res['min3'];
		$planet_fabriken = $planeten_res['fabriken'];
		if($schiffleute < eigenschaften::$frachter_kolo_infos->kolo_leute && $planet_fabriken != 0) {
			$schiffcantox += $planetcantox;
			//Ist bei dem Planeten, der beliefert werden soll, kein Schiff mit Subpartikelcluster, so werden die 
			//Vorraete "verkauft", sodass Cantox daraus gewonnen werden.
			$cluster_id = schiffe_basis::ermittleClusterSchiffID();
			if(!($this->ermittleSchiffanPlaneten($abgabe_planet, $cluster_id['id']))) {
				$schiffcantox += $planetvorrat;
				$planetvorrat = 0;
			}
			$planetcantox = 0;
		}
		$wichtiger_rohstoff = $this->ermittleWichtigenRohstoff($abgabe_planet);
		switch($wichtiger_rohstoff) {
			case 0: {
				$schiffmin1_neu = $freier_frachtraum;
				if($schiffmin1_neu > $planetmin1) $schiffmin1_neu = $planetmin1;
				$freier_frachtraum -= $schiffmin1_neu;
				$schiffmin1 += $schiffmin1_neu;
				$planetmin1 -= $schiffmin1_neu;
				break;
			}
			case 1: {
				$schiffmin2_neu = $freier_frachtraum;
				if($schiffmin2_neu > $planetmin2) $schiffmin2_neu = $planetmin2;
				$freier_frachtraum -= $schiffmin2_neu;
				$schiffmin2 += $schiffmin2_neu;
				$planetmin2 -= $schiffmin2_neu;
				break;
			}
			case 2: {
				$schiffmin3_neu = $freier_frachtraum;
				if($schiffmin3_neu > $planetmin3) $schiffmin3_neu = $planetmin3;
				$freier_frachtraum -= $schiffmin3_neu;
				$schiffmin3 += $schiffmin3_neu;
				$planetmin3 -= $schiffmin3_neu;
				break;
			}
		}
		if($wichtiger_rohstoff != 0) {
			$schiffmin1_neu = floor($freier_frachtraum / 3);
			if($schiffmin1_neu > $planetmin1) $schiffmin1_neu = $planetmin1;
			$freier_frachtraum -= $schiffmin1_neu;
			$schiffmin1 += $schiffmin1_neu;
			$planetmin1 -= $schiffmin1_neu;
		}
		if($wichtiger_rohstoff != 1) {
			$schiffmin2_neu = floor($freier_frachtraum / 3);
			if($schiffmin2_neu > $planetmin2) $schiffmin2_neu = $planetmin2;
			$freier_frachtraum -= $schiffmin2_neu;
			$schiffmin2 += $schiffmin2_neu;
			$planetmin2 -= $schiffmin2_neu;
		}
		if($wichtiger_rohstoff != 2) {
			$schiffmin3_neu = floor($freier_frachtraum / 3);
			if($schiffmin3_neu > $planetmin3) $schiffmin3_neu = $planetmin3;
			$freier_frachtraum -= $schiffmin3_neu;
			$schiffmin3 += $schiffmin3_neu;
			$planetmin3 -= $schiffmin3_neu;
		}
		if($planet_fabriken != 0) {
			$schiffvorrat_neu = ceil($freier_frachtraum / 3);
			if($schiffvorrat_neu > $planetvorrat) $schiffvorrat_neu = $planetvorrat;
			$freier_frachtraum -= $schiffvorrat_neu;
			$schiffvorrat += $schiffvorrat_neu;
			$planetvorrat -= $schiffvorrat_neu;
		}
		if($schiffcantox < 0) $schiffcantox = 0;
		if($schiffvorrat < 0) $schiffvorrat = 0;
		if($schiffmin1 < 0) $schiffmin1 = 0;
		if($schiffmin2 < 0) $schiffmin2 = 0;
		if($schiffmin3 < 0) $schiffmin3 = 0;
		if($planetcantox < 0) $planetcantox = 0;
		if($planetvorrat < 0) $planetvorrat = 0;
		if($planetmin1 < 0) $planetmin1 = 0;
		if($planetmin2 < 0) $planetmin2 = 0;
		if($planetmin3 < 0) $planetmin3 = 0;
		@mysql_query("UPDATE skrupel_schiffe SET fracht_cantox='$schiffcantox', fracht_vorrat='$schiffvorrat', 
			fracht_min1='$schiffmin1', fracht_min2='$schiffmin2', fracht_min3='$schiffmin3' 
			WHERE id='$schiff_id'");
		@mysql_query("UPDATE skrupel_planeten SET cantox='$planetcantox', vorrat='$planetvorrat', 
			min1='$planetmin1', min2='$planetmin2', min3='$planetmin3' WHERE id='$planeten_id'");
		return $freier_frachtraum;
	}
	
	/**
	 * Verwaltet die Route des uebergebenen Schiffs. Das Schiff wird zum naechsten Planeten in der Route geflogen.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Route verwaltet werden soll.
	 */
	function verwalteRoute($schiff_id) {
		$routen_daten = @mysql_query("SELECT kox, koy, routing_id, status, lemin, zielid, frachtraum 
			FROM skrupel_schiffe WHERE id='$schiff_id'");
		$routen_daten = @mysql_fetch_array($routen_daten);
		$x_pos = $routen_daten['kox'];
		$y_pos = $routen_daten['koy'];
		$routen_planeten_ids = explode(':', $routen_daten['routing_id']);
		$schiff_status = $routen_daten['status'];
		$schiff_lemin = $routen_daten['lemin'];
		$aktuelle_ziel_id = $routen_daten['zielid'];
		$frachtraum = $routen_daten['frachtraum'];
		$warp = $this->ermittleMaximumWarp($schiff_id);
		if($schiff_status != 2) {
			//Das Schiff fliegt tanken, falls nicht mehr genug Lemin da ist.
			$strecken_lemin = $this->ermittleStreckenVerbrauch($schiff_id);
			if($schiff_lemin < (eigenschaften::$jaeger_infos->min_frachter_lemin_prozent * $strecken_lemin/100)) {
				$this->fliegeTanken($schiff_id);
				return;
			}
			//Dass Schiff nimmt die Route wieder auf und fliegt zum naechsten Planeten in der Route, falls das 
			//aktuelle Ziel keine Routen-Planet ist.
			if(!in_array($aktuelle_ziel_id, $routen_planeten_ids) || $aktuelle_ziel_id == null) {
				$alter_planet = $this->ermittleLetztenRoutenPlaneten($schiff_id);
				$ziel_daten = $this->ermittleNaechstenRoutenPlaneten($schiff_id, $alter_planet);
				$this->fliegeSchiff($schiff_id, $ziel_daten['x'], $ziel_daten['y'], $warp, $ziel_daten['id']);
			}
			return;
		}
		$spiel_id = eigenschaften::$spiel_id;
		$planeten_infos = @mysql_query("SELECT id, sternenbasis FROM skrupel_planeten WHERE (x_pos='$x_pos') 
			AND (y_pos='$y_pos') AND (spiel='$spiel_id')");
		$planeten_infos = @mysql_fetch_array($planeten_infos);
		$planeten_id = $planeten_infos['id'];
		$sternenbasis = $planeten_infos['sternenbasis'];
		if($sternenbasis != 2) $this->tankeLemin($schiff_id, $planeten_id);
		$freier_frachtraum = $this->ermittleFreienFrachtraum($schiff_id);
		$routen_laenge = count($routen_planeten_ids) - 1;
		$alter_planet = $this->ermittleLetztenRoutenPlaneten($schiff_id);
		//Falls der aktuelle Planet kein Routen-Planet ist, wird zum naechsten Planeten in der Route geflogen.
		if(!in_array($planeten_id, $routen_planeten_ids) && $freier_frachtraum != 0 && $alter_planet == null) {
			$this->tankeLemin($schiff_id, $planeten_id);
			if($alter_planet == null || $alter_planet == 0) 
				$alter_planet = $ziel_daten = $routen_planeten_ids[$routen_laenge - 1];
			$ziel_daten = $this->ermittleNaechstenRoutenPlaneten($schiff_id, $alter_planet);
			$this->fliegeSchiff($schiff_id, $ziel_daten['x'], $ziel_daten['y'], $warp, $ziel_daten['id']);
			return;
		}
		//Ist der Frachter beim Planeten angelangt, der beliefert werden soll (der letzte Planet im Array), so 
		//wird der Frachtraum geleert, etwas Lemin getankt und Kurs auf den ersten Planeten der Route gesetzt.
		if($routen_planeten_ids[$routen_laenge - 1] == $planeten_id) {
			$alter_planet = $this->ermittleLetztenRoutenPlaneten($schiff_id);
			$ziel_daten = $this->ermittleNaechstenRoutenPlaneten($schiff_id, $alter_planet);
			$this->leereFrachtRaum($schiff_id, $planeten_id);
			if($frachtraum == $freier_frachtraum && $alter_planet != null && $ziel_daten['id'] != null 
			&& $ziel_daten['id'] != 0 && $ziel_daten['id'] != $planeten_id) {
				$neue_zielid = $ziel_daten['id'];
				$planet_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten 
					WHERE id='$neue_zielid'");
				$planet_koords = @mysql_fetch_array($planet_koords);
				$this->fliegeSchiff($schiff_id, $planet_koords['x_pos'], $planet_koords['y_pos'], $warp, 
									$neue_zielid);
				$this->tankeLeminStart($schiff_id, $planeten_id);
				return;
			}
			$ziel = $routen_planeten_ids[0];
			$planet_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$ziel'");
			$planet_koords = @mysql_fetch_array($planet_koords);
			$this->fliegeSchiff($schiff_id, $planet_koords['x_pos'], $planet_koords['y_pos'], $warp, $ziel);
			$this->updateLogBuchRoute($schiff_id, $planeten_id);
			$this->tankeLeminStart($schiff_id, $planeten_id);
			return;
		}
		$freier_raum = $this->beladeFrachter($schiff_id, $planeten_id);
		//Ist der Frachtraum voll, so wird sofort zum Planeten geflogen, der beladen werden soll.
		if($freier_raum == 0) {
			$ziel = $routen_planeten_ids[$routen_laenge - 1];
			$planet_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$ziel'");
			$planet_koords = @mysql_fetch_array($planet_koords);
			$this->fliegeSchiff($schiff_id, $planet_koords['x_pos'], $planet_koords['y_pos'], $warp, $ziel);
			$this->updateLogBuchRoute($schiff_id, $planeten_id);
			return;
		}
		$ziel_daten = $this->ermittleNaechstenRoutenPlaneten($schiff_id, $planeten_id);
		$neue_zielid = $ziel_daten['id'];
		$planet_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$neue_zielid'");
		$planet_koords = @mysql_fetch_array($planet_koords);
		$this->fliegeSchiff($schiff_id, $planet_koords['x_pos'], $planet_koords['y_pos'], $warp, $neue_zielid);
		$this->updateLogBuchRoute($schiff_id, $planeten_id);
	}
	
	/**
	 * Stellt eine Route fuer das uebergebene Schiff zusammen, um die Planeten in der Route abzuernten. Dabei 
	 * werden keine Planeten in die Route aufgenommen, die bereits in einer anderen Route eines Frachters der 
	 * KI sind.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das eine Route bekommen soll.
	 * 			  $start_planet - Die Datenbank-ID des Planeten, an dem die Resourcen abgeliefert werden sollen.
	 * returns: true, falls eine Route gesetzt werden koennte.
	 * 			false, sonst.
	 */
	function setzeErnteRoute($schiff_id, $start_planet) {
		//Erst werden die Routen aktualisiert, da in einer Runde mehrmals eine Route gesetzt werden kann und es 
		//sonst dennoch zu Routen zu gleichen Planeten kommen kann.
		$this->ermittleRouten();
		//Falls schon genug Routen zum Start-Planeten existieren, wird keine neue Route dahin erstellt.
		$anzahl_basen = count(eigenschaften::$basen_planeten_ids) + count(eigenschaften::$neue_basis_planeten);
		$routen_pro_basis = ceil(count(eigenschaften::$frachter_ids) - 1 / $anzahl_basen);
		if($this->ermittleRoutenVonPlanet($start_planet) >= $routen_pro_basis) return false;
		$planeten_string = "";
		$planeten_ausnahmen = array_merge($this->ermittlePlanetenInRouten(), eigenschaften::$basen_planeten_ids);
		$planeten_ausnahmen[] = $start_planet;
		$temp_planet = $start_planet;
		$routen_planeten = 0;
		//Nun werden die Planeten-IDs fuer die Route zusammen gestellt.
		for($k=1; $k < count(eigenschaften::$kolonien_ids); $k++) {
			//Hier wird abgebrochen, falls schon genug Planeten fuer die Route zusammengestellt wurden.
			if($k >= eigenschaften::$frachter_route_infos->max_planeten_pro_route) break;
			$planeten_pos = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$temp_planet'");
			$planeten_pos = @mysql_fetch_array($planeten_pos);
			$planet_id = ki_basis::ermittleNahenPlaneten($planeten_pos['x_pos'], $planeten_pos['y_pos'], 
														$planeten_ausnahmen, true, true);
			if($planet_id == null || $planet_id['id'] == 0) break;
			$planet_id = $planet_id['id'];
			$planeten_ausnahmen[] = $planet_id;
			$planeten_string = $planeten_string.$planet_id.":";
			$temp_planet = $planet_id;
			$routen_planeten++;
		}
		$planeten_string = $planeten_string.$start_planet.":";
		//Falls nicht genug Planeten fuer eine Route zusammen gestellt wurden, wird sichergestellt, dass das 
		//Schiff auch wirklich keine Route hat und abgebrochen.
		if($routen_planeten < eigenschaften::$frachter_route_infos->min_planeten_pro_route) {
			@mysql_query("UPDATE skrupel_schiffe SET routing_id='', routing_status=0, logbuch='' 
				WHERE id='$schiff_id'");
			return false;
		} else {
			@mysql_query("UPDATE skrupel_schiffe SET routing_id='$planeten_string', routing_status=2, 
				logbuch='' WHERE id='$schiff_id'");
			return true;
		}
	}
	
	/**
	 * Prueft, ob der uebergebene Frachter eine Route abfliegt.
	 * arguments: $schiff_id - Die Datenbank-ID des Frachters, dessen Route ueberprueft werden soll.
	 * returns: true, falls der Frachter eine Route hat.
	 * 			false, sonst.
	 */
	function FrachterHatRoute($schiff_id) {
		$hat_route = @mysql_query("SELECT routing_status FROM skrupel_schiffe WHERE id='$schiff_id'");
		$hat_route = @mysql_fetch_array($hat_route);
		return ($hat_route['routing_status'] == 2);
	}
	
	/**
	 * Versucht bei allen Basen-Planeten oder neuen Basen-Planeten, die nicht der uebergebene Planet sind, eine 
	 * Ernte-Route zu setzen.
	 * arguments: $frachter_id - Die Datenbank-ID des Frachters, das eine alternative Route bekommen soll.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, zu dem explizit keine Route gesetzt werden soll.
	 * returns: true, falls eine Route gesetzt wurde.
	 * 			false, sonst.
	 */
	function setzeAlternativeRoute($frachter_id, $planeten_id) {
		$ziel_planeten = array_merge(eigenschaften::$basen_planeten_ids, 
									 eigenschaften::$neue_basis_planeten);
		$neues_array = array();
		foreach($ziel_planeten as $planet) {
			if($planet != $planeten_id) {
				$koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten 
					WHERE id='$planet'");
				$koords = @mysql_fetch_array($koords);
				$neues_array[] = array('x'=>$koords['x_pos'], 'y'=>$koords['y_pos'], 'id'=>$planet);
			}
		}
		$max_planeten = count($neues_array);
		for($i=0; $i < $max_planeten; $i++) {
			if(count($neues_array) == 0) return false;
			$neuer_planet = ki_basis::ermittleNahesZiel($frachter_id, $neues_array, null);
			if($this->setzeErnteRoute($frachter_id, $neuer_planet['id'])) {
				$this->verwalteRoute($frachter_id);
				return true;
			} else {
				$temp_array = array();
				foreach($neues_array as $planet) {
					if($planet['id'] != $neuer_planet['id']) $temp_array[] = $planet;
				}
				$neues_array = $temp_array;
			}
		}
		return false;
	}
	
	/**
	 * Analysiert den Status jedes einzelnen Frachters und entscheidet, wie er sich verhalten soll.
	 */
	function verwalteFrachter() {
		$spiel_id = eigenschaften::$spiel_id;
		foreach(eigenschaften::$frachter_ids as $frachter_id) {
			if($this->istAktivesSpezialSchiff($frachter_id)) continue;
			$schiff_daten = @mysql_query("SELECT kox, koy, fracht_leute, lemin, routing_status, status, 
				routing_id, schaden, crew, crewmax FROM skrupel_schiffe WHERE id='$frachter_id'");
			$schiff_daten = @mysql_fetch_array($schiff_daten);
			$warp = $this->ermittleMaximumWarp($frachter_id);
			$x_pos = $schiff_daten['kox'];
			$y_pos = $schiff_daten['koy'];
			$fracht_leute = $schiff_daten['fracht_leute'];
			$schifflemin = $schiff_daten['lemin'];
			$routing_status = $schiff_daten['routing_status'];
			$schiff_status = $schiff_daten['status'];
			$routing_ids = $schiff_daten['routing_id'];
			$schaden = $schiff_daten['schaden'];
			$crew = $schiff_daten['crew'];
			$crew_max = $schiff_daten['crewmax'];
			$this->setzeAggressivitaet($frachter_id, 0);
			$this->setzeTaktik($frachter_id, 2);
			//Falls das Schiff vor einem angreifenden Schiff fliehen muss, werden keine anderen Befehle erteilt.
			if($this->flieheVorSchiff($frachter_id)) continue;
			if($this->reagiereAufWurmloch($frachter_id)) continue;
			//Hier wird geprueft, ob das Schiff durch ein Objekt fliegen muesste, um sein Ziel zu erreichen.
			//Falls ja, wird sogleich ein Ausweich-Kurs gesetzt.
			if($schiff_status != 2 && $this->mussObjektUmfliegen($frachter_id)) continue;
			//Falls der Frachter Schaden erlitten hat, aktiviert er die Schiffreparatur und fliegt zur
			//naechsten Sternenbasis.
			if($schaden >= eigenschaften::$frachter_infos->max_frachter_schaden) {
				if($schiff_status == 2) {
					$planeten_infos = @mysql_query("SELECT id FROM skrupel_planeten 
						WHERE (x_pos='$x_pos') AND (y_pos='$y_pos') AND (spiel='$spiel_id')");
					$planeten_infos = @mysql_fetch_array($planeten_infos);
					$this->tankeLemin($frachter_id, $planeten_infos['id']);
				}
				if($this->SchiffHatFertigkeit($frachter_id, "lloyd")) {
					@mysql_query("UPDATE skrupel_schiffe SET spezialmission=16 WHERE id='$frachter_id'");
					continue;
				}
				if($schiff_status != 2) {
					$this->zuWichtigenPlaneten($frachter_id);
					continue;
				}
			}
			//Als erstes wird der naechste Planet gescannt. Falls dieser sich nicht lohnt, und der Frachter 
			//Kolonisten an Bord hat, wird dieser Planet nicht angeflogen.
			if($this->scanneUmgebung($frachter_id)) continue;
			//Falls eine aktive Route gefunden wird, bekommt der Frachter keine neuen Befehle.
			if($routing_status == 2 && $schiff_status != 2) {
				$this->verwalteRoute($frachter_id);
				continue;
			}
			//Nun wird entschieden, was passiert, wenn der Frachter auf einem Planeten ist:
			if($schiff_status == 2) {
				$planeten_infos = @mysql_query("SELECT sternenbasis, id, besitzer FROM skrupel_planeten 
					WHERE (x_pos='$x_pos') AND (y_pos='$y_pos') AND (spiel='$spiel_id')");
				$planeten_infos = @mysql_fetch_array($planeten_infos);
				$sternenbasis = $planeten_infos['sternenbasis'];
				$planeten_id = $planeten_infos['id'];
				$besitzer = $planeten_infos['besitzer'];
				if(!(in_array($planeten_id, eigenschaften::$wichtige_planeten_ids))) 
					$this->beladeFrachter($frachter_id, $planeten_id);
				if($sternenbasis != 2) {
					if($routing_status == 2) {
						$this->verwalteRoute($frachter_id);
						continue;
					}
					if(in_array($planeten_id, eigenschaften::$wichtige_planeten_ids)) {
						if($this->setzeErnteRoute($frachter_id, $planeten_id)) {
							$this->verwalteRoute($frachter_id);
							continue;
						}
						if($this->setzeAlternativeRoute($frachter_id, $planeten_id)) {
							$this->verwalteRoute($frachter_id);
							continue;
						}
					}
					$this->tankeLemin($frachter_id, $planeten_id);
				} else {
					//Da ein Planet mit Sternenbasis wichtig ist, werden hier alle Resourcen abgeladen.
					$this->leereFrachtRaum($frachter_id, $planeten_id);
					//Falls ein beschaedigter Frachter bei einer Sternenbasis ist, bleibt er fuer die Reparatur 
					//im Orbit.
					if($crew < $crew_max) {
						$neue_crew = $crew_max - $crew;
						$extra = "0:".$neue_crew.":0";
						@mysql_query("UPDATE skrupel_schiffe SET spezialmission=23, extra='$extra' 
							WHERE id='$frachter_id'");
						continue;
					} elseif($schaden != 0) {
						@mysql_query("UPDATE skrupel_schiffe SET spezialmission=14 WHERE id='$frachter_id'");
						continue;
					}
					//Es wird bei allen anderen Frachtern geprueft, ob sie Kolonisten an Bord haben. Ist dies der
					//Fall, so wird dieser Frachter nicht weiter kolonisieren, sondern eine Route erhalten. Es 
					//wird auch nicht weiter kolonisiert, falls $this->mehrKolonien() dies fordert.
					$kolonie_schiffe = $this->ermittleAndereKolonieSchiffe($frachter_id);
					if(!($this->mehrKolonien()) || (count($kolonie_schiffe) != 0)) {
						if($this->setzeErnteRoute($frachter_id, $planeten_id)) {
							$this->verwalteRoute($frachter_id);
							continue;
						}
						if($this->setzeAlternativeRoute($frachter_id, $planeten_id)) {
							$this->verwalteRoute($frachter_id);
							continue;
						}
					}
					if($routing_status == 2) {
						$this->verwalteRoute($frachter_id);
						continue;
					}
					//Da vorher nichts gegen eine Kolonisierung sprach, wird nun weiter kolonisiert.
					$this->beladeKolonieSchiff($frachter_id);
					$schiff_leute = @mysql_query("SELECT fracht_leute FROM skrupel_schiffe 
						WHERE id='$frachter_id'");
					$schiff_leute = @mysql_fetch_array($schiff_leute);
					$schiff_leute = $schiff_leute['fracht_leute'];
					//Falls nicht genug Cantox oder Vorraete auf dem Planeten sind, wird das Schiff auch nicht 
					//mit Kolonisten beladen. Nur wenn wirklich Kolonisten an Bord sind, wird beendet.
					if($schiff_leute >= eigenschaften::$frachter_kolo_infos->kolo_leute) {
						$this->fliegeKolonieSchiff($frachter_id);
						$this->tankeLeminStart($frachter_id, $planeten_id);
						continue;
					}
				}
				//Falls dieser Planet der KI gehoert und Kolonisten an Bord des Schiffes sind, kann zum naechsten
				//Planeten geflogen werden.
				if($besitzer == eigenschaften::$comp_id 
				&& $fracht_leute >= eigenschaften::$frachter_kolo_infos->kolo_leute) {
					$this->fliegeKolonieSchiff($frachter_id);
					continue;
				}
				//Falls das Schiff kolonisieren soll und sich der Planet nicht lohnt, wird er in die Datenbank 
				//aufgenommen. Ausserdem wird dann zum naechsten unbewohnten Planeten geflogen.
				if($fracht_leute >= eigenschaften::$frachter_kolo_infos->kolo_leute 
				&& !(ki_basis::PlanetLohntSich($planeten_id))) {
					$this->fliegeKolonieSchiff($frachter_id);
					continue;
				}
				//Falls dieses Schiff keine Leute mehr hat, wird zum naechsten wichtigen Planeten (in der Regel 
				//Planeten mit Sternenbasis) geflogen.
				if($fracht_leute < eigenschaften::$frachter_kolo_infos->kolo_leute) {
					$this->zuWichtigenPlaneten($frachter_id);
					continue;
				} else {
					//Ansonsten wird der Planet kolonisiert.
					$this->kolonisierePlanet($frachter_id);
					$this->beladeFrachter($frachter_id, $planeten_id);
					continue;
				}
			} else {
				//Ist dieses Schiff also nicht auf einem Planeten und hat es nicht genug Kolonisten, wird es auch
				//zum naechsten wichtigen Planeten beordert.
				if($fracht_leute < eigenschaften::$frachter_kolo_infos->kolo_leute) {
					$this->zuWichtigenPlaneten($frachter_id);
					continue;
				}
			}
		}
	}
}
?>