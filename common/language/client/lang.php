<?php

/**
*	Linky360 紅點移動科技| Red-comet.mobi
*	本程式著作權屬於紅點移動科技有限公司，相關權益並受法律之保護。
*	如果您不是紅點移動科技有限公司之正式合作伙伴，(I) 請勿使用或散佈；(II) 請立即通知紅點移動科技有限公司；(III) 請從您的系統刪除此程式及附件的所有拷貝。
*/

$client_lang=array(
			'join_member'=>
			array(
				'a'=>array(1=>'此email帳號已註冊,請重新輸入',2=>'This email is already registered.'),
				'b'=>array(1=>'會員新增完成',2=>'Account created'),
				'c'=>array(1=>'認證碼有誤',2=>'Your verification code is incorrect'),
				'd'=>array(1=>'密碼長度不足(8-16個字元)',2=>'Password too short(8-16 characters)'),
				'e'=>array(1=>'註冊欄位、欄位值筆數不符',2=>'註冊欄位、欄位值筆數不符'),
			),
			'send_certification_member'=>
			array(
				'a'=>array(1=>'驗證碼已發送至您的信箱',2=>'Verification code has been sent to your email'),
				'b'=>array(1=>'請輸入您收到的簡訊驗證碼,即可完成驗證動作',2=>'Please enter the SMS verification code.'),
				'c'=>array(1=>'您的會員認證碼為',2=>'Your member verification code is '),
				'd'=>array(1=>'會員認證信發送超過限制',2=>'會員認證信發送超過限制'),
				'e'=>array(1=>'不需要認證',2=>'不需要認證'),

			),
			'v_code_check'=>
			array(
				'a'=>array(1=>'認證已完成',2=>'Verification is complete'),
				'b'=>array(1=>'認證碼有誤，請重新輸入',2=>'認證碼有誤，請重新輸入'),
				'c'=>array(1=>'認證次數已達限制，無法再進行認證',2=>'認證次數已達限制，無法再進行認證')
			),
			'email_check'=>
			array(
				'a'=>array(1=>'帳號已存在',2=>'Account already exists'),
			),
			'login'=>
			array(
				'a'=>array(1=>'此帳號為待審核狀態',2=>'此帳號為待審核狀態'),
				'b'=>array(1=>'1-您目前缺少一些必要資訊,您必須填寫完成才可登入',2=>'You need to complete some  required informations befor you can sign-in'),
				'c'=>array(1=>'2-因會員政策調整,目前還缺少必要的會員認證,您必須完成認證才可登入',2=>'We have modified our member policy, now you have to pass the verification befor sign-in'),
				'd'=>array(1=>'此帳號已禁用',2=>'此帳號已禁用'),
				'e'=>array(1=>'帳號或密碼有誤，請重新輸入',2=>'Your account or password is incorrect, please re-enter'),
				'f'=>array(1=>'您的帳號尚未完成認證，請完成認證或重新註冊一次',2=>'您的帳號尚未完成認證，請完成認證或重新註冊一次'),
			),
			'social_login'=>
			array(
				'a'=>array(1=>'專人審核中,待審核完成即可登入使用',2=>'Manual review is required, pls wait.'),
				'b'=>array(1=>'1-您目前缺少一些必要資訊,您必須填寫完成才可登入',2=>'You need to complete some  required informations befor you can sign-in'),
				'c'=>array(1=>'2-因會員政策調整,目前還缺少必要的會員認證,您必須完成認證才可登入',2=>'We have modified our member policy, now you have to pass the verification befor sign-in'),
				'd'=>array(1=>'此帳號已禁用,請洽管理人員',2=>'This account has been disabled, please contact the administrator.'),
				'e'=>array(1=>'帳號或密碼有誤,請重新輸入',2=>'Your account or password is incorrect, please re-enter'),
			),
			'forget_pass'=>
			array(
				'a'=>array(1=>'密碼重設連結已成功寄送至您的信箱',2=>'密碼重設連結已成功寄送至您的信箱'),
				'b'=>array(1=>'查無此帳號',2=>'This account is not exist'),
			),
			'get_module_content'=>
			array(
				'a'=>array(1=>'訊息刪除完成',2=>'Message deleted.'),
				'b'=>array(1=>'取得會員資料失敗',2=>'Unable to get your account information.'),
				'c'=>array(1=>'個人資料修改完成',2=>'Data is updated.'),
				'd'=>array(1=>'密碼修改完成',2=>'You password is updated'),
				'e'=>array(1=>'此帳號已有綁定FB帳號,如要重新綁定請先解除原先綁定之帳號',2=>'This account already has a binding FB account,if you want to re-bind to another FB account, Pls un-bind the current FB accont first.'),
				'f'=>array(1=>'此帳號已有綁定新浪帳號,如要重新綁定請先解除原先綁定之帳號',2=>'This account already has a binding Sina account,if you want to re-bind to another Sina account, Pls un-bind the current Sina accont first. '),
				'g'=>array(1=>'FB帳號綁定已完成',2=>'FB account binding is complete'),
				'h'=>array(1=>'新浪帳號綁定已完成',2=>'Sina account binding is complete'),
				'i'=>array(1=>'尚無紅利記錄',2=>'No Point record.'),
				'j'=>array(1=>'尚無紅利內容',2=>'No Point description'),
				'k'=>array(1=>'尚無相關說明',2=>'No further information.'),
				'l'=>array(1=>'個人資料',2=>'Personal profile'),
				'm'=>array(1=>'email認證',2=>'Email verification'),
				'n'=>array(1=>'簡訊認證',2=>'SMS verification'),
				'o'=>array(1=>'密碼修改',2=>'Change password'),
				'p'=>array(1=>'尚無通知記錄',2=>'No message'),
				'q'=>array(1=>'FB解除綁定已完成',2=>'Facebook unbind succesful'),
				'r'=>array(1=>'新浪解除綁定已完成',2=>'Sina unbind succesful'),
			),
			'get_member_field'=>
			array(								
				'l'=>array(1=>'個人資料',2=>'Personal profile'),	
				'b'=>array(1=>'取得會員資料失敗',2=>'Unable to get your account information.'),			
			),
			'upload_photo'=>
			array(
				'a'=>array(1=>'格式錯誤',2=>'格式錯誤'),
				'b'=>array(1=>'格式大小超過限制',2=>'格式大小超過限制'),
				'c'=>array(1=>'上傳已完成',2=>'Upload is completed.'),
			),
			'get_members_points_record'=>
			array(
				'a'=>array(1=>'系統查無此筆資料',2=>'Data not found'),
			),
		);
?>