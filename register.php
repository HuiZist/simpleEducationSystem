<?php
//开启session功能
session_start();

//调用授权
define('IN_TG',true);

//定义本页识别常量
define('SCRIPT','register');

//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//登录状态判断
_login_state();

//判断是否提交了数据
if($_GET['action']=='register'){
	//防止恶意注册，跨站攻击
	_check_code($_POST['code'],$_SESSION['code']);
	//导入验证文件
	include ROOT_PATH.'includes/check.func.php';
	$_clean=array();
	//通过唯一标识符防止恶意注册，伪装表单跨站攻击，登录cookies验证
	$_clean['uniqid']=_check_uniqid($_POST['uniqid'],$_SESSION['uniqid']);
	//用户账号状态，默认为1
	$_clean['active']=1;
	$_clean['username']=_check_username($_POST['username'],2,20);
	$_clean['password']=_check_password($_POST['password'],$_POST['notpassword'],6,20);
	$_clean['question']=_check_question($_POST['question'],2,20);
	$_clean['answer']=_check_answer($_POST['question'],$_POST['answer'],2,20);
	$_clean['nickname']=_check_nickname($_POST['nickname'],2,20);
	$_clean['sex']=_check_sex($_POST['sex']);
	$_clean['email']=_check_email($_POST['email'],6,40);

	/* 限制只查找一条匹配的记录，将获得的sql记录集以关联数组形式输出，判断用户名是否重复 */
	_is_repeat(
	"SELECT es_username FROM es_user WHERE es_username='{$_clean['username']}'LIMIT 1",
	'该账号已被注册！'
			);
	_is_repeat(
	"SELECT es_nickname FROM es_user WHERE es_nickname='{$_clean['nickname']}'LIMIT 1",
	'该用户名已被注册！'
			);

	/* 新增用户，sql语句中双引号可以直接放变量名，但是放入数组必须加上{} */
	_query(
	"INSERT INTO es_user(
							es_uniqid,
							es_active,
							es_username,
							es_password,
							es_question,
							es_answer,
							es_nickname,
							es_sex,
							es_email,
							es_level,
							es_reg_time,
							es_last_time,
							es_last_ip,
							es_login_count
				)VALUES(
							'{$_clean['uniqid']}',
							'{$_clean['active']}',
							'{$_clean['username']}',
							'{$_clean['password']}',
							'{$_clean['question']}',
							'{$_clean['answer']}',
							'{$_clean['nickname']}',
							'{$_clean['sex']}',
							'{$_clean['email']}',
							0,
							NOW(),
							NOW(),
							'{$_SERVER["REMOTE_ADDR"]}',
							0
	)"
	);
	if(_affected_rows()==1){
		//关闭mysql连接
		_close();
		//清空session内存
		_session_destroy();
		//跳转
		_location('恭喜你，注册成功！','login.php');
	}else{
		//关闭mysql连接
		_close();
		_session_destroy();
		//跳转
		_location('很遗憾，注册失败！','register.php');
	}

}else{
	//生成唯一标识符
	$_SESSION['uniqid']=$_uniqid=_sha1_uniqid();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统注册</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/code.js"></script>
<script type="text/javascript" src="js/register.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="register">
	<h2>学员注册</h2>
	<form method="post" name="register" action="register.php?action=register">
	<input type="hidden" name="uniqid" value="<?php echo $_uniqid?>"/>
	<dl>
		<dt></dt>
		<dd>账&emsp;&emsp;号：<input type="text" name="username" class="text"/><img src="images/help.jpg" class="help" title="账号长度不能小于2位或大于20位&#10账号只能由字母、数字或下划线组成"/></dd>
		<dd>密&emsp;&emsp;码：<input type="password" name="password" class="text"/><img src="images/help.jpg" class="help" title="密码长度不能小于6位或大于20位&#10确认密码需与密码一致"/></dd>
		<dd>确认密码：<input type="password" name="notpassword" class="text"/></dd>
		<dd>密码提示：<input type="text" name="question" class="text"/><img src="images/help.jpg" class="help" title="密码问题不能小于2位或大于20位&#10密码问题用于找回密码"/></dd>
		<dd>密码回答：<input type="text" name="answer" class="text"/><img src="images/help.jpg" class="help" title="密码回答不能小于2位或大于20位&#10密码回答不能与密码问题一致&#10请填写保密度较好的回答"/></dd>
		<dd>用&ensp;户&ensp;名：<input type="text" name="nickname" class="text"/><img src="images/help.jpg" class="help" title="用户名问题不能小于2位或大于20位&#10部分敏感字符请勿注册"/></dd>
		<dd>性&emsp;&emsp;别：
			<input type="radio" class="sex" name="sex" value="男" checked="checked"/>男
			<input type="radio" class="sex" name="sex" value="女"/>女
		</dd>
		<dd>电子邮件：<input type="text" name="email" class="text"/><img src="images/help.jpg" class="help" title="电子邮件长度不能小于2位或大于40位&#10请按照正确格式填写电子邮件"/></dd>		
		<dd>验&ensp;证&ensp;码：<input type="text" name="code" class="text yzm"/><img src="code.php" id="code"/></dd>
		<dd><input type="submit" class="submit" value="注册"/></dd>
	</dl>
	</form>
</div>
<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>