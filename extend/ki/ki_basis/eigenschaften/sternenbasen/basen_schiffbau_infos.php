<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zum Bau von Schiffen.
 */
class basen_schiffbau_infos {
	/**Alle Schiffnamen werden durchnummeriert. Dieses Feld bestimmt den Text vor der Nummer.*/
	var $schiff_namen_synax;
	/**Falls beim Schiffbau die Resourcen fuer den hoechsten verfuegbaren Antrieb nicht 
	 * ausreichen sollten, wird hiernach bestimmt, wie gering der Antriebs-Level ausfallen darf.*/
	var $antrieb_toleranz_limit;
	/**Falls beim Schiffbau die Resourcen fuer die hoechsten verfuegbaren Waffen nicht
	 * ausreichen sollten, wird hiernach bestimmt, wie gering der Waffen-Level ausfallen darf.*/
	var $waffen_toleranz_limit;
	
	/**Kosten-Uebersicht fuer die Antriebe.*/
	var $antriebs_kosten = array(0 => array(1,2,3,10,25,58,165,212,317), //Cantox
                    		 1 => array(1,2,2,3,3,3,3,13,17), //Baxterium
                    		 2 => array(5,5,3,3,3,4,5,3,3), //Rennurbin
                   			 3 => array(0,1,5,7,7,15,15,28,35)); //Vomisaan
	/**Kosten-Uebersicht fuer die Energetik-Waffen.*/
	var $energetik_kosten = array(array(1,2,5,10,12,13,31,35,36,54), //Cantox
							  array(0,0,2,12,12,12,12,12,18,12), //Baxterium
							  array(1,1,1,1,1,1,1,1,1,1), //Rennurbin
							  array(0,0,0,1,5,1,14,30,38,57)); //Vomisaan
	/**Kosten-Uebersicht fuer die Projektil-Waffen.*/
	var $projektil_kosten = array(array(1,2,4,10,12,13,31,35,36,54), //Cantox
							  array(1,0,4,3,1,4,7,2,3,1), //Baxterium
							  array(1,1,1,1,1,1,1,1,1,1), //Rennurbin
							  array(0,0,0,1,5,1,14,7,8,9)); //Vomisaan
	
	/**
	 * Konstruktor fuer basen_schiffbau_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($schiff_namen_synax, $antrieb_toleranz_limit, $waffen_toleranz_limit) {
		$this->schiff_namen_synax = $schiff_namen_synax;
		$this->antrieb_toleranz_limit = $antrieb_toleranz_limit;
		$this->waffen_toleranz_limit = $waffen_toleranz_limit;
	}
}
?>
