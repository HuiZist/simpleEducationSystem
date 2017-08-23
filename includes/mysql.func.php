<?php
/* 防止恶意调用 */
if(!defined('IN_TG')){
	exit('禁止直接访问该文件!');
}

/* 连接数据库 */
function _connect(){
	//global表示全局变量，使$_conn能在函数外被调用
	global $_conn;
	if(!$_conn=mysql_connect(DB_HOST,DB_USER,DB_PWD)){
		exit('数据连接失败！');
	}
}

/* 选择数据库 */
function _select_db(){
	if(!mysql_select_db(DB_NAME)){
		exit('指定的数据库不存在！');
	}
}

/* 选择字符集 */
function _set_names(){
	if(!mysql_query('SET NAMES UTF8')){
		exit('字符集错误！');
	}
}

/* 执行SQL语句 */
function _query($_sql){
	if(!$_result=mysql_query($_sql)){
		exit('sql执行失败！'.mysql_error());
	}
	return $_result;
}

/* 将结果集放入数组,输入是sql语句，只能用于获取一条数据组 */
function _fetch_array($_sql){
	return mysql_fetch_array(_query($_sql),MYSQL_ASSOC);
}

/* 将结果集放入数组，输入是结果集，可以用于获取一系列数据组 */
function _fetch_array_list($_result){
	return mysql_fetch_array($_result,MYSQL_ASSOC);
}

/* 返回结果集的行数 */
function _num_rows($_result){
	return mysql_num_rows($_result);
}

/* 表示影响到的记录数 */
function _affected_rows(){
	return mysql_affected_rows();
}

/* 释放结果集 */
function _free_result($_result){
	mysql_free_result($_result);
}

/* 获取数据表最新的id */
function _insert_id(){
	return mysql_insert_id();
}

/* 判断是否已存在该项数据 */
function _is_repeat($_sql,$_info){
	if(_fetch_array($_sql)){
		_alert_back($_info);
	}
}

/* 关闭连接 */
function _close(){
	if(!mysql_close()){
		exit('数据库关闭异常！');
	}
}