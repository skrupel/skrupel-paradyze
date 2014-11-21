<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Loescht alle von den KI-Spielern des Spiels in die KI-Tabellen eingetragenen Daten.
 */

//Loescht alle von der KI in die Datenbank eingetragenen neuen-Basen-Planeten:
$planeten = @mysql_query("SELECT k.id FROM skrupel_planeten p, skrupel_ki_neuebasen k 
		WHERE (p.spiel='$spiel') AND (p.id=k.planeten_id)");
while($id = @mysql_fetch_row($planeten)) {
	@mysql_query("DELETE FROM skrupel_ki_neuebasen WHERE id='$id[0]'");
}

//Loescht alle von der KI in die Datenbank eingetragenen schlechten Planeten:
$planeten = @mysql_query("SELECT k.id FROM skrupel_planeten p, skrupel_ki_planeten k 
		WHERE (p.spiel='$spiel') AND (p.id=k.planeten_id)");
while($id = @mysql_fetch_row($planeten)) {
	@mysql_query("DELETE FROM skrupel_ki_planeten WHERE id='$id[0]'");
}

//Loescht alle von der KI in die Datenbank eingetragenen Spezial-Schiffe:
$schiffe = @mysql_query("SELECT k.id FROM skrupel_schiffe s, skrupel_ki_spezialschiffe k 
		WHERE (s.spiel='$spiel') AND (s.id=k.schiff_id)");
while($id = @mysql_fetch_row($schiffe)) {
	@mysql_query("DELETE FROM skrupel_ki_spezialschiffe WHERE id='$id[0]'");
}

//Loescht alle von der KI in die Datenbank eingetragenen Objekte:
$objekte = @mysql_query("SELECT k.id FROM skrupel_anomalien a, skrupel_ki_objekte k 
		WHERE (a.spiel='$spiel') AND (a.id=k.objekt_id)");
while($id = @mysql_fetch_row($objekte)) {
	@mysql_query("DELETE FROM skrupel_ki_objekte WHERE id='$id[0]'");
}
?>
