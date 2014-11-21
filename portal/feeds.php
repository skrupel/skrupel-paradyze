<?php
include_once "include/global.php";

include_once "include/header.php";
?>

<p style="text-align: center;">&nbsp;</p>

<h1>Die Userfeeds</h1>

<p style="text-align: center;">&nbsp;</p>

<p style="text-align: center;">Jeder Spieler hat seinen eigenen RSS-Feed bei Paradyze,<br />
in dem alle aktuellen Züge und laufenden sowie wartenden Aktionen<br />
in den einzelenen Spielen gespeichert sind. So seid ihr immer auf dem Laufenden!</p>

<?php if ($_SESSION['user_id']): ?>
<p style="text-align: center;">Du erreichst deinen Feed über diesen Link:</p>

<p style="text-align: center;"><a href="http://www.paradyze.org/userfeed.php?name=<?php echo $_SESSION['name']; ?>"><strong>http://www.paradyze.org/userfeed.php?name=<?php echo $_SESSION['name']; ?></strong></a></p>
<?php else: ?>
<p style="text-align: center;">Ihr erreicht euren Feed über diesen Link:</p>

<p style="text-align: center;"><strong>http://www.paradyze.org/userfeed.php?name=<em>Username</em></strong></p>
<?php endif; ?>

<p style="text-align: center;">&nbsp;</p>

<?php
include_once "include/footer.php";
?>
