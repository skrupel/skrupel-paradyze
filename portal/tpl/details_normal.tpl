<center>
<table style='width: 95%;'>
  <tr>
    <td colspan='2'><p><h3>Allgemein</h3></p></td>
  </tr>
  <tr>
    <td width='40%'>Spielname:</td>
    <td width='60%'>" + games[0].getElementsByTagName("name")[0].firstChild.nodeValue + "</td>
  </tr>
  <tr>
    <td>Spielziel:</td>
    <td>" + goals[games[0].getElementsByTagName("goal")[0].firstChild.nodeValue] + " [" + fixdetails( games[0].getElementsByTagName("goal")[0].firstChild.nodeValue, games[0] ) + "]</td>
  </tr>
  <tr>
    <td>Spieler scheidet aus:</td>
    <td>" + (
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
    <td>Module:</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2'><p><h3>Galaxie</h3></p></td>
  </tr>
  <tr>
    <td>Grö&szlig;e</td>
    <td>" + games[0].getElementsByTagName("umfang")[0].firstChild.nodeValue + "x" + games[0].getElementsByTagName("umfang")[0].firstChild.nodeValue + " Lichtjahre</td>    
  </tr>
  <tr> 
    <td colspan='2'><p><h3>Plasmast&uuml;rme</h3></p></td>
  </tr>
  <tr>   
    <td>Maximal gleichzeitig</td> 
    <td>" + games[0].getElementsByTagName("max")[0].firstChild.nodeValue + "</td>
  </tr>
  <tr>
    <td>Wahrscheinlichkeit des Auftretens</td>
    <td>" + games[0].getElementsByTagName("wahr")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Anzahl Runden</td>
    <td>" + games[0].getElementsByTagName("lang")[0].firstChild.nodeValue + "</td>
  </tr>
  <tr>
    <td colspan='2'><p><h3>Piraten</h3></p></td>
  </tr>
  <tr> 
    <td>Wahrscheinlichkeit im Zentrum</td>
    <td>" + games[0].getElementsByTagName("pirates_center")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Wahrscheinlichkeit am Rand</td>
    <td>" + games[0].getElementsByTagName("pirates_outer")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Minimale Beute der Piraten</td>
    <td>" + games[0].getElementsByTagName("pirates_min")[0].firstChild.nodeValue + "%</td>
  </tr>
  <tr>
    <td>Maximale Beute der Piraten</td>
    <td>" + games[0].getElementsByTagName("pirates_max")[0].firstChild.nodeValue + "%</td>
  </tr>
</table>
</center>