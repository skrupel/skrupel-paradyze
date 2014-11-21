<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Erweitert geleitschutz_leicht.
 */
class geleitschutz_mittel extends geleitschutz_leicht {
	
	/**
	 * Ermittelt das staerkste Schiff von den uebergebenen Schiff-IDs und gibt dies zurueck.Die Kriterien sind 
	 * die maximale Anzahl an Waffen sowie der Tech-Level und die Masse. Schiffe mit zu viel Schaden werden hier 
	 * nicht als stark angesehen.
	 * arguments: $schiff_ids - Ein Array aus Datenbank-IDs von Schiffen.
	 * returns: Die Datenbank-ID des staerksten Schiffs aus dem Array.
	 */
	function ermittleStaerkstesSchiff($schiff_ids) {
		$max_waffen = 0;
		$max_techlevel = 0;
		$max_masse = 0;
		$staerkstes_schiff = null;
		foreach($schiff_ids as $schiff_id) {
			$schiff_infos = @mysql_query("SELECT techlevel, masse, lemin, schaden FROM skrupel_schiffe WHERE 
				id='$schiff_id'");
			$schiff_infos = @mysql_fetch_array($schiff_infos);
			if($schiff_infos['schaden'] > eigenschaften::$jaeger_infos->max_jaeger_schaden) continue;
			$techlevel = $schiff_infos['techlevel'];
			$masse = $schiff_infos['masse'];
			$lemin = $schiff_infos['lemin'];
			if($lemin <= eigenschaften::$geleitschutz_infos->min_lemin_geleit_geben) continue;
			$waffen = $this->ermittleMaxWaffenAnzahl($schiff_id);
			if($waffen > $max_waffen || ($waffen == $max_waffen && $techlevel > $max_techlevel) 
			|| ($waffen == $max_waffen && $techlevel == $max_techlevel && $masse > $max_masse)) {
				$max_waffen = $waffen;
				$max_techlevel = $techlevel;
				$max_masse = $masse;
				$staerkstes_schiff = $schiff_id;
			}
		}
		return $staerkstes_schiff;
	}
}
?>
