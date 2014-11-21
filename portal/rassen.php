<?php
	include_once "include/global.php";
	function fixforutf($string) {
		$search = array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß");
		$replace = array("&auml;", "&ouml;", "&uuml;", "&Auml;", "&Ouml;", "&Uuml;", "&szlig;");
		$string = str_replace($search, $replace, $string);
		return $string;
	}

$bildpfad = $conf_root_path . "bilder";

include ("{$conf_root_path}inc.conf.php");
include ("{$conf_root_path}lang/".$language."/lang.meta_rassen.php");
include ("{$conf_root_path}lang/".$language."/lang.orbitale_systeme.php");

if ($_GET["fu"]==1 || !$_GET["fu"]) {

include_once "include/header.php";
?>
<script type="text/javascript" src="../inhalt/js/helpers/swfobject.js"></script>
<script type="text/javascript">
	function link(url) {
	}
	function processReqChange() {
		//only if req shows loadeidd
		if (req.readyState == 4) {
			//only if "OK"
			if (req.status == 200) {
				text = req.responseText;
				Shadowbox.open({
					content:    text,
					player:     "html",
					title:      "Rassendetails",
					height:     510,
					width:      1010,
					options:	{ animSequence: 'sync' }
				});
			} else {
				alert("There was a problem retrieving the XML data:\n" + req.StatusText);
			}
		}
	}
  function loadXMLDocument( url )
  {
    if (window.XMLHttpRequest)
    {
      //branch for native XMLHttpRequest
      req = new XMLHttpRequest();
      req.onreadystatechange = processReqChange;
      req.open("GET", url, true);
      req.send(null);
    }
    else if (window.ActiveXObject)
    {
      //branch for IE/Windows ActiveX version
      isIE = true;
      req = new ActiveXObject("Microsoft.XMLHTTP");
      if (req)
      {
        req.onreadystatechange = processReqChange;
        req.open("GET", url, true);
        req.send();
      }
    }
  }
</script>
<h1>Die Rassen</h1>
<center>
	<table border="0" cellspacing="5" cellpadding="17" style="width: 75%;">
		<tr>
<?php
$verzeichnis="{$conf_root_path}daten/";

$handle=opendir("$verzeichnis");

while ($file=readdir($handle)) {

   if ((substr($file,0,1)<>'.') and (substr($file,0,7)<>'bilder_') and (substr($file,strlen($file)-4,4)<>'.txt')) {
    if($file == "unknown" || $file == "CVS") { continue; }
     $a = $verzeichnis . $file . "/daten.txt";
     $name = file( $a );
	 $cnt++;
	 if($cnt == 5 || $cnt == 9 || $cnt == 13 || $cnt == 17) {
?>
		</tr>
		<tr>
<?php
   }
?>
			<td style="text-align: center;"><a href="javascript:void()" onclick="loadXMLDocument('rassen.php?fu=2&rasse=<?php echo $file; ?>');"><img src="<?php echo $conf_root_path; ?>daten/<?php echo $file; ?>/bilder_allgemein/menu.png" class="imgbox" alt="<?php echo $name[0]; ?>" title="<?php echo $name[0]; ?>"></a></td>
<?php

   }
}
closedir($handle);
?>
		</tr>
	</table>
</center>
<?
include ("{$conf_root_path}admin/inc.footer.php");
include_once "include/footer.php";

 }
if ($_GET["fu"]==2) {
//include ("{$conf_root_path}admin/inc.header.php");
$rasse=$_GET["rasse"];
$file=$conf_root_path.'daten/'.$rasse.'/schiffe.txt';
$fp = fopen("$file","r");
if ($fp) {
$zaehler=0;
while (!feof ($fp)) {
    $buffer = @fgets($fp, 4096);
    $schiff[$zaehler]=$buffer;
    $zaehler++;
}
@fclose($fp); }

$file=$conf_root_path.'daten/'.$rasse.'/daten.txt';
$fp = @fopen("$file","r");
if ($fp) {
$zaehler2=0;
while (!feof ($fp)) {
    $buffer = @fgets($fp, 4096);
    $daten[$zaehler2]=$buffer;
    $zaehler2++;
}
@fclose($fp); }

$copyright=$daten[1];
$attribute=explode(":",$daten[2]);
$attribute2=explode(":",$daten[4]);
$verbieten=(isset($daten[6]) && trim($daten[6]) != '' ? explode(":",trim($daten[6])) : false);
$erlauben=(isset($daten[7]) && trim($daten[7]) != '' ? explode(":",trim($daten[7])) : false);

$beschreibung="";
$file=$conf_root_path.'daten/'.$rasse.'/beschreibung_'.$language.'.txt';

if (!file_exists($file)) { $file=$conf_root_path.'daten/'.$rasse.'/beschreibung.txt'; }


$fp = @fopen("$file","r");
if ($fp) {
while (!feof ($fp)) {
    $buffer = @fgets($fp, 4096);
    $beschreibung=$beschreibung.$buffer;
}
@fclose($fp); }

$file=$conf_root_path.'daten/dom_spezien_art.txt';
$fp = @fopen("$file","r");
if ($fp) {
while (!feof ($fp)) {
    $buffer = @fgets($fp, 4096);
    $art_b=explode(":",$buffer);
    $art[$art_b[0]]=$art_b[1];
}
@fclose($fp); }

if ($attribute2[0]>=1) {
  $assrasse=$art[(int)$attribute2[1]];
	if ($attribute2[1]==0) {$assrasse="alle";}
} else {
  $assrasse="keine";
}
$assgrad=$attribute2[0];
?>
<center>
	<table style="width: 95%;">
		<tr>
			<td style="vertical-align:top;width:100%;">
				<table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
					<tr>
						<td style="color:#aaaaaa;vertical-align:top;width:100%;"><h1><?php echo $daten[0]; ?></h1></td>
					</tr>
					<tr>
						<td style="color:#aaaaaa;text-align:justify;vertical-align:top;width:100%;">
							<p style="color:#aaaaaa;line-height:1.5em;"><img src="<?php echo $conf_root_path; ?>daten/<?php echo $rasse; ?>/bilder_allgemein/<?php echo (@file_exists("{$conf_root_path}daten/{$rasse}/bilder_allgemein/topic.png") ? "topic.png" : "topic.gif") ?>" style="margin: 0 0 6px 15px; float: right; border: 0;"><?php echo fixforutf($beschreibung); ?></p>
						</td>
					</tr>
<?php if (strlen($copyright) > 0): ?>
					<tr>
						<td style="text-align: center; padding: 15px 0;"><i><?php echo $copyright; ?></i></td>
					</tr>
<?php endif; ?>
					<tr>
						<td style="width:85%;">
<?php
	if ($attribute[6] >= 1):
		switch($attribute[6]) {
			case 1: $klassename = "M"; break;
			case 2: $klassename = "N"; break;
			case 3: $klassename = "J"; break;
			case 4: $klassename = "L"; break;
			case 5: $klassename = "G"; break;
			case 6: $klassename = "I"; break;
			case 7: $klassename = "C"; break;
			case 8: $klassename = "K"; break;
			case 9: $klassename = "F"; break;
		}
?>
							<center>
								<table border="0" cellspacing="0" cellpadding="3">
									<td style="color:#aaaaaa;"><?php echo $lang[metarassen][planetenklasse]; ?></td>
									<td><?php echo $klassename; ?></td>
									<td><img border="0" src="<?php echo $bildpfad?>/karte/planeten/<?php echo $attribute[6]; ; ?>.gif" width="10" height="10" /></td>
								</table>
								<br />
<? endif; ?>
								<table border="0" cellspacing="0" cellpadding="3" style="width:100%;">
									<tr>
										<td style="color:#aaaaaa;"><?php echo $lang[metarassen][temperatur]; ?></td>
										<td><? if ($attribute[0]>=1) { echo $attribute[0]-35; echo " Grad"; } else { echo "keine"; } ?></td>
										<td style="color:#aaaaaa;"><?php echo $lang[metarassen][minenproduktion]; ?></td>
										<td><? echo $attribute[2]*100; ?> %</td>
										<td style="color:#aaaaaa;"><?php echo $lang[metarassen][bodenkampfangriff]; ?></td>
										<td><? echo $attribute[3]*100; ?> %</td>
										<td style="color:#aaaaaa;"><?php echo $lang[metarassen][assimilation]; ?></td>
										<td><? echo fixforutf($assrasse); ?></td>
									</tr>
									<tr>
										<td style="color:#aaaaaa;"><?php echo $lang[metarassen][steuereinahmen]; ?></td>
										<td><? echo $attribute[1]*100; ?> %</td>
										<td style="color:#aaaaaa;"><?php echo $lang[metarassen][fabrikproduktion]; ?></td>
										<td><? echo $attribute[5]*100; ?> %</td>
										<td style="color:#aaaaaa;"><?php echo $lang[metarassen][bodenkampfverteidigung]; ?></td>
										<td><? echo $attribute[4]*100; ?> %</td>
										<td style="color:#aaaaaa;"><?php echo $lang[metarassen][aeffizienz]; ?></td>
										<td><? echo $assgrad; ?> %</td>
									</tr>
								</table>
							</center>
						</td>
					</tr>
                    <?php
                        $file='../daten/'.$rasse.'/theme.mp3';
                        if (@file_exists($file)) { ?>
                        <tr>
                            <td><center>
                                <div id="theme" style="padding-top:10px;"></div>
                                <script type="text/javascript">
                                    // <![CDATA[
                                    var so = new SWFObject("../inhalt/flash/mp3/mp3play.swf", "sotester", "20", "20", "9", "#000000");
                                    so.addVariable("filePath", "<?php echo $file; ?>");
                                    so.addParam("scale", "noscale");
                                    so.addParam("wmode", "transparent");
                                    so.write("theme");
                                    // ]]>
                                </script>
                            </center></td>
                        </tr>
                    <?php } ?>
				</table>
			</td>
			<td style="text-align: right; width: 15%;"><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="20" height="20" /></td>
			<td style="text-align: right;">
				<table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
					<tr>
						<td colspan="3">
							<br />
<?
    $file=$conf_root_path.'daten/'.$rasse.'/bilder_basen/1.jpg';
    if (@file_exists($file)):
?>
							<img src="<?php echo $file; ?>" border="0" width="150" height="100" />
<?
    else:
?>
							<img src="<?php echo $conf_root_path; ?>bilder/starbase_empty.jpg" border="0" width="150" height="100" />
<?
	endif;
?>
							<br /><br />
<?
    $file=$conf_root_path.'daten/'.$rasse.'/bilder_basen/2.jpg';
    if (@file_exists($file)):
?>
							<img src="<?php echo $file; ?>" border="0" width="150" height="100" />
<?
    else:
?>
							<img src="<?php echo $conf_root_path; ?>bilder/starbase_empty.jpg" border="0" width="150" height="100" />
<?
	endif;
?>
							<br /><br />
<?
    $file=$conf_root_path.'daten/'.$rasse.'/bilder_basen/3.jpg';
    if (@file_exists($file)):
?>
							<img src="<?php echo $file; ?>" border="0" width="150" height="100" />
<?
    else:
?>
							<img src="<?php echo $conf_root_path; ?>bilder/starbase_empty.jpg" border="0" width="150" height="100" />
<?
	endif;
?>
							<br /><br />
<?
    $file=$conf_root_path.'daten/'.$rasse.'/bilder_basen/4.jpg';
    if (@file_exists($file)):
?>
							<img src="<?php echo $file; ?>" border="0" width="150" height="100" />
<?
    else:
?>
							<img src="<?php echo $conf_root_path; ?>bilder/starbase_empty.jpg" border="0" width="150" height="100" />
<?
    endif;
?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br /><br />
    <?php if ($verbieten or $erlauben) { ?>
	<table border="0" cellspacing="0" cellpadding="0" style="width: 95%; min-height: 140px;">
		<tr>
			<td><h3>Orbitale Systeme</h3></td>
		</tr>
		<tr>
			<td>
                <center>
                    <table border="0" cellspacing="0" cellpadding="5" style="width: 100%; margin-top:25px;">
                        <?php if ($erlauben) { ?>
                        <td align="center" valign="top" width="50%"><?php echo $lang['metarassen']['orbs'][0]; ?><br /><br /><table border="0" cellspacing="0" cellpadding="5" style="width: 95%;">
                            <?php foreach ($erlauben as $item) { ?>
                            <tr>
                                <td valign="top" width="15%" align="center"><img src="../bilder/osysteme/<?php echo $item; ?>.gif" border="0" width="61" height="64" title="<?php echo $lang['orbitalesysteme']['name'][$item]; ?>"></td>
                                <td valign="middle" width="85%" style="color:#aaaaaa;"><b><?php echo $lang['orbitalesysteme']['name'][$item]; ?></b><br /><br /><?php echo $lang['orbitalesysteme']['kurz'][$item]; ?><?php if (0 < strlen($lang['orbitalesysteme']['lang'][$item])) { echo '<br /><br />' . $lang['orbitalesysteme']['lang'][$item]; } ?></td>
                            </tr>
                            <?php } ?>
                        </table></td>
                        <?php } ?>
                        <?php if ($verbieten) { ?>
                        <td align="center" valign="top" width="50%"><?php echo $lang['metarassen']['orbs'][1]; ?><br /><br /><table border="0" cellspacing="0" cellpadding="5" style="width: 95%;">
                            <?php foreach ($verbieten as $item) { ?>
                            <tr>
                                <td valign="top" width="15%" align="center"><img src="../bilder/osysteme/<?php echo $item; ?>.gif" border="0" width="61" height="64" title="<?php echo $lang['orbitalesysteme']['name'][$item]; ?>"></td>
                                <td valign="middle" width="85%" style="color:#aaaaaa;"><b><?php echo $lang['orbitalesysteme']['name'][$item]; ?></b><br /><br /><?php echo $lang['orbitalesysteme']['kurz'][$item]; ?><?php if (0 < strlen($lang['orbitalesysteme']['lang'][$item])) { echo '<br /><br />' . $lang['orbitalesysteme']['lang'][$item]; } ?></td>
                            </tr>
                            <?php } ?>
                        </table></td>
                        <?php } ?>
                    </table>
                </center>
			</td>
		</tr>
	</table>
	<br /><br />
    <?php } ?>
	<table border="0" cellspacing="0" cellpadding="0" style="width: 95%; min-height: 140px;">
		<tr>
			<td colspan="23"><h3>Schiffe</h3></td>
		</tr>
<?
for ($i=0;$i<$zaehler;$i++) {
   $schiffwert=explode(':',$schiff[$i]);
   $fertigkeiten=trim($schiffwert[17]);
   $spezial='';
  $cybern=@intval(substr($fertigkeiten,48,2));
  $subpartikel=@intval(substr($fertigkeiten,0,2));
  $terra_warm=@intval(substr($fertigkeiten,5,1));
  $terra_kalt=@intval(substr($fertigkeiten,6,1));
  $quark=@intval(substr($fertigkeiten,7,4));
  $sprungtriebwerk=@intval(substr($fertigkeiten,11,11));
  $tarnfeldgen=@intval(substr($fertigkeiten,22,1));
  $subraumver=@intval(substr($fertigkeiten,23,1));
  $scannerfert=@intval(substr($fertigkeiten,24,1));
  $sprungtorbau=@intval(substr($fertigkeiten,25,12));
  $fluchtmanoever=@intval(substr($fertigkeiten,38,2));
  $signaturmaske=@intval(substr($fertigkeiten,40,1));
  $viralmin=@intval(substr($fertigkeiten,41,2));
  $viralmax=@intval(substr($fertigkeiten,43,3));
  $erwtrans=@intval(substr($fertigkeiten,46,2));
  $cybern=@intval(substr($fertigkeiten,48,2));
  $destabil=@intval(substr($fertigkeiten,50,2));
  $overdrive_min=@intval(substr($fertigkeiten,53,1));
  $overdrive_max=@intval(substr($fertigkeiten,54,1));
  $luckyshot=@intval(substr($fertigkeiten,55,1));
  $orbitalschild=@intval(substr($fertigkeiten,56,1));
  $infanterie=@intval(substr($fertigkeiten,57,1));
  $hmatrix=@intval(substr($fertigkeiten,58,1));
  $fuehrung=@intval(substr($fertigkeiten,59,1));
  $fert_reperatur=@intval(substr($fertigkeiten,37,1));
  $strukturtaster=@intval(substr($fertigkeiten,52,1));
  $fert_quark_vorrat=@intval(substr($fertigkeiten,7,1))*113;
  $fert_quark_min1=@intval(substr($fertigkeiten,8,1))*113;
  $fert_quark_min2=@intval(substr($fertigkeiten,9,1))*113;
  $fert_quark_min3=@intval(substr($fertigkeiten,10,1))*113;
  $fert_sub_vorrat=@intval(substr($fertigkeiten,0,2));
  $fert_sub_min1=@intval(substr($fertigkeiten,2,1));
  $fert_sub_min2=@intval(substr($fertigkeiten,3,1));
  $fert_sub_min3=@intval(substr($fertigkeiten,4,1));
  $fert_sprungtorbau_min1=@intval(substr($fertigkeiten,25,3));
  $fert_sprungtorbau_min2=@intval(substr($fertigkeiten,28,3));
  $fert_sprungtorbau_min3=@intval(substr($fertigkeiten,31,3));
  $fert_sprungtorbau_lemin=@intval(substr($fertigkeiten,34,3));
  $fert_sprung_kosten=@intval(substr($fertigkeiten,11,3));
  $fert_sprung_min=@intval(substr($fertigkeiten,14,4));
  $fert_sprung_max=@intval(substr($fertigkeiten,18,4));
  if ($cybern>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $wert=$cybern*220;
    $textneu=str_replace(array('{1}'),array($wert),$lang[metarassen][cybernrittnikk]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($destabil>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}'),array($destabil),$lang[metarassen][destabilisator]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($erwtrans>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}'),array($erwtrans),$lang[metarassen][erweitertertransporter]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($hmatrix==1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][hmatrix] . '</span>';
  }
  if ($infanterie==1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][infanterie] . '</span>';
  }
  if ($fuehrung==1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][fuehrung] . '</span>';
  }
  if ($fluchtmanoever>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    if ($fluchtmanoever==1) {
      $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][lloydsfluchtmanoever][0] . '</span>';
    } else {
      $textneu=str_replace(array('{1}'),array($fluchtmanoever),$lang[metarassen][lloydsfluchtmanoever][1]);
      $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
    }
  }
  if ($luckyshot>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}'),array($luckyshot),$lang[metarassen][luckyshot]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($orbitalschild==1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][orbitalschild] . '</span>';
  }
  if (($overdrive_min>=1) or ($overdrive_max>=1)) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $wert1=$overdrive_min*10;
    $wert2=$overdrive_max*10;
    $textneu=str_replace(array('{1}','{2}'),array($wert1,$wert2),$lang[metarassen][overdrive]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($quark>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}','{2}','{3}','{4}'),array($fert_quark_vorrat,$fert_quark_min1,$fert_quark_min2,$fert_quark_min3),$lang[metarassen][quarkreorganisator]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($fert_reperatur>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}'),array($fert_reperatur),$lang[metarassen][reperaturunterstuetzung]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($scannerfert>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    if ($scannerfert==1) {
      $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][scanner][0] . '</span>';
    } else {
      $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][scanner][1] . '</span>';
    }
  }
  if ($signaturmaske==1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][signaturmaskierung] . '</span>';
  }
  if ($sprungtorbau>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}','{2}','{3}','{4}'),array($fert_sprungtorbau_min1,$fert_sprungtorbau_min2,$fert_sprungtorbau_min3,$fert_sprungtorbau_lemin),$lang[metarassen][sprungtorbau]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($sprungtriebwerk>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}','{2}','{3}'),array($fert_sprung_kosten,$fert_sprung_min,$fert_sprung_max),$lang[metarassen][sprungtriebwerk]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($strukturtaster==1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][strukturtaster] . '</span>';
  }
  if ($subpartikel>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}','{2}','{3}','{4}'),array($fert_sub_vorrat,$fert_sub_min1,$fert_sub_min2,$fert_sub_min3),$lang[metarassen][subpartikelcluster]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
  if ($subraumver>=1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}'),array($subraumver),$lang[metarassen][subraumverzerrer]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
   if ($tarnfeldgen==1 or $tarnfeldgen==2 or $tarnfeldgen==3) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen]['tarnfeldgenerator' . $tarnfeldgen] . '</span>';
  }
   if ($terra_warm==1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][terraformer][0] . '</span>';
  }
   if ($terra_kalt==1) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $spezial .= '<span style="font-size: 0.8em;">' . $lang[metarassen][terraformer][1] . '</span>';
  }
  if (($viralmin>=1) or ($viralmax>=1)) {
    if (strlen($spezial)>=1) { $spezial.='<br /><br />'; }
    $textneu=str_replace(array('{1}','{2}'),array($viralmin,$viralmax),$lang[metarassen][viralerangriff]);
    $spezial .= '<span style="font-size: 0.8em;">' . $textneu . '</span>';
  }
?>
		<tr>
			<td colspan="20"><table border="0" cellspacing="0" cellpadding="0"><tr><td style="font-weight:bold;"><?php echo fixforutf($schiffwert[0]); ?></td><td style="color:#aaaaaa;" valign="bottom">&nbsp;(Techlevel <?php echo $schiffwert[2]; ?>)</td></tr></table></td>
		</tr>
		<tr>
			<td colspan="20"><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="1" height="10" /></td>
		</tr>
		<tr style="height: 30px;">
			<td rowspan="4" bgcolor="#000000"><img src="<?php echo $conf_root_path?>daten/<?php echo $rasse; ?>/bilder_schiffe/<?php echo $schiffwert[3]; ?>" width="150" height="100" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/crew.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][crew]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><?php echo $schiffwert[15]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/antrieb.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][antriebe]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><?php echo $schiffwert[14]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/cantox.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][cantox]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><?php echo $schiffwert[5]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td rowspan="4" valign="top" style="color:#aaaaaa;"><div style="overflow: auto; width: 100%; height: 110px;"><?php echo fixforutf($spezial); ?></div></td>
		</tr>
		<tr style="height: 30px;">
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/masse.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][masse]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><?php echo $schiffwert[16]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/laser.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][energetik]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><?php echo $schiffwert[9]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/mineral_1.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][baxterium]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><?php echo $schiffwert[6]; ?> KT</td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
		</tr>
		<tr style="height: 30px;">
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/lemin.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][tank]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><nobr><?php echo $schiffwert[13]; ?> KT</nobr></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/projektil.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][projektile]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><?php echo $schiffwert[10]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/mineral_2.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][rennurbin]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><?php echo $schiffwert[7]; ?> KT</td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
		</tr>
		<tr style="height: 30px;">
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/vorrat.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][fracht]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><nobr><?php echo $schiffwert[12]; ?> KT</nobr></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/hangar.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;"><?php echo $lang[metarassen][hangar]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><?php echo $schiffwert[11]; ?></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
			<td><img src="<?php echo $bildpfad; ?>/icons/mineral_3.gif" border="0" width="17" height="17" /></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td style="color:#aaaaaa;">Vomisaan</td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="5" height="1" /></td>
			<td><nobr><?php echo $schiffwert[8]; ?> KT</nobr></td>
			<td><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="10" height="1" /></td>
		</tr>
		<tr>
			<td colspan="20"><img src="<?php echo $conf_root_path; ?>bilder/empty.gif" border="0" width="1" height="20" /></td>
		</tr>
<?
}
?>
	</table>
</center>
<?
}
?>
