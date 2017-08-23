<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','news_list');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//公告列表
global $_pagesize,$_pagenum,$_page;
_page("SELECT es_id FROM es_news",10);
if (!!$_result = _query("SELECT
						es_id,
						es_title,
						es_username,
						es_date,
						es_readcount
				FROM
						es_news
				ORDER BY
						es_date DESC
				LIMIT
						$_pagenum,$_pagesize
		")){
		
				$_html_news=array();
}else{
		_alert_back('新闻列表读取失败！');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统新闻列表</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="news_list">
	<dl>
	<dt>新闻列表</dt>
	<?php 
		while (!!$_rows_news = _fetch_array_list($_result)) {
				if (!!$_rows_news_author = _fetch_array("SELECT es_nickname FROM es_user WHERE es_username='{$_rows_news['es_username']}'")){
					$_html_news['id']=$_rows_news['es_id'];
					$_html_news['title']=$_rows_news['es_title'];
					$_html_news['date']=$_rows_news['es_date'];
					$_html_news['readcount']=$_rows_news['es_readcount'];
					$_html_news['nickname']=$_rows_news_author['es_nickname'];
					$_html_news=_html($_html_news);
				}else{
					_alert_back('新闻发布者数据读取失败！');
				}
		?>
		<dd>
		<a title="<?php echo $_html_news['title'];?>" href="news.php?news_id=<?php echo $_html_news['id']?>">
			<h3><?php echo $_html_news['title'];?></h3>
			<p><?php echo '发布者:'.$_html_news['nickname'].'&nbsp;&nbsp;发布时间:&nbsp;'.$_html_news['date'].'&nbsp;&nbsp;浏览量:&nbsp;'.$_html_news['readcount'];?></p>
		</a>
		</dd>
		<?php }?>
	</dl>
	<?php _paging(2);?>
</div>
<?php 
	require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>