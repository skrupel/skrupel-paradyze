<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Abstrakte Oberklasse fuer das Verhalten von Jaegern.
 */
abstract class jaeger_basis extends schiffe_basis {
	
	/**
	 * Analysiert die Situation aller Jaeger des KI-Spielers und reagiert gegebenenfalls.
	 * Muss implementiert werden, wenn von dieser Klasse geerbt wird.
	 */
	abstract function verwalteJaeger();
	
	/**
	 * Ermittelt das naechste Ziel fuer den uebergebenen Jaeger und setzt gegebenenfalls Kurs auf das Ziel. 
	 * Muss implementiert werden, wenn von dieser Klasse geerbt wird.
	 * arguments: $jaeger_id - Die Datenbank-ID des Jaegers, das ein neues Ziel bekommen soll.
	 */
	abstract function fliegeJaeger($jaeger_id);
}
?>
