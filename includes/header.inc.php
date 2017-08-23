<?php
//防止恶意调用
if(!defined('IN_TG')){
	exit('禁止直接访问该文件!');
}
?>
<script type="text/javascript" src="js/personage.js"></script>
<div id="header">
	<h1><a href="index.php">智能加工中心教学系统</a></h1>
	<ul>
		<li><a href="index.php">主页导航</a></li>
		<li><a href="lesson_list.php?area=1">课程中心</a></li>
		<li><a href="forum.php?area=1">交流论坛</a></li>
		<li><a href=###>考核机制</a></li>
		<?php 
			if(isset($_COOKIE['username'])){
				//获取数据
				$_header=_fetch_array("SELECT
						es_level,
						es_face
						FROM
						es_user
						WHERE
						es_username='{$_COOKIE['username']}'
						");
				echo '
					<li onmouseover="inpersonage()" onmouseout="outpersonage()">
						<a href="member.php"><img src="thumb.php?filename='.$_header[es_face].'&percent=0.3" title="账号：'.$_COOKIE['username'].' 的个人中心"></a>
						<dl id="personage">
							<dd><a href="logout.php">退出</a></dd>
						</dl>
					</li>
			';
			}else{
				echo '<li><a href="login.php">登录/注册</a></li>';
			}
		?>
	</ul>
</div>