<?php
session_start();         // تبدأ الجلسة
session_unset();         // تفرغ كل متغيرات الجلسة
session_destroy();       // تدمر الجلسة بالكامل


header("Location: login.php");
exit;
?>
