<?php
/**
 * Autor: Wasserleiche (Wasserleiche88@gmail.com)
 * 
 * Statische Klasse fuer das Verhalten mit Wurmloecher.
 */
class wurmloecher_basis {
	
	/**
	 * Ermittelt die Ziel-ID und die Ziel-Koordinaten des uebergebenen Wurmlochs.
	 * arguments: $wurmloch_id - Die Datenbank-ID des Wurmlochs, dessen Ziel-Daten bestimmt werden sollen.
	 * returns: Die ID sowie die X- und Y-Koordinaten des Ziel-Wurmlochs.
	 * 			null, falls das Wurmloch nicht bekannt ist.
	 */
	static function ermittleWurmlochZiel($wurmloch_id) {
		$wurmloch_ziel = @mysql_query("SELECT extra FROM skrupel_anomalien WHERE id='$wurmloch_id'");
		$wurmloch_ziel = @mysql_fetch_array($wurmloch_ziel);
		if($wurmloch_ziel == null || $wurmloch_ziel['extra'] == null) return null;
		$wurmloch_ziel = explode(':', $wurmloch_ziel['extra']);
		return array('id'=>$wurmloch_ziel[0], 'x'=>$wurmloch_ziel[1], 'y'=>$wurmloch_ziel[2]);
	}
	
	/**
	 * Ermittelt die Datenbank-IDs und Koordinaten aller gerade sichtbaren Wurmloecher und speichert diese in 
	 * eigenschaften::$gesehene_wurmloecher_daten und ubertraegt diese in die Tabelle skrupel_ki_objekte.
	 */
	static function ermittleSichtbareWurmloecher() {
		$sicht = "(sicht_".eigenschaften::$comp_id."=1)";
		$spiel_id = eigenschaften::$spiel_id;
		$gerade_sichtbare_wurmloecher = @mysql_query("SELECT x_pos, y_pos, id FROM skrupel_anomalien 
				WHERE (spiel='$spiel_id') AND $sicht AND (art=1)");
		while($wurmloch = @mysql_fetch_array($gerade_sichtbare_wurmloecher)) {
			$wurmloch_daten = array('x'=>$wurmloch['x_pos'], 'y'=>$wurmloch['y_pos'], 'id'=>$wurmloch['id']);
			if(in_array($wurmloch_daten, eigenschaften::$gesehene_wurmloecher_daten)) continue;
			self::updateGeseheneWurmloecher($wurmloch['id']);
			eigenschaften::$gesehene_wurmloecher_daten[] = $wurmloch_daten;
		}
	}
	
	/**
	 * Ermittelt die Datenbank-IDs und Koordinaten aller in der Tabelle skrupel_ki_objekte gesehenen Wurmloecher 
	 * und speichert diese in eigenschaften::$gesehene_wurmloecher_koords.
	 */
	static function ermittleGeseheneWurmloecher() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$wurmloch_koords = @mysql_query("SELECT a.x_pos, a.y_pos, a.id FROM skrupel_ki_objekte o, 
			skrupel_anomalien a WHERE (a.spiel='$spiel_id') AND (o.objekt_id=a.id) AND (o.comp_id='$comp_id') 
			AND (o.extra=0)");
		while($wurmloch = @mysql_fetch_array($wurmloch_koords)) {
			eigenschaften::$gesehene_wurmloecher_daten[] = array('x'=>$wurmloch['x_pos'], 'y'=>$wurmloch['y_pos'], 
																 'id'=>$wurmloch['id']);
		}
	}
	
	/**
	 * Aktualisiert die gesehenen Wurmloecher mit der uebergebenen Wurmloch-ID.
	 * arguments: $wurmloch_id - Die Datenbank-ID des Wurmlochs, das gesehen wurde und gespeichert werden soll.
	 */
	static function updateGeseheneWurmloecher($wurmloch_id) {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$wurmloecher = @mysql_query("SELECT a.id FROM skrupel_ki_objekte o, skrupel_anomalien a 
				WHERE (a.spiel='$spiel_id') AND (o.objekt_id=a.id) AND (o.comp_id='$comp_id')");
		$wurmloch_ids = array();
		while($wurmloch = @mysql_fetch_array($wurmloecher)) {
			$wurmloch_ids[] = $wurmloch['id'];
		}
		if(in_array($wurmloch_id, $wurmloch_ids)) return;
		$id = ki_basis::ermittleNaechsteID("skrupel_ki_objekte");
		@mysql_query("INSERT INTO skrupel_ki_objekte (id, objekt_id, comp_id, extra) 
				VALUES('$id', '$wurmloch_id', '$comp_id', 0)");
		$wurmloch_koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_anomalien WHERE id='$wurmloch_id'");
		$koords = @mysql_fetch_array($wurmloch_koords);
		eigenschaften::$gesehene_wurmloecher_daten[] = array('x'=>$koords['x_pos'], 'y'=>$koords['y_pos'], 
															 'id'=>$wurmloch_id);
	}
	
	/**
	 * Ermittelt die Datenbank-IDs und Koordinaten aller bekannten instabilen Wurmloecher und schreibt diese 
	 * in eigenschaften::$bekannte_instabile_wurmloch_daten.
	 */
	static function ermittleInstabileWurmloecher() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$wurmloch_koords = @mysql_query("SELECT a.x_pos, a.y_pos, a.id FROM skrupel_ki_objekte o, 
			skrupel_anomalien a WHERE (a.spiel='$spiel_id') AND (o.objekt_id=a.id) AND (o.comp_id='$comp_id') 
			AND (o.extra=2)");
		while($wurmloch = @mysql_fetch_array($wurmloch_koords)) {
			eigenschaften::$bekannte_instabile_wurmloch_daten[] = array('x'=>$wurmloch['x_pos'], 
																  'y'=>$wurmloch['y_pos'], 'id'=>$wurmloch['id']);
		}
	}
	
	/**
	 * Aktualisiert die bekannten instabilen Wurmloecher mit der uebergebenen Wurmloch-ID.
	 * arguments: $wurmloch_id - Die Datenbank-ID des Wurmlochs, das instabil ist und gespeichert werden soll.
	 */
	static function updateInstabileWurmloecher($wurmloch_id) {
		self::ermittleInstabileWurmloecher();
		foreach(eigenschaften::$bekannte_instabile_wurmloch_daten as $instabiles_wurmloch) {
			if($wurmloch_id == $instabiles_wurmloch['id']) return;
		}
		self::ermittleSichtbareWurmloecher();
		$comp_id = eigenschaften::$comp_id;
		@mysql_query("UPDATE skrupel_ki_objekte SET extra=2 
			WHERE (objekt_id='$wurmloch_id') AND (comp_id='$comp_id')");
		$koords = @mysql_query("SELECT x_pos, y_pos FROM skrupel_anomalien WHERE id='$wurmloch_id'");
		$koords = @mysql_fetch_array($koords);
		$wurmloch_daten = array('x'=>$koords['x_pos'], 'y'=>$koords['y_pos'], 'id'=>$wurmloch_id);
		eigenschaften::$bekannte_instabile_wurmloch_daten[] = $wurmloch_daten;
	}
	
	/**
	 * Ermittelt die Datenbank-IDs und Koordinaten aller bekannten Wurmloecher und schreibt diese 
	 * in eigenschaften::$bekannte_wurmloch_daten.
	 */
	static function ermittleBekannteWurmloecher() {
		$spiel_id = eigenschaften::$spiel_id;
		$comp_id = eigenschaften::$comp_id;
		$wurmloch_koords = @mysql_query("SELECT a.x_pos, a.y_pos, a.id FROM skrupel_ki_objekte o, 
			skrupel_anomalien a WHERE (a.spiel='$spiel_id') AND (o.objekt_id=a.id) AND (o.comp_id='$comp_id') 
			AND (o.extra=1)");
		while($wurmloch = @mysql_fetch_array($wurmloch_koords)) {
			eigenschaften::$bekannte_wurmloch_daten[] = array('x'=>$wurmloch['x_pos'],'y'=>$wurmloch['y_pos'], 
															  'id'=>$wurmloch['id']);
		}
	}
	
	/**
	 * Aktualisiert die Tabelle skrupel_ki_objekte mit dem uebergebenen Wurmloch (und dessen Ziel-Wurmloch) 
	 * und fuegt dieses in self::bekannte_wurmloch_daten ein. Ist das Wurmloch schon bekannt, so wird 
	 * nichts  veraendert.
	 * arguments: $wurmloch_id - Die Datenbank-ID des Wurmlochs, das in die Datenbank soll.
	 */
	static function updateBekannteWurmloecher($wurmloch_id) {
		if($wurmloch_id == null || $wurmloch_id == 0) return;
		self::ermittleBekannteWurmloecher();
		foreach(eigenschaften::$bekannte_wurmloch_daten as $wurmloch) {
			if($wurmloch_id == $wurmloch['id']) return;
		}
		self::ermittleSichtbareWurmloecher();
		$austritt_wurmloch = self::ermittleWurmlochZiel($wurmloch_id);
		$austritts_id = $austritt_wurmloch['id'];
		$comp_id = eigenschaften::$comp_id;
		@mysql_query("UPDATE skrupel_ki_objekte SET extra=1 
			WHERE (objekt_id='$wurmloch_id') AND (comp_id='$comp_id')");
		@mysql_query("UPDATE skrupel_ki_objekte SET extra=1 
			WHERE (objekt_id='$austritts_id') AND (comp_id='$comp_id')");
		$wurmloch_daten = @mysql_query("SELECT x_pos, y_pos FROM skrupel_anomalien WHERE id='$wurmloch_id'");
		$wurmloch_daten = @mysql_fetch_array($wurmloch_daten);
		$wurmloch_daten = array('x'=>$wurmloch_daten['x_pos'], 'y'=>$wurmloch_daten['y_pos'], 'id'=>$wurmloch_id);
		eigenschaften::$bekannte_wurmloch_daten[] = $wurmloch_daten;
		eigenschaften::$bekannte_wurmloch_daten[] = $austritt_wurmloch;
	}
}
?>
