<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','article');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//处理回复
if ($_GET['action'] == 'rerearticle') {
	if (!!$_rows = _fetch_array("SELECT
			es_uniqid,
			es_rearticle_time
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT
			1"
	)) {
		_uniqid($_rows['es_uniqid'],$_COOKIE['uniqid']);
		_time(time(), $_rows['tg_article_time'],10);
		include ROOT_PATH.'includes/check.func.php';
		//接受数据
		$_clean = array();
		$_clean['reid']=$_POST['reid'];
		$_clean['rereid'] = $_POST['rereid'];
		$_clean['flooruserid'] = $_POST['flooruserid'];
		$_clean['content'] = _check_post_content($_POST['recontent'],10);
		$_clean['username'] = $_COOKIE['username'];
		$_clean = _mysql_string($_clean);
		//写入数据库
		_query("INSERT INTO es_article (
											es_reid,
											es_refloorid,
											es_userid,
											es_username,
											es_content,
											es_date
										)
											VALUES (
											'{$_clean['reid']}',
											'{$_clean['rereid']}',
											'{$_clean['flooruserid']}',
											'{$_clean['username']}',
											'{$_clean['content']}',
											NOW()
										)"
		);
		if (_affected_rows() == 1) {
			$_clean['time']=time();
			_query("UPDATE es_user SET es_rearticle_time='{$_clean['time']}',es_experience=es_experience+3 WHERE es_username='{$_clean['username']}'");
			_query("UPDATE es_article SET es_commentcount=es_commentcount+1,es_last_reply_time=NOW() WHERE es_reid=0 AND es_id='{$_clean['reid']}'");
			_close();
			_location('回复成功！','article.php?id='.$_clean['reid']);
		} else {
			_close();
			_alert_back('回复失败！');
		}
	} else {
		_alert_back('非法登录！');
	}
}
//处理回帖
if ($_GET['action'] == 'rearticle') {
	if (!!$_rows = _fetch_array("SELECT
			es_uniqid,
			es_rearticle_time
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT
			1"
	)) {
		_uniqid($_rows['es_uniqid'],$_COOKIE['uniqid']);
		_time(time(), $_rows['tg_article_time'],10);
		include ROOT_PATH.'includes/check.func.php';
		//接受数据
		$_clean = array();
		$_clean['reid'] = $_POST['reid'];
		$_clean['reuserid'] = $_POST['reuserid'];
		$_clean['content'] = _check_post_content($_POST['content'],10);
		$_clean['username'] = $_COOKIE['username'];
		$_clean = _mysql_string($_clean);
		//写入数据库
		_query("INSERT INTO es_article (
		es_reid,
		es_userid,
		es_username,
		es_content,
		es_date
		)
		VALUES (
		'{$_clean['reid']}',
		'{$_clean['reuserid']}',
		'{$_clean['username']}',
		'{$_clean['content']}',
		NOW()
		)"
		);
		if (_affected_rows() == 1) {
			$_clean['time']=time();
			_query("UPDATE es_user SET es_rearticle_time='{$_clean['time']}',es_experience=es_experience+6 WHERE es_username='{$_clean['username']}'");
			_query("UPDATE es_article SET es_commentcount=es_commentcount+1,es_last_reply_time=NOW() WHERE es_reid=0 AND es_id='{$_clean['reid']}'");
			_close();
			_location('回帖成功！','article.php?id='.$_clean['reid']);
		} else {
			_close();
			_alert_back('回帖失败！');
		}
	} else {
		_alert_back('非法登录！');
	}
}
//置顶帖
if (isset($_GET['subject_up']) && isset($_GET['id'])){
	if (isset($_COOKIE['username'])){
		if (!!$_rows_manage1 = _fetch_array("SELECT
						es_uniqid,
						es_level
						FROM
						es_user
						WHERE
						es_username='{$_COOKIE['username']}'
						LIMIT
						1"
				)){
					_uniqid($_rows_manage1['es_uniqid'],$_COOKIE['uniqid']);
					$_html['manage_level']=$_rows_manage1['es_level'];
					if ($_html['manage_level']>=2){
						_query("UPDATE es_article SET es_up='{$_GET['subject_up']}' WHERE es_id='{$_GET['id']}' ");
						if(_affected_rows()==1){
							_close();
							_location('置顶帖设置/取消成功！','article.php?id='.$_GET['id']);
						}
					}else{
						_alert_back('非管理员操作!');
					}
		}else{
				_alert_back('非法登录！');
		}
	}else{
		_alert_back('非法操作！');
	}
}
//删除回复
if ($_GET['action'] == 'deletere' && isset($_GET['id'])) {
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
		$_row1 = _fetch_array("SELECT es_username,es_reid FROM es_article WHERE es_id='{$_GET['id']}' LIMIT 1" );
		$_row2 = _fetch_array("SELECT es_username FROM es_article WHERE es_id='{$_row1['es_reid']}' LIMIT 1" );
		if ($_rows['es_level']>=2 || $_COOKIE['username']==$_row1['es_username'] || $_COOKIE['username']==$_row2['es_username']){
			_query("DELETE FROM es_article WHERE es_id='{$_GET['id']}' OR es_refloorid='{$_GET['id']}'");
			_location('回复及其楼中楼已删除！', 'article.php?id='.$_row1['es_reid']);
		}else {
			_alert_back('非法操作！');
		}

	} else {
		_alert_back('非法登录！');
	}
}
//删除帖子
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
		$_row1 = _fetch_array("SELECT es_username FROM es_article WHERE es_id='{$_GET['id']}' LIMIT 1" );
		if ($_rows['es_level']>=2 || $_COOKIE['username']==$_row1['es_username']){
			_query("DELETE FROM es_article WHERE es_id='{$_GET['id']}' OR es_reid='{$_GET['id']}'");
			_location('帖子及其回复已删除！', 'forum.php');
		}else {
			_alert_back('非法操作！');
		}
		
	} else {
		_alert_back('非法登录！');
	}
}
//读出数据
if (isset($_GET['id'])) {
	if (!!$_rows = _fetch_array("SELECT
							        es_id,
									es_area,
									es_username,
									es_title,
									es_content,
									es_readcount,
									es_commentcount,
									es_up,
									es_date,
									es_last_modify_date
					  FROM 
									es_article
					WHERE			
									es_reid=0
					AND	
									es_id='{$_GET['id']}'")){
		//累积阅读量
		_query("UPDATE es_article SET es_readcount=es_readcount+1 WHERE es_id='{$_GET['id']}'");
		$_html = array();
		$_html['reid']=$_rows['es_id'];
		$_html['area'] = $_rows['es_area'];
		$_html['username_subject'] = $_rows['es_username'];
		$_html['title'] = $_rows['es_title'];
		$_html['content'] = $_rows['es_content'];
		$_html['readcount'] = $_rows['es_readcount'];
		$_html['commentcount'] = $_rows['es_commentcount'];
		$_html['up'] = $_rows['es_up'];
		$_html['date'] = $_rows['es_date'];
		$_html['last_modify_date']=$_rows['es_last_modify_date'];
		//拿出用户名，去查找用户信息
		if (!!$_rows = _fetch_array("SELECT 
										es_id,
										es_nickname,
										es_sex,
										es_face,
										es_email,
										es_experience
						  FROM 
						  				es_user 
						WHERE 
										es_username='{$_html['username_subject']}'")) {
			//提取用户信息
			$_html['userid'] = $_rows['es_id'];
			$_html['nickname'] = $_rows['es_nickname'];
			$_html['sex'] = $_rows['es_sex'];
			$_html['face'] = $_rows['es_face'];
			$_html['email'] = $_rows['es_email'];
			$_html = _html($_html);
			$_html['experience'] = _forum_level($_rows['es_experience']);
			
			//创建全局变量，做带参分页
			global $_id;
			$_id='id='.$_html['reid'].'&';

			//权限管理
			if (isset($_COOKIE['username'])){
				if (!!$_rows_manage = _fetch_array("SELECT
						es_id,
						es_uniqid,
						es_level
						FROM
						es_user
						WHERE
						es_username='{$_COOKIE['username']}'
						LIMIT
						1"
				)){
					_uniqid($_rows_manage['es_uniqid'],$_COOKIE['uniqid']);
					$_html['manage_level']=$_rows_manage['es_level'];
					if ($_html['manage_level']>=2){
						if ($_html['up']==0){
							$_html['subject_up']= '<a href="?subject_up=1&id='.$_html['reid'].'">置顶</a>';
						}else {
							$_html['subject_up']= '<a href="?subject_up=0&id='.$_html['reid'].'">取消置顶</a>';
						}
					}
					
					//将该主题下所有回复设为已读
					_query("UPDATE es_article SET es_read=1 WHERE es_reid='{$_html['reid']}' AND es_userid='{$_rows_manage['es_id']}'");
					
					//主题帖子修改
					if ($_html['username_subject'] == $_COOKIE['username']) {
						$_html['subject_modify'] = '<a href="article_modify.php?id='.$_html['reid'].'">修改</a>';
					}
					
					//帖子删除
					if ($_html['username_subject'] == $_COOKIE['username'] || $_html['manage_level'] >= 2){
						$_html['subject_delete'] = '<a href="article.php?action=delete&id='.$_html['reid'].'" onclick="return confirm(\'确定要删除吗？\');">删除</a>';
					}
					
				}else{
					_alert_back('非法登录！');
				}
			}
				
			//读取最后修改信息
			if ($_html['last_modify_date'] != '0000-00-00 00:00:00') {
				$_html['last_modify_date_string'] = '本贴于 '.$_html['last_modify_date'].' 修改';
			}
			
			//读取回帖
			global $_pagesize,$_pagenum,$_page;
			_page("SELECT es_id FROM es_article WHERE es_reid='{$_html['reid']}' AND es_refloorid=0",6);
			$_result = _query("SELECT
					es_id,es_username,es_content,es_date
					FROM
					es_article
					WHERE
					es_reid='{$_html['reid']}' AND es_refloorid=0
					ORDER BY
					es_date ASC
					LIMIT
					$_pagenum,$_pagesize
					");
		} else {
			//这个用户已被删除
			_alert_back('此贴作者已被删除！');
		}
	} else {
		_alert_back('不存在这个主题！');
	}
} else {
	_alert_back('非法操作！');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统帖子查看</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/article.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>

<div id="article">
	<div id="headline">
		<h2>
		<?php echo $_html['title'];
			if ($_html['up']==1){ echo '&nbsp;';
		?>
		<img alt="置顶" src="images/forum_up.jpg">
		<?php }
			if ($_html['readcount']>=100 && $_html['commentcount']>=10){
		?>
		<img alt="火帖" src="images/forum_hot.jpg">
		<?php }?>
		</h2>
		<a href="#re2" title="回复楼主">回复</a>
		<?php echo $_html['subject_delete'];?>
		<?php echo $_html['subject_modify'];?>
		<?php echo $_html['subject_up'];?>
	</div>
	<?php if ($_GET['page']==null || $_GET['page']==1){?>
	<div id="subject">
		<div class="facearea">
			<img src="<?php echo $_html['face'];?>" alt="头像" />
			<h3><?php echo $_html['nickname']?></h3>
			<a class="level"><?php echo $_html['experience'];?></a>
			<a href="message.php?to_id=<?php echo $_html['userid'];?>#now">私信</a>
			<a href="mailto:<?php echo $_html['email']?>">邮件</a>
		</div>
		<div class="content">
			<div class="detail">
				<?php echo _ubb($_html['content'])?>
			</div>
			<div class="read">
				<p>
					<?php echo $_html['last_modify_date_string'].' '.$_html['date'];?>
					阅读:(<?php echo $_html['readcount']?>) 评论:(<?php echo $_html['commentcount']?>)
				</p>
			</div>
		</div>
	</div>
	<?php }?>
	<p class="linemain"></p>
	<?php 
		$_i=2+($_page-1)*6;
		while (!!$_rows = _fetch_array_list($_result)) {
			$_html['id']=$_rows['es_id'];
			$_html['username'] = $_rows['es_username'];
			$_html['content'] = $_rows['es_content'];
			$_html['date'] = $_rows['es_date'];
			$_html = _html($_html);
			
			if (!!$_rows = _fetch_array("SELECT
																			es_id,
																			es_nickname,
																			es_sex,
																			es_face,
																			es_email,
																			es_experience
															  FROM 
															  				es_user
															WHERE 
																			es_username='{$_html['username']}'")) {
				//提取用户信息
				$_html['userid'] = $_rows['es_id'];
				$_html['nickname'] = $_rows['es_nickname'];
				$_html['sex'] = $_rows['es_sex'];
				$_html['face'] = $_rows['es_face'];
				$_html['email'] = $_rows['es_email'];
				$_html = _html($_html);
				$_html['experience'] = _forum_level($_rows['es_experience']);
				
			} else {
				//这个用户可能已经被删除了
				echo '此用户已经被删除！';
			}
	?>
	<div class="re">
		<div class="facearea">
			<img src="<?php echo $_html['face']?>" alt="<?php echo $_html['username']?>" />
			<h3><?php echo $_html['nickname'];?></h3>
			<a class="level"><?php echo $_html['experience'];?></a>
			<a href="message.php?to_id=<?php echo $_html['userid'];?>#now">私信</a>
			<a href="mailto:<?php echo $_html['email']?>">邮件</a>
		</div>
		<div class="content">
			<div class="detail">
				<?php echo _ubb($_html['content'])?>
			</div>
			<div class="read">
				<p>
					<?php echo $_i.'楼 '.$_html['date'];?>
					<a class="rea" href="###" title="回复<?php echo $_i;?>楼的<?php echo $_html['username']?>">回复</a>
					<?php if ($_html['manage_level']>=2 || $_COOKIE['username']==$_html['username_subject'] || $_COOKIE['username']==$_html['username']){?>
					<a href="article.php?action=deletere&id=<?php echo $_html['id'];?>" onclick="return confirm('确定要删除吗？');">删除</a>
					<?php }?>
				</p>
			</div>
			<div class="rere">
				<dl>
					<?php 
						$_result1 = _query("SELECT
								es_username,es_content,es_date
								FROM
								es_article
								WHERE
								es_reid='{$_html['reid']}' AND es_refloorid='{$_html['id']}'
								ORDER BY
								es_date ASC
								");
						while (!!$_rows1 = _fetch_array_list($_result1)) {
							$_html['reusername'] = $_rows1['es_username'];
							$_html['recontent'] = $_rows1['es_content'];
							$_html['redate'] = $_rows1['es_date'];
							$_html = _html($_html);
							
							if (!!$_rows2 = _fetch_array("SELECT
									es_nickname,
									es_face
									FROM
									es_user
									WHERE
									es_username='{$_html['reusername']}'")) {
									//提取用户信息
									$_html['renickname'] = $_rows2['es_nickname'];
									$_html['reface'] = $_rows2['es_face'];
									$_html = _html($_html);
							} else {
							//这个用户可能已经被删除了
									echo '此用户已经被删除！';
							}
					?>
					<dd>
						<img src="thumb.php?filename=<?php echo $_html['reface'];?>&percent=0.3"/><?php echo ' <span class="renickname">'.$_html['renickname'].'</span>: '.$_html['recontent'].' <span class="redate">'.$_html['redate'].'</span>';?>
					</dd>
					<?php }?>
				</dl>
				<form method="post" action="?action=rerearticle">
					<input type="hidden" name="reid" value="<?php echo $_html['reid']?>"/>
					<input type="hidden" name="rereid" value="<?php echo $_html['id']?>"/>
					<input type="hidden" name="flooruserid" value="<?php echo $_html['userid']?>"/>
					<textarea name="recontent" rows="2"></textarea><input type="submit" class="submit" value="回复" />
				</form>
			</div>
		</div>
	</div>
	<?php $_i++;}
		_free_result($_result);
		_paging(2);
	?>
	<?php if (isset($_COOKIE['username'])) {?>
	<a id="re2"></a>
	<div id="reply">
	<form id="subform" method="post" action="?action=rearticle">
		<input type="hidden" name="reid" value="<?php echo $_html['reid']?>" />
		<input type="hidden" name="reuserid" value="<?php echo $_html['userid']?>" />
		<dl>
			<dd id="q">贴　　图：　<a href="javascript:;">Q图系列[1]</a> <a href="javascript:;">Q图系列[2]</a> <a href="javascript:;">Q图系列[3]</a></dd>
			<dd>
				<?php include ROOT_PATH.'includes/ubb.inc.php'?>
				<textarea name="content" rows="9"></textarea>
			</dd>
			<dd><input type="submit" class="submit" value="回复" /></dd>
		</dl>
	</form>
	</div>
	<?php }?>
</div>

<?php 
	require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>
