<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Wird nur bei installierter, aktiver KI ausgefuehrt. Es werden pro installierter KI ein Spieler 
 * erstellt. Falls mehr Spieler pro KI in einem Spiel sein sollen, muss dieser Code entsprechend 
 * veraendert werden.
 */

include ('../inc.conf.php');
$conn = @mysql_connect($conf_database_server,$conf_database_login,$conf_database_password);
$db = @mysql_select_db($conf_database_database,$conn);

include ("../extend/ki/ki_basis/ki_basis.php");
$result = @mysql_query("SHOW TABLES LIKE 'skrupel_ki_planeten'") or die(mysql_error());
if(@mysql_num_rows($result) != 1) {
	@mysql_query("CREATE TABLE skrupel_ki_planeten (id INTEGER PRIMARY KEY, 
											planeten_id INTEGER NOT NULL, 
											comp_id INTEGER NOT NULL, 
											extra VARCHAR(64), 
											FOREIGN KEY (planeten_id) 
											REFERENCES skrupel_planeten(id));");
}
$result = @mysql_query("SHOW TABLES LIKE 'skrupel_ki_neuebasen'") or die(mysql_error());
if(@mysql_num_rows($result) != 1) {
	@mysql_query("CREATE TABLE skrupel_ki_neuebasen (id INTEGER PRIMARY KEY, 
										     planeten_id INTEGER NOT NULL, 
										     FOREIGN KEY (planeten_id) 
										     REFERENCES skrupel_planeten(id));");
}
$result = @mysql_query("SHOW TABLES LIKE 'skrupel_ki_objekte'") or die(mysql_error());
if(@mysql_num_rows($result) != 1) {
	@mysql_query("CREATE TABLE skrupel_ki_objekte (id INTEGER PRIMARY KEY, 
										   objekt_id INTEGER NOT NULL, 
										   comp_id INTEGER NOT NULL, 
										   extra VARCHAR(64), 
										   FOREIGN KEY (objekt_id) 
										   REFERENCES skrupel_anomalien(id));");
}
$result = @mysql_query("SHOW TABLES LIKE 'skrupel_ki_spezialschiffe'") or die(mysql_error());
if(@mysql_num_rows($result) != 1) {
	@mysql_query("CREATE TABLE skrupel_ki_spezialschiffe (id INTEGER PRIMARY KEY, 
											      schiff_id INTEGER NOT NULL, 
											      spezial_mission VARCHAR(64), 
											      aktiv TINYINT, 
										  	      FOREIGN KEY (schiff_id) 
										  		  REFERENCES skrupel_schiffe(id));");
}
$ki_daten = ki_basis::ermittleKIDaten();
foreach($ki_daten as $ki) {
	$comp_nick = $ki['nick'];
	$computerspieler = @mysql_query("SELECT id FROM skrupel_user WHERE nick LIKE '$comp_nick%'");
	$computerspieler = @mysql_fetch_array($computerspieler);
	if($computerspieler['id'] == null || $computerspieler['id'] == 0) {
		for($i=0; $i<10; $i++) {
			$nick = $comp_nick." ".$i;
			@mysql_query("INSERT INTO skrupel_user (nick, passwort) VALUES ('$nick', '$i')");
		}
	}
}
?>