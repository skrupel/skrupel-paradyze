<?php
$gmst_spieler_id = $_SESSION["user_id"];
$gmst_zeiger = @mysql_query("SELECT * FROM skrupel_spiele where phase = 0 and (spieler_1=$gmst_spieler_id or spieler_2=$gmst_spieler_id or spieler_3=$gmst_spieler_id or spieler_4=$gmst_spieler_id or spieler_5=$gmst_spieler_id or spieler_6=$gmst_spieler_id or spieler_7=$gmst_spieler_id or spieler_8=$gmst_spieler_id or spieler_9=$gmst_spieler_id or spieler_10=$gmst_spieler_id)");
$gmst_num = @mysql_num_rows($gmst_zeiger);
if ($gmst_num > 0) {
    $gmst_msgs = array();
    while ($gmst_spiele = @mysql_fetch_array($gmst_zeiger)) {
      if ($gmst_spiele["spieler_1"]==$gmst_spieler_id) {$gmst_spieler=1;}
      if ($gmst_spiele["spieler_2"]==$gmst_spieler_id) {$gmst_spieler=2;}
      if ($gmst_spiele["spieler_3"]==$gmst_spieler_id) {$gmst_spieler=3;}
      if ($gmst_spiele["spieler_4"]==$gmst_spieler_id) {$gmst_spieler=4;}
      if ($gmst_spiele["spieler_5"]==$gmst_spieler_id) {$gmst_spieler=5;}
      if ($gmst_spiele["spieler_6"]==$gmst_spieler_id) {$gmst_spieler=6;}
      if ($gmst_spiele["spieler_7"]==$gmst_spieler_id) {$gmst_spieler=7;}
      if ($gmst_spiele["spieler_8"]==$gmst_spieler_id) {$gmst_spieler=8;}
      if ($gmst_spiele["spieler_9"]==$gmst_spieler_id) {$gmst_spieler=9;}
      if ($gmst_spiele["spieler_10"]==$gmst_spieler_id) {$gmst_spieler=10;}
      $gmst_spiel = $gmst_spiele["name"];
      $gmst_fehlt = "";
      $gmst_fehlt_anz = 0;
      $gmst_i = 1;
      while ($gmst_i <= 10) {
        if ($gmst_i == $gmst_spieler) {}
        elseif ($gmst_spiele["spieler_".$gmst_i."_zug"]==0 && $gmst_spiele["spieler_".$gmst_i]!=0) {
          $gmst_zeiger3 = @mysql_query("SELECT * FROM skrupel_user where id=".$gmst_spiele["spieler_".$gmst_i]);
          $gmst_fehlt_user = @mysql_fetch_array($gmst_zeiger3);
          $gmst_fehlt_anz ++;
          $gmst_fehlt .= $gmst_fehlt_user["nick"].", ";
        }
        $gmst_i ++;
      }
      if($gmst_fehlt!="") {
        $gmst_fehlt = substr($gmst_fehlt,0,-2);
      }
      $gmst_autotick = $gmst_spiele["lasttick"] + ($gmst_spiele["autozug"] * 3600);
      $gmst_autotick_tag = strftime("%d.%m.%Y", ($gmst_spiele["lasttick"] > 0 ? $gmst_spiele["lasttick"]+($gmst_spiele["autozug"]*60*60) : $gmst_spiele["start"]+($gmst_spiele["autozug"]*60*60)));
      $gmst_autotick_zeit = strftime("%T", ($gmst_spiele["lasttick"] > 0 ? $gmst_spiele["lasttick"]+($gmst_spiele["autozug"]*60*60) : $gmst_spiele["start"]+($gmst_spiele["autozug"]*60*60)));
      if ($gmst_spiele["spieler_".$gmst_spieler."_zug"]==0) {
          $gmst_msgs[] = '<li style="color: yellow; margin-bottom: 5px;">Im Spiel <b>' . $gmst_spiel . '</b> hast du deinen Zug noch nicht abgeschlossen.' . ($gmst_spiele["autozug"] != 0 ? ($gmst_autotick >= time() ? ' Du hast noch Zeit bis zum Autotick am ' . $gmst_autotick_tag . ' um ' . $gmst_autotick_zeit . ' Uhr.' : ' Leider liegt die Autotick-Zeit bereits seit dem ' . $gmst_autotick_tag . ' um ' . $gmst_autotick_zeit . ' in der Vergangenheit, beim Login beginnt also bereits die neue Runde.') : '') . '</li>';
      } elseif ($gmst_spiele["spieler_".$gmst_spieler."_zug"] == 1 && ($gmst_autotick >= time() || $gmst_spiele["autotick"] == 0)) {
          $gmst_msgs[] = '<li style="color: silver; margin-bottom: 5px;">Im Spiel <b>' . $gmst_spiel . '</b> hast du deinen Zug bereits abgeschlossen.</li>';
      } else {
          $gmst_msgs[] = '<li style="color: lightblue; margin-bottom: 5px;">Im Spiel <b>' . $gmst_spiel . '</b> kannst du den Autotick ausl&ouml;sen. Der Autotick ist seit dem ' . $gmst_autotick_tag . ' um ' . $gmst_autotick_zeit . ' Uhr bereit zum Ausl&ouml;sen.';
      }
    }
}
?>
        <div class="box" style="font-size: 11px;">
<?php if ($gmst_num > 0): ?>
            <div style="font-family: starcraft; font-size: 15px; margin-bottom: 10px;">Zugstatus (<?php echo $gmst_num; ?>)</div>
            <ul style="padding: 0 0 0 15px; margin: 0;">
<?php echo implode("\n", $gmst_msgs); ?>
            </ul>
<?php else: ?>
            <div style="font-family: starcraft; font-size: 15px; margin-bottom: 10px;">Zugstatus</div>
            <div style="text-align: center; color: silver;">Du nimmst aktuell an keinem Spiel teil.</div>
<?php endif; ?>
        </div>