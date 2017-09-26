<?php
include_once 'include/global.php';

$username = $_POST["login"];
$password = $_POST["pass"];

if ($username && $password) {
    if ($conf_enable_ai && preg_match('/KI (Leicht|Mittel) [0-9]/i', $username) == 1) {
        die('Login mit diesem Nutzernamen nicht erlaubt!');
    }

    $sql = 'SELECT user.*, spiele.sid FROM skrupel_user AS user
            LEFT JOIN skrupel_spiele AS spiele ON phase = 0
            AND (user.id = spieler_1 OR user.id = spieler_2 OR user.id = spieler_3 OR user.id = spieler_4 OR user.id = spieler_5 OR user.id = spieler_6 OR user.id = spieler_7 OR user.id = spieler_8 OR user.id = spieler_9 OR user.id = spieler_10)
            WHERE user.nick="'.db_escape($username).'" AND user.passwort="'.db_escape($password).'"
            GROUP BY user.id';
    $query = mysql_query($sql);
    if (mysql_num_rows($query) == 1) {
        $data = mysql_fetch_assoc($query);
        $_SESSION["user_id"] = $data["id"];
        $_SESSION["uid"]     = $data["uid"];
        $_SESSION["sid"]     = $data["sid"];
        $_SESSION["name"]    = $data["nick"];
        $_SESSION["pass"]    = $data["passwort"];
        $_SESSION["rights"]  = explode(",",$data["rights"]);
    }
}

include 'include/header.php';

if ($_SESSION["user_id"]): ?>
<table cellpadding="5" cellspacing="2" width="100%">
  <tr>
    <td width="70%" valign="top">
<?php //portal_load_module('news'); ?>
    </td>
    <td>&nbsp;</td>
    <td width="30%" valign="top">
<?php
    //db_connect();
?>
<?php portal_load_module('turn_status'); ?>
        <br />
<?php portal_load_module('tips'); ?>
    </td>
  </tr>
</table>
<?php else: ?>
 <table border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
   <tr>
     <td style="text-align: center; width: 50%; padding: 0 15px 0 15px;">
	<h1>Willkommen bei Paradyze</h1>
	<p style="text-align: center; line-height: 1.7em;">
		Paradyze ist eine Mischung aus VGA-Planets und Ascendancy. Man bebaut seinen ersten Planeten, schickt Schiffe zu
		weiteren, gr&uuml;ndet dort Kolonien, sammelt Rohstoffe, baut Sternenbasen und hetzt sich gegenseitig seine Flotten auf den
		Hals. Man kann mit Partnern Karteninformationen austauschen und gemeinsame Flotten organisieren. Pro Spielrunde kannst
		du gegen 9 andere <b>menschliche oder KI-Spieler</b> antreten. Zum Spielen reicht ein ganz normaler aktueller Browser.
	</p>
	<form action="index.php" method="post" name="formular">
		<table border="0" cellspacing="0" cellpadding="4" style="text-align: center; width: 100%;">
			<tr>
				<td style="text-align: right; width: 50%;">Benutzername:&nbsp;</td>
				<td style="text-align: left; width: 50%;"><input type="text" name="login" class="eingabe" maxlength="50" style="width:150px;" value="" /></td>
			</tr>
			<tr>
				<td style="text-align: right;">Passwort:&nbsp;</td>
				<td style="text-align: left;"><input type="password" name="pass" class="eingabe" maxlength="50" style="width:150px;" value="" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center;"><input type="submit" name="submit" value="Login" style="width:120px;" /></td>
			</tr>
		</table>
	</form>
	<p style="text-align: center;">Noch kein Mitglied? <a href="register.php">Sofort registrieren!</a></p>
     </td>
     <td style="text-align: center; width: 50%;">
	   <img src="../bilder/welcome.jpg" alt="Screenshot vom Spielfeld" class="imgbox" />
     </td>
   </tr>
</table>
<?php endif;

include "include/footer.php";