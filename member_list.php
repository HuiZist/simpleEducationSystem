<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','member_list');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//必须登录
if (!isset($_COOKIE['username'])){
	_location('请先登录!', 'login.php');
}
//更改权限为教师或学生
if (($_GET['action']=='student' || $_GET['action']=='teacher') && isset($_GET['user_id'])){
	//检查
	if (!!$_row_user=_fetch_array("SELECT
			es_uniqid,
			es_level
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT
			1")){
		//为了防止cookies伪造，还要比对一下唯一标识符uniqid()
		_uniqid($_row_user['es_uniqid'],$_COOKIE['uniqid']);
		_examine_level($_row_user['es_level'],2);
		//更改权限
		if ($_GET['action']=='student'){
			_query("UPDATE es_user SET es_level=0 WHERE es_id='{$_GET['user_id']}'");
		}
		if ($_GET['action']=='teacher'){
			_query("UPDATE es_user SET es_level=1 WHERE es_id='{$_GET['user_id']}'");
		}
		if (_affected_rows() == 1) {
			_close();
			_location('更改权限成功！','member_list.php');
		} else {
			_close();
			_alert_back('更改权限失败！');
		}
	}else {
		_alert_back('非法登录！');
	}
}
//更改权限为管理员
if (($_GET['action']=='manager') && isset($_GET['user_id'])){
	//检查
	if (!!$_row_user=_fetch_array("SELECT
			es_uniqid,
			es_level
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT
			1")){
			//为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	_uniqid($_row_user['es_uniqid'],$_COOKIE['uniqid']);
	_examine_level($_row_user['es_level'],3);
	//更改权限
	_query("UPDATE es_user SET es_level=2 WHERE es_id='{$_GET['user_id']}'");
	
	if (_affected_rows() == 1) {
		_close();
		_location('更改权限成功！','member_list.php');
	} else {
		_close();
		_alert_back('更改权限失败！');
	}
	}else {
		_alert_back('非法登录！');
	}
}
//检查
if (!!$_row_user=_fetch_array("SELECT
		es_uniqid,
		es_level
		FROM
		es_user
		WHERE
		es_username='{$_COOKIE['username']}'
		LIMIT
		1")){
	//为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	_uniqid($_row_user['es_uniqid'],$_COOKIE['uniqid']);
	_examine_level($_row_user['es_level'],2);
	
	//帖子列表
	if ($_GET['action']=='search'){
		$_result = _query("SELECT
				es_id,
				es_username,
				es_nickname,
				es_sex,
				es_face,
				es_email,
				es_level,
				es_reg_time,
				es_last_time,
				es_last_ip,
				es_login_count
				FROM
				es_user
				WHERE
				es_username='{$_POST['username']}'
				LIMIT 1
				");
	}else {
		global $_pagesize,$_pagenum,$_page;
		_page("SELECT es_id FROM es_user",5);
		$_result = _query("SELECT
				es_id,
				es_username,
				es_nickname,
				es_sex,
				es_face,
				es_email,
				es_level,
				es_reg_time,
				es_last_time,
				es_last_ip,
				es_login_count
				FROM
				es_user
				ORDER BY
				es_reg_time DESC
				LIMIT
				$_pagenum,$_pagesize
				");
	}
	$_html=array();
}else {
	_alert_back('非法登录！');
}



?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统用户列表</title>
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
<h2>用户列表</h2>
<div id="search_allarea">
	<form method="post" name="search" action="member_list.php?action=search">
		<dl>
		<dd><span>账号搜索:</span>&nbsp;<input type="text" name="username" id="search_input"/><input type="submit" value="搜索" class="button"/></dd>
		</dl>
	</form>
</div>
	<dl>
	<?php 
	while (!!$_rows=_fetch_array_list($_result)){
		$_html['id'] = $_rows['es_id'];
		$_html['username'] = $_rows['es_username'];
		$_html['nickname'] = $_rows['es_nickname'];
		$_html['sex'] = $_rows['es_sex'];
		$_html['face'] = $_rows['es_face'];
		$_html['email'] = $_rows['es_email'];
		$_html['level'] = $_rows['es_level'];
		switch ($_html['level']){
			case 1:
				$_html['levelname'] = '教师';
				break;
			case 2:
				$_html['levelname'] = '管理员';
				break;
			case 3:
				$_html['levelname'] = '超级管理员';
				break;
			default:
				$_html['levelname'] = '学员';
				break;
		}
		$_html['reg_time'] = $_rows['es_reg_time'];
		$_html['last_time'] = $_rows['es_last_time'];
		$_html['last_ip'] = $_rows['es_last_ip'];
		$_html['login_count'] = $_rows['es_login_count'];
		$_html['user_level'] = $_row_user['es_level'];
		$_html=_html($_html);
	?>
	<dd><img alt="头像" src="thumb.php?filename=<?php echo $_html['face'];?>&percent=0.3" title="<?php echo $_html['id'];?>">
	<?php echo '&nbsp;<span>账号:</span>&nbsp;'.$_html['username'].'&nbsp;<span>昵称:</span>&nbsp;'.$_html['nickname'].'&nbsp;<span>性别:</span>&nbsp;'.$_html['sex'].'&nbsp;<span>电子邮箱:</span>&nbsp;'.$_html['email'].'&nbsp;<span>登录次数:</span>&nbsp;'.$_html['login_count'].'<br><span>注册时间:</span>&nbsp;'.$_html['reg_time'].'&nbsp;<span>最近登录时间:</span>&nbsp;'.$_html['last_time'].'&nbsp;<span>最近登录IP:</span>&nbsp;'.$_html['last_ip'].'<br><span>用户权限:</span>&nbsp;'.$_html['levelname'];?>
	<?php if ($_html['user_level']==3){
			if ($_html['level']<$_html['user_level']){
				if ($_html['level']==0){
					echo '<a href="member_list.php?action=teacher&user_id='.$_html['id'].'">设为教师</a><a href="member_list.php?action=manager&user_id='.$_html['id'].'">设为管理员</a>';
				}elseif ($_html['level']==1){
					echo '<a href="member_list.php?action=student&user_id='.$_html['id'].'">设为学员</a><a href="member_list.php?action=manager&user_id='.$_html['id'].'">设为管理员</a>';
				}elseif ($_html['level']==2){
					echo '<a href="member_list.php?action=student&user_id='.$_html['id'].'">设为学员</a><a href="member_list.php?action=teacher&user_id='.$_html['id'].'">设为教师</a>';
				}
			}
		}
		if ($_html['user_level']==2){
			if ($_html['level']<$_html['user_level']){
				if ($_html['level']==0){
					echo '<a href="member_list.php?action=teacher&user_id='.$_html['id'].'">设为教师</a>';
				}elseif ($_html['level']==1){
					echo '<a href="member_list.php?action=student&user_id='.$_html['id'].'">设为学员</a>';
				}
			}
		}
	?>
	</dd>
	<?php } if ($_GET['action']!=='search'){_paging(2);}?>
	</dl>
</div>
</div>
<?php 
	require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>