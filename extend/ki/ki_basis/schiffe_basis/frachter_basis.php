<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Abstrakte Oberklasse fuer Frachter.
 */
abstract class frachter_basis extends schiffe_basis {
	
	/**
	 * Prueft, ob es sinnvoll oder erforderlich ist, weiter zu kolonisieren.
	 */
	abstract static function mehrKolonien();
	
	/**
	 * Analysiert den Status jedes einzelnen Frachters und entscheidet, wie er sich verhalten soll.
	 */
	abstract function verwalteFrachter();
	
	/**
	 * Prueft, ob das uebergebene Schiff in der naehe eines Planeten ist, an dem eine neue Sternenbasis gebaut
	 * werden soll. Falls ja, wird (egal ob das Schiff in einer Route ist oder nicht) das Schiff 
	 * dort hingeschickt.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das zur neuen Basis fliegen soll.
	 * returns: true - Falls das Schiff zur naechsten neuen Basis geschickt wurde.
	 * 			false - Sonst.
	 */
	function zuNeuenBasis($schiff_id) {
		$schiff_daten = @mysql_query("SELECT kox, koy, routing_status FROM skrupel_schiffe 
			WHERE id='$schiff_id'");
		$schiff_daten = @mysql_fetch_array($schiff_daten);
		$schiff_x = $schiff_daten['kox'];
		$schiff_y = $schiff_daten['koy'];
		$schiff_routen_status = $schiff_daten['routing_status'];
		$spiel_id = eigenschaften::$spiel_id;
		//Zuerst werden die Koordinaten aller Planeten bestimmt, auf denen eine neue Basis gebaut werden soll.
		$koords = @mysql_query("SELECT p.x_pos, p.y_pos, p.id FROM skrupel_planeten p, skrupel_ki_neuebasen nb 
			WHERE (p.id=nb.planeten_id) AND (p.spiel='$spiel_id')");
		$neue_basen_koords = array();
		while($koord = @mysql_fetch_array($koords)) {
			$neue_basen_koords[] = array('x'=>$koord['x_pos'], 'y'=>$koord['y_pos'], 'id'=>$koords['id']);
		}
		if(count($neue_basen_koords) == 0) return;
		//Nun wird der naechste Planet bestimmt.
		$neue_basis = ki_basis::ermittleNahesZiel($schiff_id, $neue_basen_koords, 
								eigenschaften::$bekannte_wurmloch_koords);
		$strecke = floor(ki_basis::berechneStrecke($neue_basis['x'], $neue_basis['y'], $schiff_x, $schiff_y));
		//Das Schiff wird nur dort hingeflogen, falls es nicht zu weit entfernt ist und nicht schon da ist.
		if($strecke <= eigenschaften::$frachter_infos->max_frachter_reichweite && $strecke != 0) {
			$warp = $this->ermittleMaximumWarp($schiff_id);
			$this->fliegeSchiff($schiff_id, $neue_basis['x'], $neue_basis['y'], $warp, $neue_basis['id']);
			//Alle relevanten Routen-Infos werden geloescht, damit es nicht zu einem Konflikt kommt und 
			//damit die Planeten-IDs aus der Route wieder frei fuer andere Frachter sind.
			if($schiff_routen_status == 2) 
				@mysql_query("UPDATE skrupel_schiffe SET routing_status=0, routing_id='' WHERE id='$schiff_id'");
			return true;
		}
		return false;
	}
	
	/**
	 * Aktualisiert den Routen-Logbuch-Eintrag des uebergebenen Schiffes mit dem uebergebenen Planeten.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, dessen Routen-Logbuch-Eintrag aktualisiert wird.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, das als letztes vom Schiff besucht wurde.
	 */
	function updateLogBuchRoute($schiff_id, $planeten_id) {
		@mysql_query("UPDATE skrupel_schiffe SET logbuch='$planeten_id' WHERE id='$schiff_id'");
	}
	
	/**
	 * Entfernt den uebergebenen Planeten aus allen Frachter-Routen der KI. Es wird kein Planet geloescht, der 
	 * eigentlich beliefert werden soll!
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, der aus allen Frachter-Routen entfernt wird.
	 */
	static function entfernePlanetenAusRouten($planeten_id) {
		foreach(eigenschaften::$frachter_ids as $frachter_id) {
			$route = @mysql_query("SELECT routing_id, routing_status FROM skrupel_schiffe 
				WHERE id='$frachter_id'");
			$route = @mysql_fetch_array($route);
			if($route == null || $route['routing_status'] == 0 || $route['routing_id'] == null 
			|| $route['routing_id'] == '' || $route['routing_id'] == "") continue;
			$route = $route['routing_id'];
			$planeten_ids = explode(':', $route);
			$routen_laenge = count($planeten_ids) - 2;
			if($planeten_ids[$routen_laenge] == $planeten_id) continue;
			$neue_route = array();
			foreach($planeten_ids as $id) {
				if($id != $planeten_id) $neue_route[] = $id;
			}
			$neue_route = implode(':', $neue_route);
			@mysql_query("UPDATE skrupel_schiffe SET routing_id='$neue_route' WHERE id='$frachter_id'");
		}
	}
	
	/**
	 * Ermittlet alle Datenbank-IDs von Planeten, die in einer Route eines Schiffs der KI ist.
	 * returns: Ein Array mit Datenbank-IDs von Planeten, die in einer Route sind.
	 */
	function ermittlePlanetenInRouten() {
		$planeten_in_routen = array();
		foreach(eigenschaften::$frachter_routen as $route) {
			foreach($route as $planeten_id) $planeten_in_routen[] = $planeten_id;
		}
		return $planeten_in_routen;
	}
	
	/**
	 * Ermittelt alle Routen von Frachtern und speichert diese als Arrays von Datenbank-IDs der Planeten 
	 * in eigenschaften::$frachter_routen.
	 */
	function ermittleRouten() {
		eigenschaften::$frachter_routen = array();
		foreach(eigenschaften::$frachter_ids as $frachter_id) {
			$route = @mysql_query("SELECT routing_id FROM skrupel_schiffe 
				WHERE (id='$frachter_id') AND (routing_status=2)");
			$route = @mysql_fetch_array($route);
			if($route != null) {
				$route = $route['routing_id'];
				$planeten_ids = explode(':', $route);
				eigenschaften::$frachter_routen[] = $planeten_ids;
			}
		}
	}
	
	/**
	 * Ermittelt die Anzahl der Routen, die den uebergebenen Planeten beliefern.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, dessen Liefer-Routen ermittelt werden sollen.
	 * returns: Die Anzahl der Routen, die diesen Planeten mit Resourcen versorgen.
	 */
	function ermittleRoutenVonPlanet($planeten_id) {
		$planet_routen = 0;
		foreach(eigenschaften::$frachter_routen as $route) {
			//Es muss Minus 2 sein, da das letzte Array-Element null ist wegen explode().
			$routenlaenge = count($route)-2;
			if($route[$routenlaenge] == $planeten_id) $planet_routen++;
		}
		return $planet_routen;
	}
	
	/**
	 * Ermittelt alle Frachter, die Kolonisten geladen haben, bis auf den uebergebenen Frachter.
	 * arguments: $schiff_id - Die Datebank-ID des Schiffs, das ignoriert werden soll.
	 * returns: Alle Frachter mit Kolonisten (bis auf das uebergebene Schiff) als Array.
	 */
	static function ermittleAndereKolonieSchiffe($schiff_id) {
		$andere_kolo_schiffe = array();
		foreach(eigenschaften::$frachter_ids as $frachter_id) {
			$frachter_leute = @mysql_query("SELECT fracht_leute FROM skrupel_schiffe 
				WHERE (id='$frachter_id') AND (NOT (id='$schiff_id'))");
			$frachter_leute = @mysql_fetch_array($frachter_leute);
			$frachter_leute = $frachter_leute['fracht_leute'];
			if($frachter_leute >= eigenschaften::$frachter_kolo_infos->kolo_leute) 
				$andere_kolo_schiffe[] = $frachter_id;
		}
		return $andere_kolo_schiffe;
	}
	
	/**
	 * Belaedt das uebergebene Schiff mit Kolonisten, Vorraeten und Cantox. Die Mengen pro Planet, 
	 * der besiedelt werden soll, haengen von eigenschaften::$frachter_kolo_infos->kolo_cantox, 
	 * eigenschaften::$frachter_kolo_infos->kolo_leute und 
	 * eigenschaften::$frachter_kolo_infos->kolo_vorrat ab. Es wird ausserdem das Schiff betankt 
	 * und die Resourcen werden vom Planeten auch abgezogen. Ausserdem werden Resourcen nur fuer
	 * so viele Planeten beladen, bis der Frachtraum des Schiffs voll ist oder bis fuer soviele Kolonien 
	 * wie in eigenschaften::$frachter_kolo_infos->kolo_max_kolonien geladen wurde.
	 * Es findet keine Ueberpruefung statt, ob das Schiff bei einem Planeten ist!
	 * arguments: $schiff_id - Die Datenbank-ID des zu beladenen Schiffs.
	 */
	function beladeKolonieSchiff($schiff_id) {
		$schiff_infos = @mysql_query("SELECT kox, koy, frachtraum FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$x_pos = $schiff_infos['kox'];
		$y_pos = $schiff_infos['koy'];
		$frachtraum = $schiff_infos['frachtraum'];
		$spiel_id = eigenschaften::$spiel_id;
		$resourcen = @mysql_query("SELECT kolonisten, cantox, vorrat, id FROM skrupel_planeten 
			WHERE (x_pos='$x_pos') AND (y_pos='$y_pos') AND (spiel='$spiel_id')");
		$resourcen = @mysql_fetch_array($resourcen);
		$planeten_id = $resourcen['id'];
		$planet_cantox = $resourcen['cantox'];
		$planet_vorrat = $resourcen['vorrat'];
		$leute_max = ($resourcen['kolonisten'] / 2) + 1000;
		$max_kolonien_leute = eigenschaften::$frachter_kolo_infos->kolo_leute 
							  * eigenschaften::$frachter_kolo_infos->kolo_max_kolonien;
		if($leute_max > $max_kolonien_leute) $leute_max = $max_kolonien_leute;
		$fracht_cantox = 0; 
		$fracht_leute = 0; 
		$fracht_vorrat = 0;
		$noch_mehr = true;
		while($noch_mehr) {
			$fracht_cantox += eigenschaften::$frachter_kolo_infos->kolo_cantox;
			$fracht_leute += eigenschaften::$frachter_kolo_infos->kolo_leute;
			$fracht_vorrat += eigenschaften::$frachter_kolo_infos->kolo_vorrat;
			if((($fracht_leute / 100) + $fracht_vorrat) > $frachtraum 
			|| ($fracht_leute > $leute_max) 
			|| ($fracht_cantox > $planet_cantox) 
			|| ($fracht_vorrat > $planet_vorrat)) {
				$fracht_cantox -= eigenschaften::$frachter_kolo_infos->kolo_cantox;
				$fracht_leute -= eigenschaften::$frachter_kolo_infos->kolo_leute;
				$fracht_vorrat -= eigenschaften::$frachter_kolo_infos->kolo_vorrat;
				$noch_mehr = false;
			}
		}
		$this->tankeLeminStart($schiff_id, $planeten_id);
		@mysql_query("UPDATE skrupel_schiffe SET fracht_leute='$fracht_leute', fracht_cantox='$fracht_cantox', 
			fracht_vorrat='$fracht_vorrat' WHERE id='$schiff_id'");
		ki_basis::zieheResourcenAb("kolonisten", $fracht_leute, $planeten_id);
		ki_basis::zieheResourcenAb("cantox", $fracht_cantox, $planeten_id);
		ki_basis::zieheResourcenAb("vorrat", $fracht_vorrat, $planeten_id);
	}
	
	/**
	 * Der Planet, bei dem das uebergebene Schiff ist, wird kolonisiert, dh. es werden Kolonisten, Vorraete 
	 * und Cantox auf dem Planeten geladen (gemaess eigenschaften::$frachter_kolo_infos->kolo_leute, 
	 * eigenschaften::$frachter_kolo_infos->kolo_vorrat und 
	 * eigenschaften::$frachter_kolo_infos->kolo_cantox). Ausserdem wird das Schiff betankt. Sind noch 
	 * genuegend Leute fuer weitere Planeten im Frachtraum, so wird zum naechsten unbewohnten Planeten 
	 * geflogen. Sonst wird zum naechsten wichtigen Planeten geflogen.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das kolonisiert.
	 */
	function kolonisierePlanet($schiff_id) {
		$schiff_infos = @mysql_query("SELECT kox, koy, fracht_leute, fracht_cantox, fracht_vorrat 
			FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		$spiel_id = eigenschaften::$spiel_id;
		$schiff_x = $schiff_infos['kox'];
		$schiff_y = $schiff_infos['koy'];
		$schiff_leute = $schiff_infos['fracht_leute'];
		$schiff_cantox = $schiff_infos['fracht_cantox'];
		$schiff_vorrat = $schiff_infos['fracht_vorrat'];
		$planeten_id = @mysql_query("SELECT id FROM skrupel_planeten 
			WHERE (x_pos='$schiff_x') AND (y_pos='$schiff_y') AND (spiel='$spiel_id')");
		$planeten_id = @mysql_fetch_array($planeten_id);
		$planeten_id = $planeten_id['id'];
		if($schiff_leute >= eigenschaften::$frachter_kolo_infos->kolo_leute 
		&& $schiff_cantox >= 10 && $schiff_vorrat >= 1) {
			$kolo_leute = eigenschaften::$frachter_kolo_infos->kolo_leute;
			$kolo_cantox = eigenschaften::$frachter_kolo_infos->kolo_cantox;
			$kolo_vorrat = eigenschaften::$frachter_kolo_infos->kolo_vorrat;
			//Falls das Schiff zB. durch Piraten  beraubt wurde, so wird hier vermieden, dass Frachter auf einmal 
			//negative Resourcen haben.
			if($schiff_cantox < eigenschaften::$frachter_kolo_infos->kolo_cantox) $kolo_cantox = $schiff_cantox;
			if($schiff_vorrat < eigenschaften::$frachter_kolo_infos->kolo_vorrat) $kolo_vorrat = $schiff_vorrat;
			$comp_id = eigenschaften::$comp_id;
			@mysql_query("UPDATE skrupel_planeten SET kolonisten_new='$kolo_leute', cantox='$kolo_cantox', 
				vorrat='$kolo_vorrat', kolonisten_spieler='$comp_id' WHERE id='$planeten_id'");
			$leute = $schiff_leute - eigenschaften::$frachter_kolo_infos->kolo_leute;
			$cantox = $schiff_cantox - eigenschaften::$frachter_kolo_infos->kolo_cantox;
			$vorrat = $schiff_vorrat - eigenschaften::$frachter_kolo_infos->kolo_vorrat;
			//Hier wird nochmals sicher gestellt, dass Frachter keine negativen Resourcen bekommen.
			if($cantox < 0) $cantox = 0;
			if($vorrat < 0) $vorrat = 0;
			@mysql_query("UPDATE skrupel_schiffe SET fracht_leute='$leute', fracht_cantox='$cantox', 
				fracht_vorrat='$vorrat' WHERE id='$schiff_id'");
		}
		$this->tankeLemin($schiff_id, $planeten_id);
		eigenschaften::$kolonien_ids[] = $planeten_id;
		$schiff_leute = @mysql_query("SELECT fracht_leute FROM skrupel_schiffe WHERE id='$schiff_id'");
		$schiff_leute = @mysql_fetch_array($schiff_leute);
		if($schiff_leute['fracht_leute'] < eigenschaften::$frachter_kolo_infos->kolo_leute || $schiff_cantox < 10 
		|| $schiff_vorrat == 0) 
			$this->zuWichtigenPlaneten($schiff_id);
		else $this->fliegeKolonieSchiff($schiff_id);
	}
	
	/**
	 * Fliegt das uebegebene Schiff zum naechsten unbewohnten Planeten in Sichtweite. Dabei werden keine 
	 * Planeten angeflogen, die schon als nicht lohnend identifiziert wurden.
	 * arguments: $schiff_id - Die Datenbank-ID des zu fliegenden Schiffs.
	 */
	static function fliegeKolonieSchiff($schiff_id) {
		$start = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$schiff_id'");
		$start = @mysql_fetch_array($start);
		$x_start = $start['kox'];
		$y_start = $start['koy'];
		$antrieb_stufe = schiffe_basis::ermittleMaximumWarp($schiff_id);
		$schlechte_planeten = ki_basis::ermittleSchlechtePlaneten();
		$ausnahmen = array_merge(eigenschaften::$kolonien_ids, $schlechte_planeten);
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$min_kolo = eigenschaften::$frachter_kolo_infos->kolo_leute;
		$planeten = @mysql_query("SELECT p.id FROM skrupel_planeten p WHERE (p.spiel='$spiel_id') 
			AND (p.besitzer=0) AND (EXISTS (SELECT * FROM skrupel_schiffe s WHERE (s.zielid=p.id) 
			AND (s.besitzer='$comp_id') AND (s.fracht_leute >= '$min_kolo')))");
		while($planet = @mysql_fetch_array($planeten)) {
			$ausnahmen[] = $planet['id'];
		}
		$naher_planet = ki_basis::ermittleNahenPlaneten($x_start, $y_start, $ausnahmen, false, true);
		schiffe_basis::fliegeSchiff($schiff_id, $naher_planet['x'], $naher_planet['y'], 
			$antrieb_stufe, $naher_planet['id']);
	}
}
?>