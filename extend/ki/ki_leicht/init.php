<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Hier wird die KI initialisiert, die in diesem Ordner ist. Jede KI benoetigt eine init.php-Datei, damit 
 * diese gestartet werden kann.
 */
 
include_once("ki_leicht.php");

$leichte_ki = & new ki_leicht($sid, $id);
$leichte_ki->berechneZug();
?>
