<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Implementierung des Verhaltes der KI bei Scouts.
 */
class scouts_leicht extends scouts_basis {
	
	/**
	 * Setzt einen Kurs fuer den uebergebene Scout. Zuerst werden sichtbare feindliche Schiffe 
	 * angesteuert. Sind keine Feind-Schiffe in Sicht, so wird ein Erkundungskurs gewaehlt.
	 * arguments: $scout_id - Die Datenbank-ID des Scouts, das einen Kurs bekommen soll.
	 */
	function fliegeScout($scout_id) {
		$warp = $this->ermittleMaximumWarp($scout_id);
		$schiff_pos = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$scout_id'");
		$schiff_pos = @mysql_fetch_array($schiff_pos);
		$x_start = $schiff_pos['kox'];
		$y_start = $schiff_pos['koy'];
		$gegner_ziel = null;
		//Zuerst werden alle anderen sichtbaren Feindschiffe ueberprueft.
		if(count(eigenschaften::$sichtbare_gegner_schiffe) > 0) {
			$gegner_ziel = ki_basis::ermittleNahesZiel($scout_id, eigenschaften::$sichtbare_gegner_schiffe, 
							eigenschaften::$bekannte_wurmloch_daten);
		}
		if($gegner_ziel != null && $gegner_ziel['id'] != null && $gegner_ziel['id'] != 0) {
			$this->fliegeSchiff($scout_id, $gegner_ziel['x'], $gegner_ziel['y'], $warp, $gegner_ziel['id']);
			return;
		}
		$gegner_ziel = null;
		//Da keine passenden Ziele gefunden wurden, wird nun ein Erkundungsziel gesetzt.
		$gegner_ziel = $this->erkunde($scout_id);
		$this->fliegeSchiff($scout_id, $gegner_ziel['x'], $gegner_ziel['y'], $warp, $gegner_ziel['id']);
	}
	
	/**
	 * Verwaltet alle Scouts.
	 */
	function verwalteScouts() {
		$spiel_id = eigenschaften::$spiel_id;
		foreach(eigenschaften::$scout_ids as $scout_id) {
			$taster_infos = @mysql_query("SELECT kox, koy, lemin, status FROM skrupel_schiffe 
				WHERE id='$scout_id'");
			$this->setzeAggressivitaet($scout_id, 9);
			$this->setzeTaktik($scout_id, 1);
			$taster_infos = @mysql_fetch_array($taster_infos);
			$warp = $this->ermittleMaximumWarp($scout_id);
			$x_pos = $taster_infos['kox'];
			$y_pos = $taster_infos['koy'];
			$schiff_lemin = $taster_infos['lemin'];
			$schiff_status = $taster_infos['status'];
			if($this->SchiffHatFertigkeit($scout_id, "sensorenphalanx")) 
				@mysql_query("UPDATE skrupel_schiffe SET spezialmission=11 WHERE id='$scout_id'");
			elseif($this->SchiffHatFertigkeit($scout_id, "labor")) 
				@mysql_query("UPDATE skrupel_schiffe SET spezialmission=12 WHERE id='$scout_id'");
			elseif($this->SchiffHatFertigkeit($scout_id, "tarnung")) $this->aktiviereTarnung($scout_id);
			$this->scanneUmgebung($scout_id);
			if($this->reagiereAufWurmloch($scout_id)) continue;
			if($this->mussObjektUmfliegen($scout_id)) continue;
			if($schiff_status == 2) {
				$planeten_infos = @mysql_query("SELECT id, besitzer, sternenbasis FROM skrupel_planeten 
						WHERE (spiel='$spiel_id') AND (x_pos='$x_pos') AND (y_pos='$y_pos')");
				$planeten_infos = @mysql_fetch_array($planeten_infos);
				$planeten_id = $planeten_infos['id'];
				$planeten_besitzer = $planeten_infos['besitzer'];
				$sternenbasis = $planeten_infos['sternenbasis'];
				if($planeten_besitzer == eigenschaften::$comp_id) 
					$this->leereFrachtRaum($scout_id, $planeten_id);
				$this->fliegeScout($scout_id);
				if($schiff_lemin == 0 && $sternenbasis == 2) $this->tankeLeminStart($scout_id, $planeten_id);
				else $this->tankeLemin($scout_id, $planeten_id);
				continue;
			}
			$strecken_lemin = $this->ermittleStreckenVerbrauch($scout_id);
			if($schiff_lemin < (eigenschaften::$jaeger_infos->min_jaeger_lemin_prozent * $strecken_lemin / 100) 
			&& $schiff_status != 2) {
				$this->fliegeTanken($scout_id);
				continue;
			}
			//Hat das Schiff aus welch Gruenden auch immer kein Ziel, so wird eins bestimmt.
			$flug_daten = @mysql_query("SELECT zielx, ziely, kox, koy FROM skrupel_schiffe WHERE id='$scout_id'");
			$flug_daten = @mysql_fetch_array($flug_daten);
			if(($flug_daten['zielx'] == 0 && $flug_daten['ziely'] == 0) 
			|| ($flug_daten['zielx'] == $flug_daten['kox'] && $flug_daten['ziely'] == $flug_daten['koy'])) 
				$this->fliegeScout($scout_id);
		}
	}
}
?>