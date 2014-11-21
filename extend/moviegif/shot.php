<?php

if (!defined('PRDYZ_INSIDE')) die('GO AWAY!!!');

$moviegif_files_path = '../../files/moviegif/';

$bild_position_x = 0;
$bild_position_y = 0;

$hoehe = 350;
$breite = 350;

$hoehe_komplett = 375;
$breite_komplett = 350;

////////vorbereitungen

$bild = imagecreate($breite_komplett,$hoehe_komplett);
$hintergrundbild = ImageCreateFromGIF('./img/map350x375.gif');
$scanbild = ImageCreateFromGIF('./img/scan.gif');

$color[background] = imagecolorallocate($bild, 44, 44, 44);
$color[linien] = imagecolorallocate($bild, 30, 30, 30);

$color[grey]  = imagecolorallocate($bild, 69, 69, 69);
$color[black]  = imagecolorallocate($bild, 0, 0, 0);
$color[blue]  = imagecolorallocate($bild, 0, 0, 255);
$color[white]  = imagecolorallocate($bild, 255, 255, 255);

$color[spieler][0] = imagecolorallocate($bild, 170, 170, 170);
$color[spieler][1] = imagecolorallocate($bild, 29, 199, 16);
$color[spieler][2] = imagecolorallocate($bild, 229, 226, 3);
$color[spieler][3] = imagecolorallocate($bild, 234, 165, 0);
$color[spieler][4] = imagecolorallocate($bild, 135, 95, 0);
$color[spieler][5] = imagecolorallocate($bild, 187, 0, 0);
$color[spieler][6] = imagecolorallocate($bild, 215, 0, 193);
$color[spieler][7] = imagecolorallocate($bild, 125, 16, 199);
$color[spieler][8] = imagecolorallocate($bild, 16, 29, 199);
$color[spieler][9] = imagecolorallocate($bild, 4, 158, 239);
$color[spieler][10] = imagecolorallocate($bild, 16, 199, 155);

////////hintergrund einfuegen

Imagecopy($bild,$hintergrundbild,0,0,0,0,$breite_komplett,$hoehe_komplett);

////////aktuelle karte einfuegen

$zeiger = @mysql_query("SELECT * FROM skrupel_spiele where id=$spiel");
$datensaetze = @mysql_num_rows($zeiger);

if ($datensaetze==1) {

	$array = @mysql_fetch_array($zeiger);
	$umfang=$array['umfang'];
	$spiel_runde=$array['runde'];
	$spiel_name=$array['name'];
}

////////linien zeichnen

$sektoranzahl=round($umfang/250)-1;

for ($n=1;$n<=$sektoranzahl;$n++) {
	$x=(250*$n)/$umfang*$breite;
	$y=(250*$n)/$umfang*$hoehe;
		imageline ($bild, $x, 0, $x, $hoehe-1, $color[linien]);
		imageline ($bild, 0, $y, $breite-1, $y, $color[linien]);
}

////////schiffe und planeten ziehen

$zeiger_planeten = @mysql_query("SELECT id,x_pos,y_pos,besitzer,sternenbasis FROM skrupel_planeten where spiel=$spiel order by id");
$datensaetze_planeten = @mysql_num_rows($zeiger_planeten);

$zeiger_schiffe = @mysql_query("SELECT volk,bild_klein,masse,kox_old,koy_old,klasse,schaden,antrieb,frachtraum,fracht_leute,fracht_cantox,fracht_vorrat,fracht_min1,fracht_min2,fracht_min3,lemin,leminmax,logbuch,routing_status,routing_id,routing_koord,besitzer,id,name,kox,koy,flug,zielx,ziely,zielid,techlevel,masse_gesamt,status,spezialmission,tarnfeld,extra FROM skrupel_schiffe where status>0 and spiel=$spiel order by masse desc");
$datensaetze_schiffe = @mysql_num_rows($zeiger_schiffe);

////////scankreise

if ($datensaetze_planeten>=1) {

	for  ($i=0; $i<$datensaetze_planeten;$i++) {
    $ok = @mysql_data_seek($zeiger_planeten,$i);

      $array = @mysql_fetch_array($zeiger_planeten);
      $id=$array["id"];
      $x_pos=$array["x_pos"];
      $y_pos=$array["y_pos"];
      $besitzer=$array["besitzer"];

	  $x_position=round($x_pos/$umfang*$breite);
      $y_position=round($y_pos/$umfang*$hoehe);

		if ($besitzer>=1) {
			Imagecopy($bild,$scanbild,$x_position-12,$y_position-12,0,0,25,25);
		}

	}
}

if ($datensaetze_schiffe>=1) {

	for  ($i=0; $i<$datensaetze_schiffe;$i++) {
    $ok = @mysql_data_seek($zeiger_schiffe,$i);

      $array = @mysql_fetch_array($zeiger_schiffe);
      $id=$array["id"];
      $x_pos=$array["kox"];
      $y_pos=$array["koy"];
      $besitzer=$array["besitzer"];

	  $x_position=round($x_pos/$umfang*$breite);
      $y_position=round($y_pos/$umfang*$hoehe);

		if ($besitzer>=1) {
			Imagecopy($bild,$scanbild,$x_position-12,$y_position-12,0,0,25,25);
		}

}}

////wurmloecher etc

$zeiger_anomalie = @mysql_query("SELECT * FROM skrupel_anomalien where spiel=$spiel order by id");
$datensaetze_anomalie = @mysql_num_rows($zeiger_anomalie);

if ($datensaetze_anomalie>=1) {

	for  ($i=0; $i<$datensaetze_anomalie;$i++) {
    $ok = @mysql_data_seek($zeiger_anomalie,$i);
      $array = @mysql_fetch_array($zeiger_anomalie);
      $aid=$array["id"];
      $art=$array["art"];
      $x_pos=$array["x_pos"];
      $y_pos=$array["y_pos"];
      $extra=$array["extra"];

	  $x_position=round($x_pos/$umfang*$breite);
      $y_position=round($y_pos/$umfang*$hoehe);

	if (($art==1) or ($art==2)) {
		imagesetpixel($bild,$x_position,$y_position,$color[white]);
		imagesetpixel($bild,$x_position+1,$y_position+1,$color[blue]);
		imagesetpixel($bild,$x_position-1,$y_position-1,$color[blue]);
		imagesetpixel($bild,$x_position-1,$y_position+1,$color[blue]);
		imagesetpixel($bild,$x_position+1,$y_position-1,$color[blue]);
	}

	if ($art==3) {
		imagesetpixel($bild,$x_position,$y_position,$color[white]);
	}
}}

////planeten

if ($datensaetze_planeten>=1) {

	for  ($i=0; $i<$datensaetze_planeten;$i++) {
    $ok = @mysql_data_seek($zeiger_planeten,$i);

      $array = @mysql_fetch_array($zeiger_planeten);
      $id=$array["id"];
      $x_pos=$array["x_pos"];
      $y_pos=$array["y_pos"];
      $besitzer=$array["besitzer"];
      $sternenbasis=$array["sternenbasis"];

	  $x_position=round($x_pos/$umfang*$breite);
      $y_position=round($y_pos/$umfang*$hoehe);

	  imagesetpixel($bild,$x_position,$y_position,$color[spieler][$besitzer]);

	if ($besitzer>=1) {

	  imagesetpixel($bild,$x_position-1,$y_position,$color[spieler][$besitzer]);
	  imagesetpixel($bild,$x_position+1,$y_position,$color[spieler][$besitzer]);
	  imagesetpixel($bild,$x_position,$y_position-1,$color[spieler][$besitzer]);
	  imagesetpixel($bild,$x_position,$y_position+1,$color[spieler][$besitzer]);

	if ($sternenbasis==2) {

		imagesetpixel($bild,$x_position,$y_position-3,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position,$y_position-2,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position,$y_position+2,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position,$y_position+3,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position-3,$y_position,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position-2,$y_position,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position+2,$y_position,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position+3,$y_position,$color[spieler][$besitzer]);

	}}

	}
}

////schiffe

if ($datensaetze_schiffe>=1) {

	for  ($i=0; $i<$datensaetze_schiffe;$i++) {
    $ok = @mysql_data_seek($zeiger_schiffe,$i);

      $array = @mysql_fetch_array($zeiger_schiffe);
      $id=$array["id"];
      $x_pos=$array["kox"];
      $y_pos=$array["koy"];
      $besitzer=$array["besitzer"];
      $status=$array["status"];

	  $x_position=round($x_pos/$umfang*$breite);
      $y_position=round($y_pos/$umfang*$hoehe);

	if ($status==2)  {
		imagesetpixel($bild,$x_position-1,$y_position-1,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position-1,$y_position+1,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position+1,$y_position-1,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position+1,$y_position+1,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position,$y_position-2,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position,$y_position+2,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position-2,$y_position,$color[spieler][$besitzer]);
		imagesetpixel($bild,$x_position+2,$y_position,$color[spieler][$besitzer]);
	} else {
		imagesetpixel($bild,$x_position,$y_position,$color[spieler][$besitzer]);
	}

}}

//////////infos

ImageString($bild,2,10,356,$spiel_name,$color[white]);
ImageString($bild,2,287,356,'Round '.sprintf("%03d",$spiel_runde),$color[white]);

////////schreiben der datei

$runde_anzeige = sprintf("%04d", $spiel_runde);

$scenes_dir = $moviegif_files_path . 'temp/' . $spiel . '/';
if (!dir_exists($scenes_dir)) mkdir($scenes_dir);

$scene_file = $scenes_dir . 'scene_' . $runde_anzeige . '.gif';

@ImageGif($bild,$bildfile);
@chmod($bildfile, 0777);

////////ende

ImageDestroy($bild);
ImageDestroy($hintergrundbild);
ImageDestroy($scanbild);

?>
