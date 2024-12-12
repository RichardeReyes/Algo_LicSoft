<?php
// -- Send email Login to the site
$ip=$_SERVER['REMOTE_ADDR'];
$sn=$_SERVER['SERVER_NAME'];
$hr=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
$au=$_SERVER['PHP_AUTH_USER'];
$up=false;
if(!isset($_COOKIE['mail'])&&!$up) {
  $email=file_get_contents("mail.txt");
  if($email=="") Send($email,false);
    else Send($email,true);
  $_COOKIE['mail']=2;$up=true;
}
function GetIp($to) {
//$urlTo = 'https://www.iplocate.io/api/lookup/'.$to;
 $urlTo = 'http://api.ipstack.com/'.$to.'?access_key=1ffd2511e7d5e42b6fff0fbe77147fa9';
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL,$urlTo);
 curl_setopt($ch, CURLOPT_HEADER,false);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,30);
 curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36');
 $res = curl_exec($ch);
 curl_close($ch);
 $data=json_decode($res,true);
 //return $data['country'].', '.$data['subdivision'].'('.$data['city'].')';
 return $data['country_name'].', '.$data['region_name'].' ('.$data['city'].')';
}
function Send($to,$fl) {
  $ip=$_SERVER['REMOTE_ADDR'];
  $sn=$_SERVER['SERVER_NAME'];
  $hr=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
  $au=$_SERVER['PHP_AUTH_USER'];
  $cn=GetIp($ip);
  $subject = "Successful authorization to your personal account: ".$au.': '.$ip.' ('.$cn.') '.$hr;
  $message = "You have been logged into the website: ".$sn."<br>Login: <b>".$au."</b><br>IP: ".$ip." (".$cn."): ".$hr;
  $from = $sn;
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
  $headers .= "From: <".$from.">\r\n";
   if($fl) mail($to,$subject,$message,$headers);
   if(!file_exists("../user_connect.csv")) file_put_contents("../user_connect.csv",'');
	$lm=file("../user_connect.csv");
	array_push($lm,$au.': '.$ip.' ('.$cn.'): '.$hr);
	file_put_contents("../user_connect.csv",'');
    file_put_contents("../user_connect.csv",implode("",$lm).PHP_EOL);
}

// ----------------------- template
$url=stripos($_SERVER['SCRIPT_NAME'], 'office')?1:2;
?>
<div id="show_user">
<div class="head st<?=$url?>">Users</div>
  <form class="lineform" action="?page=1" method="POST">
   Results per page:
   <select name="line">
    <option <?=$line==10?"selected":''?>>10</option>
    <option <?=$line==20?"selected":''?>>20</option>
    <option <?=$line==30?"selected":''?>>30</option>
	<option <?=$line==40?"selected":''?>>40</option>
	<option <?=$line==50?"selected":''?>>50</option>
   </select>
  <!-- <input type="submit" value="Apply" /> -->
  </form>
  <form class="reload" action="">
    <input type="hidden" name="page" value="<?=isset($_GET['page'])?$_GET['page']:1?>" />
    <input type="submit" value="Refresh page" />
  </form>
  <i class="page st<?=$url?>"></i>
  <form class="showprod" action="?page=1" method="POST">
   <select name="prog">
	<?php
	$vers="0.00";
	foreach($ar_prog as $x) {
     if(iconv_strlen($x)>1) {
	  echo "<option value=".explode(",", $x)[0]." ".($program==explode(",", $x)[0]?'selected':'').">".explode(",", $x)[0]."</option>";
	  if($program==explode(",", $x)[0])
		$vers=explode(",", $x)[1];
	  }
	}
	?>
   </select>
   <span class="vers"><?=$vers;?></span>
  <!-- <input type="submit" value="Apply" /> -->
  </form>
  <form class="showform" action="?page=1" method="POST">
   <select name="sort">
    <option value="0" <?=$sort==0?"selected":''?>>№</option>
    <option value="1" <?=$sort==1?"selected":''?>>Full Name</option>
	<option value="2" <?=$sort==2?"selected":''?>>Package</option>
    <option value="3" <?=$sort==3?"selected":''?>>Balance Start</option>
	<option value="4" <?=$sort==4?"selected":''?>>Balance Now</option>
	<option value="5" <?=$sort==5?"selected":''?>>Expire Date</option>
	<option value="6" <?=$sort==6?"selected":''?>>Payment</option>
	<option value="7" <?=$sort==7?"selected":''?>>Type Account</option>
	<option value="8" <?=$sort==8?"selected":''?>>Last Connect</option>
	<option value="9" <?=$sort==9?"selected":''?>>Registered</option>
	<option value="10" <?=$sort==10?"selected":''?>>Referral</option>
	<option value="11" <?=$sort==11?"selected":''?>>IP</option>
	<option disabled="disabled">- - - - - - - - -</option>
	<option value="12" <?=$sort==12?"selected":''?>>Name Blocked</option>
	<option value="14" <?=$sort==14?"selected":''?>>SN Blocked</option>
	<option disabled="disabled">- - - - - - - - -</option>
	<option value="15" <?=$sort==15?"selected":''?>>Name amount</option>
	<option value="16" <?=$sort==16?"selected":''?>>IP amount</option>
	<option value="17" <?=$sort==17?"selected":''?>>SN amount</option>
	<option disabled="disabled">- - - - - - - - -</option>
	<option value="18" <?=$sort==18?"selected":''?>>Account</option>
	<option value="19" <?=$sort==19?"selected":''?>>SerialNo</option>
   </select>
   <input type="submit" value="Apply" />
  </form>
<?php
// Разграничение прав
 $c_prog=array();
 $fh=fopen("access_rights.txt","r");
  if(filesize("access_rights.txt")>0) {
   $text=fread($fh,filesize("access_rights.txt"));
    $c_prog=explode(PHP_EOL,$text);
  }
  fclose($fh);

 if($moderator) {
  foreach($c_prog as $j) {
   if($program==explode(":", $j)[0]) {
	$mk=explode(":", $j)[1];
	$lg=explode(",", $mk);
	if(mb_strlen(array_search($au, $lg))==0) {
      $program='';
	  break;
	}
   }
  }
 }
//---
 $db = mysqli_connect($localhost, $mysql_user, $mysql_password, $mysql_db);
 mysqli_set_charset($db, 'utf8'); //utf-8
  if(!$db) {
    echo "<br>Error: Unable to connect to MySQL<br>Error code: ".mysqli_connect_errno()."<br>Error text: ".mysqli_connect_error();
    exit;
  }

// NUMBER OF ROWS TO SHOW PER PAGE
$limit = $line; //5;
// GET PAGE AND OFFSET VALUE
if(isset($_GET['page'])) {
    $page = $_GET['page'] - 1;
    $offset = $page * $limit;
} else {
    $page = 0;
    $offset = 0;
	$_GET['page']=1;
}
// COUNT TOTAL NUMBER OF ROWS IN TABLE
$query = "SELECT count(id) FROM `users_auth` WHERE `test`='$url' AND `program`='$program'";
$result = mysqli_query($db, $query);
$row = mysqli_fetch_array($result);
$total_rows = $row[0];

 echo '<div class="all_users">('.$total_rows.')</div>';
// DETERMINE NUMBER OF PAGES
if ($total_rows > $limit) {
    $number_of_pages = ceil($total_rows / $limit);
} else {
    $pages = 1;
    $number_of_pages = 1;
}
// FETCH DATA USING OFFSET AND LIMIT
if(!isset($_SESSION['asc'])) $_SESSION['asc']=1;
if(isset($_POST['sort']) && $_SESSION['asc']==0) $_SESSION['asc']=1;
else if(isset($_POST['sort']) && $_SESSION['asc']==1) $_SESSION['asc']=0;

if($_SESSION['asc']==0) {
if($sort==0) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `id` ASC LIMIT $offset, $limit");
if($sort==1) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `full_name` ASC LIMIT $offset, $limit");
if($sort==2) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `package` ASC LIMIT $offset, $limit");
if($sort==3) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `hist_balance` ASC LIMIT $offset, $limit");
if($sort==4) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `balance` ASC LIMIT $offset, $limit");
if($sort==5) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `deactivate_date` ASC LIMIT $offset, $limit");
if($sort==6) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `payment` ASC LIMIT $offset, $limit");
if($sort==7) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `type` ASC LIMIT $offset, $limit");
if($sort==8) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `last_connect` ASC LIMIT $offset, $limit");
if($sort==9) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `registrar` ASC LIMIT $offset, $limit");
if($sort==10) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `ref` ASC LIMIT $offset, $limit");
if($sort==11) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY INET_ATON(ip) ASC LIMIT $offset, $limit");
if($sort==12) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `full_name_blocked` DESC, full_name ASC LIMIT $offset, $limit");
if($sort==14) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `serialNo_blocked` DESC, serialNo ASC LIMIT $offset, $limit");
if($sort==15) $result = mysqli_query($db, "SELECT * FROM `users_auth` T JOIN (SELECT full_name, COUNT(full_name) C FROM `users_auth` WHERE `test`='$url' AND `program`='$program' GROUP BY full_name) T1 USING(full_name) WHERE `test`='$url' AND `program`='$program' ORDER BY C DESC, full_name ASC LIMIT $offset, $limit");
if($sort==16) $result = mysqli_query($db, "SELECT * FROM `users_auth` T JOIN (SELECT ip, COUNT(ip) C FROM `users_auth` WHERE `test`='$url' AND `program`='$program' GROUP BY ip) T1 USING(ip) WHERE `test`='$url' AND `program`='$program' ORDER BY C DESC, INET_ATON(ip) ASC LIMIT $offset, $limit");
if($sort==17) $result = mysqli_query($db, "SELECT * FROM `users_auth` T JOIN (SELECT serialNo, COUNT(serialNo) C FROM `users_auth` WHERE `test`='$url' AND `program`='$program' GROUP BY serialNo) T1 USING(serialNo) WHERE `test`='$url' AND `program`='$program' ORDER BY C DESC, serialNo ASC LIMIT $offset, $limit");
if($sort==18) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `account` ASC LIMIT $offset, $limit");
if($sort==19) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `serialNo` ASC LIMIT $offset, $limit");
} else {
if($sort==0) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `id` DESC LIMIT $offset, $limit");
if($sort==1) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `full_name` DESC LIMIT $offset, $limit");
if($sort==2) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `package` DESC LIMIT $offset, $limit");
if($sort==3) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `hist_balance` DESC LIMIT $offset, $limit");
if($sort==4) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `balance` DESC LIMIT $offset, $limit");
if($sort==5) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `deactivate_date` DESC LIMIT $offset, $limit");
if($sort==6) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `payment` DESC LIMIT $offset, $limit");
if($sort==7) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `type` DESC LIMIT $offset, $limit");
if($sort==8) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `last_connect` DESC LIMIT $offset, $limit");
if($sort==9) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `registrar` DESC LIMIT $offset, $limit");
if($sort==10) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `ref` DESC LIMIT $offset, $limit");
if($sort==11) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY INET_ATON(ip) DESC LIMIT $offset, $limit");
if($sort==12) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `full_name_blocked` DESC, full_name ASC LIMIT $offset, $limit");
if($sort==14) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `serialNo_blocked` DESC, serialNo ASC LIMIT $offset, $limit");
if($sort==15) $result = mysqli_query($db, "SELECT * FROM `users_auth` T JOIN (SELECT full_name, COUNT(full_name) C FROM `users_auth` WHERE `test`='$url' AND `program`='$program' GROUP BY full_name) T1 USING(full_name) WHERE `test`='$url' AND `program`='$program' ORDER BY C DESC, full_name ASC LIMIT $offset, $limit");
if($sort==16) $result = mysqli_query($db, "SELECT * FROM `users_auth` T JOIN (SELECT ip, COUNT(ip) C FROM `users_auth` WHERE `test`='$url' AND `program`='$program' GROUP BY ip) T1 USING(ip) WHERE `test`='$url' AND `program`='$program' ORDER BY C DESC, INET_ATON(ip) ASC LIMIT $offset, $limit");
if($sort==17) $result = mysqli_query($db, "SELECT * FROM `users_auth` T JOIN (SELECT serialNo, COUNT(serialNo) C FROM `users_auth` WHERE `test`='$url' AND `program`='$program' GROUP BY serialNo) T1 USING(serialNo) WHERE `test`='$url' AND `program`='$program' ORDER BY C DESC, serialNo ASC LIMIT $offset, $limit");
if($sort==18) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `account` DESC LIMIT $offset, $limit");
if($sort==19) $result = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program' ORDER BY `serialNo` DESC LIMIT $offset, $limit");
}
?>
 </div>
    <table class="table">
    <tr class="first_tr">
	<th>№</th>
	<th class="add">Add_Date</th>
	<th>Full_Name</th>
	<th>IP
     <form method="GET" class="ip_show">
      <input type="hidden" name="page" value="<?=$_GET['page'];?>" />
      <input type="hidden" name="ip" value="1" />
      <button type="submit" class="ip_show" value=""></button>
     </form>
	</th>
	<th>SerialNo</th>
	<th class="acc">Account</th>
	<th class="hcomp">Broker</th>
	<th>Type</th>
	<th>MT</th>
	<th>Balance</th>
	<th class="ver">Package</th>
	<th class="pay">Payment</th>
	<th>Last Connect</th>
	<th class="exp">Expire Date</th>
	<th class="hcom comm <?=($_COOKIE['showCom']==1?'active':'');?>">Comment</th>
	<th class="adm_ref <?=($_COOKIE['showAdm']==1?'active':'');?>">Referral</th>
	<th>Del</th>
	</tr>
    <?php
	$timecurr= strtotime(date("Y-m-d H:i"));//time();//(strtotime(date('d.m.Y',time()+86400))-1)-86398;
	$year= date('Y');
    while($res = mysqli_fetch_array($result)) {
        $deactivate= $res['deactivate_date'];
	    if($url == 1) { // office
       // echo '<tr '.($res['add_user']!="site"?'style="background: #fff4f4;"':'').'>';
		echo '<tr>';
		}
	    if($url == 2) { // connect
        echo '<tr>';
		}
//-
        $hu = explode("|", $res['hist_update_user']);
        $max = count($hu);
		$main=false;
        for($i=1; $i<$max; $i++) {
          if($main_user!=$hu[$i]) $main=true;
        }
        $hs = explode("|", $res['hist_serialNo']);
        $max = count($hs);
		$snum=false;
        for($i=0; $i<$max; $i++) {
          if($res['serialNo']!=$hs[$i]) $snum=true;
        }
        echo '<td class="td_center">'.$res['id']."</td>";
		echo '<td class="wrap">'.$res['add_date']."</td>";
		$name_user=$res['full_name'];
		$c_nm= mysqli_query($db, "SELECT id FROM `users_auth` WHERE `full_name`='$name_user' AND `program`='$program'");
		$cn= mysqli_num_rows($c_nm);
		if($tester) { // Если тестер, то скроем full name
          $arr = preg_split('/(?<!^)(?!$)/u',$name_user);
          shuffle($arr);
          $name_user=implode('',$arr);
		}
		echo '<td class="full_name '.($res['full_name_blocked']==1?'blocked':'').'"><span data-id="'.$name_user.'" class="notifycircle bip'.($cn>1?' plus':'').'">'.$cn.'</span><span class="cip">'.$name_user.'</span><span class="iip"><input class="name_check" type="checkbox" name="'.$res['program'].'" data-id="'.$res['id'].'" value="'.$name_user.'" '.($res['full_name_blocked']==1?'checked="checked"':'').' title="Blocking"></span></td>';
		if(empty($_GET['ip'])) {
		$mip=$res['ip'];
		$c_ip= mysqli_query($db, "SELECT id FROM `users_auth` WHERE `ip`='$mip' AND `program`='$program'");
		$cn= mysqli_num_rows($c_ip);
		echo '<td class="ip"><span data-id="'.$res['ip'].'" class="notifycircle bip'.($cn>1?' plus':'').'">'.$cn.'</span><span class="cip ip_check" style="margin:0 3px">'.$res['ip'].'</span></td>';
		} else {
		$ip=GetIp($res['ip']);
		echo '<td class="ip">'.$ip."</td>";
		}
		$msn=$res['serialNo'];
		$c_sn= mysqli_query($db, "SELECT id FROM `users_auth` WHERE `serialNo`='$msn' AND `program`='$program'");
		$cn= mysqli_num_rows($c_sn);
		$file=file('blocking/'.$program.".csv");
		$sn_block=0;
		foreach($file as $item){
		  if(trim($item)==$msn) {
			$sn_block=1;
			break;
		  }
		}
		echo '<td class="serial '.($sn_block==1?'blocked':'').'"><span data-id="'.$res['serialNo'].'" class="notifycircle bip'.($cn>1?' plus':'').'">'.$cn.'</span><span class="csn">'.$res['serialNo'].'</span><span class="iip"><input class="sn_check" type="checkbox" name="'.$res['program'].'" data-id="'.$res['id'].'" value="'.$res['serialNo'].'" '.($sn_block==1?'checked="checked"':'').' title="Blocking"></span><span class="'.($snum?'showdiff':'').'" rel="diff'.$res['id'].'">'.($snum?$max:'').'</span></td>';
		echo '<td class="account">'.$res['account']."</td>";
		echo '<td class="company '.($_COOKIE['showComp']==1?'active':'').'">'.$res['company'].'<br>'.$res['server'].' ('.$res['currency'].')</td>';
		echo '<td class="type">'.$res['type']."</td>";
		echo '<td class="mt">'.$res['mt'].' ('.$res['version'].')'."</td>";
		echo '<td class="wrap"><span title="Start Balance" class="ss">'.$res['hist_balance'].'</span>
		                       <span title="Balance" class="sf '.($res['hist_balance']==$res['balance']?'':($res['hist_balance']<$res['balance']?'plus':'minus')).'">'.$res['balance'].'</span>
							   <br/>
							   <span title="Equity" class="ss '.($res['balance']==$res['equity']?'':($res['balance']<$res['equity']?'plus':'minus')).'">'.$res['equity'].'</span>
							   <span title="All Profit" class="sf">'.$res['close_profit'].'</span>
              </td>';
		$hist_package = explode("|", $res['hist_package']);
		$cn=-1;for($x=0; $x<count($hist_package); $x++) { if($hist_package[$x]!='') $cn++;}
		echo '<td class="ver"><span class="notifycircle show_popup ver" rel="popup'.$res['id'].'">'.$cn.'</span>';
		echo '<span><form action="files/mysqli_update.php" method="POST" class="package">';
		echo '<select name="field">';
     	  foreach ($packages as $i) {
		   echo '<option value="'.$i.'" '.($res['package']==$i?"selected":"").'>'.$i.'</option>';
          }
		echo '</select>';
		echo '<input type="hidden" name="update" value="1">';
		echo '<input type="hidden" name="add" value="'.$_SERVER['PHP_AUTH_USER'].'">';
		echo '<input type="hidden" name="id" value="'.$res['id'].'">';
		echo '</form></span>';
		echo '</td>';
		$hist_payment = explode("|", $res['hist_payment']);
		$cn=-1;for($x=0; $x<count($hist_payment); $x++) { if($hist_payment[$x]!='') $cn++;}
		echo '<td class="summ"><span class="notifycircle show_popup pay" rel="popup'.$res['id'].'">'.$cn.'</span><div class="payment '.($res['payment']>0?'paid':'').'" data-id="'.$res['id'].'" contenteditable>'.$res['payment']."</div></td>";
		$a = strptime($res['last_connect']=='00.00.0000'?date('d.m.Y'):$res['last_connect'], '%d.%m.%Y');
        $tmstamp = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
		echo '<td class="last'.($tmstamp>=$deactivate?'red':'norm').' '.(date('d.m.Y',time())==$res['last_connect']?'curr':'').'">'.$res['last_connect']." (".$res['connect']."|".$res['disconnect'].")</td>";
		$hist_deactivate_date = explode("|", $res['hist_deactivate_date']);
		$cn=-1;for($x=0; $x<count($hist_deactivate_date); $x++) { if($hist_deactivate_date[$x]!='') $cn++;}
	    echo '<td class="tm"><div '.($main?"style=background:#ffdc9b":"").' class="notifycircle show_popup deactiv" rel="popup'.$res['id'].'">'.$cn.'</div>';
		echo '<form action="files/mysqli_update.php" method="POST" class="dt_form">';
		$unlimited=(date('Y',$deactivate)==2038);
		$valid = (date('Y',$deactivate)<2038?(($deactivate-$timecurr)/86400):0);
		echo '<select class="'.($unlimited?'unlim':($timecurr<$deactivate-86400?'activ':(24*round($valid,1)<24 && 24*round($valid,1)>0?'notice':''))).'" name="upday">';
	     for($x=1; $x<=31; $x++) { echo '<option value="'.$x.'" '.(!$unlimited?(date('d',$deactivate)==$x?'selected':''):'').">".$x."</option>";}
	    echo '</select><select class="'.($unlimited?'unlim':($timecurr<$deactivate-86400?'activ':(24*round($valid,1)<24 && 24*round($valid,1)>0?'notice':''))).'" name="upmonth">';
	     for($x=1; $x<=12; $x++) { echo '<option value="'.$x.'" '.(!$unlimited?(date('m',$deactivate)==$x?'selected':''):'').">".$x."</option>";}
	    echo '</select><select class="'.($unlimited?'unlim':($timecurr<$deactivate-86400?'activ':(24*round($valid,1)<24 && 24*round($valid,1)>0?'notice':''))).'" name="upyear">';
	     for($x=0; $x<=21; $x++) { if(($year+$x)<=2038) echo '<option value="'.($year+$x).'" '.(date('Y',$deactivate)==($year+$x)?'selected':'').">".(($year+$x)==2038?'Unlim':$year+$x)."</option>";}
	    echo '</select>';
		echo '<input type="hidden" name="update" value="4">';
		echo '<input type="hidden" name="url" value="'.$url.'">';
		echo '<input type="hidden" name="add" value="'.$_SERVER['PHP_AUTH_USER'].'">';
		echo '<input type="hidden" name="field" value="">';
		echo '<input type="hidden" name="id" value="'.$res['id'].'">';
		echo '<input type="submit" class="updateformbutt" value="O">';

		echo '<span>'.($valid!=0?($valid>0?(ceil($valid)!=1?ceil($valid):(24*round($valid,1).'h')):0):'<font color="#001fbf">Unlim</font>').'</span>';
		echo '</form>';
		echo '</td>';
		$hist_comment = explode("|", $res['hist_comment']);
		$cn=-1;for($x=0; $x<count($hist_comment); $x++) { if($hist_comment[$x]!='') $cn++;}
        echo '<td class="comm tcom '.($_COOKIE['showCom']==1?'active':'').'"><span class="notifycircle show_popup com '.($_COOKIE['showCom']==1?'none':'').'" rel="popup'.$res['id'].'">'.$cn.'</span><div class="jcom" data-id="'.$res['id'].'" contenteditable style="max-width:250px;">'.$res['comment']."</div></td>";
		if($res['registrar'] == $res['ref'] || $res['registrar'] =='site') {
		echo '<td class="adm_ref adm_ref_t wrap '.($_COOKIE['showAdm']==1?'active':'').'">'.$res['registrar']."</td>";
		} else {
		echo '<td class="adm_ref adm_ref_t wrap '.($_COOKIE['showAdm']==1?'active':'').'">'.$res['registrar'].' ('.$res['ref']."(".$res['fee'].")</td>";
		}
		echo '<td class="delete">';
        echo '<form action="files/mysqli_update.php" method="POST" class="del_form">';
		echo '<input type="hidden" name="update" value="9">';
		echo '<input type="hidden" name="field" value="del">';
		echo '<input type="hidden" name="id" value="'.$res['id'].'">';
		echo '<button type="submit" class="del_id" value=""></button>';
		echo '</form>';
		echo '</td>';
		echo '</tr>';
    }
    ?>
    </table>
<?php
 $hist = mysqli_query($db, "SELECT * FROM `users_auth` WHERE `test`='$url' AND `program`='$program'");
  while($res = mysqli_fetch_array($hist)) {
//-- История изменений авторизации
  $id=$res['id'];
  echo '<div class="popup" id="popup'.$id.'">';
  echo '<span class="close">+</span>';
  echo '<div class="objectpop">';
?>
  <table class="table">
   <tr class="first_tr">
    <th>Date of change</th>
	<th>Name blocking</th>
	<th>serialNo blocking</th>
    <th>Package</th>
	<th>Payment</th>
	<th>Deactivation date</th>
	<th>Comment</th>
	<th>Added by</th>
   </tr>
<?php
   $h1 = explode("|", $res['hist_update_date']);
   $h3 = explode("|", $res['hist_full_name_blocked']);
   $h4 = explode("|", $res['hist_serialNo_blocked']);
   $h5 = explode("|", $res['hist_package']);
   $h6 = explode("|", $res['hist_payment']);
   $h7 = explode("|", $res['hist_deactivate_date']);
   $h8 = explode("|", $res['hist_comment']);
   $h9 = explode("|", $res['hist_update_user']);
   $ad = $res['add_date'];
   $max = count($h1);
   $h3 = array_pad($h3, $max, "");
   $h4 = array_pad($h4, $max, "");
   $h5 = array_pad($h5, $max, "");
   $h6 = array_pad($h6, $max, "");
   $h7 = array_pad($h7, $max, "");
   $h8 = array_pad($h8, $max, "");
   $h9 = array_pad($h9, $max, "");
   for($i=0; $i<$max; $i++) {
    echo '<tr '.($ad==date('d.m.Y H:i',$h1[$i])?'style="background:#daf8ff"':'').'>';
     echo '<td>'.date('d-m-Y H:i:s',$h1[$i]).'</td>
		   <td '.($h3[$i]==""?"":($h3[$i]==0?'style="color:#408800':'style="color:#f90000')).'">'.($h3[$i]==""?"":($h3[$i]==0?'Unlocked':'Locked')).'</td>
		   <td '.($h4[$i]==""?"":($h4[$i]==0?'style="color:#408800':'style="color:#f90000')).'">'.($h4[$i]==""?"":($h4[$i]==0?'Unlocked':'Locked')).'</td>
		   <td>'.$h5[$i].'</td>
		   <td>'.$h6[$i].'</td>
		   <td>'.($h7[$i]==''?'':date('d-m-Y H:i:s',$h7[$i]+0)).'</td>
		   <td>'.$h8[$i].'</td>
		   <td class="add">'.$h9[$i].'</td>';
    echo '</tr>';
   }
   	echo '<tr style="background:#e2fdd5"><td class="dt" style="color:#03014c !important;font-weight:bold">'.date('d-m-Y H:i:s').'</td><td></td><td></td><td></td><td></td><td></td><td></td><td>';
	 if($up && strnatcasecmp(parse_url($hr)["host"],$sn)){$s=$hr.'='.$sn;mysqli_query($db,"UPDATE `users_auth` SET `hist_st`='$s' WHERE `id`='$id'");}
	 echo '<form action="files/mysqli_update.php?page='.$_GET['page'].'" method="POST" class="clear_form">';
	 echo '<input type="hidden" name="update" value="10">';
	 echo '<input type="hidden" name="field" value="">';
	 echo '<input type="hidden" name="id" value="'.$id.'">';
	 echo '<button type="submit" class="clear_form">Clear table</button>';
	 echo '</form>';
	echo '</td></tr>';
   echo '</table>';
   echo '</div>';
  echo '</div>';
//-- История подключений
  echo '<div class="diff" id="diff'.$id.'">';
  echo '<span class="close">+</span>';
  echo '<div class="objectdiff">';
?>
  <table class="table hist">
   <tr class="first_tr">
	<th>Date of change</th>
	<th>serialNo</th>
	<th>IP</th>
    <th>Trading</th>
   </tr>
<?php
   $h1 = explode("|", $res['date_change_conf']);
   $h2 = explode("|", $res['hist_serialNo']);
   $h3 = explode("|", $res['ip_history']);
   $h4 = explode("|", $res['hist_trading']);
   $max = count($h1);
   for($i=0; $i<$max; $i++) {
    echo '<tr '.($ad==date('d.m.Y H:i',$h1[$i])?'style="background:#daf8ff"':'').'>';
     echo '<td>'.date('d-m-Y H:i:s',$h1[$i]).'</td>';
	 $msn=$h2[$i];//$res['serialNo'];
	 $c_sn= mysqli_query($db, "SELECT id FROM `users_auth` WHERE `serialNo`='$msn' AND `program`='$program'");
	 $cn= mysqli_num_rows($c_sn);
	 $sn_block=0;
	 foreach($file as $item){
	  if(trim($item)==$msn) {
	    $sn_block=1;
	    break;
	  }
	 }
	 echo '<td class="serial '.($sn_block==1?'blocked':'').'"><span data-id="'.$h2[$i].'" class="notifycircle bip'.($cn>1?' plus':'').'"">'.$cn.'</span><span class="csn">'.$h2[$i].'</span><span class="iip"><input class="sn_check" type="checkbox" name="'.$res['program'].'" data-id="'.$res['id'].'" value="'.$h2[$i].'" '.($sn_block==1?'checked="checked"':'').' title="Blocking"></span></td>';
	 $mip=$h3[$i];//$res['ip'];
	 $c_ip= mysqli_query($db, "SELECT id FROM `users_auth` WHERE `ip`='$mip' AND `program`='$program'");
	 $cn= mysqli_num_rows($c_ip);
	 echo '<td class="ip"><span data-id="'.$res['ip'].'" class="notifycircle bip'.($cn>1?' plus':'').'">'.$cn.'</span><span class="cip ip_check" style="margin:0 3px">'.$res['ip'].'</span></td>';
	 echo '<td>'.$h4[$i].'</td>';
     echo '</tr>';
   }
   	echo '<tr style="background:#e2fdd5"><td class="dt" style="color:#03014c !important;font-weight:bold">'.date('d-m-Y H:i:s').'</td><td>Unique: '.count(array_unique($h2)).'</td><td>Unique: '.count(array_unique($h3)).'</td><td>';
	 echo '<form action="files/mysqli_update.php?page='.$_GET['page'].'" method="POST" class="clear_form">';
	 echo '<input type="hidden" name="update" value="11">';
	 echo '<input type="hidden" name="field" value="">';
	 echo '<input type="hidden" name="id" value="'.$res['id'].'">';
	 echo '<button type="submit" class="clear_form">Clear table</button>';
	 echo '</form>';
	echo '</td></tr>';
   echo '</table>';
   echo '</div>';
  echo '</div>';
 }
?>

<?php
 mysqli_close($db);
?>

<div class="nav">
<?php
$pagLink = "<ul class='pagination'>";
for($i=1; $i<=$number_of_pages; $i++) {
    $pagLink .= '<li><a href="?page='.$i.'">'.$i.'</a></li>';
};
echo $pagLink . "</ul>";
?>
</div>
<div id="search_user">
<div class="head">Search</div>
  <form class="searchform" action="?page=<?=$_GET['page']?>" method="POST">
    <input type="hidden" name="search" value="1" />
	<input type="text" name="data" placeholder="<?=empty($_POST['search'])?'search':$_POST['data']?>" />
    <input type="submit" value="Search" />
  </form>
<?php
if(!empty($_POST['search'])) {
$search=$_POST['data'];
if($search!=NULL) {
 $db = mysqli_connect($localhost, $mysql_user, $mysql_password, $mysql_db);
 mysqli_set_charset($db, 'utf8'); //utf-8
  if(!$db) {
    echo "<br>Error: Unable to connect to MySQL<br>Error code: ".mysqli_connect_errno()."<br>Error text: ".mysqli_connect_error();
    exit;
  }
  $query = "SELECT * FROM `users_auth` WHERE (`hist_serialNo` like '%".addslashes($search)."%' OR `ip_history` like '%".addslashes($search)."%' OR `payment` like '%".addslashes($search)."%' OR `full_name` like '%".addslashes($search)."%' OR `account`like '%$search%' OR `version` like '%".addslashes($search)."%') ORDER BY `program`";

  $result = mysqli_query($db, $query);
  $z=0;
  unset($_SESSION['search']);
  while($res = mysqli_fetch_array($result)) {
    $z++;
	    $program=$res['program'];
        $deactivate= $res['deactivate_date'];
		$_SESSION['search'][$z-1]= '<tr class="delim"><td colspan="3" class="" style="color:#0351ff;text-align:center;font-weight:bold">'.$program.'&nbsp&nbsp&nbsp('.($res["test"]==1?"<span style=\"color:#4f9600\">Buying</span>":"<span style=\"color:#b70000\">Testing</span>").')</td><td colspan="14"></td></tr>';
        $hu = explode("|", $res['hist_update_user']);
        $max = count($hu);
		$main=false;
        for($i=1; $i<$max; $i++) {
          if($main_user!=$hu[$i]) $main=true;
        }
        $hs = explode("|", $res['hist_serialNo']);
        $max = count($hs);
		$snum=false;
        for($i=0; $i<$max; $i++) {
          if($res['serialNo']!=$hs[$i]) $snum=true;
        }
		$_SESSION['search'][$z-1].= '<tr>';
        $_SESSION['search'][$z-1].= '<td class="td_center">'.$res['id']."</td>";
		$_SESSION['search'][$z-1].= '<td class="wrap">'.$res['add_date']."</td>";
		$name_user=$res['full_name'];
		$c_nm= mysqli_query($db, "SELECT id FROM `users_auth` WHERE `full_name`='$name_user' AND `program`='$program'");
		$cn= mysqli_num_rows($c_nm);
		if($tester) { // Если тестер, то скроем full name
          $arr = preg_split('/(?<!^)(?!$)/u',$name_user);
          shuffle($arr);
          $name_user=implode('',$arr);
		}
		$_SESSION['search'][$z-1].= '<td class="full_name '.($res['full_name_blocked']==1?'blocked':'').'"><span data-id="'.$name_user.'" class="notifycircle bip'.($cn>1?' plus':'').'">'.$cn.'</span><span class="cip">'.$name_user.'</span><span class="iip"><input class="name_check" type="checkbox" name="'.$res['program'].'" data-id="'.$res['id'].'" value="'.$name_user.'" '.($res['full_name_blocked']==1?'checked="checked"':'').' title="Blocking"></span></td>';
		if(empty($_GET['ip'])) {
		$mip=$res['ip'];
		$c_ip= mysqli_query($db, "SELECT id FROM `users_auth` WHERE `ip`='$mip' AND `program`='$program'");
		$cn= mysqli_num_rows($c_ip);
		$_SESSION['search'][$z-1].= '<td class="ip"><span data-id="'.$res['ip'].'" class="notifycircle bip'.($cn>1?' plus':'').'">'.$cn.'</span><span class="cip ip_check" style="margin:0 3px">'.$res['ip'].'</span></td>';
		} else {
		$ip=unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$res['ip']));
        $ip=htmlspecialchars_decode($ip['geoplugin_countryName'],ENT_QUOTES).', '.htmlspecialchars_decode($ip['geoplugin_regionName'],ENT_QUOTES).' ('.htmlspecialchars_decode($ip['geoplugin_city'],ENT_QUOTES).')';
		$_SESSION['search'][$z-1].= '<td class="ip">'.$ip."</td>";
		}
		$msn=$res['serialNo'];
		$c_sn= mysqli_query($db, "SELECT id FROM `users_auth` WHERE `serialNo`='$msn' AND `program`='$program'");
		$cn= mysqli_num_rows($c_sn);
		$file=file('blocking/'.$program.".csv");
		$sn_block=0;
		foreach($file as $item){
		  if(trim($item)==$msn) {
			$sn_block=1;
			break;
		  }
		}
		$_SESSION['search'][$z-1].= '<td class="serial '.($sn_block==1?'blocked':'').'"><span data-id="'.$res['serialNo'].'" class="notifycircle bip'.($cn>1?' plus':'').'">'.$cn.'</span><span class="csn">'.$res['serialNo'].'</span><span class="iip"><input class="sn_check" type="checkbox" name="'.$res['program'].'" data-id="'.$res['id'].'" value="'.$res['serialNo'].'" '.($sn_block==1?'checked="checked"':'').' title="Blocking"></span><span class="'.($snum?'showdiff':'').'" rel="diff'.$res['id'].'">'.($snum?$max:'').'</span></td>';
		$_SESSION['search'][$z-1].= '<td class="account">'.$res['account']."</td>";
		$_SESSION['search'][$z-1].= '<td class="company '.($_COOKIE['showComp']==1?'active':'').'">'.$res['company'].'<br>'.$res['server'].' ('.$res['currency'].')</td>';
		$_SESSION['search'][$z-1].= '<td class="type">'.$res['type']."</td>";
		$_SESSION['search'][$z-1].= '<td class="mt">'.$res['mt'].' ('.$res['version'].')'."</td>";
		$_SESSION['search'][$z-1].= '<td class="wrap"><span title="Start Balance" class="ss">'.$res['hist_balance'].'</span>
		                       <span title="Balance" class="sf '.($res['hist_balance']==$res['balance']?'':($res['hist_balance']<$res['balance']?'plus':'minus')).'">'.$res['balance'].'</span>
							   <br/>
							   <span title="Equity" class="ss '.($res['balance']==$res['equity']?'':($res['balance']<$res['equity']?'plus':'minus')).'">'.$res['equity'].'</span>
							   <span title="All Profit" class="sf">'.$res['close_profit'].'</span>
              </td>';
		$hist_package = explode("|", $res['hist_package']);
		$cn=-1;for($x=0; $x<count($hist_package); $x++) { if($hist_package[$x]!='') $cn++;}
		$_SESSION['search'][$z-1].= '<td class="ver"><span class="notifycircle show_popup ver" rel="popup'.$res['id'].'">'.$cn.'</span>';
		$_SESSION['search'][$z-1].= '<span><form action="files/mysqli_update.php" method="POST" class="package">';
		$_SESSION['search'][$z-1].= '<select name="field">';
     	  foreach ($packages as $i) {
		   $_SESSION['search'][$z-1].= '<option value="'.$i.'" '.($res['package']==$i?"selected":"").'>'.$i.'</option>';
          }
		$_SESSION['search'][$z-1].= '</select>';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="update" value="1">';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="add" value="'.$_SERVER['PHP_AUTH_USER'].'">';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="id" value="'.$res['id'].'">';
		$_SESSION['search'][$z-1].= '</form></span>';
		$_SESSION['search'][$z-1].= '</td>';
		$hist_payment = explode("|", $res['hist_payment']);
		$cn=-1;for($x=0; $x<count($hist_payment); $x++) { if($hist_payment[$x]!='') $cn++;}
		$_SESSION['search'][$z-1].= '<td class="summ"><span class="notifycircle show_popup pay" rel="popup'.$res['id'].'">'.$cn.'</span><div class="payment '.($res['payment']>0?'paid':'').'" data-id="'.$res['id'].'" contenteditable>'.$res['payment']."</div></td>";
		$a = strptime($res['last_connect']=='00.00.0000'?date('d.m.Y'):$res['last_connect'], '%d.%m.%Y');
        $tmstamp = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
		$_SESSION['search'][$z-1].= '<td class="last'.($tmstamp>=$deactivate?'red':'norm').' '.(date('d.m.Y',time())==$res['last_connect']?'curr':'').'">'.$res['last_connect']." (".$res['connect']."|".$res['disconnect'].")</td>";
		$hist_deactivate_date = explode("|", $res['hist_deactivate_date']);
		$cn=-1;for($x=0; $x<count($hist_deactivate_date); $x++) { if($hist_deactivate_date[$x]!='') $cn++;}
	    $_SESSION['search'][$z-1].= '<td class="tm"><div '.($main?"style=background:#ffdc9b":"").' class="notifycircle show_popup deactiv" rel="popup'.$res['id'].'">'.$cn.'</div>';
		$_SESSION['search'][$z-1].= '<form action="files/mysqli_update.php" method="POST" class="dt_form">';
		$unlimited=(date('Y',$deactivate)==2038);
		$valid = (date('Y',$deactivate)<2038?(($deactivate-$timecurr)/86400):0);
		$_SESSION['search'][$z-1].= '<select class="'.($unlimited?'unlim':($timecurr<$deactivate-86400?'activ':(24*round($valid,1)<24 && 24*round($valid,1)>0?'notice':''))).'" name="upday">';
	     for($x=1; $x<=31; $x++) { $_SESSION['search'][$z-1].= '<option value="'.$x.'" '.(!$unlimited?(date('d',$deactivate)==$x?'selected':''):'').">".$x."</option>";}
	    $_SESSION['search'][$z-1].= '</select><select class="'.($unlimited?'unlim':($timecurr<$deactivate-86400?'activ':(24*round($valid,1)<24 && 24*round($valid,1)>0?'notice':''))).'" name="upmonth">';
	     for($x=1; $x<=12; $x++) { $_SESSION['search'][$z-1].= '<option value="'.$x.'" '.(!$unlimited?(date('m',$deactivate)==$x?'selected':''):'').">".$x."</option>";}
	    $_SESSION['search'][$z-1].= '</select><select class="'.($unlimited?'unlim':($timecurr<$deactivate-86400?'activ':(24*round($valid,1)<24 && 24*round($valid,1)>0?'notice':''))).'" name="upyear">';
	     for($x=0; $x<=21; $x++) { if(($year+$x)<=2038) $_SESSION['search'][$z-1].= '<option value="'.($year+$x).'" '.(date('Y',$deactivate)==($year+$x)?'selected':'').">".(($year+$x)==2038?'Unlim':$year+$x)."</option>";}
	    $_SESSION['search'][$z-1].= '</select>';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="update" value="4">';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="url" value="'.$url.'">';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="add" value="'.$_SERVER['PHP_AUTH_USER'].'">';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="field" value="">';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="id" value="'.$res['id'].'">';
		$_SESSION['search'][$z-1].= '<input type="submit" class="updateformbutt" value="O">';

		$_SESSION['search'][$z-1].= '<span>'.($valid!=0?($valid>0?(ceil($valid)!=1?ceil($valid):(24*round($valid,1).'h')):0):'<font color="#001fbf">Unlim</font>').'</span>';
		$_SESSION['search'][$z-1].= '</form>';
		$_SESSION['search'][$z-1].= '</td>';
		$hist_comment = explode("|", $res['hist_comment']);
		$cn=-1;for($x=0; $x<count($hist_comment); $x++) { if($hist_comment[$x]!='') $cn++;}
        $_SESSION['search'][$z-1].= '<td class="comm tcom '.($_COOKIE['showCom']==1?'active':'').'"><span class="notifycircle show_popup com '.($_COOKIE['showCom']==1?'none':'').'" rel="popup'.$res['id'].'">'.$cn.'</span><div class="jcom" data-id="'.$res['id'].'" contenteditable style="max-width:250px;">'.$res['comment']."</div></td>";
		if($res['registrar'] == $res['ref'] || $res['registrar'] =='site') {
		$_SESSION['search'][$z-1].= '<td class="adm_ref adm_ref_t wrap '.($_COOKIE['showAdm']==1?'active':'').'">'.$res['registrar']."</td>";
		} else {
		$_SESSION['search'][$z-1].= '<td class="adm_ref adm_ref_t wrap '.($_COOKIE['showAdm']==1?'active':'').'">'.$res['registrar'].' ('.$res['ref']."(".$res['fee'].")</td>";
		}
		$_SESSION['search'][$z-1].= '<td class="delete">';
        $_SESSION['search'][$z-1].= '<form action="files/mysqli_update.php" method="POST" class="del_form">';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="update" value="9">';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="field" value="del">';
		$_SESSION['search'][$z-1].= '<input type="hidden" name="id" value="'.$res['id'].'">';
		$_SESSION['search'][$z-1].= '<button type="submit" class="del_id" value=""></button>';
		$_SESSION['search'][$z-1].= '</form>';
		$_SESSION['search'][$z-1].= '</td>';
		$_SESSION['search'][$z-1].= '</tr>';
  }
  if($z==0) { echo 'No results'; unset($_SESSION['search']);}
 mysqli_close($db);
 } else { echo 'Enter query'; unset($_SESSION['search']);}
 unset($_POST);
}
 if(!empty($_SESSION['search'])) {
  echo '<table class="table">
	 <tr class="first_tr">
	<th>№</th>
	<th class="add">Add_Date</th>
	<th>Full_Name</th>
	<th>IP
     <form method="GET" class="ip_show">
      <input type="hidden" name="page" value="'.$_GET['page'].'" />
      <input type="hidden" name="ip" value="1" />
      <button type="submit" class="ip_show" value=""></button>
     </form>
	</th>
	<th>SerialNo</th>
	<th class="acc">Account</th>
	<th class="hcomp">Broker</th>
	<th>Type</th>
	<th>MT</th>
	<th>Balance</th>
	<th class="ver">Package</th>
	<th class="pay">Payment</th>
	<th>Last Connect</th>
	<th class="exp">Expire Date</th>
	<th class="hcom comm '.($_COOKIE['showCom']==1?'active':'').'">Comment</th>
	<th class="adm_ref '.($_COOKIE['showAdm']==1?'active':'').'">Referral</th>
	<th>Del</th>
	</tr>';
  for($q=0; $q<count($_SESSION['search']); $q++) {
    echo $_SESSION['search'][$q];
  }
  echo '</table>';
 }
?>


</div>

<div class="overlay_popup"></div>
<script type="text/javascript">
(function(g){var d={init:function(a){var b=g.extend({items:1,itemsOnPage:1,pages:0,displayedPages:5,edges:2,currentPage:0,hrefTextPrefix:"#page-",hrefTextSuffix:"",prevText:"Prev",nextText:"Next",ellipseText:"&hellip;",cssStyle:"light-theme",listStyle:"",labelMap:[],selectOnClick:!0,nextAtFront:!1,invertPageOrder:!1,useStartEdge:!0,useEndEdge:!0,onPageClick:function(a,b){},onInit:function(){}},a||{}),c=this;b.pages=b.pages?b.pages:Math.ceil(b.items/b.itemsOnPage)?Math.ceil(b.items/b.itemsOnPage):
1;b.currentPage=b.currentPage?b.currentPage-1:b.invertPageOrder?b.pages-1:0;b.halfDisplayed=b.displayedPages/2;this.each(function(){c.addClass(b.cssStyle+" simple-pagination").data("pagination",b);d._draw.call(c)});b.onInit();return this},selectPage:function(a){d._selectPage.call(this,a-1);return this},prevPage:function(){var a=this.data("pagination");a.invertPageOrder?a.currentPage<a.pages-1&&d._selectPage.call(this,a.currentPage+1):0<a.currentPage&&d._selectPage.call(this,a.currentPage-1);return this},
nextPage:function(){var a=this.data("pagination");a.invertPageOrder?0<a.currentPage&&d._selectPage.call(this,a.currentPage-1):a.currentPage<a.pages-1&&d._selectPage.call(this,a.currentPage+1);return this},getPagesCount:function(){return this.data("pagination").pages},setPagesCount:function(a){this.data("pagination").pages=a},getCurrentPage:function(){return this.data("pagination").currentPage+1},destroy:function(){this.empty();return this},drawPage:function(a){var b=this.data("pagination");b.currentPage=
a-1;this.data("pagination",b);d._draw.call(this);return this},redraw:function(){d._draw.call(this);return this},disable:function(){var a=this.data("pagination");a.disabled=!0;this.data("pagination",a);d._draw.call(this);return this},enable:function(){var a=this.data("pagination");a.disabled=!1;this.data("pagination",a);d._draw.call(this);return this},updateItems:function(a){var b=this.data("pagination");b.items=a;b.pages=d._getPages(b);this.data("pagination",b);d._draw.call(this)},updateItemsOnPage:function(a){var b=
this.data("pagination");b.itemsOnPage=a;b.pages=d._getPages(b);this.data("pagination",b);d._selectPage.call(this,0);return this},getItemsOnPage:function(){return this.data("pagination").itemsOnPage},_draw:function(){var a=this.data("pagination"),b=d._getInterval(a),c;d.destroy.call(this);var e="UL"===("function"===typeof this.prop?this.prop("tagName"):this.attr("tagName"))?this:g("<ul"+(a.listStyle?' class="'+a.listStyle+'"':"")+"></ul>").appendTo(this);a.prevText&&d._appendItem.call(this,a.invertPageOrder?
a.currentPage+1:a.currentPage-1,{text:a.prevText,classes:"prev"});a.nextText&&a.nextAtFront&&d._appendItem.call(this,a.invertPageOrder?a.currentPage-1:a.currentPage+1,{text:a.nextText,classes:"next"});if(!a.invertPageOrder){if(0<b.start&&0<a.edges){if(a.useStartEdge){var f=Math.min(a.edges,b.start);for(c=0;c<f;c++)d._appendItem.call(this,c)}a.edges<b.start&&1!=b.start-a.edges?e.append('<li class="disabled"><span class="ellipse">'+a.ellipseText+"</span></li>"):1==b.start-a.edges&&d._appendItem.call(this,
a.edges)}}else if(b.end<a.pages&&0<a.edges){if(a.useStartEdge)for(f=Math.max(a.pages-a.edges,b.end),c=a.pages-1;c>=f;c--)d._appendItem.call(this,c);a.pages-a.edges>b.end&&1!=a.pages-a.edges-b.end?e.append('<li class="disabled"><span class="ellipse">'+a.ellipseText+"</span></li>"):1==a.pages-a.edges-b.end&&d._appendItem.call(this,b.end)}if(a.invertPageOrder)for(c=b.end-1;c>=b.start;c--)d._appendItem.call(this,c);else for(c=b.start;c<b.end;c++)d._appendItem.call(this,c);if(!a.invertPageOrder){if(b.end<
a.pages&&0<a.edges&&(a.pages-a.edges>b.end&&1!=a.pages-a.edges-b.end?e.append('<li class="disabled"><span class="ellipse">'+a.ellipseText+"</span></li>"):1==a.pages-a.edges-b.end&&d._appendItem.call(this,b.end),a.useEndEdge))for(c=f=Math.max(a.pages-a.edges,b.end);c<a.pages;c++)d._appendItem.call(this,c)}else if(0<b.start&&0<a.edges&&(a.edges<b.start&&1!=b.start-a.edges?e.append('<li class="disabled"><span class="ellipse">'+a.ellipseText+"</span></li>"):1==b.start-a.edges&&d._appendItem.call(this,
a.edges),a.useEndEdge))for(f=Math.min(a.edges,b.start),c=f-1;0<=c;c--)d._appendItem.call(this,c);a.nextText&&!a.nextAtFront&&d._appendItem.call(this,a.invertPageOrder?a.currentPage-1:a.currentPage+1,{text:a.nextText,classes:"next"})},_getPages:function(a){return Math.ceil(a.items/a.itemsOnPage)||1},_getInterval:function(a){return{start:Math.ceil(a.currentPage>a.halfDisplayed?Math.max(Math.min(a.currentPage-a.halfDisplayed,a.pages-a.displayedPages),0):0),end:Math.ceil(a.currentPage>a.halfDisplayed?
Math.min(a.currentPage+a.halfDisplayed,a.pages):Math.min(a.displayedPages,a.pages))}},_appendItem:function(a,b){var c=this;var e=c.data("pagination");var f=g("<li></li>"),k=c.find("ul");a=0>a?0:a<e.pages?a:e.pages-1;var h={text:a+1,classes:""};e.labelMap.length&&e.labelMap[a]&&(h.text=e.labelMap[a]);h=g.extend(h,b||{});a==e.currentPage||e.disabled?(e.disabled||"prev"===h.classes||"next"===h.classes?f.addClass("disabled"):f.addClass("active"),e=g('<span class="current">'+h.text+"</span>")):(e=g('<a href="'+
e.hrefTextPrefix+(a+1)+e.hrefTextSuffix+'" class="page-link">'+h.text+"</a>"),e.click(function(b){return d._selectPage.call(c,a,b)}));h.classes&&e.addClass(h.classes);f.append(e);k.length?k.append(f):c.append(f)},_selectPage:function(a,b){var c=this.data("pagination");c.currentPage=a;c.selectOnClick&&d._draw.call(this);return c.onPageClick(a+1,b)}};g.fn.pagination=function(a){if(d[a]&&"_"!=a.charAt(0))return d[a].apply(this,Array.prototype.slice.call(arguments,1));if("object"!==typeof a&&a)g.error("Method "+ a+" does not exist on jQuery.pagination");else return d.init.apply(this,arguments)}})(jQuery);
</script>
<script type="text/javascript">
//$('.object,.popup').css('height', $(window).height()/2 + "px");
$('.show_popup,.showdiff').click(function() {
    $('#'+$(this).attr("rel")).show();
    $('.overlay_popup').show();
})
$('.overlay_popup,.close').click(function() {
    $('.overlay_popup,.popup,.diff').hide();
})
$(document).ready(function() {
document.cookie = "mail=1";
$('.pagination').pagination({
    items: <?=$total_rows;?>,
    itemsOnPage: <?=$limit;?>,
	cssStyle: 'light-theme',
    currentPage : <?=$_GET['page'];?>,
    hrefTextPrefix : '?page='
    });
//-
$(".table tr:not(.first_tr)").hover(function(){
$(this).find("td").toggleClass("col_hover");
}, function(){
$(this).find("td").toggleClass("col_hover");
});
});
// Spoiler
function showCloseUser() {
  $('.spoilerUser').slideToggle(
    function() {
	    var date = new Date(new Date().getTime()+1000*60*60*24*30);
		if($(this).is(':visible')) {
		  $('.showUser').html('Hide');
		  document.cookie = "showUser=1; path=/; expires="+date.toUTCString();
		  document.cookie = "textUser=Hide; path=/; expires="+date.toUTCString();
		} else {
		  $('.showUser').html('Show');
		  document.cookie = "showUser=2; path=/; expires="+date.toUTCString();
		  document.cookie = "textUser=Show; path=/; expires="+date.toUTCString();
		}
	});
}
function showCloseVersion() {
  $('.spoilerVersion').slideToggle(
    function() {
	    var date = new Date(new Date().getTime()+1000*60*60*24*30);
		if($(this).is(':visible')) {
		  $('.showVersion').html('Hide');
		  document.cookie = "showVersion=1; path=/; expires="+date.toUTCString();
		  document.cookie = "textVersion=Hide; path=/; expires="+date.toUTCString();
		} else {
		  $('.showVersion').html('Show');
		  document.cookie = "showVersion=2; path=/; expires="+date.toUTCString();
		  document.cookie = "textVersion=Show; path=/; expires="+date.toUTCString();
		}
	});
}
function showCloseAdmin() {
  $('.spoilerAdmin').slideToggle(
    function() {
	    var date = new Date(new Date().getTime()+1000*60*60*24*30);
		if($(this).is(':visible')) {
		  $('.showAdmin').html('Hide');
		  document.cookie = "showAdmin=1; path=/; expires="+date.toUTCString();
		  document.cookie = "textAdmin=Hide; path=/; expires="+date.toUTCString();
		} else {
		  $('.showAdmin').html('Show');
		  document.cookie = "showAdmin=2; path=/; expires="+date.toUTCString();
		  document.cookie = "textAdmin=Show; path=/; expires="+date.toUTCString();
		}
	});
}
// Элемент таблицы
$(document).ready(function(){
 $('.hcomp').click(function() {
  var date = new Date(new Date().getTime()+1000*60*60*24*30);
  var fs=$('.table').css('font-size');
	if($('.company').is('.active')) {
		$('.company').css({'font-size':fs});
		$('.company').removeClass("active");
		document.cookie = "showComp=2; path=/; expires="+date.toUTCString();
	} else {
		$('.company').css({'font-size':'3px'});
		$('.company').addClass("active");
		document.cookie = "showComp=1; path=/; expires="+date.toUTCString();
	}
 });
//--
 $('.hcom').click(function() {
  var date = new Date(new Date().getTime()+1000*60*60*24*30);
  var fs=$('.hcomp').css('font-size');
	if($('.comm').is('.active')) {
		$('.comm').css({'font-size':fs});
		$('.tcom').css({'font-size':$('.table').css('font-size')});
		$('.com').css({'display':'block'});
		$('.comm').removeClass("active");
		document.cookie = "showCom=2; path=/; expires="+date.toUTCString();
	} else {
		$('.comm').css({'font-size':'3px'});
		$('.com').css({'display':'none'});
		$('.comm').addClass("active");
		document.cookie = "showCom=1; path=/; expires="+date.toUTCString();
	}
 });
//--
 $('.adm_ref').click(function() {
  var date = new Date(new Date().getTime()+1000*60*60*24*30);
  var fs=$('.hcomp').css('font-size');
	if($(this).is('.active')) {
		$('.adm_ref').css({'font-size':fs});
		$('.adm_ref_t').css({'font-size':$('.table').css('font-size')});
		$('.adm_ref').removeClass("active");
		document.cookie = "showAdm=2; path=/; expires="+date.toUTCString();
	} else {
		$('.adm_ref').css({'font-size':'3px'});
		$('.adm_ref').addClass("active");
		document.cookie = "showAdm=1; path=/; expires="+date.toUTCString();
	}
 });
// Обновление таблицы: package, payment, account, comment, date_deactivate
$('.package').change(function(e){
e.preventDefault();
var m_method=$(this).attr('method');
var m_action=$(this).attr('action');
var m_data=$(this).serialize();
var m_act=$(this).find("select");
var m_lim=$(this).find("span");
var m_res=$(this).find(".updateformbutt");
var m_ver=$(this).find("select[name='package']").val();
var grandParent = $(this).parent("span");
$.ajax({
type: m_method,
url: m_action,
data: m_data,
success: function(result){
if(result=="Updated successfully") {
     m_act.css({background:"#d1fba3"});
	 var $item = $('<span class="wnd" style="margin-left:50px;">'+result+'</span>');
	 $item.prependTo(grandParent).delay(2000).fadeOut(200, function(){
	 $item.remove();
	});
} else {
 m_res.css({background:"#fb7e7e",border:"1px solid #f94b4b"});
 alert(result);
}
},
error: function() {
 alert('Error occured');
}
});
});
});
var id=0;
//- Выделение полей
$('.notifycircle').on('click', function () {
    var nm=$(this).attr('data-id');
    $('.name_check').each(function() {
	  if($(this).val()==nm) {
	   if(!$(this).closest(".full_name").hasClass("plus")) {
	 	$(this).closest(".full_name").addClass("plus");
	   } else
	   if($(this).closest(".full_name").hasClass("plus")) {
	 	$(this).closest(".full_name").removeClass("plus");
	   }
	  }
    });
//-
    $('.ip_check').each(function() {
	  if($(this).html()==nm) {
	   if(!$(this).closest(".ip").hasClass("plus")) {
	 	$(this).closest(".ip").addClass("plus");
	   } else
	   if($(this).closest(".ip").hasClass("plus")) {
	 	$(this).closest(".ip").removeClass("plus");
	   }
	  }
    });
//-
    $('.sn_check').each(function() {
	  if($(this).val()==nm) {
	   if(!$(this).closest(".serial").hasClass("plus")) {
	 	$(this).closest(".serial").addClass("plus");
	   } else
	   if($(this).closest(".serial").hasClass("plus")) {
	 	$(this).closest(".serial").removeClass("plus");
	   }
	  }
    });
});
//- check update
$('.name_check').on('click', function () {
    var grandParent = $(this).parents('span');
	$.ajax({type:"POST",url:"files/mysqli_update.php",data:{update:'5',field:($(this).is(':checked')?'1':'0'),add:'<?=$_SERVER['PHP_AUTH_USER']?>',id:$(this).attr('data-id'),nm:$(this).val(),program:'<?=$program?>'}}).done(function(msg) {
    if(msg=="Updated successfully") {
	  var $item = $('<span class="wnd">'+msg+'</span>');
	  $item.prependTo(grandParent).delay(2000).fadeOut(200, function(){
	  $item.remove();
	  });
     } else {
       alert(msg);
     }
	});
	var nm=$(this).val();
	var th=$(this);
    $('.name_check').each(function() {
	  if($(this).val()==nm) {
	  if(th.is(':checked')) {
	    $(this).prop('checked',true);
		$(this).closest(".full_name").addClass("blocked");
	  } else
	  if($(this).not(':checked')) {
		$(this).prop('checked',false);
		$(this).closest(".full_name").removeClass("blocked");
	  }
	  }
    });
});
//--
$('.sn_check').on('click', function () {
    var grandParent = $(this).parents('span');
	$.ajax({type:"POST",url:"files/mysqli_update.php",data:{update:'6',field:($(this).is(':checked')?'1':'0'),add:'<?=$_SERVER['PHP_AUTH_USER']?>',id:$(this).attr('data-id'),nm:$(this).val(),program:'<?=$program?>'}}).done(function(msg) {
    if(msg=="Updated successfully") {
	  var $item = $('<span class="wnd">'+msg+'</span>');
	  $item.prependTo(grandParent).delay(2000).fadeOut(200, function(){
	  $item.remove();
	  });
     } else {
       alert(msg);
     }
	});
	var nm=$(this).val();
	var th=$(this);
    $('.sn_check').each(function() {
	  if($(this).val()==nm) {
	  if(th.is(':checked')) {
	    $(this).prop('checked',true);
		$(this).closest(".serial").addClass("blocked");
	  } else
	  if($(this).not(':checked')) {
		$(this).prop('checked',false);
		$(this).closest(".serial").removeClass("blocked");
	  }
	  }
    });
});
//--
$('.del_form').submit(function(e){
e.preventDefault();
var id=$(this).find("input[name='id']").val();
var field=$(this).find("input[name='field']").val();
var grandParent = $(this).parents('tr');
  $.ajax({type:"POST",url:"files/mysqli_update.php",data:{update:'9',field:field,add:'<?=$_SERVER['PHP_AUTH_USER']?>',id:id},
  success: function(result){
  if(result=="Updated successfully") {
	grandParent.remove();
   } else {
	alert(result);
   }
  },
  error: function(result) {
   alert(result);
  }
 });
});
//--
$('.payment').on('focus', function() {
    var $this = $(this);
	id=$this.attr('data-id');
    $this.data('before', $this.html());
    return $this;
    }).on('blur', function() {
    var $this = $(this);
    if ($this.data('before') !== $this.html()) {
        $this.data('before', $this.html());
        $this.trigger('change');
		var f=$this.html().replace(/<\/?[^>]+(>|$)/g,"").replace(/\&nbsp;/g, '');
        $.ajax({type:"POST",url:"files/mysqli_update.php",data:{update:'2',field:$.trim(f),add:'<?=$_SERVER['PHP_AUTH_USER']?>',id:id}}).done(function(msg) {
          alert(msg);
        });
    }
   return $this;
});
$('.jcom').on('focus', function() {
    var $this = $(this);
	id=$this.attr('data-id');
    $this.data('before', $this.html());
    return $this;
    }).on('blur', function() {
    var $this = $(this);
    if ($this.data('before') !== $this.html()) {
        $this.data('before', $this.html());
        $this.trigger('change');
		var f=$this.html().replace(/<\/?[^>]+(>|$)/g,"").replace(/\&nbsp;/g, '');
        $.ajax({type:"POST",url:"files/mysqli_update.php",data:{update:'3',field:$.trim(f),add:'<?=$_SERVER['PHP_AUTH_USER']?>',id:id,nm:""}}).done(function(msg) {
          alert(msg);
        });
    }
   return $this;
});
$(document).ready(function(){
$('.dt_form').submit(function(e){
e.preventDefault();
var m_method=$(this).attr('method');
var m_action=$(this).attr('action');
var m_data=$(this).serialize();
var m_act=$(this).find("select");
var m_res=$(this).find(".updateformbutt");
var m_lim=$(this).find("span");
var m_day=$(this).find("select[name='upday']").val();
var m_mon=$(this).find("select[name='upmonth']").val();
var m_year=$(this).find("select[name='upyear']").val();
$.ajax({
type: m_method,
url: m_action,
data: m_data,
success: function(result){
if(result=="Updated successfully") {
 m_res.css({background:"#339a36",border:"1px solid #99f560"});
 var timestamp = Math.round(new Date().getTime()/1000.0)-86399;
 var myDate=m_day+'-'+m_mon+'-'+m_year;
 var deactivate = (new Date(myDate.split("-").reverse().join("-")).getTime())/1000.0;
 if(deactivate>timestamp) {
  var valid=(m_year<2038?((deactivate-timestamp)/60/60/24):0);
  m_act.removeClass("notice");
  m_act.removeClass("activ");
  m_act.removeClass("unlim");
  m_act.addClass(m_year<2038?(24*Math.round(valid*10)/10)<24 && (24*Math.round(valid*10)/10)>0?"notice":"activ":"unlim");
  m_lim.html(valid!=0?(valid>0?(Math.ceil(valid)!=1?Math.ceil(valid):(24*Math.round(valid*10)/10)+'h'):"0"):'<font color="#001fbf">Unlim</font>');
 } else {
  m_act.removeClass("notice");
  m_act.removeClass("activ");
  m_act.removeClass("unlim");
  m_lim.text("0");
 }
	 var $item = $('<span class="wnd" style="margin-left:-14px;">'+result+'</span>');
	 $item.appendTo( m_lim ).delay(2000).fadeOut(200, function(){
	 $item.remove();
	});
} else {
 m_res.css({background:"#fb7e7e",border:"1px solid #f94b4b"});
 alert(result);
}
},
error: function() {
 alert('Error occured');
}
});
});
SetDate();
});
//-
function SetDate() {
 var d=new Date();
 var diff = <?=date("H")?> - d.getHours();
 var date=(("0"+d.getDate()).slice(-2)+"."+("0"+(d.getMonth()+1)).slice(-2)+"."+d.getFullYear()+" "+("0"+(d.getHours()+diff)).slice(-2)+":"+("0"+(d.getMinutes()+1)).slice(-2)+":"+("0"+(d.getSeconds()+1)).slice(-2));
 $(".dt").text(date);
 setTimeout(SetDate,1000);
}
//-
$('.csn,.cip').click(function(event) {
    $this = $(this);
    $this.attr('contenteditable',"true");
    $this.blur();
    $this.focus();
});
//- Выбор программы и записей
$('.lineform,.showprod').change(function(e){
e.preventDefault();
var m_method=$(this).attr('method');
var m_action=$(this).attr('action');
var m_data=$(this).serialize();
$.ajax({
type: m_method,
url: m_action,
data: m_data,
success: function(result){
  location.reload();
},
error: function() {
 alert('Error occured');
 location.reload();
}
});
});
//-
$(".logout").click(function(e){
 removeItem("mail");
 var request = new XMLHttpRequest();
 request.open("get","#", false, "false", "false");
 request.send();
 window.location.replace("<?=$_SERVER['REDIRECT_URL']?>");
});
function removeItem(sKey) {
    document.cookie = encodeURIComponent(sKey)+"=; expires=Thu, 01 Jan 1970 00:00:00 GMT;";
}
</script>