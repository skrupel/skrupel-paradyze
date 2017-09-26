<?php
$tips[] = 'Sucht ihr Mitspieler f&uuml;r euer Spiel? Dann besucht doch mal den <a href="http://forum.iceflame.net/" target="_blank">Spielertreff</a>.';
$tips[] = 'Alles, was ihr &uuml;ber Paradyze wissen m&uuml;sst findet ihr in der <a href="http://wiki.iceflame.net/paradyze">Dokumentation</a>.';
$tips[] = 'Bitte meldet alle Bugs und Fehler, die ihr findet, aber auch alle Ideen, die euch einfallen im <a href="http://develop.iceflame.net/bugtracker">Bugtracker</a>.';
$tips[] = '<a href="http://wiki.iceflame.net/paradyze/gruendung-einer-kolonie" target="_blank">Hier</a> erfahrt ihr, wie man ein Spiel am besten beginnt, um schnell an die erste eigene Kolonie zu kommen. Ein <a rel="shadowbox;width=662;height=433" href="../bilder/movies/erstekolonie.swf">Video</a> gibt\'s auch!';
$tips[] = 'In <a rel="shadowbox;width=662;height=433" href="../bilder/movies/schiffsbau.swf">diesem Video</a> wird ausf&uuml;hrlich erkl&auml;rt, wie man richtig ein Schiff in Auftrag gibt.';
$tips[] = 'Speziell zu Beginn sind Kolonisten meist sehr rar. Befinden sich auf einem Nachbarplaneten humanoide Ureinwohner, so sind diese schnell vom Kollektiv assimiliert. 10000 Kolonisten in den ersten Runden zus&auml;tzlich k&ouml;nnen schon einen guten Vorteil verschaffen.';
$tips[] = 'Der Plasmakreuzer ist nicht nur gut bewaffnet sondern kann auch ein Transwarpkanalnetz aufbauen. Dadurch lassen sich nicht nur Routen von Frachtern verk&uuml;rzen, sondern auch die m&auml;chtigen Taktischen Kuben zu ihrem Ziel bef&ouml;rdern.';
$tips[] = 'Die Sonden sind als ~Tech1-Schiffe schnell gebaut und helfen zu Spielbeginn die Galaxie zu erkunden. Es ist genug Frachtraum f&uuml;r eine Kolonie vorhanden, wobei die Bewaffnung einen recht großen Vorteil verschafft.';
$tips[] = 'Von insgesamt 22 Schiffen der Romulaner sind 11 Schiffe in der Lage, sich zu tarnen.';
$tips[] = 'Die Kampfschiffe der Romulaner sind preisg&uuml;nstig und stark bewaffnet. Die Raubvogelklasse beispielsweise kann im Alleingang selbst gr&ouml;ßere feindliche Kolonien zerst&ouml;ren oder angreifen und mit Tarnung und 280 KT Ressourcen wieder entkommen.';
$tips[] = 'Mit Kuatoh ist es m&ouml;glich, alle gegnerischen Schiffe zu scannen und selbst zu bauen.';
$tips[] = 'Die reifen Sporen Kuatohs verhalten sich wie Frachter mit Sprungtriebwerken.';

$random_tip = mt_rand(0, count($tips)-1);
?>
        <div class="box" style="font-size: 11px;">
            <div style="font-family: starcraft; font-size: 15px; margin-bottom: 10px;">Tipp des Moments</div>
            <?php echo $tips[$random_tip];?>
        </div>