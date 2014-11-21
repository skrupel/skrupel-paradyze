<?php
include_once '../include/browser_check.php';

if ($browser == 'mozilla' && $browser == 'epiphany') {
    $file = "mozilla";
} else {
    $file = "ie";
}

header("Content-type: text/css");
include 'skrupel.css';
include $file.'.css';
?>
