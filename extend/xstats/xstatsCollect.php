<?php
/**
 * Autor: SHE
 */
include ('xstatsUtil.php');


/**Enters information about a ship vs planet fight, called from inside the Skrupel code
 * Event types:
 * 1: Attacking ship has been destroyed by planetary defense
 */
function xstats_shipVSPlanet( $gameId, $shipId, $shipOwnerIndex, $posX, $posY, $planetId, $planetOwnerIndex, $eventType) {
    //occured once that the owner index is 0
    if( $shipOwnerIndex == 0 || $planetOwnerIndex == 0 ){
        return;
    }
    $shipOwnerId = xstats_getPlayerId($gameId, $shipOwnerIndex);
    $planetOwnerId = xstats_getPlayerId($gameId, $planetOwnerIndex);
    $planetName = xstats_getPlanetName($gameId, $posX, $posY);
    $turn = xstats_getActualTurn($gameId);
    //check if ship already exists. Its possible that the stats start in the middle of a game
    $collectQuery = "SELECT COUNT(1) FROM skrupel_xstats_ships WHERE shipid=".$shipId;
    $collectResult = @mysql_query($collectQuery) or die(mysql_error());
    $collectResult = @mysql_fetch_row($collectResult);
    if( $collectResult[0] == 1 ) {
        $collectQuery = "INSERT INTO skrupel_xstats_shipvsplanet(gameid,turn,shipid,posx,posy,planetownerid,planetownerindex,planetname,planetid,eventtype".
            ")VALUES(".
            $gameId.",".
            $turn.",".
            $shipId.",".
            $posX.",".
            $posY.",".
            $planetOwnerId.",".
            $planetOwnerIndex.",".
            "'".$planetName."',".
            $planetId.",".
            $eventType.
            ");";
        //echo $collectQuery;
        @mysql_query( $collectQuery )or die(mysql_error());
    }
}


/**Collects all stats data and stores it in the database table skrupel_xstats
 * !!!!!!!WARNING The passed arrays have to be 1-based!!!!!
 *
 */
function xstats_collectAndStore( $sid, $battleCountArray,$battleCountWonArray,$coloniesTakenCountArray,$ljArray ) {
    $statsGameId = xstats_gameSID2ID( $sid );
    $statsTurn = xstats_getActualTurn( $statsGameId );
    $statsAvailablePlayerIndicies = xstats_getAvailablePlayerIndicies( $statsGameId );
    $statsAvailablePlayerIds = xstats_getAvailablePlayerIds( $statsGameId );
    for ( $i = 0; $i<count($statsAvailablePlayerIds); $i++) {
        $statsPlayerIndex = $statsAvailablePlayerIndicies[$i];
        $statsPlayerId = $statsAvailablePlayerIds[$i];
        $statsShipCount = xstats_getShipCount( $statsGameId, $statsPlayerIndex );
        $statsFreighterCount = xstats_getFreighterCount( $statsGameId, $statsPlayerIndex );
        $statsAccumulatedMass = xstats_getShipMassAccumulated($statsGameId, $statsPlayerIndex);
        $statsPlanetCount = xstats_getPlanetCount($statsGameId, $statsPlayerIndex);
        $statsColonistsCount = xstats_getColonistsAccumulated($statsGameId, $statsPlayerIndex);
        $statsStarbaseCount = xstats_getStarbaseCount($statsGameId, $statsPlayerIndex);
        $statsStarbaseCountStandard = xstats_getStarbaseCountOfType($statsGameId, $statsPlayerIndex, 0);
        $statsStarbaseCountRepair = xstats_getStarbaseCountOfType($statsGameId, $statsPlayerIndex, 1);
        $statsStarbaseCountDefense = xstats_getStarbaseCountOfType($statsGameId, $statsPlayerIndex, 2);
        $statsStarbaseCountStandardWithExtra = xstats_getStarbaseCountOfType($statsGameId, $statsPlayerIndex, 3);
        $statsLeminCount = xstats_getLeminAccumulated($statsGameId, $statsPlayerIndex);
        $statsMin1Count = xstats_getMineralAccumulated($statsGameId, $statsPlayerIndex, 1);
        $statsMin2Count = xstats_getMineralAccumulated($statsGameId, $statsPlayerIndex, 2);
        $statsMin3Count = xstats_getMineralAccumulated($statsGameId, $statsPlayerIndex, 3);
        $statsCantoxCount = xstats_getCantoxAccumulated($statsGameId, $statsPlayerIndex);
        $statsMinesCount = xstats_getMinesAccumulated($statsGameId, $statsPlayerIndex);
        $statsFactoryCount = xstats_getFactoriesAccumulated($statsGameId, $statsPlayerIndex);
        $statsCargoHold = xstats_getSumCargoHold($statsGameId, $statsPlayerIndex);
        $statsUsedCargoHold = xstats_getUsedCargoHold($statsGameId, $statsPlayerIndex);
        $statsBoxCount = xstats_getBoxesAccumulated($statsGameId, $statsPlayerIndex);
        $statsRank = xstats_getRank($statsGameId, $statsPlayerIndex);
        $statsAllPlanetCount = xstats_getAllPlanetCount($statsGameId);
        $statsBattleCount = 0;
        if($battleCountArray != NULL ) {
        //passed arrays are 1-based!
            $statsBattleCount = $battleCountArray["$statsPlayerIndex"];
        }
        $statsBattleWonCount = 0;
        if($battleCountWonArray != NULL ) {
        //passed arrays are 1-based!
            $statsBattleWonCount = $battleCountWonArray["$statsPlayerIndex"];
        }
        $statsColoniesTakenCount = 0;
        if($coloniesTakenCountArray != NULL ) {
        //passed arrays are 1-based!
            $statsColoniesTakenCount = $coloniesTakenCountArray["$statsPlayerIndex"];
        }
        $lj = 0;
        if($ljArray != NULL ) {
        //passed arrays are 1-based!
            $lj = $ljArray["$statsPlayerIndex"];
        }
        $query = "INSERT INTO skrupel_xstats (gameid,playerindex,playerid,turn,shipcount,freightercount,shipmasscount,planetcount,colonistcount,stationcount,stationcountstd,stationcountrep,stationcountdef,stationcountstdextra,lemincount,min1count,min2count,min3count,cantoxcount,minescount,factorycount,sumcargohold,usedcargohold,boxcount,rank,allplanetcount,battlecount,battlewoncount,coloniestakencount,lj) VALUES".
            "($statsGameId,$statsPlayerIndex,$statsPlayerId,$statsTurn,$statsShipCount,$statsFreighterCount,$statsAccumulatedMass,$statsPlanetCount,$statsColonistsCount,$statsStarbaseCount,$statsStarbaseCountStandard,$statsStarbaseCountRepair,$statsStarbaseCountDefense,$statsStarbaseCountStandardWithExtra,$statsLeminCount,$statsMin1Count,$statsMin2Count,$statsMin3Count,$statsCantoxCount,$statsMinesCount,$statsFactoryCount,$statsCargoHold,$statsUsedCargoHold,$statsBoxCount,$statsRank,$statsAllPlanetCount,$statsBattleCount,$statsBattleWonCount,$statsColoniesTakenCount,$lj)";
        @mysql_query($query) or die(mysql_error());
    }
    //check if the stats_ships table is empty
    $query = "SELECT COUNT(1) FROM skrupel_xstats_ships WHERE gameid=".$statsGameId;
    $result = @mysql_query($query) or die(mysql_error());
    $result = @mysql_fetch_row($result);
    $statsShipCount = $result[0];
    xstats_handleShipsStats($statsGameId, $statsTurn);
    //ignore the fights if the table skrupel_xstats_ships has no entries for this game so far. In this case
    //the stats extension  is installed in the middle of a game and the skrupel fight table could contain
    //references to non-existing ships which would result in a violation of the db integrity
    // (the shipid is defined as secondary key in the table stats_shipvsship)
    if( $statsShipCount != 0 ) {
        xstats_handleShipVsShipStats( $statsGameId, $statsTurn);
    }
    xstats_insertTurnTime( $statsGameId, $statsTurn);
}

/**Inserts the actual turn time into the database*/
function xstats_insertTurnTime( $gameId, $turn ) {
    $query = "INSERT INTO skrupel_xstats_turntime (gameid,turn,turnendtime) VALUES".
        "(".$gameId.",".($turn-1).",".time().")";
    @mysql_query($query) or die(mysql_error());
}


/**performs an insert into the shipvs ship table*/
function xstats_insertToShipVSShip( $gameId, $shipId, $fightResult, $hulldamage, $enemyShipId, $turn ) {
//check if both ship ids exist. Its possible that a ship is destroyed directly after it has been build, this
//would result in a key violation by the following insert
    $query = "SELECT COUNT(1) FROM skrupel_xstats_ships WHERE shipid=".$shipId." OR shipid=".$enemyShipId;
    $result = @mysql_query($query) or die(mysql_error());
    $result = @mysql_fetch_row($result);
    if( $result[0] == 2 ) {
        $query = "INSERT INTO skrupel_xstats_shipvsship(gameid,shipid,fightresult,hulldamage,enemyshipid,turn".
            ") VALUES(".
            $gameId.",".
            $shipId.",".
            $fightResult.",".
            $hulldamage.",".
            $enemyShipId.",".
            $turn.
            ")";
        @mysql_query( $query );
    }
}


/**Collects the fight (ship vs ship) stats*/
function xstats_handleShipVsShipStats( $gameId, $currentTurn ) {
    $query = "SELECT * FROM skrupel_kampf WHERE spiel=".$gameId." ORDER BY id";
    $result = @mysql_query($query) or die(mysql_error());
    while($row = @mysql_fetch_array($result)) {
        $hull1 = $row['schaden_1'];
        $hull2 = $row['schaden_2'];
        $shipId1 = $row['schiff_id_1'];
        $shipId2 = $row['schiff_id_2'];
        if( substr_count($hull1, ':') > 0) {
            $hull1 = explode( ":", $hull1 );
            //format is "nn:nn:nn:" --> last entry is empty after explode
            $index = count( $hull1 )-2;
            $hull1 = $hull1[$index];
            //echo "HULL1=".$hull1."<br>";
            $hull2 = explode( ":", $hull2 );
            //format is "nn:nn:nn:" --> last entry is empty after explode
            $index = count( $hull2 )-2;
            $hull2 = $hull2[$index];
            //echo "HULL2=".$hull2."<br>";
            $crew1 = $row['crew_1'];
            $crew1 = explode( ":", $crew1 );
            //format is "nn:nn:nn:" --> last entry is empty after explode
            $index = count( $crew1 )-2;
            $crew1 = $crew1[$index];
            //echo "CREW1=".$crew1."<br>";
            $crew2 = $row['crew_2'];
            $crew2 = explode( ":", $crew2 );
            //format is "nn:nn:nn:" --> last entry is empty after explode
            $index = count( $crew2 )-2;
            $crew2 = $crew2[$index];
            //echo "CREW2=".$crew2."<br>";
            //states:
            //1 == VICTORY
            //2 == DESTROYED
            //3 == CAPTURED
            $fightResult1 = 1;
            $fightResult2 = 1;
            if( $hull1 == 0 || ($crew1 == 0 && $crew2 == 0 ) ) {
            //echo "SHIP 1 destroyed<br>";
            //destroyed
                $fightResult1 = 2;
                //victory for ship2
                xstats_shipPerformedVictory( $gameId, $currentTurn, $shipId2);
            }
            if( $hull2 == 0 || ($crew1 == 0 && $crew2 == 0 ) ) {
            //echo "SHIP 2 destroyed<br>";
            //destroyed
                $fightResult2 = 2;
                //victory for ship1
                xstats_shipPerformedVictory( $gameId, $currentTurn, $shipId1);
            }
            if($hull1 > 0 && $hull2 > 0 && $crew1 == 0) {
            //echo "SHIP 1 has been captured<br>";
            //captured
                $fightResult1 = 3;
                //victory for ship2
                xstats_shipPerformedVictory( $gameId, $currentTurn, $shipId2);
            }
            if($hull1 > 0 && $hull2 > 0 && $crew2 == 0) {
            //echo "SHIP 2 has been captured<br>";
            //captured
                $fightResult2 = 3;
                //victory for ship1
                xstats_shipPerformedVictory( $gameId, $currentTurn, $shipId1);
            }
            //insert this into the ship vs ship stats table
            xstats_insertToShipVSShip($gameId, $shipId1, $fightResult1, (100-$hull1), $shipId2, $currentTurn);
            xstats_insertToShipVSShip($gameId, $shipId2, $fightResult2, (100-$hull2), $shipId1, $currentTurn);
        }else {
        //hull damage is unknown, just set it to 0%
            $hull1 = 100;
            $hull2 = 100;
            //1 == VICTORY
            //2 == DESTROYED
            //3 == CAPTURED
            $fightResult1 = 1;
            $fightResult2 = 1;
            //empty string, its a ship capture without a fight
            //check for an owner change to see who won
            if( xstats_detectShipOwnerChangeLastTurn( $gameId, $shipId1, $currentTurn )) {
            //ok, ship 1 has been captured by ship 2
                $fightResult1 = 3;
                $fightResult2 = 1;
                //victory for ship2
                xstats_shipPerformedVictory( $gameId, $currentTurn, $shipId2);
            }
            if( xstats_detectShipOwnerChangeLastTurn( $gameId, $shipId2, $currentTurn )) {
            //ok, ship 2 has been captured by ship 1
                $fightResult1 = 1;
                $fightResult2 = 3;
                //victory for ship1
                xstats_shipPerformedVictory( $gameId, $currentTurn, $shipId1);
            }
            if( $fightResult1 != 1 || $fightResult2 != 1) {
            //insert this into the ship vs ship stats table
                xstats_insertToShipVSShip($gameId, $shipId1, $fightResult1, (100-$hull1), $shipId2, $currentTurn);
                xstats_insertToShipVSShip($gameId, $shipId2, $fightResult2, (100-$hull2), $shipId1, $currentTurn);
            }
        }
    }
}


/**Updates the stats ship table*/
function xstats_handleShipsStats( $gameId, $currentTurn ) {
    $gameShipIdArray = array();
    $query = "SELECT id FROM skrupel_schiffe WHERE spiel=".$gameId;
    $result = @mysql_query($query) or die(mysql_error());
    while($row = @mysql_fetch_row($result)) {
        $gameShipIdArray[] = $row[0];
    }
    //select all ships from stats that are still alive
    $statsShipIdArray = array();
    $query = "SELECT shipid FROM skrupel_xstats_ships WHERE gameid=".$gameId." AND untilturn=0";
    $result = @mysql_query($query) or die(mysql_error());
    while($row = @mysql_fetch_row($result)) {
        $statsShipIdArray[] = $row[0];
    }
    foreach($gameShipIdArray as $gameShipId) {
        $foundInStats = false;
        foreach($statsShipIdArray as $statsShipId) {
        //ship found in stats: update its stats
            if( $gameShipId == $statsShipId) {
                xstats_updateShipInStats($gameId, $gameShipId, $currentTurn);
                $foundInStats = true;
                break;
            }
        }
        if( !$foundInStats ) {
        //new ship has been built
            xstats_newShipToStats($gameId, $currentTurn, $gameShipId);
        }
    }
    foreach($statsShipIdArray as $statsShipId) {
        $foundInGame = false;
        foreach($gameShipIdArray as $gameShipId) {
        //ship found in stats: ignore this entry
            if( $gameShipId == $statsShipId) {
                $foundInGame = true;
                break;
            }
        }
        if( !$foundInGame ) {
        //ship exists in stats that has not been found in the life game data
            xstats_markShipAsDestroyedInStats( $gameId, $currentTurn, $statsShipId );
        }
    }
}

/**An existing ship has been found in both stats and actual game data: update it in the stats*/
function xstats_markShipAsDestroyedInStats( $gameId, $currentTurn, $statsShipId  ) {
    @mysql_query("UPDATE skrupel_xstats_ships SET untilturn=".$currentTurn." WHERE gameid=".$gameId." AND shipid=".$statsShipId);
    //enter the actual owner. No owner exists any more, mirror the last valid line
    $query = "SELECT * FROM skrupel_xstats_shipowner WHERE gameid=".$gameId." AND shipid=".$statsShipId." AND turn=".($currentTurn-1);
    $result = @mysql_query($query) or die(mysql_error());
    $result = @mysql_fetch_array($result);
    xstats_setShipOwner( $gameId, $currentTurn, $statsShipId, $result['ownerid'], $result['ownerindex'] );
}

/**A victory of a ship should be marked in the ship stats*/
function xstats_shipPerformedVictory( $gameId, $currentTurn, $shipId  ) {
//get actual number of victories and inc them
    $query = "SELECT victories FROM skrupel_xstats_ships WHERE gameid=".$gameId." AND shipid=".$shipId;
    $result = @mysql_query($query) or die(mysql_error());
    $result = @mysql_fetch_row($result);
    $victories = $result[0]+1;
    $query = "UPDATE skrupel_xstats_ships SET victories=".$victories." WHERE gameid=".$gameId." AND shipid=".$shipId;
    @mysql_query($query);
}

/**Checks if there occured a ship owner change in the last turn*/
function xstats_detectShipOwnerChangeLastTurn( $gameId, $shipId, $turn ) {
    $query = "SELECT ownerid FROM skrupel_xstats_shipowner WHERE gameid=".$gameId." AND shipid=".$shipId." AND turn=".($turn);
    $result = @mysql_query($query) or die(mysql_error());
    if( mysql_num_rows($result)>0 ) {
        $result = @mysql_fetch_array($result);
        $newOwner = $result['ownerid'];
    }else {
    //ship has been built this turn
        return( false );
    }
    $query = "SELECT ownerid FROM skrupel_xstats_shipowner WHERE gameid=".$gameId." AND shipid=".$shipId." AND turn=".($turn-1);
    $result = @mysql_query($query) or die(mysql_error());
    if( mysql_num_rows($result)>0 ) {
        $result = @mysql_fetch_array($result);
        $oldOwner = $result['ownerid'];
    }else {
    //ship has been built this turn
        return( false );
    }
    if(  $oldOwner != $newOwner ) {
        return( true );
    }else {
        return( false );
    }
}


/**Sets the owner of a ship for a special turn of a game*/
function xstats_setShipOwner( $gameId, $turn, $shipId, $ownerId, $ownerIndex ) {
    $query = "INSERT INTO skrupel_xstats_shipowner(gameid,shipid,ownerid,ownerindex,turn".
        ")VALUES(".
        $gameId.",".
        $shipId.",".
        $ownerId.",".
        $ownerIndex.",".
        $turn.
        ")";
    @mysql_query( $query );
}

/**An existing ship has been found in both stats and actual game data: update it in the stats*/
function xstats_updateShipInStats( $gameId, $gameShipId, $turn ) {
    $result = @mysql_query("SELECT * FROM skrupel_schiffe WHERE spiel=".$gameId." AND id=".$gameShipId) or die(mysql_error());
    $result = @mysql_fetch_array($result);
    $ownerIndex = $result['besitzer'];
    //no idea why but the "0" as ownerindex occured once. Skrupel bug?
    if( $ownerIndex > 0 ) {
        $actualCargo = (int)($result['fracht_leute']/100)+$result['fracht_vorrat']+$result['fracht_min1']+$result['fracht_min2']+$result['fracht_min3'];
        $experience = $result['erfahrung'];
        $ownerId = xstats_getPlayerId($gameId, $ownerIndex);
        $lj = $result['strecke'];
        //get the actual cargo sum value
        $query = "SELECT cargosum FROM skrupel_xstats_ships WHERE gameid=".$gameId." AND shipid=".$gameShipId;
        $result = @mysql_query($query) or die(mysql_error());
        $result = @mysql_fetch_row($result);
        $cargoSum = $result[0];
        $query = "UPDATE skrupel_xstats_ships SET ".
            "cargosum=".($cargoSum+$actualCargo).",".
            "lj=".$lj.",".
            "experience=".$experience.
            " WHERE gameid=".$gameId." AND shipid=".$gameShipId;
        @mysql_query($query);
        //enter the actual owner
        xstats_setShipOwner( $gameId, $turn, $gameShipId, $ownerId, $ownerIndex );
    }
}

/**An existing ship has been found in both stats and actual game data: update it in the stats*/
function xstats_newShipToStats( $gameId, $currentTurn, $gameShipId ) {
    $query = "SELECT * FROM skrupel_schiffe WHERE spiel=".$gameId." AND id=".$gameShipId;
    $result = @mysql_query($query) or die(mysql_error());
    $result = @mysql_fetch_array($result);
    $ownerIndex = $result['besitzer'];
    //occured once that the owner index was 0 in the skrupel db, bug?
    if( $ownerIndex > 0 ) {
        $ownerId = xstats_getPlayerId($gameId, $ownerIndex);
        $buildPlanetName = xstats_getPlanetName($gameId, $result['kox'], $result['koy']);
        $query = "INSERT INTO skrupel_xstats_ships(gameid,shipid,shiprace,picturesmall,picture,shipname,shipclass,shipclassid,sinceturn,lj,victories,experience,".
            "levelhull,levelengine,levelbeam,leveltube,enginecount,beamcount,hangarcount,tubecount,leminmax,crewmax,cargomax,cargosum,mass,".
            "buildposx,buildposy,buildplanetname".
            ") VALUES(".
            $gameId.",".
            $result['id'].",".
            "'".$result['volk']."',".
            "'".$result['bild_klein']."',".
            "'".$result['bild_gross']."',".
            "'".$result['name']."',".
            "'".$result['klasse']."',".
            "'".$result['klasseid']."',".
            "'".$currentTurn."',".
            "'".$result['strecke']."',".
            "'0',".
            "'".$result['erfahrung']."',".
            "'".$result['techlevel']."',".
            "'".$result['antrieb']."',".
            "'".$result['energetik_stufe']."',".
            "'".$result['projektile_stufe']."',".
            "'".$result['antrieb_anzahl']."',".
            "'".$result['energetik_anzahl']."',".
            "'".$result['hanger_anzahl']."',".
            "'".$result['projektile_anzahl']."',".
            "'".$result['leminmax']."',".
            "'".$result['crewmax']."',".
            "'".$result['frachtraum']."',".
            "'0',".
            "'".$result['masse']."',".
            "'".$result['kox']."',".
            "'".$result['koy']."',".
            "'".$buildPlanetName."'".
            ")";
        @mysql_query( $query );
        //enter the actual owner
        xstats_setShipOwner( $gameId, $currentTurn, $gameShipId, $ownerId, $ownerIndex );
    }
}

/**Returns a planets name*/
function xstats_getPlanetName( $gameId, $xpos, $ypos) {
    $result = @mysql_query("SELECT * FROM skrupel_planeten WHERE spiel=".$gameId." AND x_pos=".$xpos." AND y_pos=".$ypos) or die(mysql_error());
    $result = @mysql_fetch_array($result);
    return( $result['name']);
}


/**
 * Returns the actual turn of the game
 */
function xstats_getActualTurn($gameId) {
    $turn = @mysql_query("SELECT runde FROM skrupel_spiele WHERE id=$gameId") or die(mysql_error());
    $turn = @mysql_fetch_array($turn);
    return $turn['runde'];
}

/**Returns the number of ships of a single player*/
function xstats_getShipCount( $gameId, $playerIndex ) {
    $shipCount = @mysql_query("SELECT COUNT(1) AS shipcount FROM skrupel_schiffe WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    $shipCount = @mysql_fetch_array($shipCount);
    return $shipCount['shipcount'];
}

/**Returns the accumulated ship mass of a single player*/
function xstats_getShipMassAccumulated( $gameId, $playerIndex ) {
    $accumulatedMass = 0;
    $result = @mysql_query("SELECT masse FROM skrupel_schiffe WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedMass = $accumulatedMass+$row['masse'];
    }
    return( $accumulatedMass );
}

/**Returns the accumulated colonists of a single player*/
function xstats_getColonistsAccumulated( $gameId, $playerIndex ) {
    $accumulatedColonists = 0;
    //get the colonists on planet surface
    $result = @mysql_query("SELECT kolonisten FROM skrupel_planeten WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedColonists = $accumulatedColonists+$row['kolonisten'];
    }
    //get onboard colonists
    $result = @mysql_query("SELECT fracht_leute FROM skrupel_schiffe WHERE spiel='$gameId' AND besitzer='$playerIndex'") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedColonists = $accumulatedColonists+$row['fracht_leute'];
    }
    return( $accumulatedColonists );
}

/**Returns the accumulated mines of a single player*/
function xstats_getMinesAccumulated( $gameId, $playerIndex ) {
    $accumulatedMines = 0;
    //get the mines on planet surface
    $result = @mysql_query("SELECT minen FROM skrupel_planeten WHERE spiel='$gameId' AND besitzer='$playerIndex'") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedMines = $accumulatedMines+$row['minen'];
    }
    return( $accumulatedMines );
}

/**Returns the accumulated mines of a single player*/
function xstats_getFactoriesAccumulated( $gameId, $playerIndex ) {
    $accumulatedFactories = 0;
    //get the factories on planet surface
    $result = @mysql_query("SELECT fabriken FROM skrupel_planeten WHERE spiel='$gameId' AND besitzer='$playerIndex'") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedFactories = $accumulatedFactories+$row['fabriken'];
    }
    return( $accumulatedFactories );
}

/**Returns the accumulated lemin of a single player*/
function xstats_getLeminAccumulated( $gameId, $playerIndex ) {
    $accumulatedLemin = 0;
    //get the cololeminnists on planet surface
    $result = @mysql_query("SELECT lemin FROM skrupel_planeten WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedLemin = $accumulatedLemin+$row['lemin'];
    }
    //get onboard lemin
    $result = @mysql_query("SELECT lemin FROM skrupel_schiffe WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedLemin = $accumulatedLemin+$row['lemin'];
    }
    return( $accumulatedLemin );
}

/**Returns the accumulated boxes of a single player*/
function xstats_getBoxesAccumulated( $gameId, $playerIndex ) {
    $accumulatedBoxes = 0;
    //get the cololeminnists on planet surface
    $result = @mysql_query("SELECT vorrat FROM skrupel_planeten WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedBoxes = $accumulatedBoxes+$row['vorrat'];
    }
    //get onboard boxes
    $result = @mysql_query("SELECT fracht_vorrat FROM skrupel_schiffe WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedBoxes = $accumulatedBoxes+$row['fracht_vorrat'];
    }
    return( $accumulatedBoxes );
}


/**Returns the accumulated mineral of a single player*/
function xstats_getMineralAccumulated( $gameId, $playerIndex, $mineralIndex ) {
    $mineralName = "min".$mineralIndex;
    $accumulatedMineral = 0;
    //get the mineral on planet surface
    $result = @mysql_query("SELECT {$mineralName} FROM skrupel_planeten WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedMineral = $accumulatedMineral+$row[$mineralName];
    }
    //get onboard mineral
    $result = @mysql_query("SELECT fracht_{$mineralName} FROM skrupel_schiffe WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedMineral = $accumulatedMineral+$row['fracht_'.$mineralName];
    }
    return( $accumulatedMineral );
}

/**Returns the accumulated cantox of a single player*/
function xstats_getCantoxAccumulated( $gameId, $playerIndex) {
    $accumulatedCantox = 0;
    //get the colonists on planet surface
    $result = @mysql_query("SELECT cantox FROM skrupel_planeten WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedCantox = $accumulatedCantox+$row['cantox'];
    }
    //get onboard colonists
    $result = @mysql_query("SELECT fracht_cantox FROM skrupel_schiffe WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $accumulatedCantox = $accumulatedCantox+$row['fracht_cantox'];
    }
    return( $accumulatedCantox );
}

/**Returns the available cargo hold*/
function xstats_getSumCargoHold( $gameId, $playerIndex) {
    $cargoHold = 0;
    $result = @mysql_query("SELECT frachtraum FROM skrupel_schiffe WHERE energetik_anzahl=0 AND projektile_anzahl=0 AND hanger_anzahl=0 AND spiel='$gameId' AND besitzer='$playerIndex'") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $cargoHold = $cargoHold+$row['frachtraum'];
    }
    return( $cargoHold );
}

/**Returns the used cargo hold*/
function xstats_getUsedCargoHold( $gameId, $playerIndex) {
    $usedCargoHold = 0;
    $result = @mysql_query("SELECT * FROM skrupel_schiffe WHERE energetik_anzahl=0 AND projektile_anzahl=0 AND hanger_anzahl=0 AND spiel='$gameId' AND besitzer='$playerIndex'") or die(mysql_error());
    while ($row=mysql_fetch_array($result)) {
        $usedCargoHold = $usedCargoHold+(int)($row['fracht_leute']/100)+$row['fracht_vorrat']+$row['fracht_min1']+$row['fracht_min2']+$row['fracht_min3'];
    }
    return( $usedCargoHold );
}

/**Returns the number of freighters*/
function xstats_getFreighterCount( $gameId, $playerIndex) {
    $result = @mysql_query("SELECT COUNT(1) AS freightercount FROM skrupel_schiffe WHERE energetik_anzahl=0 AND projektile_anzahl=0 AND hanger_anzahl=0 AND spiel='$gameId' AND besitzer='$playerIndex'") or die(mysql_error());
    $result = @mysql_fetch_array($result);
    return $result['freightercount'];
}

/**Returns the planet count of a single player*/
function xstats_getPlanetCount( $gameId, $playerIndex ) {
    $planetCount = @mysql_query("SELECT COUNT(1) AS planetcount FROM skrupel_planeten WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    $planetCount = @mysql_fetch_array($planetCount);
    return $planetCount['planetcount'];
}


/**Returns the summ of all planets in the universe. This may change because planets could be destroyed*/
function xstats_getAllPlanetCount( $gameId ) {
    $allPlanetCount = @mysql_query("SELECT COUNT(1) AS allplanetcount FROM skrupel_planeten WHERE spiel=$gameId") or die(mysql_error());
    $allPlanetCount = @mysql_fetch_array($allPlanetCount);
    return $allPlanetCount['allplanetcount'];
}

/**Returns the starbase count of a single player*/
function xstats_getStarbaseCount( $gameId, $playerIndex ) {
    $starbaseCount = @mysql_query("SELECT COUNT(1) AS starbasecount FROM skrupel_sternenbasen WHERE spiel=$gameId AND besitzer=$playerIndex") or die(mysql_error());
    $starbaseCount = @mysql_fetch_array($starbaseCount);
    return $starbaseCount['starbasecount'];
}

/**Returns the starbase count of a single player of a single type
 * 0: builder starbase
 * 1: repair base
 * 2: warbase
 * 3: builder starbase + extra
 */
function xstats_getStarbaseCountOfType( $gameId, $playerIndex, $type ) {
    $starbaseCount = @mysql_query("SELECT COUNT(1) AS starbasecount FROM skrupel_sternenbasen WHERE spiel=$gameId AND besitzer=$playerIndex AND art=$type") or die(mysql_error());
    $starbaseCount = @mysql_fetch_array($starbaseCount);
    return $starbaseCount['starbasecount'];
}


/**Returns the actual skrupel rank of the player in the game in this turn*/
function xstats_getRank($gameId, $playerIndex) {
    $rank = @mysql_query("SELECT spieler_{$playerIndex}_platz AS rank FROM skrupel_spiele WHERE id=$gameId") or die(mysql_error());
    $rank = @mysql_fetch_array($rank);
    return $rank['rank'];
}


?>
