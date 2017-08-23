<?php 
session_start();
/*调用授权*/
define('IN_TG',true);
/* 定义本页识别常量 */
define('SCRIPT','post');
/*导入公共文件  */
require dirname(__FILE__).'/includes/common.inc.php';
//登陆后才可以发帖
if (!isset($_COOKIE['username'])) {
	_location('发帖前，必须登录','login.php');
}
//将帖子写入数据库
if ($_GET['action'] == 'post') {
	_check_code($_POST['code'],$_SESSION['code']); //验证码判断
	if (!!$_rows = _fetch_array("SELECT
			es_uniqid,
			es_post_time
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT
			1"
	)) {
	//为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	_uniqid($_rows['es_uniqid'],$_COOKIE['uniqid']);
	_time(time(),$_rows['es_post_time'],30);
	include ROOT_PATH.'includes/check.func.php';
	//接受帖子内容
	$_clean = array();
	$_clean['area'] = $_POST['area'];
	$_clean['username'] = $_COOKIE['username'];
	$_clean['title'] = _check_post_title($_POST['title'],2,40);
	$_clean['content'] = _check_post_content($_POST['content'],15);
	$_clean = _mysql_string($_clean);
	//写入数据库
	_query("INSERT INTO es_article (
			es_area,
			es_username,
			es_title,
			es_content,
			es_date,
			es_last_reply_time
			)
			VALUES (
			'{$_clean['area']}',
			'{$_clean['username']}',
			'{$_clean['title']}',
			'{$_clean['content']}',
			NOW(),
			NOW()
	)
	");
	if (_affected_rows() == 1) {
	$_clean['id'] = _insert_id();
	$_clean['time']=time();
	//发帖论坛经验加6，限制发帖间隔时间
	_query("UPDATE es_user SET es_post_time='{$_clean['time']}',es_experience=es_experience+12 WHERE es_username='{$_clean['username']}'");
	_close();
	_session_destroy();
	_location('帖子发表成功！','article.php?id='.$_clean['id']);
	} else {
	_close();
	_session_destroy();
	_alert_back('帖子发表失败！');
	}
	}
}else{
	$_GET['area']=_limit_int($_GET['area'],1,8);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统发表帖子</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/code.js"></script>
<script type="text/javascript" src="js/post.js"></script>
</head>
<body>
<?php 
  require ROOT_PATH.'includes/header.inc.php';
?>
<div id="post">
	<h2>发表帖子</h2>
	<form method="post" name="post" action="?action=post">
	<dl>
		<dt><span>发表论坛区：</span>
		<?php
			switch ($_GET['area']){
				case 2: echo '分论坛区（幽州）';
				break;
				case 3: echo '分论坛区（并州）';
				break;
				case 4: echo '分论坛区（冀州）';
				break;
				case 5: echo '分论坛区（徐州）';
				break;
				case 6: echo '分论坛区（青州）';
				break;
				case 7: echo '分论坛区（凉州）';
				break;
				case 8: echo '主论坛区（每周报告）';
				break;
				default: echo '主论坛区（天下荟萃）';
				break;
			}
		?>
		</dt>
		<dd><input type="hidden" name="area" value=<?php echo $_GET['area']?>></dd>
		<dd><span>标&nbsp;题：&nbsp;</span><input type="text" name="title" class="text"/></dd>
		<dd id="q"><span>表&nbsp;情：</span>　
			<a href="javascript:;">Q图系列[1]</a>
			<a href="javascript:;">Q图系列[2]</a>
			<a href="javascript:;">Q图系列[3]</a>
		</dd>
		<dd>
			<?php include ROOT_PATH.'includes/ubb.inc.php'?>
			<textarea name="content" rows="22"></textarea>
		</dd>
		<dd>验 证 码：<input type="text" name="code" class="text yzm"/><img src="code.php" id="code"/><input type="submit" class="submit" value="发表帖子"/></dd>

	</dl>
	</form>
</div>

<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>