<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Erweitert die Implementierung von planeten_leicht.
 */
class planeten_mittel extends planeten_leicht {
	
	/**
	 * Verwaltet die orbitalen System des uebergebenen Planeten. Zuerst wird versucht, eine Megafabrik zu 
	 * bauen, danach eine Metropole, danach ein Vergnuegungspark. Falls eine nuetzliche dominante Spezies 
	 * auf dem Planeten ist und genug von ihnen da sind, wird ein Reservat gebaut.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, dessen orbitale System verwaltet werden.
	 */
	function verwalteOrbitaleSysteme($planeten_id) {
		$weiter = false;
		if(!($this->hatOrbitalesSystem("megafabrik", $planeten_id))) 
			if($this->baueOrbitalesSystem("megafabrik", $planeten_id)) $weiter = true;
		if(!($this->hatOrbitalesSystem("metropole", $planeten_id)) && $weiter) {
			$weiter = false;
			if($this->baueOrbitalesSystem("metropole", $planeten_id)) $weiter = true;
		}
		if(!($this->hatOrbitalesSystem("vergnuegungspark", $planeten_id)) && $weiter) {
			$weiter = false;
			if($this->baueOrbitalesSystem("vergnuegungspark", $planeten_id)) $weiter = true;
		}
		if(!($this->hatOrbitalesSystem("reservat", $planeten_id)) && $weiter) {
			$dom_spezies = ki_basis::ermittleDomSpezies($planeten_id);
			if($dom_spezies == null) return;
			if($dom_spezies['anzahl'] >= eigenschaften::$planeten_infos->min_dom_spezies_reservat 
			&& ($dom_spezies['id'] == 35 || $dom_spezies['id'] == 22 || $dom_spezies['id'] == 4 
			|| $dom_spezies['id'] == 25 || $dom_spezies['id'] == 6 || $dom_spezies['id'] == 27 
			|| $dom_spezies['id'] == 10 || $dom_spezies['id'] == 15 || $dom_spezies['id'] == 35)) 
				$this->baueOrbitalesSystem("reservat", $planeten_id);
		}
	}
}
?>
