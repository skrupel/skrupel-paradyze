<?php
    include_once "include/global.php";

    if ($_SESSION["user_id"]) {
        header("Location: index.php");
    }

    include_once "include/header.php";

    $fu = $_GET["fu"];

    if ($fu == 1):
        $fehlermeldung = "";
        $email = $_POST["email"];
        $loginname = $_POST["loginname"];

        if (!strlen($loginname) >= 1) {
            $fehlermeldung = $fehlermeldung . "Fehler: Benutzername erforderlich\\n";
        }
        if (((strlen($loginname) < 4) || (strlen($loginname) > 30)) and (strlen($loginname) >= 1)) {
            $fehlermeldung = $fehlermeldung . "Fehler: Benutzername darf nur zwischen 4 und 30 Zeichen haben\\n";
        }
        if ((!preg_match("/^[a-zA-Z_0-9_\(\)\[\]\-\+\*]+$/", $loginname)) and (strlen($loginname) >= 1)) {
            $fehlermeldung = $fehlermeldung . "Fehler: Benutzername muss aus alphanummerischen Zeichen bestehen (0-9,a-z,A-Z)([]()-_+*)\\n";
        }

        $zeiger = mysql_query("SELECT count(*) as total FROM skrupel_user where nick='$loginname'");
        $array = @mysql_fetch_array($zeiger);
        $total = $array["total"];
        if ($total >= 1) {
            $fehlermeldung = $fehlermeldung . "Fehler: Benutzername bereits vorhanden\\n";
        }

        if (!strlen($email) >= 1) {
            $fehlermeldung = $fehlermeldung . "Fehler: E-Mail erforderlich\\n";
        }

        if (strlen($fehlermeldung) >= 1):
?><center><br><br><?php echo $fehlermeldung; ?></center><?php
        else:
            $conso = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "v", "w", "x", "y", "z");
            $vocal = array("a", "e", "i", "o", "u");
            $passwort = "";
            @srand((double)microtime() * 1000000);
            for ($f = 1; $f <= 5; $f++) {
                $passwort .= $conso[rand(0, 19)];
                $passwort .= $vocal[rand(0, 4)];
            }

            $zeiger = @mysql_query("INSERT INTO skrupel_user (nick,passwort,email,optionen) values ('$loginname','$passwort','$email','10111111111000')");
            $nachricht = "Willkommen bei $conf_game_title! Wir w�nschen dir viel Spa� beim Spielen!\n\nDeine Zugangsdaten lauten:\n\nBenutzername: $loginname\nPasswort: $passwort\n\nDu kannst dich nun mit diesen Daten einloggen auf:\nhttp://spaze.iceflame.net/portal/login.php\n\n---\nDies ist eine automatisch generierte E-Mail\nBitte nicht antworten.";
            if ($conf_mail_extra_params) {
                @mail($email, "$conf_game_name - Zugangsdaten", $nachricht, "From: $absenderemail\r\n" . "Reply-To: $absenderemail\r\n" . "X-Mailer: PHP/" . phpversion(), "-f $absenderemail {$email}");
            } else {
                @mail($email, "$conf_game_name - Zugangsdaten", $nachricht, "From: $absenderemail\r\n" . "Reply-To: $absenderemail\r\n" . "X-Mailer: PHP/" . phpversion());
            }

?><center><br><br>Die Anmeldung war erfolgreich.<br><br>Die Zugangsdaten wurden per E-Mail �bermittelt.</center><?php
        endif;
    else:
?>
<center>
  <p style="text-align: center;">&nbsp;</p>
  <h1>Registrieren</h1>
  <p style="text-align: center;">&nbsp;</p>
  <form action="?fu=1" method="post" name="formular">
    <table border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td align="right">Benutzername&nbsp;</td>
        <td><input type="text" name="loginname" class="eingabe" maxlength="30" style="width:350px;" value="" /></td>
      </tr>
      <tr>
        <td align="right">E-Mail&nbsp;</td>
        <td><input type="text" name="email" class="eingabe" maxlength="255" style="width:350px;" value="" /></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center;">Mit der Registrierung stimmst du unseren <a href="http://www.iceflame.net/page/nutzungsregeln" rel="shadowbox;options={animSequence:'sync'}">Nutzungsregeln</a> zu.</td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center;">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center;"><input type="submit" name="submit" value="Registrieren" style="width:150px;" /></td>
      </tr>
    </table>
  </form>
  <p style="text-align: center;">&nbsp;</p>
<center>
<?php
    endif;

    include_once "include/footer.php";
?>
