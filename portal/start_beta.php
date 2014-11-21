<?php

require "include/global.php";

if (!$_SESSION["user_id"]) {
    portal_error_message('Du musst eingelogt sein, um ein Spiel erstellen zu können.');
}

if (!is_numeric($_GET["game_id"])) {
    portal_error_message('Es wurde keine Spiel-ID angegeben.');
}

$sql = "SELECT spiel_name, spieler_admin, user_1, user_2, user_3, user_4, user_5, user_6, user_7, user_8, user_9, user_10 FROM skrupel_waiting_games WHERE id = {$_GET["game_id"]}";
$spiel = mysql_fetch_assoc(mysql_query($sql));
echo mysql_error();

if ($_SESSION["user_id"] != $spiel["spieler_admin"]) {
    portal_error_message('Du versuchst ein Spiel zu starten das nicht von dir erstellt wurde.');
}
for ($i = 1; $i <= 10; $i++) {
    if ($spiel["user_" . $i] == -1) {
        $sql = "UPDATE skrupel_waiting_games SET user_$i = 0 WHERE id = {$_GET["game_id"]} AND user_$i = -1";
        mysql_query($sql);
    }
}
$mailsto = "";
for ($i = 1; $i <= 10; $i++) {
    if ($spiel["user_" . $i] > 0) {
        $mailsto .= ($mailsto != "" ? "," : "") . $spiel["user_{$i}"];
    }
}

$sql = "DELETE FROM skrupel_waiting_games WHERE id = {$_GET["game_id"]}";
mysql_query($sql);

//


$sql = "SELECT * FROM skrupel_waiting_games WHERE id = {$_GET["game_id"]}";
$query = mysql_query($sql);
if (mysql_num_rows($query) == 0) {
    portal_error_message('Konnte das Spiel nicht finden!');
}

$data = mysql_fetch_assoc($query);
if (!in_array($_SESSION["user_id"], array($data["user_1"],
        $data["user_2"],
        $data["user_3"],
        $data["user_4"],
        $data["user_5"],
        $data["user_6"],
        $data["user_7"],
        $data["user_8"],
        $data["user_9"],
        $data["user_10"]))) {
    //TODO
    die("Fehler");
}

//


$zeiger = @mysql_query("SELECT extend,serial FROM skrupel_info");
$array = @mysql_fetch_array($zeiger);
$spiel_extend = $array["extend"];
$spiel_serial = $array["serial"];

//////////////////////////////////////

$sid = random_string();
$spielname = $data["spiel_name"];
$spielname = str_replace("'", " ", $spielname);
$spielname = str_replace('"', " ", $spielname);


$ziel_id = $data["siegbedingungen"];
$umfang = $data["umfang"];
$struktur = $data["struktur"];

$piraten_mitte = $data["piraten_mitte"];
$piraten_aussen = $data["piraten_aussen"];
$piraten_min = $data["piraten_min"];
$piraten_max = $data["piraten_max"];
$out = $data["out"];

if ($ziel_id == 6) {
    $team[1] = $data["team1"];
    $team[2] = $data["team2"];
    $team[3] = $data["team3"];
    $team[4] = $data["team4"];
    $team[5] = $data["team5"];
    $team[6] = $data["team6"];
    $team[7] = $data["team7"];
    $team[8] = $data["team8"];
    $team[9] = $data["team9"];
    $team[10] = $data["team10"];
}

$module = array();
$module[0] = @intval($data["modul_0"]);
$module[1] = 0;
$module[2] = @intval($data["modul_2"]);
$module[3] = @intval($data["modul_3"]);
$module[4] = @intval($data["modul_4"]);
$module[5] = @intval($data["modul_5"]);
$module[6] = @intval($data["modul_6"]);
$module = @implode(":", $module);

$zeiger = @mysql_query("INSERT INTO skrupel_spiele (sid,name,module,oput) values ('$sid','$spielname','$module',$out)");

$zeiger = @mysql_query("SELECT id,sid FROM skrupel_spiele where sid='$sid'");
$array = @mysql_fetch_array($zeiger);
$spiel = $array["id"];

$aufloesung = 50;

$daten_verzeichnis = "../daten/";

$handle = opendir("$daten_verzeichnis");

while ($rasse = readdir($handle)) {
    if ((substr($rasse, 0, 1) <> '.') and (substr($rasse, 0, 7) <> 'bilder_') and (substr($rasse, strlen($rasse) - 4, 4) <> '.txt')) {

        $daten = "";
        $attribute = "";

        $file = $daten_verzeichnis . $rasse . '/daten.txt';
        $fp = @fopen("$file", "r");
        if ($fp) {
            $zaehler2 = 0;
            while (!feof($fp)) {
                $buffer = @fgets($fp, 4096);
                $daten[$zaehler2] = $buffer;
                $zaehler2++;
            }
            @fclose($fp);
        }

        $namerassen[$rasse] = trim($daten[0]);
        $nameplanet[$rasse] = trim($daten[5]);
    }
}


///////////////////////////////////////////////////SPEZIEN
$speziena = 0;
if ($data["spezien"] >= 1) {

    $file = '../daten/dom_spezien.txt';
    $fp = @fopen("$file", "r");
    if ($fp) {
        while (!feof($fp)) {
            $buffer = @fgets($fp, 4096);
            $ur[$speziena] = explode(":", $buffer);
            $speziena++;
        }
        @fclose($fp);
    }

    $file = '../daten/dom_spezien_art.txt';
    $fp = @fopen("$file", "r");
    if ($fp) {
        while (!feof($fp)) {
            $buffer = @fgets($fp, 4096);
            $art_b = explode(":", $buffer);
            $art[$art_b[0]] = $art_b[1];
        }
        @fclose($fp);
    }
}
////////////////////////////////////////////////////FAVPLANETEN ANFANG
$daten_verzeichnis = "../daten/";

$handle = opendir("$daten_verzeichnis");

while ($rasses = readdir($handle)) {
    if ((substr($rasses, 0, 1) <> '.') and (substr($rasses, 0, 7) <> 'bilder_') and (substr($rasses, strlen($rasses) - 4, 4) <> '.txt')) {

        $daten = "";
        $attribute = "";

        $file = $daten_verzeichnis . $rasses . '/daten.txt';
        $fp = @fopen("$file", "r");
        if ($fp) {
            $zaehler2 = 0;
            while (!feof($fp)) {
                $buffer = @fgets($fp, 4096);
                $daten[$zaehler2] = $buffer;
                $zaehler2++;
            }
            @fclose($fp);
        }

        $attribute = explode(":", $daten[3]);

        $r_eigenschaften[$rasses]['temperatur'] = $attribute[0];
        $r_eigenschaften[$rasses]['planet'] = $attribute[1];
    }
}
////////////////////////////////////////////////////FAVPLANETEN ENDE
///////////////////////////////////////////////ERLAUBTE BEREICHE

$file = '../daten/bilder_galaxien/' . $struktur . '.txt';
$fp = @fopen("$file", "r");
if ($fp) {
    $zaehler = 0;
    while (!feof($fp)) {
        $buffer = @fgets($fp, 4096);
        $raum[$zaehler] = trim($buffer);
        //echo  $raum[$zaehler];
        $zaehler++;
    }
    @fclose($fp);
}


///////////////////////////////////////////////PLANETEN GENRERIEREN ANFANG

$planetenname = @file('../daten/planetennamen.txt');
shuffle($planetenname);

$sternendichte = $data["sternendichte"];
$startposition = $data["startposition"];




for ($i = 0; $i < $sternendichte; $i++) {
//for ($i=0;$i<50;$i++) {

    $ok = 1;
    $full_zahl = 0;
    while ($ok == 1) {

        $oke = 1;
        while ($oke == 1) {

            $x = rand(50, $umfang - 51);
            $y = rand(50, $umfang - 51);

            $test_x = round($x * 50 / $umfang);
            $test_y = round($y * 50 / $umfang);

            if ($raum[$test_y][$test_x] == 1) {
                $oke = 2;
            }
        }

        //echo $raum[$test_y][$test_x].":".$test_x.":".$test_y."<br>";

        $oben = $y - 55;
        $unten = $y + 55;
        $links = $x - 55;
        $rechts = $x + 55;

        $ok = 2;
        $nachbarn = 0;
        $zeiger2 = @mysql_query("SELECT count(*) as total from skrupel_planeten where x_pos>$links and x_pos<$rechts and y_pos>$oben and y_pos<$unten and spiel=$spiel");
        $array = @mysql_fetch_array($zeiger2);
        $nachbarn = $array["total"];

        if ($nachbarn >= 1) {
            $ok = 1;
            $full_zahl++;
            if ($full_zahl >= 35) {
                break 2;
            }
        }
    }


    $name = trim($planetenname[$i]);
    $name = preg_replace("/\r\n|\n\r|\n|\r/", "", $name);

    $klasse = rand(1, 9);

    if ($klasse == 1) {
        $bild = rand(1, 9);
        $temp = rand(40, 60);
    }  //Klasse M wie Erde
    if ($klasse == 2) {
        $bild = rand(1, 24);
        $temp = rand(30, 50);
    }  //Klasse N Wasserwelt
    if ($klasse == 3) {
        $bild = rand(1, 16);
        $temp = rand(0, 10);
    }  //Klasse J wie Luna
    if ($klasse == 4) {
        $bild = rand(1, 14);
        $temp = rand(50, 75);
    }  //Klasse L warm nur wenig Wasseroberflaeche
    if ($klasse == 5) {
        $bild = rand(1, 11);
        $temp = rand(86, 100);
    }  //Klasse G Wuestenplanet
    if ($klasse == 6) {
        $bild = rand(1, 22);
        $temp = rand(70, 95);
    }  //Klasse I Heiss giftige Gase
    if ($klasse == 7) {
        $bild = rand(1, 13);
        $temp = rand(75, 90);
    }   //Klasse C Heiss wie Venus
    if ($klasse == 8) {
        $bild = rand(1, 33);
        $temp = rand(20, 35);
    }  //Klasse K wie Mars
    if ($klasse == 9) {
        $bild = rand(1, 9);
        $temp = rand(25, 45);
    }    //Klasse F jung zerklueftet

    $lemin = rand(1, 70);
    $min1 = rand(1, 70);
    $min2 = rand(1, 70);
    $min3 = rand(1, 70);

    if ($data["mineralien"] == 1) {
        $minrand = 1000;
        $maxrand = 7000;
    }
    if ($data["mineralien"] == 2) {
        $minrand = 800;
        $maxrand = 5000;
    }
    if ($data["mineralien"] == 3) {
        $minrand = 500;
        $maxrand = 3500;
    }
    if ($data["mineralien"] == 4) {
        $minrand = 100;
        $maxrand = 1500;
    }

    $rohstoff = rand($minrand, $maxrand);
    $rohstoffe[0] = rand(0, $rohstoff);
    $rohstoffe[1] = rand(0, ($rohstoff - $rohstoffe[0]));
    $rohstoffe[2] = rand(0, ($rohstoff - $rohstoffe[0] - $rohstoffe[1]));
    $rohstoffe[3] = ($rohstoff - $rohstoffe[0] - $rohstoffe[1] - $rohstoffe[2]);

    shuffle($rohstoffe);

    $planet_lemin = $rohstoffe[0];
    $planet_min1 = $rohstoffe[1];
    $planet_min2 = $rohstoffe[2];
    $planet_min3 = $rohstoffe[3];

    $konz_lemin = rand(1, 5);
    $konz_min1 = rand(1, 5);
    $konz_min2 = rand(1, 5);
    $konz_min3 = rand(1, 5);

    if ($data["leminvorkommen"] == 2) {
        $zulemin = (rand(25, 50) + 100) / 100;
        $planet_lemin = round($planet_lemin * $zulemin);
        $zulemin = (rand(25, 50) + 100) / 100;
        $lemin = round($lemin * $zulemin);
    }
    if ($data["leminvorkommen"] == 3) {
        $zulemin = (rand(50, 100) + 100) / 100;
        $planet_lemin = round($planet_lemin * $zulemin);
        $zulemin = (rand(50, 100) + 100) / 100;
        $lemin = round($lemin * $zulemin);
    }

    $artefakt = rand(1, 100);
    if ($artefakt > 6) {
        $artefakt = 0;
    }

    $native_id = 0;
    $native_name = '';
    $native_art = 0;
    $native_art_name = '';
    $native_abgabe = 0;
    $native_bild = '';
    $native_text = '';
    $native_fert = '';
    $native_kol = 0;

    if ($data["spezien"] >= 1) {
        $zufall = rand(1, 100);
        if ($zufall <= $data["spezien"]) {
            $zufall_art = rand(0, $speziena - 1);

            $native_id = $ur[$zufall_art][1];
            $native_name = $lang['admin']['spiel']['alpha']['spezien'][$ur[$zufall_art][1]]['name'];
            $native_art = $ur[$zufall_art][3];
            $bla = $ur[$zufall_art][3];
            $native_art_name = trim($art[$bla]);
            $native_abgabe = $ur[$zufall_art][4];
            $native_bild = $ur[$zufall_art][2];
            $native_text = trim($lang['admin']['spiel']['alpha']['spezien'][$ur[$zufall_art][1]]['effekt']);
            $native_fert = $ur[$zufall_art][5];

            $native_kol = (rand(2000, 17500));
        }
    }

    $slots = rand(1, 6);

    $zeiger = mysql_query("INSERT into skrupel_planeten (osys_anzahl,native_id,native_name,native_art,native_art_name,native_abgabe,native_bild,native_text,native_fert,native_kol,name,x_pos,y_pos,klasse,bild,temp,lemin,min1,min2,min3,planet_lemin,planet_min1,planet_min2,planet_min3,konz_lemin,konz_min1,konz_min2,konz_min3,spiel,artefakt) values ($slots,$native_id,'$native_name',$native_art,'$native_art_name',$native_abgabe,'$native_bild','$native_text','$native_fert',$native_kol,'$name',$x,$y,$klasse,$bild,$temp,$lemin,$min1,$min2,$min3,$planet_lemin,$planet_min1,$planet_min2,$planet_min3,$konz_lemin,$konz_min1,$konz_min2,$konz_min3,$spiel,$artefakt)");
}
///////////////////////////////////////////////PLANETEN GENRERIEREN ENDE
///////////////////////////////////////////////INSTABLIE WURMLOECHER ANFANG

if ($data["instabil"] >= 1) {
    for ($i = 0; $i < $data["instabil"]; $i++) {

        $ok = 1;
        while ($ok == 1) {

            $x = rand(50, $umfang - 100);
            $y = rand(50, $umfang - 100);

            $oben = $y - 30;
            $unten = $y + 30;
            $links = $x - 30;
            $rechts = $x + 30;

            $ok = 2;
            $nachbarn = 0;
            $zeiger2 = @mysql_query("SELECT count(*) as total from skrupel_planeten where x_pos>$links and x_pos<$rechts and y_pos>$oben and y_pos<$unten and spiel=$spiel");
            $array = @mysql_fetch_array($zeiger2);
            $nachbarn = $array["total"];
            if ($nachbarn >= 1) {
                $ok = 1;
            }
            $nachbarn = 0;
            $zeiger2 = @mysql_query("SELECT count(*) as total from skrupel_anomalien where x_pos>$links and x_pos<$rechts and y_pos>$oben and y_pos<$unten and spiel=$spiel");
            $array = @mysql_fetch_array($zeiger2);
            $nachbarn = $array["total"];
            if ($nachbarn >= 1) {
                $ok = 1;
            }
        }

        $zeiger2 = @mysql_query("INSERT INTO skrupel_anomalien (art,x_pos,y_pos,spiel) values (1,$x,$y,$spiel);");
    }
}

///////////////////////////////////////////////INSTABLIE WURMLOECHER ENDE
///////////////////////////////////////////////STABILE WURMLOECHER ANFANG

if ($data["stabil"] >= 1) {

    if ($data["stabil"] <= 5) {
        $anzahl = $data["stabil"];
    }
    if ($data["stabil"] == 6) {
        $anzahl = 1;
    }
    if ($data["stabil"] == 7) {
        $anzahl = 2;
    }
    if ($data["stabil"] == 8) {
        $anzahl = 1;
    }
    if ($data["stabil"] == 9) {
        $anzahl = 2;
    }
    if ($data["stabil"] == 10) {
        $anzahl = 2;
    }
    if ($data["stabil"] == 11) {
        $anzahl = 1;
    }
    if ($data["stabil"] == 12) {
        $anzahl = 1;
    }
    if ($data["stabil"] == 13) {
        $anzahl = 2;
    }

    for ($i = 0; $i < $anzahl; $i++) {

        $ok = 1;
        while ($ok == 1) {

            if ($data["stabil"] <= 5) {
                $x = rand(50, $umfang - 100);
                $y = rand(50, $umfang - 100);
            }

            if ($data["stabil"] == 6) {
                $x = rand(50, $umfang - 100);
                $y = rand(50, ($umfang / 2) - 50);
            }
            if ($data["stabil"] == 7) {
                $x = rand(50, $umfang - 100);
                $y = rand(50, ($umfang / 2) - 50);
            }

            if ($data["stabil"] == 8) {
                $x = rand(50, ($umfang / 2) - 50);
                $y = rand(50, $umfang - 100);
            }
            if ($data["stabil"] == 9) {
                $x = rand(50, ($umfang / 2) - 50);
                $y = rand(50, $umfang - 100);
            }

            if (($data["stabil"] == 10) and ($i == 0)) {
                $x = rand(50, $umfang - 100);
                $y = rand(50, ($umfang / 2) - 50);
            }
            if (($data["stabil"] == 10) and ($i == 1)) {
                $x = rand(50, ($umfang / 2) - 50);
                $y = rand(50, $umfang - 100);
            }

            if ($data["stabil"] == 11) {
                $x = rand(50, ($umfang / 2) - 50);
                $y = rand(50, ($umfang / 2) - 50);
            }
            if ($data["stabil"] == 12) {
                $x = rand(($umfang / 2) + 50, $umfang - 100);
                $y = rand(50, ($umfang / 2) - 50);
            }

            if (($data["stabil"] == 13) and ($i == 0)) {
                $x = rand(50, ($umfang / 2) - 50);
                $y = rand(50, ($umfang / 2) - 50);
            }
            if (($data["stabil"] == 13) and ($i == 1)) {
                $x = rand(($umfang / 2) + 50, $umfang - 100);
                $y = rand(50, ($umfang / 2) - 50);
            }

            $oben = $y - 30;
            $unten = $y + 30;
            $links = $x - 30;
            $rechts = $x + 30;
            $ok = 2;
            $nachbarn = 0;
            $zeiger2 = @mysql_query("SELECT count(*) as total from skrupel_planeten where x_pos>$links and x_pos<$rechts and y_pos>$oben and y_pos<$unten and spiel=$spiel");
            $array = @mysql_fetch_array($zeiger2);
            $nachbarn = $array["total"];
            if ($nachbarn >= 1) {
                $ok = 1;
            }
            $nachbarn = 0;
            $zeiger2 = @mysql_query("SELECT count(*) as total from skrupel_anomalien where x_pos>$links and x_pos<$rechts and y_pos>$oben and y_pos<$unten and spiel=$spiel");
            $array = @mysql_fetch_array($zeiger2);
            $nachbarn = $array["total"];
            if ($nachbarn >= 1) {
                $ok = 1;
            }
        }
        $zeiger2 = @mysql_query("INSERT INTO skrupel_anomalien (art,x_pos,y_pos,spiel) values (1,$x,$y,$spiel);");

        $zeiger = @mysql_query("SELECT * FROM skrupel_anomalien where x_pos=$x and y_pos=$y and spiel=$spiel");
        $array = @mysql_fetch_array($zeiger);
        $aid_eins = $array["id"];
        $x_pos_eins = $array["x_pos"];
        $y_pos_eins = $array["y_pos"];

        $ok = 1;
        while ($ok == 1) {

            if ($data["stabil"] <= 5) {
                $x = rand(50, $umfang - 100);
                $y = rand(50, $umfang - 100);
            }

            if ($data["stabil"] == 6) {
                $x = rand(50, $umfang - 100);
                $y = rand(($umfang / 2) + 50, $umfang - 100);
            }
            if ($data["stabil"] == 7) {
                $x = rand(50, $umfang - 100);
                $y = rand(($umfang / 2) + 50, $umfang - 100);
            }

            if ($data["stabil"] == 8) {
                $x = rand(($umfang / 2) + 50, $umfang - 100);
                $y = rand(50, $umfang - 100);
            }
            if ($data["stabil"] == 9) {
                $x = rand(($umfang / 2) + 50, $umfang - 100);
                $y = rand(50, $umfang - 100);
            }

            if (($data["stabil"] == 10) and ($i == 0)) {
                $x = rand(50, $umfang - 100);
                $y = rand(($umfang / 2) + 50, $umfang - 100);
            }
            if (($data["stabil"] == 10) and ($i == 1)) {
                $x = rand(($umfang / 2) + 50, $umfang - 100);
                $y = rand(50, $umfang - 100);
            }

            if ($data["stabil"] == 11) {
                $x = rand(($umfang / 2) + 50, $umfang - 100);
                $y = rand(($umfang / 2) + 50, $umfang - 100);
            }
            if ($data["stabil"] == 12) {
                $x = rand(50, ($umfang / 2) - 50);
                $y = rand(($umfang / 2) + 50, $umfang - 100);
            }

            if (($data["stabil"] == 13) and ($i == 0)) {
                $x = rand(($umfang / 2) + 50, $umfang - 100);
                $y = rand(($umfang / 2) + 50, $umfang - 100);
            }
            if (($data["stabil"] == 13) and ($i == 1)) {
                $x = rand(50, ($umfang / 2) - 50);
                $y = rand(($umfang / 2) + 50, $umfang - 100);
            }

            $oben = $y - 30;
            $unten = $y + 30;
            $links = $x - 30;
            $rechts = $x + 30;
            $ok = 2;
            $nachbarn = 0;
            $zeiger2 = @mysql_query("SELECT count(*) as total from skrupel_planeten where x_pos>$links and x_pos<$rechts and y_pos>$oben and y_pos<$unten and spiel=$spiel");
            $array = @mysql_fetch_array($zeiger2);
            $nachbarn = $array["total"];
            if ($nachbarn >= 1) {
                $ok = 1;
            }
            $nachbarn = 0;
            $zeiger2 = @mysql_query("SELECT count(*) as total from skrupel_anomalien where x_pos>$links and x_pos<$rechts and y_pos>$oben and y_pos<$unten and spiel=$spiel");
            $array = @mysql_fetch_array($zeiger2);
            $nachbarn = $array["total"];
            if ($nachbarn >= 1) {
                $ok = 1;
            }
        }
        $extra = $aid_eins . ":" . $x_pos_eins . ":" . $y_pos_eins;
        $zeiger2 = @mysql_query("INSERT INTO skrupel_anomalien (art,x_pos,y_pos,extra,spiel) values (1,$x,$y,'$extra',$spiel);");

        $zeiger = @mysql_query("SELECT * FROM skrupel_anomalien where x_pos=$x and y_pos=$y and spiel=$spiel");
        $array = @mysql_fetch_array($zeiger);
        $aid_zwei = $array["id"];
        $x_pos_zwei = $array["x_pos"];
        $y_pos_zwei = $array["y_pos"];

        $extra = $aid_zwei . ":" . $x_pos_zwei . ":" . $y_pos_zwei;
        $zeiger2 = @mysql_query("UPDATE skrupel_anomalien set extra='$extra' where id=$aid_eins;");
    }
}

///////////////////////////////////////////////STABILE WURMLOECHER ENDE
////////////////////////////////////////////////WER SPIELT MIT

$spieleranzahl = 0;

if ($data["user_1"] >= 1) {
    $spieleranzahl++;
    $spieler[1][0] = $data["user_1"];
    $spieler[1][1] = $data["rasse_1"];
} else {
    $spieler[1][0] = 0;
    $spieler[1][1] = '';
}
if ($data["user_2"] >= 1) {
    $spieleranzahl++;
    $spieler[2][0] = $data["user_2"];
    $spieler[2][1] = $data["rasse_2"];
} else {
    $spieler[2][0] = 0;
    $spieler[2][1] = '';
}
if ($data["user_3"] >= 1) {
    $spieleranzahl++;
    $spieler[3][0] = $data["user_3"];
    $spieler[3][1] = $data["rasse_3"];
} else {
    $spieler[3][0] = 0;
    $spieler[3][1] = '';
}
if ($data["user_4"] >= 1) {
    $spieleranzahl++;
    $spieler[4][0] = $data["user_4"];
    $spieler[4][1] = $data["rasse_4"];
} else {
    $spieler[4][0] = 0;
    $spieler[4][1] = '';
}
if ($data["user_5"] >= 1) {
    $spieleranzahl++;
    $spieler[5][0] = $data["user_5"];
    $spieler[5][1] = $data["rasse_5"];
} else {
    $spieler[5][0] = 0;
    $spieler[5][1] = '';
}
if ($data["user_6"] >= 1) {
    $spieleranzahl++;
    $spieler[6][0] = $data["user_6"];
    $spieler[6][1] = $data["rasse_6"];
} else {
    $spieler[6][0] = 0;
    $spieler[6][1] = '';
}
if ($data["user_7"] >= 1) {
    $spieleranzahl++;
    $spieler[7][0] = $data["user_7"];
    $spieler[7][1] = $data["rasse_7"];
} else {
    $spieler[7][0] = 0;
    $spieler[7][1] = '';
}
if ($data["user_8"] >= 1) {
    $spieleranzahl++;
    $spieler[8][0] = $data["user_8"];
    $spieler[8][1] = $data["rasse_8"];
} else {
    $spieler[8][0] = 0;
    $spieler[8][1] = '';
}
if ($data["user_9"] >= 1) {
    $spieleranzahl++;
    $spieler[9][0] = $data["user_9"];
    $spieler[9][1] = $data["rasse_9"];
} else {
    $spieler[9][0] = 0;
    $spieler[9][1] = '';
}
if ($data["user_10"] >= 1) {
    $spieleranzahl++;
    $spieler[10][0] = $data["user_10"];
    $spieler[10][1] = $data["rasse_10"];
} else {
    $spieler[10][0] = 0;
    $spieler[10][1] = '';
}



///////////////////////////////////////////////SPIELER AUFBAUEN ANFANG
///////////////////////////////////////////////STARTPOSITIONEN

if ($startposition == 1) {
    $file = '../daten/gala_strukturen.txt';
    $fp = @fopen("$file", "r");
    if ($fp) {
        while (!feof($fp)) {
            $buffer = @fgets($fp, 4096);
            $strukturdaten = explode(':', $buffer);
            if ($strukturdaten[1] == $struktur) {
                $spieler_koordinaten = trim($strukturdaten[2 + $spieleranzahl]);
            }
        }
        @fclose($fp);
    }
    $koordinaten = explode('-', $spieler_koordinaten);
    for ($ker = 0; $ker < $spieleranzahl; $ker++) {
        $ko_daten = explode(',', $koordinaten[$ker]);
        $position[$ker]['x'] = $ko_daten[0];
        $position[$ker]['y'] = $ko_daten[1];
    }
    shuffle($position);
}
if ($startposition == 2) {
    for ($ker = 0; $ker < $spieleranzahl; $ker++) {
        $oke = 1;
        while ($oke == 1) {
            $position[$ker]['x'] = rand(2, 97);
            $position[$ker]['y'] = rand(2, 97);
            $test_x = round($position[$ker]['x'] / 2);
            $test_y = round($position[$ker]['y'] / 2);
            if ($raum[$test_y][$test_x] == 1) {
                $oke = 2;
            }
        }
    }
}
if (3 == $startposition) {
    for ($kkk = 1; $kkk < 11; $kkk++) {
        $position[$kkk]['x'] = $data['user_' . $kkk . '_x'];
        $position[$kkk]['y'] = $data['user_' . $kkk . '_y'];
    }
}


if (($data["imperiumgroesse"] == 1) or ($data["imperiumgroesse"] == 2) or ($data["imperiumgroesse"] == 3) or ($data["imperiumgroesse"] == 4)) {

    if ($data["imperiumgroesse"] == 1) {
        
    }

    $iii = 0;
    for ($i = 1; $i <= 10; $i++) {
        if ($spieler[$i][0] >= 1) {
            $iii++;
            $rasse = $spieler[$i][1];
            $planetenname = $nameplanet[$rasse];

            if ((1 == $startposition) or (1 == $startposition)) {
                $x_pos = round($position[$iii - 1]['x'] * $umfang / 100);
                $y_pos = round($position[$iii - 1]['y'] * $umfang / 100);
            }
            if (3 == $startposition) {
                $x_pos = round($position[$i]['x'] * $umfang / 100);
                $y_pos = round($position[$i]['y'] * $umfang / 100);
            }

            $oben = $y_pos - 30;
            $unten = $y_pos + 30;
            $links = $x_pos - 30;
            $rechts = $x_pos + 30;
            $zeiger_temp = @mysql_query("delete from skrupel_planeten where x_pos>$links and x_pos<$rechts and y_pos>$oben and y_pos<$unten and spiel=$spiel");

            $zeiger_temp = mysql_query("INSERT into skrupel_planeten (name,x_pos,y_pos,spiel) values ('$planetenname',$x_pos,$y_pos,$spiel)");

            $zeiger_temp = @mysql_query("SELECT id FROM skrupel_planeten where x_pos=$x_pos and y_pos=$y_pos and spiel=$spiel");
            $array = @mysql_fetch_array($zeiger_temp);
            $pid = $array["id"];


            $konz_lemin = rand(4, 5);
            $konz_min1 = rand(4, 5);
            $konz_min2 = rand(4, 5);
            $konz_min3 = rand(4, 5);

            $kolonisten = rand(1, 1000) + (rand(1, 1000) * 5) + 50000;
            $vorrat = 5;
            $minen = 5;
            $abwehr = 5;
            $fabriken = 5;
            $cantox = $data["geldmittel"];

            $lemin = rand(50, 70);
            $min1 = rand(50, 70);
            $min2 = rand(50, 70);
            $min3 = rand(50, 70);

            if ($data["mineralienhome"] == 1) {
                $minrand = 2000;
                $maxrand = 3000;
            }
            if ($data["mineralienhome"] == 2) {
                $minrand = 1500;
                $maxrand = 2500;
            }
            if ($data["mineralienhome"] == 3) {
                $minrand = 1000;
                $maxrand = 2000;
            }
            if ($data["mineralienhome"] == 4) {
                $minrand = 500;
                $maxrand = 1000;
            }

            $planet_lemin = rand($minrand, $maxrand);
            $planet_min1 = rand($minrand, $maxrand);
            $planet_min2 = rand($minrand, $maxrand);
            $planet_min3 = rand($minrand, $maxrand);

            $klasse = $r_eigenschaften[$rasse]['planet'];
            if ($klasse == 0) {
                $klasse = rand(1, 9);
            }

            if ($klasse == 1) {
                $bild = rand(1, 9);
            }
            if ($klasse == 2) {
                $bild = rand(1, 24);
            }
            if ($klasse == 3) {
                $bild = rand(1, 15);
            }
            if ($klasse == 4) {
                $bild = rand(1, 14);
            }
            if ($klasse == 5) {
                $bild = rand(1, 11);
            }
            if ($klasse == 6) {
                $bild = rand(1, 22);
            }
            if ($klasse == 7) {
                $bild = rand(1, 13);
            }
            if ($klasse == 8) {
                $bild = rand(1, 33);
            }
            if ($klasse == 9) {
                $bild = rand(1, 9);
            }

            $temp = $r_eigenschaften[$rasse]['temperatur'];

            if ($temp == 0) {

                if ($klasse == 1) {
                    $temp = rand(40, 60);
                }
                if ($klasse == 2) {
                    $temp = rand(30, 50);
                }
                if ($klasse == 3) {
                    $temp = rand(0, 10);
                }
                if ($klasse == 4) {
                    $temp = rand(50, 75);
                }
                if ($klasse == 5) {
                    $temp = rand(86, 100);
                }
                if ($klasse == 6) {
                    $temp = rand(70, 95);
                }
                if ($klasse == 7) {
                    $temp = rand(75, 90);
                }
                if ($klasse == 8) {
                    $temp = rand(20, 35);
                }
                if ($klasse == 9) {
                    $temp = rand(25, 45);
                }
            }

            $zeiger = @mysql_query("UPDATE skrupel_planeten set konz_lemin=$konz_lemin,konz_min1=$konz_min1,konz_min2=$konz_min2,konz_min3=$konz_min3,osys_anzahl=4,native_id=0,native_name ='',native_art=0,native_art_name='',native_abgabe=0,native_bild='',native_text='',native_fert='',native_kol=0,besitzer=$i,heimatplanet=$i,vorrat=$vorrat,cantox=$cantox,minen=$minen,abwehr=$abwehr,fabriken=$fabriken,kolonisten=$kolonisten,lemin=$lemin,min1=$min1,min2=$min2,min3=$min3,klasse=$klasse,bild=$bild,temp=$temp,planet_lemin=$planet_lemin,planet_min1=$planet_min1,planet_min2=$planet_min2,planet_min3=$planet_min3 where id=$pid");

            if (($data["imperiumgroesse"] == 1) or ($data["imperiumgroesse"] == 2)) {

                $namesb = 'Starbase I';
                $rasse = $spieler[$i][1];
                $zeiger = @mysql_query("INSERT INTO skrupel_sternenbasen (name,x_pos,y_pos,rasse,planetid,besitzer,status,spiel) values ('$namesb',$x_pos,$y_pos,'$rasse',$pid,$i,1,$spiel)");

                $zeiger_temp = @mysql_query("SELECT * FROM skrupel_sternenbasen where planetid=$pid");
                $array_temp = @mysql_fetch_array($zeiger_temp);
                $baid = $array_temp["id"];

                $zeiger_temp = @mysql_query("UPDATE skrupel_planeten set sternenbasis=2,sternenbasis_name='$namesb',sternenbasis_id=$baid,sternenbasis_rasse='$rasse' where id=$pid");
            }
            if ($data["imperiumgroesse"] == 1) {



                $schiffbau_klasse = $array["schiffbau_klasse"];
                $schiffbau_bild_gross = $array["schiffbau_bild_gross"];
                $schiffbau_bild_klein = $array["schiffbau_bild_klein"];
                $schiffbau_crew = $array["schiffbau_crew"];
                $schiffbau_masse = $array["schiffbau_masse"];
                $schiffbau_tank = $array["schiffbau_tank"];
                $schiffbau_fracht = $array["schiffbau_fracht"];
                $schiffbau_antriebe = $array["schiffbau_antriebe"];
                $schiffbau_energetik = $array["schiffbau_energetik"];
                $schiffbau_projektile = $array["schiffbau_projektile"];
                $schiffbau_hangar = $array["schiffbau_hangar"];
                $schiffbau_klasse_name = $array["schiffbau_klasse_name"];
                $schiffbau_rasse = $array["schiffbau_rasse"];
                $schiffbau_fertigkeiten = $array["schiffbau_fertigkeiten"];
                $schiffbau_energetik_stufe = $array["schiffbau_energetik_stufe"];
                $schiffbau_projektile_stufe = 0;
                $schiffbau_techlevel = 3;




                $schiffbau_antriebe_stufe = 3;
                $schiffbau_name = 'Frachter I';


                $zeiger_temp = @mysql_query("INSERT INTO skrupel_schiffe
        
    (besitzer,status,name,klasse,klasseid,volk,techlevel,antrieb,antrieb_anzahl,kox,koy,crew,crewmax,lemin,leminmax,frachtraum,masse,masse_gesamt,bild_gross,bild_klein,energetik_stufe,energetik_anzahl,projektile_stufe,projektile_anzahl,hanger_anzahl,schild,fertigkeiten,spiel)
 values ($i,2,'$schiffbau_name','$schiffbau_klasse_name',$schiffbau_klasse,'$schiffbau_rasse',$schiffbau_techlevel,$schiffbau_antriebe_stufe, $schiffbau_antriebe,$x_pos,$y_pos,$schiffbau_crew,$schiffbau_crew,0,$schiffbau_tank,$schiffbau_fracht,$schiffbau_masse,$schiffbau_masse,'$schiffbau_bild_gross','$schiffbau_bild_klein',$schiffbau_energetik_stufe,$schiffbau_energetik,$schiffbau_projektile_stufe,$schiffbau_projektile,$schiffbau_hangar,100,'$schiffbau_fertigkeiten',$spiel)");
            }
        }
    }
}

//////////////////////////////////////////////SPIELER AUFBAUEN ENDE
//////////////////////////////////////////////SCHIFFSORDNER ANFANG

for ($k = 1; $k < 11; $k++) {
    $zeiger_temp = @mysql_query("INSERT INTO skrupel_ordner (name,besitzer,spiel) values ('Frachter',$k,$spiel)");
    $zeiger_temp = @mysql_query("INSERT INTO skrupel_ordner (name,besitzer,spiel) values ('J�ger',$k,$spiel)");
    $zeiger_temp = @mysql_query("INSERT INTO skrupel_ordner (name,besitzer,spiel) values ('Sonstige',$k,$spiel)");
}


//////////////////////////////////////////////SCHIFFSORDNER ENDE
//////////////////////////////////////////////ZIEL

$ziel_info = '';
$spieler_1_ziel = '';
$spieler_2_ziel = '';
$spieler_3_ziel = '';
$spieler_4_ziel = '';
$spieler_5_ziel = '';
$spieler_6_ziel = '';
$spieler_7_ziel = '';
$spieler_8_ziel = '';
$spieler_9_ziel = '';
$spieler_10_ziel = '';

if ($ziel_id == 1) {
    $ziel_info = $data["zielinfo_1"];
}
if ($ziel_id == 2) {
    $feind = 0;
    $checkstring = '';
    $zahl = 0;
    for ($n = 1; $n <= 10; $n++) {
        $ok = 1;
        if ($spieler[$n][0] >= 1) {
            while ($ok == 1) {
                $zahl = rand(1, 10);
                $code = ":::" . $zahl . ":::";
                if (($zahl != $n) and ($spieler[$zahl][0] >= 1)) {
                    if (strstr($checkstring, $code)) {
                        
                    } else {
                        $ok = 2;
                        $checkstring = $checkstring . $code;
                        if ($n == 1) {
                            $spieler_1_ziel = "$zahl";
                        }
                        if ($n == 2) {
                            $spieler_2_ziel = "$zahl";
                        }
                        if ($n == 3) {
                            $spieler_3_ziel = "$zahl";
                        }
                        if ($n == 4) {
                            $spieler_4_ziel = "$zahl";
                        }
                        if ($n == 5) {
                            $spieler_5_ziel = "$zahl";
                        }
                        if ($n == 6) {
                            $spieler_6_ziel = "$zahl";
                        }
                        if ($n == 7) {
                            $spieler_7_ziel = "$zahl";
                        }
                        if ($n == 8) {
                            $spieler_8_ziel = "$zahl";
                        }
                        if ($n == 9) {
                            $spieler_9_ziel = "$zahl";
                        }
                        if ($n == 10) {
                            $spieler_10_ziel = "$zahl";
                        }
                    }
                }
            }
        }
    }
}

//$spieleranzahl

if ($ziel_id == 6) {
    $zahl = 0;
    $ok = 1;

    for ($n = 1; $n <= 10; $n++) {
        if ($spieler[$n][0] >= 1) {
            $feind[$zahl] = $n;
            $zahl = $zahl + 1;
        }
    }

    while ($ok == 1) {
        $zahl = 0;
        $ok = 2;
        shuffle($feind);
        for ($n = 1; $n <= 10; $n++) {
            if ($spieler[$n][0] >= 1) {
                if ($feind[$zahl] == $n) {
                    $ok = 1;
                }

                for ($mo = 1; $mo <= 10; $mo++) {
                    if (($spieler[$mo][0] >= 1) and ($mo != $n) and ($team[$mo] == $team[$n])) {
                        if ($feind[$zahl] == $mo) {
                            $ok = 1;
                        }
                        $partner[$n] = $mo;
                    }
                }


                $zahl = $zahl + 1;
            }
        }
    }
    $zahl = 0;
    for ($n = 1; $n <= 10; $n++) {
        if ($spieler[$n][0] >= 1) {
            $vernichte[$n] = $feind[$zahl];
            $zahl = $zahl + 1;
        }
    }

    for ($n = 1; $n <= 10; $n++) {
        if ($spieler[$n][0] >= 1) {
            if ($n == 1) {
                $spieler_1_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
            if ($n == 2) {
                $spieler_2_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
            if ($n == 3) {
                $spieler_3_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
            if ($n == 4) {
                $spieler_4_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
            if ($n == 5) {
                $spieler_5_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
            if ($n == 6) {
                $spieler_6_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
            if ($n == 7) {
                $spieler_7_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
            if ($n == 8) {
                $spieler_8_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
            if ($n == 9) {
                $spieler_9_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
            if ($n == 10) {
                $spieler_10_ziel = $team[$n] . ':' . $vernichte[$n] . ':' . $vernichte[$partner[$n]];
            }
        }
    }

    for ($n = 1; $n <= 10; $n++) {
        if ($spieler[$n][0] >= 1) {
            if ($n < $partner[$n]) {
                $zeigertemp = @mysql_query("INSERT INTO skrupel_politik (partei_a,partei_b,status,optionen,spiel) values ($n,$partner[$n],5,0,$spiel)");
            }
        }
    }
}



if ($ziel_id == 5) {
    $ziel_info = $data["zielinfo_5"];
    $spieler_1_ziel = "0";
    $spieler_2_ziel = "0";
    $spieler_3_ziel = "0";
    $spieler_4_ziel = "0";
    $spieler_5_ziel = "0";
    $spieler_6_ziel = "0";
    $spieler_7_ziel = "0";
    $spieler_8_ziel = "0";
    $spieler_9_ziel = "0";
    $spieler_10_ziel = "0";
}

//////////////////////////////////////////////ZIEL
///////////////////////////////////////////////NEBEL ERSTELLEN ANFANG

$nebel = $data["nebel"];

$besitzer_recht[1] = '1000000000';
$besitzer_recht[2] = '0100000000';
$besitzer_recht[3] = '0010000000';
$besitzer_recht[4] = '0001000000';
$besitzer_recht[5] = '0000100000';
$besitzer_recht[6] = '0000010000';
$besitzer_recht[7] = '0000001000';
$besitzer_recht[8] = '0000000100';
$besitzer_recht[9] = '0000000010';
$besitzer_recht[10] = '0000000001';


if ($nebel >= 1) {

    function sichtaddieren($sicht_alt, $sicht_neu) {

        if ((substr($sicht_alt, 0, 1) == "1") or (substr($sicht_neu, 0, 1) == "1")) {
            $s1 = "1";
        } else {
            $s1 = "0";
        }
        if ((substr($sicht_alt, 1, 1) == "1") or (substr($sicht_neu, 1, 1) == "1")) {
            $s2 = "1";
        } else {
            $s2 = "0";
        }
        if ((substr($sicht_alt, 2, 1) == "1") or (substr($sicht_neu, 2, 1) == "1")) {
            $s3 = "1";
        } else {
            $s3 = "0";
        }
        if ((substr($sicht_alt, 3, 1) == "1") or (substr($sicht_neu, 3, 1) == "1")) {
            $s4 = "1";
        } else {
            $s4 = "0";
        }
        if ((substr($sicht_alt, 4, 1) == "1") or (substr($sicht_neu, 4, 1) == "1")) {
            $s5 = "1";
        } else {
            $s5 = "0";
        }
        if ((substr($sicht_alt, 5, 1) == "1") or (substr($sicht_neu, 5, 1) == "1")) {
            $s6 = "1";
        } else {
            $s6 = "0";
        }
        if ((substr($sicht_alt, 6, 1) == "1") or (substr($sicht_neu, 6, 1) == "1")) {
            $s7 = "1";
        } else {
            $s7 = "0";
        }
        if ((substr($sicht_alt, 7, 1) == "1") or (substr($sicht_neu, 7, 1) == "1")) {
            $s8 = "1";
        } else {
            $s8 = "0";
        }
        if ((substr($sicht_alt, 8, 1) == "1") or (substr($sicht_neu, 8, 1) == "1")) {
            $s9 = "1";
        } else {
            $s9 = "0";
        }
        if ((substr($sicht_alt, 9, 1) == "1") or (substr($sicht_neu, 9, 1) == "1")) {
            $s10 = "1";
        } else {
            $s10 = "0";
        }

        $sicht = $s1 . $s2 . $s3 . $s4 . $s5 . $s6 . $s7 . $s8 . $s9 . $s10;
        return $sicht;
    }

    $dateiinclude = "../inhalt/inc.host_nebel.php";
    include ($dateiinclude);
}


///////////////////////////////////////////////NEBEL ERSTELLEN ENDE
///////////////////////////////////////////////SPIELBLOCK ERSTELLEN ANFANG


$letztermonat = "Es fand leider noch kein Zug statt.";
for ($sp = 1; $sp <= 10; $sp++) {
    $spieler_rasse_c[$sp] = $spieler[$sp][1];
}
$spieler_1 = $spieler[1][0];
$spieler_2 = $spieler[2][0];
$spieler_3 = $spieler[3][0];
$spieler_4 = $spieler[4][0];
$spieler_5 = $spieler[5][0];
$spieler_6 = $spieler[6][0];
$spieler_7 = $spieler[7][0];
$spieler_8 = $spieler[8][0];
$spieler_9 = $spieler[9][0];
$spieler_10 = $spieler[10][0];

$plasma_wahr = $data["wahr"];
$plasma_lang = $data["lang"];
$plasma_max = $data["max"];
$spieler_admin = $data["spieler_admin"];

for ($sp = 1; $sp <= 10; $sp++) {
    if (strlen($spieler_rasse_c[$sp]) >= 2) {
        $spieler_rassename_c[$sp] = $namerassen[$spieler_rasse_c[$sp]];
    }
}

$zeiger = @mysql_query("UPDATE skrupel_spiele set
                        sid='$sid',
                        ziel_id=$ziel_id,
                        ziel_info='$ziel_info',
                        spieler_1=$spieler_1,
                        spieler_admin=$spieler_admin,
                        plasma_wahr=$plasma_wahr,
                        plasma_lang=$plasma_lang,
                        plasma_max=$plasma_max,
                        spieler_2=$spieler_2,
                        spieler_3=$spieler_3,
                        spieler_4=$spieler_4,
                        spieler_5=$spieler_5,
                        spieler_6=$spieler_6,
                        spieler_7=$spieler_7,
                        spieler_8=$spieler_8,
                        spieler_9=$spieler_9,
                        spieler_10=$spieler_10,
                        spieler_1_ziel='$spieler_1_ziel',
                        spieler_2_ziel='$spieler_2_ziel',
                        spieler_3_ziel='$spieler_3_ziel',
                        spieler_4_ziel='$spieler_4_ziel',
                        spieler_5_ziel='$spieler_5_ziel',
                        spieler_6_ziel='$spieler_6_ziel',
                        spieler_7_ziel='$spieler_7_ziel',
                        spieler_8_ziel='$spieler_8_ziel',
                        spieler_9_ziel='$spieler_9_ziel',
                        spieler_10_ziel='$spieler_10_ziel',
                        spieleranzahl=$spieleranzahl,
                        spieler_1_zug=0,
                        spieler_2_zug=0,
                        spieler_3_zug=0,
                        spieler_4_zug=0,
                        spieler_5_zug=0,
                        spieler_6_zug=0,
                        spieler_7_zug=0,
                        spieler_8_zug=0,
                        spieler_9_zug=0,
                        spieler_10_zug=0,
                        lasttick='',
                        spieler_1_basen=1,
                        spieler_1_schiffe=1,
                        spieler_1_planeten=1,
                        spieler_2_basen=1,
                        spieler_2_schiffe=1,
                        spieler_2_planeten=1,
                        spieler_3_basen=1,
                        spieler_3_schiffe=1,
                        spieler_3_planeten=1,
                        spieler_4_basen=1,
                        spieler_4_schiffe=1,
                        spieler_4_planeten=1,
                        spieler_5_basen=1,
                        spieler_5_schiffe=1,
                        spieler_5_planeten=1,
                        spieler_6_basen=1,
                        spieler_6_schiffe=1,
                        spieler_6_planeten=1,
                        spieler_7_basen=1,
                        spieler_7_schiffe=1,
                        spieler_7_planeten=1,
                        spieler_8_basen=1,
                        spieler_8_schiffe=1,
                        spieler_8_planeten=1,
                        spieler_9_basen=1,
                        spieler_9_schiffe=1,
                        spieler_9_planeten=1,
                        spieler_10_basen=1,
                        spieler_10_schiffe=1,
                        spieler_10_planeten=1,
                        letztermonat='$letztermonat',
                        spieler_1_rasse='{$spieler_rasse_c[1]}',
                        spieler_2_rasse='{$spieler_rasse_c[2]}',
                        spieler_3_rasse='{$spieler_rasse_c[3]}',
                        spieler_4_rasse='{$spieler_rasse_c[4]}',
                        spieler_5_rasse='{$spieler_rasse_c[5]}',
                        spieler_6_rasse='{$spieler_rasse_c[6]}',
                        spieler_7_rasse='{$spieler_rasse_c[7]}',
                        spieler_8_rasse='{$spieler_rasse_c[8]}',
                        spieler_9_rasse='{$spieler_rasse_c[9]}',
                        spieler_10_rasse='{$spieler_rasse_c[10]}',
                        spieler_1_rassename='{$spieler_rassename_c[1]}',
                        spieler_2_rassename='{$spieler_rassename_c[2]}',
                        spieler_3_rassename='{$spieler_rassename_c[3]}',
                        spieler_4_rassename='{$spieler_rassename_c[4]}',
                        spieler_5_rassename='{$spieler_rassename_c[5]}',
                        spieler_6_rassename='{$spieler_rassename_c[6]}',
                        spieler_7_rassename='{$spieler_rassename_c[7]}',
                        spieler_8_rassename='{$spieler_rassename_c[8]}',
                        spieler_9_rassename='{$spieler_rassename_c[9]}',
                        spieler_10_rassename='{$spieler_rassename_c[10]}',
                        autozug=0,
                        umfang=$umfang,
                        nebel=$nebel,
                        spieler_1_platz=1,
                        spieler_2_platz=1,
                        spieler_3_platz=1,
                        spieler_4_platz=1,
                        spieler_5_platz=1,
                        spieler_6_platz=1,
                        spieler_7_platz=1,
                        spieler_8_platz=1,
                        spieler_9_platz=1,
                        spieler_10_platz=1,
                        runde=1,
                        aufloesung=50,
                        name='$spielname',
                        piraten_mitte=$piraten_mitte,
                        piraten_aussen=$piraten_aussen,
                        piraten_min=$piraten_min,
                        piraten_max=$piraten_max
                        where id=$spiel");



if ($spieler_1 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_1");
}
if ($spieler_2 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_2");
}
if ($spieler_3 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_3");
}
if ($spieler_4 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_4");
}
if ($spieler_5 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_5");
}
if ($spieler_6 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_6");
}
if ($spieler_7 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_7");
}
if ($spieler_8 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_8");
}
if ($spieler_9 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_9");
}
if ($spieler_10 >= 1) {
    $zeiger = @mysql_query("UPDATE skrupel_user set stat_teilnahme=stat_teilnahme+1 where id=$spieler_10");
}

$zeiger = @mysql_query("UPDATE skrupel_info set stat_spiele=stat_spiele+1");

///////////////////////////////////////////////////////////////////////////////////////////////MOVIEGIF OPTIONAL ANFANG

$moviegif_verzeichnis = '../extend/moviegif';

if ((@file_exists($moviegif_verzeichnis)) and (@intval(substr($spiel_extend, 0, 1)) == 1)) {
    include($moviegif_verzeichnis . '/shot.php');
}

//////////////////////////////////////////////////////
// Send mails that the game has started
@mysql_close();
db_connect();

$sql = "SELECT email FROM skrupel_user WHERE id in (" . $mailsto . ")";
$query = mysql_query($sql);
$header = "From: $absenderemail\r\n" . "Reply-To: $absenderemail\r\n" . "X-Mailer: PHP/" . phpversion();
$betreff = "$conf_game_name - Spiel gestartet";
$nachricht = "Das Spiel '" . $spiel["spiel_name"] . "', f�r das Du Dich angemeldet hast, wurde soeben gestartet. Viel Spa� und viel Erfolg bei dieser Runde von $conf_game_name!\n\n---\nDies ist eine automatisch generierte E-Mail. Bitte nicht antworten.";
$nachrichtmessenger = "Das Spiel '" . $spiel["spiel_name"] . "' wurde soeben gestartet.";
while ($data = mysql_fetch_assoc($query)) {
    if ($conf_mail_extra_params)
        @mail($data["email"], $betreff, $nachricht, $header, "-f {$absenderemail} {$data["email"]}");
    else
        @mail($data["email"], $betreff, $nachricht, $header);
}

// Nachricht auf Twitter "zwitschern"
include_once PRDYZ_DIR_INCLUDES . '/classes/class.twitter.php';
$twt = new Twitter('Paradyze_Game', 'De$t!n4tIoN#uNkN0wN');
$twt->updateStatus(stripslashes($nachrichtmessenger));

///////////////////////////////////////////////////////////////////////////////////////////////MOVIEGIF OPTIONAL END
///////////////////////////////////////////////SPIELBLOCK ERSTELLEN ENDE
portal_success_message('Das Spiel wurde erfolgreich erstellt.', 'active.php');