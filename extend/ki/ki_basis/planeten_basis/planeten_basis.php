<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Abstrakte Oberklasse fuer das Verhalten der KI bei ihren Planeten.
 */
abstract class planeten_basis {
	
	/**
	 * Diese Funktion muss implementiert werden. Hier wird das Verhalten der KI bezueglich 
	 * ihrer Planeten spezifiziert.
	 */
	abstract function verwaltePlaneten();
	
	/**
	 * Muss implementiert werden, um das Verwalten der orbitalen Systemen auf den Planeten der KI 
	 * zu bestimmen.
	 * arguments: $planeten_id - Die Datenbank-ID des Planeten, dessen orbitale System verwaltet werden.
	 */
	abstract function verwalteOrbitaleSysteme($planeten_id);
	
	/**
	 * Prueft, ob am uebergebenen Planeten das uebergebene orbitale System existiert.
	 * arguments: $osystem - Der Name des orbitalen Systems, das ueberprueft werden soll.
	 * 			  $planeten_id - Die Datenbank-ID des zu ueberpruefenden Planeten.
	 * returns: true, falls am Planeten das orbitale System vorhanden ist.
	 * 			false, sonst.
	 */
	function hatOrbitalesSystem($osystem, $planeten_id) {
		$planeten_daten = @mysql_query("SELECT osys_anzahl, osys_1, osys_2, osys_3, osys_4, osys_5, osys_6 
			FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_daten = @mysql_fetch_array($planeten_daten);
		$osys_anzahl = $planeten_daten['osys_anzahl'];
		$system = null;
		switch($osystem) {
			case "megafabrik": { $system = 1; break; }
			case "exo-raffinerie": { $system = 2; break; }
			case "bank": { $system = 3; break; }
			case "vergnuegungspark": { $system = 6; break; }
			case "metropole": { $system = 9; break; }
			case "reservat": { $system = 23; break; }
		}
		for($i=1; $i<=$osys_anzahl; $i++) {
			$osys = $planeten_daten['osys_'.$i];
			if($osys == $system) return true;
		}
		return false;
	}
	
	/**
	 * Versucht auf dem uebergebenen Planeten das uebergebene orbitale System zu bauen. Sind auf dem Planeten 
	 * keine freien Slots oder nicht genug Resourcen fuer das orbitale System vorhanden, wird keins gebaut.
	 * arguments: $orb_system - Der Name des zu bauenden orbitalen Systems.
	 * 			  $planeten_id - Die Datenbank-ID des Planeten, wo das orbitale System gebaut werden soll.
	 * returns: true, falls das orbitale System gebaut wurde und dessen Resourcen vom Planeten abgezogen wurden.
	 * 			false, sonst.
	 */
	function baueOrbitalesSystem($orb_system, $planeten_id) {
		$planeten_daten = @mysql_query("SELECT cantox, vorrat, lemin, min1, min2, min3, osys_anzahl, osys_1, 
			osys_2, osys_3, osys_4, osys_5, osys_6 FROM skrupel_planeten WHERE id='$planeten_id'");
		$planeten_daten = @mysql_fetch_array($planeten_daten);
		$osys_anzahl = $planeten_daten['osys_anzahl'];
		$osys_slot = null;
		for($i=1; $i<=$osys_anzahl; $i++) {
			$osys = $planeten_daten['osys_'.$i];
			if($osys == 0) {
				$osys_slot = "osys_".$i;
				break;
			}
		}
		if($osys_slot == null) return false;
		$cantox = $planeten_daten['cantox'];
		$vorrat = $planeten_daten['vorrat'];
		$lemin = $planeten_daten['lemin'];
		$min1 = $planeten_daten['min1'];
		$min2 = $planeten_daten['min2'];
		$min3 = $planeten_daten['min3'];
		switch($orb_system) {
			case "megafabrik": {
				if($cantox >= 120 && $vorrat >= 17 && $min1 >= 20 && $min2 >= 17 && $min3 >= 13) {
					$cantox -= 120; $vorrat -= 17; $min1 -= 20; $min2 -= 17; $min3 -= 13;
					@mysql_query("UPDATE skrupel_planeten SET $osys_slot=1 WHERE id='$planeten_id'");
				} else return false;
				break;
			}
			case "exo-raffinerie": {
				if($cantox >= 85 && $vorrat >= 32 && $lemin >= 28 && $min1 >= 35 && $min2 >= 12 && $min3 >= 8) {
					$cantox -= 85; $vorrat -= 32; $lemin -= 28; $min1 -= 35; $min2 -= 12; $min3 -= 8;
					@mysql_query("UPDATE skrupel_planeten SET $osys_slot=2 WHERE id='$planeten_id'");
				} else return false;
				break;
			}
			case "bank": {
				if($cantox >= 250 && $vorrat >= 10 && $lemin >= 10 && $min1 >= 36 && $min2 >= 12 && $min3 >= 12) {
					$cantox -= 250; $vorrat -= 10; $lemin -= 10; $min1 -= 36; $min2 -= 12; $min3 -= 12;
					@mysql_query("UPDATE skrupel_planeten SET $osys_slot=3 WHERE id='$planeten_id'");
				} else return false;
				break;
			}
			case "vergnuegungspark": {
				if($cantox >= 250 && $vorrat >= 500 && $lemin >= 13 && $min1 >= 95 && $min2 >= 100 
				&& $min3 >= 73) {
					$cantox -= 250; $vorrat -= 500; $lemin -= 13; $min1 -= 95; $min2 -= 100; $min3 -= 73;
					@mysql_query("UPDATE skrupel_planeten SET $osys_slot=6 WHERE id='$planeten_id'");
				} else return false;
				break;
			}
			case "metropole": {
				if($cantox >= 200 && $vorrat >= 25 && $lemin >= 50 && $min1 >= 20 && $min2 >= 30 && $min3 >= 20) {
					$cantox -= 200; $vorrat -= 25; $lemin -= 50; $min1 -= 20; $min2 -= 30; $min3 -= 20;
					@mysql_query("UPDATE skrupel_planeten SET $osys_slot=9 WHERE id='$planeten_id'");
				} else return false;
				break;
			}
			case "reservat" : {
				if($cantox >= 50 && $vorrat >= 75 && $lemin >= 7 && $min1 >= 19 && $min2 >= 26 && $min3 >= 14) {
					$cantox -= 50; $vorrat -= 75; $lemin -= 7; $min1 -= 19; $min2 -= 26; $min3 -= 14;
					@mysql_query("UPDATE skrupel_planeten SET $osys_slot=23 WHERE id='$planeten_id'");
				} else return false;
				break;
			}
		}
		@mysql_query("UPDATE skrupel_planeten SET cantox='$cantox', vorrat='$vorrat', lemin='$lemin', 
			min1='$min1', min2='$min2', min3='$min3' WHERE id='$planeten_id'");
		return true;
	}
}
?>
