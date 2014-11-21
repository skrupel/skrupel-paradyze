<?php
include_once '../inc.conf.php';
include_once PRDYZ_DIR_INCLUDE . '/inc.hilfsfunktionen.php';

include PRDYZ_DIR_INCLUDE . '/inc.check.php';

db_connect();

$zeiger = @mysql_query("SELECT * FROM skrupel_info");
$array = @mysql_fetch_array($zeiger);
$spiel_chat = $array['chat'];
$spiel_anleitung = $array['anleitung'];
$spiel_forum = $array['forum'];
$spiel_forum_url = $array['forum_url'];
$spiel_version = $array['version'];
$spiel_extend = $array['extend'];
$spiel_serial = $array['serial'];

compressed_output();

$useragent = getEnv("HTTP_USER_AGENT");

$firefox = preg_match("/firefox/i", $useragent);
$linux = preg_match("/linux/i", $useragent);

$plus = 0;
if ($linux) {
    $plus = 1;
}
?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <meta http-equiv="imagetoolbar" content="no" />
        <style type="text/css">
            body, p, td {
                font-family: Verdana;
                font-size: <?php echo 10 - $plus; ?>px;
                color: #ffffff;
                scrollbar-darkshadow-color: #444444;
                scrollbar-3dlight-color: #444444;
                scrollbar-track-color: #444444;
                scrollbar-face-color: #555555;
                scrollbar-shadow-color: #222222;
                scrollbar-highlight-color: #888888;
                scrollbar-arrow-color: #555555;
            }
            td.weissklein {
                font-family: Verdana;
                font-size: <?php echo 10 - $plus; ?>px;
                color: #ffffff;
            }
            td.weissgross {
                font-family: Verdana;
                font-size: <?php echo 12 - $plus; ?>px;
                color: #ffffff;
            }
            a {
                color: #aaaaaa;
                font-weight: normal;
                text-decoration: none;
            }
            a:hover {
                font-weight: normal;
                text-decoration: underline;
                color: #ffffff;
            }
            input,select {
                background-color: #555555;
                color: #ffffff;
                border-bottom-color: #222222;
                border-left-color: #888888;
                border-right-color: #222222;
                border-top-color: #888888;
                Border-style: solid;
                Border-width: 1px;
                font-family: Verdana;
                font-size: <?php echo 10 - $plus; ?>px;
            }
            input.nofunc {
                background-color: #555555;
                color: #777777;
                border-bottom-color: #222222;
                border-left-color: #888888;
                border-right-color: #222222;
                border-top-color: #888888;
                Border-style: solid;
                Border-width: 1px;
                font-family: Verdana;
                font-size: <?php echo 10 - $plus; ?>px;
            }
            input.eingabe {
                background-color: #555555;
                color: #ffffff;
                border-bottom-color: #888888;
                border-left-color: #222222;
                border-right-color: #888888;
                border-top-color: #222222;
                Border-style: solid;
                Border-width: 1px;
                font-family: Verdana;
                font-size: <?php echo 10 - $plus; ?>px;
            }
            textarea {
                background-color: #555555;
                color: #ffffff;
                border-bottom-color: #888888;
                border-left-color: #222222;
                border-right-color: #888888;
                border-top-color: #222222;
                Border-style: solid;
                Border-width: 1px;
                font-family: Verdana;
                font-size: <?php echo 10 - $plus; ?>px;
            }
        </style>
        <script type="text/javascript">
            function hilfe(hid) {
                window.top.Mediabox.open('inhalt/hilfe.php?fu2='+hid+'&uid=<?php echo $uid; ?>&sid=<?php echo $sid; ?>&sprache=<?php echo $_GET["sprache"] ?>', '', '520 220');
            }
        </script>
    </head>