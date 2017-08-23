<?php
//开启session功能
session_start();

//调用授权
define('IN_TG',true);

//定义本页识别常量
define('SCRIPT','login');

//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//登录状态判断
_login_state();

//开始处理登录状态
if($_GET['action']=='login'){
	//防止恶意注册，跨站攻击
	_check_code($_POST['code'],$_SESSION['code']);
	//导入验证文件
	include ROOT_PATH.'includes/login.func.php';
	//接收数据
	$_clean=array();
	$_clean['username']=_check_username($_POST['username'],2,20);
	$_clean['password']=_check_password($_POST['password'],6,20);
	$_clean['time']=_check_time($_POST['time']);
	if(!!$_rows=_fetch_array("SELECT 
										es_username,
										es_uniqid,
										es_level 
								FROM
										es_user
								WHERE
										es_username='{$_clean['username']}' AND es_password='{$_clean['password']}' AND es_active=1
								LIMIT 1")
	){
		//登录成功后，记录登录信息
		_query("UPDATE 
						es_user
				SET
						es_last_time=NOW(),
						es_last_ip='{$_SERVER["REMOTE_ADDR"]}',
						es_login_count=es_login_count+1
				WHERE
						es_username='{$_clean['username']}'
		");

		_setcookies($_rows['es_username'],$_rows['es_uniqid'],$_clean['time']);

		_close();
		//PHP方法直接跳转，不弹窗
		_location(null, 'index.php');
	}else{
		_close();
		_location('账号不存在或密码不正确！','login.php');
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统登录</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/code.js"></script>
<script type="text/javascript" src="js/login.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="login">
	<h2>登录</h2>
	<form method="post" name="login" action="login.php?action=login">
	<dl>
		<dt></dt>
		<dd>账号：<input type="text" name="username" class="text"/></dd>
		<dd>密码：<input type="password" name="password" class="text"/></dd>
		<dd>保留：
			<select name="time">
				<option value="0">不保留</option>
				<option value="1" selected="selected">保留一天</option>
				<option value="2">保留一周</option>
				<option value="3">保留一月</option>
			</select>
		</dd>
		<dd>验证码：<input type="text" name="code" class="text code"/><img src="code.php" id="code"/></dd>
		<dd><input type="submit" value="登录" class="button"/></dd>
	</dl>
	</form>
	<a href="register.php" class="register">立即注册</a>
	<a href="found_password.php" class="found_password">找回密码</a>
</div>
<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>