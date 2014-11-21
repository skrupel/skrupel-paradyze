<?php
/////////////////////////////////////////////////////////////////////////////////
// Datenbank

function db_connect() {
    global $conf_database_server, $conf_database_login, $conf_database_password, $conf_database_database;
    $db_conn = mysql_connect($conf_database_server, $conf_database_login, $conf_database_password);
    if (!$db_conn) {
        die(mysql_error());
    }
    if (!mysql_select_db($conf_database_database, $db_conn)) {
        die(mysql_error());
    }
    return $db_conn;
}

function db_escape($string) {
    return mysql_real_escape_string($string);
}

/////////////////////////////////////////////////////////////////////////////////
// Hilfsfunktionen

function compressed_output() {
    $encoding = getEnv("HTTP_ACCEPT_ENCODING");
    $useragent = getEnv("HTTP_USER_AGENT");
    $method = trim(getEnv("REQUEST_METHOD"));
    $msie = preg_match("=msie=i", $useragent);
    $gzip = preg_match("=gzip=i", $encoding);

    if ($gzip && ($method != "POST" or !$msie)) {
        ob_start("ob_gzhandler");
    } else {
        ob_start();
    }
}

function neuigkeit($art, $icon, $spieler_id, $inhalt) {
    global $db, $spiel;
    $datum = time();
    $zeiger_temp = mysql_query("INSERT INTO skrupel_neuigkeiten (datum, art, icon, inhalt, spieler_id, spiel_id, sicher) values ('$datum', $art, '$icon', '$inhalt', $spieler_id, $spiel, 1);");
}

function nick($userid) {
    global $db, $spiel;
    $zeiger = mysql_query("SELECT nick, id FROM skrupel_user WHERE id = $userid");
    $array = mysql_fetch_array($zeiger);
    return $array['nick'];
}

function int_post($key) {
    if (isset($_POST[$key]) && is_numeric($_POST[$key])) {
        return intVal($_POST[$key]);
    }
    return false;
}

function int_get($key) {
    if (isset($_GET[$key]) && is_numeric($_GET[$key])) {
        return intVal($_GET[$key]);
    }
    return false;
}

function str_post($key) {
    if (isset($_POST[$key]) && strLen($_POST[$key]) > 0) {
        return $_POST[$key];
    }
    return false;
}

function str_get($key) {
    if (isset($_GET[$key]) && strLen($_GET[$key]) > 0) {
        return $_GET[$key];
    }
    return false;
}

function random_number() {
    mt_srand((double) microtime()*1000000);
    $num = mt_rand(48, 122);
    return $num;
}

function random_char() {
    do {
        $num = random_number();
    } while (($num > 57 && $num < 65) || ($num > 90 && $num < 97));
    return chr($num);
}

function random_string() {
    $salt = '';
    for ($i = 1; $i <= 20; $i++) {
        $salt .= random_char();
    }
    return $salt;
}

function jsArrayFromArray($name, $a) {
    // soll auch mit js 1.0 funktionieren, daher nicht einfach var $name = [$a[0],$a[1],..];//js 1.2
    $count = count($a);
    $js = "var $name = new Array(".$count.");\n";
    for ($i = 0; $i < $count; ++$i) {
        $js .= "$name".'['.$i.']="'.$a[$i].'"'.";\n";
    }
    return $js;
}