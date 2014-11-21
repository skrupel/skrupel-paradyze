<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Impementierung der Klasse planeten_basis.
 */
class planeten_leicht extends planeten_basis {
	
	/**
	 * Verwaltet die orbitalen System des uebergebenen Planeten. Es wird erst versucht, eine Megafabrik zu 
	 * bauen. Falls schon eine existiert, wird versucht, eine Bank zu bauen. Weitere orbitale System werden 
	 * nicht versucht zu bauen.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, dessen orbitale System verwaltet werden.
	 */
	function verwalteOrbitaleSysteme($planeten_id) {
		$weiter = false;
		if(!($this->hatOrbitalesSystem("megafabrik", $planeten_id))) 
			if($this->baueOrbitalesSystem("megafabrik", $planeten_id)) $weiter = true;
		if(!($this->hatOrbitalesSystem("bank", $planeten_id)) && $weiter) 
			$this->baueOrbitalesSystem("bank", $planeten_id);
	}
	
	/**
	 * Verwaltet alle Planeten im Bezug auf den Bau von Minen, Fabriken usw.
	 * Diese Implementierung baut in den ersten Runden etwa gleich viele Fabriken wie Minen. Spaeter 
	 * wird der Minen- und Fabrikenbau automatisiert. Ist ein Minene-Limit gesetzt, so werden Minen nur bis 
	 * zu diesem Limit automatisch gebaut.
	 */
	function verwaltePlaneten() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$planeten_ids = @mysql_query("SELECT id FROM skrupel_planeten WHERE (besitzer='$comp_id') 
			AND (spiel='$spiel_id')");
		while($planeten_id = @mysql_fetch_array($planeten_ids)) {
			$planeten_id = $planeten_id['id'];
			$planeten_daten = @mysql_query("SELECT fabriken, minen, cantox, vorrat, kolonisten 
				FROM skrupel_planeten WHERE id='$planeten_id'");
			$planeten_daten = @mysql_fetch_array($planeten_daten);
			$cantox = $planeten_daten['cantox'];
			$vorrat = $planeten_daten['vorrat'];
			$this->verwalteOrbitaleSysteme($planeten_id);
			//In den ersten Runden ist ein automatischer Bau von Fabriken und Minen nicht sinnvoll, da das erste
			//Kolonie-Schiff los geschickt werden sollte und bei automatischem Bau alle Anfangs-Vorrate weg sind.
			if(eigenschaften::$tick <= eigenschaften::$planeten_infos->kein_auto_bau_limit) {
				$cantox_alt = $cantox;
				$vorrat_alt = $vorrat;
				$fabriken = $planeten_daten['fabriken'];
				$minen = $planeten_daten['minen'];
				$kolonisten = $planeten_daten['kolonisten'];
				$max_fabriken = $kolonisten / 100;
				if($kolonisten >= 20000) $max_fabriken = 100 + sqrt($kolonisten / 100);
				while($vorrat >= ($vorrat_alt / 2) && $cantox >= ($cantox_alt / 2) && $fabriken < $max_fabriken) {
					$cantox -= 3;
					$vorrat--;
					$fabriken++;
				}
				$max_minen = $kolonisten / 100;
				if($kolonisten >= 20000) $max_minen = 200 + sqrt($kolonisten / 100);
				while($vorrat >= 1 && $cantox >= 4 && $minen < $max_minen) {
					$cantox -= 4;
					$vorrat--;
					$minen++;
				}
				@mysql_query("UPDATE skrupel_planeten SET fabriken='$fabriken', minen='$minen', cantox='$cantox', 
					vorrat='$vorrat' WHERE id='$planeten_id'");
				continue;
			}
			@mysql_query("UPDATE skrupel_planeten SET auto_fabriken=1 WHERE id='$planeten_id'");
			if(count(eigenschaften::$gegner_planeten_daten) > 0 
			|| count(eigenschaften::$sichtbare_gegner_schiffe) > 0 
			|| count(eigenschaften::$gegner_angriffe) > 0) 
				@mysql_query("UPDATE skrupel_planeten SET auto_abwehr=1 WHERE id='$planeten_id'");
			$minen = @mysql_query("SELECT minen FROM skrupel_planeten WHERE id='$planeten_id'");
			$minen = @mysql_fetch_array($minen);
			$minen = $minen['minen'];
			$minen_limit = eigenschaften::$planeten_infos->minen_limit;
			if(in_array($planeten_id, eigenschaften::$basen_planeten_ids) && 
			count(eigenschaften::$basen_planeten_ids) == 1) 
				$minen_limit = eigenschaften::$planeten_infos->minen_limit_start_planet;
			if($minen_limit == 0 || $minen < $minen_limit) 
				@mysql_query("UPDATE skrupel_planeten SET auto_minen=1 WHERE id='$planeten_id'");
			else @mysql_query("UPDATE skrupel_planeten SET auto_minen=0 WHERE id='$planeten_id'");
		}
	}
}
?>
