<?php
//调用授权
define('IN_TG',true);
//定义本页识别常量
define('SCRIPT','notice');
//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//删除公告
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
		$_row1 = _fetch_array("SELECT es_username FROM es_notice WHERE es_id='{$_GET['id']}' LIMIT 1" );
		if ($_rows['es_level']>=2 || $_COOKIE['username']==$_row1['es_username']){
			_query("DELETE FROM es_notice WHERE es_id='{$_GET['id']}'");
			_location('公告已删除！', 'notice_list.php');
		}else {
			_alert_back('非法操作！');
		}

	} else {
		_alert_back('非法登录！');
	}
}

if (!isset($_GET['notice_id'])){
	_alert_back('非法操作！');
}else {
	$_notice_id=_mysql_string($_GET['notice_id']);
}
if (!!$_rows_notice = _fetch_array("SELECT
				es_id,
				es_title,
				es_content,
				es_username,
				es_date,
				es_last_modify_time
		FROM
				es_notice
		WHERE
				es_id=$_notice_id
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
					
					//公告删除
					if(isset($_COOKIE['username'])){
						$_row_user=_fetch_array("SELECT es_level FROM es_user WHERE es_username='{$_COOKIE['username']}'");
						if ($_rows_notice['es_username'] == $_COOKIE['username'] || $_row_user['es_level'] >= 2){
							$_html['delete'] = '<a href="notice.php?action=delete&id='.$_html_notice['id'].'" onclick="return confirm(\'确定要删除吗？\');">删除</a>';
						}
					}
				}else{
					_alert_back('公告发布者数据读取失败！');
				}
}else{
	_alert_back('公告信息读取失败！');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统公告</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="notice">
	<h2>公告</h2>
	<h3><?php echo $_html_notice['title']?></h3>
	<p><?php echo '发布者:'.$_html_notice['nickname'].' 发布时间:'.$_html_notice['date'];if ($_html_notice['date']!=$_html_notice['last_modify_time']){echo ' 修改时间:'.$_html_notice['last_modify_time'];}?>
	<?php echo $_html['delete'];?>
	</p>
	<div id="notice_content">
		<?php echo _ubb($_html_notice['content']);?>
	</div>
</div>
<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>