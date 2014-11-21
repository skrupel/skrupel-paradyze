<? if ($_GET["fu"]==1) {
include ("../inc.conf.php");
include ("inc.header.php");

  $zeiger = @mysql_query("SELECT * FROM skrupel_kampf where schiff_id_1=$_GET[sh1] and schiff_id_2=$_GET[sh2] and datum=$_GET[datum]");
  $gefechteanzahl = @mysql_num_rows($zeiger);

  $array = @mysql_fetch_array($zeiger);
  $id=$array["id"];
  $schiff_id_1=$array["schiff_id_1"];
  $schiff_id_2=$array["schiff_id_2"];
  $name_1=$array["name_1"];
  $klasse_1=$array["klasse_1"];
  $rasse_1=$array["rasse_1"];
  $bild_1=$array["bild_1"];
  $name_2=$array["name_2"];
  $klasse_2=$array["klasse_2"];
  $rasse_2=$array["rasse_2"];
  $bild_2=$array["bild_2"];
  $energetik_1=$array["energetik_1"];
  $projektile_1=$array["projektile_1"];
  $hangar_1=$array["hangar_1"];
  $schild_1=$array["schild_1"];
  $schaden_1=$array["schaden_1"];
  $energetik_2=$array["energetik_2"];
  $projektile_2=$array["projektile_2"];
  $hangar_2=$array["hangar_2"];
  $schild_2=$array["schild_2"];
  $schaden_2=$array["schaden_2"];
  $art=$array["art"];

  $energetik_1=split(":",$energetik_1);
  $energetik_2=split(":",$energetik_2);
  $projektile_1=split(":",$projektile_1);
  $projektile_2=split(":",$projektile_2);
  $schild_1=split(":",$schild_1);
  $schild_2=split(":",$schild_2);
  $hangar_1=split(":",$hangar_1);
  $hangar_2=split(":",$hangar_2);
  $schaden_1=split(":",$schaden_1);
  $schaden_2=split(":",$schaden_2);
  $crew_1=split(":",$crew_1);
  $crew_2=split(":",$crew_2);
?>

<body text="#ffffff" bgcolor="#000000" style="background: url(../bilder/hintergrund.gif) #000000;" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<script language=JavaScript>document.title='Gefechtdetails';</script>

<table border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td width='10%' align="left"></td>
	<td colspan="3" width='30%' align="center">
		<img border="0" src="../daten/<?php echo $rasse_1; ?>/bilder_schiffe/<?php echo $bild_1; ?>">
		<br><i><?php echo $name_1; ?></i>
		<br><?php echo $klasse_1; ?>
    </td>
	<td width='20%' align="center">
	<center>  
      vs.
    </center>
	</td>
	<td colspan="3" width='30%' align="center">
		<img border="0" src="../daten/<?php echo $rasse_2; ?>/bilder_schiffe/<?php echo $bild_2; ?>">
		<br><i><?php echo $name_2; ?></i>
		<br><?php echo $klasse_2; ?>
	</td>
  </tr>
  <tr><td colspan="8">&nbsp;</td></tr>
  <tr>
     <td><b>Runde</b></td>
     <td><b>Bericht</b></td>
	 <td align='right'><b>Schilde</b></td>
	 <td align='right'><b>Rumpf</b></td>
	 <td></td>
	 <td><b>Bericht</b></td>
	 <td align='right'><b>Schilde</b></td>
	 <td align='right'><b>Rumpf</b></td>
  </tr>
  
<?php

$phase = 1;

for ($r = 0; $r < (sizeof($schaden_1) -1); $r++)
{
  /*if ($r % 10 == 0) {
	echo "<tr><td colspan='3' align='center'><br><b>Phase ".$phase."</b><br>&nbsp;</td></tr>";
	$phase++;
  }*/
  
  $text_1 = "<td nowrap>";
  
  if ($energetik_2[$r] == '1') {
	$text_1 .= "<font color='#ff0000'><b>(E)</b></font> ";
  }
  if ($projektile_2[$r] == '1') {
	$text_1 .= "<font color='#ff0000'><b>(P)</b></font> ";
  }
  else if ($projektile_2[$r] == '2') {
    $text_1 .= "<font color='#00ff00'><b>(P)</b></font> ";
  }
  if ($hangar_2[$r] == '1') {
    $text_1 .= "<font color='#ff0000'><b>(J)</b></font> ";
  }
  
  //if ($text_1 == "Schadensbericht: ") $text_1 = "&nbsp;";
  
  $text_1 .= "</td><td align='right'>".$schild_1[$r]." %</td><td align='right'>".$schaden_1[$r]." %</td>";
   
  $text_2 = "<td nowrap>";
  
  if ($energetik_1[$r] == '1') {
	$text_2 .= "<font color='#ff0000'><b>(E)</b></font> ";
  }
  if ($projektile_1[$r] == '1') {
	$text_2 .= "<font color='#ff0000'><b>(P)</b></font> ";
  }
  else if ($projektile_1[$r] == '2') {
    $text_2 .= "<font color='#00ff00'><b>(P)</b></font> ";
  }
  if ($hangar_1[$r] == '1') {
    $text_2 .= "<font color='#ff0000'><b>(J)</b></font> ";
  }
  
  //if ($text_2 == "Schadensbericht: ") $text_2 = "&nbsp;";
  
  $text_2 .= "</td><td align='right'>".$schild_2[$r]." %</td><td align='right'>".$schaden_2[$r]." %</td>";

 
  
  echo "<tr><td>".($r+1)."</td>".$text_1."<td></td>".$text_2."</tr>";
  
  $zer_1 = "";
  if ($schaden_1[$r] == 0) {
	$zer_1 = "<br /><font color='#FF0000'>Schiff zerst�rt.</font>";
  }
  $zer_2 = "";
  if ($schaden_2[$r] == 0) {
	$zer_2 = "<br /><font color='#FF0000'>Schiff zerst�rt.</font>";
  }

  if ($zer_1 != "" || $zer_2 != "") {
  echo "<tr><td></td><td colspan='3' nowrap>".$zer_1."</td><td></td><td colspan='3' nowrap>".$zer_2."</td></tr>";
  }  
  
}



?>
<tr><td colspan="8">&nbsp;</td></tr>
<tr><td colspan="8"><u>Legende:</u><br><br>
<font color="#ff0000">E</font> ... Schaden durch feindliche Energetikwaffen<br>
<font color="#ff0000">P</font> ... Schaden durch feindliches Projektil<br>
<font color="#00ff00">P</font> ... Feindliches Projektil hat uns verfehlt<br>
<font color="#ff0000">J</font> ... Schaden durch feindliche Raumj�ger<br>
</td></tr>  
</table>

<?
include ("inc.footer.php");
 }


 ?>