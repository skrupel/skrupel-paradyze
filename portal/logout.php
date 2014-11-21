<?php
  session_start();
  unset($_SESSION["user_id"]);
  unset($_SESSION["name"]);
  unset($_SESSION["pass"]);
  unset($_SESSION);
  header("Location: index.php");
?>