<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Implementierung von basen_schiffbau_basis.
 */
class basen_schiffbau_leicht extends basen_schiffbau_basis {
	
	/**
	 * Ermittelt den besten Frachter, der in der Sternenbasis des uebergebenen Planeten gebaut werden kann. Der 
	 * beste Frachter ist in dieser Implementierung das Schiff, das genug Frachtraum, den hoechsten Tech-Level
	 * beim Rumpf und nicht zu viele Waffen hat.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten mit Sternenbasis.
	 * returns: Die Klassen-ID sowie die Rasse des Frachters, der in der Sternenbasis am besten ist.
	 * 			null, falls es keinen guten Frachter gibt.
	 */
	static function ermittleBestenFrachter($planeten_id) {
		$frachter_id = null;
		$max_rumpf = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_huelle");
		$max_frachtraum = 0;
		$rasse = null;
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$tech_level_schiff = $schiff_array[2];
			$frachtraum = $schiff_array[12];
			if(($frachtraum >= eigenschaften::$frachter_infos->min_frachtraum_frachter) 
			&& ($max_frachtraum < $frachtraum) && ($max_rumpf >= $tech_level_schiff) 
			&& ($schiff_array[9] + $schiff_array[10] + $schiff_array[11] <= 
			eigenschaften::$frachter_infos->max_frachter_waffen)) {
				$max_frachtraum = $frachtraum;
				$frachter_id = $schiff_array[1];
				$rasse_index = count($schiff_array) - 1;
				$rasse = $schiff_array[$rasse_index];
			}
		}
		if($frachter_id == null) return null;
		return array('id'=>$frachter_id, 'rasse'=>$rasse);
	}
	
	/**
	 * Ermittelt den besten Scout, der in der Sternenbasis des uebergebenen Planeten gebaut werden kann. 
	 * Der beste Scout ist in dieser Implementierung das Schiff, dass genug (aber nicht zu viele) und die meisten 
	 * Waffen hat und bei gleicher Waffen-Anzahl den hoechsten Techlevel. Bei gleicher Waffen-Anzahl und Techstufe 
	 * wird schliesslich die geringste Masse betrachtet.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten mit Sternenbasis.
	 * returns Die Klassen-ID und die Rasse des Scouts, der in der Sternenbasis am besten ist.
	 * 		   null, falls es keinen guten Scout gibt.
	 */
	static function ermittleBestenScout($planeten_id) {
		$max_rumpf = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_huelle");
		$scout_id = null;
		$rasse = null;
		$leichteste_masse = 0;
		$meiste_waffen = 0;
		$hoechster_techlevel = 0;
		$cluster_schiff = schiffe_basis::ermittleClusterSchiffID();
		$quark_schiff = schiffe_basis::ermittleQuarkSchiffID();
		$cyber_schiff = schiffe_basis::ermittleCyberSchiffID();
		$terra_warm = schiffe_basis::ermittleTerraWarmID();
		$terra_kalt = schiffe_basis::ermittleTerraKaltID();
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$klassen_id = $schiff_array[1];
			$rasse_index = count($schiff_array) - 1;
			$volk = $schiff_array[$rasse_index];
			if(($klassen_id == $cluster_schiff['id'] && $volk == $cluster_schiff['rasse']) 
			|| ($klassen_id == $quark_schiff['id'] && $volk == $quark_schiff['rasse']) 
			|| ($klassen_id == $cyber_schiff['id'] && $volk == $cyber_schiff['rasse']) 
			|| ($klassen_id == $terra_warm['id'] && $volk == $terra_warm['rasse']) 
			|| ($klassen_id == $terra_kalt['id'] && $volk == $terra_kalt['rasse']) 
			|| schiffe_basis::istViralesSchiff($klassen_id, $volk)) 
				continue;
			$techlevel = $schiff_array[2];
			$masse = $schiff_array[16];
			$waffen = $schiff_array[9] + $schiff_array[10] + $schiff_array[11];
			if($max_rumpf >= $techlevel && eigenschaften::$scouts_infos->min_scout_waffen <= $waffen 
			&& eigenschaften::$scouts_infos->max_scout_waffen >= $waffen 
			&& ($meiste_waffen < $waffen || ($meiste_waffen == $waffen && $hoechster_techlevel < $techlevel) 
			|| ($meiste_waffen == $waffen && $hoechster_techlevel == $techlevel && $leichteste_masse > $masse))) {
				$meiste_waffen = $waffen;
				$scout_id = $klassen_id;
				$hoechster_techlevel = $techlevel;
				$leichteste_masse = $masse;
				$rasse = $volk;
			}
		}
		if($scout_id == null) return null;
		return array('id'=>$scout_id, 'rasse'=>$rasse);
	}
	
	/**
	 * Ermittelt den besten Jaeger, der in der Sternenbasis des uebergebenen Planeten gebaut werden kann. 
	 * Der beste Jaeger ist in dieser Implementierung das Schiff, dass genug und die meisten Waffen hat und 
	 * bei gleicher Waffen-Anzahl den hoechsten Techlevel. Bei gleicher Waffen-Anzahl und Techstufe wird 
	 * schliesslich die Masse betrachtet.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten mit Sternenbasis.
	 * returns Die Klassen-ID und die Rasse des Jaegers, der in der Sternenbasis am besten ist.
	 * 		   null, falls es keinen guten Jaeger gibt.
	 */
	static function ermittleBestenJaeger($planeten_id) {
		$max_rumpf = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_huelle");
		$jaeger_id = null;
		$rasse = null;
		$beste_masse = 0;
		$meiste_waffen = 0;
		$hoechster_techlevel = 0;
		$cluster_schiff = schiffe_basis::ermittleClusterSchiffID();
		$quark_schiff = schiffe_basis::ermittleQuarkSchiffID();
		$cyber_schiff = schiffe_basis::ermittleCyberSchiffID();
		$terra_warm = schiffe_basis::ermittleTerraWarmID();
		$terra_kalt = schiffe_basis::ermittleTerraKaltID();
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$klassen_id = $schiff_array[1];
			$rasse_index = count($schiff_array) - 1;
			$volk = $schiff_array[$rasse_index];
			if(($klassen_id == $cluster_schiff['id'] && $volk == $cluster_schiff['rasse']) 
			|| ($klassen_id == $quark_schiff['id'] && $volk == $quark_schiff['rasse']) 
			|| ($klassen_id == $cyber_schiff['id'] && $volk == $cyber_schiff['rasse']) 
			|| ($klassen_id == $terra_warm['id'] && $volk == $terra_warm['rasse']) 
			|| ($klassen_id == $terra_kalt['id'] && $volk == $terra_kalt['rasse']) 
			|| schiffe_basis::istViralesSchiff($klassen_id, $volk)) 
				continue;
			$techlevel = $schiff_array[2];
			$masse = $schiff_array[16];
			$waffen = $schiff_array[9] + $schiff_array[10] + $schiff_array[11];
			if($max_rumpf >= $techlevel && eigenschaften::$jaeger_infos->min_jaeger_waffen <= $waffen 
			&& ($meiste_waffen < $waffen || ($meiste_waffen == $waffen && $hoechster_techlevel < $techlevel) 
			|| ($meiste_waffen == $waffen && $hoechster_techlevel == $techlevel && $beste_masse < $masse))) {
				$meiste_waffen = $waffen;
				$jaeger_id = $klassen_id;
				$hoechster_techlevel = $techlevel;
				$beste_masse = $masse;
				$rasse = $volk;
			}
		}
		if($jaeger_id == null) return null;
		return array('id'=>$jaeger_id, 'rasse'=>$rasse);
	}
	
	/**
	 * Ueberprueft, ob beim uebergebenen Planeten ein Schiff mit Quarksreorganisator ist und versucht, falls 
	 * keins da ist, eins zu bauen. Es wird nicht ueberprueft, ob an dem Planeten eine Sternenbasis ist!
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, bei dem ein Schiff mit Quarksreorganisator 
	 * 			  gebaut werden soll.
	 * returns: true, falls eine neues Schiff mit Quarksreorganisator gebaut wurde.
	 * 			false, falls kein Schiff gebaut werden konnte oder schon eines am Planeten vorhanden ist.
	 */
	static function baueQuarkSchiff($planeten_id) {
		$rumpf_stufe = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_huelle");
		$quark_schiff_info = schiffe_basis::ermittleQuarkSchiffID();
		if($quark_schiff_info == null) return false;
		$quark_schiff_id = $quark_schiff_info['id'];
		$rasse = $quark_schiff_info['rasse'];
		$tech_level = schiffe_basis::ermittleSchiffInfo($quark_schiff_id, 2);
		if($tech_level > $rumpf_stufe) return false;
		if(schiffe_basis::ermittleSchiffanPlaneten($planeten_id, $quark_schiff_id)) return false;
		return self::baueSchiff($planeten_id, $quark_schiff_id, $rasse, true, true);
	}
	
	/**
	 * Ueberprueft, ob beim uebergebenen Planeten ein Schiff mit Subpartikelcluster ist und versucht, falls 
	 * keins da ist, eins zu bauen. Es wird nicht ueberprueft, ob an dem Planeten eine Sternenbasis ist!
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, bei dem ein Schiff mit Subpartikelcluster 
	 * 			  gebaut werden soll.
	 * returns: true, falls eine neues Schiff mit Subpartikelcluster gebaut wurde.
	 * 			false, falls kein Schiff gebaut werden konnte oder schon eines am Planeten vorhanden ist.
	 */
	static function baueClusterSchiff($planeten_id) {
		$rumpf_stufe = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_huelle");
		$cluster_schiff_info = schiffe_basis::ermittleClusterSchiffID();
		if($cluster_schiff_info == null) return false;
		$cluster_schiff_id = $cluster_schiff_info['id'];
		$rasse = $cluster_schiff_info['rasse'];
		$tech_level = schiffe_basis::ermittleSchiffInfo($cluster_schiff_id, 2);
		if($tech_level > $rumpf_stufe) return false;
		if(schiffe_basis::ermittleSchiffanPlaneten($planeten_id, $cluster_schiff_id)) return false;
		return self::baueSchiff($planeten_id, $cluster_schiff_id, $rasse, true, true);
	}
	
	/**
	 * Ueberprueft, ob beim uebergebenen Planeten ein Schiff mit Cybernrittnikk ist und versucht, falls keins
	 * da ist, eins zu bauen. Es wird nicht ueberprueft, ob an dem Planeten eine Sternenbasis ist!
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, bei dem ein Schiff mit Cybernrittnikk gebaut
	 * 			  werden soll.
	 * returns: true, falls eine neues Schiff mit Cybernrittnikk gebaut wurde.
	 * 			false, falls kein Schiff gebaut werden konnte oder schon eines am Planeten vorhanden ist.
	 */
	static function baueCyberSchiff($planeten_id) {
		$rumpf_stufe = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_huelle");
		$cyber_schiff_info = schiffe_basis::ermittleCyberSchiffID();
		if($cyber_schiff_info == null) return false;
		$cyber_schiff_id = $cyber_schiff_info['id'];
		$rasse = $cyber_schiff_info['rasse'];
		$tech_level = schiffe_basis::ermittleSchiffInfo($cyber_schiff_id, 2);
		if($tech_level > $rumpf_stufe) return false;
		if(schiffe_basis::ermittleSchiffanPlaneten($planeten_id, $cyber_schiff_id)) return false;
		return self::baueSchiff($planeten_id, $cyber_schiff_id, $rasse, true, true);
	}
	
	/**
	 * Ermittelt das beste am Planeten verfuegbare Schiff mit Strukturtaster.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten mit Sternenbasis, wo das beste Taster-Schiff 
	 * 			  ermittelt werden soll.
	 * returns: Die Klassen-ID und die Rasse des besten Taster-Schiffes.
	 * 			null, falls kein Taster-Schiff verfuegbar ist.
	 */
	static function ermittleBestesTasterSchiff($planeten_id) {
		$taster_id = null;
		$rasse = null;
		$max_rumpf = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_huelle");
		$bester_techlevel = 0;
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$tech_level_schiff = $schiff_array[2];
			$spezial_string = $schiff_array[17];
			$substring = substr($spezial_string, 52, 1);
			if($substring == 1 && $max_rumpf >= $tech_level_schiff && $tech_level_schiff > $bester_techlevel) {
				$bester_techlevel = $tech_level_schiff;
				$taster_id = $schiff_array[1];
				$rasse_index = count($schiff_array) - 1;
				$rasse = $schiff_array[$rasse_index];
			}
		}
		return array('id'=>$taster_id, 'rasse'=>$rasse);
	}
	
	/**
	 * Baut am uebergebenen Planeten ein Schiff mit Strukturtaster (falls ein solches Schiff am Planeten 
	 * verfuegbar ist).
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, wo eine Taster-Schiff gebaut werden soll.
	 * returns: true, falls ein Taster-Schiff gebaut wird.
	 * 			false, sonst.
	 */
	static function baueTasterSchiff($planeten_id) {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$taster_Schiffe = @mysql_query("SELECT s.id FROM skrupel_schiffe s, 
				skrupel_ki_spezialschiffe k WHERE (s.id=k.schiff_id) AND (s.spiel='$spiel_id') 
				AND (k.spezial_mission=2) AND (s.besitzer='$comp_id')");
		$taster_anzahl = 0;
		while($taster_infos = @mysql_fetch_array($taster_Schiffe)) {
			$taster_anzahl++;
			if($taster_anzahl / count(eigenschaften::$schiff_ids) * 100 
			>= eigenschaften::$spezialschiffe_infos->max_taster_prozent 
			&& count(eigenschaften::$schiff_ids) > 7) return false;
		}
		$aktive_taster_Schiffe = @mysql_query("SELECT s.id FROM skrupel_schiffe s, 
				skrupel_ki_spezialschiffe k WHERE (s.id=k.schiff_id) AND (s.spiel='$spiel_id') 
				AND (k.spezial_mission=2) AND (sp.aktiv=1) AND (s.besitzer='$comp_id')");
		$aktive_schiffe_anzahl = 0;
		while($taster_infos = @mysql_fetch_array($aktive_taster_Schiffe)) {
			$aktive_schiffe_anzahl++;
			if($aktive_schiffe_anzahl >= eigenschaften::$spezialschiffe_infos->max_aktive_taster_anzahl) 
				return false;
		}
		$taster_info = self::ermittleBestesTasterSchiff($planeten_id);
		if($taster_info == null) return false;
		$taster_id = $taster_info['id'];
		$rasse = $taster_info['rasse'];
		return self::baueSchiff($planeten_id, $taster_id, $rasse, true, false);
	}
	
	/**
	 * Baut am uebergebenen Planeten einen Frachter. Zuerst wird ermittelt, ob ein neuer Frachter sinnvoll ist.
	 * In dieser Implementierung ist dies der Fall, wenn die Kolonien des KI-Spielers im Verhaeltnis zur Anzahl
	 * der Frachter gross genug ist. Danach wird der beste Frachter ermittelt, der am Planeten gebaut werden 
	 * kann. Es wird nicht ueberprueft, ob an dem Planeten eine Sternenbasis ist!
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, an dem ein Frachter gebaut werden soll.
	 * returns: true, falls ein Frachter gebaut wurde.
	 * 			false, falls keiner gebaut wurde.
	 */
	static function baueFrachter($planeten_id) {
		$kolo_anzahl = count(eigenschaften::$kolonien_ids);
		$frachter_anzahl = count(eigenschaften::$frachter_ids);
		$kolonie_schiffe = frachter_basis::ermittleAndereKolonieSchiffe(0);
		if($kolo_anzahl / eigenschaften::$frachter_kolo_infos->kolos_pro_frachter > $frachter_anzahl 
		|| (count($kolonie_schiffe) == 0 && frachter_leicht::mehrKolonien())) {
			$frachter_info = self::ermittleBestenFrachter($planeten_id);
			if($frachter_info == null) return false;
			$frachter_id = $frachter_info['id'];
			$rasse = $frachter_info['rasse'];
			return self::baueSchiff($planeten_id, $frachter_id, $rasse, true, false);
		}
		return false;
	}
	
	/**
	 * Baut am uebergebenen Planeten einen Scout. Zuerst wird ueberprueft, ob es nicht schon genug Scouts gibt. 
	 * Falls nicht, wird der beste Scout ermittelt, der am uebergebenen Planeten gebaut werden kann.
	 * Anschliessend wird das Schiff in Auftrag gegeben.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, an dem ein Scout gebaut werden soll.
	 * returns: true, falls ein Scout gebaut wurde.
	 * 			false, falls keiner gebaut wurde.
	 */
	static function baueScout($planeten_id) {
		$scout_anzahl = count(eigenschaften::$scout_ids);
		if(count(eigenschaften::$schiff_ids) == 0) return false;
		$scout_prozent = $scout_anzahl / count(eigenschaften::$schiff_ids) * 100;
		if($scout_anzahl >= eigenschaften::$scouts_infos->max_scout_anzahl 
		|| $scout_prozent >= eigenschaften::$scouts_infos->max_scout_prozent 
		|| (eigenschaften::$tick >= eigenschaften::$basen_ausbau_infos->kein_waffenausbau_limit 
		&& $scout_anzahl >= count(eigenschaften::$jaeger_ids))) return false;
		$scout_info = self::ermittleBestenScout($planeten_id);
		if($scout_info == null) return false;
		$scout_id = $scout_info['id'];
		$rasse = $scout_info['rasse'];
		return self::baueSchiff($planeten_id, $scout_id, $rasse, false, false); 
	}
	
	/**
	 * Baut am uebergebenen Planeten einen Jaeger. Zuerst wird der beste Jaeger ermittelt, der am Planeten
	 * gebaut werden kann. Es wird nicht ueberprueft, ob an dem Planeten eine Sternenbasis ist!
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, an dem ein Jaeger gebaut werden soll.
	 * returns: true, falls ein Jaeger gebaut wurde.
	 * 			false, falls keiner gebaut wurde.
	 */
	static function baueJaeger($planeten_id) {
		if(eigenschaften::$basen_ausbau_infos->kein_waffenausbau_limit > eigenschaften::$tick 
		|| self::PlanetSpartRumpf($planeten_id)) return false;
		$jaeger_info = self::ermittleBestenJaeger($planeten_id);
		if($jaeger_info == null) return false;
		$jaeger_id = $jaeger_info['id'];
		$rasse = $jaeger_info['rasse'];
		return self::baueSchiff($planeten_id, $jaeger_id, $rasse, false, false); 
	}
	
	/**
	 * Ermittelt alle Kolonien, die waermer/kaelter sind als die optimale Temperatur der Rasse und 
	 * gibt diese als Array zurueck.
	 * arguments: $warm - Bei $warm == true werden alle waermeren Kolonien zurueckgegeben,
	 * 					  sonst alle kaelteren.
	 * returns: Alle Planeten-IDs der KI, die waermer/kaelter sind als die optimale Temperatur.
	 */
	static function ermittleTerraformerKolonien($warm) {
		$planeten_ids = array();
		$optimale_temp = ki_basis::ermittleOptimaleTemp();
		if($optimale_temp == 0) return $planeten_ids;
		foreach(eigenschaften::$kolonien_ids as $kolo_id) {
			$temp = @mysql_query("SELECT temp FROM skrupel_planeten WHERE id='$kolo_id'");
			$temp = @mysql_fetch_array($temp);
			$temp = $temp['temp'] - 35;
			$bedingung = $optimale_temp > $temp;
			if($warm) $bedingung = $optimale_temp < $temp;
			if($bedingung) $planeten_ids[] = $kolo_id;
		}
		return $planeten_ids;
	}
	
	/**
	 * Baut am uebergebenen Planeten ein Schiff mit warmen Terraformer. Sind bereits genug warme Terraformer
	 * Schiffe vorhanden, wird keins gebaut. Es wird nicht ueberprueft, ob an dem Planeten eine Sternenbasis 
	 * ist!
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, an dem ein warmer Terraformer gebaut 
	 * 			  werden soll.
	 * returns: true, falls ein warmer Terraformer gebaut wurde.
	 * 			false, falls keiner gebaut wurde.
	 */
	static function baueTerraWarm($planeten_id) {
		if(count(eigenschaften::$terra_warm_ids) >= eigenschaften::$spezialschiffe_infos->max_terra_warm 
		|| count(eigenschaften::$kolonien_ids) / 
		eigenschaften::$spezialschiffe_infos->min_planeten_pro_terra_warm 
		<= count(eigenschaften::$terra_warm_ids)) return false;
		$anzahl_warmer_planeten = count(self::ermittleTerraformerKolonien(true));
		if($anzahl_warmer_planeten == 0) return false;
		$terraWarm_info = schiffe_basis::ermittleTerraWarmID();
		if($terraWarm_info == null) return false;
		$terraWarm = $terraWarm_info['id'];
		$rasse = $terraWarm_info['rasse'];
		return self::baueSchiff($planeten_id, $terraWarm, $rasse, true, false);
	}
	
	/**
	 * Baut am uebergebenen Planeten ein Schiff mit kalten Terraformer. Sind bereits genug kalte Terraformer
	 * Schiffe vorhanden, wird keins gebaut. Es wird nicht ueberprueft, ob an dem Planeten eine Sternenbasis 
	 * ist!
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, an dem ein kalter Terraformer gebaut 
	 * 			  werden soll.
	 * returns: true, falls ein kalter Terraformer gebaut wurde.
	 * 			false, falls keiner gebaut wurde.
	 */
	static function baueTerraKalt($planeten_id) {
		if(count(eigenschaften::$terra_kalt_ids) == eigenschaften::$spezialschiffe_infos->max_terra_kalt 
		|| count(eigenschaften::$kolonien_ids) / 
		eigenschaften::$spezialschiffe_infos->min_planeten_pro_terra_kalt 
		<= count(eigenschaften::$terra_kalt_ids)) return false;
		$anzahl_kalter_planeten = count(self::ermittleTerraformerKolonien(false));
		if($anzahl_kalter_planeten == 0) return false;
		$terraKalt_info = schiffe_basis::ermittleTerraKaltID();
		if($terraKalt_info == null) return false;
		$terraKalt = $terraKalt_info['id'];
		$rasse = $terraKalt_info['rasse'];
		return self::baueSchiff($planeten_id, $terraKalt, $rasse, true, false);
	}
	
	/**
	 * Ermittelt das beste Schiff mit viraler Invasion, was an der Sternenbasis des uebergebenen Planeten 
	 * verfuegbar ist.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, wo geprueft werden soll, ob dort ein Schiff
	 * 							 mit viraler Invasion gebaut werden kann.
	 * returns: Die Klassen-ID und die Rasse des besten Schiffs mit viraler Invasion.
	 * 			null, falls es kein Schiff mit virale Invasion gibt.
	 */
	static function ermittleBestesViraleInvasionSchiff($planeten_id) {
		$rumpf_stufe = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_huelle");
		$virale_id = null;
		$rasse = null;
		$bester_tech_level = 0;
		foreach(eigenschaften::$schiff_arrays as $schiff_array) {
			$spezial_string = $schiff_array[17];
			$tech_level = $schiff_array[2];
			$sub_string = substr($spezial_string, 41, 5);
			if($sub_string+0 != 0 && $tech_level <= $rumpf_stufe && $tech_level > $bester_tech_level) {
				$bester_tech_level = $tech_level;
				$virale_id = $schiff_array[1];
				$rasse_index = count($schiff_array) - 1;
				$rasse = $schiff_array[$rasse_index];
			}
		}
		return array('id'=>$virale_id, 'rasse'=>$rasse);
	}
	
	/**
	 * Baut am uebergebenen Planeten ein Schiff mit viraler Invasion. Es wird nur ein solches Schiff 
	 * gebaut, wenn es noch nicht genug gibt und wenn es ueberhaupt Planeten gibt, die von einer 
	 * schlechten Dominanten Spezies befreit werden sollen.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, an dem ein Schiff mit viraler 
	 * 							 Invasion gebaut werden soll.
	 * returns: true, falls ein Schiff mit viraler Invasion gebaut wurde.
	 * 			false, falls keines gebaut wurde.
	 */
	static function baueViraleInvasionSchiff($planeten_id) {
		$ziel_planeten = count(schiffe_basis::ermittlePlanetenFuerViraleInvasionSchiffe());
		if(count(eigenschaften::$virale_ids) >= eigenschaften::$spezialschiffe_infos->max_viral_anzahl 
		|| $ziel_planeten == 0 || $ziel_planeten <= count(eigenschaften::$virale_ids)) 
			return false;
		$virale_info = self::ermittleBestesViraleInvasionSchiff($planeten_id);
		if($virale_info == null) return false;
		$virale_id = $virale_info['id'];
		$rasse = $virale_info['rasse'];
		return self::baueSchiff($planeten_id, $virale_id, $rasse, false, false);
	}
	
	/**
	 * Prueft, ob die Sternenbasis des uebergebenen Planeten auf den Ausbau fuer die naechste Stufe der 
	 * Ruempfe sparen sollte.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten mit Sternenbasis.
	 * returns: true, falls der Planet auf die naechste Rumpf-Stufe sparen sollte.
	 * 			false, sonst.
	 */
	static function PlanetSpartRumpf($planeten_id) {
		$rumpf_stufe = sternenbasen_basis::ermittleTechLevel($planeten_id, "t_huelle");
		if($rumpf_stufe < eigenschaften::$basen_ausbau_infos->rumpf_sparen_min_stufe) return false;
		$cluster_id = schiffe_basis::ermittleClusterSchiffID();
		$cluster_schiff_vorhanden = schiffe_basis::ermittleSchiffanPlaneten($planeten_id, $cluster_id['id']);
		if($rumpf_stufe == 10) return !$cluster_schiff_vorhanden;
		$jaeger_anzahl = count(eigenschaften::$jaeger_ids);
		$schon_gesparte_stufen = $rumpf_stufe - eigenschaften::$basen_ausbau_infos->rumpf_sparen_min_stufe;
		$zusatz_jaeger = $schon_gesparte_stufen * eigenschaften::$basen_ausbau_infos->rumpf_sparen_zusatz_jaeger;
		$min_jaeger = eigenschaften::$basen_ausbau_infos->rumpf_sparen_min_jaeger + $zusatz_jaeger;
		return ($min_jaeger <= $jaeger_anzahl);
	}
	
	/**
	 * Baut das naechste Schiff an der Sternenbasis des uebergebenen Planeten. Zu erst wird versucht, ein 
	 * Schiff mit  Quarksreorganisator zu bauen. Dann eins mit Subpartikelcluster, dann ein Frachter und falls 
	 * das Spiel weit genug fortgeschritten ist, auch ein Jaeger.
	 * Es wird nicht ueberprueft, ob an dem Planeten eine Sternenbasis ist!
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, an dessen Sternenbasis ein Schiff gebaut 
	 * 							 werden soll.
	 */
	static function baueNeuesSchiff($planeten_id) {
		if(self::baueQuarkSchiff($planeten_id)) return;
		if(self::baueClusterSchiff($planeten_id)) return;
		if(self::baueCyberSchiff($planeten_id)) return;
		if(self::baueFrachter($planeten_id)) return;
		if(self::baueTasterSchiff($planeten_id)) return;
		if(self::baueTerraWarm($planeten_id)) return;
		if(self::baueTerraKalt($planeten_id)) return;
		if(self::baueViraleInvasionSchiff($planeten_id)) return;
		if(self::baueScout($planeten_id)) return;
		self::baueJaeger($planeten_id);
	}
}
?>