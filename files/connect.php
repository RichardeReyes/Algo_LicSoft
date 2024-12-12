<?php
require "connect_mysql.inc.php";
require "session.inc.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>Connect</title>
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" href="/files/styles.css" />
<script src="/files/jquery-2.0.3.min.js"></script>
</head>
<body>
<div id="add_user">
<div class="head">
<b style="color:#bd0000;">Testing programs</b>
<span class="logout" title="Logout">&nbsp;<?=$_SERVER['PHP_AUTH_USER']?>&nbsp;</span>
<span><a class="landing" href="/download.php" target="_blank">Download</a></span>
<span><a class="connect" href="/office?page=1">User registration</a></span>
</div>
<?php
// ----- Show User
require "template.inc.php";
?>
</div>
<footer class="head" style="margin-top:50px;text-align:right;font-size:12px;height:15px;">
<?php
$finish=microtime(true);
$delta=round($finish-$start,3);
if($delta<0.001) $delta = 0.001;
echo 'Page size: '.round(memory_get_usage()/1024/1024, 2).' МБ / generated in: '.$delta.' sec';
?>
</footer>
</body>
</html>