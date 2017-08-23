<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','message_invitation');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//必须登录才能发送消息
if (!isset($_COOKIE['username'])){
	_location('请先登录!', 'login.php');
}
//检查
if (!!$_row_user=_fetch_array("SELECT
		es_uniqid
		FROM
		es_user
		WHERE
		es_username='{$_COOKIE['username']}'
		LIMIT
		1")){
	//为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	_uniqid($_row_user['es_uniqid'],$_COOKIE['uniqid']);
	//帖子列表
	global $_pagesize,$_pagenum,$_page;
	_page("SELECT es_id FROM es_article WHERE es_username='{$_COOKIE['username']}' AND es_reid=0",10);
	$_result = _query("SELECT
			es_id,
			es_title,
			es_date
			FROM
			es_article
			WHERE
			es_username='{$_COOKIE['username']}' AND es_reid=0
			ORDER BY
			es_date DESC
			LIMIT
			$_pagenum,$_pagesize
			");
	$_html=array();
}else {
	_alert_back('非法登录！');
}



?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统我的论坛</title>
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
<h2>我的论坛</h2>
	<dl>
	<?php 
	while (!!$_rows=_fetch_array_list($_result)){
		$_html['id']=$_rows['es_id'];
		$_html['title']=$_rows['es_title'];
		$_html['date']=$_rows['es_date'];
		$_html=_html($_html);
	?>
	<dd><a href="article.php?id=<?php echo $_html['id'];?>"><?php echo _title($_html['title'],30);?><span><?php echo $_html['date'];?></span></a></dd>
	<?php }_paging(2);?>
	</dl>
</div>
</div>
<?php 
	require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>