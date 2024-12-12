<?php
require "connect_mysql.inc.php";
require "session.inc.php";
// Только для первого админа
if($administrator) {
// Перезапишем mail в файл
 if(isset($_GET["mail"])) {
  file_put_contents("mail.txt",trim($_GET["mail"]));
 }
// Перезапишем program в файл
 if(!empty($_GET["edit_prog"])) {
  $k= implode(",", $_GET["edit_prog"]);
  $m= explode("|", $k);
  $res='';
  $npr=array();
  $h=-1;
   foreach($m as $l) {
    if(iconv_strlen($l)>4) {
	 $h++;
	 $str =trim(str_replace(" ",'',$l));
     $res.= trim(ltrim($str,",")).PHP_EOL;
	 $npr[] = explode("|",trim(trim($m[$h],",")))[0].PHP_EOL;
    }
   }
   file_put_contents("name_version.txt", $res);
   // Создадим файлы для блокировки в папке "blocking"
   foreach($npr as $e) {
    if(mb_strlen($e)>4) {
     $nm=explode(",",$e)[0];
     $filename='blocking/'.$nm.".csv";
     if(!file_exists($filename))
       file_put_contents($filename,'');
    }
   }
 }
// Перезапишем price в файл
 if(!empty($_GET["edit_price"])) {
  $k= implode(",", $_GET["edit_price"]);
  $m= explode("|", $k);
  $res='';
   foreach($m as $l) {
    if(iconv_strlen($l)>4) {
	 $str =trim(str_replace(" ",'',$l));
     $res.= trim(ltrim($str,",")).PHP_EOL;
    }
   }
   file_put_contents("price_link.txt", $res);
 }
// Перезапишем agent в файл
 if(!empty($_GET["edit_agent"])) {
  $k= implode(",", $_GET["edit_agent"]);
  $m= explode("|", $k);
  $res='';
   foreach($m as $l) {
    if(iconv_strlen($l)>4) {
	 $str =str_replace(".",',',$l);
	 $str =trim(str_replace(" ",'',$str));
	 $str =implode(',',array_unique(explode(',',$str)));
	 $res.=trim(str_replace(":,",':',$str),",").PHP_EOL;
    }
   }
   file_put_contents("access_rights.txt", $res);
 }
// Перезапишем pass в файл
 if(!empty($_GET["edit_pass"])) {
  file_put_contents(".htpasswd", trim($_GET["edit_pass"]).PHP_EOL);
 }
}
// перезагрузим после запроса
if(isset($_GET["mail"]) || isset($_GET["edit_prog"]) || isset($_GET["edit_price"]) || isset($_GET["edit_agent"]) || isset($_GET["edit_pass"])) {
  header('Location: '.$_SERVER['HTTP_REFERER']);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>Office</title>
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" href="/files/styles.css" />
<script src="/files/jquery-2.0.3.min.js"></script>
</head>
<body>
<div id="add_user">
<div class="head">
<b style="color:#227500;">Purchased</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="showUser" onclick="showCloseUser()"><?=(!isset($_COOKIE['textUser'])?'Show':$_COOKIE['textUser']);?></span>
<form class="fmail" method="GET">
    <input type="text" name="mail" value="<?=$administrator?file_get_contents("mail.txt"):'Your e-mail'?>">
    <input type="submit" name="smail" value="Write">
</form>
<span class="logout" title="Logout">&nbsp;<?=$_SERVER['PHP_AUTH_USER']?>&nbsp;</span>
<span><a class="landing" href="/download.php" target="_blank">Download</a></span>
<span><a class="connect" href="/connect?page=1">Auto registration</a></span>
</div>
<!-- block -->
<div class="spoilerUser" style="<?=$_COOKIE['showUser']==1?'display:block':'display:none'?>">
<form class="regform" action="/office?page=1" method="POST">
 <table>
    <tr>
	  <td>Package:</td>
	  <td>
	    <select class="reg_form" name="package">
		  <option value="----">----</option>
		  <?php
		  foreach($packages as $i) {
		   echo '<option value="'.$i.'" '.(isset($_POST['package'])?$_POST['package']==$i?'selected':'':'').'>'.$i.'</option>';
          }
		  ?>
		</select>
	  </td>
	</tr>
	<tr>
	  <td>Account:</td>
	  <td><input class="reg_form" type="text" name="account" value="<?=isset($_POST['account'])?$_POST['account']:''?>" placeholder="number" /></td>
	</tr>
	<tr>
	  <td>Expire:</td>
	  <td>
	   <select name="day">
	    <option>----</option>
	    <?php
	    for($x=1; $x<=31; $x++) {
		  echo '<option '.(empty($_POST['day'])?(date("j")==$x?'selected':''):($_POST['day']==$x?'selected':'')).'>'.$x.'</option>';
		}
		?>
	   </select>
	   <select name="month">
	    <option>----</option>
	    <?php
	    for($x=1; $x<=12; $x++) {
		  echo '<option '.(empty($_POST['month'])?(date("n")==$x?'selected':''):($_POST['month']==$x?'selected':'')).'>'.$x.'</option>';
		}
		?>
	   </select>
	   <select name="year">
	    <option>------</option>
	    <?php
	    for($x=0; $x<=16; $x++) {
		  echo '<option '.(empty($_POST['year'])?(date("Y")==date("Y")+$x?'selected':''):($_POST['year']==(date('Y')+$x)?'selected':'')).'>'.(date('Y')+$x).'</option>';
		 if(date('Y')+$x=='2037') break;
		}
		?>
	   </select>
	  </td>
	</tr>
	<tr>
	  <td>Program:</td>
	  <td>
	   <select name="nprog">
	    <option>--------------</option>
	    <?php
		foreach($ar_prog as $w) {
		 if(iconv_strlen($w)>1) {
		  $rsl=explode(",",$w)[0];
		  echo "<option value=".$rsl." ".(isset($_POST['nprog'])?$_POST['nprog']==$rsl?'selected':'':'').">".$rsl."</option>";
		 }
        }
		?>
	   </select>
	  </td>
	</tr>
	<tr>
	  <td>Payment:</td>
	  <td><input class="reg_form" type="number" name="payment" value="<?=isset($_POST['payment'])?$_POST['payment']:'';?>" placeholder="100" /></td>
	</tr>
	<tr>
	  <td>Comment:</td>
	  <td><textarea class="reg_form" rows="2" cols="50" name="comment" placeholder="comment"><?=empty($_POST['comment'])?'':$_POST['comment'];?></textarea></td>
	</tr>
	<input type="hidden" name="add" value="<?=$_SERVER['PHP_AUTH_USER'];?>" />
    <tr><td></td><td><input class="button" type="submit" value="Register now" name="submit" /></td></tr>
  </table>
<?php
 $info_reg='';
 if(isset($_POST['submit'])) {
	if(empty($_POST['package'])||$_POST['package']=="----") $info_reg = 'Fill in: Package';
	elseif(empty($_POST['account'])) $info_reg = 'Fill in: Account';
	elseif(empty($_POST['day'])||$_POST['day']=="----") $info_reg = 'Fill in: Day';
	elseif(empty($_POST['month'])||$_POST['month']=="----") $info_reg = 'Fill in: Month';
	elseif(empty($_POST['year'])||$_POST['year']=="------") $info_reg = 'Fill in: Year';
	elseif(empty($_POST['nprog'])||$_POST['nprog']=="--------------") $info_reg = 'Fill in: Program';
	elseif(!is_numeric($_POST['payment'])) $info_reg = 'Fill in: Payment';
	else {
		$registrar = htmlspecialchars(trim($_POST['add']));
		$package = htmlspecialchars(trim($_POST['package']));
		$account = htmlspecialchars(trim($_POST['account']));
		$deactivate_date = strtotime($_POST['day'].'-'.$_POST['month'].'-'.$_POST['year']. '23:59:59');
		$program = htmlspecialchars(trim($_POST['nprog']));
		$payment = htmlspecialchars(trim($_POST['payment']));
		$comment = htmlspecialchars(trim($_POST['comment']));
		if($comment=='') $comment=' ';
//-
 $db = mysqli_connect($localhost, $mysql_user, $mysql_password, $mysql_db);
 mysqli_set_charset($db, 'utf8'); //utf-8
  if(!$db) {
    echo "<br>Error: Unable to connect to MySQL<br>Error code: ".mysqli_connect_errno()."<br>Error text: ".mysqli_connect_error();
    exit;
  }
  $flag=true;
  $query = "SELECT * FROM `users_auth` WHERE `program`='$program'";
  $result = mysqli_query($db, $query);
  while($row = mysqli_fetch_array($result)) {
	if($account==$row['account']) {
     echo 'Account <b>'.$account.'</b> is already registered under number <b>'.$row['id'].'</b>';
	 $flag=false;
	 break;
	}
  }
// Только для первого админа
 if($administrator) {
  if($flag) {
	$time=time();
    $result = mysqli_query($db, "SELECT * FROM `users_auth` ORDER BY `id` DESC LIMIT 1");
    $cn=mysqli_fetch_array($result)[0]+1;
    $query = "INSERT INTO `users_auth` (`id`,`add_date`,`hist_update_date`,`deactivate_date`,`hist_deactivate_date`,`account`,`registrar`,`ref`,`fee`,`program`,`package`,`hist_package`,`payment`,`hist_payment`,`comment`,`hist_comment`,`hist_update_user`,`test`,`hist_full_name_blocked`,`hist_serialNo_blocked`) VALUES ('$cn','".date('d.m.Y H:i',time())."',".time().",'$deactivate_date','$deactivate_date','$account','$registrar','$registrar','100','$program','$package','$package','$payment','$payment','$comment','$comment','$registrar','1','0','0')";
	$result = mysqli_query($db, $query);// or die(mysqli_error($db));
	if(!$result) {
	 $info_reg = mysqli_error($db);
	} else {
	 $info_reg = 'Registered!';
	 $_SESSION['prog']=$_POST['nprog'];
     ob_start();
     header('Location: '.$_SERVER['HTTP_REFERER']);
     ob_end_flush();
	}
  }
 } $info_reg = 'Only Administrator';
  mysqli_close($db);
 }
} else $info_reg = 'Blank Fields';
echo '<div class="add_error">'.$info_reg.'</div>';
?>
</form>
<div class="head"><b>Editing products:</b> &nbsp;&nbsp;&nbsp;<span class="showVersion" onclick="showCloseVersion()"><?=(isset($_COOKIE['textVersion'])?$_COOKIE['textVersion']:'Show');?></span></div>
<?php
// Покажем форму ввода
 if(empty($_COOKIE['showVersion'])) $_COOKIE['showVersion']=0;
 echo '<div class="spoilerVersion edit_fields" style="display:'.($_COOKIE['showVersion']==1?'block':'none').'">';
 echo '<form class="edit_form" method="get">';
 $a_prog=array();
 $fh=fopen("name_version.txt","r");
 if(filesize("name_version.txt")>0) {
 $text=fread($fh,filesize("name_version.txt"));
 $a_prog=explode(PHP_EOL,$text);
 }
 fclose($fh);
  foreach($a_prog as $str) {
   if(iconv_strlen($str)>1) {
	echo '<div class="formdwl">';
	echo 'Program: <input type="text" class="edit_prog" name="edit_prog[]" value="'.explode(",", $str)[0].'" />';
	echo ' Version: <input type="text" class="edit_vers" name="edit_prog[]" value="'.explode(",", $str)[1].'" />';
	echo ' Trial version days: <input type="text" class="edit_time" name="edit_prog[]" value="'.explode(",", $str)[2].'" />';
	echo ' Package type auto-registration: <select class="reg_form" name="edit_prog[]">
	                                        <option value="">-----</option>';
											foreach ($packages as $i) {
											 echo '<option value="'.$i.'" '.(explode(",", $str)[3]==$i?'selected':'').'>'.$i.'</option>';
                                            }
		                                    echo '</select>';
	echo ' Number of activations: <input type="text" class="edit_time" name="edit_prog[]" value="'.explode(",", $str)[4].'" />';
	echo '<input type="hidden" name="edit_prog[]" value="|" />';
  $path = $_SERVER['DOCUMENT_ROOT'].'/storage/';
  if($open = scandir($path)) {
	$list=array();
    foreach($open as $v) {
      if(strstr($v, explode(".", explode(",", $str)[0])[0])) {
		$list[] = $v;
	  }
	}
	natcasesort($list);
    foreach($list as $v) {
       if(strstr($v, explode(".", explode(",", $str)[0])[0])) {
        switch(explode(".", $v)[1]) {
          case 'ex4':$color='006f03'; break;
          case 'ex5':$color='002ca9'; break;
          case 'jpg':$color='00b6ec'; break;
          case 'JPG':$color='c70081'; break;
          case 'png':$color='00b381'; break;
          case 'PNG':$color='8b9407'; break;
          case 'gif':$color='e07200'; break;
          default: $color='000';
        }
		if(explode(".", $v)[1]=='ex4' || explode(".", $v)[1]=='ex5') echo ', <a style="color:#'.$color.';text-decoration:none" href="'.($administrator?'storage?nm='.$v:'').'" onclick="return confirm(\'Delete file?\')">'.$v.' ('.date('d.m.Y',filectime($path.$v)).')</a>';
		else echo ', <a style="color:#'.$color.';text-decoration:none" href="'.($administrator?'storage?nm='.$v:'').'" onclick="return confirm(\'Delete file?\')">'.$v.'</a>';
      }
    }
  }
    echo '</div>';
   }
  }
	echo '<div class="formdwl">';
	echo 'Program: <input type="text" class="edit_prog" name="edit_prog[]" value="" />';
	echo ' Version: <input type="text" class="edit_vers" name="edit_prog[]" value="" />';
	echo ' Trial version days: <input type="text" class="edit_time" name="edit_prog[]" value="" />';
	echo ' Package type auto-registration: <select class="reg_form" name="edit_prog[]">
	                                        <option value="">-----</option>';
											foreach ($packages as $i) {
											 echo '<option value="'.$i.'">'.$i.'</option>';
                                            }
		                                    echo '</select>';
	echo ' Number of activations: <input type="text" class="edit_time" name="edit_prog[]" value="" />';
	echo '<input type="hidden" name="edit_prog[]" value="|" />';
    echo '</div>';
    echo '<p><input class="edit_button" type="submit" style="margin-left:450px;" value="Update" name="submit" /></p>';
  echo '</form>';
  echo '<span id="download">';
        // Только для первого админа
         if($administrator) echo '<form enctype="multipart/form-data" action="/storage/" method="POST">';
           echo '<label for="filename" class="chous">Select files</label>';
           echo '<input type="file" class="file" id="filename" name="filename" accept=".ex4,.ex5,.gif,.jpg,.png" />';
           echo '<input type="submit" class="submit" style="line-height:10px;background:#72c505;margin-left:5px;padding:4px 7px;display:none;" value="&#8663;" />';
		   echo ' (ex4, ex5, gif, jpg, png)';
         if($administrator) echo '</form>';
  echo '</span>';

  echo '<div class="spoilerVersion edit_fields" style="display:'.($_COOKIE['showVersion']==1?'block':'none').'">';
  $b_prog=array();
  $fh=fopen("price_link.txt","r");
  if(filesize("price_link.txt")>0) {
   $text=fread($fh,filesize("price_link.txt"));
   $b_prog=explode(PHP_EOL,$text);
  }
  fclose($fh);
  echo '<form class="edit_form" method="get">';
   foreach($a_prog as $str1) {
    if(iconv_strlen($str1)>1) {
	 echo '<div class="formdw1" style="padding:1px;">';
	 echo 'Program: <input disabled="disabled" type="text" class="edit_price" name="edit_price[]" value="'.explode(",", $str1)[0].'" />';
	 echo '<input type="hidden" class="edit_price" name="edit_price[]" value="'.explode(",", $str1)[0].'" />';
	foreach($b_prog as $str2) {
	 if(explode(",", $str1)[0]==explode(",", $str2)[0]) {
	  echo '&nbsp;&nbsp;Price: <input type="text" class="edit_vers" name="edit_price[]" value="'.explode(",", $str2)[1].'" />';
	  echo '&nbsp;&nbsp;Link Buy:<input type="text" class="edit_time" name="edit_price[]" value="'.explode(",", $str2)[2].'" style="width:25%;text-align:left;" />';
	  echo '&nbsp;&nbsp;Message:<input type="text" class="edit_time" name="edit_price[]" value="'.explode(",", $str2)[3].'" style="width:30%;text-align:left;" placeholder="Terminal message (Update version) 60 symb" />';
	 }
	}
	 echo '<input type="hidden" name="edit_price[]" value="|" />';
     echo '</div>';
    }
   }
   echo '<p><input class="edit_button" type="submit" style="margin-left:442px;" value="Update" name="submit" /></p>';
   echo '</form>';
  echo '</div>';

 echo '</div>';
?>
<script>
$('.edit_vers').change(function() {
  var r=$(this).val().replace(",",".");
  $(this).val(r);
});
$('.file').change(function() {
   if($(this).val() != '') {
    var flag=false;
	  var fd=$(this)[0].files[0].name;
      $('.edit_prog').each(function() {
	   var nm=$(this).val();
	   if(fd.split('.')[0]==nm) {
		 flag=true;
	   }
	  });
	if(flag) {
	  $('.submit').show();
	  $(this).prev().text(fd);
	  return;
	} else {
	  $('.submit').hide();
	  $('.file').prev().text(fd.split('.')[0]+': program is not registered in the system');
	}
   } else {
	  $(this).prev().text('Select files');
	  $('.submit').hide();
   }
});
</script>
<div class="head"><b>Adding admins:</b> &nbsp;&nbsp;&nbsp;<span class="showAdmin" onclick="showCloseAdmin()"><?=(isset($_COOKIE['textAdmin'])?$_COOKIE['textAdmin']:'Show');?></span></div>
<?php
// Покажем форму ввода
 if(empty($_COOKIE['showAdmin'])) $_COOKIE['showAdmin']=0;
 echo '<div class="spoilerAdmin edit_fields" style="display:'.($_COOKIE['showAdmin']==1?'block':'none').'">';
 echo '<h3 style="margin:0 0 5px 10px;">login:password (encrypt)</h3>';
 echo '<form class="edit_form" action="" method="get">';
 echo '<textarea id="pass" style="margin-left:10px;" rows="5" cols="60" name="edit_pass">'.($administrator?file_get_contents('.htpasswd', true):'Only Administrator').'</textarea>';
 echo '<div style="margin-left:170px;"><a href="https://htmlweb.ru/service/htpasswd.php" target="_blank">Generate password</a></div>';
 echo '<p><input class="edit_button" style="margin-left:192px;" type="submit" value="Update" name="submit" /></p>';
 echo '</form>';
//----------------
  $c_prog=array();
  $fh=fopen("access_rights.txt","r");
  if(filesize("access_rights.txt")>0) {
   $text=fread($fh,filesize("access_rights.txt"));
   $c_prog=explode(PHP_EOL,$text);
  }
  fclose($fh);
  echo '<form class="edit_form" method="get">';
   foreach($a_prog as $str3) {
    if(iconv_strlen($str3)>1) {
	 echo '<div class="formdw1" style="padding:1px;">';
	 echo 'Program: <input disabled="disabled" type="text" class="edit_price" name="edit_agent[]" value="'.explode(",",$str3)[0].'" />';
	 echo '<input type="hidden" class="edit_price" name="edit_agent[]" value="'.explode(",", $str3)[0].':" />';
	 foreach($c_prog as $str4) {
	  if(explode(",", $str3)[0]==explode(":", $str4)[0]) { 
	   echo '&nbsp;&nbsp;Login: <input type="text" class="edit_vers" style="width:40%;" name="edit_agent[]" value="'.($administrator?''.str_replace(",",', ',explode(":",$str4)[1]).', ':'Only Administrator').'" />';
	  }
	 }
	 echo '<input type="hidden" name="edit_agent[]" value="|" />';
     echo '</div>';
    }
   }
   echo '<p><input class="edit_button" type="submit" style="margin-left:442px;" value="Update" name="submit" /></p>';
   echo '</form>';

 echo '</div>';
?>
<script>
$('#pass').on('click', function(){
  this.style.height = '1px';
  this.style.height = (this.scrollHeight + 14) + 'px'; 
});
$('#pass').on('input', function(){
  this.style.height = '1px';
  this.style.height = (this.scrollHeight + 14) + 'px'; 
});
</script>
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