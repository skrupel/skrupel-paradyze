<?php
/**
 * Autor: SHE
 * Contains some util functions
 */

/**Returns the game id for a given game sid
 */
function xstats_gameSID2ID( $sid ) {
    $gameId = @mysql_query("SELECT id FROM skrupel_spiele WHERE sid='$sid'") or die(mysql_error());
    $gameId = @mysql_fetch_array($gameId);
    $gameId = $gameId['id'];
    return( $gameId );
}

/**Find out if a game is finished*/
function xstats_gameIsFinished( $gameId ) {
    $result = @mysql_query("SELECT * FROM skrupel_spiele WHERE id=$gameId") or die(mysql_error());
    $result = @mysql_fetch_array($result);
    return($result['phase']==1 || $result['spieleranzahl']<=1);
}

/**checks if xstats is installed*/
function xstats_isInstalled(){
    $result = @mysql_query("SHOW TABLES LIKE 'skrupel_xstats_version'") or die(mysql_error());
    return( @mysql_num_rows($result) == 1);
}

/**check if a game of this id exists in the stats*/
function xstats_gameExistsInStats($gameId){
    if( !xstats_isInstalled()){
        return( false );
    }
    $query = "SELECT COUNT(1) FROM skrupel_xstats WHERE gameid=".$gameId;
    $collectResult = @mysql_query($query) or die(mysql_error());
    $collectResult = @mysql_fetch_row($collectResult);
    return( $collectResult[0] == 1 );
}


/**Get the game name*/
function xstats_getGameName( $gameId ) {
    $result = @mysql_query("SELECT name FROM skrupel_spiele WHERE id=$gameId") or die(mysql_error());
    $result = @mysql_fetch_array($result);
    return $result['name'];
}

/**
 * Returns an array with available players for the passed game id
 */
function xstats_getAvailablePlayerIndicies($gameId) {
    $playerIndex = array();
    for($k=1; $k<=10; $k++) {
        $playerId = @mysql_query("SELECT spieler_{$k} FROM skrupel_spiele WHERE id=$gameId") or die(mysql_error());
        $playerId = @mysql_fetch_array($playerId);
        $playerId = $playerId['spieler_'.$k];
        if($playerId != 0) {
            $playerIndex[] = $k;
        }
    }
    return $playerIndex;
}


/**
 * Returns the 1-based player index for the passed player id
 */
function xstats_getPlayerIndex($gameId, $playerId) {
    for($k=1; $k<=10; $k++) {
        $xstatsQuery = "SELECT spieler_".$k." FROM skrupel_spiele WHERE id=".$gameId;
        $xstatsResult = @mysql_query($xstatsQuery) or die(mysql_error());
        $xstatsResult = @mysql_fetch_array($xstatsResult);
        if($xstatsResult['spieler_'.$k] == $playerId) {
            return( $k );
        }
    }
    return -1;
}

/**
 * Returns the player id for the passed player index
 */
function xstats_getPlayerId($gameId, $playerIndex) {
    $xstatsQuery = "SELECT spieler_".$playerIndex." FROM skrupel_spiele WHERE id=".$gameId;
    $xstatsResult = @mysql_query($xstatsQuery) or die(mysql_error());
    $xstatsResult = @mysql_fetch_array($xstatsResult);
    return($xstatsResult['spieler_'.$playerIndex]);
}



/**
 * Returns the nick of a player of a passed player id
 */
function xstats_getPlayerNick($playerId) {
    $nick = @mysql_query("SELECT nick FROM skrupel_user WHERE id=$playerId") or die(mysql_error());
    $nick = @mysql_fetch_array($nick);
    $nick = $nick['nick'];
    return( $nick );
}


/**
 * Returns an array with available players for the passed game id
 */
function xstats_getAvailablePlayerIds($gameId) {
    $playerArray = array();
    for($k=1; $k<=10; $k++) {
        $playerId = @mysql_query("SELECT spieler_{$k} FROM skrupel_spiele WHERE id='$gameId'") or die(mysql_error());
        $playerId = @mysql_fetch_array($playerId);
        $playerId = $playerId['spieler_'.$k];
        if($playerId != 0) {
            $playerArray[] = $playerId;
        }
    }
    return $playerArray;
}

/**Returns the max value of a column of a user*/
function xstats_getMaxValueOfUser($gameId, $colName, $userId) {
    $query = "SELECT MAX({$colName}) AS maxvalue FROM skrupel_xstats WHERE gameid=".$gameId." AND playerid=".$userId;
    $result = @mysql_query( $query );
    if( mysql_num_rows($result)>0 ) {
        $result = @mysql_fetch_array($result);
        return( $result['maxvalue']);
    }else {
        return( 0 );
    }
}


/**Returns the max value of a column*/
function xstats_getMaxValue($gameId, $colName) {
    $query = "SELECT MAX({$colName}) AS maxvalue FROM skrupel_xstats WHERE gameid=$gameId";
    $result = @mysql_query( $query );
    $result = @mysql_fetch_array($result);
    if( $result == NULL ) {
        return( 0 );
    }else {
        return( $result['maxvalue']);
    }
}

/**Returns the max turn for a passed game id*/
function xstats_getMaxTurn( $gameId ) {
    $query = "SELECT MAX(turn)AS maxturn FROM skrupel_xstats WHERE gameid='$gameId'";
    $maxTurn = @mysql_query($query) or die(mysql_error());
    $maxTurn = @mysql_fetch_array($maxTurn);
    return $maxTurn['maxturn'];
}

/**Returns the game size for a passed game id*/
function xstats_getGameSize( $gameId ) {
    $query = "SELECT umfang FROM skrupel_spiele WHERE id='$gameId'";
    $maxTurn = @mysql_query($query) or die(mysql_error());
    $maxTurn = @mysql_fetch_array($maxTurn);
    return $maxTurn['umfang'];
}

?>