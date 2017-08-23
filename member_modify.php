<?php
//调用授权
define('IN_TG',true);
//定义本页识别常量
define('SCRIPT','member_modify');
//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//接收修改的资料
if ($_GET['action']=='modify'){
	//防止恶意注册，跨站攻击
 	_check_code($_POST['code'],$_SESSION['code']);
 	if(!!$_rows=_fetch_array("SELECT es_uniqid FROM es_user WHERE es_username='{$_COOKIE['username']}' LIMIT 1")){
		//比对唯一标识符uniqid()，防止cookies伪造
		_uniqid($_rows['es_uniqid'], $_COOKIE['uniqid']);
		
		//导入验证文件
		include ROOT_PATH.'includes/check.func.php';
		$_clean=array();
		$_clean['nickname']=_check_nickname($_POST['nickname'],2,20);
		$_clean['sex']=_check_sex($_POST['sex']);
		$_clean['email']=_check_email($_POST['email'],6,40);
		
		//检查用户名是否已被使用
		_is_repeat(
		"SELECT es_nickname FROM es_user WHERE es_nickname='{$_clean['nickname']}'AND es_username!='{$_COOKIE['username']}' LIMIT 1",
		'该用户名已被注册！'
				);
		//修改资料
		_query("UPDATE es_user SET 
					es_nickname='{$_clean['nickname']}',
					es_sex='{$_clean['sex']}',
					es_email='{$_clean['email']}'
				WHERE
					es_username='{$_COOKIE['username']}'
					");
 	}else{
 		_alert_close('禁止非法登录！');
 	}
	//判断是否修改成功
	if(_affected_rows()==1){
		//关闭mysql连接
		_close();
		//跳转
		_location('恭喜你，修改成功！','member.php');
	}else{
		//关闭mysql连接
		_close();
		//跳转
		_location('没有数据修改！','member_modify.php');
	}
}
//阻止非法登录
if(isset($_COOKIE['username'])){
	//获取数据
	$_rows=_fetch_array("SELECT
									es_username,
									es_nickname,
									es_sex,
									es_email
							FROM
									es_user
							WHERE
									es_username='{$_COOKIE['username']}'
	");
	if($_rows){
		$_html=array();
		$_html['username']=$_rows['es_username'];
		$_html['nickname']=$_rows['es_nickname'];
		$_html['sex']=$_rows['es_sex'];
		$_html['email']=$_rows['es_email'];
		$_html=_html($_html);
		//性别
		if ($_html['sex']=='男'){
			$_html['sex_html']='<input type="radio" name="sex" value="男" checked="checked"/>男<input type="radio" name="sex" value="女"/>女 ';
		}elseif($_html['sex']=='女'){
			$_html['sex_html']='<input type="radio" name="sex" value="男"/>男<input type="radio" name="sex" value="女" checked="checked"/>女 ';
		}
		
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
<script type="text/javascript" src="js/member_modify.js"></script>
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
		<h2>修改信息</h2>
		<form method="post" action="member_modify.php?action=modify">
		<dl>
			<dd><span>账&nbsp;&nbsp;号：&nbsp;</span><?php echo $_html['username']?></dd>
			<dd><span>用 户 名：&nbsp;</span><input type="text" class="text" name="nickname" value="<?php echo $_html['nickname']?>"/></dd>
			<dd><span>性&nbsp;&nbsp;别：&nbsp;</span><?php echo $_html['sex_html']?></dd>
			<dd><span>头&nbsp;&nbsp;像：&nbsp;</span><a href="javascript:;" id="up">上传新的头像</a></dd>
			<dd><span>电子邮件：&nbsp;</span><input type="text" class="text" name="email" value="<?php echo $_html['email']?>"/></dd>
			<dd><span>密&nbsp;&nbsp;码：&nbsp;</span><a href="javascript:;" id="password_change">密码修改</a></dd>
			<dd><input type="submit" class="submit" value="修改信息"/></dd>
		</dl>
		</form>
	</div>
</div>
<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>