<?php
if ($_GET["fu"]>=0) {
    include ("../inc.conf.php");
    include ("inc.header.php");
    if(!$_GET["sprache"]){$_GET["sprache"]=$language;}
    $file="../lang/".$_GET["sprache"]."/lang.hilfe.php";
    include ($file);
    
    function tlquad($tl){
        return $tl*$tl*100;
    }
    
    ?>
    <body text="#ffffff" bgcolor="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
		<p style="font-size: 11px;"><b><?php echo $lang['hilfe']['ueberschrift'][$_GET["fu2"]]; ?></b></p>
        <p><?php echo $lang['hilfe']['text'][$_GET["fu2"]]; ?></p>
	</body>
<?php
	if ($_GET["fu"]==1) {
		echo $lang['hilfe'][$von]['ueberschrift']
		?>
		<table border="0" cellspacing="0" cellpadding="2" width="100%">
			<?php
			$cantox = array(100,200,300,800,1000,1200,2500,5000,7500,10000);
			for($i=1;$i<6;$i++){
				?>
				<tr>
					<td><?php echo str_replace('{1}',$i, $lang['hilfe']['stufe']);?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',$cantox[$i-1], $lang['hilfe']['cx'])?></td>
					<td><?php echo str_replace('{1}',$i+5, $lang['hilfe']['stufe'])?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',$cantox[$i+4], $lang['hilfe']['cx'])?></td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}elseif ($_GET["fu"]==2) { 
		?>
		<table border="0" cellspacing="0" cellpadding="2" width="100%">
			<?php
			$cantox = array(100,200,300,400,500,600,700,4000,7000,10000);
			for($i=1;$i<6;$i++){
				?>
				<tr>
					<td><?php echo str_replace('{1}',$i, $lang['hilfe']['stufe']);?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',$cantox[$i-1], $lang['hilfe']['cx'])?></td>
					<td><?php echo str_replace('{1}',$i+5, $lang['hilfe']['stufe'])?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',$cantox[$i+4], $lang['hilfe']['cx'])?></td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}elseif ($_GET["fu"]==3) {
		?>
		<table border="0" cellspacing="0" cellpadding="2" width="100%">
			<?php
			for($i=1;$i<6;$i++){
				?>
				<tr>
					<td><?php echo str_replace('{1}',$i, $lang['hilfe']['stufe']);?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',tlquad($i), $lang['hilfe']['cx'])?></td>
					<td><?php echo str_replace('{1}',$i+5, $lang['hilfe']['stufe'])?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',tlquad($i+5), $lang['hilfe']['cx'])?></td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}elseif ($_GET["fu"]==4) {
		?>
		<table border="0" cellspacing="0" cellpadding="2" width="100%">
			<?php
			for($i=1;$i<6;$i++){
				?>
				<tr>
					<td><?php echo str_replace('{1}',$i, $lang['hilfe']['stufe']);?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',tlquad($i), $lang['hilfe']['cx'])?></td>
					<td><?php echo str_replace('{1}',$i+5, $lang['hilfe']['stufe'])?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',tlquad($i+5), $lang['hilfe']['cx'])?></td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}elseif ($_GET["fu"]==5) {
		?>
		<center>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><?php echo $lang['hilfe'][5][0]?></td>
				</tr>
			</table>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><img src="<?php echo $bildpfad; ?>/icons/cantox.gif" border="0" width="17" height="17"></td>
					<td>10</td>
				</tr>
				<tr>
					<td><img src="<?php echo $bildpfad; ?>/icons/vorrat.gif" border="0" width="17" height="17"></td>
					<td>1</td>
				</tr>
			</table>
		</center>
		<?php echo $lang['hilfe'][5][1];
	}elseif ($_GET["fu"]==6) {
		?>
		<center>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><?php echo $lang['hilfe'][6][0]?></td>
				</tr>
			</table>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><img src="<?php echo $bildpfad; ?>/icons/cantox.gif" border="0" width="17" height="17"></td>
					<td>3</td>
				</tr>
				<tr>
					<td><img src="<?php echo $bildpfad; ?>/icons/vorrat.gif" border="0" width="17" height="17"></td>
					<td>1</td>
				</tr>
			</table>
		</center>
		<?php echo $lang['hilfe'][6][1];
	//}elseif ($_GET["fu"]==7) { da unn�tig, nur kopf
	}elseif ($_GET["fu"]==8) {
		?>
		<center>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><?php echo $lang['hilfe'][8][0]?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',1,$lang['hilfe'][8][5])?></td>
				</tr>
				<tr>
					<td><?php echo $lang['hilfe'][8][1]?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',2,$lang['hilfe'][8][6])?></td>
				</tr>
				<tr>
					<td><?php echo $lang['hilfe'][8][2]?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',4,$lang['hilfe'][8][6])?></td>
				</tr>
				<tr>
					<td><?php echo $lang['hilfe'][8][3]?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',6,$lang['hilfe'][8][6])?></td>
				</tr>
				<tr>
					<td><?php echo $lang['hilfe'][8][4]?></td>
					<td style="color:#aaaaaa;"><?php echo str_replace('{1}',10,$lang['hilfe'][8][6])?></td>
				</tr>
			</table>
		</center>
		<?php echo $lang['hilfe'][8][7];?>
		<center>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><?php echo $lang['hilfe'][8][8];?></td>
				</tr>
			</table>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><img src="../bilder/icons/cantox.gif" border="0" width="17" height="17"></td>
					<td>4</td>
				</tr>
				<tr>
					<td><img src="../bilder/icons/vorrat.gif" border="0" width="17" height="17"></td>
					<td>1</td>
				</tr>
			</table>
		</center>
		<?php 
		echo $lang['hilfe'][8][9];
	//}elseif ($_GET["fu"]==9) { da unn�tig, nur kopf
	}elseif ($_GET["fu"]==10) {
		?>
		<center>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><?php echo $lang['hilfe'][10][0];?></td>
				</tr>
			</table>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><img src="<?php echo $bildpfad; ?>/icons/cantox.gif" border="0" width="17" height="17"></td>
					<td>10</td>
				</tr>
				<tr>
					<td><img src="<?php echo $bildpfad; ?>/icons/mineral_2.gif" border="0" width="17" height="17"></td>
					<td>1</td>
				</tr>
			</table>
		</center>
		<?php
	//}elseif ($_GET["fu"]==11) { da unn�tig, nur kopf
	}elseif ($_GET["fu"]==12) {
		?>
		<center>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><b><?php echo $lang['hilfe'][12][0];?></b></td>
				</tr>
			</table>
		</center>
		<center>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][7];?></b></td>
					<td>&nbsp;</td>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][8];?></b></td>
				</tr>
			</table>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][2];?></b></td>
					<td>&nbsp;&nbsp;</td>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][3];?>&nbsp;</b></td>
					<td></td>
					<td style="color:#aaaaaa;"><b>&nbsp;<?php echo $lang['hilfe'][12][2];?></b></td>
					<td>&nbsp;&nbsp;</td>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][3];?></b></td>
				</tr>
				<?php
				$schaden=array(array(3,1,2,1),array(7,2,4,3),array(10,2,5,3),array(15,4,8,5),array(12,16,6,20),array(29,7,15,9),array(35,8,18,10),array(37,9,19,11),array(18,33,9,41),array(45,11,23,14));                                
				for($i=0;$i<10;$i++){
					?>
					<tr>
						<td><center><?php echo $schaden[$i][0];?></center></td>
						<td></td>
						<td><center><?php echo $schaden[$i][1];?></center></td>
						<td style="color:#aaaaaa;"><center><?php echo $lang['hilfe'][12][5][$i];?></center></td>
						<td><center><?php echo $schaden[$i][2];?></center></td>
						<td></td>
						<td><center><?php echo $schaden[$i][3];?></center></td>
					</tr>
					<?php
				}
				?>
			</table>
		</center>
		<center><?php echo $lang['hilfe'][12][4];?></center>
		<center>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><b><?php echo $lang['hilfe'][12][1];?></b></td>
				</tr>
			</table>
		</center>
		<center>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][7];?></b></td>
					<td>&nbsp;</td>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][8];?></b></td>
				</tr>
			</table>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][2];?></b></td>
					<td>&nbsp;&nbsp;</td>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][3];?>&nbsp;</b></td>
					<td></td>
					<td style="color:#aaaaaa;"><b>&nbsp;<?php echo $lang['hilfe'][12][2];?></b></td>
					<td>&nbsp;&nbsp;</td>
					<td style="color:#aaaaaa;"><b><?php echo $lang['hilfe'][12][3];?></b></td>
				</tr>
				<?php
				$schaden=array(array(5,1,3,1),array(8,2,4,3),array(10,2,5,3),array(6,13,3,16),array(15,6,8,8),array(30,7,15,9),array(35,8,18,10),array(12,36,6,45),array(48,12,24,15),array(55,14,28,18));                                
				for($i=0;$i<10;$i++){
					?>
					<tr>
						<td><center><?php echo $schaden[$i][0];?></center></td>
						<td></td>
						<td><center><?php echo $schaden[$i][1];?></center></td>
						<td style="color:#aaaaaa;"><center><?php echo $lang['hilfe'][12][6][$i];?></center></td>
						<td><center><?php echo $schaden[$i][2];?></center></td>
						<td></td>
						<td><center><?php echo $schaden[$i][3];?></center></td>
					</tr>
					<?php
				}
				?>
			</table>
		</center>
		<center><?php echo $lang['hilfe'][12][9];?></center>
		<?php
	//}elseif ($_GET["fu"]==13 bis 15 ) { unn�tig, nur kopf
	}elseif ($_GET["fu"]==16) {
		?>
		<ul>
			<?php
			for($i=0;$i<6;$i++){
				?>
				<li><b><?php echo $lang['hilfe'][16][0][$i];?></b><br><br>
				<?php echo $lang['hilfe'][16][1][$i];?><br><br>
				<?php
			}
			?>
		</ul>
		<?php
		echo $lang['hilfe'][16][2];
	//}elseif ($_GET["fu"]==17 bis 32) { unn�tig, nur kopf
	}elseif ($_GET["fu"]==33) {
		?>
		<center>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td>1</td>
					<td><img src="../bilder/icons/cantox.gif" border="0" width="17" height="17"></td>
					<td><nobr>&nbsp;:&nbsp;</nobr></td>
					<td>0</td>
					<td><img src="../bilder/icons/cantox.gif" border="0" width="17" height="17"></td>
				</tr>
				<tr>
					<td><?php echo str_replace('{1}',1,$lang['hilfe']['kt']);?></td>
					<td><img src="../bilder/icons/lemin.gif" border="0" width="17" height="17"></td>
					<td><nobr>&nbsp;:&nbsp;</nobr></td>
					<td>8</td>
					<td><img src="../bilder/icons/cantox.gif" border="0" width="17" height="17"></td>
				</tr>
				<tr>
					<td><?php echo str_replace('{1}',1,$lang['hilfe']['kt']);?></td>
					<td><img src="../bilder/icons/vorrat.gif" border="0" width="17" height="17"></td>
					<td><nobr>&nbsp;:&nbsp;</nobr></td>
					<td>8</td>
					<td><img src="../bilder/icons/cantox.gif" border="0" width="17" height="17"></td>
				</tr>
				<tr>
					<td><?php echo str_replace('{1}',1,$lang['hilfe']['kt']);?></td>
					<td><img src="../bilder/icons/mineral_1.gif" border="0" width="17" height="17"></td>
					<td><nobr>&nbsp;:&nbsp;</nobr></td>
					<td>8</td>
					<td><img src="../bilder/icons/cantox.gif" border="0" width="17" height="17"></td>
				</tr>
				<tr>
					<td><?php echo str_replace('{1}',1,$lang['hilfe']['kt']);?></td>
					<td><img src="../bilder/icons/mineral_2.gif" border="0" width="17" height="17"></td>
					<td><nobr>&nbsp;:&nbsp;</nobr></td>
					<td>8</td>
					<td><img src="../bilder/icons/cantox.gif" border="0" width="17" height="17"></td>
				</tr>
				<tr>
					<td><?php echo str_replace('{1}',1,$lang['hilfe']['kt']);?></td>
					<td><img src="../bilder/icons/mineral_3.gif" border="0" width="17" height="17"></td>
					<td><nobr>&nbsp;:&nbsp;</nobr></td>
					<td>8</td>
					<td><img src="../bilder/icons/cantox.gif" border="0" width="17" height="17"></td>
				</tr>
			</table>
		</center>
		<?php echo $lang['hilfe'][33][0];
	}//elseif ($_GET["fu"]==34-47) {}
} else {
    include ("../inc.conf.php");
    include ("inc.header.php");
    echo 'Wrong parameters!';
    include ("inc.footer.php");
}
?>
