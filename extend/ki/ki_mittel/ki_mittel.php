<?php
include_once("../extend/ki/ki_leicht/ki_leicht.php");
include_once("planeten_mittel/planeten_mittel.php");
include_once("politik_mittel/politik_mittel.php");
include_once("schiffe_mittel/schiffe_mittel.php");
include_once("schiffe_mittel/frachter_mittel.php");
include_once("schiffe_mittel/jaeger_mittel.php");
include_once("schiffe_mittel/scouts_mittel.php");
include_once("schiffe_mittel/geleitschutz_mittel.php");
include_once("schiffe_mittel/spezialschiffe_mittel.php");
include_once("sternenbasen_mittel/basen_schiffbau_mittel.php");
include_once("sternenbasen_mittel/sternenbasen_mittel.php");
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Implementiert eine bessere KI als ki_leicht.
 */
class ki_mittel extends ki_basis {
	
	/**
	 * Dies ist der Konstruktor von ki_mittel. Beim erstellen eines neuen ki_mittel-Objekts wird 
	 * diese Funktion ausgefuehrt.
	 * arguments: $sid - Die temporaere Datenbank-ID des aktuellen Spiels (stammt aus /inhalt/zugende.php).
	 * 			  $id - Die Datenbank-ID des Computer-Spielers.
	 */
	function __construct($sid, $id) {
		//arguments: $mindest_minenfeld_abstand, $max_abstand_minenfeld_raeumen, 
		//$min_abstand_minenfeld_raeumen_feindschiff, $mindest_wurmloch_abstand, $min_restlemin_sprung, 
		//$sektor_erkundung_groesse, $min_ren_tarnung, $max_ren_tarnung_start, 
		//$max_wurmloch_erkundungs_reichweite, $max_lemin_start
		$schiffe_infos = & new schiffe_infos(95, 400, 600, 35, 15, 250, 10, 30, 300, 200);
		//arguments: $min_jaeger_waffen, $min_jaeger_lemin_prozent, $min_lemin_jaeger_tank_start, 
		//$max_jaeger_reichweite_schiffe, $max_jaeger_reichweite_planeten, $max_jaeger_reichweite_angriff, 
		//$max_jaeger_schaden, $min_projektile_minen_legen, $min_feind_abstand_minen_legen, 
		//$max_feind_abstand_minen_legen, $min_kolonisten_destabilisator, $min_destabilisator_prozent
		$jaeger_infos = & new jaeger_infos(6, 50, 90, 500, 1000, 750, 20, 10, 100, 200, 2000, 50);
		//arguments: $min_scout_waffen, $max_scout_waffen, $min_scout_lemin_prozent, 
		//$max_scout_reichweite_schiffe, $max_scout_anzahl, $max_scout_prozent
		$scouts_infos = & new scouts_infos(2, 5, 60, 500, 8, 40);
		//arguments: $min_frachtraum_frachter, $max_frachter_waffen, $min_frachter_lemin_prozent, 
		//$max_frachter_reichweite, $max_frachter_schaden, $max_gegner_frachter_naehe
		$frachter_infos = & new frachter_infos(200, 3, 60, 350, 10, 100);
		//arguments: $kolo_cantox, $kolo_leute, $kolo_leute_anfangs, $max_kolonien_wenig_leute, $kolo_vorrat, 
		//$kolo_max_kolonien, $kolos_pro_frachter, $kolo_anfangs_runden, $min_kolonien, $min_angriffe
		$frachter_kolo_infos = & new frachter_kolo_infos(100, 10001, 1000, 20, 5, 8, 4, 15, 20, 1);
		//arguments: $max_planeten_pro_route, $min_planeten_pro_route, $min_route_lemin
		$frachter_route_infos = & new frachter_route_infos(6, 3, 30);
		//arguments: $min_lemin_cluster_off, $min_planeten_pro_terra_warm, $min_planeten_pro_terra_kalt, 
		//$max_terra_warm, $max_terra_kalt, $max_srv_prozent, $max_srv_anzahl, $max_srv_reichweite, 
		//$max_viral_anzahl, $max_aktive_taster_prozent, $max_aktive_taster_anzahl, $max_taster_prozent, 
		//$max_lemin_quark_on, $min_native_invasion
		$spezialschiffe_infos = & new spezialschiffe_infos(400, 10, 10, 2, 2, 40, 6, 750, 2, 70, 10, 50, 500, 500);
		//arguments: $basis_namen_synax, $min_basen_abstand, $neue_basis_min_rumpf, $neue_basis_min_antrieb, 
		//$neue_basis_min_waffen
		$basen_neu_infos = & new basen_neu_infos("Starbase ", 200, 9, 8, 5);
		//arguments: $waffen_tech_limit, $antrieb_tech_limit, $kein_waffenausbau_limit, $rumpf_anfangs_limit, 
		//$rumpf_anfangs_limit_ticks, $rumpf_sparen_min_stufe, $rumpf_sparen_min_jaeger, 
		//$rumpf_sparen_zusatz_jaeger
		$basen_ausbau_infos = & new basen_ausbau_infos(5, 8, 10, 5, 5, 5, 6, 2);
		//arguments: $schiff_namen_synax, $antrieb_toleranz_limit, $waffen_toleranz_limit
		$basen_schiffbau_infos = & new basen_schiffbau_infos("schiff_", 1, 1);
		//arguments: $raumfalte_min_lemin, $raumfalte_frachter_lemin, $raumfalte_jaeger_lemin, 
		//$raumfalte_max_planeten_lemin, $raumfalte_min_cantox_basen, $raumfalte_min_min1_basen, 
		//$raumfalte_min_min2_basen, $raumfalte_min_min3_basen, $raumfalte_min_lemin_basen, $max_cantox_basen, 
		//$max_min1_basen, $max_min2_basen, $max_min3_basen, $max_lemin_basen, $max_raumfalten_pro_planet
		$raumfalten_infos = & new raumfalten_infos(30, 60, 100, 15, 5000, 500, 500, 500, 400, 
							3000, 350, 350, 350, 150, 2);
		//arguments: $minen_limit, $kein_auto_bau_limit, $minen_limit_start_planet, $min_dom_spezies_reservat
		$planeten_infos = & new planeten_infos(30, 12, 220, 5000);
		//arguments: $max_distanz_geleitschutz, $min_lemin_warten, $min_lemin_geleit_geben
		$geleitschutz_infos = & new geleitschutz_infos(70, 50, 60);
		
		ki_basis::__construct($sid, $id, "Computer (Leicht)", $schiffe_infos, $jaeger_infos, $scouts_infos, 
						$frachter_infos, $frachter_kolo_infos, $frachter_route_infos, $spezialschiffe_infos, 
						$basen_neu_infos, $basen_ausbau_infos, $basen_schiffbau_infos, $raumfalten_infos, 
						$planeten_infos, $geleitschutz_infos);
		
		$this->planeten = & new planeten_mittel();
		$this->politik = & new politik_mittel();
		$this->frachter = & new frachter_mittel();
		$this->jaeger = & new jaeger_mittel();
		$this->scouts = & new scouts_mittel();
		$this->spezialschiffe = & new spezialschiffe_mittel();
		$this->sternenbasen = & new sternenbasen_mittel();
		$this->geleitschutz = & new geleitschutz_mittel();
		
		if(eigenschaften::$rasse == "kuatoh") eigenschaften::$frachter_infos->min_frachtraum_frachter = 150;
		
		ki_basis::pruefeSpezialSchiffe();
		$this->ermittleSchiffArrays();
		$this->frachter->ermittleSchiffIDs();
		$this->ermittleKolonien();
		$this->sternenbasen->ermittleBasisIDs();
		$this->ermittleGegner();
		$this->frachter->ermittleRouten();
		$this->ermittleGegnerSchiffe();
		$this->ermittleGegnerPlaneten();
		$this->ermittleSichtbareGegnerPlaneten();
		$this->ermittleGegnerAngriffe();
		$this->ermittleNeueBasenPlaneten();
	}
		
	/**
	 * Dies ist die wichtigste Funktion einer KI-Klasse. Sie wird in /inhalt/zugende.php aufgerufen, nachdem 
	 * das KI-Objekt erstellt wurde.
	 */
	function berechneZug() {
		$this->politik->verwaltePolitikAnfragen();
		$this->geleitschutz->verwalteGeleitschutz();
		$this->frachter->verwalteFrachter();
		$this->spezialschiffe->verwalteSpezialSchiffe();
		$this->scouts->verwalteScouts();
		$this->jaeger->verwalteJaeger();
		$this->sternenbasen->verwalteBasen();
		$this->planeten->verwaltePlaneten();
	}
}
?>