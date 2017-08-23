<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','message_list');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//必须登录才能发送消息
if (!isset($_COOKIE['username'])){
	_location('请先登录!', 'login.php');
}
//检查
if (!!$_row_user=_fetch_array("SELECT
		es_id,
		es_uniqid
		FROM
		es_user
		WHERE
		es_username='{$_COOKIE['username']}'
		LIMIT
		1")){
	//为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	_uniqid($_row_user['es_uniqid'],$_COOKIE['uniqid']);
	$_html=array();
	$_html['id_user']=$_row_user['es_id'];
}

//消息列表
!!$_result_message=_query("SELECT 
		es_id,
		es_post_id,
		es_to_id
		FROM
		es_message
		WHERE
		es_post_id='{$_html['id_user']}' OR es_to_id='{$_html['id_user']}'
		ORDER BY
		es_date DESC
");

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统消息列表</title>
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
<h2>私信列表</h2>
	<dl>
	<?php 
	$_toid=array();
	while (!!$_rows=_fetch_array_list($_result_message)){
		if (in_array($_rows['es_post_id'], $_toid) || in_array($_rows['es_to_id'], $_toid)){
			continue;
		}
		if ($_rows['es_post_id']!=$_html['id_user']){
			array_push($_toid, $_rows['es_post_id']);
			$_html['id_chatter']=$_rows['es_post_id'];
		}
		if ($_rows['es_to_id']!=$_html['id_user']){
			array_push($_toid, $_rows['es_to_id']);
			$_html['id_chatter']=$_rows['es_to_id'];
		}
		if (!!$_row_message = _fetch_array("SELECT
			es_content,
			es_date,
			es_read
			FROM
			es_message 
			WHERE 
			es_id='{$_rows['es_id']}'")){
			if (!!$_row_to=_fetch_array("SELECT
					es_nickname,
					es_face
					FROM
					es_user
					WHERE 
					es_id='{$_html['id_chatter']}'")){
					$_html['nickname_to']=$_row_to['es_nickname'];
					$_html['face_to']=$_row_to['es_face'];
					$_html['content']=$_row_message['es_content'];
					$_html['date']=$_row_message['es_date'];
					$_html['read']=$_row_message['es_read'];
					$_html=_html($_html);
			}else{
				_alert_back('读取对话人信息失败!');
			}
		}else{
			_alert_back('读取对话信息失败!');
		}

	?>
		<a class="<?php if($_html['read']==0 && $_rows['es_to_id']==$_html['id_user']){echo 'unread';} else{echo 'read';}?>" href="message.php?to_id=<?php echo $_html['id_chatter'];?>#now">
		<dd>
			<img title="<?php echo $_html['nickname_to']?>" src="thumb.php?filename=<?php echo $_html['face_to'];?>&percent=0.5">
			<?php echo _ubb(_title($_html['content'],32));?>
			<p><?php echo $_html['date'];?></p>
		</dd>
		</a>
	<?php }?>
	</dl>
</div>
</div>
<?php 
	require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>