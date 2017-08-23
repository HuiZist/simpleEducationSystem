<?php
/*防止恶意调用 */
if(!defined('IN_TG')){
	exit('禁止直接访问该文件!');
}
//获取数据
if(isset($_COOKIE['username'])){
	//用户信息
	$_member_sidebar=_fetch_array("SELECT
											es_id,
											es_level
											FROM
											es_user
											WHERE
											es_username='{$_COOKIE['username']}'
			");
	//用户未读短信
	$_message_unread=_query("SELECT
											es_id
											FROM
											es_message
											WHERE
											es_to_id='{$_member_sidebar['es_id']}' AND es_read=0");
	$_message_unread_num=mysql_num_rows($_message_unread);
	//用户未读帖子回复
	$_invitation_unread=_query("SELECT
			es_id
			FROM
			es_article
			WHERE
			es_userid='{$_member_sidebar['es_id']}' AND es_username!='{$_COOKIE['username']}' AND es_read=0");
	$_invitation_unread_num=mysql_num_rows($_invitation_unread);
}
?>
<link rel="stylesheet" type="text/css" href="styles/1/member_sidebar.css"/>
<div id="member_sidebar">
		<dl>
			<dt>账号</dt>
			<dd><a href="member.php">账号信息</a></dd>
			<dd><a href="member_modify.php">修改信息</a></dd>
		</dl>
		<dl>
			<dt>消息</dt>
			<?php if ($_message_unread_num==0){?>
				<dd><a href="message_list.php">我的私信</a></dd>
			<?php }else {?>
				<dd class="message_unread"><a href="message_list.php">我的私信<span><?php echo $_message_unread_num;?></span></a></dd>
			<?php }?>
			<dd><a href="message_invitation.php">我的论坛</a></dd>
			<?php if ($_invitation_unread_num==0){?>
				<dd><a href="message_reply.php">论坛回复</a></dd>
			<?php }else{?>
				<dd class="message_unread"><a href="message_reply.php">论坛回复<span><?php echo $_invitation_unread_num;?></span></a></dd>
			<?php }?>
		</dl>
		<dl>
			<dt>课程</dt>
			<dd><a href=###>学习信息</a></dd>
			<dd><a href=###>考核信息</a></dd>
			<?php if($_member_sidebar['es_level']>=1){?>
			<dd><a href=lesson_post.php>课程发布</a></dd>
			<dd><a href=###>考核查询</a></dd>
			<?php }?>
		</dl>
		<?php if($_member_sidebar['es_level']>=2){?>
		<dl>
			<dt>管理</dt>
			<dd><a href="notice_post.php">公告发布</a></dd>
			<dd><a href="news_post.php">新闻发布</a></dd>
			<dd><a href="manage.php">后台版本</a></dd>
			<dd><a href=###>网站设置</a></dd>
			<dd><a href="member_list.php">用户列表</a></dd>
		</dl>
		<?php }?>
	</div>