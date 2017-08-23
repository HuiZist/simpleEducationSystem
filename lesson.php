<?php
//调用授权
define('IN_TG',true);
//定义本页识别常量
define('SCRIPT','lesson');
//导入公共文件
require dirname(__FILE__).'/includes/common.inc.php';

//删除课程
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
		$_row1 = _fetch_array("SELECT es_username,es_files FROM es_lesson WHERE es_id='{$_GET['id']}' LIMIT 1" );
		if ($_rows['es_level']>=2 || $_COOKIE['username']==$_row1['es_username']){
			$_files_path=array();
			$_files_path=explode('||', $_row1['es_files']);
			foreach ($_files_path as $_file){
				if ($_file!=null){
					if (file_exists(iconv("UTF-8","gb2312",$_file))) {
						unlink(iconv("UTF-8","gb2312",$_file));
					}else{
						_alert_back('磁盘里已不存在此文件！');
					}
				}
			}
			_query("DELETE FROM es_lesson WHERE es_id='{$_GET['id']}'");
			_location('课程已删除！', 'lesson_list.php?area=1');
		}else {
			_alert_back('非法操作！');
		}

	} else {
		_alert_back('非法登录！');
	}
}

//上传文件和课程信息
if ($_GET['action'] == 'lesson_work' && isset($_COOKIE['username'])) {
	if (!!$_rows_ex = _fetch_array("SELECT
			es_uniqid
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT 1")) {
			_uniqid($_rows_ex['es_uniqid'],$_COOKIE['uniqid']);

			//检查课程信息
			include ROOT_PATH.'includes/check.func.php';
			$_clean=array();
			$_clean['reid']=$_POST['reid'];
			$_clean['rename']=$_POST['rename'];
			$_clean['title']=$_POST['title'];
			$_clean['content']=_check_post_content($_POST['content'],2);
			$_clean['username']=$_COOKIE['username'];

			$_files_info = $_FILES['file'];
			//检查文件大小
			foreach ($_files_info['size'] as $_size){
				if ($_size > 10000000) {
					_alert_back('上传的文件大小不得超过10M');
				}
			}
			//检验上传错误
			foreach ($_files_info['error'] as $_a =>$_error){
				if ($_files_info['name'][$_a]==null){
					continue;
				}
				if ($_error > 0) {
					switch ($_error) {
						case 1:
							$err_info="上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值";
							break;
						case 2:
							$err_info="上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";
							break;
						case 3:
							$err_info="文件只有部分被上传";
							break;
						case 4:
							$err_info="没有文件被上传";
							break;
						case 6:
							$err_info="找不到临时文件夹";
							break;
						case 7:
							$err_info="文件写入失败";
							break;
						default:
							$err_info="未知的上传错误";
							break;
					}
					_alert_back($err_info);
				}
			}
			$_j=0;
			$_work_dir='lesson_work';
			//移动文件
			for ($_i=0;$_i<count($_files_info['tmp_name']);$_i++){
				if ($_files_info['name'][$_i]==null){
					continue;
				}
				$_j++;
				if (is_uploaded_file($_files_info['tmp_name'][$_i])){
					$_file_new = _lesson_filename($_files_info['name'][$_i],$_work_dir,$_clean['title'],$_j);
					if	(!@move_uploaded_file($_files_info['tmp_name'][$_i],iconv("UTF-8","gb2312",$_file_new))) {
						_alert_back('文件['.$_i.']移动失败!');
					}
					//提取文件路径
					$_file_path.=$_file_new.'||';
				}else {
					_alert_back('上传的临时文件['.$_i.']不存在！');
				}
			}
			$_clean['file_path']=$_file_path;
			//写入数据库
			_query("INSERT INTO es_work (
			es_reid,
			es_rename,
			es_username,
			es_content,
			es_file,
			es_date
			)
			VALUES (
			'{$_clean['reid']}',
			'{$_clean['rename']}',
			'{$_clean['username']}',
			'{$_clean['content']}',
			'{$_clean['file_path']}',
			NOW()
			)
			");
			if (_affected_rows() == 1) {
				$_clean['id'] = _insert_id();
				_close();
				_location('报告提交成功！','lesson.php?lesson_id='.$_clean['reid']);
			} else {
				_close();
				_alert_back('报告提交失败！');
			}

	}else {

		_alert_back('非法登录！');
	}
}

//只能用户登录
if (!isset($_COOKIE['username'])){
	_location('请先登录!', 'login.php');
}else {
	$_lesson_id=$_GET['lesson_id'];
}

//读取课程数据
if (!!$_rows_lesson = _fetch_array("SELECT
		es_type,
		es_title,
		es_content,
		es_username,
		es_files,
		es_date
		FROM
		es_lesson
		WHERE
		es_id=$_lesson_id
		LIMIT 1")){
		if (!!$_rows_lesson_author = _fetch_array("SELECT es_nickname,es_face FROM es_user WHERE es_username='{$_rows_lesson['es_username']}'")){
			$_html_lesson=array();
			$_html_lesson['id']=$_lesson_id;
			$_html_lesson['type']=$_rows_lesson['es_type'];
			$_html_lesson['title']=$_rows_lesson['es_title'];
			$_html_lesson['content']=$_rows_lesson['es_content'];
			$_html_lesson['date']=$_rows_lesson['es_date'];
			$_html_lesson['files']=$_rows_lesson['es_files'];
			$_html_lesson['nickname']=$_rows_lesson_author['es_nickname'];
			$_html_lesson['face']=$_rows_lesson_author['es_face'];
			$_html_lesson=_html($_html_lesson);
			
			//课程删除
			if(isset($_COOKIE['username'])){
			$_row_user=_fetch_array("SELECT es_level FROM es_user WHERE es_username='{$_COOKIE['username']}'");
			if ($_rows_lesson['es_username'] == $_COOKIE['username'] || $_row_user['es_level'] >= 2){
				$_html['delete'] = '<a href="lesson.php?action=delete&id='.$_html_lesson['id'].'" onclick="return confirm(\'确定要删除吗？\');">删除</a>';
			}
			}
			
		}else{
			_alert_back('课程发布者数据读取失败！');
		}
}else{
	_alert_back('课程信息读取失败！');
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工中心教学系统课程</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/lesson.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="lesson">
	<h2>课程</h2>
	<div id="lesson_title">
		<h3><?php echo $_html_lesson['title']?></h3>
		<p>
			<img src="thumb.php?filename=<?php echo $_html_lesson['face'];?>&percent=0.3" title="发布者头像">
			<?php echo $_html_lesson['nickname'].'&nbsp;发布于:'.$_html_lesson['date'].'&nbsp;&nbsp;机床类型:';
				switch ($_html_lesson['type']){
					case 1:
						echo '三菱';
						break;
					case 2:
						echo 'FANUC';
						break;
					case 3:
						echo 'Siemens';
						break;
					case 4:
						echo '月工作报告';
						break;
				}
			?>
			<?php echo $_html['delete'];?>		
		</p>
	</div>
	<h2>课程介绍</h2>
	<div id="lesson_content">
		<?php echo _ubb($_html_lesson['content']);?>
	</div>
	<h2>课程资料下载</h2>
	<div id="lesson_file">
		<?php 
			$_file = explode('||', $_html_lesson['files']);
			foreach ($_file as $_file_path){
				if ($_file_path==null){
					continue;
				}
		?>
		<a href="lesson_download.php?filename=<?php echo $_file_path;?>"><?php echo basename($_file_path);?></a>
		<?php }?>
	</div>
	<h2>课程报告</h2>
	<div id="lesson_work">
		<form enctype="multipart/form-data" action="?action=lesson_work" method="post">
		<dl>
			<dt></dt>
			<dd>
				<input type="hidden" name="reid" value="<?php echo $_html_lesson['id'];?>">
				<input type="hidden" name="title" value="<?php echo $_html_lesson['title'];?>">
				<input type="hidden" name="rename" value="<?php echo $_rows_lesson['es_username']?>">
			</dd>
			<dd id="q"><span>添加表情:</span>　
				<a href="javascript:;">Q图系列[1]</a>
				<a href="javascript:;">Q图系列[2]</a>
				<a href="javascript:;">Q图系列[3]</a>
			</dd>
			<dd>
				<?php include ROOT_PATH.'includes/ubb.inc.php'?>
				<textarea name="content" rows="20"></textarea>
			</dd>
			<dd>
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
				<span>上传附件:</span>
			</dd>
			<dd>
				<input type="file" name="file[]" class="file"/>
				<input type="file" name="file[]" class="file"/>
				<input type="file" name="file[]" class="file"/>
			</dd>
			<dd class="center"><input type="submit" value="提交报告" class="submit"/></dd>
		</dl>	
		</form>
	</div>
</div>
<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>