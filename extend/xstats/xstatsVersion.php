<?php
/**
 * Autor: SHE
 * Contains version information
 */

/**Returns the version of the xstats package
 */
function xstats_getVersion() {
    if( !xstats_isInstalled()){
        return( '<strong> ist nicht initialisiert. <a href="xstatsInitialize.php" target="_new">Hier klicken zum Initialisieren</a></strong>');
    }
    $query = "SELECT version FROM skrupel_xstats_version WHERE id=(SELECT MAX(id) FROM skrupel_xstats_version)";
    $result = @mysql_query($query) or die(mysql_error());
    $result = @mysql_fetch_row($result);
    $result = $result[0];
    $version = "v".$result;
    return( $version );
}

/**Returns the version of the xstats package
 */
function xstats_getVersionStr() {
    $version = "<p class='statsver'>xstats ".xstats_getVersion().'</p>';
    return( $version );
}


?>