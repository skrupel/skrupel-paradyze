<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Implementierung von geleitschutz_basis.
 */
class geleitschutz_leicht extends geleitschutz_basis {
	
	/**
	 * Waehlt das staerkste Schiff aus den uebergebenen Schiffen aus und laesst alle anderen Schiffe (nur 
	 * soviele, wie sinnvoll sind und nur die, deren Techlevel Sinn machen) diesem Schiff Geleitschutz geben.
	 * Falls Schiffe uebrig bleiben, werden die restlichen Schiffe rekursiv weitergegeben.
	 * Ausserdem wird das Leit-Schiff so lange warten gelassen, bis alle anderen Schiffe an dessen Position sind.
	 * arguments: $gruppen_ids - Ein Array aus den Datenbank-IDs der Schiffe, die eine Begleiter-Gruppe bilden
	 * 							 sollen.
	 */
	function erstelleGeleitschutzGruppe($gruppen_ids) {
		if(count($gruppen_ids) < 2) return;
		$leit_schiff = $this->ermittleStaerkstesSchiff($gruppen_ids);
		$schiff_infos = @mysql_query("SELECT flug FROM skrupel_schiffe WHERE id='$leit_schiff'");
		$schiff_infos = @mysql_fetch_array($schiff_infos);
		if($schiff_infos['flug'] == 4) 
			@mysql_query("UPDATE skrupel_schiffe SET flug=0, zielid=0, zielx=0, ziely=0 WHERE id='$leit_schiff'");
		$max_waffen = $this->ermittleMaxWaffenAnzahl($leit_schiff);
		$gruppen_anzahl = count($gruppen_ids);
		$hat_ein_begleitschiff = false;
		for($i=0; $i < $gruppen_anzahl && $i < $max_waffen; $i++) {
			$schiff_id = $gruppen_ids[$i];
			if($schiff_id == $leit_schiff) {
				unset($gruppen_ids[$i]);
				continue;
			}
			if(!$this->kannBegleitschutzGeben($schiff_id, $leit_schiff)) continue;
			$this->begleite($schiff_id, $leit_schiff);
			$hat_ein_begleitschiff = true;
			$this->setzeAggressivitaet($schiff_id, 8);
			if(count($this->ermittleKommendeBegleitSchiffe($leit_schiff)) != 0) 
				@mysql_query("UPDATE skrupel_schiffe SET flug=0 WHERE id='$leit_schiff'");
			unset($gruppen_ids[$i]);
		}
		$rest_ids = array_values($gruppen_ids);
		if(count($rest_ids) >= 2 && $hat_ein_begleitschiff) $this->erstelleGeleitschutzGruppe($rest_ids);
	}
	
	/**
	 * Ermittelt alle Schiffe, die sich nahe genug sind, um eine Geleit-Gruppe zu bilden und verwaltet 
	 * diese Gruppen.
	 */
	function verwalteJaegerGeleitschutz() {
		$gleitschutz_gruppen = array();
		$jaeger_schon_drin = array();
		foreach(eigenschaften::$jaeger_ids as $jaeger_id) {
			$geleitschutz_gruppe = array($jaeger_id);
			$jaeger_schon_drin[] = $jaeger_id;
			$koords = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$jaeger_id'");
			$koords = @mysql_fetch_array($koords);
			$x1 = $koords['kox'];
			$y1 = $koords['koy'];
			foreach(eigenschaften::$jaeger_ids as $andere_jaeger_id) {
				if(in_array($andere_jaeger_id, $jaeger_schon_drin)) continue;
				$koords2 = @mysql_query("SELECT kox, koy FROM skrupel_schiffe WHERE id='$andere_jaeger_id'");
				$koords2 = @mysql_fetch_array($koords2);
				$x2 = $koords2['kox'];
				$y2 = $koords2['koy'];
				$entfernung = floor(ki_basis::berechneStrecke($x1, $y1, $x2, $y2));
				//Nur Jaege, die nahe genug sind werden in eine Geleitschutz-Gruppe aufgenommen. Ausserdem 
				//koennen nur maximal 10 Schiffe in einer Geleitschutz-Gruppe sein.
				if($entfernung <= eigenschaften::$geleitschutz_infos->max_distanz_geleitschutz 
				&& count($geleitschutz_gruppe) < 10) {
					$jaeger_schon_drin[] = $andere_jaeger_id;
					$geleitschutz_gruppe[] = $andere_jaeger_id;
				}
			}
			if(count($geleitschutz_gruppe) > 1) $gleitschutz_gruppen[] = $geleitschutz_gruppe;
		}
		foreach($gleitschutz_gruppen as $gruppe) {
			$this->erstelleGeleitschutzGruppe($gruppe);
		}
	}
	
	/**
	 * Implementierung der Verwaltung fuer Geleitschutz.
	 */
	function verwalteGeleitschutz() {
		$this->verwalteJaegerGeleitschutz();
	}
}
?>
