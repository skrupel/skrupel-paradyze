<?php
include ("../inc.conf.php");
if(!$_GET["sprache"]){$_GET["sprache"]=$language;}
$file="../lang/".$_GET["sprache"]."/lang.orbitale_systeme.php";
include ($file);
include ("inc.header.php");
?>
    <body text="#ffffff" bgcolor="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" marginwidth="0" marginheight="0">
		<p style="font-size: 11px;"><b><?php echo $lang['orbitalesysteme']['name'][$_GET["fu2"]]; ?></b></p>
        <p>
			<img src="<?php echo $bildpfad; ?>/osysteme/<?php echo $_GET["fu2"]; ?>.gif" border="0" width="61" height="64" alt="" style="float: right; padding: 1px; border: 1px solid #eee;" />
			<?php echo ($lang['orbitalesysteme']['lang'][$_GET["fu2"]] ? $lang['orbitalesysteme']['lang'][$_GET["fu2"]] : $lang['orbitalesysteme']['kurz'][$_GET["fu2"]]); ?>
        </p>
	</body>
<?php
include ("inc.footer.php");
?>
