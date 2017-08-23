<?php
//开启session
session_start();

//调用授权
define('IN_TG',true);

//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

/* 运行验证码函数,默认验证码为4位，可输入参数调整位数 */
_code();
