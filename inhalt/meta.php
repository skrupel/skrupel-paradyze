<?
if ($_GET["fu"]==1) {
include ("../inc.conf.php");
include ("../lang/".$language."/lang.meta.php");
include ("inc.header.php");
?><body text="#000000" bgcolor="#444444" style="background-image:url('<?=$bildpfad?>/aufbau/14.gif'); background-attachment:fixed;" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<script language=JavaScript>
function metalink(url) {

        if (parent.mittelinksoben.document.globals.map.value==1) {
             parent.mittelinksoben.document.globals.map.value=0;
             parent.mittemitte.window.location='aufbau.php?fu=100&query='+url;
        }  else  {
             parent.mittemitte.rahmen12.window.location=url;
        }

}
</script>
<center><table border="0" height="100%" cellspacing="0" cellpadding="0">
	<tr>
  <td><center><a href="javascript:;" onclick="metalink('meta_fordner.php?fu=1&uid=<?=$uid?>&sid=<?=$sid?>');self.focus();"><img src="<?=$bildpfad?>/menu/flotte.gif" width="75" height="75" border="0"><br><nobr><?=$lang['meta']['fordner']?></nobr></a></center></td>
<? if (($spieleranzahl>=2) and ($spieler_raus==0)) { ?>
  <td><center><a href="javascript:;" onclick="metalink('meta_aufgabe.php?fu=1&uid=<?=$uid?>&sid=<?=$sid?>');self.focus();"><img src="<?=$bildpfad?>/menu/aufgabe.gif" width="75" height="75" border="0"><br><nobr><?=$lang['meta']['aufgabe']?></nobr></a></center></td>
<? } ?>
  <td><center><a href="javascript:;" onclick="metalink('meta_simulation.php?fu=1&uid=<?=$uid?>&sid=<?=$sid?>');self.focus();"><img src="<?=$bildpfad?>/menu/simbb.gif" width="75" height="75" border="0"><br><nobr><?=$lang['meta']['sim3']?></nobr></a></center></td>
  <td><center><a href="javascript:;" onclick="metalink('meta_simulation.php?fu=2&uid=<?=$uid?>&sid=<?=$sid?>');self.focus();"><img src="<?=$bildpfad?>/menu/simss.gif" width="75" height="75" border="0"><br><nobr><?=$lang['meta']['sim1']?></nobr></a></center></td>
  <td><center><a href="javascript:;" onclick="metalink('meta_simulation.php?fu=3&uid=<?=$uid?>&sid=<?=$sid?>');self.focus();"><img src="<?=$bildpfad?>/menu/simsp.gif" width="75" height="75" border="0"><br><nobr><?=$lang['meta']['sim2']?></nobr></a></center></td>
    <? /* ?>
   <td><center><a href="javascript:;" onclick="metalink('wisdom.php?fu=8&uid=<?=$uid?>&sid=<?=$sid?>');self.focus();"><img src="<?=$bildpfad?>/menu/galaxiedruck.gif" width="75" height="75" border="0"><br><nobr>Galaxiedruck</nobr></a></center></td>
  <? */ ?>
<? if ($spiel_anleitung==0) { ?>  
  <td><center><a href="http://wiki.iceflame.net/dokumentation/projekt-spaze" target="_blank"><img src="<?=$bildpfad?>/menu/anleitung.gif" width="75" height="75" border="0"><br><nobr><?=$lang['meta']['anleitung']?></nobr></a></center></td>
<? } ?>

     </tr>
</table></center>
<?
include ("inc.footer.php");
}