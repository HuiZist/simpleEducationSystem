<?php
//防止恶意调用
if(!defined('IN_TG')){
	exit('禁止直接访问该文件!');
}

//设置字符集编码
header('Content-Type:text/html;charset=utf-8');

//转换绝对路径常量，去除后几位，只留主目录路径 
define('ROOT_PATH',substr(dirname(__FILE__),0,-8));

//拒绝PHP低版本
if(PHP_VERSION<'4.1.0'){
	exit('PHP版本过低!');
}

//导入核心函数库
require ROOT_PATH.'includes/global.func.php';
require ROOT_PATH.'includes/mysql.func.php';

//连接数据库
define('DB_HOST','XXX');
define('DB_USER','XXX');
define('DB_PWD','XXX');
define('DB_NAME','XXX');

//初始化数据库
_connect();
_select_db();
_set_names();

//程序起始时间
$_starttime=_now_time();
