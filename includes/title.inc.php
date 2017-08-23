<?php
//防止恶意调用
if(!defined('IN_TG')){
	exit('禁止直接访问该文件!');
}

//防止非HTML页面调用
if(!defined('SCRIPT')){
	exit('该页面未定义!');
}
?>
<!-- 导入图标LOGO -->
<link rel="shortcut icon" href="images/uestclogo.jpg" />
<link rel="stylesheet" type="text/css" href="styles/1/basic.css"/>
<link rel="stylesheet" type="text/css" href="styles/1/<?php echo SCRIPT?>.css"/>
