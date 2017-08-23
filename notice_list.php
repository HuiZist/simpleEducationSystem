<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','notice_list');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//公告列表
global $_pagesize,$_pagenum,$_page;
_page("SELECT es_id FROM es_notice",10);
if (!!$_result = _query("SELECT
						es_id,
						es_title,
						es_username,
						es_date,
						es_last_modify_time
				FROM
						es_notice
				ORDER BY
						es_last_modify_time DESC
				LIMIT
						$_pagenum,$_pagesize
		")){
		
				$_html_notice=array();
}else{
		_alert_back('公告列表读取失败！');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统公告列表</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="notice_list">
	<dl>
	<dt>公告列表</dt>
	<?php 
		while (!!$_rows_notice = _fetch_array_list($_result)) {
				if (!!$_rows_notice_author = _fetch_array("SELECT es_nickname FROM es_user WHERE es_username='{$_rows_notice['es_username']}'")){
					$_html_notice['id']=$_rows_notice['es_id'];
					$_html_notice['title']=$_rows_notice['es_title'];
					$_html_notice['date']=$_rows_notice['es_date'];
					$_html_notice['last_modify_time']=$_rows_notice['es_last_modify_time'];
					$_html_notice['nickname']=$_rows_notice_author['es_nickname'];
					$_html_notice=_html($_html_notice);
				}else{
					_alert_back('公告发布者数据读取失败！');
				}
		?>
		<dd>
		<a title="<?php echo $_html_notice['title'];?>" href="notice.php?notice_id=<?php echo $_html_notice['id']?>">
			<h3><?php echo $_html_notice['title'];?></h3>
			<p><?php echo '发布者:'.$_html_notice['nickname'].' 发布时间:'.$_html_notice['date'];if ($_html_notice['date']!=$_html_notice['last_modify_time']){echo ' 修改时间:'.$_html_notice['last_modify_time'];}?></p>
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