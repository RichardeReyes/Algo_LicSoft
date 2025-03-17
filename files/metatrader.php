<?php
require "connect_mysql.inc.php";

file_put_contents("Dbg.txt","Iniciox");




if(empty($_POST['r1'])) {
	if(empty($_GET['r1']))  {
		file_put_contents("Dbg.txt","ErrRR1",FILE_APPEND);

		echo ":::Error 429=contact technical support"; // respuesta al terminal en caso de error
		exit();
	}
	else
	{
		$PRMT=$_GET['r1'];
		file_put_contents("Dbg.txt","Pmt-".$PRMT,FILE_APPEND);
	}
} 
else
{
	$PRMT=$_POST['r1'];
}


file_put_contents("Dbg.txt","2",FILE_APPEND);

$arrpost=explode("$",str_replace(' ','+',$PRMT));
if(count($arrpost)!=3) {
file_put_contents("Dbg.txt","ErrRR2",FILE_APPEND);
  echo ":::Error 429=contact technical support-".$PRMT; // respuesta al terminal en caso de error
  exit();
}

file_put_contents("Dbg.txt","3",FILE_APPEND);

$post_key=$post1=StringDecrypt($arrpost[0],"jlY2E9rzw/qJOd1S#G!28/k10C3!Sku5");
$criptkey="h4yT!H3/dA3K9z".$post_key."trl/xdFgj#erPjm";
$post1=StringDecrypt($arrpost[1],$criptkey);
$post2=StringDecrypt($arrpost[2],$criptkey);
$rpost='|'.$post1.'|'.$post2;
$POST=explode("|",$rpost);
//--
$req1= trim($POST[1]);  // ACCOUNT_LOGIN
$req2= trim($POST[2]);  // ACCOUNT_NAME
$req3= trim($POST[3]);  // ACCOUNT_COMPANY
$req4= trim($POST[4]);  // MQL PROGRAM NAME
$req5= trim($POST[5]);  // ACCOUNT_TRADE_MODE
$req6= trim($POST[6]);  // Version Programm
$req7= trim($POST[7]);  // MetaTrader 4/5
$req8= trim($POST[8]);  // Referrer
$req9= trim($POST[9]);  // Referrer payout percentage
$req10=trim($POST[10]); // ACCOUNT_BALANCE
$req11=trim($POST[11]); // ACCOUNT_EQUITY
$req12=trim($POST[12]); // Close Profit
$req13=trim($POST[13]); // ACCOUNT_SERVER
$req14=trim($POST[14]); // ACCOUNT_CURRENCY
$req15=trim($POST[15]); // ACCOUNT_TRADE_ALLOWED
$req16=trim($POST[16]); // driveID
$req17=trim($POST[17]); // UUID
$req18=trim($POST[18]); // Code
$NowIP=$_SERVER['REMOTE_ADDR']; // IP


file_put_contents("Dbg.txt","Prueba".$req16,FILE_APPEND);

switch($req5) {
    case 0: $req5="Demo";    break;
    case 1: $req5="Contest"; break;
	case 2: $req5="Real";    break;
}
switch($req15) {
    case 0: $req15="Investor"; break;
    case 1: $req15="Trader";   break;
}
// Versión actual del producto
$program="";
foreach($ar_prog as $x) {
  if($req4==trim(explode(",",$x)[0])) {
	$program=trim(explode(",",$x)[0]);
    $version=trim(explode(",",$x)[1]);
	$period=trim(explode(",",$x)[2]);  // Registro automático desde la terminal por N días
	$package=trim(explode(",",$x)[3]); // Registro automático con el paquete
	$num_activ=trim(explode(",",$x)[4]); // Número de activaciones
	break;
  }
}
// Comprobando el nombre del asesor conectado
if($program=="") {
  echo StringEncrypt(":::The program is not registered in the system|end",$criptkey); // respuesta a la terminal
  exit();
}

//--
 $db = mysqli_connect($localhost, $mysql_user, $mysql_password, $mysql_db);
 mysqli_set_charset($db, 'utf8'); //utf-8
  if(!$db) {
	echo StringEncrypt(":::Error 503=Try later|end",$criptkey); // respuesta al terminal cuando hay un error al conectarse a la base de datos
    exit();
  }
//--
 $maxdate=Array();
 $athist=Array();
 $allhist=Array();
 $uthist=Array();
 $unhist=Array();
 $allcount_id='';
 $uncount_id='';
 $ftest=2;
 $Account_exists=false;
 $result = mysqli_query($db,"SELECT * FROM `users_auth` WHERE `program`='".addslashes($req4)."'");
  while($row = mysqli_fetch_array($result)) {
   $full_name= iconv('utf-8','cp1251',$row['full_name']);
   $full_name_blocked= $row["full_name_blocked"];
   $serialNo_blocked=0;
   $file=file('blocking/'.$row["program"].".csv");
   foreach($file as $item){
	if($req16==trim($item)){
	  $serialNo_blocked=1;
	  break;
    }
   }
    //- Buscar hardware de identificación en la base de datos
  /*  $hist_sNo= $row["hist_serialNo"];
    if(stristr($hist_sNo, $req16)) {
      $maxdate[]= $row["deactivate_date"];
	  $athist[]= explode('|', $hist_sNo);
	  if($row["test"]==1) {
		$uthist[]= explode('|', $hist_sNo);
		$ftest=1;
	  }
    }*/
  array_walk_recursive($athist, function ($item, $key) use (&$allhist) {
    $allhist[] = $item;    
  });
  array_walk_recursive($uthist, function ($item, $key) use (&$unhist) {
    $unhist[] = $item;    
  });
  $allhist=array_unique($allhist);
  $allcount_id=count($allhist);
  $unhist=array_unique($unhist);
  $uncount_id=count($unhist);

	//- Блокировки
	if(($req2==$full_name && $full_name_blocked==1) || $serialNo_blocked==1) {
     if($req16!='') { // actualizar la información del dispositivo y de la conexión
      $result = mysqli_query($db,"UPDATE `users_auth` SET `serialNo`='$req16',`ip`='$NowIP',`trading`='$req15',`hist_serialNo`=CONCAT_WS('|',`hist_serialNo`,'$req16'),`ip_history`=CONCAT_WS('|',`ip_history`,'$NowIP'), `hist_trading`=CONCAT_WS('|',`hist_trading`,'$req15'),`date_change_conf`=CONCAT_WS('|',`date_change_conf`,'".time()."') WHERE serialNo!='$req16' AND account='$req1'");
     }
     echo StringEncrypt(":::Blocked User".($serialNo_blocked==1?": ".$req16:"")."|end",$criptkey);
	 mysqli_close($db);
	 exit();
	}
 //-- Si el número de cuenta está en la base de datos- rellena los datos

   if($req1 == $row["account"]) {
    $_id= $row["id"];
    $_account= $row["account"];
    $_program= $row["program"];
    $_version= $row["version"];
    $_full_name= $row["full_name"];
	$_full_name=iconv('utf-8','cp1251', $_full_name);
    $_deactive_date= $row["deactivate_date"];
    $_mt= $row["mt"];
    $_balance= $row["balance"];
    $_equity= $row["equity"];
    $_hist_balance= $row["hist_balance"];
    $_close_profit= $row["close_profit"];
    $_package= $row["package"];
	$_serialNo= $row["serialNo"];
	$Account_exists=true;
   }
  }
 // Resultados de búsqueda por ID de hardware
  // Si se excede el número de activaciones
  if($uncount_id>$num_activ) {
    $result = mysqli_query($db,"UPDATE `users_auth` SET `serialNo`='$req16',`ip`='$NowIP',`trading`='$req15',`hist_serialNo`=CONCAT_WS('|',`hist_serialNo`,'$req16'),`ip_history`=CONCAT_WS('|',`ip_history`,'$NowIP'), `hist_trading`=CONCAT_WS('|',`hist_trading`,'$req15'),`date_change_conf`=CONCAT_WS('|',`date_change_conf`,'".time()."') WHERE serialNo!='$req16' AND account='$req1'");
	echo StringEncrypt(":::The number of activations has ended: ".$uncount_id."|end",$criptkey); // El número de activaciones ha finalizado.
	mysqli_close($db);
	exit;
  }

//-----------------------------
// Si el número de cuenta está en la base de datos
if($Account_exists) {
//- rellenar/añadir/actualizar datos (registro manual)
if($req2!=$_full_name) {
  $req2=iconv('cp1251', 'utf-8', $req2);
  $result = mysqli_query($db,"UPDATE `users_auth` SET `full_name`='$req2',`company`='$req3',`server`='$req13',`mt`='$req7',`type`='$req5',`currency`='$req14',`serialNo`='$req16',`ip`='$NowIP',`trading`='$req15',`hist_serialNo`='$req16',`ip_history`='$NowIP', `hist_trading`='$req15',`date_change_conf`='".time()."' WHERE id=$_id");
}
//- actualizar la información del dispositivo y de la conexión
if($req16!=$_serialNo && $req16!='' && $_serialNo!='') {
  $result = mysqli_query($db,"UPDATE `users_auth` SET `serialNo`='$req16',`ip`='$NowIP',`trading`='$req15',`hist_serialNo`=CONCAT_WS('|',`hist_serialNo`,'$req16'),`ip_history`=CONCAT_WS('|',`ip_history`,'$NowIP'), `hist_trading`=CONCAT_WS('|',`hist_trading`,'$req15'),`date_change_conf`=CONCAT_WS('|',`date_change_conf`,'".time()."') WHERE id=$_id");
}
//- actualiza tu saldo y versión del programa
//if($_version!=$req6 || $_balance!=$req10 || $_equity!=$req11 || $_close_profit!=$req12) {
if($_balance!=$req10 || $_equity!=$req11 || $_close_profit!=$req12) {
  if($_hist_balance==0) {
	$result = mysqli_query($db,"UPDATE `users_auth` SET `version`='$req6',`balance`='$req10',`equity`='$req11',`close_profit`='$req12',`hist_balance`='$req10' WHERE id=$_id");
  } else {
	$result = mysqli_query($db,"UPDATE `users_auth` SET `version`='$req6',`balance`='$req10',`equity`='$req11',`close_profit`='$req12' WHERE id=$_id");
  }
}
//- comprobemos la activación
 if($_deactive_date>=time()-0) {
  // actualizar el número de conexiones `connect`
  $result = mysqli_query($db,"UPDATE `users_auth` SET `connect`=`connect`+1,`last_connect`='".date('d.m.Y',time())."' WHERE id=$_id");
//  if($req6==$version) { // Si las versiones coinciden
//    echo StringEncrypt(date('d.m.Y',$_deactive_date)."|".$_package."|".$version."||end",$criptkey); // todo esta bien
    echo StringEncrypt(date('d.m.Y',$_deactive_date)."|".$_package."|".$req6."||end",$criptkey); // todo esta bien
//  } else {
//    $b_prog=array();
//    $fh=fopen("price_link.txt","r");
//    if(filesize("price_link.txt")>0) {
//      $text=fread($fh,filesize("price_link.txt"));
//      $b_prog=explode(PHP_EOL,$text);
//    } fclose($fh);
//	foreach($b_prog as $str2) {
//	 if($req4==trim(explode(",", $str2)[0])) {
//      echo StringEncrypt(date('d.m.Y',$_deactive_date)."|".$_package."|".$version."|".trim(explode(",", $str2)[3])."|end",$criptkey); // Comentar en el gráfico.
//	 }
//	}
//  }
 } else {
  // actualizar el número de conexiones `disconnect`
  $result = mysqli_query($db,"UPDATE `users_auth` SET `disconnect`=`disconnect`+1,`last_connect`='".date('d.m.Y',time())."' WHERE id=$_id");
  echo StringEncrypt(":::Activation expired: ".$req16."|end",$criptkey);
  }
  mysqli_close($db);
  exit();
} else { ///////////////////////////////////////////////
 // Si no hay ningún usuario, lo registraremos en la base de datos como “Vigilado”
  // Si estás en fin de semana, agregaremos activaciones.
  if($period>0) {
   switch(date("w",time())) {
    case 5: $period+=3; break;
	case 6: $period+=2; break;
	case 0: $period+=1; break;
	default: $period+=1;
   }
  }
 $dt=strtotime(date('d.m.Y',time()+(86400*$period)))-1; // fecha de desactivación
 $comm=' ';
 
  $req2=iconv('cp1251', 'utf-8', $req2);
  $result = mysqli_query($db, "SELECT * FROM `users_auth` ORDER BY `id` DESC LIMIT 1");
  $cn=mysqli_fetch_array($result)[0]+1;
//--
$result = "INSERT INTO `users_auth` (`id`,`add_date`,`full_name`,`account`,`company`,`server`,`mt`,`type`,`trading`,`hist_trading`,`deactivate_date`,`registrar`,`balance`,`hist_balance`,`equity`,`currency`,`connect`,`last_connect`,`program`,`package`,`hist_package`,`version`,`test`,`serialNo`,`hist_serialNo`,`ip`,`ip_history`,`ref`,`fee`,`hist_update_date`,`date_change_conf`,`hist_deactivate_date`,`hist_update_user`,`comment`,`hist_comment`,`mt_code`,`hist_payment`,`hist_full_name_blocked`,`hist_serialNo_blocked`) 
VALUES ('$cn','".date('d.m.Y H:i',time())."','$req2','$req1','$req3','$req13','$req7','$req5','$req15','$req15','$dt','MT$req7','$req10','$req10','$req11','$req14','1','".date('d.m.Y',time())."','$req4','$package','$package','$req6','$ftest','$req16','$req16','$NowIP','$NowIP','$req8','$req9','".time()."','".time()."','$dt','MT$req7','$comm','$comm','$req18','0','0','0')";

  $add = mysqli_query($db, $result); // or die(mysqli_error($db));
   if(!$add) {
	$reg = mysqli_error($db);
	echo StringEncrypt(':::Error 503=Try later|end"',$criptkey); // respuesta al terminal cuando hay un error al conectarse a la base de datos
   } else {
	echo StringEncrypt(date('d.m.Y',$dt).'|'.$package."|".'Registered|""|end',$criptkey); // Registered before: 10 symb
	$email=file_get_contents("mail.txt");
	if($email!="") Send($email,$req4,$req7);
   }
  mysqli_close($db);
  exit();
}
// -- Send email
function Send($to,$prog,$ver) {
  $subject = "Product registration: ".$prog." (MT".$ver.")";
  $message = "Automatic product registration: ".$prog." (MT".$ver.")";
  $from = $_SERVER['SERVER_NAME'];
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
  $headers .= "From: <".$from.">\r\n";
   if(mail($to,$subject,$message,$headers)) {
	 //
   }
}
function StringEncrypt($plainText,$crKey) {
  $ciphtext = openssl_encrypt($plainText,'AES-256-ECB',$crKey,OPENSSL_RAW_DATA);
 return base64_encode($ciphtext);
}
function StringDecrypt($plainText,$crKey) {
  $ciphtext = openssl_decrypt(base64_decode($plainText),"AES-256-ECB",$crKey,OPENSSL_ZERO_PADDING|OPENSSL_RAW_DATA);
 return trim($ciphtext,"\x00..\x1F");
}
?>