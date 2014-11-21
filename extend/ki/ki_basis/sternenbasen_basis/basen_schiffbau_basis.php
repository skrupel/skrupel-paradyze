<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com) 
 * 
 * Abstrakte Oberklasse fuer den Bau von Schiffen.
 */
abstract class basen_schiffbau_basis {
	
	/**
	 * Baut ein neues Schiff an der Sternenbasis des uebergebenen Planeten.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, an dessen Sternenbasis ein Schiff 
	 * 			  gebaut werden soll.
	 */
	abstract static function baueNeuesSchiff($planeten_id);
	
	/**
	 * Ermittelt alle Klassen-Informationen als Array zur uebergebenen Klassen-ID der uebergebenen Rasse.
	 * arguments: $klassen_id - Die Klassen-ID des Schiffs, dessen Informationen gesucht sind.
	 * 			  $rasse - Die Rasse des gesuchten Schiffs.
	 * returns: Ein Array der Klassen-Informationen des gesuchten Schiffs.
	 * 			null, falls es das gesucht Schiff nicht verfuegbar ist.
	 */
	static function ermittleSchiffArray($klassen_id, $rasse) {
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$rasse_index = count($schiff_array) - 1;
			$volk = $schiff_array[$rasse_index];
			if($klassen_id == $schiff_array[1] && $rasse == $volk) return $schiff_array;
		}
		return null;
	}
	
	/**
	 * Ermittelt die Kosten fuer den Bau von einer Menge von Antrieben fuer einen bestimmten Antriebs-Level.
	 * arguments: $antriebe - Die Anzahl der Antriebe, deren Kosten ermittelt werden sollen.
	 * 			  $antriebs_level - Die Stufe der Antriebe, deren Kosten ermittelt werden sollen.
	 */
	static function ermittleAntriebsKosten($antriebe, $antriebs_level) {
		$kosten_cantox = 0;
		$kosten_min1 = 0;
		$kosten_min2 = 0;
		$kosten_min3 = 0;
		$antriebs_level--;
		if($antriebs_level >= 8) $antriebs_level--;
		for($k=1; $k<=$antriebe; $k++) {
			$kosten_cantox += eigenschaften::$basen_schiffbau_infos->antriebs_kosten[0][$antriebs_level];
			$kosten_min1 += eigenschaften::$basen_schiffbau_infos->antriebs_kosten[1][$antriebs_level];
			$kosten_min2 += eigenschaften::$basen_schiffbau_infos->antriebs_kosten[2][$antriebs_level];
			$kosten_min3 += eigenschaften::$basen_schiffbau_infos->antriebs_kosten[3][$antriebs_level];
		}
		return array($kosten_cantox, $kosten_min1, $kosten_min2, $kosten_min3);
	}
	
	/**
	 * Ermittelt die Kosten fuer den Bau von einer Menge von Energetik-Waffen fuer einen bestimmten 
	 * Energetik-Level.
	 * arguments: $waffen - Die Anzahl der Energetik-Waffen, deren Kosten ermittelt werden sollen.
	 * 			  $energetik_level - Die Stufe der Energetik, deren Kosten ermittelt werden sollen.
	 * returns: Die Kosten (in Cantox) fuer die Energetik.
	 */
	static function ermittleEnergetikKosten($waffen, $energetik_level) {
		$kosten_cantox = 0;
		$kosten_min1 = 0;
		$kosten_min2 = 0;
		$kosten_min3 = 0;
		$energetik_level--;
		for($n=1; $n<=$waffen; $n++) {
			$kosten_cantox += eigenschaften::$basen_schiffbau_infos->energetik_kosten[0][$energetik_level];
			$kosten_min1 += eigenschaften::$basen_schiffbau_infos->energetik_kosten[1][$energetik_level];
			$kosten_min2 += eigenschaften::$basen_schiffbau_infos->energetik_kosten[2][$energetik_level];
			$kosten_min3 += eigenschaften::$basen_schiffbau_infos->energetik_kosten[3][$energetik_level];
		}
		return array($kosten_cantox, $kosten_min1, $kosten_min2, $kosten_min3);
	}
	
	/**
	 * Ermittelt die Kosten fuer den Bau von einer Menge von Projektil-Waffen fuer einen bestimmten 
	 * Projektil-Level.
	 * arguments: $waffen - Die Anzahl der Projektil-Waffen, deren Kosten ermittelt werden sollen.
	 * 			  $projektil_level - Die Stufe der Projektil, deren Kosten ermittelt werden sollen.
	 * returns: Die Kosten (in Cantox) fuer die Projektil.
	 */
	static function ermittleProjektilKosten($waffen, $projektil_level) {
		$kosten_cantox = 0;
		$kosten_min1 = 0;
		$kosten_min2 = 0;
		$kosten_min3 = 0;
		$projektil_level--;
		for($i=1; $i<=$waffen; $i++) {
			$kosten_cantox += eigenschaften::$basen_schiffbau_infos->projektil_kosten[0][$projektil_level];
			$kosten_min1 += eigenschaften::$basen_schiffbau_infos->projektil_kosten[1][$projektil_level];
			$kosten_min2 += eigenschaften::$basen_schiffbau_infos->projektil_kosten[2][$projektil_level];
			$kosten_min3 += eigenschaften::$basen_schiffbau_infos->projektil_kosten[3][$projektil_level];
		}
		return array($kosten_cantox, $kosten_min1, $kosten_min2, $kosten_min3);
	}
	
	/**
	 * Ermittelt die naechste Nummer fuer den Namen des naechsten Schiffs, das gebaut wird. Dabei soll kein Name 
	 * doppelt vorkommen (was sonst moeglich waere durch zerstoerte Schiffe), sodass jedes Schiff eine eigene 
	 * Nummer hat. Es werden vorhandene als auch im Bau befindliche Schiffe beruecksichtigt.
	 * returns: Die naechste Nummer fuer den Namen eines neuen Schiffs.
	 */
	static function ermittleNaechsteSchiffNummer() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$namen = @mysql_query("SELECT name FROM skrupel_schiffe WHERE (besitzer='$comp_id') 
			AND (spiel='$spiel_id')");
		$schiff_nummern = array();
		$syntax_laenge = strlen(eigenschaften::$basen_schiffbau_infos->schiff_namen_synax);
		while($name = @mysql_fetch_array($namen)) {
			$name = $name['name'];
			$namen_laenge = strlen($name);
			$nummer_laenge = $namen_laenge - $syntax_laenge;
			$nummer = substr($name, $syntax_laenge, $nummer_laenge);
			$schiff_nummern[] = $nummer;
		}
		$namen = @mysql_query("SELECT schiffbau_name FROM skrupel_sternenbasen WHERE (besitzer='$comp_id') 
			AND (spiel='$spiel_id') AND (schiffbau_status=1)");
		while($name = @mysql_fetch_array($namen)) {
			$name = $name['schiffbau_name'];
			$namen_laenge = strlen($name);
			$nummer_laenge = $namen_laenge - $syntax_laenge;
			$nummer = substr($name, $syntax_laenge, $nummer_laenge);
			$schiff_nummern[] = $nummer;
		}
		$namen_anzahl = count($schiff_nummern);
		if($namen_anzahl == 0) return 1;
		$bekannte_nummern = array();
		for($i=0; $i < $namen_anzahl; $i++) {
			if(!(in_array($schiff_nummern[$i], $bekannte_nummern))) $bekannte_nummern[] = $schiff_nummern[$i];
		}
		sort($bekannte_nummern);
		if($bekannte_nummern[0] != 1) return 1;
		for($i=0; $i < count($bekannte_nummern); $i++) {
			$aktuelle_nummer = $bekannte_nummern[$i];
			if($aktuelle_nummer != $i+1) return $bekannte_nummern[$i] - 1;
		}
		return count($bekannte_nummern) + 1;
	}
	
	/**
	 * Gibt ein Schiff in einer Sternenbasis in Auftrag, damit es in der naechsten Runde verfuegbar ist.
	 * arguments: $schiff_array - Ein Array mit Informationen des zu bauenden Schiffs aus schiffe.txt.
	 * 			  $rasse - Die Rasse, der das zu bauende Schiff angehoert.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, an dessen Sternenbasis das Schiff gebaut 
	 * 							 werden soll.
	 * 			  $energetik_stufe - Die Energetik-Stufe der Waffen, die das Schiff haben soll.
	 * 			  $projektile_stufe - Die Projektil-Stufe der Waffen, die das Schiff haben soll.
	 * 			  $antrieb_stufe - Die Antriebs-Stufe, die das Schiff haben soll.
	 */
	static function stelleSchiffAuftrag($schiff_array, $planeten_id, $rasse, 
								$energetik_stufe,  $projektile_stufe, $antrieb_stufe) {
		$schiff_name = eigenschaften::$basen_schiffbau_infos->schiff_namen_synax;
		$schiff_nummer = self::ermittleNaechsteSchiffNummer();
		$schiff_name = $schiff_name.$schiff_nummer;
		@mysql_query("UPDATE skrupel_sternenbasen SET schiffbau_status=1, schiffbau_klasse ='$schiff_array[1]', 
				schiffbau_bild_gross ='$schiff_array[3]', schiffbau_tank='$schiff_array[13]', 
				schiffbau_bild_klein ='$schiff_array[4]', schiffbau_crew='$schiff_array[15]', 
				schiffbau_masse='$schiff_array[16]', schiffbau_fracht='$schiff_array[12]', 
				schiffbau_antriebe='$schiff_array[14]', schiffbau_energetik='$schiff_array[9]', 
				schiffbau_projektile='$schiff_array[10]', schiffbau_hangar='$schiff_array[11]', 
				schiffbau_klasse_name='$schiff_array[0]', schiffbau_rasse='$rasse', 
				schiffbau_fertigkeiten='$schiff_array[17]', schiffbau_techlevel='$schiff_array[2]', 
				schiffbau_energetik_stufe='$energetik_stufe', schiffbau_antriebe_stufe='$antrieb_stufe', 
				schiffbau_projektile_stufe='$projektile_stufe', schiffbau_name='$schiff_name' 
				WHERE planetid='$planeten_id'");
	}
	
	/**
	 * Baut das uebergebene Schiff an der Sternenbasis des uebergebenen Planten. Zu erst werden die Resourcen 
	 * des Planeten fuer den Bau des Rumpfes ueberprueft. Sind diese Ausreichend, wird ueberprueft, ob die 
	 * Resourcen fuer die Antriebe ausreichen (gegebenenfalls wird eine niedrigere Antriebsstufe gebaut, 
	 * gemaess antrieb_toleranz_limit). Sind diese auch ausreichend, werden die Resourcen fuer die 
	 * Waffensysteme ueberprueft (und gegebenenfalls wird eine niedrigere Waffenstufe gebaut, gemaess 
	 * waffen_toleranz_limit). Sind alle Resourcen verfuegbar, wird das Schiff in Auftrag gegeben.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, auf dessen Sternenbasis das Schiff gebaut 
	 * 							 werden soll.
	 * 			  $klassen_id - Die Klassen-ID des zu bauenden Schiffs.
	 * 			  $rasse - Die Rasse, der das Schiff angehoert.
	 * 			  $min_waffen - true, wenn das zu bauende Schiff die schwaechsten Waffen erhalten soll. 
	 * 							false, sonst.
	 * 			  $min_antrieb - true, wenn das zu bauende Schiff den langsamsten Antrieb erhalten soll. 
	 * 							 false, sonst.
	 * returns: true - Falls das Schiff erfolgreich in Auftrag gegeben wurde (Dh. alle Resourcen fuer den Bau 
	 * 				   sind ausreichend und wurden vom Planeten abgezogen).
	 * 			false - Falls die Resourcen fuer den Rumpf, die Antriebe oder die Waffensysteme nicht 
	 * 					ausreichen oder	ein andere Fehler aufgetreten ist.
	 */
	static function baueSchiff($planeten_id, $klassen_id, $rasse, $min_waffen, $min_antrieb) {
		$schiff_array = self::ermittleSchiffArray($klassen_id, $rasse);
		$cantox = ki_basis::ermittleResource($planeten_id, "cantox");
		$min1 = ki_basis::ermittleResource($planeten_id, "min1");
		$min2 = ki_basis::ermittleResource($planeten_id, "min2");
		$min3 = ki_basis::ermittleResource($planeten_id, "min3");
		//Reichen die Resourcen des Planeten schon fuer den Bau des Rumpfes nicht, wird abgebrochen.
		if($cantox < $schiff_array[5] 
		|| $min1 < $schiff_array[6] 
		|| $min2 < $schiff_array[7] 
		|| $min3 < $schiff_array[8]) return false;
		$antrieb_cantox = 0; $antrieb_min1 = 0; $antrieb_min2 = 0; $antrieb_min3 = 0;
		$antrieb_stufe = 0;
		//Hier wird unterschieden, ob der minimale Antrieb verwendet werden soll (zB. bei 
		//Schiffen mit Subpartikelcluster oder Quarksreorganistor).
		if($min_antrieb) {
			$antrieb_stufe = 1;
			$antriebs_kosten = self::ermittleAntriebsKosten($schiff_array[14], $antrieb_stufe);
			$antrieb_cantox = $antriebs_kosten[0]; $antrieb_min1 = $antriebs_kosten[1];
			$antrieb_min2 = $antriebs_kosten[2]; $antrieb_min3 = $antriebs_kosten[3];
			if(!($cantox >= $schiff_array[5] + $antrieb_cantox) 
			|| !($min1 >= $schiff_array[6] + $antrieb_min1) 
			|| !($min2 >= $schiff_array[7] + $antrieb_min2) 
			|| !($min3 >= $schiff_array[8] + $antrieb_min3)) 
				return false;
		} else {
			//Diese Schleife realisiert das Toleranz-Niveau fuer den Bau der Antriebe.
			for($n=0; $n <= eigenschaften::$basen_schiffbau_infos->antrieb_toleranz_limit; $n++) {
				$antrieb_stufe = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_antrieb") - $n;
				$antriebs_kosten = self::ermittleAntriebsKosten($schiff_array[14], $antrieb_stufe);
				$antrieb_cantox = $antriebs_kosten[0]; $antrieb_min1 = $antriebs_kosten[1];
				$antrieb_min2 = $antriebs_kosten[2]; $antrieb_min3 = $antriebs_kosten[3];
				if(($cantox >= $schiff_array[5] + $antrieb_cantox) 
				&& ($min1 >= $schiff_array[6] + $antrieb_min1) 
				&& ($min2 >= $schiff_array[7] + $antrieb_min2) 
				&& ($min3 >= $schiff_array[8] + $antrieb_min3)) break;
				else {
					$antrieb_cantox = 0; $antrieb_min1 = 0;
					$antrieb_min2 = 0; $antrieb_min3 = 0;
				}
			}
		}
		//Sind die Kosten alle 0, so wurde kein Antrieb gefunden, der bezahlbar ist.
		if($antrieb_cantox == 0 && $antrieb_min1 == 0 
		&& $antrieb_min2 == 0 && $antrieb_min3 == 0) return false;
		
		$energetik_cantox = 0; $energetik_min1 = 0; $energetik_min2 = 0; $energetik_min3 = 0;
		$energetik_stufe = 0;
		//Hier werden die Energetik-Waffen gebaut.
		if($schiff_array[9] > 0) {
			//Hier wird unterschieden, ob die minimalen Energetik-Waffen verwendet werden soll 
			//(zB. bei Schiffen mit Subpartikelcluster oder Quarksreorganistor oder auch Frachtern).
			if($min_waffen) {
				$energetik_stufe = 1;
				$energetik_kosten = self::ermittleEnergetikKosten($schiff_array[9], $energetik_stufe);
				$energetik_cantox = $energetik_kosten[0]; $energetik_min1 = $energetik_kosten[1]; 
				$energetik_min2 = $energetik_kosten[2]; $energetik_min3 = $energetik_kosten[3];
				if(!($cantox - $antrieb_cantox - $schiff_array[5] >= $energetik_cantox) 
				|| !($min1 - $antrieb_min1 - $schiff_array[6] >= $energetik_min1) 
				|| !($min2 - $antrieb_min2 - $schiff_array[7] >= $energetik_min2) 
				|| !($min3 - $antrieb_min3 - $schiff_array[8] >= $energetik_min3)) 
					return false;
			} else {
				//Diese Schleife realisiert das Toleranz-Niveau fuer den Bau der Energie-Waffen.
				for($n=0; $n <= eigenschaften::$basen_schiffbau_infos->waffen_toleranz_limit; $n++) {
					$energetik_stufe = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_energie") - $n;
					$energetik_kosten = self::ermittleEnergetikKosten($schiff_array[9], $energetik_stufe);
					$energetik_cantox = $energetik_kosten[0]; $energetik_min1 = $energetik_kosten[1]; 
					$energetik_min2 = $energetik_kosten[2]; $energetik_min3 = $energetik_kosten[3];
					if(($cantox - $antrieb_cantox - $schiff_array[5] >= $energetik_cantox) 
					&& ($min1 - $antrieb_min1 - $schiff_array[6] >= $energetik_min1) 
					&& ($min2 - $antrieb_min2 - $schiff_array[7] >= $energetik_min2) 
					&& ($min3 - $antrieb_min3 - $schiff_array[8] >= $energetik_min3)) break;
					else {
						$energetik_cantox = 0; $energetik_min1 = 0; 
						$energetik_min2 = 0; $energetik_min3 = 0;
					}
				}
				//Sind die Kosten alle 0, so wurde keine Energetik-Waffe gefunden, der bezahlbar ist.
				if($energetik_cantox == 0 && $energetik_min1 == 0 
				&& $energetik_min2 == 0 && $energetik_min3 == 0) return false;
			}
		}
		
		$projektil_cantox = 0; $projektil_min1 = 0; $projektil_min2 = 0; $projektil_min3 = 0;
		$projektil_stufe = 0;
		//Hier werden die Projektil-Waffen gebaut.
		if($schiff_array[10] > 0) {
			//Hier wird unterschieden, ob die minimalen Projektil-Waffen verwendet werden soll 
			//(zB. bei Schiffen mit Subpartikelcluster oder Quarksreorganistor oder auch Frachtern)
			if($min_waffen) {
				$projektil_stufe = 1;
				$projektil_kosten = self::ermittleProjektilKosten($schiff_array[10], $projektil_stufe);
				$projektil_cantox = $projektil_kosten[0]; $projektil_min1 = $projektil_kosten[1]; 
				$projektil_min2 = $projektil_kosten[2]; $projektil_min3 = $projektil_kosten[3];
				if(!($cantox - $antrieb_cantox - $energetik_cantox - $schiff_array[5] >= $projektil_cantox) 
				|| !($min1 - $antrieb_min1 - $energetik_min1 - $schiff_array[6] >= $projektil_min1) 
				|| !($min2 - $antrieb_min2 - $energetik_min2 - $schiff_array[7] >= $projektil_min2) 
				|| !($min3 - $antrieb_min3 - $energetik_min3 - $schiff_array[8] >= $projektil_min3))
					return false;
			} else {
				//Diese Schleife realisiert das Toleranz-Niveau fuer den Bau der Projektil-Waffen.
				for($n=0; $n <= eigenschaften::$basen_schiffbau_infos->waffen_toleranz_limit; $n++) {
					$projektil_stufe = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_explosiv") - $n;
					$projektil_kosten = self::ermittleProjektilKosten($schiff_array[10], $projektil_stufe);
					$projektil_cantox = $projektil_kosten[0]; $projektil_min1 = $projektil_kosten[1]; 
					$projektil_min2 = $projektil_kosten[2]; $projektil_min3 = $projektil_kosten[3];
					if(($cantox - $antrieb_cantox - $energetik_cantox - $schiff_array[5] >= $projektil_cantox) 
					&& ($min1 - $antrieb_min1 - $energetik_min1 - $schiff_array[6] >= $projektil_min1) 
					&& ($min2 - $antrieb_min2 - $energetik_min2 - $schiff_array[7] >= $projektil_min2) 
					&& ($min3 - $antrieb_min3 - $energetik_min3 - $schiff_array[8] >= $projektil_min3)) break;
					else {
						$projektil_cantox = 0; $projektil_min1 = 0;
						$projektil_min2 = 0; $projektil_min3 = 0;
					}
				}
				//Sind die Kosten alle 0, so wurde keine Projektil-Waffe gefunden, der bezahlbar ist.
				if($projektil_cantox == 0 && $projektil_min1 == 0 
				&& $projektil_min2 == 0 && $projektil_min3 == 0) return false;
			}
		}
		
		//Jetzt werden die Resourcen fuer den Bau des Rumpfes, der Antriebe und der Waffen vom
		//Planeten abgezogen.
		ki_basis::zieheResourcenAb("cantox", 
					$schiff_array[5] + $antrieb_cantox + $projektil_cantox + $energetik_cantox, $planeten_id);
		ki_basis::zieheResourcenAb("min1", 
					$schiff_array[6] + $antrieb_min1 + $projektil_min1 + $energetik_min1, $planeten_id);
		ki_basis::zieheResourcenAb("min2", 
					$schiff_array[7] + $antrieb_min2 + $projektil_min2 + $energetik_min2, $planeten_id);
		ki_basis::zieheResourcenAb("min3", 
					$schiff_array[8] + $antrieb_min3 + $projektil_min3 + $energetik_min3, $planeten_id);
		
		self::stelleSchiffAuftrag($schiff_array, $planeten_id, $rasse, $energetik_stufe, $projektil_stufe, 
								  $antrieb_stufe);
		return true;
	}
}
?>