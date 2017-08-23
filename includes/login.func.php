<?php
/*防止恶意调用 */
if(!defined('IN_TG')){
	exit('禁止直接访问该文件!');
}

/* 检查_alert_back()函数是否存在 */
if(!function_exists('_alert_back')){
	exit('_alert_back()函数不存在，请检查！');
}

/* 检查_mysql_string()函数是否存在 */
if(!function_exists('_mysql_string')){
	exit('_mysql_string()函数不存在，请检查！');
}

/* 生成登录cookie */
function _setcookies($_username,$_uniqid,$_time){
	switch($_time){
		case'1'://一天
			setcookie('username',$_username,time()+86400);
			setcookie('uniqid',$_uniqid,time()+86400);
			break;
		case'2'://一周
			setcookie('username',$_username,time()+604800);
			setcookie('uniqid',$_uniqid,time()+604800);
			break;
		case'3'://一月
			setcookie('username',$_username,time()+2592000);
			setcookie('uniqid',$_uniqid,time()+2592000);
			break;
		default://浏览器进程
			setcookie('username',$_username);
			setcookie('uniqid',$_uniqid);
			break;
	}
}

/* 检查账号合法性 */
function _check_username($_string,$_min_num,$_max_num){
	//去掉两边空格
	$_string=trim($_string);
	
	//长度小于$_min_num或大于$_max_num
	if(strlen($_string)<$_min_num||strlen($_string)>$_max_num){
		_alert_back('账号长度不能小于'.$_min_num.'位或大于'.$_max_num.'位!');
	}
	//账号只能由数字、字母、下划线组成
	if(!preg_match('/\w+/',$_string)){
		_alert_back('账号只能由字母、数字或下划线组成！');
	}
	
	//将用户名转义
	return _mysql_string($_string);
}

/* 检查密码 */
function _check_password($_string,$_min_num,$_max_num){
	if(strlen($_string)<$_min_num||strlen($_string)>$_max_num){
		_alert_back('密码长度不能小于'.$_min_num.'位或大于'.$_max_num.'位!');
	}
	
	return _mysql_string(sha1($_string));
}

/* 检查cookie保存时间 */
function _check_time($_string){
	$_time=array(0,1,2,3);
	if(!in_array($_string, $_time)){
		_alert_back('保留方式出错！');
	}
	return _mysql_string($_string);				
}
