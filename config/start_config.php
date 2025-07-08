<?php

/**
 *	Linky360 紅點移動科技| Red-comet.mobi
 *	本程式著作權屬於紅點移動科技有限公司，相關權益並受法律之保護。
 *	如果您不是紅點移動科技有限公司之正式合作伙伴，(I) 請勿使用或散佈；(II) 請立即通知紅點移動科技有限公司；(III) 請從您的系統刪除此程式及附件的所有拷貝。
 */

// start_config 主要針對一進到網域就必須進行的功能，比方資料庫連線設定，ip確認等等
$HTTP_HOST	= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$SHELL		= isset($_SERVER['SHELL']) ? $_SERVER['SHELL'] : ''; //20200715-支援linux-script執行


if (in_array($HTTP_HOST, array('localhost', '127.0.0.1'))) {

	$dbhost	= '127.0.0.1';
	$dbuser	= 'root';
	$dbport	= '3307';
	$dbname	= 'shopi_test';
	$dbpasswd = '';
	
}


