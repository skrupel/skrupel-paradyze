<?php
/**
 * Autor: SHE
 */

include ('../../inc.conf.php');
$conn = @mysql_connect($conf_database_server,$conf_database_login,$conf_database_password);
$db = @mysql_select_db($conf_database_database,$conn);

$result = @mysql_query("SHOW TABLES LIKE 'skrupel_xstats'") or die(mysql_error());
if(@mysql_num_rows($result) != 1) {
    @mysql_query("CREATE TABLE skrupel_xstats (id INTEGER PRIMARY KEY AUTO_INCREMENT,
			gameid INTEGER NOT NULL,
			playerindex INTEGER NOT NULL,
			playerid INTEGER NOT NULL, 
			turn INTEGER NOT NULL, 
			shipcount INTEGER NOT NULL,
                        freightercount INTEGER NOT NULL,
			shipmasscount INTEGER NOT NULL, 
			planetcount INTEGER NOT NULL,
                        allplanetcount INTEGER NOT NULL,
                        colonistcount INTEGER NOT NULL,
                        stationcount INTEGER NOT NULL,
                        stationcountstd INTEGER NOT NULL,
                        stationcountrep INTEGER NOT NULL,
                        stationcountdef INTEGER NOT NULL,
                        stationcountstdextra INTEGER NOT NULL,
                        lemincount INTEGER NOT NULL,
                        min1count INTEGER NOT NULL,
                        min2count INTEGER NOT NULL,
                        min3count INTEGER NOT NULL,
                        cantoxcount INTEGER NOT NULL,
                        minescount INTEGER NOT NULL,
                        factorycount INTEGER NOT NULL,
                        sumcargohold INTEGER NOT NULL,
                        usedcargohold INTEGER NOT NULL,
                        boxcount INTEGER NOT NULL,
                        rank INTEGER NOT NULL,
                        battlecount INTEGER NOT NULL,
                        battlewoncount INTEGER NOT NULL,
                        coloniestakencount INTEGER NOT NULL,
                        lj FLOAT NOT NULL,
                FOREIGN KEY (playerid) REFERENCES skrupel_user(id),
                FOREIGN KEY (gameid) REFERENCES skrupel_spiele(id));");
    @mysql_query("CREATE INDEX skrupel_xstats_playerindex ON skrupel_xstats(playerindex);");
    @mysql_query("CREATE INDEX skrupel_xstats_turn ON skrupel_xstats(turn);");
    @mysql_query("CREATE TABLE skrupel_xstats_ships (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                        gameid INT NOT NULL ,
                        shipid INT NOT NULL ,
                        shiprace VARCHAR( 255 ) NOT NULL ,
                        picturesmall VARCHAR( 255 ) NOT NULL ,
                        picture VARCHAR( 255 ) NOT NULL ,
                        shipname VARCHAR( 25 ) NOT NULL ,
                        shipclass VARCHAR( 25 ) NOT NULL ,
                        shipclassid INT NOT NULL ,
                        sinceturn INT NOT NULL ,
                        untilturn INT NOT NULL DEFAULT '0',
                        lj FLOAT NOT NULL ,
                        victories INT NOT NULL,
                        experience INT NOT NULL,
                        levelhull INT NOT NULL DEFAULT '0',
                        levelengine INT NOT NULL DEFAULT '0',
                        levelbeam INT NOT NULL DEFAULT '0',
                        leveltube INT NOT NULL DEFAULT '0',
                        enginecount INT NOT NULL DEFAULT '0',
                        beamcount INT NOT NULL DEFAULT '0',
                        hangarcount INT NOT NULL DEFAULT '0',
                        tubecount INT NOT NULL DEFAULT '0',
                        leminmax INT NOT NULL DEFAULT '0',
                        crewmax INT NOT NULL DEFAULT '0',
                        cargomax INT NOT NULL DEFAULT '0',
                        cargosum INT NOT NULL DEFAULT '0',
                        mass INT NOT NULL DEFAULT '0',
                        buildposx INT NOT NULL DEFAULT '0',
                        buildposy INT NOT NULL DEFAULT '0',
                        buildplanetname VARCHAR( 255 ) NOT NULL,
                FOREIGN KEY (gameid) REFERENCES skrupel_spiele(id));");
    @mysql_query("CREATE INDEX skrupel_xstats_ships_shipid ON skrupel_xstats_ships(shipid);");
    @mysql_query("CREATE INDEX skrupel_xstats_ships_shipclassid ON skrupel_xstats_ships(shipclassid);");
    @mysql_query("CREATE TABLE skrupel_xstats_shipowner (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                        gameid INT NOT NULL ,
                        shipid INT NOT NULL ,
                        ownerid INT NOT NULL ,
                        ownerindex INT NOT NULL ,
                        turn INT NOT NULL ,
                FOREIGN KEY (gameid) REFERENCES skrupel_spiele(id),
                FOREIGN KEY (shipid) REFERENCES skrupel_xstats_ships(shipid),
                FOREIGN KEY (ownerid) REFERENCES skrupel_user(id)
                );");
    @mysql_query("CREATE INDEX skrupel_xstats_shipowner_ownerindex ON skrupel_xstats_shipowner(ownerindex);");
    @mysql_query("CREATE INDEX skrupel_xstats_shipowner_turn ON skrupel_xstats_shipowner(turn);");
    @mysql_query("CREATE TABLE skrupel_xstats_shipvsship (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                        gameid INT NOT NULL ,
                        shipid INT NOT NULL ,
                        fightresult INT NOT NULL ,
                        hulldamage INT NOT NULL ,
                        enemyshipid INT NOT NULL ,
                        turn INT NOT NULL ,
                FOREIGN KEY (gameid) REFERENCES skrupel_spiele(id),
                FOREIGN KEY (shipid) REFERENCES skrupel_xstats_ships(shipid),
                FOREIGN KEY (enemyshipid) REFERENCES skrupel_xstats_ships(shipid)
                );");
    @mysql_query("CREATE INDEX skrupel_xstats_ships_fightresult ON skrupel_xstats_shipvsship(fightresult);");
    @mysql_query("CREATE INDEX skrupel_xstats_ships_turn ON skrupel_xstats_shipvsship(turn);");
    @mysql_query("CREATE TABLE skrupel_xstats_turntime (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                        gameid INT NOT NULL ,
                        turn INT NOT NULL ,
                        turnendtime VARCHAR(20) NOT NULL ,
                FOREIGN KEY (gameid) REFERENCES skrupel_spiele(id)
                );");
    @mysql_query("CREATE INDEX skrupel_xstats_turntime_turn ON skrupel_xstats_turntime(turn);");
    @mysql_query("CREATE TABLE skrupel_xstats_shipvsplanet (
                        id int(11) NOT NULL auto_increment PRIMARY KEY,
                        gameid int(11) NOT NULL default '0',
                        turn int(11) NOT NULL default '0',
                        shipid int(11) NOT NULL default '0',
                        posx int(11) NOT NULL default '0',
                        posy int(11) NOT NULL default '0',
                        planetownerid int(11) NOT NULL default '0',
                        planetownerindex int(11) NOT NULL default '0',
                        planetname varchar(100) NOT NULL default 'UNKNOWN',
                        planetid int(11) NOT NULL default '0',
                        eventtype int(11) NOT NULL default '0',
                FOREIGN KEY (gameid) REFERENCES skrupel_spiele(id),
                FOREIGN KEY (shipid) REFERENCES skrupel_xstats_ships(shipid)
                );");
    @mysql_query("CREATE INDEX skrupel_xstats_shipvsplanet_eventtype ON skrupel_xstats_shipvsplanet(eventtype);");    
    @mysql_query("CREATE TABLE skrupel_xstats_version (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                        version VARCHAR( 20 ) NOT NULL
                );");
    @mysql_query("INSERT INTO skrupel_xstats_version(version)VALUES('1.01');");
    echo("Skrupel XStats initialized successfully.");
}else {
    echo("Skrupel XStats already initialized - no action performed.");
}
?>