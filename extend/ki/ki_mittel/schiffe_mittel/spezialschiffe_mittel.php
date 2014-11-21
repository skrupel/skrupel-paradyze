<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Erweitert spezialschiffe_leicht.
 */
class spezialschiffe_mittel extends spezialschiffe_leicht {
	
	/**
	 * Bestimmt, welche Erkundungs-Funktion benutzt wird. Diese Funktion ist nur eine Wrapper-Funktion!
	 * Hier wird die Funktion schiffe_mittel::ermittleErkundungsZiel() verwendet.
	 * arguments: $schiff_id - Die Datenbank-ID des Schiffs, das erkunden soll.
	 * returns: Die Rueckgabe der verwendeten Erkundungs-Funktion.
	 */
	function erkunde($schiff_id) {
		return schiffe_mittel::ermittleErkundungsZiel($schiff_id);
	}
}
?>