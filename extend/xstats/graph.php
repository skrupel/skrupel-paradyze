<?php
/**
 * Autor: SHE
 *
 * Display a graph
 */

$statsGameId=$_GET["gameid"];
if( is_null($statsGameId)) {
    die("Parameter gameid erwartet.");
}
$statsType=$_GET["type"];
if( is_null($statsType)) {
    die("Parameter col erwartet.");
}
$width=$_GET["width"];
if( is_null($width)) {
    $width = 500;
}
$height=$_GET["height"];
if( is_null($height)) {
    $height = 300;
}
if( $_GET["displayLegend"]=="FALSE") {
    $displayLegend = FALSE;
}else {
    $displayLegend = TRUE;
}
$statsGraphOutputType=$_GET["outputtype"];
if( is_null($statsGraphOutputType)) {
    $statsGraphOutputType = 'IMAGE';
}
if( !$statsGraphOutputType == 'IMAGE' && !$statsGraphOutputType == 'FLASH' ) {
    die( "outputtype muss eines von IMAGE, FLASH sein.");
}

include ('xstatsUtil.php');
include('phplot/phplot.php');
include ('../../inc.conf.php');
$conn = @mysql_connect($conf_database_server,$conf_database_login,$conf_database_password);
$db = @mysql_select_db($conf_database_database,$conn);
if( $statsGraphOutputType == 'IMAGE') {
    xstats_displayGraphImage( $statsGameId, $statsType, $displayLegend, $width,$height);
}else {
    xstats_displayGraphFlash( $statsGameId, $statsType, $width,$height);
}

/**Outputs a graph of the requested data as flash*/
function xstats_displayGraphFlash( $statsGameId, $statsType, $width, $height) {
    //contains player colors
    include ('../../inc.conf.php');
    $statsMaxTurn = xstats_getMaxTurn( $statsGameId );
    $statsAvailablePlayerIndicies = xstats_getAvailablePlayerIndicies( $statsGameId );
    $statsAvailablePlayerIds = xstats_getAvailablePlayerIds($statsGameId);
    if( $statsType == 'average_cargoholdpercent') {
        require("amcharts-php/lib/AmBarChart.php");
        AmChart::$swfObjectPath = "swfobject.js";
        AmChart::$libraryPath = "amcharts-php/amcolumn";
        AmChart::$jQueryPath = "amcharts-php/examples/jquery.js";
        $chart = new AmBarChart($statsType);
    }else {
        require("amcharts-php/lib/AmLineChart.php");
        AmChart::$swfObjectPath = "swfobject.js";
        AmChart::$libraryPath = "amcharts-php/amline";
        AmChart::$jQueryPath = "amcharts-php/examples/jquery.js";
        $chart = new AmLineChart($statsType);
    }
    //configure the chart
    $chart->setConfig("width", $width);
    $chart->setConfig("height", $height);
    $chart->setConfig( "values.y_left.integers_only", "true");
    $chart->setConfig( "grid.y_left.fill_color", "666666");
    $chart->setConfig( "grid.y_left.fill_alpha", "9");
    //only relevant for bar charts
    $chart->setConfig( "depth", 30 );
    //only relevant for bar charts
    $chart->setConfig( "column.grow_time", 3 );
    //only relevant for bar charts
    $chart->setConfig( "column.grow_effect", "regular" );
    //graph options to display the graph later
    $graphOptionArray = array();
    $graphOptionArray["line_width"] = 2;
    if( xstats_statsTypeIsAll($statsType) ||
            $statsType == "stationcount" ||
            $statsType == "stationcountstd" ||
            $statsType == "stationcountrep" ||
            $statsType == "stationcountdef" ||
            $statsType == "stationcountstdextra" ||
            $statsType == 'planetcountpercent' ||
            $statsType == 'battlewoncount' ||
            $statsType == 'coloniestakencount' ||
            $statsType == 'battlelostcount') {
        //display as areas
        $chart->setConfig( "axes.y_left.type", "stacked");
        //fill the areas
        $graphOptionArray["fill_alpha"] = 100;
        if( $statsType == 'battlelostcount' ||
                $statsType == 'battlewoncount' ||
                $statsType == 'coloniestakencount' ) {
            $graphOptionArray["line_width"] = 0;
        }
    }
    $labelX = xstats_getGraphTitle( $statsType );
    // \n is not allowed here..
    $labelX = str_replace("\n", "", $labelX);
    $chart->addLabel('<b>'.$labelX.'</b>', 0, 20,
            array( "align" => "center", "text_size" => "20"));
    $chart->addLabel('<b>'.xstats_getGraphYTitle($statsType).'</b>', 0, 100,
            array( "rotate" => "true"));
    $chart->addLabel('<b>Runde</b>', 0, $height-20, array( "align" => "right"));
    $dataStatsType = $statsType;
    if( xstats_statsTypeIsAverage($statsType)) {
        $dataStatsType = substr( $statsType, 8);
    }
    if( xstats_statsTypeIsAll($statsType)) {
        $dataStatsType = substr( $statsType, 4);
    }
    if( $dataStatsType=='cargoholdpercent') {
        $playerData = xstats_collectDataPercent( $statsGameId, 'usedcargohold', 'sumcargohold', $statsMaxTurn, $statsAvailablePlayerIndicies);
    }else if($dataStatsType=='planetcountpercent') {
        $playerData = xstats_collectDataPercent( $statsGameId, 'planetcount', 'allplanetcount', $statsMaxTurn, $statsAvailablePlayerIndicies);
    }else if($dataStatsType=='battlelostcount') {
        $playerData = xstats_collectDataSub( $statsGameId, 'battlecount', 'battlewoncount', $statsMaxTurn, $statsAvailablePlayerIndicies);
    }else if($dataStatsType=='fightercount') {
        $playerData = xstats_collectDataSub( $statsGameId, 'shipcount', 'freightercount', $statsMaxTurn, $statsAvailablePlayerIndicies);
    }else {
        $playerData = xstats_collectData( $statsGameId, $dataStatsType, $statsMaxTurn, $statsAvailablePlayerIndicies);
    }
    if( xstats_statsTypeIsAverage($statsType)) {
        $playerData = xstats_computeAverage( $playerData, $statsAvailablePlayerIds );
        $chart->setConfig("legend.enabled", "false");
    }
    for($i=0; $i<count($playerData); $i++) {
        $chart->addSerie($i, $playerData[$i][0]);
    }
    //display only if game is finished
    if( xstats_gameIsFinished($statsGameId)) {
        if( xstats_statsTypeIsAverage($statsType)) {
        }
        for( $i=0; $i<count($statsAvailablePlayerIndicies); $i++) {
            $dataArray = array();
            for( $j=0;$j<count($playerData);$j++) {
                $dataArray[] = $playerData[$j][$i+1];
            }
            $graphOptionArray["color"] = $spielerfarbe[$statsAvailablePlayerIndicies[$i]];
            $chart->addGraph(xstats_getPlayerNick($statsAvailablePlayerIds[$i]),
                    xstats_getPlayerNick($statsAvailablePlayerIds[$i]),
                    $dataArray, $graphOptionArray );
        }
    }
    echo $chart->getCode();
}

/**Outputs a graph of the requested data as image*/
function xstats_displayGraphImage( $statsGameId, $statsType, $displayLegend, $width, $height) {
    $statsMaxTurn = xstats_getMaxTurn( $statsGameId );
    $statsAvailablePlayerIndicies = xstats_getAvailablePlayerIndicies( $statsGameId );
    $statsPlayerColors = array();
    foreach( $statsAvailablePlayerIndicies as $playerIndex ) {
        $statsPlayerColors[] = xstats_getPHPlotColorForPlayer($playerIndex);
    }
    $statsLegend = array();
    $statsAvailablePlayerIds = xstats_getAvailablePlayerIds($statsGameId);
    foreach( $statsAvailablePlayerIds as $playerId ) {
        $statsLegend[] = xstats_getPlayerNick($playerId);
    }
    //initialize graph
    $statsPlot = new PHPlot($width,$height);    
    $statsPlot->SetPlotAreaBgImage( 'images/stats_background.jpg', 'scale' );
    $statsPlot->SetImageBorderType('plain');
    $statsPlot->SetYTitle(xstats_getGraphYTitle($statsType));
    $statsPlot->SetBackgroundColor('white');
    $statsPlot->SetDataColors($statsPlayerColors);
    //bar for average type, line for normal type
    if( xstats_statsTypeIsAverage($statsType)) {
        $statsPlot->SetPlotType('bars');
        $statsPlot->SetYDataLabelPos('plotin');
        $statsPlot->SetPrecisionY(1);
        $statsPlot->SetPlotAreaWorld(NULL, 0);
        $statsPlot->SetYTickLabelPos('none');
        $statsPlot->SetYTickPos('none');
    }else {
        $statsPlot->SetXTitle('Runde');
        if( $displayLegend) {
            $statsPlot->SetLegend($statsLegend);
            $statsPlot->SetLegendPixels(80, 30);
        }
        if( xstats_statsTypeIsAll($statsType) ||
                $statsType == 'stationcount' ||
                $statsType == "stationcountstd" ||
                $statsType == "stationcountrep" ||
                $statsType == "stationcountdef" ||
                $statsType == "stationcountstdextra" ||
                $statsType == 'planetcountpercent' ||
                $statsType == 'battlewoncount' ||
                $statsType == 'coloniestakencount' ||
                $statsType == 'battlelostcount') {
            $statsPlot->SetPlotType('stackedbars');
        }else {
            $statsPlot->SetPlotType('lines');
            $statsPlot->SetLineWidths(3);
        }
    }
    //display finished data only
    if( xstats_gameIsFinished($statsGameId)) {
        $dataStatsType = $statsType;
        if( xstats_statsTypeIsAverage($statsType)) {
            $dataStatsType = substr( $statsType, 8);
        }
        if( xstats_statsTypeIsAll($statsType)) {
            $dataStatsType = substr( $statsType, 4);
        }
        if( $dataStatsType=='cargoholdpercent') {
            $playerData = xstats_collectDataPercent( $statsGameId, 'usedcargohold', 'sumcargohold', $statsMaxTurn, $statsAvailablePlayerIndicies);
        }else if($dataStatsType=='planetcountpercent') {
            $playerData = xstats_collectDataPercent( $statsGameId, 'planetcount', 'allplanetcount', $statsMaxTurn, $statsAvailablePlayerIndicies);
        }else if($dataStatsType=='battlelostcount') {
            $playerData = xstats_collectDataSub( $statsGameId, 'battlecount', 'battlewoncount', $statsMaxTurn, $statsAvailablePlayerIndicies);
        }else if($dataStatsType=='fightercount') {
            $playerData = xstats_collectDataSub( $statsGameId, 'shipcount', 'freightercount', $statsMaxTurn, $statsAvailablePlayerIndicies);
        }else {
            $playerData = xstats_collectData( $statsGameId, $dataStatsType, $statsMaxTurn, $statsAvailablePlayerIndicies);
        }
        if( xstats_statsTypeIsAverage($statsType)) {
            $statsPlot->SetDataValues(xstats_computeAverage( $playerData, $statsAvailablePlayerIds ));
        }else {
            $statsPlot->SetDataValues($playerData);
        }
        $statsYTickIncrement = xstats_getYTickIncrement($statsType, $statsGameId);
        if( $statsYTickIncrement != NULL ) {
            $statsPlot->SetYTickIncrement($statsYTickIncrement);
        }
    }
    $statsPlot->SetTitle(xstats_getGraphTitle($statsType));
    $statsPlot->SetXTickLabelPos('none');
    $statsPlot->SetXTickPos('none');
    //scale title, ticks etc if image is to small
    if( $width < 400 ) {
        $statsPlot->SetFontGD('title', 3);
        $statsPlot->SetXDataLabelPos('none');
    }
    $statsPlot->DrawGraph();
}

/**Tries to map the graph lib colors to the player colors*/
function xstats_getPHPlotColorForPlayer( $playerIndex ) {
    if( $playerIndex == 1 ) {
        return( "YellowGreen");
    }
    if( $playerIndex == 2 ) {
        return( "yellow");
    }
    if( $playerIndex == 3 ) {
        return( "gold");
    }
    if( $playerIndex == 4 ) {
        return( "peru");
    }
    if( $playerIndex == 5 ) {
        return( "red");
    }
    if( $playerIndex == 6 ) {
        return( "magenta");
    }
    if( $playerIndex == 7 ) {
        return( "purple");
    }
    if( $playerIndex == 8 ) {
        return( "blue");
    }
    if( $playerIndex == 9 ) {
        return( "SkyBlue");
    }
    if( $playerIndex == 10 ) {
        return( "aquamarine1");
    }
    return( "black");
}

/**Computes an array with average data of the passed data array*/
function xstats_computeAverage( $playerDataArray, $statsAvailablePlayerIds ) {
    $averageData = array();
    for( $i=0;$i<count($statsAvailablePlayerIds);$i++ ) {
        $averageValue = 0;
        $valueCount = 0;
        foreach( $playerDataArray as $singlePointArray) {
            $averageValue = $averageValue + $singlePointArray[$i+1];
            $valueCount++;
        }
        if( $valueCount > 0 ) {
            $averageValue = $averageValue/$valueCount;
        }
        $singlePoint = array();
        $singlePoint[] = xstats_getPlayerNick($statsAvailablePlayerIds[$i]);
        for( $j=0;$j<count($statsAvailablePlayerIds);$j++) {
            if( $j==$i) {
                $singlePoint[] = $averageValue;
            }else {
                $singlePoint[] = 0;
            }
        }
        $averageData[] = $singlePoint;
    }
    return( $averageData);
}

/**Returns if the statsType starts with the string "average_"*/
function xstats_statsTypeIsAverage( $statsType ) {
    return( strlen($statsType) > 8 && substr( $statsType, 0, 8) == 'average_');
}

/**Returns if the statsType starts with the string "all_"*/
function xstats_statsTypeIsAll( $statsType ) {
    return( strlen($statsType) > 4 && substr( $statsType, 0, 4) == 'all_');
}


/**Returns a data array that should be displayed where the percentage value from the first type of the second type is returned*/
function xstats_collectDataPercent( $gameId, $colName1, $colName2, $maxTurn, $statsAvailablePlayerIndicies ) {
    $statsData = array();
    for($currentTurn=1; $currentTurn<=$maxTurn; $currentTurn++) {
        $singlePoint = array();
        $singlePoint[] = $currentTurn;
        foreach( $statsAvailablePlayerIndicies as $playerIndex ) {
            $query = "SELECT {$colName1},{$colName2} FROM skrupel_xstats WHERE playerindex=$playerIndex AND gameid=$gameId AND turn={$currentTurn}";
            $result = @mysql_query($query) or die(mysql_error());
            $result = @mysql_fetch_array($result);
            $value1 = $result[$colName1];
            $value2 = $result[$colName2];
            if( is_null( $value2 )) {
                $singlePoint[] = 0;
            }else {
                $singlePoint[] = ($value1*100)/$value2;
            }
        }
        $statsData[] = $singlePoint;
    }
    return $statsData;
}

/**Returns a data array that should be displayed where the first value minus the second value is returned*/
function xstats_collectDataSub( $gameId, $colName1, $colName2, $maxTurn, $statsAvailablePlayerIndicies ) {
    $statsData = array();
    for($currentTurn=1; $currentTurn<=$maxTurn; $currentTurn++) {
        $singlePoint = array();
        $singlePoint[] = $currentTurn;
        foreach( $statsAvailablePlayerIndicies as $playerIndex ) {
            $query = "SELECT {$colName1},{$colName2} FROM skrupel_xstats WHERE playerindex=$playerIndex AND gameid=$gameId AND turn={$currentTurn}";
            $result = @mysql_query($query) or die(mysql_error());
            $result = @mysql_fetch_array($result);
            $value1 = $result[$colName1];
            $value2 = $result[$colName2];
            if( is_null( $value2 )) {
                $value2=0;
            }
            if( is_null( $value1 )) {
                $value1=0;
            }
            $singlePoint[] = $value1-$value2;
        }
        $statsData[] = $singlePoint;
    }
    return $statsData;
}


/**Returns a data array that should be displayed for a list of players*/
function xstats_collectData( $gameId, $colName, $maxTurn, $statsAvailablePlayerIndicies ) {
    $statsData = array();
    for($currentTurn=1; $currentTurn<=$maxTurn; $currentTurn++) {
        $singlePoint = array();
        //display every 5th round on the axis
        if( $currentTurn %5 == 0 ){
            $singlePoint[] = $currentTurn;
        }else{
            $singlePoint[] = " ";
        }
        foreach( $statsAvailablePlayerIndicies as $playerIndex ) {
            $query = "SELECT {$colName} FROM skrupel_xstats WHERE playerindex=$playerIndex AND gameid=$gameId AND turn={$currentTurn}";
            $result = @mysql_query($query) or die(mysql_error());
            $result = @mysql_fetch_array($result);
            $result = $result[$colName];
            if( is_null( $result )) {
                $singlePoint[] = 0;
            }else {
                $singlePoint[] = $result;
            }
        }
        $statsData[] = $singlePoint;
    }
    return $statsData;
}


/**Get the inc of the Y axis depending on the graph type. Do not set the value if NULL
 * is returned
 */
function xstats_getYTickIncrement($graphType, $gameId) {
    if( $graphType == "stationcount" || $graphType == "stationcountstd"
            || $graphType == "stationcountrep" || $graphType == "stationcountdef"
            || $graphType == "stationcountstdextra" ) {
        return( 1);
    }
    if( strpos($graphType,"percent") > 0 ) {
        return( 10);
    }
    if( $graphType=="rank") {
        return( 1);
    }
    if( !xstats_statsTypeIsAverage($graphType) && !xstats_statsTypeIsAll($graphType)) {
        $maxValue = xstats_getMaxValue($gameId, $graphType);
        $tick = $maxValue/10;
        $scale = 1;
        //possible scale values
        $scaleArray = array(1,2,5,10,20,25,50,100,200,250,500,1000,2000,2500,5000,10000,20000,25000,50000,100000,200000);
        for( $i=0;$i<count($scaleArray);$i++) {
            if( $tick < $scaleArray[$i]) {
                $scale = $scaleArray[$i];
                break;
            }
        }
        return( $scale);
    }
    return( NULL );
}


/**Computes the y title of the graph depending on the grapg type*/
function xstats_getGraphYTitle( $graphType ) {
    if( xstats_statsTypeIsAverage($graphType)) {
        return( xstats_getGraphYTitle(substr( $graphType, 8)));
    }
    if( xstats_statsTypeIsAll($graphType)) {
        return( xstats_getGraphYTitle(substr( $graphType, 4)));
    }
    if( $graphType=="shipmasscount") {
        return( "KT");
    }
    if( $graphType=="shipcount") {
        return( "Schiffe");
    }
    if( $graphType=="planetcount") {
        return( "Planeten");
    }
    if( $graphType=="planetcountpercent") {
        return( "Prozent");
    }
    if( $graphType=="colonistcount") {
        return( "Kolonisten");
    }
    if( $graphType == "stationcount" || $graphType == "stationcountstd"
            || $graphType == "stationcountrep" || $graphType == "stationcountdef"
            || $graphType == "stationcountstdextra" ) {
        return( "Menge");
    }
    if( $graphType=="lemincount") {
        return( "KT");
    }
    if( $graphType=="min1count") {
        return( "KT");
    }
    if( $graphType=="min2count") {
        return( "KT");
    }
    if( $graphType=="min3count") {
        return( "KT");
    }
    if( $graphType=="cantoxcount") {
        return( "Cantox");
    }
    if( $graphType=="minescount") {
        return( "Anzahl");
    }
    if( $graphType=="factorycount") {
        return( "Fabriken");
    }
    if( $graphType=="sumcargohold") {
        return( "KT");
    }
    if( $graphType=="usedcargohold") {
        return( "KT");
    }
    if( $graphType=="cargoholdpercent") {
        return( "Prozent");
    }
    if( $graphType=="freightercount") {
        return( "Schiffe");
    }
    if( $graphType=="fightercount") {
        return( "Schiffe");
    }
    if( $graphType=="boxcount") {
        return( "Kisten");
    }
    if( $graphType=="rank") {
        return( "Position");
    }
    if( $graphType=="lj") {
        return( "Lichtjahre");
    }
    if( $graphType=="battlewoncount") {
        return( "Anzahl");
    }
    if( $graphType=="battlelostcount") {
        return( "Anzahl");
    }
    if( $graphType=="coloniestakencount") {
        return( "Anzahl");
    }
    return( "");
}

/**Computes the title of the grapg depending on the graph type*/
function xstats_getGraphTitle( $graphType ) {
    if( xstats_statsTypeIsAverage($graphType)) {
        return( xstats_getGraphTitle(substr( $graphType, 8))." [Durchschnitt]");
    }
    if( xstats_statsTypeIsAll($graphType)) {
        return( xstats_getGraphTitle(substr( $graphType, 4))." [Gesamt]");
    }
    if( $graphType=="shipmasscount") {
        return( "Masse der Schiffe");
    }
    if( $graphType=="shipcount") {
        return( "Flotte");
    }
    if( $graphType=="planetcount") {
        return( "Imperium");
    }
    if( $graphType=="planetcountpercent") {
        return( "Besiedeltes Universum");
    }
    if( $graphType=="colonistcount") {
        return( "Bevoelkerung");
    }
    if( $graphType=="stationcount") {
        return( "Raumstationen (Gesamt)");
    }
    if( $graphType=="stationcountstd") {
        return( "Raumstationen (Sternenbasis)");
    }
    if( $graphType=="stationcountrep") {
        return( "Raumstationen (Raumwerft)");
    }
    if( $graphType=="stationcountdef") {
        return( "Raumstationen (Kampfstation)");
    }
    if( $graphType=="stationcountstdextra") {
        return( "Raumstationen (Kriegsbasis)");
    }
    if( $graphType=="lemincount") {
        return( "Verfuegbares Lemin");
    }
    if( $graphType=="min1count") {
        return( "Verfuegbares Baxterium");
    }
    if( $graphType=="min2count") {
        return( "Verfuegbares Rennurbin");
    }
    if( $graphType=="min3count") {
        return( "Verfuegbares Vomisaan");
    }
    if( $graphType=="cantoxcount") {
        return( "Reichtum");
    }
    if( $graphType=="minescount") {
        return( "Minen");
    }
    if( $graphType=="factorycount") {
        return( "Produktionsstaetten");
    }
    if( $graphType=="sumcargohold") {
        return( "Verfuegbarer Frachtraum\n(nur auf Frachtern)");
    }
    if( $graphType=="usedcargohold") {
        return( "Benutzter Frachtraum\n(nur auf Frachtern)");
    }
    if( $graphType=="cargoholdpercent") {
        return( "Frachtraumauslastung\n(nur bei Frachtern)");
    }
    if( $graphType=="freightercount") {
        return( "Flotte (Frachter)");
    }
    if( $graphType=="fightercount") {
        return( "Flotte (Bewaffnet)");
    }
    if( $graphType=="boxcount") {
        return( "Vorraete");
    }
    if( $graphType=="rank") {
        return( "Rang Verlauf");
    }
    if( $graphType=="lj") {
        return( "Geflogene Distanz");
    }
    if( $graphType=="battlewoncount") {
        return( "Gewonnene Raumkaempfe");
    }
    if( $graphType=="battlelostcount") {
        return( "Verlorene Raumkaempfe");
    }
    if( $graphType=="coloniestakencount") {
        return( "Kolonien erobert");
    }
    if( $graphType=="allshipcount") {
        return( "Flotte (Gesamt)");
    }
    if( $graphType=="allfreightercount") {
        return( "Flotte (Frachter, Gesamt)");
    }
    if( $graphType=="allfigthercount") {
        return( "Flotte (Bewaffnet, Gesamt)");
    }
    return( "Unbekannt");
}

?>
