<?php
  include_once "include/global.php";

  include_once "include/header.php";
?>
<h1>Statistiken</h1>
<table style="text-align: center;" cellpadding="5" cellspacing="2" width="100%">
  <tr>
    <th class="headcell" width="16%">Spielername</th>
    <th class="headcell" width="12%">Spiele gesamt</th>
    <th class="headcell" width="12%">Spiele gewonnen</th>
    <th class="headcell" width="12%">Schlachten gesamt</th>
    <th class="headcell" width="12%">Schlachten gewonnen</th>
    <th class="headcell" width="12%">Kolonien erobert</th>
    <th class="headcell" width="12%">Lichtjahre geflogen</th>
    <th class="headcell" width="12%">Gespielte Runden</th>
  </tr>
<?php
  $sql = "SELECT nick,
                 stat_teilnahme,
                 stat_sieg,
                 stat_schlacht,
                 stat_schlacht_sieg,
                 stat_kol_erobert,
                 stat_lichtjahre,
                 stat_monate
          FROM skrupel_user WHERE nick NOT LIKE 'KI %'
          ORDER BY stat_monate DESC, stat_teilnahme DESC LIMIT 20";
  $query = mysql_query( $sql );
  while ( $data = mysql_fetch_assoc( $query ) )
  {
?>
  <tr>
    <td class="cell"><a href="http://www.paradyze.org/userfeed.php?name=<?=$data["nick"]?>" rel="shadowbox;options={animSequence:'sync'}" title="Feed von <?=$data["nick"]?>"><?=$data["nick"]?></a></td>
    <td class="cell" style="text-align: center;"><?=$data["stat_teilnahme"]?></td>
    <td class="cell" style="text-align: center;"><?=$data["stat_sieg"]?></td>
    <td class="cell" style="text-align: center;"><?=$data["stat_schlacht"]?></td>
    <td class="cell" style="text-align: center;"><?=$data["stat_schlacht_sieg"]?></td>
    <td class="cell" style="text-align: center;"><?=$data["stat_kol_erobert"]?></td>
    <td class="cell" style="text-align: center;"><?=$data["stat_lichtjahre"]?></td>
    <td class="cell" style="text-align: center;"><?=$data["stat_monate"]?></td>
  </tr>
<?php
  }
?>
</table>
<?php
  include_once "include/footer.php";
?>
