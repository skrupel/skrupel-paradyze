<?php
/**
 * Autor: SHE
 * 
 * Delete a game from the stats table
 */
@mysql_query("DELETE FROM skrupel_xstats_shipvsplanet WHERE gameid=".$spiel );
@mysql_query("DELETE FROM skrupel_xstats_shipowner WHERE gameid=".$spiel );
@mysql_query("DELETE FROM skrupel_xstats_shipvsship WHERE gameid=".$spiel );
@mysql_query("DELETE FROM skrupel_xstats WHERE gameid=".$spiel );
@mysql_query("DELETE FROM skrupel_xstats_ships WHERE gameid=".$spiel );
@mysql_query("DELETE FROM skrupel_xstats_turntime WHERE gameid=".$spiel );


?>
