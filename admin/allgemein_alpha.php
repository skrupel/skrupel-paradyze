<?
if ($_GET["fu"]==1) {
include ("../inc.conf.php");
include ("inc.header.php");
if (($ftploginname==$admin_login) and ($ftploginpass==$admin_pass)) {
?>
<body text="#ffffff" bgcolor="#444444" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<center>
<h1>Offenbarung</h1>
<form name="formular" method="post" action="allgemein_alpha.php?fu=2">
<table border="0" cellspacing="5" cellpadding="0" width="80%">
 <tr>
  <td colspan="2"><textarea name="nachricht" style="width:100%;height:200px;"></textarea></td>
 </tr>
 <tr>
  <td colspan="2" align="center"><input type="submit" name="dumdidum" value="Offenbarung verk�nden" style="width:250px;"></td>
 </tr>
</table>
</form>
</center>
<? } include ("inc.footer.php");
 }

if ($_GET["fu"]==2) {
   include ("../inc.conf.php");
   include ("inc.header.php");
?>
<body text="#ffffff" bgcolor="#444444" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
   $zeiger = mysql_query("SELECT * FROM skrupel_user order by id");
   while($data = mysql_fetch_assoc($zeiger)) {
	 $cnt++;
	 if($data['email'] == '') continue;
	 $empfaenger = $data['email'];
	 $betreff = "Projekt Spaze: Newsletter";
	 $nachricht = "Hallo ".$data['nick'].",

".stripslashes($_POST['nachricht'])."

Mit freundlichen Gr��en
Dein Team von IceFlame.net

---
Du erh�lst diese Mail, da du Mitglied bei Spaze bist.";
	 $header = "From: webmaster@iceflame.net
Reply-To: webmaster@iceflame.net
X-Mailer: www.iceflame.net";
	 if(mail($empfaenger, $betreff, $nachricht, $header)) {
	   echo "Newsletter an $empfaenger geschickt!<br>\n";
	 }
	 if($cnt == mysql_num_rows($zeiger)) break;
   }
?>
</body>
<?php
   include ("inc.footer.php");
 }

?>