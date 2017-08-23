<?php
session_start();

//调用授权
define('IN_TG',true);

//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//删除表单
_unsetcookies();
_session_destroy();
?>