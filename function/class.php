<?php

/**
 *	Linky360 紅點移動科技| Red-comet.mobi
 *	本程式著作權屬於紅點移動科技有限公司，相關權益並受法律之保護。
 *	如果您不是紅點移動科技有限公司之正式合作伙伴，(I) 請勿使用或散佈；(II) 請立即通知紅點移動科技有限公司；(III) 請從您的系統刪除此程式及附件的所有拷貝。
 */

ini_set('memory_limit', '1024M');
class Linky_system
{
	public $db;
	public $name			= '';
	public $account_id	= 0;
	public $power			= 0;
	public $account		= '';
	public $logCheck		= '';
	public $is_stop		= 0;
	public $session_id	= 0;
	public $permissions	= '';
	public $wmid			= 0;	//前台使用者代號-前台代號-匿名登入成功作為依據
	public $mobile_type	= '';	//手機種類：android、iphone
	public $mobile_imei	= '';	//手機識別碼
	public $mobile_width	= 0;	//手機寬度
	public $mobile_height	= 0;	//手機高度
	public $mobile_lat	= 0;	//手機經度
	public $mobile_lng	= 0;	//手機緯度
	public $rc_id			= 0;	//服務頻道
	public $cookies_name	= 'linky_session';
	public $connect_fail	= false;

	public function __construct($DIR_PATH, $order)
	{
		try {
			// ① 讀取設定檔
			require $DIR_PATH . 'config/start_config.php';

			// ② 建 DSN，順便補 charset（建議 utf8mb4）
			$dsn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset=utf8mb4";

			// ③ 建立 PDO
			$this->db = new PDO(
				$dsn,
				$dbuser,
				$dbpasswd,
				[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
			);

			// ④ 設定 sql_mode（可合併一次執行）
			$this->db->exec("SET NAMES utf8mb4");
			$this->db->exec("SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");

			// ⑤ 以下原本程式碼…
			if ($this->session_id == 0 && !empty($_COOKIE[$this->cookies_name])) {
				$this->get_login_status();
				$this->update_user_account();
			}
		} catch (PDOException $e) {
			$_xml		= '';
			if (empty($order)) {
				$order = 'error';
			}
			$_xml_header = '<?xml version="1.0" encoding="utf-8"?><LinkyCRM>';
			$_xml		.= '<order>' . $e . '</order>';
			$_xml		.= '<' . $order . '>';
			$_xml		.= '<statusCode>E002</statusCode>';
			$_xml		.= '<message> SYSTEM ERROR</message>';
			$_xml		.= '</' . $order . '>';
			$_xml_end	= '</LinkyCRM>';
			header('Content-Type: text/xml');
			echo $_xml_header . $_xml . $_xml_end;
			exit;
		}
	}

	public function __destruct()
	{
		$this->db = null;
	}

	public function __call($name, $param)
	{
		echo1($name . "-目前這個函式沒有定義，請重新確認！");
		pre($param);
	}

	public function __toString()
	{
		echo1('您所輸入的類別名稱：' . __CLASS__);
		return __CLASS__;
	}

	public function __set($name, $value)
	{
		$this->$name = $value;
		echo1('$name=' . $name . ',$value=' . $value);
	}

	public function __get($name = NULL)
	{
		echo1($name);
		return $this->self[$name];
	}

	public function __isset($name)
	{
		return isset($this->$name);
	}

	public function __unset($name)
	{
		unset($this->$name);
	}

	function fetch_arrr($sql, $aValue = array())
	{
		$results = array();
		try {
			$rs = $this->db->prepare($sql);
			$rs->execute($aValue);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			while ($row = $rs->fetch()) {
				$results[] = $row;
			}
			return $results;
		} catch (PDOException $e) {
			echo (__FUNCTION__);
			pre($e);
			return false;
		}
	}

	function pdo_debugSql($StrParams = '')
	{
		echo1(__FUNCTION__);
		var_dump($StrParams);
		if ($StrParams) {
			$ary_StrParams = explode("\n", $StrParams);
			if (count($ary_StrParams) > 0) {
				foreach ($ary_StrParams as $key => $val) {
					if (stripos(trim($val), 'Sent SQL:', 0) > -1) {
						return $val;
						break;
					}
				}
			}
		}
	}

	function pdo_debugStrParams($stmt = null)
	{
		echo1(__FUNCTION__);
		var_dump($stmt);
		if (!empty($stmt)) {
			ob_start();
			$stmt->debugDumpParams();
			$r = ob_get_contents();
			ob_end_clean();
			echo1('abc=' . $r);
			return $r;
		}
	}

	function num_rows($sql)							//計算出此SQL資料有多少數量
	{
		try {
			$q = $this->db->query($sql);
			$rows = $q->fetchAll();
			$rowCount = count($rows);
			return $rowCount;
		} catch (PDOException $e) {
			return false;
		}
	}

	function PDO_Data($sTbName, $aCon, $type)
	{
		if (count($aCon) <= 0 && $type != 'delete') {
			return false;
		} else {
			$con		= "";
			$sColumns = "";
			$aValue	= array();
			$sValues	= "";
			switch ($type) {
				case 'insert':
					$sSQL = "insert into `" . $sTbName . "` ";
					foreach ($aCon as $s => $v) {
						switch ($s) {
							case 'value':
								foreach ($v as $s2 => $v2) {
									$sColumns .= endKey($aCon['value']) != $s2 ? '`' . $s2 . '`' . "," : '`' . $s2 . '`';
									if ($v2 != 'now()') {
										$sValues .= endKey($aCon['value']) != $s2 ? ":" . str_replace(":", "", $s2) . "," : ":" . str_replace(":", "", $s2);
										$aValue[":" . str_replace(":", "", $s2)] = $v2;
									} else {
										$sValues .= endKey($aCon['value']) != $s2 ? $v2 . "," : $v2;
									}
								}
								break;
						}
					}
					$sSQL .= "(" . $sColumns . ") values (" . $sValues . ")";
					break;
				case 'update':
					foreach ($aCon as $s => $v) {
						switch ($s) {
							case 'value':
								foreach ($v as $s2 => $v2) {
									if ($v2 != 'now()') {
										$sValues .= endKey($aCon['value']) != $s2 ? str_replace(":", "", $s2) . "=:" . $s2 . "," : str_replace(":", "", $s2) . "=:" . $s2;
										$aValue[":" . str_replace(":", "", $s2)] = $v2;
									} else {
										// $sValues .= endKey($aCon['value']) != $s2? $s2."=:".$v2.",": $s2."=:".$v2 ;
										$sValues .= (endKey($aCon['value']) != $s2) ? ($s2 . "=:" . $v2 . ",") : ($s2 . "=:" . $v2);
									}
								}
								break;
							case 'con':
								foreach ($v as $s2 => $v2) {
									if ($v2 != 'now()') {
										$con .= endKey($aCon['con']) != $s2 ? str_replace(":", "", $s2) . "=:" . $s2 . " and " : str_replace(":", "", $s2) . "=:" . $s2;
										$aValue[":" . str_replace(":", "", $s2)] = $v2;
									} else {
										$con .= endKey($aCon['con']) != $s2 ? $s2 . "=:" . $v2 . " and " : $s2 . "=:" . $v2;
									}
								}
								break;
						}
					}
					$sSQL = "update `" . $sTbName . "` set " . $sValues . " where " . $con;
					break;
				case 'delete':
					if (!empty($sTbName)) {
						$sSQL = "delete from `" . $sTbName . "`";
					}
					break;
				default:
					break;
			}


			// pre($sSQL);
			// pre($aValue);


			$q = $this->db->prepare($sSQL);
			$q->execute($aValue);

			// get the error 
			// $error = $q->errorInfo();
			// pre($error);
			// exit;

			$r = $type == 'insert' ? $this->db->lastInsertId() : $sSQL;
			return $r;
		}
	}

	function login_read($account = '', $password = '')
	{
		$str = 'SELECT * from `bill_account` WHERE `account`=:account AND `password`=:password;';
		$data = $this->fetch_arrr($str, array('account' => $account, 'password' => $password));
		if (!empty($data)) {
			$this->account_id = $data[0]['account_id'];
			$this->check_login_status(); //檢查有無登入，並且變更登入狀態在資料表裡
		}
		return $data;
	}

	function check_session()
	{
		$account_id	= $this->account_id;
		$is_stop		= $this->is_stop;

		// pre($account_id);
		// exit;

		if (!$account_id) {
			echo '<script>alert("請重新登入!");window.location="index.php"</script>';
			exit;
		}
		if ($is_stop == 1) {
			echo '<script>alert("您的管理者帳號已被暫停使用!");window.location="/index.php"</script>';
			exit;
		}
	}


	function read_general_setting()
	{
		$str = "SELECT * FROM `bill_general_setting` WHERE `g_id`";
		if ($this->num_rows($str)) {
			return $this->fetch_arrr($str);
		}
	}

	//取得用戶IP
	function get_client_ip()
	{
		$_ary_ip = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
		foreach ($_ary_ip as $key) {
			if (!array_key_exists($key, $_SERVER)) {
				continue;
			}
			foreach (explode(',', $_SERVER[$key]) as $ip) {
				$ip = trim($ip);
				if ((bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
					return $ip;
				}
			}
		}
		return NULL;
	}

	function update_user_account()
	{
		$sql	= 'select * from `bill_account` where `account_id`=' . $this->account_id;
		$_result = $this->db->query($sql);
		$_end	= $this->num_rows($sql);
		if ($_end > 0) {
			$_row = $this->fetch_arrr($sql);
			$this->account_id	= $_row[0]["account_id"];
			$this->account		= $_row[0]["account"];
			$this->name			= $_row[0]["name"];
			$this->is_stop		= $_row[0]["is_stop"];
			$this->permissions	= $_row[0]["permissions"];
			$this->power		= $_row[0]["power"];
			$_SESSION["account"] = $this->account;
			$_SESSION["account_id"] = $this->account_id;
		}
		unset($_result, $_end, $_row);
	}

	function check_login_status()
	{
		$account_id	= $this->account_id;
		$user_ip		= $this->get_client_ip();
		$date_count	= 86400; //有限時間:3小時
		if ($account_id) { //處理cookies
			$_session_id = md5(uniqid(rand(), true));
			$sql	= 'select `account_id`, `session_id`, `livetime` from `bill_account_login_session` where `account_id`=' . $account_id;
			$end	= $this->num_rows($sql);
			if ($end > 0) { //有存在
				$ResultData = $this->fetch_arrr($sql);
				$livetime	= $ResultData[0]["livetime"];
				if (time() > $livetime) {
					$sql = 'update `bill_account_login_session` set `session_id`="' . $_session_id . '", `livetime`=' . (time() + $date_count) . ' where `account_id`=' . $account_id;
					$this->db->query($sql);
				} else {
					$_session_id = $ResultData[0]["session_id"];
				}
			} else {
				//無存在-刪除現有的，保持唯一
				$sql = 'DELETE FROM `bill_account_login_session` WHERE `account_id`=' . $account_id;
				$this->db->query($sql);
				$sql = 'ALTER TABLE `bill_account_login_session` AUTO_INCREMENT=1';
				$this->db->query($sql);
				$sql = "INSERT INTO `bill_account_login_session`(`account_id`, `ip`, `session_id`, `livetime`) VALUES( " . $account_id . ", '" . $user_ip . "', '" . $_session_id . "', " . (time() + $date_count) . ")";
				$this->db->query($sql);
			}

			setrawcookie($this->cookies_name, $_session_id, time() + $date_count, '/');
			$this->session_id = $_session_id;
			//$this->update_lasttime();//更新最後登入時間
		}
	}

	//檢查cookies有無存在、過期，並且取回個人身份資料
	function get_login_status()
	{
		$this->session_id = $this->get_cookies();
		if ($this->session_id) {
			$sql	= 'select `account_id` from `bill_account_login_session` where `session_id`="' . $this->session_id . '"';
			$_end	= $this->num_rows($sql);
			if ($_end > 0) {
				$ResultData = $this->fetch_arrr($sql);

				$sql1	= 'select * from `bill_account` where `account_id`=' . $ResultData[0]["account_id"];
				$end1	= $this->num_rows($sql1);
				if ($end1 > 0) {
					$result1 = $this->fetch_arrr($sql1);
					$this->account_id	= $result1[0]["account_id"];
					$this->account		= $result1[0]["account"];
				}
			}
		}
		return $this->account_id;
	}


	//取得現在cookies狀態
	function get_cookies()
	{
		$cookies_name = isset($_COOKIE[$this->cookies_name]) && trim($_COOKIE[$this->cookies_name]) ? trim($_COOKIE[$this->cookies_name]) : '';
		$cookies_name = $cookies_name;
		return $cookies_name;
	}

	//清除cookies-登入者本身
	function clear_cookies()
	{
		setcookie($this->cookies_name, '', (time() - 1), '/');
		$this->session_id = 0;
	}

	function API_response($sl_id = 1, $statuscode)
	{							//輸入slid , 狀態碼 輸出對應的語系狀態碼
		$sql = "SELECT `tran`.`name` as `message` FROM `system_lang_api_response_list` as `list`
		inner join `system_lang_api_response_translator` as `tran` 
		on `list`.`system_lang_api_response_list_id`=`tran`.`system_lang_api_response_list_id`
		where `status_code`= '" . $statuscode . "' and `tran`.`sl_id` = '" . $sl_id . "' and `is_del` = 0 ";
		$count = $this->num_rows($sql);
		$arr = array();
		if ($count == 0) {
			$arr = array('0' => array('message' => 'null'));
			return $arr;
		} else {
			return $this->fetch_arrr($sql);
		}
	}

	function ppvPointCount($fields)
	{
		// pre($fields);
		// exit;

		$ppv_id = !empty($fields['ppv_id']) ? $fields['ppv_id'] : 0;
		$start_time = !empty($fields['start_time']) ? $fields['start_time'] : 0;
		$end_time = !empty($fields['end_time']) ? $fields['end_time'] : 0;
		$ppv_icoin_ex_rate = !empty($fields['ppv_icoin_ex_rate']) ? $fields['ppv_icoin_ex_rate'] : 0;
		$ppv_cp_share = !empty($fields['ppv_cp_share']) ? $fields['ppv_cp_share'] : 0;
		$TranStatus = !empty($fields['TranStatus']) ? $fields['TranStatus'] : 0;

		$sys_service_id = !empty($fields['sys_service_id']) ? $fields['sys_service_id'] : '0';
		// 去除最後一個逗號
		$sys_service_id = rtrim($sys_service_id, ',');

		$issuerIdStr = !empty($fields['issuerIdStr']) ? $fields['issuerIdStr'] : "";

		$rejectEmail = $this->fetch_arrr("SELECT * from `bill_reject_account` WHERE `bill_reject_account_id`=1");
		$rejectEmail = !empty($rejectEmail[0]['reject_account']) ? $rejectEmail[0]['reject_account'] : "";
		if (!empty($rejectEmail)) {
			$rejectEmailSql = "AND `email` NOT IN ($rejectEmail)";
		} else {
			$rejectEmailSql = "";
		}

		// `order`.`sys_service_id` = 14 代表是ppv

		$SqlPointData = "SELECT 
							`order`.`issuer_id`,
							count(`order`.`issuer_id`) AS orderCount,
							sum(`order`.`points`) AS totalPoints,
							$ppv_icoin_ex_rate AS ppvIcoinExRate,
							$ppv_cp_share AS ppvCpShare,
							sum(`order`.`points`)*$ppv_icoin_ex_rate AS pointToCash,
							ROUND(sum(`order`.`points`)*$ppv_icoin_ex_rate*$ppv_cp_share) AS cashCaShare,
							`bill_ppv_issue_list`.`issuer_name` AS issuerName
						FROM 
							`video_service`.`order`
							join `bill_ppv_issue_list` on `order`.`issuer_id` = `bill_ppv_issue_list`.`issuer_id`
						WHERE  
							`order`.`sys_service_id` IN ($sys_service_id) AND
							`order`.`issuer_id` IN ($issuerIdStr) AND 
							`order`.`createtime` > $start_time AND `order`.`createtime` <= $end_time AND
							`order`.`TranStatus` = '$TranStatus ' AND 
							`order`.`cancel` = 0 AND 
							`order`.`is_cash` = 0 AND 
							`order`.`points` != 0 AND 
							`order`.`price`  = 0 
							$rejectEmailSql 
						GROUP by issuer_id ;";

		// pre($SqlPointData );
		// exit;

		$PointData = $this->fetch_arrr($SqlPointData);

		// pre($PointData);
		// exit;



		return $PointData;
	}

	function ppvPointOrderList($fields)
	{
		$ppv_id = !empty($fields['ppv_id']) ? $fields['ppv_id'] : 0;
		$start_time = !empty($fields['start_time']) ? $fields['start_time'] : 0;
		$end_time = !empty($fields['end_time']) ? $fields['end_time'] : 0;

		$issuerIdStr = !empty($fields['issuerIdStr']) ? $fields['issuerIdStr'] : "";

		$rejectEmail = $this->fetch_arrr("SELECT * from `bill_reject_account` WHERE `bill_reject_account_id`=1");
		$rejectEmail = !empty($rejectEmail[0]['reject_account']) ? $rejectEmail[0]['reject_account'] : "";
		if (!empty($rejectEmail)) {
			$rejectEmailSql = "AND `email` NOT IN ($rejectEmail)";
		} else {
			$rejectEmailSql = "";
		}

		$SqlPointData = "SELECT 
							`order`.`movie_id`,
							`order`.`issuer_id`,
							`order`.`createtime`,
							`order`.`order_number`,
							`order`.`email`,
							`order`.`points`,
							`order`.`price`,
							`order`.`TranStatus`,
							`bill_ppv_issue_list`.`issuer_name` AS issuerName,

							`order_id`,
                			`m_id`,
                			`card_num`,
                			`order_number`,
                			`currency`,
                			`price`,
                			CASE 
                			    WHEN `order`.`price` = FLOOR(`order`.`price`) THEN CAST(FLOOR(`order`.`price`) AS CHAR)
                			    ELSE CAST(`order`.`price` AS CHAR)
                			END AS formattedPrice,
                			floor(`order`.`price`*(100-`fee_rate`)/100 ) As `realPrice`,
                			`points`,
                			`cancel`,
                			`price_subscribe_id`,
                			`price_subscribe_type`,
                			`order`.`createtime`,
                			`order`.`lasttime`,
                			`order`.`email`,
                			`order`.`box_type`,
                			`TranStatus`,
                			`referral_key2`,

                			`price_subscribe_type`.`name_tw` AS `tw_subscribe_name`,
                			`sys_service`.`name_tw` AS `tw_service_name`,
                			`bill_price_subscribe_fee`.`fee_rate` AS `bill_fee_rate`,
                			`bill_price_subscribe_fee`.`is_point` AS `is_point`

						FROM 
							`order`
							join `bill_ppv_issue_list` on `order`.`issuer_id` = `bill_ppv_issue_list`.`issuer_id`
							JOIN `price_subscribe_type` ON `order`.`price_subscribe_type` = `price_subscribe_type`.`price_subscribe_type_id`
							JOIN `sys_service` ON `price_subscribe_type`.`sys_service_id` = `sys_service`.`sys_service_id`
							JOIN `bill_price_subscribe_fee` ON `price_subscribe_type`.`price_subscribe_type_id` = `bill_price_subscribe_fee`.`p_s_t_id`
						WHERE  
							`order`.`issuer_id` IN ($issuerIdStr) AND 
							`order`.`createtime` > $start_time AND `order`.`createtime` <= $end_time AND
							`order`.`cancel` = 0 AND 
							`order`.`is_cash` = 0 AND 
							`order`.`points` != 0 AND
							`order`.`price`  = 0
							$rejectEmailSql
						ORDER BY FIELD(`order`.`TranStatus`,'S','F','C','W'),createtime DESC;";
		$PointData = $this->fetch_arrr($SqlPointData);
		// pre($PointData);
		return $PointData;
	}

	function ppvAgentCaclulation($fields)
	{
		$start_time_forSqlUnix = !empty($fields['start_time']) ? $fields['start_time'] : 0;
		$end_time_forSqlUnix = !empty($fields['end_time']) ? $fields['end_time'] : 0;

		if (empty($start_time_forSqlUnix) || empty($end_time_forSqlUnix)) {
			return false;
		}

		$sql = "SELECT * FROM `bill_ppv_vendor` WHERE 1=1";
		$ppvData = $this->fetch_arrr($sql);

		// pre($ppvData);
		$successRecordArray = array();
		$cancleRecordArray = array();

		foreach ($ppvData as $key => $value) {
			// 查詢出帳單 status = 1 的 紀錄
			$ppv_id = $value['ppv_id'];
			$ppv_name = $value['ppv_name'];
			$ppvCaShare = $value['ca_share'];

			$icon_ex_rate = $value['icon_ex_rate'];
			$vat = $value['camp_tax'];

			$sql = "SELECT * FROM `bill_ppv_bill_record` WHERE `ppv_id`='$ppv_id' AND `bill_status`='1' AND `bill_create_time` BETWEEN '$start_time_forSqlUnix' AND '$end_time_forSqlUnix' ";

			$recordData = $this->fetch_arrr($sql);

			$successPoint = 0;
			$cancelsPoint = 0;

			$successCount = 0;
			$cancelsCount = 0;

			foreach ($recordData as $key => $value) {
				$bill_json = $value['bill_json'];
				$bill_json = json_decode($bill_json, true);

				$billSuccess = $bill_json['Success'];
				$billCancels = $bill_json['Cancels'];

				foreach ($billSuccess as $keyS => $valueS) {
					$successPoint += $valueS['totalPoints'];
					$successCount += $valueS['orderCount'];
				}
				foreach ($billCancels as $keyC => $valueC) {
					$cancelsPoint += $valueC['totalPoints'];
					$cancelsCount += $valueC['orderCount'];
				}
			}

			if (!empty($successPoint)) {
				$successRecordArray[$ppv_id]['ppv_id'] = $ppv_id;
				$successRecordArray[$ppv_id]['ppv_name'] = $ppv_name;
				$successRecordArray[$ppv_id]['successPoint'] = $successPoint;
				$successRecordArray[$ppv_id]['successCount'] = $successCount;

				// 點數:金額(1:TWD)
				$successRecordArray[$ppv_id]['icon_ex_rate'] = $icon_ex_rate;

				// ca_share
				$successRecordArray[$ppv_id]['ca_share'] = $ppvCaShare;

				// camp_tax
				$successRecordArray[$ppv_id]['vat'] = $vat;

				// count total 無條件捨去
				$successRecordArray[$ppv_id]['total'] = floor($successPoint * $icon_ex_rate * ($ppvCaShare / 100) * ((100 - $vat) / 100));
			}

			if (!empty($cancelsPoint)) {
				$cancleRecordArray[$ppv_id]['ppv_id'] = $ppv_id;
				$cancleRecordArray[$ppv_id]['ppv_name'] = $ppv_name;
				$cancleRecordArray[$ppv_id]['cancelsPoint'] = $cancelsPoint;
				$cancleRecordArray[$ppv_id]['cancelsCount'] = $cancelsCount;

				// 點數:金額(1:TWD)
				$cancleRecordArray[$ppv_id]['icon_ex_rate'] = $icon_ex_rate;

				// ca_share
				$cancleRecordArray[$ppv_id]['ca_share'] = $ppvCaShare;

				// camp_tax
				$cancleRecordArray[$ppv_id]['vat'] = $vat;

				// count total 無條件捨去
				$cancleRecordArray[$ppv_id]['total'] = floor($cancelsPoint * $icon_ex_rate * ($ppvCaShare / 100) * ((100 - $vat) / 100));
			}
		}

		return array(
			'successRecordArray' => $successRecordArray,
			'cancleRecordArray' => $cancleRecordArray
		);
	}
}
