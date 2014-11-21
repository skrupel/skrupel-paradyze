<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com) 
 * 
 * Eine abstrakte Ober-Klasse fuer alle Klassen, die etwas mit Sternenbasen zu tun haben.
 */
abstract class sternenbasen_basis {
	
	/**
	 * Abstrakte Funktion, welche ermittelt, ob und wo eine neue Sternenbasis errichtet werden soll.
	 */
	abstract function ermittleNeuenBasenPlaneten();
	
	/**
	 * Verwaltet die Sternenbasen der KI.
	 */
	abstract function verwalteBasen();
	
	/**
	 * Verwaltet alle Jaeger und Frachter, die mit zu wenig Lemin im All sind oder auf einem Planeten 
	 * mit wenig Lemin sind.
	 * arguments: $basen_planeten_id - Die Datenbank-ID des Planeten, von dem aus Raumfalten mit Lemin 
	 * 								   zu gestrandeten Schiffen gesendet werden sollen.
	 */
	abstract function verwalteSchiffeOhneLemin($basen_planeten_id);
	
	/**
	 * Ermittelt ob zum uebergebenen Schiff eine Raumfalte mit Lemin unterwegs ist.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, zu dem ueberprueft werden soll, ob eine Raumfalte 
	 * 						   mit Lemin unterwegs ist.
	 * returns: true, falls zum Schiff eine Lemin-Raumfalte unterwegs ist.
	 * 			false, sonst.
	 */
	function SchiffHatLeminRaumfalte($schiff_id) {
		$spiel_id = eigenschaften::$spiel_id;
		$raumfalten = @mysql_query("SELECT extra FROM skrupel_anomalien WHERE (spiel='$spiel_id') AND (art=3)");
		while($raumfalten_extra = @mysql_fetch_array($raumfalten)) {
			$raumfalten_extra = explode(':', $raumfalten_extra['extra']);
			if($raumfalten_extra[0] != 's') continue;
			$ziel_id = $raumfalten_extra[1];
			$lemin = $raumfalten_extra[6];
			if($ziel_id == $schiff_id && $lemin != 0) return true;
		}
		return false;
	}
	
	/**
	 * Ermittelt alle Raumfalten, die zum uebergebenen Planeten gehn.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, dessen eingehenden Raumfalten ermittelt werden.
	 * returns: Ein Array aus Datenbank-IDs der Raumfalten, die zum Planet fliegen.
	 */
	function ermittlePlanetenRaumfalten($planeten_id) {
		$spiel_id = eigenschaften::$spiel_id;
		$raumfalten = @mysql_query("SELECT extra, id FROM skrupel_anomalien WHERE (spiel='$spiel_id') 
			AND (art=3)");
		$raumfalten_array = array();
		while($raumfalten_infos = @mysql_fetch_array($raumfalten)) {
			$raumfalten_extra = explode(':', $raumfalten_infos['extra']);
			if($raumfalten_extra[0] != 'p') continue;
			$ziel_id = $raumfalten_extra[1];
			if($ziel_id == $planeten_id) $raumfalten_array[] = $raumfalten_infos['id'];
		}
		return $raumfalten_array;
	}
	
	/**
	 * Berechnet die fuer die uebergebenen Resourcen anfallende Menge an Cantox, die fuer das Senden einer 
	 * Raumfalte noetig ist.
	 * arguments: $resourcen - Ein Array aus Resourcen mit folgendem Aufbau: (Cantox, Vorraete, Lemin, 
	 * 						   min1, min2, min3).
	 */
	function berechneRaumfaltenKosten($resourcen) {
		return (8 * ($resourcen[1] + $resourcen[2] + $resourcen[3] + $resourcen[4] + $resourcen[5])) + 75;
	}
	
	/**
	 * Sendet eine Raumfalte von der uebergebenen Sternenbasis zu den uebergebenen Ziel-Koordinaten mit den 
	 * uebergebenen Resourcen. Reicht das Cantox auf dem Planeten nicht aus, um die Raumfalte zu finanzieren, 
	 * gibt es die Ziel-ID nicht oder sind zuviele Resourcen uebergeben worden, wie es Resourcen auf dem 
	 * Planeten gibt, so wird keine Raumfalte initialisiert.
	 * arguments: $ziel_id - Die Datenbank-ID des Planeten oder Schiffes, dessen Ziel die Raumfalte hat.
	 * 			  $basis_planet_id - Die Datenbank-ID des Planeten mit Sternenbasis, von dem aus die Raumfalte
	 * 								 initialisiert werden soll.
	 * 			  $ziel_ist_planet - true, falls das Ziel ein Planet ist.
	 * 								 false, sonst.
	 * 			  $resourcen - Ein Array aus Resourcen mit folgendem Aufbau: Cantox, Vorraete, Lemin,
	 * 						   min1, min2, min3.
	 * returns: true, falls die Raumfalte geschickt werden konnte.
	 * 			false, sonst.
	 */
	function sendeRaumfalte($ziel_id, $basis_planet_id, $ziel_ist_planet, $resourcen) {
		$alles_null = true;
		foreach($resourcen as $res) {
			if($res != 0) {
				$alles_null = false;
				break;
			}
		}
		if($alles_null) return false;
		$planeten_infos = @mysql_query("SELECT x_pos, y_pos, cantox, vorrat, lemin, min1, min2, min3 
			FROM skrupel_planeten WHERE id='$basis_planet_id'");
		$planeten_infos = @mysql_fetch_array($planeten_infos);
		$startx = $planeten_infos['x_pos'];
		$starty = $planeten_infos['y_pos'];
		$planet_cantox = $planeten_infos['cantox'];
		$cantox_kosten = $this->berechneRaumfaltenKosten($resourcen);
		if($planet_cantox < ($cantox_kosten + $resourcen['cantox']) 
		|| $resourcen['vorrat'] > $planeten_infos['vorrat'] 
		|| $resourcen['lemin'] > $planeten_infos['lemin'] || $resourcen['min1'] > $planeten_infos['min1'] 
		|| $resourcen['min2'] > $planeten_infos['min2'] || $resourcen['min3'] > $planeten_infos['min3']) 
			return false;
		$zielx = 0;
		$ziely = 0;
		$ziel_koords = null;
		$extra_string = "";
		if($ziel_ist_planet) {
			$extra_string = "p:";
			$ziel_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten WHERE id='$ziel_id'");
			$ziel_koords = @mysql_fetch_array($ziel_koords);
			if($ziel_koords == null) return false;
			$zielx = $ziel_koords['x_pos'];
			$ziely = $ziel_koords['y_pos'];
		} else {
			$extra_string = "s:";
			$ziel_koords = @mysql_query("SELECT kox, kox FROM skrupel_schiffe WHERE id='$ziel_id'");
			$ziel_koords = @mysql_fetch_array($ziel_koords);
			if($ziel_koords == null) return false;
			$zielx = $ziel_koords['kox'];
			$ziely = $ziel_koords['koy'];
		}
		$res_string = implode(':', $resourcen);
		$extra_string = $extra_string.$ziel_id.":".$zielx.":".$ziely.":".$res_string;
		$id = ki_basis::ermittleNaechsteID("skrupel_anomalien");
		$spiel_id = eigenschaften::$spiel_id;
		@mysql_query("INSERT INTO skrupel_anomalien (id, art, x_pos, y_pos, extra, spiel) 
			VALUES('$id', 3, '$startx', '$starty', '$extra_string', '$spiel_id')");
		ki_basis::zieheResourcenAb("cantox", $resourcen['cantox'], $basis_planet_id);
		ki_basis::zieheResourcenAb("vorrat", $resourcen['vorrat'], $basis_planet_id);
		ki_basis::zieheResourcenAb("lemin", $resourcen['lemin'], $basis_planet_id);
		ki_basis::zieheResourcenAb("min1", $resourcen['min1'], $basis_planet_id);
		ki_basis::zieheResourcenAb("min2", $resourcen['min2'], $basis_planet_id);
		ki_basis::zieheResourcenAb("min3", $resourcen['min3'], $basis_planet_id);
		return true;
	}
	
	/**
	 * Ermittelt den Technologie-Stufe der uebergebenen Technologie der Sternenbasis des uebergebenen Planeten.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten mit Sternenbasis, dessen Technologie abgefragt
	 * wird.
	 * arguments: $tech - Die Technologie (t_huelle, t_antrieb, t_energie oder t_explosiv), dessen Stufe 
	 * 					  gefragt ist.
	 * returns: Die Stufe der gefragten Technologie der betreffenden Sternenbasis.
	 */
	static function ermittleTechLevel($planeten_id, $tech) {
		$tech_level = @mysql_query("SELECT $tech FROM skrupel_sternenbasen WHERE planetid='$planeten_id'");
		$tech_level = @mysql_fetch_array($tech_level);
		return $tech_level[$tech];
	}
	
	/**
	 * Ermittelt alle Datenbank-IDs der Planeten mit Sternenbasis und schreibt diese in 
	 * eigenschaften::$basen_planeten_ids und eigenschaften::$wichtige_planeten_ids.
	 */
	function ermittleBasisIDs() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$planeten_ids = @mysql_query("SELECT id FROM skrupel_planeten 
			WHERE (besitzer='$comp_id') AND (sternenbasis=2) AND (spiel='$spiel_id')");
		eigenschaften::$basen_planeten_ids = array();
		while($planeten_id = @mysql_fetch_array($planeten_ids)) {
			eigenschaften::$wichtige_planeten_ids[] = $planeten_id['id'];
			eigenschaften::$basen_planeten_ids[] = $planeten_id['id'];
		}
	}
	
	/**
	 * Baut die Orbitale Verteidigung der Sternenbasis des uebergebenen Planeten aus. Die dafuer benoetigten
	 * Cantox und Vorraete werden dabei vom Planeten abgezogen.
	 * arguments: planeten_id - Die Datenbank-ID des Planeten, an dessen Sternenbasis die P.D.S ausgebaut wird.
	 */
	function baueBasisVerteidigung($planeten_id) {
		$planeten_daten = @mysql_query("SELECT cantox, vorrat FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_daten = @mysql_fetch_array($planeten_daten);
		$planet_cantox = $planeten_daten['cantox'];
		$planet_vorrat = $planeten_daten['vorrat'];
		$basis_defense = @mysql_query("SELECT defense FROM skrupel_sternenbasen WHERE planetid='$planeten_id'");
		$basis_defense = @mysql_fetch_array($basis_defense);
		$basis_defense = $basis_defense['defense']; 
		while($planet_cantox >= 10 && $planet_vorrat >= 1 && $basis_defense < 50) {
			$planet_cantox -= 10;
			$planet_vorrat--;
			$basis_defense++;
		}
		@mysql_query("UPDATE skrupel_planeten SET cantox='$planet_cantox', vorrat='$planet_vorrat' 
			WHERE id='$planeten_id'");
		@mysql_query("UPDATE skrupel_sternenbasen SET defense='$basis_defense' WHERE planetid='$planeten_id'");
	}
	
	/**
	 * Updatet den uebergebenen Tech-Level der Sternenbasis des uebergebenen Planeten.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, dessen Sternenbasis ausgebaut werden soll.
	 * 			  $geld - Die Menge an Cantox, die auf dem Planeten ist.
	 * 			  $kosten - Die Kosten fuer den Ausbau fuer die Stufe.
	 * 			  $tech - Die Art der Technologie der ausgebaut werden soll (t_huelle, t_antrieb, t_energie 
	 * 					  oder t_explosiv).
	 * 			  $level - Der neue Level fuer die Technologie.
	 */
	function updateTechLevel($planeten_id, $kosten, $tech, $level) {
		ki_basis::zieheResourcenAb("cantox", $kosten, $planeten_id);
		@mysql_query("UPDATE skrupel_sternenbasen SET $tech='$level' WHERE planetid='$planeten_id'");
	}
	
	/**
	 * Erweitert die uebergebene Waffen-Technologie der Sternenbasis des uebergebenen Planeten. Die Waffen
	 * werden um eine Stufe erhoeht.
	 * arguments: $tech - Die Waffen-Technologie, die erweitert werden soll.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, an dem die betreffende Sternenbasis ist.d
	 * returns: true - Falls das entsprechende Waffensystem noch erweitert werden kann.
	 * 		    false - Sonst.
	 */
	function erweitereWaffen($tech, $planeten_id) {
		$waffen_basis = @mysql_query("SELECT $tech FROM skrupel_sternenbasen WHERE planetid='$planeten_id'");
		$waffen_basis = @mysql_fetch_array($waffen_basis);
		$waffen_basis = $waffen_basis[$tech];
		$geld = @mysql_query("SELECT cantox FROM skrupel_planeten WHERE id='$planeten_id'");
		$geld = @mysql_fetch_array($geld);
		$geld = $geld['cantox'];
		$waffe_erweiterbar = true;
		if($waffen_basis < eigenschaften::$basen_ausbau_infos->waffen_tech_limit) {
			switch($waffen_basis) {
					case 0: if($geld >= 100) 
								$this->updateTechLevel($planeten_id, 100, $tech, 1);
							else $waffe_erweiterbar = false;
							break;
					case 1: if($geld >= 400) 
								$this->updateTechLevel($planeten_id, 400, $tech, 2);
							else $waffe_erweiterbar = false;
							break;
					case 2: if($geld >= 900) 
								$this->updateTechLevel($planeten_id, 900, $tech, 3);
							else $waffe_erweiterbar = false;
							break;
					case 3: if($geld >= 1600) 
								$this->updateTechLevel($planeten_id, 1600, $tech, 4);
							else $waffe_erweiterbar = false;
							break;
					case 4: if($geld >= 2500) 
								$this->updateTechLevel($planeten_id, 2500, $tech, 5);
							else $waffe_erweiterbar = false;
							break;
					case 5: if($geld >= 3600) 
								$this->updateTechLevel($planeten_id, 3600, $tech, 6);
							else $waffe_erweiterbar = false;
							break;
					case 6: if($geld >= 4900) 
								$this->updateTechLevel($planeten_id, 4900, $tech, 7);
							else $waffe_erweiterbar = false;
							break;
					case 7: if($geld >= 6400) 
								$this->updateTechLevel($planeten_id, 6400, $tech, 8);
							else $waffe_erweiterbar = false;
							break;
					case 8: if($geld >= 8100) 
								$this->updateTechLevel($planeten_id, 8100, $tech, 9);
							else $waffe_erweiterbar = false;
							break;
					case 9: if($geld >= 10000) 
								$this->updateTechLevel($planeten_id, 10000, $tech, 10);
							else $waffe_erweiterbar = false;
							break;
					default: $waffe_erweiterbar = false;
				}
		} else $waffe_erweiterbar = false;
		return $waffe_erweiterbar;
	}
	
	/**
	 * Erweitert alle Technologien der Sternenbasis des uebergebenen Planeten. Die Prioritaet beim erweitern
	 * ist: Antriebe, Rumpf, Energetik, Projektil, dh. es wird solange die Antriebstechnik erweitert, bis kein
	 * Cantox mehr fuer die naechste Stufe auf dem Planeten verfuegbar ist, dann der Rumpf usw.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, dessen Sternenbasis erweitert werden soll.
	 */
	function erweitereBasen($planeten_id) {
		$geld = @mysql_query("SELECT cantox FROM skrupel_planeten WHERE id='$planeten_id'");
		$geld = @mysql_fetch_array($geld);
		$geld = $geld['cantox'];
		$basis_daten = @mysql_query("SELECT t_huelle, t_antrieb, t_energie, t_explosiv 
			FROM skrupel_sternenbasen WHERE planetid='$planeten_id'");
		$basis_daten = @mysql_fetch_array($basis_daten);
		$rumpf_basis = $basis_daten['t_huelle'];
		$antrieb_basis = $basis_daten['t_antrieb'];
		$energetik_basis = $basis_daten['t_energie'];
		$projektil_basis = $basis_daten['t_explosiv'];
		//Energetik-Waffen werden zu beginn auf Stufe 1 gebracht, da manche Frachter Waffen brauchen.
		if($energetik_basis < 1) $this->updateTechLevel($planeten_id, 100, "t_energie", 1);
		$geld = @mysql_query("SELECT cantox FROM skrupel_planeten WHERE id='$planeten_id'");
		$geld = @mysql_fetch_array($geld);
		$geld = $geld['cantox'];
		//Projektil-Waffen werden zu beginn auf Stufe 1 gebracht, da manche Frachter Waffen brauchen.
		if($projektil_basis < 1) $this->updateTechLevel($planeten_id, 100, "t_explosiv", 1);
		$erweiterbar = true;
		$rumpf_erweiterbar = true;
		$antrieb_erweiterbar = true;
		$projektil_erweiterbar = true;
		$energie_erweiterbar = true;
		//In den ersten Runden macht es ansonsten nicht unbedingt Sinn, seine Waffen auszubauen:
		if(eigenschaften::$tick <= eigenschaften::$basen_ausbau_infos->kein_waffenausbau_limit) {
			$projektil_erweiterbar = false;
			$energie_erweiterbar = false;
		}
		//Die Technologien werden solange erweitert, bis kein Cantox mehr da ist oder sie voll ausgebaut sind.
		while($erweiterbar) {
			$geld = @mysql_query("SELECT cantox FROM skrupel_planeten WHERE id='$planeten_id'");
			$geld = @mysql_fetch_array($geld);
			$geld = $geld[cantox];
			$basis_daten = @mysql_query("SELECT t_huelle, t_antrieb FROM skrupel_sternenbasen 
				WHERE planetid='$planeten_id'");
			$basis_daten = @mysql_fetch_array($basis_daten);
			$rumpf_basis = $basis_daten['t_huelle'];
			$antrieb_basis = $basis_daten['t_antrieb'];
			//Die Rumpf-Technik wird in den ersten Runden ($this->rumpf_anfangs_limit_ticks) nur bis zu 
			//einem gewissen Limit ($this->rumpf_anfangs_limit) ausgebaut, damit alle Rassen in der ersten 
			//Runde einen Frachter bauen.
			if($rumpf_basis >= eigenschaften::$basen_ausbau_infos->rumpf_anfangs_limit 
			&& eigenschaften::$tick <= eigenschaften::$basen_ausbau_infos->rumpf_anfangs_limit_ticks) 
				$rumpf_erweiterbar = false;
			if(!$antrieb_erweiterbar && $rumpf_erweiterbar) {
				switch($rumpf_basis) {
					case 0: if($geld >= 100) 
								$this->updateTechLevel($planeten_id, 100, "t_huelle", 1);
							else $rumpf_erweiterbar = false;
							break;
					case 1: if($geld >= 200) 
								$this->updateTechLevel($planeten_id, 200, "t_huelle", 2);
							else $rumpf_erweiterbar = false;
							break;
					case 2: if($geld >= 300) 
								$this->updateTechLevel($planeten_id, 300, "t_huelle", 3);
							else $rumpf_erweiterbar = false;
							break;
					case 3: if($geld >= 800) 
								$this->updateTechLevel($planeten_id, 800, "t_huelle", 4);
							else $rumpf_erweiterbar = false;
							break;
					case 4: if($geld >= 1000) 
								$this->updateTechLevel($planeten_id, 1000, "t_huelle", 5);
							else $rumpf_erweiterbar = false;
							break;
					case 5: if($geld >= 1200) 
								$this->updateTechLevel($planeten_id, 1200, "t_huelle", 6);
							else $rumpf_erweiterbar = false;
							break;
					case 6: if($geld >= 2500) 
								$this->updateTechLevel($planeten_id, 2500, "t_huelle", 7);
							else $rumpf_erweiterbar = false;
							break;
					case 7: if($geld >=5000) 
								$this->updateTechLevel($planeten_id, 5000, "t_huelle", 8);
							else $rumpf_erweiterbar = false;
							break;
					case 8: if($geld >= 7500) 
								$this->updateTechLevel($planeten_id, 7500, "t_huelle", 9);
							else $rumpf_erweiterbar = false;
							break;
					case 9: if($geld >= 10000) 
								$this->updateTechLevel($planeten_id, 10000, "t_huelle", 10);
							else $rumpf_erweiterbar = false;
							break;
					default: $rumpf_erweiterbar = false;
				}
			}
			if($antrieb_basis < eigenschaften::$basen_ausbau_infos->antrieb_tech_limit && $antrieb_erweiterbar) {
				switch($antrieb_basis) {
					case 0: if($geld >= 100) 
								$this->updateTechLevel($planeten_id, 100, "t_antrieb", 1);
							else $antrieb_erweiterbar = false;
							break;
					case 1: if($geld >= 200) 
								$this->updateTechLevel($planeten_id, 200, "t_antrieb", 2);
							else $antrieb_erweiterbar = false;
							break;
					case 2: if($geld >= 300) 
								$this->updateTechLevel($planeten_id, 300, "t_antrieb", 3);
							else $antrieb_erweiterbar = false;
							break;
					case 3: if($geld >= 400) 
								$this->updateTechLevel($planeten_id, 400, "t_antrieb", 4);
							else $antrieb_erweiterbar = false;
							break;
					case 4: if($geld >= 500) 
								$this->updateTechLevel($planeten_id, 500, "t_antrieb", 5);
							else $antrieb_erweiterbar = false;
							break;
					case 5: if($geld >= 600) 
								$this->updateTechLevel($planeten_id, 600, "t_antrieb", 6);
							else $antrieb_erweiterbar = false;
							break;
					case 6: if($geld >= 700) 
								$this->updateTechLevel($planeten_id, 700, "t_antrieb", 7);
							else $antrieb_erweiterbar = false;
							break;
					case 7: if($geld >=4000) 
								$this->updateTechLevel($planeten_id, 4000, "t_antrieb", 8);
							else $antrieb_erweiterbar = false;
							break;
					case 8: if($geld >= 7000) 
								$this->updateTechLevel($planeten_id, 7000, "t_antrieb", 9);
							else $antrieb_erweiterbar = false;
							break;
					case 9: if($geld >= 10000) 
								$this->updateTechLevel($planeten_id, 10000, "t_antrieb", 10);
							else $antrieb_erweiterbar = false;
							break;
					default: $antrieb_erweiterbar = false;
				}
			}
			else $antrieb_erweiterbar = false;
			if(!$antrieb_erweiterbar && !$rumpf_erweiterbar && $energie_erweiterbar) {
				$energie_erweiterbar = $this->erweitereWaffen("t_energie", $planeten_id);
			}
			if(!$antrieb_erweiterbar && !$rumpf_erweiterbar && $projektil_erweiterbar) {
				$projektil_erweiterbar = $this->erweitereWaffen("t_explosiv", $planeten_id);
			}
			if(!$rumpf_erweiterbar && !$antrieb_erweiterbar 
			&& !$energie_erweiterbar && !$projektil_erweiterbar) $erweiterbar = false;
		}
	}
	
	/**
	 * Baut eine neue Sternenbasis fuer den KI-Spieler. Zuerst wird ein geeigneter Planet fuer eine neue Basis
	 * gesucht. Falls keiner gefunden wird, wird keine Basis gebaut. Ansonsten werden die Resourcen auf dem Planeten
	 * ueberprueft. Falls sie fuer eine neue Sternenbasis nicht ausreichen, wird keine gebaut. Die Datenbank-ID des
	 * Planeten wird aber zurueckgegeben, sodass Frachter diesen Planeten ansteuern und mit Resourcen beliefern.
	 * Ansonsten wird die Sternenbasis gebaut und die Resourcen vom Planeten abgezogen.
	 */
	function baueNeueSternenbasis($planeten_id) {
		if($planeten_id == null) return;
		frachter_basis::entfernePlanetenAusRouten($planeten_id);
		$planeten_res = @mysql_query("SELECT cantox, vorrat, min1, min2, min3, lemin, x_pos, y_pos 
			FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_res = @mysql_fetch_array($planeten_res);
		$x_pos = $planeten_res['x_pos'];
		$y_pos = $planeten_res['y_pos'];
		if($planeten_res['cantox'] >= 855 && $planeten_res['vorrat'] >= 35 
		&& $planeten_res['min1'] >= 123 && $planeten_res['min2'] >= 418 
		&& $planeten_res['min3'] >= 370 && $planeten_res['lemin'] >= 70) {
			$basis_zahl = count(eigenschaften::$basen_planeten_ids) + 1;
			$basis_name = eigenschaften::$basen_neu_infos->basis_namen_synax.$basis_zahl;
			$basis_id = ki_basis::ermittleNaechsteID("skrupel_sternenbasen");
			$spiel_id = eigenschaften::$spiel_id;
			$comp_id = eigenschaften::$comp_id;
			$rasse = eigenschaften::$rasse;
			@mysql_query("UPDATE skrupel_planeten SET sternenbasis=2, sternenbasis_name='$basis_name', 
				sternenbasis_rasse='$rasse', sternenbasis_id='$basis_id' WHERE id='$planeten_id'");
			@mysql_query("INSERT INTO skrupel_sternenbasen (id, name, x_pos, y_pos, planetid, besitzer, 
				rasse, status, spiel) VALUES ('$basis_id', '$basis_name', '$x_pos', '$y_pos', 
				'$planeten_id', '$comp_id', '$rasse', 1, '$spiel_id')");
			ki_basis::zieheResourcenAb("cantox", 855, $planeten_id);
			ki_basis::zieheResourcenAb("vorrat", 35, $planeten_id);
			ki_basis::zieheResourcenAb("min1", 123, $planeten_id);
			ki_basis::zieheResourcenAb("min2", 418, $planeten_id);
			ki_basis::zieheResourcenAb("min3", 370, $planeten_id);
			ki_basis::zieheResourcenAb("lemin", 70, $planeten_id);
			if(in_array($planeten_id, eigenschaften::$neue_basis_planeten)) {
				ki_basis::updateNeueBasenPlaneten($planeten_id, 0);
				ki_basis::ermittleNeueBasenPlaneten();
			}
		} elseif(!(in_array($planeten_id, eigenschaften::$neue_basis_planeten))) {
			eigenschaften::$neue_basis_planeten[] = $planeten_id;
			eigenschaften::$wichtige_planeten_ids[] = $planeten_id;
			frachter_basis::entfernePlanetenAusRouten($planeten_id);
			ki_basis::updateNeueBasenPlaneten($planeten_id, 1);
		}
	}
}
?>