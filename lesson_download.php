<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

header("Content-type:text/html;charset=utf-8");
$file_name=$_GET['filename'];
$file_name=iconv("utf-8","gb2312",$file_name);
$file_path=ROOT_PATH.$file_name;
$file_name=basename($file_name);
//首先要判断给定的文件存在与否
if(!file_exists($file_path)){
	_alert_back('该文件不存在！');
	return ;
}
$file_size=filesize($file_path);

//返回的文件
header("Content-type: application/octet-stream");
//按照字节大小返回
header("Accept-Ranges: bytes");
//返回文件大小
header("Accept-Length: $file_size");
//这里客户端的弹出对话框，对应的文件名
header("Content-Disposition: attachment; filename=".$file_name);
//此函数用来丢弃输出缓冲区中的内容
ob_clean();
//刷新输出缓冲
flush();
readfile($file_path);
exit;


