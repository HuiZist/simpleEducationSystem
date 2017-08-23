<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','lesson_post');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//检查表单
if (!$_COOKIE['username']) {
	_location('请先登录!', 'login.php');
}
//上传文件和课程信息
if ($_GET['action'] == 'lesson_post') {
	if (!!$_rows_ex = _fetch_array("SELECT
			es_uniqid,
			es_level
			FROM
			es_user
			WHERE
			es_username='{$_COOKIE['username']}'
			LIMIT 1")) {
	_uniqid($_rows_ex['es_uniqid'],$_COOKIE['uniqid']);
	_examine_level($_rows_ex['es_level'], 1);
	
	//检查课程信息
	include ROOT_PATH.'includes/check.func.php';
	$_clean=array();
	$_clean['type']=$_POST['type'];
	$_clean['title']=_check_post_title($_POST['title'], 2, 40);
	$_clean['content']=_check_post_content($_POST['content'],2);
	$_clean['username']=$_COOKIE['username'];
	
	$_files_info = $_FILES['file'];
	//检查文件大小
	foreach ($_files_info['size'] as $_size){
		if ($_size > 100000000) {
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
	$_work_dir='lesson_file';
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
	_query("INSERT INTO es_lesson (
	es_type,
	es_title,
	es_content,
	es_username,
	es_files,
	es_date
	)
	VALUES (
	'{$_clean['type']}',
	'{$_clean['title']}',
	'{$_clean['content']}',
	'{$_clean['username']}',
	'{$_clean['file_path']}',
	NOW()
	)
	");
	if (_affected_rows() == 1) {
		$_clean['id'] = _insert_id();
		_close();
		_location('课程发布成功！','lesson.php?lesson_id='.$_clean['id']);
	} else {
		_close();
		_alert_back('课程发布失败！');
	}
		
	}else {
		
		_alert_back('非法登录！');
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工教学系统课程发布</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/lesson_post.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'includes/header.inc.php';
?>
<div id="lesson">
	<h2>课程发布</h2>
	<form enctype="multipart/form-data" action="?action=lesson_post" method="post">
		<dl>
			<dt></dt>
			<dd><span>课程名称:&nbsp;</span><input type="text" name="title" class="text"></dd>
			<dd><span>课程类型:&nbsp;</span>
				<select name="type">
					<option value="1" selected="selected">三菱</option>
					<option value="2">FANUC</option>
					<option value="3">Siemens</option>
					<option value="4">月工作报告</option>
				</select>
			</dd>
			<dd><span>课程介绍:</span></dd>
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
				<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
				<span>上传文件:</span>
			</dd>
			<dd><input type="file" name="file[]" class="file"/></dd>
			<dd><input type="file" name="file[]" class="file"/></dd>
			<dd><input type="file" name="file[]" class="file"/></dd>
			<dd><input type="file" name="file[]" class="file"/></dd>
			<dd><input type="file" name="file[]" class="file"/></dd>	
			<dd class="center"><input type="submit" value="发布" class="submit"/></dd>
		</dl>	
	</form>
</div>
<?php 
  require ROOT_PATH.'includes/footer.inc.php';
?>
</body>
</html>