<?php
//防止恶意调用
if(!defined('IN_TG')){
	exit('禁止直接访问该文件!');
}

//关闭数据库
_close();
$_endtime=_now_time();
?>
<div id="footer">
	<p>本程序为电子科技大学学生项目（863403035@qq.com）</p>
	<p>版权所有 翻版必究</p>
	<p>当前页面用时：<?php echo $_endtime-$_starttime;?>秒</p>
</div>