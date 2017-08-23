<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','message');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//必须登录才能发送消息
if (!isset($_COOKIE['username'])){
	_location('请先登录!', 'login.php');
}
//检查
if (!!$_row_post=_fetch_array("SELECT
			es_id,
			es_uniqid,
			es_face
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT
			1")){
	//为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	_uniqid($_row_post['es_uniqid'],$_COOKIE['uniqid']);
	//发送人和接收人不能一样
	_different_id($_row_post['es_id'],$_GET['to_id']);
	//接收发送的消息数据
	if ($_GET['action']=='message_post'){
		_query("INSERT INTO es_message (
											es_post_id,
											es_to_id,
											es_content,
											es_date,
											es_read
										)
											VALUES (
											'{$_row_post['es_id']}',
											'{$_POST['to_id']}',
											'{$_POST['content']}',
											NOW(),
											0
											)");
		if (_affected_rows() == 1) {
				_close();
				_location('消息发送成功！','message.php?to_id='.$_POST['to_id'].'#now');
			} else {
				_close();
				_alert_back('消息发送失败！');
			}
	}
	if (!!$_row_to = _fetch_array("SELECT
			es_nickname,
			es_face
			FROM
			es_user 
			WHERE 
			es_id='{$_GET['to_id']}'")){
		$_html_messages=array();
		$_html_messages['post_face']=$_row_post['es_face'];
		$_html_messages['to_nickname']=$_row_to['es_nickname'];
		$_html_messages['to_face']=$_row_to['es_face'];
		$_html_messages=_html($_html_messages);
	}else {
		_alert_close('接收人数据读取失败!');
	}
	//提取消息列表
	$_result_num = _query("SELECT es_id FROM es_message WHERE
			(es_post_id='{$_row_post['es_id']}' AND es_to_id='{$_GET['to_id']}') OR
			(es_post_id='{$_GET['to_id']}' AND es_to_id='{$_row_post['es_id']}')");
	$_message_num = mysql_num_rows($_result_num)-10;
	if ($_message_num<0){
		$_message_num=0;
	}
	if (!!$_result_messages = _query("SELECT
				es_id,
				es_post_id,
				es_to_id,
				es_content,
				es_date
			FROM
				es_message
			WHERE
			(es_post_id='{$_row_post['es_id']}' AND es_to_id='{$_GET['to_id']}') OR
			(es_post_id='{$_GET['to_id']}' AND es_to_id='{$_row_post['es_id']}')
			ORDER BY
				es_date ASC
				LIMIT $_message_num,10")){
				_query("UPDATE es_message 
				SET es_read=1 
				WHERE 
				es_post_id='{$_GET['to_id']}' AND es_to_id='{$_row_post['es_id']}'");
	}else{
		_alert_close('消息列表读取失败！');
	}
	
			
}else{
	_alert_close('非法登录！');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统发送消息</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/message.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="message">
<h2>对话者.<?php echo $_html_messages['to_nickname'];?></h2>
	<div id="message_list">
	<dl>
		<dt></dt>
		<?php 
		while (!!$_rows_messages=_fetch_array_list($_result_messages)){
			$_html_messages['post_id']=$_rows_messages['es_post_id'];
			$_html_messages['to_id']=$_rows_messages['es_to_id'];
			$_html_messages['content']=$_rows_messages['es_content'];
			$_html_messages['date']=$_rows_messages['es_date'];
			if ($_html_messages['post_id']==$_GET['to_id']){
		?>
			<dd>
			<div class="left"><img class="face" title="<?php echo $_html_messages['post_id'];?>" src="thumb.php?filename=<?php echo $_html_messages['to_face'];?>&percent=0.5"></div>
			<div class="mid_left">
				<p><?php echo _ubb($_html_messages['content']);?></p>
				<br><span class="date"><?php echo $_html_messages['date'];?></span>
			</div>
			<div class="right"></div>
			</dd>
		<?php }else{?>
			<dd>
			<div class="left"></div>
			<div class="mid_right">
				<p><?php echo _ubb($_html_messages['content']);?></p>
				<br><span class="date"><?php echo $_html_messages['date'];?></span>
			</div>
			<div class="right"><img class="face" title="<?php echo $_html_messages['post_id'];?>" src="thumb.php?filename=<?php echo $_html_messages['post_face'];?>&percent=0.5"></div>
			</dd>
		<?php }}?>
	</dl>
	<a name="now"></a> 
	</div>
	<div id="post">
		<form id="subform" method="post" action="?action=message_post">
		<input type="hidden" name="to_id" value="<?php echo $_GET['to_id']?>" />
		<dl>
			<dd id="q">贴　　图：　<a href="javascript:;">Q图系列[1]</a> <a href="javascript:;">Q图系列[2]</a> <a href="javascript:;">Q图系列[3]</a></dd>
			<dd>
				<?php include ROOT_PATH.'includes/ubb.inc.php'?>
				<textarea name="content" rows="9"></textarea>
				<input type="submit" class="submit" value="发送" />
			</dd>
		</dl>
		</form>
	</div>
</div>
<?php 
	require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>