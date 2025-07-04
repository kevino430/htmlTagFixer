<?php
session_start();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-type: text/html; charset=utf-8');
header('Vary: Accept-Language');
date_default_timezone_set('Asia/Taipei');
mb_internal_encoding("UTF-8");

include($DIR_PATH.'function/function.php');


include($DIR_PATH.'function/class.php');




global $lang_ip;
$lang_ip=['211.75.186.7','220.133.105.104'];//語系功能顯不顯示[]的ip位置-可用於代表內部開發用IP



$HTTP_HOST	=isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"]:'';




$order		=isset($_REQUEST['order'])? $_REQUEST['order']:'';



$system		=new Linky_system($DIR_PATH,$order);

// echo 123555;
// exit;

$account_type=$system->read_general_setting();
$_startitems	=isset($_GET['startitems']) && is_numeric($_GET['startitems']) ? $_GET['startitems'] : 0;
$_page_items	=50;
$account_id	=$system->account_id;
$power		=$system->power;
$_ary_file	=explode('/', $_SERVER['PHP_SELF']);
$_this_file	=$_ary_file[count($_ary_file)-1];
$_last_file	=isset($_SERVER['HTTP_REFERER'])&& trim($_SERVER['HTTP_REFERER'])	? trim($_SERVER['HTTP_REFERER']): '';





$bigquery_filename='linky-id-c7a2a20197ec.json';
putenv('GOOGLE_APPLICATION_CREDENTIALS='.$DIR_PATH.'/keys/linky-id-c7a2a20197ec.json');
$projectId='linky-id';
$datasetId='Dev_linky360';

/** 版本資訊 **/
$version_code='202010151541'; //git上的版本日期(年月日時分)
$version360=1;


