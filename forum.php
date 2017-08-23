<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','forum');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
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
if ($_GET['action']=='search' || isset($_GET['search_invi'])){
	if ($_GET['action']=='search'){
		$_search_invi=_search_string($_POST['invitation']);
	}else {
		$_search_invi=$_GET['search_invi'];
	}
	//帖子列表
	global $_id;
	$_id='search_invi='.$_search_invi.'&';
	
	global $_pagesize,$_pagenum,$_page;
	_page("SELECT es_id FROM es_article WHERE es_reid=0 AND es_up=0 AND es_title LIKE '%$_search_invi%' escape '/'",10);
	$_result = _query("SELECT
			es_id,
			es_username,
			es_title,
			es_readcount,
			es_commentcount,
			es_last_reply_time
			FROM
			es_article
			WHERE
			es_reid=0 AND es_up=0 AND es_title LIKE '%$_search_invi%' escape '/'
			ORDER BY
			es_last_reply_time DESC
			LIMIT
			$_pagenum,$_pagesize
			");
	
	$_html = array();
}else{
	//帖子列表
	global $_id;
	$_GET['area']=_limit_int($_GET['area'],1,8);
	$_id='area='.$_GET['area'].'&';
	
	global $_pagesize,$_pagenum,$_page;
	_page("SELECT es_id FROM es_article WHERE es_reid=0 AND es_up=0 AND es_area='{$_GET['area']}'",10);
	$_result = _query("SELECT
								es_id,
								es_username,
								es_title,
								es_readcount,
								es_commentcount,
								es_last_reply_time
						FROM
								es_article
						WHERE
								es_reid=0 AND es_up=0 AND es_area='{$_GET['area']}'
						ORDER BY
								es_last_reply_time DESC
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
<title>智能加工中心教学系统论坛</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="forum">
	<div id="forum_sidebar">
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
			<dt>主论坛区</dt>
			<dd><a href="forum.php?area=1">天下荟萃</a></dd>
			<dd><a href="forum.php?area=8">每周报告</a></dd>
		</dl>
		<dl>
			<dt>分论坛区</dt>
			<dd><a href="forum.php?area=2">幽州</a></dd>
			<dd><a href="forum.php?area=3">并州</a></dd>
			<dd><a href="forum.php?area=4">冀州</a></dd>
			<dd><a href="forum.php?area=5">徐州</a></dd>
			<dd><a href="forum.php?area=6">青州</a></dd>
			<dd><a href="forum.php?area=7">凉州</a></dd>
		</dl>
	</div>
	<div id="forum_main">
		<div id="search_allarea">
			<form method="post" name="search" action="forum.php?action=search">
				<dl>
				<dd>论坛搜索:&nbsp;<input type="text" name="invitation" id="search_input"/><input type="submit" value="搜索" class="button"/></dd>
				</dl>
			</form>
			<a href="post.php?area=<?php if(isset($_GET['area'])){echo $_GET['area'];} else {echo 1;}?>">发帖</a>
		</div>
		<h2>全站置顶</h2>
		<div id="stick" style="scrollBar-base-color:pink;">
			<dl class="invitation">
				<dt></dt>
				<?php 
				$_result_up = _query("SELECT
							es_id,
							es_username,
							es_title,
							es_readcount,
							es_commentcount,
							es_last_reply_time
					FROM
							es_article
					WHERE
							es_up=1
					ORDER BY
							es_date DESC
				");
				$_html_up=array();
				while (!!$_rows_up = _fetch_array_list($_result_up)) {
					$_html_up['id']=$_rows_up['es_id'];
					$_html_up['title_t']=$_rows_up['es_title'];
					$_html_up['title']=_title($_rows_up['es_title'],20);
					$_html_up['date']=$_rows_up['es_last_reply_time'];
					$_html_up['readcount']=$_rows_up['es_readcount'];
					$_html_up['commentcount']=$_rows_up['es_commentcount'];
					$_rows_up_user=_fetch_array("SELECT
															es_nickname
											  		FROM 
											  				es_user 
													WHERE 
															es_username='{$_rows_up['es_username']}'");
					$_html_up['nickname']=$_rows_up_user['es_nickname'];
					$_html_up=_html($_html_up);
				?>
				<dd><a href="article.php?id=<?php echo $_html_up['id'];?>"><span class="invitation_title"><?php echo $_html_up['title'].'&nbsp;';?></span>
					<img alt="置顶" src="images/forum_up.jpg">
					<?php if ($_html_up['readcount']>=100 && $_html_up['commentcount']>=10){?>
						&nbsp;&nbsp;&nbsp;
						<img alt="火帖" src="images/forum_hot.jpg">
					<?php }?>
					<span class="invitation_behind"><?php echo $_html_up['nickname'].'&nbsp;'.$_html_up['date'].'&nbsp;阅读:('.$_html_up['readcount'].')&nbsp;评论:('.$_html_up['commentcount'].')';?></span>
				</a></dd>
				<?php }?>
			</dl>
		</div>
		<div id="sort">
			<form method="post" name="sort" action="forum.php?action=sort">
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
			帖子排序：
				<select name="forum_sort">
					<option value="1" selected="selected">回复时间</option>
					<option value="2">发帖时间</option>
					<option value="3">评论量</option>
					<option value="4">阅读量</option>
				</select>
				<input type="submit" value="确定" class="button"/>
			</form>
		</div>
		<dl class="invitation">
		<?php 
		while (!!$_rows = _fetch_array_list($_result)) {
			$_html['id']=$_rows['es_id'];
			$_html['title_t']=$_rows['es_title'];
			$_html['title']=_title($_rows['es_title'],40);
			$_html['date']=$_rows['es_last_reply_time'];
			$_html['readcount']=$_rows['es_readcount'];
			$_html['commentcount']=$_rows['es_commentcount'];
			$_rows_user=_fetch_array("SELECT
					es_nickname
					FROM
					es_user
					WHERE
					es_username='{$_rows['es_username']}'");
			$_html['nickname']=$_rows_user['es_nickname'];
			$_html=_html($_html);
		?>
			<dd><a href="article.php?id=<?php echo $_html['id'];?>"><span class="invitation_title"><?php echo $_html['title'].'&nbsp;';?></span>
			<?php if ($_html['readcount']>=100 && $_html['commentcount']>=10){?>
			<img alt="火帖" src="images/forum_hot.jpg">
			<?php }?>
			<span class="invitation_behind"><?php echo $_html['nickname'].'&nbsp;'.$_html['date'].'&nbsp;阅读:('.$_html['readcount'].')&nbsp;评论:('.$_html['commentcount'].')';?></span>
			</a></dd>
		<?php }?>
		</dl>
		<?php _paging(2);?>
	</div>
</div>


<?php 
	require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>