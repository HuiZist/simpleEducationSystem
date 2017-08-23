<?php
session_start();
//调用授权
define('IN_TG',true);
//定义本页识别常量
define('SCRIPT','member');
//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//阻止非法登录
if(isset($_COOKIE['username'])){
	//获取数据
	$_rows=_fetch_array("SELECT
									es_username,
									es_nickname,
									es_sex,
									es_face,
									es_email,
									es_level,
									es_reg_time
							FROM
									es_user
							WHERE
									es_username='{$_COOKIE['username']}' LIMIT 1
	");
	if($_rows){
		$_html=array();
		$_html['username']=$_rows['es_username'];
		$_html['nickname']=$_rows['es_nickname'];
		$_html['sex']=$_rows['es_sex'];
		$_html['face']=$_rows['es_face'];
		$_html['email']=$_rows['es_email'];
		$_html['reg_time']=$_rows['es_reg_time'];
		switch($_rows['es_level']){
			case 1:
				$_html['level']='教师';
				break;
			case 2:
				$_html['level']='管理员';
				break;
			case 3:
				$_html['level']='超级管理员';
				break;
			default:
				$_html['level']='学员';
				break;
		}
		$_html=_html($_html);
	}else{
		_alert_back('此用户不存在！');
	}
}else{
	_alert_back('非法登录！');
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统个人中心</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>
<body>
<?php 
  require ROOT_PATH.'includes/header.inc.php';
?>
<div id="member">
<?php 
	require 'includes/member.inc.php';
?>
	<div id="member_main">
		<h2>账号信息</h2>
		<dl>
			<dd><span>账&nbsp;&nbsp;号：&nbsp;</span><?php echo $_html['username']?></dd>
			<dd><span>用 户 名：&nbsp;</span><?php echo $_html['nickname']?></dd>
			<dd><span>性&nbsp;&nbsp;别：&nbsp;</span><?php echo $_html['sex']?></dd>
			<dd><span>头&nbsp;&nbsp;像：&nbsp;</span><img src="<?php echo $_html['face']?>" alt="<?php echo $_html['face']?>"/></dd>
			<dd><span>电子邮件：&nbsp;</span><?php echo $_html['email']?></dd>
			<dd><span>注册时间：&nbsp;</span><?php echo $_html['reg_time']?></dd>
			<dd><span>权&nbsp;&nbsp;限：&nbsp;</span><?php echo $_html['level']?></dd>
		</dl>
	</div>
</div>
<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>