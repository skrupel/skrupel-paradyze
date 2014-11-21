<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Implementierung von sternenbasen_basis.
 */
class sternenbasen_leicht extends sternenbasen_basis {
	
	/**
	 * Ermittelt einen geeigneten Planeten, um eine neue Sternenbasis zu bauen. In dieser Implementierung wird 
	 * dieser Planet anhand der groessten Bevoelkerungszahl und des Abstandes zu anderen Sternenbasen ermittelt.
	 * Die Bevoelkerungszahl ist deshalb mit entscheidend, weil eine Sternenbasis viel Cantox fuer den Ausbau und 
	 * spaeter viele Vorraete (fuer das Subpartikelclusterschiff) fuer den Schiffsbau benoetigt.
	 * Bessere Implementierungen koennten zB. die Anzahl umliegender Planeten oder die Nahe 
	 * zum Feind beruecksichtigen.
	 * returns: Die Datenbank-ID des Planeten, auf dem eine neue Sternenbasis sinnvoll erscheint.
	 * 			null, falls kein passender Planet gefunden wurde.
	 */
	function ermittleNeuenBasenPlaneten() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$planeten_infos = @mysql_query("SELECT id, x_pos, y_pos FROM skrupel_planeten 
			WHERE (sternenbasis=0) AND (besitzer='$comp_id') AND (spiel='$spiel_id') ORDER BY kolonisten DESC");
		$planeten_id = null;
		while($planeten_info = @mysql_fetch_array($planeten_infos)) {
			if(ki_basis::planetWirdAngegriffen($planeten_info['id'])) continue;
			
			$x_neue_basis = $planeten_info['x_pos'];
			$y_neue_basis = $planeten_info['y_pos'];
			if(count(eigenschaften::$basen_planeten_ids) == 0) {
				$planeten_id = $planeten_info['id'];
				break;
			}
			$basen_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_planeten 
				WHERE (sternenbasis=2) AND (besitzer='$comp_id') AND (spiel='$spiel_id')");
			while($basen_pos = @mysql_fetch_array($basen_koords)) {
				$x_basis = $basen_pos['x_pos'];
				$y_basis = $basen_pos['y_pos'];
				$strecke = floor(ki_basis::berechneStrecke($x_neue_basis, $y_neue_basis, $x_basis, $y_basis));
				if($strecke >= eigenschaften::$basen_neu_infos->min_basen_abstand) {
					$planeten_id = $planeten_info['id'];
					break;
				}
			}
			if($planeten_id != null) break;
		}
		return $planeten_id;
	}
	
	/**
	 * Verwaltet alle Jaeger und Frachter, die mit zu wenig Lemin im All sind oder auf einem Planeten 
	 * mit wenig Lemin sind.
	 * arguments: $basen_planeten_id - Die Datenbank-ID des Planeten, von dem aus Raumfalten mit Lemin 
	 * 								   zu gestrandeten Schiffen gesendet werden sollen.
	 */
	function verwalteSchiffeOhneLemin($basen_planeten_id) {
		$planeten_res = @mysql_query("SELECT cantox, lemin FROM skrupel_planeten WHERE id='$basen_planeten_id'");
		$planeten_res = @mysql_fetch_array($planeten_res);
		$planet_cantox = $planeten_res['cantox'];
		$planet_lemin = $planeten_res['lemin'];
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$lemin_grenze = eigenschaften::$raumfalten_infos->raumfalte_min_lemin;
		$ziel_schiffe = @mysql_query("SELECT id, status, kox, koy FROM skrupel_schiffe WHERE (spiel='$spiel_id') 
			AND (besitzer='$comp_id') AND (kox_old=0) AND (koy_old=0) AND (lemin < '$lemin_grenze')");
		while($schiff_daten = @mysql_fetch_array($ziel_schiffe)) {
			$schiff_id = $schiff_daten['id'];
			if($this->SchiffHatLeminRaumfalte($schiff_id)) continue;
			$status = $schiff_daten['status'];
			$x = $schiff_daten['kox'];
			$y = $schiff_daten['koy'];
			//Ist das Schiff bei einem Planeten mit genug Lemin, so wird keine Raumfalte gesendet.
			if($status == 2) {
				$schiff_planet_lemin = @mysql_query("SELECT lemin FROM skrupel_planeten 
					WHERE (x_pos='$x') AND (y_pos='$y') AND (spiel='$spiel_id')");
				$schiff_planet_lemin = @mysql_fetch_array($schiff_planet_lemin);
				if($schiff_planet_lemin['lemin']>eigenschaften::$raumfalten_infos->raumfalte_max_planeten_lemin) 
					continue;
			}
			$lemin_raumfalte = 0;
			if(in_array($schiff_id, eigenschaften::$frachter_ids)) 
				$lemin_raumfalte = eigenschaften::$raumfalten_infos->raumfalte_frachter_lemin;
			else $lemin_raumfalte = eigenschaften::$raumfalten_infos->raumfalte_jaeger_lemin;
			if($planet_cantox < (8 * $lemin_raumfalte) || $lemin_raumfalte > $planet_lemin) continue;
			$res_array = array(0, 0, $lemin_raumfalte, 0, 0, 0);
			$this->sendeRaumfalte($schiff_id, $basen_planeten_id, false, $res_array);
		}
	}
	
	/**
	 * Versorgt andere Planeten mit Sternenbasen oder neue Basen-Planeten mit Raumfalten vom uebergebenen 
	 * Planeten mit Sternenbasis aus.
	 * arguments: $planeten_id - Die Datenbank-ID es Planeten, von dem aus andere Sternenbasen/neue Basen-Planeten
	 * 							 per Raumfalte versorgt werden sollen.
	 */
	function versorgeAndereBasen($planeten_id) {
		$basen_planeten = array_merge(eigenschaften::$basen_planeten_ids, eigenschaften::$neue_basis_planeten);
		if(count($basen_planeten) == 0) return;
		$planeten_res = @mysql_query("SELECT cantox, min1, min2, min3, lemin FROM skrupel_planeten 
			WHERE id='$planeten_id'");
		$planeten_res = @mysql_fetch_array($planeten_res);
		$cantox = $planeten_res['cantox'];
		$min1 = $planeten_res['min1'];
		$min2 = $planeten_res['min2'];
		$min3 = $planeten_res['min3'];
		$lemin = $planeten_res['lemin'];
		foreach($basen_planeten as $planet) {
			if($planet == $planeten_id || count($this->ermittlePlanetenRaumfalten($planet)) > 
			eigenschaften::$raumfalten_infos->max_raumfalten_pro_planet) continue;
			$planeten_res = @mysql_query("SELECT cantox, min1, min2, min3, lemin FROM skrupel_planeten 
				WHERE id='$planet'");
			$planeten_res = @mysql_fetch_array($planeten_res);
			$cantox_planet = $planeten_res['cantox'];
			$min1_planet = $planeten_res['min1'];
			$min2_planet = $planeten_res['min2'];
			$min3_planet = $planeten_res['min3'];
			$lemin_planet = $planeten_res['lemin'];
			$raumfalte_min1 = $min1 - eigenschaften::$raumfalten_infos->raumfalte_min_min1_basen;
			if(eigenschaften::$raumfalten_infos->max_min1_basen < $min1_planet 
			|| $raumfalte_min1 < 0) $raumfalte_min1 = 0;
			$raumfalte_min2 = $min2 - eigenschaften::$raumfalten_infos->raumfalte_min_min2_basen;
			if(eigenschaften::$raumfalten_infos->max_min2_basen < $min2_planet 
			|| $raumfalte_min2 < 0) $raumfalte_min2 = 0;
			$raumfalte_min3 = $min3 - eigenschaften::$raumfalten_infos->raumfalte_min_min3_basen;
			if(eigenschaften::$raumfalten_infos->max_min3_basen < $min3_planet 
			|| $raumfalte_min3 < 0) $raumfalte_min3 = 0;
			$raumfalte_lemin = $lemin - eigenschaften::$raumfalten_infos->raumfalte_min_lemin_basen;
			if(eigenschaften::$raumfalten_infos->max_lemin_basen < $lemin_planet 
			|| $raumfalte_lemin < 0) $raumfalte_lemin = 0;
			$resourcen = array(0, 0, $raumfalte_lemin, $raumfalte_min1, $raumfalte_min2, $raumfalte_min3);
			$cantox_kosten = $this->berechneRaumfaltenKosten($resourcen);
			$raumfalte_cantox = $cantox - eigenschaften::$raumfalten_infos->raumfalte_min_cantox_basen 
								-$cantox_kosten;
			if(eigenschaften::$raumfalten_infos->max_cantox_basen < $cantox_planet 
			|| $raumfalte_cantox < 0) $raumfalte_cantox = 0;
			$resourcen[0] = $raumfalte_cantox;
			if($this->sendeRaumfalte($planet, $planeten_id, true, $resourcen)) {
				$planeten_res = @mysql_query("SELECT cantox, min1, min2, min3, lemin FROM skrupel_planeten 
					WHERE id='$planeten_id'");
				$planeten_res = @mysql_fetch_array($planeten_res);
				$cantox = $planeten_res['cantox'];
				$min1 = $planeten_res['min1'];
				$min2 = $planeten_res['min2'];
				$min3 = $planeten_res['min3'];
				$lemin = $planeten_res['lemin'];
			}
		}
	}
	
	/**
	 * Verwaltet alle Sternenbasen bezueglich des Ausbaus der Technologien und des Bauens neuer Sternenbasen.
	 * Diese Implementierung baut eine neue Sternenbasis, sobald alle vorhanden einen gewissen Ausbaugrad 
	 * erreicht haben. Kann die neue Basis aufgrund von Resourcenmangel auf dem Planeten noch nicht gebaut 
	 * werden, wird sie dem Array eigenschaften::$neue_basis_planeten hinzugefuegt, damit in der Naehe 
	 * befindliche Frachter diesen Planeten mit Resourcen versorgen.
	 */
	function verwalteBasen() {
		$fertig_ausgebaute_basen = 0;
		if(count(eigenschaften::$basen_planeten_ids) == 0) {
			$neuer_basis_planet = $this->ermittleNeuenBasenPlaneten();
			$this->baueNeueSternenbasis($neuer_basis_planet);
			return;
		}
		foreach(eigenschaften::$basen_planeten_ids as $planeten_id) {
			if($this->ermittleTechLevel($planeten_id, "t_huelle") >= 7 
			&& $this->ermittleTechLevel($planeten_id, "t_antrieb") >= 8) {
				$this->verwalteSchiffeOhneLemin($planeten_id);
			}
			$this->erweitereBasen($planeten_id);
			if(eigenschaften::$tick >= eigenschaften::$planeten_infos->kein_auto_bau_limit) 
				$this->baueBasisVerteidigung($planeten_id);
			basen_schiffbau_leicht::baueNeuesSchiff($planeten_id);
			$basis_infos = @mysql_query("SELECT t_huelle, t_antrieb, t_energie, t_explosiv 
				FROM skrupel_sternenbasen WHERE planetid='$planeten_id'");
			$basis_infos = @mysql_fetch_array($basis_infos);
			if($basis_infos['t_huelle'] >= eigenschaften::$basen_neu_infos->neue_basis_min_rumpf 
			&& $basis_infos['t_antrieb'] >= eigenschaften::$basen_neu_infos->neue_basis_min_antrieb 
			&& $basis_infos['t_energie'] >= eigenschaften::$basen_neu_infos->neue_basis_min_waffen 
			&& $basis_infos['t_explosiv'] >= eigenschaften::$basen_neu_infos->neue_basis_min_waffen) {
				$this->versorgeAndereBasen($planeten_id);
				$fertig_ausgebaute_basen++;
			}
		}
		if($fertig_ausgebaute_basen == count(eigenschaften::$basen_planeten_ids)) {
			if(count(eigenschaften::$neue_basis_planeten) == 0) {
				$neuer_basis_planet = $this->ermittleNeuenBasenPlaneten();
				$this->baueNeueSternenbasis($neuer_basis_planet);
				return;
			}
			foreach(eigenschaften::$neue_basis_planeten as $neuer_basis_planet) {
				$this->baueNeueSternenbasis($neuer_basis_planet);
			}
		}
	}
}
?>