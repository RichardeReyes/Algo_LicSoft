<?php
ini_set('session.gc_maxlifetime', 604800);
ini_set('session.cookie_lifetime', 604800);
session_start();

// выясним админа
$main_user='';
$administrator=false;
$moderator=false;
$test_user='test';
$tester=false;
if(isset($_SERVER['PHP_AUTH_USER'])) {
 $main_user=explode(":", file_get_contents('.htpasswd', true))[0];
 if($_SERVER['PHP_AUTH_USER']==$main_user) {
  $administrator=true;
 }
 if($_SERVER['PHP_AUTH_USER']!=$main_user && $_SERVER['PHP_AUTH_USER']!=$test_user) {
  $moderator=true;
 }
 if($_SERVER['PHP_AUTH_USER']==$test_user) {
  $tester=true;
 }
}

// сессии и куки
if(!isset($_SESSION['sort'])) $_SESSION['sort']=0;
$_SESSION['sort']=(isset($_POST['sort'])?$_POST['sort']:$_SESSION['sort']);
$sort=$_SESSION['sort'];
//-
if(!isset($_SESSION['prog'])) $_SESSION['prog']=explode(",", $ar_prog[0])[0];
$_SESSION['prog']=(isset($_POST['prog'])?$_POST['prog']:$_SESSION['prog']);
$program=$_SESSION['prog'];
//-
if(!isset($_SESSION['line'])) $_SESSION['line']=10;
$_SESSION['line']=(isset($_POST['line'])?$_POST['line']:$_SESSION['line']);
$line=$_SESSION['line'];
//-
if(!isset($_COOKIE['showComp'])) $_COOKIE['showComp']=1;
if(!isset($_COOKIE['showCom'])) $_COOKIE['showCom']=1;
if(!isset($_COOKIE['showAdm'])) $_COOKIE['showAdm']=1;
//-
if(isset($_POST['sort'])) { echo ''.$_SERVER['HTTP_REFERER'];
  header('Location: '.$_SERVER['HTTP_REFERER']);
}
?>