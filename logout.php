<?php
ob_start();
session_start();

$_SESSION = array();

session_destroy();

header("Location: index.php");
exit();
?>
<?php
ob_end_flush();
?>