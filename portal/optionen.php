<?php
    include_once "include/global.php";
    if (!$_SESSION["user_id"]) {
        header("Location: index.php");
    }
    if (!$_SESSION["user_id"]) {
        $sql = "SELECT uid FROM skrupel_user WHERE id = {$_SESSION["user_id"]}";
        $data = mysql_fetch_assoc(mysql_query($sql));
        if ($data["uid"]) {
            $_SESSION["user_id"] = $data["uid"];
        } else {
            function random_number() {
                mt_srand((double)microtime() * 1000000);
                $num = mt_rand(46, 122);
                return $num;
            }
            function random_char() {
                do {
                    $num = random_number();
                } while (($num > 57 && $num < 65) || ($num > 90 && $num < 97));
                $char = chr($num);
                return $char;
            }
            function random_string() {
                $salt = "";
                for ($i = 0; $i < 20; $i++) {
                    $salt .= random_char();
                }
                return $salt;
            }
            $_SESSION["user_id"] = random_string();
            $sql = "UPDATE skrupel_user set uid=\"{$_SESSION["user_id"]}\"WHERE id={$_SESSION["user_id"]}";
            mysql_query($sql);
        }
    }
    if ($_SESSION["user_id"] == NULL) {
        header("Location: index.php");
        die();
    }
    $_GET["uid"] = $_SESSION["user_id"];
    $_GET["sid"] = $_SESSION["sid"];

    include_once "include/header.php";
    require $conf_root_path . "inhalt/meta_optionen.php";
    include_once "include/footer.php";
?>
