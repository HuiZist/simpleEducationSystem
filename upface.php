<?php
//定义个常量，用来授权调用includes里面的文件
define('IN_TG',true);
//定义个常量，用来指定本页的内容
define('SCRIPT','upface');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';
//用户才能进入
if (!$_COOKIE['username']) {
	_alert_back('非法登录！');
}
//执行上传图片功能
if ($_GET['action'] == 'up') {
	if (!!$_rows = _fetch_array("SELECT
											es_uniqid
									FROM 
											es_user 
									WHERE 
											es_username='{$_COOKIE['username']}' 
							 LIMIT 1")) {
		_uniqid($_rows['es_uniqid'],$_COOKIE['uniqid']);
		//设置上传图片的类型
		$_files = array('image/jpeg','image/pjpeg','image/png','image/x-png','image/gif');
		
		//判断类型是否是数组里的一种
		if (is_array($_files)) {
			if (!in_array($_FILES['userfile']['type'],$_files)) {
				_alert_back('上传文件类型必须是图片jpg,png,gif中的一种！');
			}
		}
		
		//判断文件错误类型
		if ($_FILES['userfile']['error'] > 0) {
			switch ($_FILES['userfile']['error']) {
				case 1: _alert_back('上传文件超过约定值1');
					break;
				case 2: _alert_back('上传文件超过约定值2');
					break;
				case 3: _alert_back('部分文件被上传');
					break;
				case 4: _alert_back('没有任何文件被上传！');
					break;
			}
			exit;
		}
		
		//判断配置大小
		if ($_FILES['userfile']['size'] > 1000000) {
			_alert_back('上传的文件大小不得超过1M');
		}
		//获取文件扩展名
		$_n=explode('.',$_FILES['userfile']['name']);
		$_name='faces/'.time().mt_rand(10, 99).'.'.$_n[1];
		//移动文件
		if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			if	(!@move_uploaded_file($_FILES['userfile']['tmp_name'],$_name)) {
				_alert_back('移动失败');
			} else {
				//将图片缩放为100X100,当头像生成函数执行失败时删除上传图片
				if(!_face_thumb($_name,100,100)){
					unlink($_name);
					_alert_back('头像处理失败！');
				}
				//查询用户原来头像路径，如果不是默认头像，则更改头像后删除原头像
				$_facepath=_fetch_array("SELECT
												es_face
										FROM
												es_user
										WHERE
												es_username='{$_COOKIE['username']}'
										LIMIT 1");
				if ($_facepath['es_face']!='faces/defaultface.jpg'){
					unlink($_facepath['es_face']);
				}
				//修改资料
				_query("UPDATE 
								es_user 
						SET
								es_face='$_name'
						WHERE
								es_username='{$_COOKIE['username']}'
						");
				_alert_close('上传成功！');
			}
		} else {
			_alert_back('上传的临时文件不存在！');
		}
		
	} else {
		_alert_back('非法登录！');
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>智能加工教学系统上传头像</title>
<?php 
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>
<body>
<div id="upface">
	<h2>上传头像</h2>
	<form enctype="multipart/form-data" action="upface.php?action=up" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
		<span>选择图片: </span><input type="file" name="userfile" class="choose"/>
		<input type="submit" value="上传" class="submit"/>
	</form>
</div>
</body>
</html>