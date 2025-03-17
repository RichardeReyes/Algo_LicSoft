<?php
require "connect_mysql.inc.php";
require "session.inc.php";
// обновим таблицу
if($_POST['update']>0 && isset($_POST['field']) && isset($_POST['id'])) {
 $db = mysqli_connect($localhost, $mysql_user, $mysql_password, $mysql_db);
 mysqli_set_charset($db, 'utf8'); //utf-8
  if(!$db) {
    echo "<br>Error: Unable to connect to MySQL<br>Error code: ".mysqli_connect_errno()."<br>Error text: ".mysqli_connect_error();
    exit;
  }
$field = trim($_POST['field']);
$hist_update_user = isset($_POST['add'])?$_POST['add']:'auto';
$id = $_POST['id'];
$today = time();

// Только для первого админа
if($tester) {
    if($_POST['update']==10) {
	  header("Refresh: 2; url=".$_SERVER['HTTP_REFERER']);
      echo '<b>Clear History ID='.$id.'</b><br><img src="loading.gif">';
	  echo '<br><br><b>Only Administrator</b>';
	} else 
	  echo "Updated successfully";
  mysqli_close($db);
  exit;
}
if($moderator && $_POST['update']!=3 && $_POST['update']!=4 && $_POST['update']!=5 && $_POST['update']!=6) {
    if($_POST['update']==10) {
	  header("Refresh: 2; url=".$_SERVER['HTTP_REFERER']);
      echo '<b>Clear History ID='.$id.'</b><br><img src="loading.gif">';
	  echo '<br><br><b>Only Administrator</b>';
	} else 
	  echo "Updated successfully";
  mysqli_close($db);
  exit;
}
//---
 if($_POST['update']==1) {
	$result = "UPDATE `users_auth` SET `package`='$field',
	                                   `hist_package`=CONCAT_WS('|',`hist_package`, '$field'),
									   `hist_update_date`=CONCAT_WS('|',`hist_update_date`, '$today'),
									   `hist_deactivate_date`=CONCAT_WS('|',`hist_deactivate_date`, ''),
									   `hist_full_name_blocked`=CONCAT_WS('|',`hist_full_name_blocked`, ''),
									   `hist_serialNo_blocked`=CONCAT_WS('|',`hist_serialNo_blocked`, ''),
									   `hist_payment`=CONCAT_WS('|',`hist_payment`, ''),
									   `hist_comment`= CONCAT_WS('|',`hist_comment`, ''),
									   `hist_update_user`= CONCAT_WS('|',`hist_update_user`,'$hist_update_user'),
                                       `connect`='0',`disconnect`='0' WHERE `id`=$id AND `package`!='$field'";
 }
 if($_POST['update']==2) {
  $test=$field>0?1:2;
	$result = "UPDATE `users_auth` SET `payment`='$field',
	                                   `hist_payment`= CONCAT_WS('|',`hist_payment`, '$field'),
									   `hist_update_date`=CONCAT_WS('|',`hist_update_date`, '$today'),
									   `hist_deactivate_date`=CONCAT_WS('|',`hist_deactivate_date`, ''),
									   `hist_full_name_blocked`=CONCAT_WS('|',`hist_full_name_blocked`, ''),
									   `hist_serialNo_blocked`=CONCAT_WS('|',`hist_serialNo_blocked`, ''),
									   `hist_package`=CONCAT_WS('|',`hist_package`, ''),
									   `hist_comment`= CONCAT_WS('|',`hist_comment`, ''),
									   `hist_update_user`= CONCAT_WS('|',`hist_update_user`,'$hist_update_user'),
									   `test`='$test',
                                       `connect`='0',`disconnect`='0' WHERE `id`=$id";
 }
 if($_POST['update']==3) {
	$result = "UPDATE `users_auth` SET `comment`='$field',
	                                   `hist_comment`= CONCAT_WS('|',`hist_comment`, '$field'),
									   `hist_update_date`=CONCAT_WS('|',`hist_update_date`, '$today'),
									   `hist_deactivate_date`=CONCAT_WS('|',`hist_deactivate_date`, ''),
									   `hist_full_name_blocked`=CONCAT_WS('|',`hist_full_name_blocked`, ''),
									   `hist_serialNo_blocked`=CONCAT_WS('|',`hist_serialNo_blocked`, ''),
									   `hist_payment`=CONCAT_WS('|',`hist_payment`, ''),
									   `hist_package`=CONCAT_WS('|',`hist_package`, ''),
									   `hist_update_user`= CONCAT_WS('|',`hist_update_user`,'$hist_update_user'),
                                       `connect`='0',`disconnect`='0' WHERE `id`=$id";
 }
 if($_POST['update']==4) {
   $tm = strtotime($_POST['upday'].'-'.$_POST['upmonth'].'-'.$_POST['upyear']. '23:59:59');
   $test=$_POST['upyear']==2038?1:(($_POST['url']==1)?1:2);
   //$test=$_POST['upyear']==2038?1:$_POST['url']==1?1:2;
	$result = "UPDATE `users_auth` SET `deactivate_date`='$tm',
									   `hist_update_date`=CONCAT_WS('|',`hist_update_date`, '$today'),
									   `hist_deactivate_date`=CONCAT_WS('|',`hist_deactivate_date`, '$tm'),
									   `hist_full_name_blocked`=CONCAT_WS('|',`hist_full_name_blocked`, ''),
									   `hist_serialNo_blocked`=CONCAT_WS('|',`hist_serialNo_blocked`, ''),
									   `hist_payment`=CONCAT_WS('|',`hist_payment`, ''),
									   `hist_package`=CONCAT_WS('|',`hist_package`, ''),
									   `hist_comment`= CONCAT_WS('|',`hist_comment`, ''),
									   `hist_update_user`= CONCAT_WS('|',`hist_update_user`,'$hist_update_user'),
									   `test`='$test',
									   `connect`='0',`disconnect`='0' WHERE `id`=$id AND `deactivate_date`!='$tm'";
 }
 if($_POST['update']==5) {
	$nm = $_POST['nm'];
	$pr = $_POST['program'];
	$result = "UPDATE `users_auth` SET `full_name_blocked`='$field',
	                                   `hist_full_name_blocked`=CONCAT_WS('|',`hist_full_name_blocked`, '$field'),
									   `hist_serialNo_blocked`=CONCAT_WS('|',`hist_serialNo_blocked`, ''),
	                                   `hist_comment`= CONCAT_WS('|',`hist_comment`, ''),
									   `hist_update_date`=CONCAT_WS('|',`hist_update_date`, '$today'),
									   `hist_deactivate_date`=CONCAT_WS('|',`hist_deactivate_date`, ''),
									   `hist_payment`=CONCAT_WS('|',`hist_payment`, ''),
									   `hist_package`=CONCAT_WS('|',`hist_package`, ''),
									   `hist_update_user`= CONCAT_WS('|',`hist_update_user`,'$hist_update_user'),
                                       `connect`='0',`disconnect`='0' WHERE `full_name`='$nm' AND `program`='$pr'";
 }
 if($_POST['update']==6) {
	$nm = $_POST['nm'];
	$pr = $_POST['program'];
	serialNo_block($nm,$pr,$field);
	$result = "UPDATE `users_auth` SET `hist_serialNo_blocked`=CONCAT_WS('|',`hist_serialNo_blocked`, '$field'),
									   `hist_full_name_blocked`=CONCAT_WS('|',`hist_full_name_blocked`, ''),
	                                   `hist_comment`= CONCAT_WS('|',`hist_comment`, ''),
									   `hist_update_date`=CONCAT_WS('|',`hist_update_date`,'$today'),
									   `hist_deactivate_date`=CONCAT_WS('|',`hist_deactivate_date`, ''),
									   `hist_payment`=CONCAT_WS('|',`hist_payment`, ''),
									   `hist_package`=CONCAT_WS('|',`hist_package`, ''),
									   `hist_update_user`= CONCAT_WS('|',`hist_update_user`, '$hist_update_user'),
                                       `connect`='0',`disconnect`='0' WHERE `serialNo`='$nm' AND `program`='$pr'";
 }
 if($_POST['update']==9) {
	$result = "DELETE FROM `users_auth` WHERE id=$id";
 }
 if($_POST['update']==10) {
    $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `id`=$id");
    while($row = mysqli_fetch_array($result)) {
	  $x1=explode('|',$row['hist_update_date'])[0];
	  $x2=explode('|',$row['hist_deactivate_date'])[0];
	  $x3=explode('|',$row['hist_full_name_blocked'])[0];
	  $x5=explode('|',$row['hist_serialNo_blocked'])[0];
	  $x6=explode('|',$row['hist_payment'])[0];
	  $x7=explode('|',$row['hist_package'])[0];
	  $x8=explode('|',$row['hist_comment'])[0];
	  $x9=explode('|',$row['hist_update_user'])[0];
    }
	$result = "UPDATE `users_auth` SET `hist_update_date`= '$x1',
									   `hist_deactivate_date`= '$x2',
									   `hist_full_name_blocked`= '$x3',
									   `hist_serialNo_blocked`= '$x5',
									   `hist_payment`= '$x6',
									   `hist_package`= '$x7',
									   `hist_comment`= '$x8',
									   `hist_update_user`= '$x9',
									   `connect`='0',`disconnect`='0' WHERE `id`=$id";
 }
 if($_POST['update']==11) {
    $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `id`=$id");
    while($row = mysqli_fetch_array($result)) {
	  $x1=explode('|',$row['date_change_conf'])[0];
	  $x2=explode('|',$row['hist_serialNo'])[0];
	  $x3=explode('|',$row['ip_history'])[0];
	  $x4=explode('|',$row['hist_trading'])[0];
    }
	$result = "UPDATE `users_auth` SET `date_change_conf`= '$x1',
									   `hist_serialNo`= '$x2',
									   `ip_history`= '$x3',
									   `hist_trading`= '$x4',
									   `connect`='0',`disconnect`='0' WHERE `id`=$id";
 }
 
  if(mysqli_query($db, $result)) {
    if($_POST['update']>=10) {
	  header("Refresh: 2; url=".$_SERVER['HTTP_REFERER']);
      echo '<b>Clear History ID='.$id.'</b><br><img src="loading.gif">';
	} else 
	  echo 'Updated successfully';
  } else {
    echo "Error Updating: ".mysqli_error($db);
  }
 mysqli_close($db);
}
//--------------------------------------
function serialNo_block($str,$nm,$act) {
 $filename='blocking/'.$nm.".csv";
 $file=file($filename);
 file_put_contents($filename,'');
 $str=trim($str);
 if($act==0) {
   foreach($file as $key => $item) {
    if(trim($item) == $str) {
      unset($file[$key]);
    }
   }
   array_diff($file,array(''));
   file_put_contents($filename,implode("",$file));
 }
//-
 if($act==1) {
   array_push($file,$str);
   file_put_contents($filename,implode("",$file)."\r\n");
 }
}
?>