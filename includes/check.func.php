<?php
/*防止恶意调用 */
if(!defined('IN_TG')){
	exit('Access Defined!');
}

/* 检查_alert_back()函数是否存在 */
if(!function_exists('_alert_back')){
	exit('_alert_back()函数不存在，请检查！');
}

/* 检查_mysql_string()函数是否存在 */
if(!function_exists('_mysql_string')){
	exit('_mysql_string()函数不存在，请检查！');
}

/* 检查唯一标识符 */
function _check_uniqid($_first_uniqid,$_end_uniqid){
	if((strlen($_first_uniqid)!=40)||($_first_uniqid!=$_end_uniqid)){
		_alert_back('唯一标识符异常！');
	}
	return _mysql_string($_first_uniqid);
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
function _check_password($_first_pass,$_end_pass,$_min_num,$_max_num){
	if(strlen($_first_pass)<$_min_num||strlen($_string)>$_max_num){
		_alert_back('密码长度不能小于'.$_min_num.'位或大于'.$_max_num.'位!');
	}
	if($_first_pass!=$_end_pass){
		_alert_back('密码和确认密码不一致！');
	}
	return _mysql_string(sha1($_first_pass));
}

/* 检查密码提示问题 */
function _check_question($_question,$_min_num,$_max_num){
	$_string=trim($_string);
	if(mb_strlen($_question,'utf8')<$_min_num||mb_strlen($_question,'utf-8')>$_max_num){
		_alert_back('密码问题不能小于'.$_min_num.'位或大于'.$_max_num.'位');
	}
	return _mysql_string($_question);
}

/* 检查回答 */
function _check_answer($_question,$_answer,$_min_num,$_max_num){
	$_answer=trim($_answer);
	//长度不能小于4位或20位
	if(mb_strlen($_answer,'utf8')<$_min_num||mb_strlen($_answer,'utf-8')>$_max_num){
		_alert_back('密码回答不能小于'.$_min_num.'位或大于'.$_max_num.'位');
	}
	//密码回答与问题不能一致
	if($_question==$_answer){
		_alert_back('密码提示与问题不能一致!');
	}
	//加密返回值
	return _mysql_string(sha1($_answer));
}

/* 检查用户名合法性 */
function _check_nickname($_string,$_min_num,$_max_num){
	//去掉两边空格
	$_string=trim($_string);
	//长度小于$_min_num或大于$_max_num
	if(mb_strlen($_string,'utf8')<$_min_num||mb_strlen($_string,'utf-8')>$_max_num){
		_alert_back('名字长度不能小于'.$_min_num.'位或大于'.$_max_num.'位');
	}

	//用丨分割敏感字符串
	$_mg=explode('|','习近平|胡锦涛|毛泽东');
	//声明不能注册的ID
	foreach($_mg as $value){
		$_mg_string.='['.$value.']'.'\n';
	}
	if(in_array($_string,$_mg)){
		_alert_back($_mg_string.'以上敏感用户名不得注册！');
	}

	//将用户名转义
	return _mysql_string($_string);
}

/* 检查性别 */
function _check_sex($_string){
	return _mysql_string($_string);
}

/* 检查email */
function _check_email($_string,$_min_num,$_max_num){
	//如果不为空则进行正则判定
	if(!preg_match('/^[\w\_\.]+@[\w\_\.]+(\.\w+)+$/',$_string)){
		_alert_back('邮件格式不正确！');
	}
	if(strlen($_string)<$_min_num||strlen($_string)>$_max_num){
		_alert_back('邮件长度不合法！');
	}
	
	return _mysql_string($_string);
}

/* 检查发帖标题 */
function _check_post_title($_string,$_min,$_max) {
	if (mb_strlen($_string,'utf-8') < $_min || mb_strlen($_string,'utf-8') > $_max) {
		_alert_back('标题内容不得小于'.$_min.'位大于'.$_max.'位！');
	}
	return $_string;
}

/* 检查发帖内容 */
function _check_post_content($_string,$_num) {
	if (mb_strlen($_string,'utf-8') < $_num) {
		_alert_back('内容不得小于'.$_num.'位！');
	}
	return $_string;
}

/* 检查密码回答是否正确 */
function _check_found_answer($_your_answer,$_right_answer){
	$_your_answer=_mysql_string(sha1($_your_answer));
	if ($_your_answer!=$_right_answer){
		_alert_back('密码回答不正确！');
	}
}
