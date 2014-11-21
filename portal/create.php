<?php
include_once 'include/global.php';

include PRDYZ_DIR_LANG.'/'.$language.'/lang.admin.create.php';

if (!$_SESSION['user_id']) {
    portal_error_message('Du musst eingelogt sein, um ein Spiel erstellen zu k&ouml;nnen.');
}

$percentage_list = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 15, 20, 30, 40, 50, 60, 70, 80, 90, 100);

switch ($_GET["fu"]) {
    
    default:
        if ($conf_enable_ai) {
            include PRDYZ_DIR_EXTEND.'/ki/ki_basis/setup.php';
        }

        include 'include/header.php';
?>
<h1>Neues Spiel erstellen</h1>

<form name="formular" method="post" action="create.php?fu=10">
<center><table border="0" cellspacing="0" cellpadding="2" style="width:90%;">
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['wie_name']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><input type="text" name="spiel_name" class="eingabe" value="" maxlength="50" style="width:250px;"></td></tr>
   <tr><td>&nbsp;</td></tr>

   <tr><td><?php echo $lang['admin']['spiel']['alpha']['siegbedingungen']['wie']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td><input type="radio" name="siegbedingungen" value="0" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['siegbedingungen']['jff']?></td></tr>
      <tr><td><input type="radio" name="siegbedingungen" value="1"></td><td>&nbsp;</td><td><table border="0" cellspacing="0" cellpadding="0"><tr><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['siegbedingungen']['ueberleben'][0]?></td><td><select name="zielinfo_1"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['siegbedingungen']['ueberleben'][1]?></td></tr></table></td></tr>
      <tr><td><input type="radio" name="siegbedingungen" value="2"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['siegbedingungen']['tf']?></td></tr>

      <tr><td><input type="radio" name="siegbedingungen" value="6"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['siegbedingungen']['ttf']?></td></tr>
      <?php /* ?>
      <tr><td><input type="radio" name="siegbedingungen" value="3"></td><td>&nbsp;</td><td><table border="0" cellspacing="0" cellpadding="0"><tr><td style="color:#aaaaaa;">Dominanz (es gilt &nbsp;</td><td><select name="zielinfo_3"><option value="30">30 %</option><option value="40">40 %</option><option value="50">50 %</option><option value="60">60 %</option><option value="70">70 %</option><option value="80">80 %</option><option value="90">90 %</option></select></td><td style="color:#aaaaaa;">&nbsp;aller Planeten zu beherrschen)</td></tr></table></td></tr>
      <tr><td><input type="radio" name="siegbedingungen" value="4"></td><td>&nbsp;</td><td><table border="0" cellspacing="0" cellpadding="0"><tr><td style="color:#aaaaaa;">King of the Planet (in der Mitte der Galaxie befindet sich ein heiliger Planet, welcher &nbsp;</td><td><select name="zielinfo_4"><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="150">150</option><option value="200">200</option></select></td><td style="color:#aaaaaa;">&nbsp;Monate beherrscht werden mu?)</td></tr></table></td></tr>
      <?php */ ?>
      <tr><td><input type="radio" name="siegbedingungen" value="5"></td><td>&nbsp;</td><td><table border="0" cellspacing="0" cellpadding="0"><tr><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['siegbedingungen']['spice'][0]?></td><td><select name="zielinfo_5"><option value="2500">2500</option><option value="5000">5000</option><option value="7500">7500</option><option value="10000">10000</option><option value="15000">15000</option></select></td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['siegbedingungen']['spice'][1]?></td></tr></table></td></tr>

     </table></td></tr>
    <tr><td>&nbsp;</td></tr>
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['optional']['welche']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td><input type="checkbox" name="modul_0" value="1" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['optional']['sus']?></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><input type="checkbox" name="modul_4" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['optional']['wysiwyg']?></td>
        </tr>
        <tr>
            <td><input type="checkbox" name="modul_2" value="1" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['optional']['mf']?></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><input type="checkbox" name="modul_5" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['optional']['f']?></td>
        </tr>
        <tr>
            <td><input type="checkbox" name="modul_3" value="1" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['optional']['tk']?></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><input type="checkbox" name="modul_6" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['optional']['h']?></td>
        </tr>

      <?php /* ?>
      <tr><td><input type="checkbox" name="modul_1" value="1" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['optional']['as'][0]?><select name="stat_delay"><?php for($i=0;$i<=15;$i++) { ?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php } ?></select><?php echo $lang['admin']['spiel']['alpha']['optional']['as'][1]?></td></tr>
            <?php */ ?>
      </table></td></tr>

</table>
<input type="submit" name="bla" value="<?php echo str_replace('{1}',2,$lang['admin']['spiel']['alpha']['weiter_'])?>"></center>
</form>
<?php
        include 'include/footer.php';
        break;

    case 10:
        if (strlen($_POST['spiel_name']) < 5) {
            portal_error_message('Der Name eines Spiels muss mindestens 5 Zeichen lang sein.');
        }

        include 'include/header.php';
?>
<h1>Neues Spiel erstellen</h1>

<form name="formular" method="post" action="create.php?fu=2">
<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>
<center><table border="0" cellspacing="0" cellpadding="2" style="width:90%;">
  <tr><td><?php echo $lang['admin']['spiel']['alpha']['struktur']?><br><br></td></tr>
  <tr>
<?php
$zahl = 0;
        include ("../lang/" . $language . "/lang.admin.galaxiestrukturen.php");
        $file = '../daten/gala_strukturen.txt';
        $fp = @fopen("$file", "r");
        if ($fp) {
            $zaehler = 0;
            while (!feof($fp)) {
                $buffer = @fgets($fp, 4096);
                $struktur[$zaehler] = $buffer;
                $zaehler++;
            }
            @fclose($fp);
        }

        for ($n = 0; $n < $zaehler; $n++) {

            $strukturdaten = explode(':', $struktur[$n]);
?>
<td with="50%"><table border="0" cellspacing="0" cellpadding="2" with="100%">
 <tr>
   <td><input type="radio" name="struktur" value="<?php echo $strukturdaten[1]; ?>" <?php if ($n==0) { echo 'checked'; } ?>></td>
   <td style="color:#aaaaaa;" colspan="2"><?php echo $lang['admin']['galaxiestrukturen'][$strukturdaten[0]]; ?></td>
 </tr>
 <tr>
   <td></td>
   <td><table border="0" cellspacing="0" cellpadding="0">
     <tr>
       <td><img src="../bilder/admin/gala_1.gif"></td>
       <td><img src="../bilder/admin/gala_2.gif"></td>
       <td><img src="../bilder/admin/gala_3.gif"></td>
     </tr>
     <tr>
       <td><img src="../bilder/admin/gala_4.gif"></td>
       <td background="../daten/bilder_galaxien/<?php echo $strukturdaten[1]; ?>.png"><img src="../bilder/admin/gala_stars.gif"></td>
       <td><img src="../bilder/admin/gala_5.gif"></td>
     </tr>
     <tr>
       <td><img src="../bilder/admin/gala_6.gif"></td>
       <td><img src="../bilder/admin/gala_7.gif"></td>
       <td><img src="../bilder/admin/gala_8.gif"></td>
     </tr>
   </table></td>
   <td width="100%"><center><?php echo $lang['admin']['spiel']['alpha']['spieleranzahl']?><br><br>
     <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><input type="button" style="height:20px;width:20px;" value="1"></td>
        <td><input type="button" style="height:20px;width:20px;" value="<?php if (strstr($strukturdaten[2],'2')) { echo '2'; } ?>"></td>
        <td><input type="button" style="height:20px;width:20px;" value="<?php if (strstr($strukturdaten[2],'3')) { echo '3'; } ?>"></td>
        <td><input type="button" style="height:20px;width:20px;" value="<?php if (strstr($strukturdaten[2],'4')) { echo '4'; } ?>"></td>
        <td><input type="button" style="height:20px;width:20px;" value="<?php if (strstr($strukturdaten[2],'5')) { echo '5'; } ?>"></td>
        <td><input type="button" style="height:20px;width:20px;" value="<?php if (strstr($strukturdaten[2],'6')) { echo '6'; } ?>"></td>
        <td><input type="button" style="height:20px;width:20px;" value="<?php if (strstr($strukturdaten[2],'7')) { echo '7'; } ?>"></td>
        <td><input type="button" style="height:20px;width:20px;" value="<?php if (strstr($strukturdaten[2],'8')) { echo '8'; } ?>"></td>
        <td><input type="button" style="height:20px;width:20px;" value="<?php if (strstr($strukturdaten[2],'9')) { echo '9'; } ?>"></td>
        <td><input type="button" style="height:20px;width:20px;" value="<?php if (strstr($strukturdaten[2],'10')) { echo '10'; } ?>"></td>
      <tr>
     </table>
   </center></td>
 </tr>

</table></td>
<?php
    $zahl++;
    if ($zahl == 2) {
       $zahl = 0;
       echo '</tr>';
    }
}
?>
</table>
<input type="submit" name="bla" value="<?php echo str_replace('{1}',3,$lang['admin']['spiel']['alpha']['weiter_'])?>"></center>
</form>
<?php
          include 'include/footer.php';
          break;

    case 2:
        include 'include/header.php';
?>
<h1>Neues Spiel erstellen</h1>

<form name="formular" method="post" action="create.php?fu=3">
<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>

<center><table border="0" cellspacing="0" cellpadding="2" style="width:90%;">
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['startpositionen']['wie']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td><input type="radio" name="startposition" value="1" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['startpositionen']['vorg']?></td></tr>
      <tr><td><input type="radio" name="startposition" value="2"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['startpositionen']['zufl']?></td></tr>
      <tr><td><input type="radio" name="startposition" value="3"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['startpositionen']['admin']?></td></tr>
     </table></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['groesse']['welche']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <?php /* ?>
      <tr><td><input type="radio" name="imperiumgroesse" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['groesse']['1']?></td></tr>
      <?php */ ?>
      <tr><td><input type="radio" name="imperiumgroesse" value="2" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['groesse']['2']?></td></tr>
      <?php /* ?>
      <tr><td><input type="radio" name="imperiumgroesse" value="3"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['groesse']['3']?></td></tr>
      <?php */ ?>
      <tr><td><input type="radio" name="imperiumgroesse" value="4"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['groesse']['4']?></td></tr>
      <?php /* ?>
      <tr><td><input type="radio" name="imperiumgroesse" value="5"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['groesse']['5']?></td></tr>
      <tr><td><input type="radio" name="imperiumgroesse" value="6"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['groesse']['6']?></td></tr>
      <?php */ ?>
   </table></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['ausscheiden']['wann']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
   <tr><td><input type="radio" name="out" value="3" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['ausscheiden']['1']?></td></tr>
   <tr><td><input type="radio" name="out" value="2"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['ausscheiden']['2']?></td></tr>
   <tr><td><input type="radio" name="out" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['ausscheiden']['3']?></td></tr>
      <tr><td><input type="radio" name="out" value="0"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['ausscheiden']['4']?></td></tr>
      </table></td></tr>
</table>
<input type="submit" name="bla" value="<?php echo str_replace('{1}',4,$lang['admin']['spiel']['alpha']['weiter_'])?>"></center>
</form>
<?php
          include 'include/footer.php';
          break;

    case 3:
        $file = '../daten/gala_strukturen.txt';
        $fp = @fopen("$file", "r");
        if ($fp) {
            while (!feof($fp)) {
                $buffer = @fgets($fp, 4096);
                $strukturdaten = explode(':', $buffer);
                if ($strukturdaten[1] == $_POST["struktur"]) {
                    $spieleranzahlmog = trim($strukturdaten[2]);
                }
            }
            @fclose($fp);
        }

        include 'include/header.php';
?>
<script language="JavaScript" type="text/javascript">

  spielermog = new Array(0,1,<?php echo (strstr($spieleranzahlmog, '2') ? '1' : '0'); ?>,<?php echo (strstr($spieleranzahlmog, '3') ? '1' : '0'); ?>,<?php echo (strstr($spieleranzahlmog, '4') ? '1' : '0'); ?>,<?php echo (strstr($spieleranzahlmog, '5') ? '1' : '0'); ?>,<?php echo (strstr($spieleranzahlmog, '6') ? '1' : '0'); ?>,<?php echo (strstr($spieleranzahlmog, '7') ? '1' : '0'); ?>,<?php echo (strstr($spieleranzahlmog, '8') ? '1' : '0'); ?>,<?php echo (strstr($spieleranzahlmog, '9') ? '1' : '0'); ?>,<?php echo (strstr($spieleranzahlmog, '10') ? '1' : '0'); ?>);

function check() {

  var spieleranzahl=0;

  if (document.formular.user_1.value != '0') { spieleranzahl++; }
  if (document.formular.user_2.value != '0') { spieleranzahl++; }
  if (document.formular.user_3.value != '0') { spieleranzahl++; }
  if (document.formular.user_4.value != '0') { spieleranzahl++; }
  if (document.formular.user_5.value != '0') { spieleranzahl++; }
  if (document.formular.user_6.value != '0') { spieleranzahl++; }
  if (document.formular.user_7.value != '0') { spieleranzahl++; }
  if (document.formular.user_8.value != '0') { spieleranzahl++; }
  if (document.formular.user_9.value != '0') { spieleranzahl++; }
  if (document.formular.user_10.value != '0') { spieleranzahl++; }

<?php if ($_POST["startposition"] == 1): ?>
  if (spielermog[spieleranzahl]==0) {
    alert ('F&uuml;r die gew&auml;hlte Galaxiestruktur sind nur folgene Spielerzahlen m&ouml;glich: <?=$spieleranzahlmog?>');
    return false;
  }
<?php endif; ?>

<?php for ($oprt = 1; $oprt <= 10; $oprt++): ?>
  if (document.formular.user_<?php echo $oprt; ?>.value >= '1') {
         var anzahle=0;
           <?php for ($n=1;$n<=10;$n++): ?>
             if (document.formular.user_<?=$n?>.value == document.formular.user_<?=$oprt?>.value) { anzahle++; }
           <?php endfor; ?>
         if (anzahle!=1) {
           alert("Jeder Spieler kann nur einen Slot belegen.");
           return false;
         }
   }
<?php endfor; ?>
<?php if ($_POST["siegbedingungen"]==6): ?>
    <?php for ($oprt=1;$oprt<=10;$oprt++): ?>
  if (document.formular.user_<?=$oprt?>.value >= '1' || document.formular.user_<?=$oprt?>.value == '-1') {
        <?php for ($op=0;$op<=4;$op++): ?>
     if (document.formular.team<?=$oprt?>[<?=$op?>].checked == true) {
         var anzahl=0;
           <?php for ($n=1;$n<=10;$n++): ?>
             if (document.formular.team<?=$n?>[<?=$op?>].checked == true) { anzahl++; }
           <?php endfor; ?>
         if (anzahl!=2) {
           alert("In jedem aktivierten Team m&uuml;ssen genau 2 Spieler sein.");
           return false;
         }
     }
        <?php endfor; ?>
      if ((document.formular.team<?=$oprt?>[0].checked == false) <?php for ($n=1;$n<=4;$n++): ?> && (document.formular.team<?=$oprt?>[<?=$n?>].checked == false)<?php endfor; ?>)  {
          alert("Jeder Spieler muss einem Team zugeordnet werden.");
          return false;
      }
  } else {
      if ((document.formular.team<?=$oprt?>[0].checked == true) <?php for ($n=1;$n<=4;$n++): ?> || (document.formular.team<?=$oprt?>[<?=$n?>].checked == true)<?php endfor; ?>)  {
          alert("Unbelegte Slots k&ouml;nnen keinem Team zugeordnet werden.");
          return false;
      }
  }
    <?php endfor; ?>
<?php endif; ?>
  return true;
}

</script>

<h1>Neues Spiel erstellen</h1>

<form name="formular" method="post" action="create.php?fu=4" onSubmit="return check();">
<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>

<center>
<p>Wer spielt mit und mit welchem Volk?</p>

<?php
$verzeichnis = PRDYZ_DIR_ROOT.'/daten';
$handle = opendir($verzeichnis);

$zaehler = 0;
while ($file = readdir($handle)) {
    if ((substr($file, 0, 1) <> '.') and (substr($file, 0, 7) <> 'bilder_') and (substr($file, strlen($file) - 4, 4) <> '.txt')) {
        if (trim($file) != 'unknown' && trim($file) != 'CVS') {
            $datei = $verzeichnis.'/'.$file.'/daten.txt';
            $fp = @fopen($datei, "r");
            if ($fp) {
                $zaehler2 = 0;
                while (!feof($fp)) {
                    $buffer = @fgets($fp, 4096);
                    $daten[$zaehler][$zaehler2] = $buffer;
                    $zaehler2++;
                }
                @fclose($fp);
            }

            $filename[$zaehler] = $file;

            $zaehler++;
        }
    }
}
closedir($handle);

  $zeiger = @mysql_query("SELECT * FROM skrupel_user order by nick");
?>
    <table align="center" cellpadding="5" cellspacing="2" width="100%">
   <tr>
	<th class="headcell" align="center" width="4%"><b>#</b></td>
	<th class="headcell" align="center" width="30%"><b>Spieler</b></td>
	<th class="headcell" align="center" width="30%"><b>Rasse</b></td>
	<th class="headcell" align="center" width="6%"><center><b>I</b></center></td>
	<th class="headcell" align="center" width="6%"><center><b>II</b></center></td>
	<th class="headcell" align="center" width="6%"><center><b>III</b></center></td>
	<th class="headcell" align="center" width="6%"><center><b>IV</b></center></td>
	<th class="headcell" align="center" width="6%"><center><b>V</b></center></td>
	<th class="headcell" align="center" width="6%"><center><b>-</b></center></td>
   </tr>
<?php for ($k = 1; $k <= 10; $k++): ?>
        <tr>
				  <td class="cell" style="background-color: <?=$spielerfarbe[$k]?>;" align="center">&nbsp;<input type="hidden" name="spieler_admin" value="<?=$_SESSION["user_id"]?>">&nbsp;</td>
          <td class="cell" align="center"><select name="user_<?=$k?>" style="width:180px;">
    <?php if ($k > 1): ?>
             <option value="0" onclick="document.formular.rasse_<?=$k?>.options[0].disabled=''; document.formular.rasse_<?=$k?>.options[0].selected = true;">- Slot bleibt leer -</option>
             <option value="-1" onclick="document.formular.rasse_<?=$k?>.options[0].disabled=''; document.formular.rasse_<?=$k?>.options[0].selected = true;">- Warte auf Spieler -</option>
        <?php while ($data = mysql_fetch_array($zeiger)):
   $check = explode(' ', $nick); ?>
            <?php if(count($check) == 3 && $check[0] == 'KI' && $check[2] == ($k-1)): ?>
             <option onclick="document.formular.rasse_<?=$k?>.options[0].disabled='disabled';document.formular.rasse_<?=$k?>.options[1].selected = true;" value="<?=$data["id"]?>"><?=$data["nick"]?></option>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php else: ?>
             <option value="<?php echo $_SESSION["user_id"]; ?>"><?php echo $_SESSION["name"]; ?></option>";
    <?php endif; ?>
   </select></td>
          <td class="cell" align="center"><select name="rasse_<?=$k?>" style="width:180px;">
    <?php if ($k != 1): ?>
                  <option value="-1">- Durch Spieler w&auml;hlbar -</option>
    <?php endif; ?>
    <?php for ($n = 0; $n < $zaehler; $n++): ?>
             <option value="<?=$filename[$n]?>"><?=$daten[$n][0]?></option>
    <?php endfor; ?>
           </select></td>
    <?php if ($_POST["siegbedingungen"] == 6): ?>
   <td class="cell"><center><input type="radio" name="team<?=$k?>" value="1"></center></td>
   <td class="cell"><center><input type="radio" name="team<?=$k?>" value="2"></center></td>
   <td class="cell"><center><input type="radio" name="team<?=$k?>" value="3"></center></td>
   <td class="cell"><center><input type="radio" name="team<?=$k?>" value="4"></center></td>
   <td class="cell"><center><input type="radio" name="team<?=$k?>" value="5"></center></td>
   <td class="cell"><center><input type="radio" name="team<?=$k?>" value="0" checked></center></td>
    <? else: ?>
   <td class="cell" colspan="6" align="center"><center>Einzelk&auml;mpfer</center></td>
    <? endif; ?>
        </tr>
<? endfor; ?>
   <tr>
   <td><!--<table border="0" cellspacing="0" cellpadding="0"><tr><td><input type="radio" name="spieler_admin" value="0" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;">niemand</td></tr></table>--></td>
   </tr>
</table>
<input type="submit" name="bla" value="Weiter mit Schritt 5">
</center>
</form>
<?php 
        include 'include/footer.php';
        break;

    case 4:
        include 'include/header.php';

        if (($_GET["startposset"] !== '1') and ($_POST["startposition"] == 3)) {
?>
<script type="text/javascript">
    function check () {
        for (n=0;n<document.formular.active.length;n++) {
            feldnamex = 'user_' + document.formular.active[n].value + '_xx';
            if (document.getElementById(feldnamex).value == '') {
                alert ('<?php echo $lang['admin']['spiel']['alpha']['jeder_startposition']; ?>');
                return false;
            }
        }
        return true;
    }
</script>

<h1>Neues Spiel erstellen</h1>
<form name="formular" method="post" action="create.php?fu=4&startposset=1" onSubmit="return check();">
<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>
    <center>
        <table border="0" cellspacing="0" cellpadding="2" style="width:90%;">
            <tr>
                <td colspan="3">
                    <center><?php echo $lang['admin']['spiel']['alpha']['wo_startposition']?></center>
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td valign="top" style="width:61%;">
                    <table border="0" cellspacing="0" cellpadding="0" style="float:right;">
                        <tr>
                            <td colspan="3"><img src="../bilder/aufbau/galaoben.gif" border="0" width="258" height="4"></td>
                        </tr>
                        <tr>
                            <td><img src="../bilder/aufbau/galalinks.gif" border="0" width="4" height="250"></td>
                            <td><iframe src="create.php?fu=12&struktur=<?php echo $_POST["struktur"]; ?>" width="250" height="250" name="map" scrolling="no" marginheight="0" marginwidth="0" frameborder="0"></iframe></td>
                            <td><img src="../bilder/aufbau/galarechts.gif" border="0" width="4" height="250"></td>
                        </tr>
                        <tr>
                            <td colspan="3"><img src="../bilder/aufbau/galaunten.gif" border="0" width="258" height="4"></td>
                        </tr>
                    </table>
                </td>
                <td>&nbsp;</td>
                <td valign="top" style="width:39%;">
                    <table border="0" cellspacing="0" cellpadding="2">
                <?php
                $first = 0;
                for ($n=1;$n<=10;$n++) {
                    if (intval($_POST['user_'.$n]) >= 1) {
                        $zeiger_temp = @mysql_query("SELECT * FROM skrupel_user where id = ".intval($_POST['user_'.$n]));
                        $array_temp = @mysql_fetch_array($zeiger_temp);
                        echo '<tr><td><input type="radio" name="active" id="active" value="'.$n.'" ';
                        if ($first == 0) {
                            $first = 1;
                            echo 'checked';
                        }
                        echo '></td><td style="color:'.$spielerfarbe[$n].';">';
                        echo $array_temp["nick"].'</td><td><input type="hidden" name="user_'.$n.'_x" id="user_'.$n.'_xx" value=""><input type="hidden" name="user_'.$n.'_y" id="user_'.$n.'_yy" value=""></td></tr>';
                    }
                }
                ?>
                    </table>
                </td>
            </tr>
        </table>
    <input type="submit" name="bla" value="Weiter"></center></form>
<?php
    } else {
?>
<h1>Neues Spiel erstellen</h1>

<form name="formular" method="post" action="create.php?fu=5">
<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>

<center><table border="0" cellspacing="0" cellpadding="2" style="width:90%;">
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['cantox']['wieviel']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td><input type="radio" name="geldmittel" value="15000"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['cantox']['1']?></td></tr>
      <tr><td><input type="radio" name="geldmittel" value="10000"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['cantox']['2']?></td></tr>
      <tr><td><input type="radio" name="geldmittel" value="5000" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['cantox']['3']?></td></tr>
      <tr><td><input type="radio" name="geldmittel" value="1000"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['cantox']['4']?></td></tr>
  </table></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['res']['wieviel']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td><input type="radio" name="mineralienhome" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res']['1']?></td></tr>
      <tr><td><input type="radio" name="mineralienhome" value="2" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res']['2']?></td></tr>
      <tr><td><input type="radio" name="mineralienhome" value="3"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res']['3']?></td></tr>
      <tr><td><input type="radio" name="mineralienhome" value="4"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res']['4']?></td></tr>
  </table></td></tr>
   <tr><td>&nbsp;</td></tr>
</table>
    <input type="submit" name="bla" value="<?php echo str_replace('{1}',6,$lang['admin']['spiel']['alpha']['weiter_'])?>"></center></form>
<?php
}

        include 'include/footer.php';
        break;

    case 5:
        include 'include/header.php';
?>
<h1>Neues Spiel erstellen</h1>

<form name="formular" method="post" action="create.php?fu=6">
<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>

<center><table border="0" cellspacing="0" cellpadding="2" style="width:90%;">
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['welche']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><input type="radio" name="umfang" value="1000"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['a']['1']?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input type="radio" name="umfang" value="3000"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['a']['5']?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input type="radio" name="sternendichte" value="400" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['b']['1']?></td>
      </tr>
      <tr>
        <td><input type="radio" name="umfang" value="1500"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['a']['2']?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input type="radio" name="umfang" value="3500"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['a']['6']?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input type="radio" name="sternendichte" value="300"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['b']['2']?></td>
      </tr>
     <tr>
        <td><input type="radio" name="umfang" value="2000"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['a']['3']?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input type="radio" name="umfang" value="4000"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['a']['7']?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input type="radio" name="sternendichte" value="200"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['b']['3']?></td>
     </tr>
     <tr>
        <td><input type="radio" name="umfang" value="2500" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['a']['4']?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input type="radio" name="umfang" value="4500"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['a']['8']?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input type="radio" name="sternendichte" value="100"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['umfang_dicht']['b']['4']?></td>
     </tr>
 </table></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['res_n_hq']['wieviel']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td><input type="radio" name="mineralien" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res_n_hq']['1']?></td><td>&nbsp;&nbsp;</td><td><input type="radio" name="leminvorkommen" value="3"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res_n_hq']['5']?></td></tr>
      <tr><td><input type="radio" name="mineralien" value="2"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res_n_hq']['2']?></td><td>&nbsp;&nbsp;</td><td><input type="radio" name="leminvorkommen" value="2" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res_n_hq']['6']?></td></tr>
      <tr><td><input type="radio" name="mineralien" value="3" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res_n_hq']['3']?></td><td>&nbsp;&nbsp;</td><td><input type="radio" name="leminvorkommen" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res_n_hq']['7']?></td></tr>
      <tr><td><input type="radio" name="mineralien" value="4"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['res_n_hq']['4']?></td><td></td><td></td><td></td><td></td></tr>
  </table></td></tr>

   <tr><td>&nbsp;</td></tr>

</table>
    <input type="submit" name="bla" value="<?php echo str_replace('{1}',7,$lang['admin']['spiel']['alpha']['weiter_'])?>"></center></form>
<?php
        include 'include/footer.php';
        break;

    case 6:
        include 'include/header.php';
?>
<h1>Neues Spiel erstellen</h1>

<form name="formular" method="post" action="create.php?fu=7">
<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>

<center><table border="0" cellspacing="0" cellpadding="2" style="width:90%;">

   <tr><td><?php echo $lang['admin']['spiel']['alpha']['ds']['wie']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td><input type="radio" name="spezien" value="0"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['ds']['1']?></td></tr>
      <tr><td><input type="radio" name="spezien" value="25"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['ds']['2']?></td></tr>
      <tr><td><input type="radio" name="spezien" value="50" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['ds']['3']?></td></tr>
      <tr><td><input type="radio" name="spezien" value="75"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['ds']['4']?></td></tr>
      <tr><td><input type="radio" name="spezien" value="100"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['ds']['5']?></td></tr>
  </table></td></tr>
    <tr><td>&nbsp;</td></tr>
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['plasmasturm_frage']['1']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['plasmasturm_frage']['2']?></td><td>&nbsp;</td><td><select name="max" style="width:100px;">
    <option value="0">0</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5" selected>5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
    <option value="9">9</option>
    <option value="10">10</option>
    </select></td></tr>
      <tr><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['plasmasturm_frage']['3']?></td><td>&nbsp;</td><td><select name="wahr" style="width:100px;">
	<?php for($p=1;$p<21;$p++){ ?>
		<option value="<?php echo $percentage_list[$p]?>"><?php echo str_replace('{1}',$percentage_list[$p],$lang['admin']['spiel']['alpha']['vh']);?></option>
	<?php } ?>
    </select></td></tr>
      <tr><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['plasmasturm_frage']['4']?></td><td>&nbsp;</td><td><select name="lang" style="width:100px;">
	<?php for($p=3;$p<13;$p++){ ?>
		<option value="<?php echo $percentage_list[$p]?>"><?php echo str_replace('{1}',$percentage_list[$p],$lang['admin']['spiel']['alpha']['runden']);?></option>
	<?php } ?>
    </select></td></tr>
      </table></td></tr>

   <tr><td>&nbsp;</td></tr>
</table>
    <input type="submit" name="bla" value="<?php echo str_replace('{1}',8,$lang['admin']['spiel']['alpha']['weiter_'])?>"></center></form>
<?php
        include 'include/footer.php';
        break;

    case 7:
        include 'include/header.php';
?>
<h1>Neues Spiel erstellen</h1>

<form name="formular" method="post" action="create.php?fu=8">
<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>

<center><table border="0" cellspacing="0" cellpadding="2" style="width:90%;">

   <tr><td><?php echo $lang['admin']['spiel']['alpha']['wurmloch_frage']['1']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td><input type="radio" name="instabil" value="0" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;">00</td><td>&nbsp;</td><td><input type="radio" name="instabil" value="2"></td><td>&nbsp;</td><td style="color:#aaaaaa;">02</td><td>&nbsp;</td><td><input type="radio" name="instabil" value="10"></td><td>&nbsp;</td><td style="color:#aaaaaa;">10</td><td>&nbsp;</td><td><input type="radio" name="instabil" value="30"></td><td>&nbsp;</td><td style="color:#aaaaaa;">30</td></tr>
      <tr><td><input type="radio" name="instabil" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;">01</td><td>&nbsp;</td><td><input type="radio" name="instabil" value="5"></td><td>&nbsp;</td><td style="color:#aaaaaa;">05</td><td>&nbsp;</td><td><input type="radio" name="instabil" value="20"></td><td>&nbsp;</td><td style="color:#aaaaaa;">20</td><td>&nbsp;</td><td><input type="radio" name="instabil" value="50"></td><td>&nbsp;</td><td style="color:#aaaaaa;">50</td></tr>
 </table></td></tr>

   <tr><td>&nbsp;</td></tr>
   <tr><td><?php echo $lang['admin']['spiel']['alpha']['wurmloch_frage']['2']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td><input type="radio" name="stabil" value="0" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['1']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['2']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="2"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['3']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="3"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['4']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="4"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['5']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="5"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['6']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="6"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['7']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="7"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['8']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="8"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['9']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="9"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['10']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="10"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['11']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="11"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['12']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="12"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['13']?></td></tr>
      <tr><td><input type="radio" name="stabil" value="13"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['wurmloch_stabil']['14']?></td></tr>
  </table></td></tr>

       <tr><td>&nbsp;</td></tr>
</table>
    <input type="submit" name="bla" value="<?php echo str_replace('{1}',9,$lang['admin']['spiel']['alpha']['weiter_'])?>"></center></form>
<?php
        include 'include/footer.php';
        break;

    case 8:
        include 'include/header.php';
?>
<h1>Neues Spiel erstellen</h1>

<form name="formular" method="post" action="create.php?fu=100">
<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>

<center><table border="0" cellspacing="0" cellpadding="2" style="width:90%;">

   <tr><td><?php echo $lang['admin']['spiel']['alpha']['nebel']['wie']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td><input type="radio" name="nebel" value="0"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['nebel']['1']?></td></tr>
      <tr><td><input type="radio" name="nebel" value="1"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['nebel']['2']?></td></tr>
      <tr><td><input type="radio" name="nebel" value="2"></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['nebel']['3']?></td></tr>
      <tr><td><input type="radio" name="nebel" value="3" checked></td><td>&nbsp;</td><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['nebel']['4']?></td></tr>
     </table></td></tr>
   <tr><td>&nbsp;</td></tr>

   <tr><td><?php echo $lang['admin']['spiel']['alpha']['raumpiratenfrage']['1']?></td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td><table border="0" cellspacing="0" cellpadding="0">
      <tr><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['raumpiratenfrage']['2']?></td><td>&nbsp;</td><td><select name="piraten_mitte" style="width:60px;">
	<?php for($p=0;$p<21;$p++){ ?>
		<option value="<?php echo $percentage_list[$p]?>"><?php echo str_replace('{1}',$percentage_list[$p],$lang['admin']['spiel']['alpha']['vh']);?></option>
	<?php } ?>
    </select></td></tr>
      <tr><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['raumpiratenfrage']['3']?></td><td>&nbsp;</td><td><select name="piraten_aussen" style="width:60px;">
	<?php for($p=0;$p<21;$p++){ ?>
		<option value="<?php echo $percentage_list[$p]?>"><?php echo str_replace('{1}',$percentage_list[$p],$lang['admin']['spiel']['alpha']['vh']);?></option>
	<?php } ?>
    </select></td></tr>
      <tr><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['raumpiratenfrage']['4']?></td><td>&nbsp;</td><td><select name="piraten_min" style="width:60px;">
	<?php for($p=1;$p<21;$p++){ ?>
		<option value="<?php echo $percentage_list[$p]?>"><?php echo str_replace('{1}',$percentage_list[$p],$lang['admin']['spiel']['alpha']['vh']);?></option>
	<?php } ?>
    </select></td></tr>
      <tr><td style="color:#aaaaaa;"><?php echo $lang['admin']['spiel']['alpha']['raumpiratenfrage']['5']?></td><td>&nbsp;</td><td><select name="piraten_max" style="width:60px;">
	<?php for($p=1;$p<21;$p++){ ?>
		<option value="<?php echo $percentage_list[$p]?>"><?php echo str_replace('{1}',$percentage_list[$p],$lang['admin']['spiel']['alpha']['vh']);?></option>
	<?php } ?>
    </select></td></tr>

      </table></td></tr>

    <tr><td>&nbsp;</td></tr>
</table>
    <input type="submit" name="bla" value="<?php echo $lang['admin']['spiel']['alpha']['erstellen']?>"></center></form>
<?php
        include 'include/footer.php';
        break;

    case 12:
?>
<script type="text/javascript">
function auswahl(xx, yy) {
    for (n=0;n<parent.document.formular.active.length;n++) {
        if (parent.document.formular.active[n].checked) {
            feldnamex = 'user_' + parent.document.formular.active[n].value + '_xx';
            feldnamey = 'user_' + parent.document.formular.active[n].value + '_yy';
            parent.document.getElementById(feldnamex).value = xx;
            parent.document.getElementById(feldnamey).value = yy;
            elementname = 'start_' + parent.document.formular.active[n].value;
            element = document.getElementById(elementname);
            element.style.top = (yy*2.5)-3.5;
            element.style.left = (xx*2.5)-3.5;
        }
    }
}

var IE = document.all?true:false;
var NS = (!document.all && document.getElementById)?true:false;
if (!IE && !NS) {
    document.captureEvents(Event.MOUSEMOVE);
}
document.onmousemove = getMouseXY;

var tempX = 0;
var tempY = 0;

function getMouseXY(e) {

  if (IE) {
    tempXX = event.clientX + document.body.scrollLeft;
    tempYY = event.clientY + document.body.scrollTop;
  } else {
    tempXX = e.pageX;
    tempYY = e.pageY;
  }

  if (tempXX!=tempX) {


  tempX=tempXX;
  tempY=tempYY;
  if (tempX < 0){tempX = 0;}
  if (tempY < 0){tempY = 0;;}

  }

}
</script>

<div id="galastruktur" style="z-index:1;position: absolute; left:0px; top:0px; width: 250px; height: 250px;">
    <img src="../daten/bilder_galaxien/<?php echo $_GET['struktur']; ?>.png" width="250" height="250">
</div>
<div id="stars" style="z-index:2;position: absolute; left:0px; top:0px; width: 250px; height: 250px;">
    <img src="../bilder/admin/gala_stars.gif" width="250" height="250" border="0">
</div>
<div id="empty" style="z-index:4;position: absolute; left:0px; top:0px; width: 250px; height: 250px;">
    <a href="#" onclick="auswahl(tempX/2.5, tempY/2.5);" style="cursor:crosshair;"><img src="../bilder/empty.gif" width="250" height="250" border="0"></a>
</div>
<?php for ($n = 1; $n <= 10; $n++): ?>
<div id="start_<?php echo $n; ?>" style="z-index:3;position: absolute; left:-1000px; top:-1000px; width: 7px; height: 7px;">
    <img src="../bilder/karte/farben/schiff_5_<?php echo $n; ?>.gif" width="7" height="7" border="0">
</div>
<?php endfor; ?>
<?php
        break;

    case 100:
        $sql = "INSERT INTO skrupel_waiting_games ( spiel_name,
                                           siegbedingungen,
                                           zielinfo_1,
                                           zielinfo_2,
                                           zielinfo_3,
                                           zielinfo_4,
                                           zielinfo_5,
                                           `out`,
                                           user_1,
                                           user_2,
                                           user_3,
                                           user_4,
                                           user_5,
                                           user_6,
                                           user_7,
                                           user_8,
                                           user_9,
                                           user_10,
                                           rasse_1,
                                           rasse_2,
                                           rasse_3,
                                           rasse_4,
                                           rasse_5,
                                           rasse_6,
                                           rasse_7,
                                           rasse_8,
                                           rasse_9,
                                           rasse_10,
                                           spieler_admin,
                                           startposition,
                                           imperiumgroesse,
                                           geldmittel,
                                           mineralienhome,
                                           sternendichte,
                                           mineralien,
                                           spezien,
                                           max,
                                           wahr,
                                           lang,
                                           instabil,
                                           stabil,
                                           leminvorkommen,
                                           umfang,
                                           struktur,
                                           modul_0,
                                           modul_2,
                                           modul_3,
                                           team1,
                                           team2,
                                           team3,
                                           team4,
                                           team5,
                                           team6,
                                           team7,
                                           team8,
                                           team9,
                                           team10,
                                           nebel,
                                           piraten_mitte,
                                           piraten_aussen,
                                           piraten_min,
                                           piraten_max
                                         ) VALUES(
                                           \"{$_POST["spiel_name"]}\",
                                           " . $_POST[siegbedingungen] . ",
                                           \"{$_POST["zielinfo_1"]}\",
                                           \"{$_POST["zielinfo_2"]}\",
                                           \"{$_POST["zielinfo_3"]}\",
                                           \"{$_POST["zielinfo_4"]}\",
                                           \"{$_POST["zielinfo_5"]}\",
                                           " . $_POST[out] . ",
                                           {$_POST["user_1"]},
                                           {$_POST["user_2"]},
                                           {$_POST["user_3"]},
                                           {$_POST["user_4"]},
                                           {$_POST["user_5"]},
                                           {$_POST["user_6"]},
                                           {$_POST["user_7"]},
                                           {$_POST["user_8"]},
                                           {$_POST["user_9"]},
                                           {$_POST["user_10"]},
                                           \"{$_POST["rasse_1"]}\",
                                           \"{$_POST["rasse_2"]}\",
                                           \"{$_POST["rasse_3"]}\",
                                           \"{$_POST["rasse_4"]}\",
                                           \"{$_POST["rasse_5"]}\",
                                           \"{$_POST["rasse_6"]}\",
                                           \"{$_POST["rasse_7"]}\",
                                           \"{$_POST["rasse_8"]}\",
                                           \"{$_POST["rasse_9"]}\",
                                           \"{$_POST["rasse_10"]}\",
                                           {$_POST["spieler_admin"]},
                                           {$_POST["startposition"]},
                                           {$_POST["imperiumgroesse"]},
                                           {$_POST["geldmittel"]},
                                           {$_POST["mineralienhome"]},
                                           {$_POST["sternendichte"]},
                                           {$_POST["mineralien"]},
                                           {$_POST["spezien"]},
                                           {$_POST["max"]},
                                           {$_POST["wahr"]},
                                           {$_POST["lang"]},
                                           {$_POST["instabil"]},
                                           {$_POST["stabil"]},
                                           {$_POST["leminvorkommen"]},
                                           {$_POST["umfang"]},
                                           \"{$_POST["struktur"]}\",
                                           " . ($_POST["modul_0"] != "" ? $_POST["modul_0"] : 0) . ",
                                           " . ($_POST["modul_2"] != "" ? $_POST["modul_2"] : 0) . ",
                                           " . ($_POST["modul_3"] != "" ? $_POST["modul_3"] : 0) . ",
                                           " . ($_POST["team1"] != "" ? $_POST["team1"] : 0 ) . ",
                                           " . ($_POST["team2"] != "" ? $_POST["team2"] : 0 ) . ",
                                           " . ($_POST["team3"] != "" ? $_POST["team3"] : 0 ) . ",
                                           " . ($_POST["team4"] != "" ? $_POST["team4"] : 0 ) . ",
                                           " . ($_POST["team5"] != "" ? $_POST["team5"] : 0 ) . ",
                                           " . ($_POST["team6"] != "" ? $_POST["team6"] : 0 ) . ",
                                           " . ($_POST["team7"] != "" ? $_POST["team7"] : 0 ) . ",
                                           " . ($_POST["team8"] != "" ? $_POST["team8"] : 0 ) . ",
                                           " . ($_POST["team9"] != "" ? $_POST["team9"] : 0 ) . ",
                                           " . ($_POST["team10"] != "" ? $_POST["team10"] : 0 ) . ",
                                           {$_POST["nebel"]},
                                           {$_POST["piraten_mitte"]},
                                           {$_POST["piraten_aussen"]},
                                           {$_POST["piraten_min"]},
                                           {$_POST["piraten_max"]}
                                          )";
        if (mysql_query($sql)) {
            portal_success_message('Das Spiel wurde in die Liste wartender Spiele aufgenommen.', 'waiting.php');
        } else {
            portal_error_message('Das Spiel wurde nicht hinzugef&uuml;gt, weil wahrscheinlich schon ein Spiel mit dem selben Namen existiert.', 'create.php');
        }
        break;

}