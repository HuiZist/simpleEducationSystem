<?php 
session_start();
/*调用授权*/
define('IN_TG',true);
/* 定义本页识别常量 */
define('SCRIPT','news_post');
/*导入公共文件  */
require dirname(__FILE__).'/includes/common.inc.php';
//登陆后才可以发帖
if (!isset($_COOKIE['username'])) {
	_location('发帖前，必须登录','login.php');
}
//将帖子写入数据库
if ($_GET['action'] == 'news_post') {
	_check_code($_POST['code'],$_SESSION['code']); //验证码判断
	if (!!$_rows = _fetch_array("SELECT
			es_uniqid,
			es_level
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT
			1"
	)) {
	//为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	_uniqid($_rows['es_uniqid'],$_COOKIE['uniqid']);
	_examine_level($_rows['es_level'],2);
	include ROOT_PATH.'includes/check.func.php';
	//接受帖子内容
	$_clean = array();
	$_clean['username'] = $_COOKIE['username'];
	$_clean['title'] = _check_post_title($_POST['title'],2,40);
	$_clean['content'] = _check_post_content($_POST['content'],15);
	$_clean = _mysql_string($_clean);
	//写入数据库
	_query("INSERT INTO es_news (
			es_title,
			es_content,
			es_username,
			es_date
			)
			VALUES (
			'{$_clean['title']}',
			'{$_clean['content']}',
			'{$_clean['username']}',
			NOW()
	)
	");
	if (_affected_rows() == 1) {
	_close();
	_session_destroy();
	_location('新闻发布成功！','index.php');
	} else {
	_close();
	_session_destroy();
	_alert_back('新闻发布失败！');
	}
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统发布新闻</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/code.js"></script>
<script type="text/javascript" src="js/news_post.js"></script>
</head>
<body>
<?php 
  require ROOT_PATH.'includes/header.inc.php';
?>
<div id="news_post">
	<h2>发布新闻</h2>
	<form method="post" name="news_post" action="?action=news_post">
	<dl>
		<dt></dt>
		<dd><span>新闻标题:&nbsp;</span><input type="text" name="title" class="text"/></dd>
		<dd id="q"><span>添加表情:</span>　
			<a href="javascript:;">Q图系列[1]</a>
			<a href="javascript:;">Q图系列[2]</a>
			<a href="javascript:;">Q图系列[3]</a>
		</dd>
		<dd>
			<?php include ROOT_PATH.'includes/ubb.inc.php'?>
			<textarea name="content" rows="20"></textarea>
		</dd>
		<dd>验 证 码：<input type="text" name="code" class="text yzm"/><img src="code.php" id="code"/><input type="submit" class="submit" value="发布"/></dd>
	</dl>
	</form>
</div>

<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>