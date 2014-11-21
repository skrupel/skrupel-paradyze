<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zum Verhalten von Frachtern, 
 * die kolonisieren.
 */
class frachter_kolo_infos {
	/**Die Menge an Cantox, die ein Schiff, das kolonisieren soll, pro Planeten einladen soll.
	 * Muss groesser 0 sein, damit sich die Kolonien entwickeln koennen.*/
	var $kolo_cantox;
	/**Die Menge an Kolonisten, die ein Schiff, das kolonisieren soll, pro Planeten einladen soll.
	 * Muss groesser als 999 sein, damit sich die Kolonien entwickeln koennen.*/
	var $kolo_leute;
	/**Bestimmt die Anzahl an Kolonisten, die ein kolonisierendes Schiff zu Beginn eines Spiels pro Planet 
	 * laden soll. Muss groesser als 999 sein.*/
	var $kolo_leute_anfangs;
	/**Bestimmt die maximale Anzahl an Kolonien, die die KI haben darf, damit $kolo_leute_anfangs statt 
	 * $kolo_leute benutzt wird.*/
	var $max_kolonien_wenig_leute;
	/**Die Menge an Vorraeten, die ein Schiff, das kolonisieren soll, pro Planeten einladen soll.
	 * Muss groesser als 0 sein, damit sich die Kolonien entwickeln koennen.*/
	var $kolo_vorrat;
	/**Die Obergrenze an Kolonien, fuer die ein Schiff, das kolonisieren soll, einladen soll.*/
	var $kolo_max_kolonien;
	/**Bestimmt den "Takt", in der neue Frachter gebaut werden, in Abhaengigkeit der Planetenanzahl.*/
	var $kolos_pro_frachter;
	/**Bestimmt die Runden-Anzahl am Anfang, in der kolonisiert wird, ohne zu pruefen, ob es 
	 * sinnvoll/notwendig ist.*/
	var $kolo_anfangs_runden;
	/**Bestimmt die Anzahl an Kolonien, bis zu denen mindestens kolonisiert wird.*/
	var $min_kolonien;
	/**Die minimalen detektierten Angriffe, sodass die weitere Kolonisierung eingestellt wird.*/
	var $min_angriffe;
	
	/**
	 * Konstruktor fuer frachter_kolo_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($kolo_cantox, $kolo_leute, $kolo_leute_anfangs, $max_kolonien_wenig_leute, $kolo_vorrat, 
						$kolo_max_kolonien, $kolos_pro_frachter, $kolo_anfangs_runden, $min_kolonien, 
						$min_angriffe) {
		$this->kolo_cantox = $kolo_cantox;
		$this->kolo_leute = $kolo_leute;
		$this->kolo_leute_anfangs = $kolo_leute_anfangs;
		$this->max_kolonien_wenig_leute = $max_kolonien_wenig_leute;
		$this->kolo_vorrat = $kolo_vorrat;
		$this->kolo_max_kolonien = $kolo_max_kolonien;
		$this->kolos_pro_frachter = $kolos_pro_frachter;
		$this->kolo_anfangs_runden = $kolo_anfangs_runden;
		$this->min_kolonien = $min_kolonien;
		$this->min_angriffe = $min_angriffe;
	}
}
?>
