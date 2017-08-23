<?php
//调用授权
define('IN_TG',true);
//定义本页识别常量
define('SCRIPT','news');
//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//删除新闻
if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
	if (!!$_rows = _fetch_array("SELECT
			es_level,
			es_uniqid
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT
			1"
	)) {
		_uniqid($_rows['es_uniqid'],$_COOKIE['uniqid']);
		$_row1 = _fetch_array("SELECT es_username FROM es_news WHERE es_id='{$_GET['id']}' LIMIT 1" );
		if ($_rows['es_level']>=2 || $_COOKIE['username']==$_row1['es_username']){
			_query("DELETE FROM es_news WHERE es_id='{$_GET['id']}'");
			_location('新闻已删除！', 'news_list.php');
		}else {
			_alert_back('非法操作！');
		}

	} else {
		_alert_back('非法登录！');
	}
}

if (!isset($_GET['news_id'])){
	_alert_back('非法操作！');
}else {
	$_news_id=_mysql_string($_GET['news_id']);
}
if (!!$_rows_news = _fetch_array("SELECT
				es_id,
				es_title,
				es_content,
				es_username,
				es_date,
				es_readcount
		FROM
				es_news
		WHERE
				es_id=$_news_id
		LIMIT 1")){
				if (!!$_rows_news_author = _fetch_array("SELECT es_nickname,es_level FROM es_user WHERE es_username='{$_rows_news['es_username']}'")){
					$_html_news=array();
					$_html_news['id']=$_rows_news['es_id'];
					$_html_news['title']=$_rows_news['es_title'];
					$_html_news['content']=$_rows_news['es_content'];
					$_html_news['date']=$_rows_news['es_date'];
					$_html_news['readcount']=$_rows_news['es_readcount'];
					$_html_news['nickname']=$_rows_news_author['es_nickname'];
					$_html_news=_html($_html_news);
					
					//浏览量+1
					_query("UPDATE es_news SET es_readcount=es_readcount+1 WHERE es_id='{$_html_news['id']}'");
					
					//新闻删除
					if ($_rows_news['es_username'] == $_COOKIE['username'] || $_rows_news_author['es_level'] >= 2){
						$_html['delete'] = '<a href="news.php?action=delete&id='.$_html_news['id'].'" onclick="return confirm(\'确定要删除吗？\');">删除</a>';
					}
					
				}else{
					_alert_back('新闻发布者数据读取失败！');
				}
}else{
	_alert_back('新闻信息读取失败！');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统新闻</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="news">
	<h2>新闻</h2>
	<h3><?php echo $_html_news['title']?></h3>
	<p><?php echo '发布者:'.$_html_news['nickname'].' 发布时间:'.$_html_news['date'].' 浏览量:'.$_html_news['readcount'];?>
	<?php echo $_html['delete'];?>
	</p>
	<div id="news_content">
		<?php echo _ubb($_html_news['content']);?>
	</div>
</div>
<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>