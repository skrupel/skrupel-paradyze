<center>
<table style='width: 95%;'>
  <tr>
    <td colspan='5'><p><h3>Allgemein</h3></p></td>
  </tr>
  <tr>
    <td>Spielname:</td>
    <td colspan='5'>" + games[0].getElementsByTagName("name")[0].firstChild.nodeValue + "</td>
  </tr>
  <tr>
    <td>Spielziel:</td>
    <td colspan='5'>" + goals[games[0].getElementsByTagName("goal")[0].firstChild.nodeValue] + " [" + fixdetails( games[0].getElementsByTagName("goal")[0].firstChild.nodeValue, games[0] ) + "]</td>
  </tr> 
  <tr>
    <td colspan='5'><p><h3>Spieler</h3></p></td>   
  </tr>
  <tr>
    <td colspan='5' align='center'>
		 <table cellpadding='3' cellspacing='3' width='100%'>
	    <tr>
	      <td align='center' width='35%'><b>Spieler</b></td>
	      <td align='center' width='35%'><b>Rasse</b></td>
	      <td align='center' width='30%'><b>Teilnahme</b></td>
	    </tr>
			" + (games[0].getElementsByTagName("user_1")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_1")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_1")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_1")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
			" + (games[0].getElementsByTagName("user_2")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_2")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_2")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_2")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
			" + (games[0].getElementsByTagName("user_3")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_3")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_3")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_3")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
			" + (games[0].getElementsByTagName("user_4")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_4")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_4")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_4")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
			" + (games[0].getElementsByTagName("user_5")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_5")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_5")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_5")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
			" + (games[0].getElementsByTagName("user_6")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_6")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_6")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_6")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
			" + (games[0].getElementsByTagName("user_7")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_7")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_7")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_7")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
			" + (games[0].getElementsByTagName("user_8")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_8")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_8")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_8")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
			" + (games[0].getElementsByTagName("user_9")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_9")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_9")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_9")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
			" + (games[0].getElementsByTagName("user_10")[0].firstChild.nodeValue == 0 ? "" : "
	    <tr>
	      <td align='center'>" + games[0].getElementsByTagName("user_10")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + games[0].getElementsByTagName("race_10")[0].firstChild.nodeValue + "</td>
	      <td align='center'>" + (games[0].getElementsByTagName("user_10")[0].firstChild.nodeValue == "[ Freier Slot ]" ? "<a href='waiting.php?action=join&game_id=" + games[0].getElementsByTagName("id")[0].firstChild.nodeValue + "'>Mitspielen</a>" : "") + "</td>
	    </tr>") + "
	   </table>
		</td>   
  </tr>
  <tr>
    <td colspan='2'><p><h3>Starteinstellungen</h3></p></td>
    <td width='2%'>&nbsp;</td>
    <td colspan='2'><p><h3>Allgemein</h3></p></td>    
  </tr>
  <tr>
    <td width='20%'>Startposition</td>
    <td width='29%'>" + (
             games[0].getElementsByTagName("startposition")[0].firstChild.nodeValue == 1 
             ? "Vorgegeben" 
             : "Zuf&auml;llig"
            ) + "</td>
    <td width='2%'>&nbsp;</td>
    <td width='20%'>Spieler scheidet aus:</td>
    <td width='29%'>" + (
             games[0].getElementsByTagName("out")[0].firstChild.nodeValue == 3
             ? "Verlust des Heimatplaneten"
             : (
                games[0].getElementsByTagName("out")[0].firstChild.nodeValue == 2
                ? "Verlust aller Basen"
                : (
                   games[0].getElementsByTagName("out")[0].firstChild.nodeValue == 1
                   ? "Verlust aller Kolonien"
                   : "Verlust aller Kolonien<br>und kompletter Flotte"
                  )
               )
            ) + "</td>
  </tr>
  <tr>
    <td>Imperiumsgr&ouml;sse:</td>
    <td>" + (
             games[0].getElementsByTagName("imperiumgroesse")[0].firstChild.nodeValue == 4 
             ? "1 Planet" 
             : "1 Planet + Sternenbasis"
            ) + "</td>
    <td width='2%'>&nbsp;</td>
    <td>Module:</td>
    <td></td>
  </tr>
  <tr>
    <td colspan='2'><p><h3>Galaxie</h3></p></td>
    <td width='2%'>&nbsp;</td>
    <td colspan='2'><p><h3>Piraten</h3></p></td>            
  </tr>
  <tr>
    <td>Struktur der Galaxie</td>
    <td>" + games[0].getElementsByTagName("struktur")[0].firstChild.nodeValue + "</td>    
    <td width='2%'>&nbsp;</td>
    <td>Wahrscheinlichkeit im Zentrum</td>
    <td>" + games[0].getElementsByTagName("pirates_center")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Grösse</td>
    <td>" + games[0].getElementsByTagName("umfang")[0].firstChild.nodeValue + "x" + games[0].getElementsByTagName("umfang")[0].firstChild.nodeValue + " Lichtjahre</td>    
    <td width='2%'>&nbsp;</td>
    <td>Wahrscheinlichkeit am Rand</td>
    <td>" + games[0].getElementsByTagName("pirates_max")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Sternendichte</td>
    <td>max. " + games[0].getElementsByTagName("sternendichte")[0].firstChild.nodeValue + " Planeten</td>    
    <td width='2%'>&nbsp;</td>
    <td>Maximale Beute der Piraten</td>
    <td>" + games[0].getElementsByTagName("pirates_outer")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Bevölkerte Planeten</td>
    <td>" + games[0].getElementsByTagName("species")[0].firstChild.nodeValue + "%</td>    
    <td width='2%'>&nbsp;</td>
    <td>Minimale Beute der Piraten</td>
    <td>" + games[0].getElementsByTagName("pirates_min")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td colspan='2'><p><h3>Resourcen</h3></p></td>   
    <td width='2%'>&nbsp;</td>
    <td colspan='2'><p><h3>Natürliche Geschehnisse</h3></p></td>
  </tr>
  <tr>
    <td>Geldmittel</td>
    <td>" + games[0].getElementsByTagName("geldmittel")[0].firstChild.nodeValue + " Cantox</td>    
    <td width='2%'>&nbsp;</td>
    <td>Plasmast&uuml;rme - Wahrscheinlichkeit</td>
    <td>" + games[0].getElementsByTagName("wahr")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Leminvorkommen</td>
    <td>" + (
             games[0].getElementsByTagName("leminvorkommen")[0].firstChild.nodeValue == 3
             ? "stark erhöhte Vorkommen"
             : (
                games[0].getElementsByTagName("leminvorkommen")[0].firstChild.nodeValue == 2
                ? "erhöhte Vorkommen"
                : "normale Vorkommen"
               )
            ) + "</td>    
    <td width='2%'>&nbsp;</td>
    <td>Plasmast&uuml;rme - Anzahl der Runden</td>
    <td>" + games[0].getElementsByTagName("lang")[0].firstChild.nodeValue + "</td>
  </tr>
  <tr>
    <td>Mineralien auf Heimatplaneten</td>
    <td>" + (
             games[0].getElementsByTagName("mineralienhome")[0].firstChild.nodeValue == 1 
             ? "Extrem"
             : (
                games[0].getElementsByTagName("mineralienhome")[0].firstChild.nodeValue == 2 
                ? "Hoch"
                : (
                   games[0].getElementsByTagName("mineralienhome")[0].firstChild.nodeValue == 3
                   ? "Mittel"
                   : "Gering"
                  )
               )
            ) + "</td>    
    <td width='2%'>&nbsp;</td>
    <td>Plasmast&uuml;rme - Maximal gleichzeitig</td> 
    <td>" + games[0].getElementsByTagName("max")[0].firstChild.nodeValue + "</td>
  </tr>
  <tr>
    <td>Mineralienvorkommen</td>
    <td>" + (
             games[0].getElementsByTagName("mineralien")[0].firstChild.nodeValue == 1 
             ? "Extrem"
             : (
                games[0].getElementsByTagName("mineralien")[0].firstChild.nodeValue == 2 
                ? "Hoch"
                : (
                   games[0].getElementsByTagName("mineralien")[0].firstChild.nodeValue == 3
                   ? "Mittel"
                   : "Gering"
                  )
               )
            ) + "</td>
    <td width='2%'>&nbsp;</td>
    <td>Anzahl stabiler Wurml&ouml;cher</td>
    <td>" + (
              games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 0
              ? "keins" : (
               games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 1
               ? "1 zufällig" : (
                games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 2
                ? "2 zufällig" : (
                 games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 3
                 ? "3 zufällig" : (
                  games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 4
                  ? "4 zufällig" : (
                   games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 5
                   ? "5 zufällig" : (
                    games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 6
                    ? "1 verbindet N und S" : (
                     games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 7
                     ? "2 verbinden N und S" : (
                      games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 8
                      ? "1 verbindet W und O" : (
                       games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 9
                       ? "2 verbinden W und O" : (
                        games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 10
                        ? "1 verbindet N und S und 1 verbindet W und O" : (
                         games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 11
                         ? "1 verbindet NW und SO" : (
                          games[0].getElementsByTagName("stabil")[0].firstChild.nodeValue == 12
                          ? "1 verbindet NO und SW"
                          : "2 verbinden Quadranten<br> über kreuz"
                         )
                        )
                       )
                      )
                     )
                    )
                   )
                  )
                 )
                )
               )                 
              )
            ) + "</td>
  </tr>
  <tr>
    <td colspan='2'>&nbsp;</td>
    <td width='2%'>&nbsp;</td>
    <td>Anzahl instabiler Wurml&ouml;cher</td>
    <td>" + games[0].getElementsByTagName("instabil")[0].firstChild.nodeValue + "</td>
  </tr>
</table>
</center>