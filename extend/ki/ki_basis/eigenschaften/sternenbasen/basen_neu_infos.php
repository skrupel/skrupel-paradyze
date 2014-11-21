<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Kontainer-Klasse fuer Informationen zum Bau neuer Sternenbasen.
 */
class basen_neu_infos {
	/**Alle Basisnamen werden durchnummeriert. Dieses Feld bestimmt den Text vor der Nummer.*/
	var $basis_namen_synax;
	/**Bestimmt den minimalen Abstand, die Sternenbasen voneinander haben duerfen. 
	 * Kann sinnvoll sein, um die Resourcen aller Planeten auf die Sternenbasen zu verteilen.*/
	var $min_basen_abstand;
	/**Der minimale Tech-Level fuer Rumpf in allen Sternenbasen der KI. Ist dieser Wert erreicht, 
	 * wird eine neue Sternenbasis gebaut.*/
	var $neue_basis_min_rumpf;
	/**Der minimale Tech-Level fuer Antriebe in allen Sternenbasen der KI. Ist dieser Wert 
	 * erreicht, wird eine neue Sternenbasis gebaut.*/
	var $neue_basis_min_antrieb;
	/**Der minimale Tech-Level fuer Waffen in allen Sternenbasen der KI. Ist dieser Wert erreicht, 
	 * wird eine neue Sternenbasis gebaut.*/
	var $neue_basis_min_waffen;
	
	/**
	 * Konstruktor fuer basen_neu_infos. Die uerbergebenen Werte werden einfach uebernommen.
	 */
	function __construct($basis_namen_synax, $min_basen_abstand, $neue_basis_min_rumpf, 
						$neue_basis_min_antrieb, $neue_basis_min_waffen) {
		$this->basis_namen_synax = $basis_namen_synax;
		$this->min_basen_abstand = $min_basen_abstand;
		$this->neue_basis_min_rumpf = $neue_basis_min_rumpf;
		$this->neue_basis_min_antrieb = $neue_basis_min_antrieb;
		$this->neue_basis_min_waffen = $neue_basis_min_waffen;
	}	
}
?>
