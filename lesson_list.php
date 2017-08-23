<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','lesson_list');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//当用户登录
if (isset($_COOKIE[username])){
	if(!!$_rows=_fetch_array("SELECT es_uniqid FROM es_user WHERE es_username='{$_COOKIE['username']}' LIMIT 1")){
		//比对唯一标识符uniqid()，防止cookies伪造
		_uniqid($_rows['es_uniqid'], $_COOKIE['uniqid']);
		$_rows_sidebar=_fetch_array("SELECT
				es_face
				FROM
				es_user
				WHERE
				es_username='{$_COOKIE['username']}'
				LIMIT 1");
		if (!!$_rows_sidebar){
			$_html_sidebar=array();
			$_html_sidebar['face']=$_rows_sidebar['es_face'];
			$_html_sidebar=_html($_html_sidebar);
		}else{
			_alert_back('此用户不存在！');
		}
	}else{
		_alert_back('禁止非法登录！');
	}
}
//当点击搜索或者在搜索页面翻页
if ($_GET['action']=='search' || isset($_GET['search_invi'])){
	if ($_GET['action']=='search'){
		$_search_invi=_search_string($_POST['lesson_search']);
	}else {
		$_search_invi=$_GET['search_invi'];
	}
	//帖子列表
	global $_id;
	$_id='search_invi='.$_search_invi.'&';

	global $_pagesize,$_pagenum,$_page;
	_page("SELECT es_id FROM es_lesson WHERE es_title LIKE '%$_search_invi%' escape '/'",12);
	$_result = _query("SELECT
			es_id,
			es_title,
			es_username,
			es_date
			FROM
			es_lesson
			WHERE
			es_title LIKE '%$_search_invi%' escape '/'
			ORDER BY
			es_date DESC
			LIMIT
			$_pagenum,$_pagesize
			");

	$_html = array();
}else{
	
	//除了搜索必须传入机床型号
	if (!isset($_GET['area']) || ($_GET['area']!=1 && $_GET['area']!=2 && $_GET['area']!=3 && $_GET['area']!=4)){
		_alert_back('非法操作！');
	}
	//帖子列表
	global $_id;
	$_id='area='.$_GET['area'].'&';

	global $_pagesize,$_pagenum,$_page;
	_page("SELECT es_id FROM es_lesson WHERE es_type='{$_GET['area']}'",10);
	$_result = _query("SELECT
			es_id,
			es_title,
			es_username,
			es_date
			FROM
			es_lesson
			WHERE
			es_type='{$_GET['area']}'
			ORDER BY
			es_date DESC
			LIMIT
			$_pagenum,$_pagesize
			");

	$_html = array();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统课程列表</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="lesson_list">
	<div id="lesson_list_sidebar">
		<div id="personage_face">
			<?php 
				if(isset($_COOKIE['username'])){
					echo '<img alt="'.$_html_sidebar['face'].'" src="'.$_html_sidebar['face'].'">';
				}else{
					echo '<img alt="images/unlog.jpg" src="images/unlog.jpg">
							<a href="login.php" id="loglink">登录</a>
							<a href="register.php">注册</a>';
				}
			?>
		</div>
		<dl>
			<dt>机床类型</dt>
			<dd><a href="lesson_list.php?area=1">三菱</a></dd>
			<dd><a href="lesson_list.php?area=2">FANUC</a></dd>
			<dd><a href="lesson_list.php?area=3">Siemens</a></dd>
			<dd><a href="lesson_list.php?area=4">月工作报告</a></dd>
		</dl>
	</div>
	<div id="lesson_list_main">
		<div id="search_allarea">
			<form method="post" name="search" action="lesson_list.php?action=search">
				<dl>
				<dd>课程搜索:&nbsp;<input type="text" name="lesson_search" id="search_input"/><input type="submit" value="搜索" class="button"/></dd>
				</dl>
			</form>
		</div>
		<h2>机床型号：
			<?php switch ($_GET['area']){
					case 1:
						echo '三菱';break;
					case 2:
						echo 'FANUC';break;
					case 3:
						echo 'Siemens';break;
					case 4:
						echo '月工作报告';break;
				}
			?>
		</h2>
		<div id="lesson_invi">
			<dl>
			<?php 
				while (!!$_rows = _fetch_array_list($_result)){
					$_html['id']=$_rows['es_id'];
					$_html['title']=$_rows['es_title'];
					$_html['date']=$_rows['es_date'];
					$_rows_user=_fetch_array("SELECT
							es_nickname
							FROM
							es_user
							WHERE
							es_username='{$_rows['es_username']}'");
					$_html['nickname']=$_rows_user['es_nickname'];
					$_html=_html($_html);
			?>
			<dd><a href="lesson.php?lesson_id=<?php echo $_html['id'];?>"><?php echo $_html['title'];?><span><?php echo $_html['nickname'].'&nbsp;'.$_html['date'];?></span></a></dd>
			<?php }?>
			</dl>
		</div>
		<?php _paging(2);?>
	</div>
</div>


<?php 
	require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>