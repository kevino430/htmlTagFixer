<?php

/**
*	Linky360 紅點移動科技| Red-comet.mobi
*	本程式著作權屬於紅點移動科技有限公司，相關權益並受法律之保護。
*	如果您不是紅點移動科技有限公司之正式合作伙伴，(I) 請勿使用或散佈；(II) 請立即通知紅點移動科技有限公司；(III) 請從您的系統刪除此程式及附件的所有拷貝。
*/


// Returns the key at the end of the array
function endKey($array){
	end($array);
	return key($array);
}

function echo1($str){
	echo trim($str).'<br />';
}

function pre($d){
	echo '<pre>';
	print_r($d);
	echo '</pre>';
}

//密碼遮蔽
function MaskString($s, $masknum=3){
	$len= strlen($s);
	return substr( $s, 0,1). str_repeat('*',$len-2). substr( $s, -1);
	/*
	if($masknum<0) $masknum = $len + $masknum;
	if($len<3)return $s;
	elseif( $len< $masknum+1)return substr( $s, 0,1). str_repeat('*',$len-2). substr( $s, -1);
	$right=  ($len-$masknum)>>1;
	$left= $len- $right- $masknum;
	return substr( $s, 0,$left). str_repeat('*',$len-$right-$left). substr( $s, -$right);
	*/
}

function MaskString_name($s, $masknum=1){
	$len= mb_strlen($s);
	if($len<=2){return mb_substr( $s, 0,1).'*';}
	$right= 1;
	$left= 1;
	return mb_substr( $s, 0,1). str_repeat('*',$len-$right-$left). mb_substr( $s, -1);
}

function MaskString_phone($s, $masknum=3){
	$len= mb_strlen($s);
	if($masknum<0) $masknum = $len + $masknum;
	if($len<3)return $s;
	elseif( $len< $masknum+1)return mb_substr( $s, 0,1). str_repeat('*',$len-2). mb_substr( $s, -1);
	$right=  ($len-$masknum)>>1;
	$left= $len- $right- $masknum;
	return mb_substr( $s, 0,2). str_repeat('*',$len-$right-$left). mb_substr( $s, -$right);
}

function MaskString_tel($s, $masknum=3){
	$len= mb_strlen($s);
	if($masknum<0) $masknum = $len + $masknum;
	if($len<3)return $s;
	elseif( $len< $masknum+1)return mb_substr( $s, 0,3). str_repeat('*',$len-2). mb_substr( $s, -1);
	$right=  ($len-$masknum)>>1;
	$left= $len- $right- $masknum;
	return mb_substr( $s, 0,3). str_repeat('*',$len-$right-$left). mb_substr( $s, -$right);
}

function MaskString_address($s, $masknum=3){
	$len= mb_strlen($s);
	if($masknum<0) $masknum = $len + $masknum;
	if($len<3)return $s;
	elseif( $len< $masknum+1)return mb_substr( $s, 0,1). str_repeat('*',$len-2). mb_substr( $s, -1);
	$right=  ($len-$masknum)>>1;
	$left= $len- $right- $masknum;
	return mb_substr( $s, 0,2). str_repeat('*',$len-$right-$left). mb_substr( $s, -$right);
}

//產生亂碼字碼
function CreatePassword($password_len=20){
	$password = '';
	$word	='abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789!@#$^*_-|';
	$len	=strlen($word);
	for($i=0;$i<$password_len;$i++){
		$password.=trim($word[rand() % $len]);
	}
	$new_str='';
	$len=strlen($password);
	for($i=0;$i<$len;$i++){
		$str=trim(substr($password,$i,1));//去除半空白字元
		if($str){$new_str.=$str;}
	}
	$password=$new_str;
	return $password;
}

function top_left_link($DIR_PATH='./'){
	// $content='<div id="sidebar-brand" class="themed-background">
	// 				<a href="overview.php" class="sidebar-title">
    //                     <div class="sidebar-brand_logo">
    //                         <img src="'.$DIR_PATH.'img/logo.png" style="height:38px;width:170px;" alt="">
    //                     </div>
    //                 </a>
	// 		</div>';
	$content='';
			/* '<a href="index.php" class="sidebar-title">
			<span class="sidebar-nav-mini-hide"><strong>LINKY-UI</strong></span>
			</a>' */
	return $content;
}
function top_right_link($field){
	$DIR_PATH	=isset($field['DIR_PATH'])	&&trim($field['DIR_PATH'])		?trim($field['DIR_PATH'])	:'./';
	$account_id	=isset($field['account_id'])	&&is_numeric($field['account_id'])	?$field['account_id']		:0;
	$test		=isset($field['test'])		&&is_numeric($field['test'])		?$field['test']			:0;
	if($test){echo1(__FUNCTION__);pre($field);}
	if($field['account_id']==0){exit;}

	global $system;
	$sSQL		='SELECT `name` FROM `bill_account` where `account_id`='.$account_id.';';
	$ary_account	=$system->fetch_arrr($sSQL,array());
	$name		=count($ary_account)>0?$ary_account[0]['name']:'';
	unset($ary_account);

	$html='	<ul class="nav navbar-nav-custom">
				<li>
					<a href="javascript:void(0)" onclick="App.sidebar(\'toggle-sidebar\');">
						<i class="fa fa-ellipsis-v fa-fw animation-fadeInRight" id="sidebar-toggle-mini"></i>
						<i class="fa fa-bars fa-fw animation-fadeInRight" id="sidebar-toggle-full"></i>
					</a>
				</li>
				<li class="hidden-xs animation-fadeInQuick">
					<a href=""><strong>WELCOME '.$name.'</strong></a>
				</li>
			</ul>
			<ul class="nav navbar-nav-custom pull-right">
				<li class="dropdown">
					<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
							<img src="'.$DIR_PATH.'img/dna.png" alt="avatar">
						</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li class="dropdown-header">
							<strong>ADMINISTRATOR</strong>
						</li>
						<li>
							<a href="'.$DIR_PATH.'logout.php"><i class="fa fa-power-off fa-fw pull-right"></i>Log out</a>
						</li>
					</ul>
				</li>
			</ul>';
	return $html;
}
function getDataOption($type,$defid='')
{
	$str="";
	switch ($type)
	{
		case 'y':
			for($i=2012;$i>1910;$i--)
			{
				$str.="<option value='".$i."'" ;
				$str.=($defid==$i)?" selected ":"";
				$str.=">".$i. "</option>" ;
			};
			break;
		case 'm':
			for($i=1;$i<13;$i++)
			{
				$str.="<option value='".sprintf("%02d", $i)."'" ;
				$str.=($defid==sprintf("%02d", $i))?" selected ":"";
				$str.=">".sprintf("%02d", $i)."</option>" ;
			};
			break;
		default:
			for($i=1;$i<32;$i++)
			{
				$str.="<option value='".sprintf("%02d", $i)."'" ;
				$str.=($defid==sprintf("%02d", $i))?" selected ":"";
				$str.=">".sprintf("%02d", $i)."</option>" ;
			};
			break;
	}
	return $str;
}
//------------------------------------------session------------------------------------------//
function check_session($data){
	$logCheck=isset($_SESSION["logCheck"])&&trim($_SESSION["logCheck"]) ? $_SESSION["logCheck"]:'';
	$is_stop=isset($_SESSION["is_stop"])&&is_numeric($_SESSION["is_stop"]) ? $_SESSION["is_stop"]:0;
	if($logCheck != 'yes'){
		header('Location:http://'.url);
		exit;
	}

	if($is_stop==1){
		echo '<script>alert("您的管理者帳號已被暫停使用!");window.location="index.php"</script>';
	}
}

function make_pre_next_html( $_startitems, $_page_items, $_item_count, $page_var){
	$_make_pre_next_html='';
	if( $_startitems > 0 || $_page_items<$_item_count )
	{
		$_pages = (int)($_item_count / $_page_items);
		if( $_item_count % $_page_items != 0 )
			$_pages += 1;

		$_make_pre_next_html .= '<div class="page_nav1">';
		if($_startitems>0)
		{
			$_set_startitems = ($_startitems-$_page_items)>0 ? $_startitems-$_page_items : 0;
			$_make_pre_next_html.='&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?startitems='.$_set_startitems.$page_var.'">&laquo;</a>&nbsp;';
		}else{
			$_make_pre_next_html.= '&nbsp;&laquo;&nbsp;';
		}
		if( round($_item_count / $_page_items,0) >= 2)
		{
			$_nowpage = (int)($_startitems / $_page_items);
			$_nowpage+= 1;
			for($i=-10;$i<9;$i++)
			{
				if($_nowpage+$i > $_pages) break;
				if($_nowpage+$i < 1) continue;
				if($i==0){
					$_make_pre_next_html .= '&nbsp;'.($_nowpage+$i).'&nbsp;';
				}else{
					$_make_pre_next_html .= '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?startitems='.(($_nowpage+$i-1)*$_page_items).$page_var.'">'.($_nowpage+$i).'</a>&nbsp;';
				}
			}
		}
		if($_page_items<=$_item_count && ($_startitems+$_page_items<$_item_count)){
			$_make_pre_next_html .= '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?startitems='.($_startitems+$_page_items).$page_var.'">&raquo;</a>&nbsp;';
		}else{
			$_make_pre_next_html .= '&nbsp;&raquo;&nbsp;';
		}
		$_make_pre_next_html .= '</div>';
	}
	return $_make_pre_next_html;
}

function make_pre_next_html_01( $_startitems, $_page_items, $_item_count, $page_var){
	$_make_pre_next_html='';
	if( $_startitems > 0 || $_page_items<$_item_count )
	{
		$_pages = (int)($_item_count / $_page_items);
		if( $_item_count % $_page_items != 0 )
			$_pages += 1;

		$_make_pre_next_html .= '<div class="text-center"><ul class="pagination">';
		if($_startitems>0)
		{
			$_set_startitems = ($_startitems-$_page_items)>0 ? $_startitems-$_page_items : 0;
			$_make_pre_next_html.='<li ><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array_merge($page_var,array('startitems'=>$_set_startitems))).'"><i class="fa fa-chevron-left"></i></a></li>';
		}else{
			$_make_pre_next_html.= '<li class="disabled"><a href="javascript:void(0)"><i class="fa fa-chevron-left"></i></a></li>';
		}
		if( round($_item_count / $_page_items,0) >= 2)
		{
			$_nowpage = (int)($_startitems / $_page_items);
			$_nowpage+= 1;
			for($i=-10;$i<9;$i++)
			{
				if($_nowpage+$i > $_pages) break;
				if($_nowpage+$i < 1) continue;
				if($i==0){
					$_make_pre_next_html .= '<li class="active"><a href="javascript:void(0)">'.($_nowpage+$i).'</a></li>';
				}else{
					$_make_pre_next_html .= '<li><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array_merge($page_var,array('startitems'=>(($_nowpage+$i-1)*$_page_items)))).'">'.($_nowpage+$i).'</a></li>';
				}
			}
		}
		if($_page_items<=$_item_count && ($_startitems+$_page_items<$_item_count)){
			$NextPage=(int)$_startitems+$_page_items;
			$_make_pre_next_html .= '<li><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array_merge($page_var,array('startitems'=>($NextPage)))).'"><i class="fa fa-chevron-right"></i></a></li>';
		}else{
			$_make_pre_next_html .= '<li class="disabled"><a href="javascript:void(0)"><i class="fa fa-chevron-right"></i></a></li>';
		}
		$_make_pre_next_html .= '</ul></div>';
	}
	return $_make_pre_next_html;
}

function make_pre_next_html_02( $_startitems, $_page_items, $_item_count, $page_var){
	$_make_pre_next_html='';
	if( $_startitems > 0 || $_page_items<$_item_count )
	{
		$_pages = (int)($_item_count / $_page_items);
		if( $_item_count % $_page_items != 0 )
			$_pages += 1;

		$_make_pre_next_html .= '<div class="text-center"><ul class="pagination">';
		if($_startitems>0)
		{
			$_set_startitems = ($_startitems-$_page_items)>0 ? $_startitems-$_page_items : 0;
			$_make_pre_next_html.='<li ><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array_merge($page_var,array('startitems'=>$_set_startitems))).'"><i class="fa fa-chevron-left"></i></a></li>';
		}else{
			$_make_pre_next_html.= '<li class="disabled"><a href="javascript:void(0)"><i class="fa fa-chevron-left"></i></a></li>';
		}
		if( round($_item_count / $_page_items,0) >= 2)
		{
			$_nowpage = (int)($_startitems / $_page_items);
			$_nowpage+= 1;
			for($i=-10;$i<9;$i++)
			{
				if($_nowpage+$i > $_pages) break;
				if($_nowpage+$i < 1) continue;
				if($i==0){
					$_make_pre_next_html .= '<li class="active"><a href="javascript:void(0)">'.($_nowpage+$i).'</a></li>';
				}else{
					$_make_pre_next_html .= '<li><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array_merge($page_var,array('startitems'=>(($_nowpage+$i-1)*$_page_items)))).'">'.($_nowpage+$i).'</a></li>';
				}
			}
		}
		if($_page_items<=$_item_count && ($_startitems+$_page_items<$_item_count)){
			$NextPage=(int)$_startitems+$_page_items;
			$_make_pre_next_html .= '<li><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array_merge($page_var,array('startitems'=>($NextPage)))).'"><i class="fa fa-chevron-right"></i></a></li>';
		}else{
			$_make_pre_next_html .= '<li class="disabled"><a href="javascript:void(0)"><i class="fa fa-chevron-right"></i></a></li>';
		}
		$_make_pre_next_html .= '</ul></div>';
	}
	return $_make_pre_next_html;
}

//取得使用者真實ip
function get_user_ip(){
	$userip='';
	$ary_ip_name=array('HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
	foreach($ary_ip_name as $ip_name){
		if(isset($_SERVER[$ip_name])){
			$userip=$_SERVER[$ip_name];
			break;
		}
	}
	return $userip;
}

/***
 * 20201022 發信程式整併
 * 此處刪除原有的兩隻function
 * smtp_mail_send($field)、smtp_send($data)、sendgrid_send($data)
 */

// 發送sms簡訊(三竹)
function send_sms_message($field){
	//回傳範例  [1] msgid=0820413646 statuscode=1 AccountPoint=92 ');
	//destname=>收訊人名稱。若其他系統需要與簡訊資料進行系統整合，此欄位可填入來源系統所產生的Key值，以對應回來源資料庫。
	$sms_username=isset($field['sms_username'])	&& trim($field['sms_username'])? trim($field['sms_username'])	:'';
	$sms_password=isset($field['sms_password'])	&& trim($field['sms_password'])? trim($field['sms_password'])	:'';
	$fx			=isset($field['fx'])			&& trim($field['fx'])			? trim($field['fx'])			:'LinkyCRM';//前綴 - 用來區分哪個平台的LinkyCRM
	//$m_id		=isset($field['m_id'])		&& is_numeric($field['m_id'])	? $field['m_id']				:0;	//m_id  20180108 改為非必要
	$dstaddr		=isset($field['dstaddr'])		&& trim($field['dstaddr'])		? trim($field['dstaddr'])		:'';	//受訊方手機號碼。請填入09帶頭的手機號碼。
	$smbody		=isset($field['smbody'])		&& trim($field['smbody'])		? trim($field['smbody'])		:'';	//簡訊內容必須為BIG-5編碼。
	$destName   =isset($field['destName'])		&& trim($field['destName'])		? trim($field['destName'])		:'';	//使用者名稱必須為BIG-5編碼。
	$dlvtime   =isset($field['time'])		&& trim($field['time'])		? trim($field['time'])		:date("YmdHis");	//使用者名稱必須為BIG-5編碼。
	// $newtime=($dlvtime-time())<36000?'':$dlvtime-time();

	$smbody=iconv("UTF-8", "BIG5", $smbody);
	$smbody=urlencode($smbody);
	$url='http://smexpress.mitake.com.tw:9600/SmSendGet.asp?username='.$sms_username.'&password='.$sms_password.'&dstaddr='.$dstaddr.'&destName='.$destName .'&smbody='.$smbody.'&dlvtime='.$dlvtime;
	if($sms_username && $sms_password && $dstaddr && $smbody){
		$plik	=fopen($url, 'r');
		$response=fread($plik,1024);
		fclose($plik);
		return $response;
	}
}

// sms狀態
function sms_message_status($field){
	$sms_username=isset($field['username'])	&& trim($field['username'])? trim($field['username'])	:'';
	$sms_password=isset($field['password'])	&& trim($field['password'])? trim($field['password'])	:'';
	$APIKEY=isset($field['APIKEY'])	&& trim($field['APIKEY'])? trim($field['APIKEY'])	:'';
	// $msgid		=isset($field['msgid'])		&& trim($field['msgid'])		? trim($field['msgid'])		:'';	//受訊方手機號碼。請填入09帶頭的手機號碼。

	$url=$APIKEY.'?username='.$sms_username.'&password='.$sms_password;//.'&msgid='.$msgid;
	// return $url;
	// if($sms_username && $sms_password && $APIKEY && $APIKEY){
		$plik	=fopen($url, 'r');
		$response=fread($plik,1024);
		fclose($plik);
		return $response;
	// }
}

function sms_string($content){
    $msgid='';
    //後續可以將資料存成ARRAY做分析判別
    for($i=0;$i<strlen($content);$i++){                             //將所有字串從頭查到最後一個字
        if(mb_substr($content,$i,6)=="msgid="){                     //找出msgid=字串
            for($j=0;$j<strlen($content)-$i;$j++){                  //將剩下的字串數量做統計查詢
                if(mb_substr($content,$i+$j,10)=="statuscode"){     //計算"statuscode"出現的位置
                    $msgid.=mb_substr($content,$i+6,$j-6).",";
                    $i=$i+$j;
                    break;
                }
            }
        }
    }
    $msgid=trim(substr($msgid,0,-1));
    return $msgid;
}

function check_account_type($account){
	if(checkEmail($account)){
		return 1;
	}elseif(checkPhone($account)){
		return 2;
	}else{
		return 3;
	// }else{
	// 	return 4;
	}
}

function checkisTWID($id){
    $id=strtoupper($id);
    $d0=strlen($id);
    if ($d0 <= 0) {return false;}
    if ($d0 > 10) {return false;}
    if ($d0 < 10 && $d0 > 0) {return false;}
    $d1=substr($id,0,1);
    $ds=ord($d1);
    if ($ds > 90 || $ds < 65) {return false;}
    $d2=substr($id,1,1);
    if($d2!="1" && $d2!="2") {return false;}
    for ($i=1;$i<10;$i++) {
        $d3=substr($id,$i,1);
        $ds=ord($d3);
        if ($ds > 57 || $ds < 48) {
            $n=$i+1;
            return false;
            break;
        }
    }
    $num=array("A" => "10","B" => "11","C" => "12","D" => "13","E" => "14",
        "F" => "15","G" => "16","H" => "17","J" => "18","K" => "19","L" => "20",
        "M" => "21","N" => "22","P" => "23","Q" => "24","R" => "25","S" => "26",
        "T" => "27","U" => "28","V" => "29","X" => "30","Y" => "31","W" => "32",
        "Z" => "33","I" => "34","O" => "35");
    $n1=substr($num[$d1],0,1)+(substr($num[$d1],1,1)*9);
    $n2=0;
    for ($j=1;$j<9;$j++) {
        $d4=substr($id,$j,1);
        $n2=$n2+$d4*(9-$j);
    }
    $n3=$n1+$n2+substr($id,9,1);
    if(($n3 % 10)!=0) {return false;}
    return true;
}

function checkPhone($str) {
    if (preg_match("/^09[0-9]{2}-[0-9]{3}-[0-9]{3}$/", $str)) {
        return true;    // 09xx-xxx-xxx
    } else if(preg_match("/^09[0-9]{2}-[0-9]{6}$/", $str)) {
        return true;    // 09xx-xxxxxx
    } else if(preg_match("/^09[0-9]{8}$/", $str)) {
        return true;    // 09xxxxxxxx
    } else {
        return false;
    }
}
function checkEmail($str){
    if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
        return true;    // valid
    } else {
        return false;   // invalid
    }
}

// function checkEmail($email){
// 	$ret=false;
// 	if(strstr($email, '@') && strstr($email, '.')){
// 		$reg = '/[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*$/';
// 		if(preg_match($reg, $email)){
// 			$ret=true;
// 		}
// 	}
// 	return $ret;
// }

function mdate($time = NULL) {
	$text='';
	$time=$time === NULL || $time > time() ? time() : intval($time);
	$t	=time() - $time; //时间差 （秒）
	$y	=date('Y', $time)-date('Y', time());//是否跨年
	switch($t){
		case $t == 0:
			$text = '剛剛';
			break;
		case $t < 60:
			$text = $t . '秒前'; // 一分钟内
			break;
		case $t < 60 * 60:
			$text = floor($t / 60) . '分鐘前'; //一小时内
			break;
		case $t < 60 * 60 * 24:
			$text = floor($t / (60 * 60)) . '小時前'; // 一天内
			break;
		case $t < 60 * 60 * 24 * 3:
			$text = floor($time/(60*60*24)) ==1 ?'昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time) ; //昨天和前天
			break;
		case $t < 60 * 60 * 24 * 30:
			$text = date('m月d日 H:i', $time); //一个月内
			break;
		case $t < 60 * 60 * 24 * 365&&$y==0:
			$text = date('m月d日', $time); //一年内
			break;
		default:
			$text = date('Y年m月d日', $time); //一年以前
			break;
	}
	return $text;
}

function sel_date($field){
	$date=isset($field['date']) && trim($field['date']) ? trim($field['date']):'';
	$date_ary=array();
	switch($date){
		case 0:	//7日
			$date_ary['beginToday']	=mktime(0,0,0,date('m'),date('d')-7,date('Y'));
			$date_ary['endToday']	=time();
			break;
		case 1://30日
			$date_ary['beginToday']	=mktime(0,0,0,date('m')-1,date('d'),date('Y'));
			$date_ary['endToday']	=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			break;
		case 2:	//今日
			$date_ary['beginToday']	=mktime(0,0,0,date('m'),date('d'),date('Y'));
			$date_ary['endToday']	=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			break;
		case 3:	//一年     如果僅要要設定該年度資料   直接將起始日期及結束日期設定為空值即可，目前class有作設定
			$date_ary['beginToday']	=mktime(0,0,0,date('m'),date('d'),date('Y')-1);
			$date_ary['endToday']	=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			break;
		case 4:
			$date_ary['beginToday']	='';
			$date_ary['endToday']	='';
		default:
			$date_ary=explode(',', $date);
			$start_date=isset($date_ary[0]) ? $date_ary[0]:0;
			$end_date=isset($date_ary[1]) ? $date_ary[1]:0;
			if($start_date && $end_date){
				$date_ary['beginToday']	=strtotime($start_date);
				$date_ary['endToday']	=strtotime($end_date)+86399;
			}else{
				$date_ary['beginToday']	=0;
				$date_ary['endToday']	=0;
			}
			break;
	}
	// echo $date;
	return $date_ary;
}

function app_sync_name($field){
	$url		=isset($field['url'])		&& trim($field['url'])			? trim($field['url'])	:'http://www.appmakertw.com/xml_linkycrm_sync.php';
	$app_aid	=isset($field['app_aid'])	&& is_numeric($field['app_aid'])	? $field['app_aid']	:0;

	$ary=array();
	if($url&&$app_aid){
		$ch=curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'order=get_data&aid='.$app_aid);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response	=curl_exec($ch);
		$xml			=simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
		$count		=isset($xml->count)		? $xml->count		:0;
		$android_name	=isset($xml->app_name)	? $xml->app_name	:'';//android name
		$ios_name	=isset($xml->app_name1)	? $xml->app_name1	:'';//ios name
		$ary['android_name']	=$android_name;
		$ary['ios_name']		=$ios_name;
	}
	return $ary;
}

//陣列轉網址字串
function array_export_url($Object){
	$_tmp_url='';
	$i=0;
	foreach($Object as $key => $value){
		if($i==0)
			$_tmp_url.='?'.$key.'='.$value;
		else
			$_tmp_url.='&'.$key.'='.$value;
		$i++;
	}
	return $_tmp_url;
}

//字串加密base64+urlencode
function encryBase64encodeUrl($word=null){
	return base64_encode(urlencode($word));
}

//字串解密base64+urlencode
function decryBase64decodeUrl($word=null){
	return base64_decode(urldecode($word));
}

//多筆搜尋字串組合
/*
function mutipleSearch($key=null, array $array, $method=null){
*/
function mutipleSearch(array $array, $key=null, $method=null){
	$Con='';
	$end=count($array);
	if($end>0){
		$Con=' and (';
		for($i=0;$i<$end;$i++){
			$d=$i==0 ? '' : 'or';
			if($method=='like'){$Con.=$d.' `'.$key.'` like "%'.$array[$i].'%" ';}
			else{$Con.=$d.' `'.$key.'`="'.$array[$i].'" ';}
		}
		$Con.=')';
	}
	return $Con;
}

/**
*系統：Curl(POST)
*說明：
*   必要參數：aPost(array),$type(溝通主機(LinkyCRM/RongCloud))
*   選擇參數：
*接收：
*   基本參數即可
*輸出：
*   伺服器回應代碼
*/
function Curl_Post($aPost,$url){
	if($aPost){
		if(!empty($url))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url );
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // https通訊傳輸
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // https通訊傳輸
			curl_setopt($ch, CURLOPT_POST, true); // 啟用POST
			curl_setopt($ch, CURLOPT_POSTFIELDS,$aPost);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		}
		else
		{
			return trim('<error>url參數錯誤!</error>');
		}
	}else{
		return trim('<error>未帶入必要參數!</error>');
	}
}

function Get_curl($url,$header){
	$curl = curl_init($url);
	$options = array(
		CURLOPT_SSL_VERIFYPEER=>false,	//一定要加不然會報錯
		CURLOPT_HEADER=>false,
		CURLOPT_HTTPHEADER=>$header,
		CURLOPT_RETURNTRANSFER=>1  //將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。
	);
	curl_setopt_array($curl, $options);//陣列設定curl參數
	$result = curl_exec($curl);//輸出結果
	curl_close ($curl);

	$data = json_decode($result,true); //加true轉成陣列

	return $data;
	//取得access_token
}

function Post_curl($url,$header,$postdata){

	$curl = curl_init($url);
	$options = array(
		CURLOPT_POST=>true,
		CURLOPT_SSL_VERIFYPEER=>false,	//一定要加不然會報錯
		CURLOPT_HEADER=>false,
		CURLOPT_HTTPHEADER=>$header,//-h
		CURLOPT_POSTFIELDS=>$postdata,	//傳遞data -d
		CURLOPT_RETURNTRANSFER=>1  //將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。
	);
	curl_setopt_array($curl, $options);//陣列設定curl參數
	$result = curl_exec($curl);//輸出結果
	curl_close ($curl);

	$data = json_decode($result,true);
	return $data;
}

function DELETE_curl($url,$header,$postdata){

	$curl = curl_init($url);
	$options = array(
		CURLOPT_SSL_VERIFYPEER=>false,	//一定要加不然會報錯
		CURLOPT_CUSTOMREQUEST=>"DELETE",
		CURLOPT_HEADER=>false,
		CURLOPT_HTTPHEADER=>$header,//-h
		CURLOPT_POSTFIELDS=>$postdata,	//傳遞data -d
		CURLOPT_RETURNTRANSFER=>1  //將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。
	);
	curl_setopt_array($curl, $options);//陣列設定curl參數
	$result = curl_exec($curl);//輸出結果
	curl_close ($curl);

	//pre($result);
	$data = json_decode($result,true);
	return $data;
}

function type($name, $str_type, $type){
	/*
	$name 命名名稱
	$str_type 字串形態
	$type 接收型態(post,get,session)
	*/
	if($str_type=="num"){
		if($type=="POST"){
			$result=isset($_POST[''.$name.''])&&is_numeric($_POST[''.$name.''])?$_POST[''.$name.'']: 0;
		}
		if($type=="GET"){
			$result=isset($_GET[''.$name.''])&&is_numeric($_GET[''.$name.''])?$_GET[''.$name.'']: 0;
		}
		if($type=="SESSION"){
			$result=isset($_SESSION[''.$name.''])&&is_numeric($_SESSION[''.$name.''])?$_SESSION[''.$name.'']: 0;
		}
	}
	if($str_type=="str"){
		if($type=="POST"){
			$result=isset($_POST[''.$name.''])&&trim($_POST[''.$name.''])?$_POST[''.$name.'']: "";
		}
		if($type=="GET"){
			$result=isset($_GET[''.$name.''])&&trim($_GET[''.$name.''])?$_GET[''.$name.'']: "";
		}
		if($type=="SESSION"){
			$result=isset($_SESSION[''.$name.''])&&trim($_SESSION[''.$name.''])?$_SESSION[''.$name.'']: "";
		}
	}
	if($str_type=="arr"){
		if($type=="POST"){
			$result=isset($_POST[''.$name.''])	&&is_array($_POST[''.$name.''])	?$_POST[''.$name.'']	:"";
		}
		if($type=="GET"){
			$result=isset($_GET[''.$name.''])	&&is_array($_GET[''.$name.''])		?$_GET[''.$name.'']	:"";
		}
		if($type=="SESSION"){
			$result=isset($_SESSION[''.$name.''])&&is_array($_SESSION[''.$name.''])?$_SESSION[''.$name.'']: "";
		}
	}

	return $result;
}


/**
 * 產生alert js code.
 *
 * @param string $s_msg alert訊息
 *
 * @return void
 */
function jsAlert($s_msg)
{
	echo '<script>alert("' . $s_msg . '");</script>';
}

/**
 * 使用js進行網頁轉址.
 *
 * @param string $s_url 欲轉址的網址
 *
 * @return void
 */
function jsHref($s_url)
{
	echo '<script>location.href="' . $s_url . '";</script>';
}

/**
 * 使用js回到上一頁.
 *
 * @return void
 */
function jsBack()
{
	echo '<script>history.back();</script>';
}

/**
 *	訊息回應
 *
 */

function set_result_msg($code,$msg,$order){
	$_xml='';
	if($order&&$msg){
		$_xml.='<order>'.$order.'</order>';
		$_xml.='<'.$order.'>';
		if($code)
		{
			$_xml.='<statusCode>'.$code.'</statusCode>';
		}
		$_xml.='<message>'.($msg).'</message>';
		$_xml.='</'.$order.'>';
	}
	return $_xml;
}


/**
 *	設定錯誤訊息回應
 *
 */
function set_error_msg($msg){
	if($msg){
		return '<error>'.trim($msg).'</error>';
	}
}


/**
 *	AES128解密
 *
 */
function Get_AESdecrypt($text=null)
{
	$aes 	= new AESEncryption($key='EB78D815DE3BD34C2BE6759BE89D567D', $initVector = '', $padding = 'ZERO', $mode = 'ecb', $encoding = 'hex');
	//$enc 	= $aes->encrypt($text);
	return trim($aes->decrypt($text), "\x00..\x1f");
}

/**
 *	AES128加密
 *
 */
function Get_AESencrypt($text)
{
	$encryptText='';
	if(!empty($text))
	{
		$aes 	= new AESEncryption($key='EB78D815DE3BD34C2BE6759BE89D567D', $initVector = '', $padding = 'ZERO', $mode = 'ecb', $encoding = 'hex');
		$enc 	= $aes->encrypt($text);
		$encryptText = $aes->encrypt($text);
	}
	return $encryptText;
}

/**
 *	錯誤控制
 *
 */
function error_handler( $errno, $errmsg, $filename, $linenum, $vars )
{
	if ( 0 === error_reporting() )
	  return false;

	if ( $errno !== E_ERROR )
	  throw new \ErrorException( sprintf('%s: %s', $errno, $errmsg ), 0, $errno, $filename, $linenum );

}

// 輸出秒數的固定格式
function get_time($time_value=''){
	$date_format='Y-m-d H:i:s';
	if($time_value && is_numeric($time_value)){
		return date($date_format, $time_value);
	}else{
		return date($date_format, time());
	}
}

//讀出CSV檔案內容
function csv_list($address){					//輸入檔案位置      檔案格式請以逗號分隔
	$content = stripslashes(htmlspecialchars(file_get_contents($address),ENT_QUOTES));//將傳過來的HTML值變成字串存入資料庫
	$comma_count=substr_count($content, ',');
	if($comma_count<2) return 1;				//回傳沒有，隔開
	$function=explode("\n",$content);			//分開斷行位置
	$array=array();
	foreach($function as $functionlist){		//將各分行位置列出
		$exp=explode(",",$functionlist);		//分開各項次位置
		array_push($array,$exp);    			//傳到$array中
	}
	if($array[0][0]=='name')unset($array[0]);	//刪除第一筆欄位值
	array_pop($array);						//刪除最後一筆空值
	if(count($array)==0) return 0;				//回傳沒有相關數值
	return $array;							//回傳陣列
}

//計算月數
function getMonthNum( $date1, $date2, $tags='-' ){
	$date1 = explode($tags,$date1);
	$date2 = explode($tags,$date2);
	return abs($date1[0] - $date2[0]) * 12 + ($date2[1]-$date1[1]);
}
/**
* 解析json串
* @param type $json_str
* @return type
*/
function analyJson($json_str){
	$json_str=str_replace('\\', '', $json_str);
	$out_arr	=array();
	preg_match('/{.*}/', $json_str, $out_arr);
	if(!empty($out_arr)){
		$result=json_decode($out_arr[0], TRUE);
	}else{
		return FALSE;
	}
	return $result;
}

//(引入前置,檔案傳入位置,檔案資料夾名稱,寬,高,檔案名稱)
function uploadFile($DIR_PATH,$fileInfo,$folder,$w,$h,$name,$maxsize) {
	if(isset($fileInfo['name']) && $fileInfo['name'] != null){
		if (!file_exists($DIR_PATH.$folder)){
			mkdir($DIR_PATH.$folder, 0755, true);
		}
		$type=0;
		if($fileInfo['type']=="image/jpeg"){
			$src = imagecreatefromjpeg($fileInfo['tmp_name']);
			$type=1;
		}elseif($fileInfo['type']=="image/png"){
			$src = imagecreatefrompng($fileInfo['tmp_name']);
			$type=2;
		}elseif($fileInfo['type']=="image/gif"){
			$src = imagecreatefromgif($fileInfo['tmp_name']);
			$type=3;
		}elseif($fileInfo['type']=="image/bmp"){
			$src = imagecreatefrombmp($fileInfo['tmp_name']);
			$type=4;
		}
		$src_w = imagesx($src);
		$src_h = imagesy($src);
		if($maxsize<$fileInfo['size']){  //檔案大小限制
			// 建立縮圖
			$thumb = imagecreatetruecolor($w, $h);
			// 開始縮圖
			imagecopyresampled($thumb, $src, 0, 0, 0, 0, $w, $h, $src_w, $src_h);
			$src=$thumb;
		}elseif($src_w!=$src_h){
			// 建立縮圖
			$thumb = imagecreatetruecolor($w, $h);
			// 開始縮圖
			imagecopyresampled($thumb, $src, 0, 0, 0, 0, $w, $h, $src_w, $src_h);
			$src=$thumb;
		}

		$filename = $fileInfo['name'];
		$extension = explode(".", $filename);
		$new_filename = $name.substr(md5(uniqid(time(), true)),0,10).".".$extension[1];
		if($type==1){
			imagejpeg($src, $DIR_PATH.$folder.'/'.$new_filename);
		}elseif($type==2){
			imagepng( $src, $DIR_PATH.$folder.'/'.$new_filename);
		}elseif($type==3){
			imagegif( $src, $DIR_PATH.$folder.'/'.$new_filename);
		}elseif($type==4){
			imagebmp( $src, $DIR_PATH.$folder.'/'.$new_filename);
		}
		imagedestroy($src);
		return $new_filename;
	}else{
		return '';
	}
}


function getAuthId($ApiUrl, $ApiKey){
	$getTimeUrl = $ApiUrl."get_time";

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($curl, CURLOPT_URL, $getTimeUrl );
	curl_setopt($curl, CURLOPT_POST, true);
	$data = curl_exec($curl);

	$json = json_decode($data, true);
	$t = $json['get_time']['time'];
	$t = substr( $t, 0, strlen( $t )-2 );	

	$auth_id = md5($t.$ApiKey);

	return $auth_id;
}

function apiCurl($Type,$Url,$auth_id,$aid,$postdata){


	$Type = mb_strtoupper($Type);
	
	if($Type == 'GET'){

		$ori_Url = $Url;

		if(!empty($postdata)){
			foreach ($postdata as $K => $V) {
				if ($K != 'ApiUrl' && $K != 'Apikey' &&  $K != 'AUTH-ID' &&  $K != 'AID') {
					if ($Url == $ori_Url) {
						$Url .= "?$K=$V";
					} else {
						$Url .= "&$K=$V";
					}
				}
			}
		}

		$header[]="AUTH-ID: ".$auth_id;
		$header[]="AID: ".$aid;
		$ch  =curl_init();
		curl_setopt($ch, CURLOPT_URL, trim($Url));
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_REFERER, '');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_HEADER , 0);  //Change this to a 1 to return headers
		curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
		// curl_setopt($ch ,CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);

	  
		$error =curl_error($ch); //CURL執行後回傳的錯誤訊息$err
		
		$t = curl_exec($ch);
		$result = json_decode($t);

		curl_close($ch);


		if(!empty($error)){
			$result = json_encode(array('status' => 'error', 'result' => $error));
		}	else {
			$result = json_encode(array('status' => 'success', 'result' => $result));
		}	
		return $result;

	}else if($Type == 'POST'){

		$postdata = json_encode($postdata);

		$header[] = "AUTH-ID: " . $auth_id;
		$header[] = "AID: " . $aid;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, trim($Url));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$error = curl_error($ch);
		$t = curl_exec($ch);
		$result = json_decode($t);

		curl_close($ch);

		if(!empty($error)){
			$result = json_encode(array('status' => 'error', 'result' => $error));
		}	else {
			$result = json_encode(array('status' => 'success', 'result' => $result));
		}	
		return $result;

	}else if($Type == 'PATCH'){
		$postdata = json_encode($postdata);

		$header[] = "AUTH-ID: " . $auth_id;
		$header[] = "AID: " . $aid;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, trim($Url));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH"); // 設定為 PATCH 方法
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$error = curl_error($ch);
		$t = curl_exec($ch);
		$result = json_decode($t);

		curl_close($ch);

		if (!empty($error)) {
			$result = json_encode(array('status' => 'error', 'result' => $error));
		} else {
			$result = json_encode(array('status' => 'success', 'result' => $result));
		}
		return $result;
	}

}
?>
