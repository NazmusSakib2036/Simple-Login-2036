<?php
ob_start(); // আউটপুট বাফারিং শুরু
session_start(); // সেশন শুরু

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: index.php");
exit(); // নিশ্চিত করুন যে রিডাইরেক্টের পর স্ক্রিপ্ট এক্সিকিউশন বন্ধ হয়
?>
<?php
ob_end_flush(); // আউটপুট বাফারিং শেষ
?>