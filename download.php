<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

?>
 
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>Download</title>
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" href="/files/style.css" />
<script src="files/jquery-2.0.3.min.js"></script>
<style>
.myButton{box-shadow:inset 0 1px 0 0 #fff;background:linear-gradient(to bottom,#ededed 5%,#dfdfdf 100%);background-color:#ededed;border-radius:5px;border:1px solid #828282;display:inline-block;cursor:pointer;color:#565656;font-family:Arial;font-size:14px;font-weight:bold;padding:2px 4px;text-shadow:0 1px 0 #fff}
.myButton:hover{background:linear-gradient(to bottom,#dfdfdf 5%,#ededed 100%);background-color:#dfdfdf}
.myButton:active{position:relative;top:1px}
p.myText{font-size:17px;font-weight:bold;font-family:sans-serif}
</style>
</head>
<body>
<div id="add_user" style="text-align: center;">
<div class="head"><b>Download program:</b></div>
<?php
 $a_prog=array();
 $fh=fopen("./files/name_version.txt","r");
 if(filesize("./files/name_version.txt")>0) {
 $text=fread($fh,filesize("./files/name_version.txt"));
 $a_prog=explode(PHP_EOL,$text);
 }
 fclose($fh);
  foreach($a_prog as $str) {
   if(iconv_strlen($str)>1) {
    $path = $_SERVER['DOCUMENT_ROOT'].'/storage/';
    if($open = scandir($path)) {
	 $list=array();
     foreach($open as $k => $v) {
        if(strstr($v, explode(".", trim(explode(",", $str)[0]))[0])) {
		  $list[$k] = $v;
		}
	 }
    uasort($list, function($a, $b) {
     $a = mb_strtolower($a);
     $b = mb_strtolower($b);
      if ($a == $b)
        return 0;
      return ($a > $b) ? -1 : 1;
    });
	echo '<div style="border-bottom:2px solid #e7e7e7;margin:0 15px;padding:10px 0;">';
     foreach($list as $v) {
	  if(mb_strtolower(explode(".", $v)[1])=='gif' || mb_strtolower(explode(".", $v)[1])=='jpg' || mb_strtolower(explode(".", $v)[1])=='png') {
           echo '<p><img style="background:#d6d6d6;-webkit-box-shadow:2px 2px 8px rgb(0 0 0 / 20%);box-shadow:2px 2px 8px rgb(0 0 0 / 20%);border-radius:3px;padding:2px;" src="/storage/'.$v.'" alt="'.$v.'" title="'.$v.'" /></p>';
	  } else {
		  echo '<form action="/storage/?file='.$v.'" method="post">';
           echo '<p class="myText">MetaTrader '.explode(".ex", $v)[1].': <button class="myButton">'.$v.'</button></p>';
          echo '</form>';
	  }
     }
      $price=array();
      $fh=fopen("./files/price_link.txt","r");
      if(filesize("./files/price_link.txt")>0) {
       $text=fread($fh,filesize("./files/price_link.txt"));
       $price=explode(PHP_EOL,$text);
      }
      fclose($fh);
	  foreach($price as $str1) {
	   if(explode(".ex", $v)[0]==trim(explode(",", $str1)[0])) {
	   // if(trim(explode(",", $str1)[2])!='') {
		 //echo '<a href="'.trim(explode(",", $str1)[2]).'" target="_blank"><img src="/storage/buy.gif" alt="buy.gif" title="'.$v.'" /></a>';
	   //}
	   if(trim(explode(",", $str1)[1])!='') {
		echo '<div style="font-size:22px;font-weight:bold;color:#048ae8;margin:-20px -20px 0 0;"><span>'.trim(explode(",", $str1)[1]).'</span> <span style="font-size:18px;font-weight:normal;">USD</span></div>';
	   }
	  }
	 }
	echo '</div>';
    }
   }
  }
?>
</div>
<div class="head" style="margin-top:10px;height:20px;text-align:center;font-size:18px;color:#0b00b9;">
<?php
$email='support@algosamurai.com';//file_get_contents("./files/mail.txt");
if($email!="") echo 'Contact by E-mail: <span style="color:#003469;">'.$email.'</span>';
?>
</div>
</body>
</html>