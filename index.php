<?php
//调用授权
define('IN_TG',true);
//定义本页识别常量
define('SCRIPT','index');
//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//获取新闻结果集
if (!!$_result_news = _query("SELECT
				es_id,
				es_title,
				es_date
				FROM
				es_news
				ORDER BY
				es_date DESC
				LIMIT 6")){
				
				$_html_news=array();
}else{
	_alert_back('新闻列表信息读取失败！');
}

//获取论坛结果集
if (!!$_result_article = _query("SELECT
				es_id,
				es_username,
				es_title,
				es_date
		FROM
				es_article
		WHERE
				es_reid=0
		ORDER BY
				es_date DESC
				LIMIT 6")){

				$_html_article=array();
}else{
	_alert_back('论坛列表信息读取失败！');
}

//获取课程结果集
if (!!$_result_lesson = _query("SELECT
				es_id,
				es_title,
				es_username,
				es_date
		FROM
				es_lesson
		ORDER BY
				es_date DESC
				LIMIT 6")){

				$_html_lesson=array();
}else{
	_alert_back('课程列表信息读取失败！');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统首页</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="notice">
	<h2>公告栏<a href="notice_list.php">>>更多</a></h2>
	<?php 
		if (!!$_rows_notice = _fetch_array("SELECT
				es_id,
				es_title,
				es_content,
				es_username,
				es_date,
				es_last_modify_time
				FROM
				es_notice
				ORDER BY
				es_last_modify_time DESC
				LIMIT 1")){
				if (!!$_rows_notice_author = _fetch_array("SELECT es_nickname FROM es_user WHERE es_username='{$_rows_notice['es_username']}'")){
					$_html_notice=array();
					$_html_notice['id']=$_rows_notice['es_id'];
					$_html_notice['title']=$_rows_notice['es_title'];
					$_html_notice['content']=$_rows_notice['es_content'];
					$_html_notice['date']=$_rows_notice['es_date'];
					$_html_notice['last_modify_time']=$_rows_notice['es_last_modify_time'];
					$_html_notice['nickname']=$_rows_notice_author['es_nickname'];
					$_html_notice=_html($_html_notice);
				}else{
					_alert_back('公告发布者数据读取失败！');
				}
		}else{
			_alert_back('公告信息读取失败！');
		}
	?>
	<div id="notice_display">
		<h3><a id="notice_title" title="<?php echo $_html_notice['title'];?>" href="notice.php?notice_id=<?php echo $_html_notice['id']?>"><?php echo _title($_html_notice['title'],20);?></a></h3>
		<p><?php echo '发布者:'.$_html_notice['nickname'].' 发布时间:'.$_html_notice['date'];?></p>
		<?php if ($_html_notice['date']!=$_html_notice['last_modify_time']){echo '<p>修改时间:'.$_html_notice['last_modify_time'].'</p>';}?>
		<div id="notice_content">
			<?php echo _ubb($_html_notice['content']);?>
		</div>
	</div>
</div>
<div id="subject">
	<h2>最新课程<a href="lesson_list.php?area=1">>>更多</a></h2>
	<dl>
	<?php 
		while (!!$_rows_lesson=_fetch_array_list($_result_lesson)){
			if (!!$_rows_lesson_author = _fetch_array("SELECT es_nickname FROM es_user WHERE es_username='{$_rows_lesson['es_username']}'")){
				$_html_lesson['id']=$_rows_lesson['es_id'];
				$_html_lesson['nickname']=$_rows_lesson_author['es_nickname'];
				$_html_lesson['title']=$_rows_lesson['es_title'];
				$_html_lesson['date']=$_rows_lesson['es_date'];
				$_html_lesson=_html($_html_lesson);
			}else {
				_alert_back('读取课程发布者信息失败！');
			}
	?>
		<dd><a href="lesson.php?lesson_id=<?php echo $_html_lesson['id'];?>"><?php echo $_html_lesson['title'];?><span><?php echo $_html_lesson['nickname'].'&nbsp;'.$_html_lesson['date'];?></span></a></dd>
	<?php }?>
	</dl>
</div>
<div id="news">
	<h2>新闻动态<a href="news_list.php">>>更多</a></h2>
	<dl>
	<?php 
		while (!!$_rows_news=_fetch_array_list($_result_news)){
			$_html_news['id']=$_rows_news['es_id'];
			$_html_news['title']=$_rows_news['es_title'];
			$_html_news['date']=$_rows_news['es_date'];
			$_html_news=_html($_html_news);
	?>
	<dd>
	<a class="news_title" title="<?php echo $_html_news['title'];?>" href="news.php?news_id=<?php echo $_html_news['id']?>">
		<?php echo _title($_html_news['title'],20);?><span><?php echo $_html_news['date'];?></span>
	</a>
	</dd>
	<?php }?>
	</dl>
</div>
<div id="invitation">
	<h2>论坛热门<a href="forum.php?area=1">>>更多</a></h2>
	<dl>
	<?php 
		while (!!$_rows_article=_fetch_array_list($_result_article)){
			if (!!$_rows_article_author = _fetch_array("SELECT es_nickname FROM es_user WHERE es_username='{$_rows_article['es_username']}'")){
				$_html_article['id']=$_rows_article['es_id'];
				$_html_article['nickname']=$_rows_article_author['es_nickname'];
				$_html_article['title']=$_rows_article['es_title'];
				$_html_article['date']=$_rows_article['es_date'];
				$_html_article=_html($_html_article);
			}else {
				_alert_back('读取帖子发布者信息失败！');
			}
	?>
	<dd><a href="article.php?id=<?php echo $_html_article['id'];?>">
	<?php echo _title($_html_article['title'],20)?>
	<span><?php echo $_html_article['nickname'].'&nbsp;'.$_html_article['date']?></span>
	</a></dd>
	<?php }?>
	</dl>
</div>


<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>