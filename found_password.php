<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','found_password');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//输入账号
if ($_GET['action']=='search'){
	if ($_row_search=_fetch_array("SELECT es_question FROM es_user WHERE es_username='{$_POST['username']}' LIMIT 1")){
		$_html=array();
		$_html['username']=$_POST['username'];
		$_html['question']=$_row_search['es_question'];
		$_html=_html($_html);
	}else {
		_alert_back('没有这个账号！');
	}
}

//修改密码
if ($_GET['action']=='change'){
	if ($_row_change=_fetch_array("SELECT es_answer FROM es_user WHERE es_username='{$_POST['username']}' LIMIT 1")){
		//导入验证文件
		include ROOT_PATH.'includes/check.func.php';
		$_clean=array();
		_check_found_answer($_POST['answer'],$_row_change['es_answer']);
		$_clean['password']=_check_password($_POST['newps'],$_POST['reps'],6,20);
		_query("UPDATE es_user SET es_password='{$_clean['password']}' WHERE es_username='{$_POST['username']}'");
		if (_affected_rows() == 1) {
				_close();
				_location('密码修改成功！','login.php');
			} else {
				_close();
				_alert_back('密码修改失败！');
			}
		
	}else {
		_alert_back('没有这个账号！');
	}
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工教学系统账号找回</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/found_password.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="user">
	<?php if (!isset($_GET['action'])){?>
	<form method="post" name="search" action="found_password.php?action=search">
		<dl>
		<dd>请输入账号:<input type="text" name="username" class="text"/><input type="submit" value="确定" class="button"></dd>
		</dl>
	</form>
	<?php }?>
	<?php if ($_GET['action']=='search'){?>
	<form method="post" name="change" action="found_password.php?action=change" id="change">
		<dl>
		<dd><input type="hidden" name="username" value="<?php echo $_html['username']?>"></dd>
		<dd>账号：<?php echo $_html['username']?></dd>
		<dd>密码问题：<?php echo $_html['question'];?></dd>
		<dd>请输入密码回答：<input type="text" name="answer" class="text"/></dd>
		<dd>请输入新的密码：<input type="password" name="newps" class="text"></dd>
		<dd>请确认新的密码：<input type="password" name="reps" class="text"></dd>
		<dd><input type="submit" value="确定" id="submit"></dd>
		</dl>
	</form>
	<?php }?>
</div>
<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>