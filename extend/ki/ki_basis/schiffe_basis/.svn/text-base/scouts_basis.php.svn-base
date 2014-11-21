<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Abstrakte Oberklasse fuer das Verhalten von Scouts.
 */
abstract class scouts_basis extends schiffe_basis {
	
	/**
	 * Analysiert die Situation aller Scouts des KI-Spielers und reagiert gegebenenfalls.
	 */
	abstract function verwalteScouts();
	
	/**
	 * Setzt einen Kurs fuer den uebergebene Scout.
	 * Muss implementiert werden, wenn von dieser Klasse geerbt wird.
	 * arguments: $scout_id - Die Datenbank-ID des Scouts, das einen Kurs bekommen soll.
	 */
	abstract function fliegeScout($scout_id);
}
?>
