<?php
/////////////////////////////////////////////////////////////////////////////////

// Datenbankzugangsdaten

$conf_database_server                        = 'localhost';
$conf_database_login                         = 'master';
$conf_database_password                      = '43!rGdLwid?xhYme$wyEwU#A&lic3p';
$conf_database_database                      = 'paradyze';

// Adminzugangsdaten

$admin_login                   = 'admin';
$admin_pass                    = '8DbaGDRP7XXW2mzy';

// Konfiguration

$language                      = 'de';
$absenderemail                 = 'webmaster@iceflame.net';
$conf_mail_extra_params        = false;
$conf_admin_users              = array(4);
$ping_off                      = 0;

// Extensions

$conf_enable_ai                = true;
$conf_enable_xstats            = false;

// Meta-Daten

$conf_game_title               = "Paradyze";
$conf_meta_title               = "Willkommen im Paradyze! | &Ouml;ffentlicher Skrupel-Server";
$conf_meta_author              = "Bernd Kantoks";
$conf_meta_description         = "Paradyze ist eine Mischung aus VGA-Planets und Ascendancy. Man bebaut seinen ersten Planeten, schickt Schiffe zu weiteren, gr�ndet dort Kolonien, sammelt Rohstoffe, baut Sternenbasen und hetzt sich gegenseitig seine Flotten auf den Hals.";
$conf_meta_keywords            = "paradyze, space, weltraum, universum, planet, raumschiff, strategie, free, kostenlos, browsergame, browserspiel";

// Zeit-Konfig

$conf_timezone                 = 'Europe/Berlin';

/////////////////////////////////////////////////////////////////////////////////
// SYSTEMEINSTELLUNGEN
/////////////////////////////////////////////////////////////////////////////////

define('PRDYZ_INSIDE', 1);

define('PRDYZ_DIR_ROOT', dirname(__FILE__));
define('PRDYZ_DIR_DATA', PRDYZ_DIR_ROOT.'/daten');
define('PRDYZ_DIR_INCLUDES', PRDYZ_DIR_ROOT.'/includes');
define('PRDYZ_DIR_LANG', PRDYZ_DIR_ROOT.'/lang');
define('PRDYZ_DIR_EXTEND', PRDYZ_DIR_ROOT.'/extend');

// Error-Behandlung aus -- bei Bedarf aktivieren

//ini_set('display_errors', 'On');
//ini_set('log_errors', 'On');
//ini_set('ignore_repeated_errors', 1);
//error_reporting(E_ALL);

// Spielerfarben

$spielerfarbe[1]	= "#1DC710";    //gruen
$spielerfarbe[2]	= "#E5E203";    //gelb
$spielerfarbe[3]	= "#EAA500";    //orange
$spielerfarbe[4]	= "#875F00";    //braun
$spielerfarbe[5]	= "#bb0000";    //rot
$spielerfarbe[6]	= "#D700C1";    //rosa
$spielerfarbe[7]	= "#7D10C7";    //lila
$spielerfarbe[8]	= "#101DC7";    //blau
$spielerfarbe[9]	= "#049EEF";    //hellblau
$spielerfarbe[10]	= "#10C79B";   //tuerkis

// Verbrauch pro Monat

if (!function_exists("getVerbrauchProMonat")) {
    function getVerbrauchProMonat($antrieb){
            if ($antrieb==1) { $verbrauchpromonat = array ("0","0","0","0","0","0","0","0","0","0"); }
            elseif ($antrieb==2) { $verbrauchpromonat = array ("0","100","107.5","300","400","500","600","700","800","900"); }
            elseif ($antrieb==3) { $verbrauchpromonat = array ("0","100","106.25","107.78","337.5","500","600","700","800","900"); }
            elseif ($antrieb==4) { $verbrauchpromonat = array ("0","100","103.75","104.44","106.25","300","322.22","495.92","487.5","900"); }
            elseif ($antrieb==5) { $verbrauchpromonat = array ("0","100","103.75","104.44","106.25","104","291.67","291.84","366.41","900"); }
            elseif ($antrieb==6) { $verbrauchpromonat = array ("0","100","103.75","104.44","106.25","104","103.69","251.02","335.16","900"); }
            elseif ($antrieb==7) { $verbrauchpromonat = array ("0","100","103.75","104.44","106.25","104","103.69","108.16","303.91","529.63"); }
            elseif ($antrieb==8) { $verbrauchpromonat = array ("0","100","100","100","100","100","100","102.04","109.38","529.63"); }
            elseif ($antrieb==9) { $verbrauchpromonat = array ("0","100","100","100","100","100","100","100","100","100"); }

       return $verbrauchpromonat;
    }
}