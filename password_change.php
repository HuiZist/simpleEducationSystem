<?php
session_start();
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','password_change');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//用户才能进入
if (!$_COOKIE['username']) {
	_alert_back('非法登录！');
}
if ($_GET['action']=='change'){
	//防止恶意注册，跨站攻击
	_check_code($_POST['code'],$_SESSION['code']);
	if(!!$_rows=_fetch_array("SELECT es_uniqid FROM es_user WHERE es_username='{$_COOKIE['username']}' LIMIT 1")){
		//比对唯一标识符uniqid()，防止cookies伪造
		_uniqid($_rows['es_uniqid'], $_COOKIE['uniqid']);
		//导入验证文件
		include ROOT_PATH.'includes/login.func.php';
		//接收数据
		$_clean=array();
		$_clean['password']=_check_password($_POST['password'],6,20);
		$_clean['newpassword']=_check_password($_POST['newpassword'],6,20);
		//修改密码
		_query("UPDATE 
						es_user
				SET
						es_password='{$_clean['newpassword']}'
				WHERE
						es_username='{$_COOKIE['username']}' AND es_password='{$_clean['password']}' LIMIT 1
		");
	}else{
		_alert_close('禁止非法登录！');
	}
	//判断是否修改成功
	if(_affected_rows()==1){
		//关闭mysql连接
		_close();
		//跳转
		_alert_close('密码修改成功！');
	}else{
		//关闭mysql连接
		_close();
		//跳转
		_alert_close('密码修改失败！');
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工教学系统密码修改</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/code.js"></script>
<script type="text/javascript" src="js/password_change.js"></script>
</head>
<body>
<div id="password_change">
	<h2>密码修改</h2>
	<form method="post" name="change" action="password_change.php?action=change">
		<dl>
		<dt></dt>
		<dd><span>密&nbsp;&nbsp;码: </span><input type="password" name="password" class="text"/></dd>
		<dd><span>新 密 码: </span><input type="password" name="newpassword" class="text"/></dd>
		<dd><span>确认密码: </span><input type="password" name="notpassword" class="text"/></dd>
		<dd><span>验 证 码: </span><input type="text" name="code" class="text yzm"/><img src="code.php" id="code"/></dd>
		<dd><input type="submit" value="确认修改" class="submit"/></dd>
		</dl>
	</form>
</div>
</body>
</html>