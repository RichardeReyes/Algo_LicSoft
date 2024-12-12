<?php
header('Content-type: text/html; charset=UTF-8');
$start = microtime(true);

// подключение к БД
$localhost= "localhost";
$mysql_user= "edjaptkb_algosamu_license";
$mysql_password= "NkrKH26vmBAKQvSg73ea";
$mysql_db= "edjaptkb_algosamu_license";

// --
$packages=array("MINI","MAX");

// файл с именем и версией программ
 $ar_prog=array();
 $fh=fopen("name_version.txt","r");
 if(filesize("name_version.txt")>0) {
  $text=fread($fh,filesize("name_version.txt"));
  $ar_prog=explode(PHP_EOL,$text);
 }
 fclose($fh);
?>