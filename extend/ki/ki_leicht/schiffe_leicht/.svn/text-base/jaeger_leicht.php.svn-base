<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Implementierung des Verhaltes der KI bei Jaegern.
 */
class jaeger_leicht extends jaeger_basis {
	
	/**
	 * Aktiviert gegebenenfalls die Spezialmission Planetenbombardement oder deaktiviert sie wieder. Falls das 
	 * Schiff ueber virale Invasion verfuegt, dann wird stattdessen dieser aktiviert.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das bombardieren soll.
	 * 			  $bombadiere - true, falls Planetenbombardement aktiviert werden soll.
	 * 						    false, Sonst.
	 */
	function bombadierePlaneten($schiff_id, $bombadiere) {
		$aktive_spezialmission = @mysql_query("SELECT spezialmission FROM skrupel_schiffe WHERE id='$schiff_id'");
		$aktive_spezialmission = @mysql_fetch_array($aktive_spezialmission);
		$aktive_spezialmission = $aktive_spezialmission['spezialmission'];
		if($aktive_spezialmission != 3 && $aktive_spezialmission != 0 && $bombadiere) return;
		$bomben_mission = 0;
		if($bombadiere) $bomben_mission = 3;
		@mysql_query("UPDATE skrupel_schiffe SET spezialmission='$bomben_mission' WHERE id='$schiff_id'");
		if($this->SchiffHatFertigkeit($schiff_id, "viraleinvasion")) {
			$virale_mission = 0;
			if($bombadiere) $virale_mission = 17;
			@mysql_query("UPDATE skrupel_schiffe SET spezialmission='$virale_mission' WHERE id='$schiff_id'");
		}
	}
	
	/**
	 * Checkt, ob das uebergebene SRV-Schiff nahe genug bei einem Feindschiff und weit genug weg von einem 
	 * freundlichen Schiff ist. Falls ja, wird der SRV aktiviert.
	 * arguments: $schiff_id - Die Datenbank-ID des SRV-Schiffs, das vieleicht bald explodiert.
	 * returns: true, falls der SRV aktiviert wurde,
	 * 			false, sonst.
	 */
	function aktiviereSRV($schiff_id) {
		if(count(eigenschaften::$sichtbare_gegner_schiffe) == 0) return false;
		$koords = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$koords = @mysql_fetch_array($koords);
		$xpos = $koords['kox'];
		$ypos = $koords['koy'];
		$nahes_feind_schiff = ki_basis::ermittleNahesZiel($schiff_id, 
										eigenschaften::$sichtbare_gegner_schiffe, null);
		if($nahes_feind_schiff == null || $nahes_feind_schiff['id'] == null || $nahes_feind_schiff['id'] == 0) 
			return false;
		$zielx = $nahes_feind_schiff['x'];
		$ziely = $nahes_feind_schiff['y'];
		$feind_entfernung = ki_basis::berechneStrecke($xpos, $ypos, $zielx, $ziely);
		if($feind_entfernung > 83) return false;
		$schiff_koords = array();
		foreach(eigenschaften::$schiff_ids as $schiff) {
			$schiff_koord = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff'");
			$schiff_koord = @mysql_fetch_array($schiff_koord);
			$schiff_koords[] = array('x'=>$schiff_koord['kox'], 'y'=>$schiff_koord['koy'], 'id'=>$schiff);
		}
		$nahes_freund_schiff = ki_basis::ermittleNahesZiel($schiff_id, $schiff_koords, null);
		if($nahes_freund_schiff != null && $nahes_freund_schiff['id'] != null 
		&& $nahes_freund_schiff['id'] != 0) {
			$zielx = $nahes_freund_schiff['x'];
			$ziely = $nahes_freund_schiff['y'];
			$freund_entfernung = ki_basis::berechneStrecke($xpos, $ypos, $zielx, $ziely);
			if($freund_entfernung < 83) return false;
		}
		@mysql_query("UPDATE skrupel_schiffe SET spezialmission=9, flug=0 WHERE id='$schiff_id'");
		return true;
	}
	
	/**
	 * Prueft, ob es fuer den Jaeger sinnvoll ist, weiter Geleitschutz zu geben. Hier wird nur ueberprueft, ob 
	 * der Jaeger ueberhaupt Geleitschutz bietet.
	 * arguments: $jaeger_id - Die Datenbank-ID des Jaegers, dessen Geleitschutz ueberprueft werden soll.
	 * returns: true, falls der Jaeger weiter Geleitschutz geben soll.
	 * 			false, sonst.
	 */
	function gibtWeiterGeleitschutz($jaeger_id) {
		$schiff_flug = @mysql_query("SELECT flug FROM skrupel_schiffe WHERE id='$jaeger_id'");
		$schiff_flug = @mysql_fetch_array($schiff_flug);
		return ($schiff_flug['flug'] == 4);
	}
	
	/**
	 * Prueft, ob es Sinn macht, den Destabilisator des uebergebenen Jaegers zu aktivieren und aktiviert ihn 
	 * gegebenenfalls. Dies passiert nur, wenn das Schiff an einem Planeten ist und dieser Planet einem 
	 * gegnerischen Spieler gehoert. Ausserdem muessen genuegend Kolonisten auf dem Planeten sein.
	 * Es wird nicht ueberprueft, ob der Jaeger wirklich ueber einen Destabilisator verfuegt!
	 * arguments: $jaeger_id - Die Datebank-ID des Jaegers, dessen Destabilisator aktiviert werden soll.
	 * returns: true, falls der Destabilisator aktiviert wurde.
	 * 			false, sonst.
	 */
	function aktiviereDestabilisator($jaeger_id) {
		$schiff_daten = @mysql_query("SELECT kox, koy, status, fertigkeiten FROM skrupel_schiffe 
			WHERE id='$jaeger_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$schiff_status = $schiff_daten['status'];
		if($schiff_status != 2) return false;
		$fertigkeiten = $schiff_daten['fertigkeiten'];
		if(substr($fertigkeiten,50,2)+0 < eigenschaften::$jaeger_infos->min_destabilisator_prozent) return false;
		$x_pos = $schiff_daten['kox'];
		$y_pos = $schiff_daten['koy'];
		$spiel_id = eigenschaften::$spiel_id;
		$planeten_infos = @mysql_query("SELECT id, besitzer, kolonisten FROM skrupel_planeten 
			WHERE (x_pos='$x_pos') AND (y_pos='$y_pos') AND (spiel='$spiel_id')");
		$planeten_infos = @mysql_fetch_array($planeten_infos);
		$planeten_id = $planeten_infos['id'];
		$besitzer = $planeten_infos['besitzer'];
		$this->tankeLemin($jaeger_id, $planeten_infos['id']);
		$kolonisten = $planeten_infos['kolonisten'];
		if(in_array($besitzer, eigenschaften::$gegner_ids) 
		&& $kolonisten >= eigenschaften::$jaeger_infos->min_kolonisten_destabilisator) {
			@mysql_query("UPDATE skrupel_schiffe SET spezialmission=20 WHERE id='$jaeger_id'");
			return true;
		}
		return false;
	}
	
	/**
	 * Prueft, ob der Jaeger ein Minenfeld raeumen sollte. Falls ja, wird ein Kurs auf ein nahes Minenfeld 
	 * gesetzt oder die Spezialmission Minenfeld raeumen wird aktiviert, falls das Schiff schon am Minenfeld ist.
	 * arguments: $jaeger_id - Die Datenbank-ID des Jaegers, dass ein Minenfeld raeumen soll.
	 * returns: true, falls der Jaeger zu einem Minenfeld fliegt oder es raeumt.
	 * 			false, sonst.
	 */
	function MinenfeldRaeumen($jaeger_id) {
		if(!ki_basis::ModulAktiv("minenfelder")) return false;
		$schiff_daten = @mysql_query("SELECT kox, koy, hanger_anzahl, status FROM skrupel_schiffe 
			WHERE id='$jaeger_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$x_pos = $schiff_daten['kox'];
		$y_pos = $schiff_daten['koy'];
		$hangars = $schiff_daten['hanger_anzahl'];
		$schiff_status = $schiff_daten['status'];
		if($hangars != 0) {
			$minenfelder = ki_basis::ermittleFeindlicheMinenfelder();
			if(count($minenfelder) != 0) {
				$nahes_minenfeld = ki_basis::ermittleNahesZiel($jaeger_id, $minenfelder, null);
				$entfernung_minenfeld = ceil(ki_basis::berechneStrecke($x_pos, $y_pos, $nahes_minenfeld['x'], 
																  $nahes_minenfeld['y']));
				if($nahes_minenfeld != null && $entfernung_minenfeld 
				<= eigenschaften::$schiffe_infos->max_abstand_minenfeld_raeumen) {
					$nahes_feindschiff = ki_basis::ermittleNahesZiel($jaeger_id, 
															eigenschaften::$sichtbare_gegner_schiffe, null);
					$entfernung_schiff = ki_basis::berechneStrecke($x_pos, $y_pos, $nahes_feindschiff['x'], 
																   $nahes_feindschiff['y']);
					if($nahes_feindschiff == null || $entfernung_schiff 
					>= eigenschaften::$schiffe_infos->min_abstand_minenfeld_raeumen_feindschiff) {
						if($entfernung_minenfeld <= 95) {
							if($schiff_status == 2) {
								$warp = $this->ermittleMaximumWarp($jaeger_id);
								$this->fliegeSchiff($jaeger_id, $x_pos, $y_pos+10, $warp, null);
								return true;
							}
							@mysql_query("UPDATE skrupel_schiffe SET flug=0, spezialmission='25' 
								WHERE id='$jaeger_id'");
							return true;
						} elseif($this->fliegeZuMinenfeldRand($jaeger_id, $nahes_minenfeld['id'])) return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Ermittelt das naechste Ziel fuer den uebergebenen Jaeger und setzt gegebenenfalls Kurs auf das Ziel. 
	 * Ausserdem wird Planetenbombadement aktiviert (bei Flug auf gegnerischen Planeten) bzw. deaktiviert 
	 * (bei Flug auf gegnerisches Schiff).
	 * arguments: $jaeger_id - Die Datenbank-ID des Jaegers, das ein neues Ziel bekommen soll.
	 */
	function fliegeJaeger($jaeger_id) {
		if($this->MinenfeldRaeumen($jaeger_id)) return null;
		$warp = $this->ermittleMaximumWarp($jaeger_id);
		$schiff_pos = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$jaeger_id'");
		$schiff_pos = @mysql_fetch_array($schiff_pos);
		$x_start = $schiff_pos['kox'];
		$y_start = $schiff_pos['koy'];
		$gegner_ziel = null;
		//Zuerst werden alle Feindschiffe ueberprueft, die einen Planeten oder ein Schiff der KI angreifen.
		if(count(eigenschaften::$gegner_angriffe) > 0) {
			$gegner_ziel = ki_basis::ermittleNahesZiel($jaeger_id, eigenschaften::$gegner_angriffe, 
													   eigenschaften::$bekannte_wurmloch_daten);
		}
		if($gegner_ziel != null && $gegner_ziel['id'] != null && $gegner_ziel['id'] != 0) {
			$this->bombadierePlaneten($jaeger_id, false);
			$x_ziel = $gegner_ziel['x'];
			$y_ziel = $gegner_ziel['y'];
			$ziel_id = $gegner_ziel['id'];
			$entfernung = floor(ki_basis::berechneStrecke($x_start, $y_start, $x_ziel, $y_ziel));
			//Es wird nur angegriffen, falls die Reichweite zum angreifenden Schiff nicht zu gross ist.
			if($entfernung <= eigenschaften::$jaeger_infos->max_jaeger_reichweite_angriff && $entfernung != 0) {
				$this->fliegeSchiff($jaeger_id, $x_ziel, $y_ziel, $warp, $ziel_id);
				return;
			}
		}
		$gegner_ziel = null;
		//Jetzt werden alle bekannten schon gesehenen Planeten der Gegner ueberprueft.
		if(count(eigenschaften::$gegner_planeten_daten) > 0) {
			$gegner_ziel = ki_basis::ermittleNahesZiel($jaeger_id, eigenschaften::$gegner_planeten_daten, 
				 eigenschaften::$bekannte_wurmloch_daten);
		}
		if($gegner_ziel != null && $gegner_ziel['id'] != null && $gegner_ziel['id'] != 0) {
			$this->bombadierePlaneten($jaeger_id, true);
			$x_ziel = $gegner_ziel['x'];
			$y_ziel = $gegner_ziel['y'];
			$ziel_id = $gegner_ziel['id'];
			$entfernung = floor(ki_basis::berechneStrecke($x_start, $y_start, $x_ziel, $y_ziel));
			//Es wird nur angegriffen, falls die Reichweite zum Feindplaneten nicht zu gross ist.
			if($entfernung <= eigenschaften::$jaeger_infos->max_jaeger_reichweite_planeten && $entfernung != 0) {
				$this->fliegeSchiff($jaeger_id, $x_ziel, $y_ziel, $warp, $ziel_id);
				return;
			}
		}
		$gegner_ziel = null;
		//Nun werden alle anderen sichtbaren Feindschiffe ueberprueft.
		if(count(eigenschaften::$sichtbare_gegner_schiffe) > 0) {
			$gegner_ziel = ki_basis::ermittleNahesZiel($jaeger_id, eigenschaften::$sichtbare_gegner_schiffe, 
							eigenschaften::$bekannte_wurmloch_daten);
		}
		if($gegner_ziel != null && $gegner_ziel['id'] != null && $gegner_ziel['id'] != 0) {
			$this->bombadierePlaneten($jaeger_id, false);
			$x_ziel = $gegner_ziel['x'];
			$y_ziel = $gegner_ziel['y'];
			$ziel_id = $gegner_ziel['id'];
			$entfernung = floor(ki_basis::berechneStrecke($x_start, $y_start, $x_ziel, $y_ziel));
			//Es wird nur angegriffen, falls die Reichweite zum Feindschiff nicht zu gross ist.
			if($entfernung <= eigenschaften::$jaeger_infos->max_jaeger_reichweite_schiffe && $entfernung != 0) {
				$this->fliegeSchiff($jaeger_id, $x_ziel, $y_ziel, $warp, $ziel_id);
				return;
			}
		}
		$gegner_ziel = null;
		//Da keine passenden Ziele gefunden wurden, wird nun ein Erkundungsziel gesetzt.
		$gegner_ziel = $this->erkunde($jaeger_id);
		$this->fliegeSchiff($jaeger_id, $gegner_ziel['x'], $gegner_ziel['y'], $warp, $gegner_ziel['id']);
	}
	
	/**
	 * Analysiert die Situation aller Jaeger des KI-Spielers und reagiert gegebenenfalls.
	 */
	function verwalteJaeger() {
		$spiel_id = eigenschaften::$spiel_id;
		foreach(eigenschaften::$jaeger_ids as $jaeger_id) {
			if($this->istAktivesSpezialSchiff($jaeger_id)) continue;
			$this->setzeAggressivitaet($jaeger_id, 9);
			$this->setzeTaktik($jaeger_id, 1);
			if($this->SchiffHatFertigkeit($jaeger_id, "destabilisator") 
			&& $this->aktiviereDestabilisator($jaeger_id)) continue;
			if(count(geleitschutz_basis::ermittleKommendeBegleitSchiffe($jaeger_id)) != 0) {
				@mysql_query("UPDATE skrupel_schiffe SET flug=0, zielid=0, zielx=0, ziely=0 
					WHERE id='$jaeger_id'");
				continue;
			}
			$schiff_daten = @mysql_query("SELECT kox, koy, status, zielid, flug, lemin, spezialmission, schaden, 
				projektile, hanger_anzahl, crew, crewmax FROM skrupel_schiffe WHERE id='$jaeger_id'");
			$schiff_daten = @mysql_fetch_array($schiff_daten);
			//Der naechste unbewohnte Planet wird gescannt und ins Schiffs-Logbuch eingetragen, falls er sich 
			//fuer die Kolonisierung nicht lohnt.
			$this->scanneUmgebung($jaeger_id);
			//Hat der Jaeger einen Subraumverzerrer und macht es gerade Sinn, diesen zu aktivieren, so wird 
			//nichts weiter gemacht.
			if($this->SchiffHatFertigkeit($jaeger_id, "srv") && $this->aktiviereSRV($jaeger_id)) continue;
			//Falls der Jaeger einen Tarnfeldgenerator hat, wird dieser (falls es gerade Sinn macht) aktiviert.
			if($this->SchiffHatFertigkeit($jaeger_id, "tarnung")) $this->aktiviereTarnung($jaeger_id);
			$warp = $this->ermittleMaximumWarp($jaeger_id);
			$x_pos = $schiff_daten['kox'];
			$y_pos = $schiff_daten['koy'];
			$schiff_status = $schiff_daten['status'];
			$ziel_id = $schiff_daten['zielid'];
			$schiff_flug = $schiff_daten['flug'];
			if($schiff_flug == 4) $this->setzeAggressivitaet($jaeger_id, 8);
			$schiff_lemin = $schiff_daten['lemin'];
			$spezialmission = $schiff_daten['spezialmission'];
			$schaden = $schiff_daten['schaden'];
			$projektile = $schiff_daten['projektile'];
			$hangars = $schiff_daten['hanger_anzahl'];
			$crew = $schiff_daten['crew'];
			$crew_max = $schiff_daten['crewmax'];
			//Falls der Jaeger Schaden erlitten hat, aktiviert er die Schiffreparatur und fliegt zur 
			//naechsten Sternenbasis.
			if($schaden >= eigenschaften::$jaeger_infos->max_jaeger_schaden 
			&& !$this->SchiffHatFertigkeit($jaeger_id, "srv")) {
				//Falls feindliche Schiffe nicht zu nahe sind und das Schiff Projektile an Bord hat, wird ein 
				//Minenfeld gelegt, bevor das Schiff einen Reparatur-Flug macht.
				if(ki_basis::ModulAktiv("minenfelder") && $schiff_status != 2 
				&& $projektile > eigenschaften::$jaeger_infos->min_projektile_minen_legen) {
					$nahes_feindschiff = ki_basis::ermittleNahesZiel($jaeger_id, 
								eigenschaften::$sichtbare_gegner_schiffe, null);
					$gegner_entfernung = floor(ki_basis::berechneStrecke($x_pos, $y_pos, 
										$nahes_feindschiff['x'], $nahes_feindschiff['y']));
					if($nahes_feindschiff == null || $nahes_feindschiff['id'] == null 
					|| $nahes_feindschiff['id'] == 0 || ($gegner_entfernung != 0 && $gegner_entfernung 
					> eigenschaften::$jaeger_infos->min_feind_abstand_minen_legen && $gegner_entfernung 
					< eigenschaften::$jaeger_infos->max_feind_abstand_minen_legen)) {
						$extra = "0:0:".$projektile;
						@mysql_query("UPDATE skrupel_schiffe SET spezialmission=24, routing_schritt=0, 
							routing_koord='', extra='$extra', zielx=0, ziely=0, zielid=0, flug=0 
							WHERE id='$jaeger_id'");
						continue;
					}
				}
				if($schiff_status == 2) {
					$planeten_infos = @mysql_query("SELECT id FROM skrupel_planeten 
						WHERE (x_pos='$x_pos') AND (y_pos='$y_pos') AND (spiel='$spiel_id')");
					$planeten_infos = @mysql_fetch_array($planeten_infos);
					$this->tankeLemin($jaeger_id, $planeten_infos['id']);
				}
				if($this->SchiffHatFertigkeit($jaeger_id, "lloyd")) {
					@mysql_query("UPDATE skrupel_schiffe SET spezialmission=16 WHERE id='$jaeger_id'");
					continue;
				}
				if($schiff_status != 2) {
					$this->zuWichtigenPlaneten($jaeger_id);
					continue;
				}
			}
			if($this->reagiereAufWurmloch($jaeger_id)) continue;
			//Ist der Jaeger nah genug an einem feindlichen Minenfeld und verfuegt der Jaeger ueber Hangars, so 
			//wird versucht das Minenfeld zu raeumen.
			if($this->MinenfeldRaeumen($jaeger_id)) continue;
			//Hier wird geprueft, ob das Schiff durch ein Objekt fliegen muesste, um sein Ziel zu erreichen.
			//Falls ja, wird sogleich ein Ausweich-Kurs gesetzt.
			if($this->mussObjektUmfliegen($jaeger_id)) continue;
			//Wird die untere Schranke oder die untere Prozent-Marke fuer Lemin unterschritten, fliegt 
			//das Schiff zum naechsten Planeten, um aufzutanken.
			$strecken_lemin = $this->ermittleStreckenVerbrauch($jaeger_id);
			if($schiff_lemin < (eigenschaften::$jaeger_infos->min_jaeger_lemin_prozent * $strecken_lemin / 100) 
			&& $schiff_status != 2) {
				$this->fliegeTanken($jaeger_id);
				@mysql_query("UPDATE skrupel_schiffe SET routing_schritt=0, routing_koord='' 
					WHERE id='$jaeger_id'");
				continue;
			}
			if($schiff_status == 2) {
				//Baut alle moeglichen Projektile.
				$this->baueProjektile($jaeger_id);
				$planeten_infos = @mysql_query("SELECT id, besitzer, sternenbasis, kolonisten 
					FROM skrupel_planeten WHERE (x_pos='$x_pos') AND (y_pos='$y_pos') AND (spiel='$spiel_id')");
				$planeten_infos = @mysql_fetch_array($planeten_infos);
				$planeten_id = $planeten_infos['id'];
				$besitzer = $planeten_infos['besitzer'];
				$sternenbasis = $planeten_infos['sternenbasis'];
				$kolonisten = $planeten_infos['kolonisten'];
				//Hat das Schiff einen Tarnfeldgenerator, so wird eine gewisse Menge an Rennurbin geladen.
				if($this->SchiffHatFertigkeit($jaeger_id, "tarnung")) {
					$this->ladeResourceAufSchiff($jaeger_id, $planeten_id, "min2", 
								eigenschaften::$schiffe_infos->max_ren_tarnung_start);
				}
				//Falls ein beschaedigter Jaeger bei einer Sternenbasis ist, bleibt er fuer die Reparatur
				//im Orbit.
				if($sternenbasis == 2 && $besitzer == eigenschaften::$comp_id) {
					if($crew < $crew_max) {
						$neue_crew = $crew_max - $crew;
						$extra = "0:".$neue_crew.":0";
						@mysql_query("UPDATE skrupel_schiffe SET spezialmission=23, extra='$extra' 
							WHERE id='$jaeger_id'");
						continue;
					} elseif($schaden != 0) {
						@mysql_query("UPDATE skrupel_schiffe SET spezialmission=14 WHERE id='$jaeger_id'");
						continue;
					}
				}
				//Es wird geprueft, ob sich der Planet fuer die Kolonisierung nicht lohnt.
				ki_basis::PlanetLohntSich($planeten_id);
				//Falls der Jaeger nicht an einem gegnerischen Planeten ist, wird weiter geflogen.
				if(($schiff_flug == 0 || $schiff_flug == 4) && $besitzer != 0 
				&& $besitzer != eigenschaften::$comp_id && $kolonisten >= 1000) {
					$this->bombadierePlaneten($jaeger_id, true);
					continue;
				} else $this->fliegeJaeger($jaeger_id);
				//Hat das Schiff aus welch Gruenden auch immer kein Ziel, so wird eins bestimmt.
				$flug_daten = @mysql_query("SELECT zielx, ziely, kox, koy FROM skrupel_schiffe 
					WHERE id='$jaeger_id'");
				$flug_daten = @mysql_fetch_array($flug_daten);
				if(($flug_daten['zielx'] == 0 && $flug_daten['ziely'] == 0) 
				|| ($flug_daten['zielx'] == $flug_daten['kox'] && $flug_daten['ziely'] == $flug_daten['koy'])) 
					$this->fliegeJaeger($jaeger_id);
				if($schiff_lemin == 0 && $sternenbasis == 2) 
					$this->tankeLeminStart($jaeger_id, $planeten_id);
				else $this->tankeLemin($jaeger_id, $planeten_id);
				continue;
			}
			//Falls das Schiff Begleitschutz gibt, wird nur die Aggressivitaet etwas herabgesetzt, damit das 
			//zu begleitende Schiff zu erst angegriffen wird.
			if($this->gibtWeiterGeleitschutz($jaeger_id)) {
				$this->setzeAggressivitaet($jaeger_id, 8);
				continue;
			}
			//Es wird ueberprueft, ob das gegnerische Schiff, das angeflogen wird, noch sichtbar ist und 
			//ueberhaupt noch existiert. Falls nicht, wird ein neuer Kurs berechnet.
			if($schiff_flug == 3) {
				$sicht = "sicht_".eigenschaften::$comp_id;
				$ziel_daten = @mysql_query("SELECT $sicht FROM skrupel_schiffe WHERE (id='$ziel_id')");
				$ziel_daten = @mysql_fetch_array($ziel_daten);
				$ziel_sichtbar = $ziel_daten[$sicht];
				if($ziel_sichtbar == null || $ziel_sichtbar == 0) {
					$this->fliegeJaeger($jaeger_id);
					continue;
				}
			}
			//Ist ein sichtbares Schiff in Reichweite, wird es sofort angegriffen.
			if(count(eigenschaften::$sichtbare_gegner_schiffe) > 0) {
				$nahes_schiff = ki_basis::ermittleNahesZiel($jaeger_id, eigenschaften::$sichtbare_gegner_schiffe, 
								  eigenschaften::$bekannte_wurmloch_daten);
				$entfernung = floor(ki_basis::berechneStrecke($x_pos, $y_pos, $nahes_schiff['x'], 
															  $nahes_schiff['y']));
				if($entfernung <= eigenschaften::$jaeger_infos->max_jaeger_reichweite_schiffe 
				&& $entfernung != 0 && $nahes_schiff['id'] != 0) {
					$this->bombadierePlaneten($jaeger_id, false);
					$this->fliegeSchiff($jaeger_id, $nahes_schiff['x'], $nahes_schiff['y'], $warp, 
										$nahes_schiff['id']);
					continue;
				}
			}
			//Hat das Schiff aus welch Gruenden auch immer kein Ziel, so wird eins bestimmt.
			$flug_daten = @mysql_query("SELECT zielx, ziely, kox, koy FROM skrupel_schiffe 
				WHERE id='$jaeger_id'");
			$flug_daten = @mysql_fetch_array($flug_daten);
			if(($flug_daten['zielx'] == 0 && $flug_daten['ziely'] == 0) 
			|| ($flug_daten['zielx'] == $flug_daten['kox'] && $flug_daten['ziely'] == $flug_daten['koy'])) 
				$this->fliegeJaeger($jaeger_id);
		}
	}
}
?>