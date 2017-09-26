<?php
function portal_error_message($message, $back_link = null) {
    include 'header.php';
?>
<div class="message-box error-message">
    <p><?php echo $message; ?></p>
    <p><a href="<?php echo isset($back_link) ? $back_link : 'javascript:history.back()'; ?>">Zur&uuml;ck</a></p>
<div>
<?php
    include 'footer.php';
    exit();
}

function portal_success_message($message, $next_link = null) {
    include 'header.php';
?>
<div class="message-box success-message">
    <p><?php echo $message; ?></p>
    <p><a href="<?php echo isset($next_link) ? $next_link : 'index.php'; ?>">Weiter</a></p>
<div>
<?php
    include 'footer.php';
    exit();
}

function portal_load_module($module) {
    $module_file = PRDYZ_DIR_INCLUDES.'/portal/modules/'.$module.'.php';
    if (file_exists($module_file)) {
        return include $module_file;
    } else {
        return false;
    }
}