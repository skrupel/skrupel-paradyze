<?php if ($_GET["fu"]==1) {
include ("../inc.conf.php");
include ("../lang/".$language."/lang.admin.welcome.php");
include ("inc.header.php");
if (($ftploginname==$admin_login) and ($ftploginpass==$admin_pass)) {
?>
<body text="#ffffff" bgcolor="#444444" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<center><table border="0" height="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><center>
        <img src="../bilder/logo.png">
    </center></td>
  </tr>
</table></center>
<?php
 } include ("inc.footer.php");
 }

?>
