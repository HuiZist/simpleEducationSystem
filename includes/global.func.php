<?php
/* 防止恶意调用 */
if(!defined('IN_TG')){
	exit('禁止直接访问该文件!');
}

/* 清除session */
function _session_destroy(){
	if(session_start()){
		session_destroy();
	}
}

/* 删除cookie */
function _unsetcookies(){
	setcookie('username','',time()-1);
	setcookie('uniqid','',time()-1);
	_session_destroy();
	_location(null,'index.php');
}

/* 获取当前时间，微秒级 */
function _now_time(){
	list($a,$b)=explode(' ', microtime());
	return $a+$b;
}

/* 限制发帖及回帖时间间隔 */
function _time($_now_time,$_pre_time,$_second){
	if($_now_time-$_pre_time<$_second){
		_alert_back('发帖/回帖时间间隔过短，请稍后再操作！');
	}
}

/* JS弹窗，并返回上一个页面 */
function _alert_back($_info){
	echo"<script type='text/javascript'>alert('$_info');history.back();</script>";
	exit();
}

/* JS弹窗，并关闭页面 */
function _alert_close($_info){
	echo"<script type='text/javascript'>alert('$_info');window.close();</script>";
	exit();
}

/* 弹窗然后跳转页面 */
function _location($_info,$_url){
	if(!empty($_info)){
		echo"<script type='text/javascript'>alert('$_info');location.href='$_url';</script>";
		exit();
	}else{
		header('Location:'.$_url);
	}
}

/* 登录状态的判断 */
function _login_state(){
	if(isset($_COOKIE['username'])){
		_alert_back('登录状态无法进行本操作！');
	}
}

/* 唯一标识符表单验证 */
function _uniqid($_mysql_uniqid,$_cookie_uniqid){
	if($_mysql_uniqid!=$_cookie_uniqid){
		_alert_back('唯一标识符异常！');
	}
}

/* 截取标题函数 */
function _title($_string,$_strlen=14){
	if(mb_strlen($_string,'utf-8')>$_strlen){
		/* 截取字符串 */
		$_string=mb_substr($_string,0,$_strlen,'utf-8').'...';
	}
	return $_string;
}

/* 课程上传文件命名 */
function _lesson_filename($_file_name,$_work_dir,$_lesson_name,$_num){
	$_n=explode('.',$_file_name);
	if (mb_strlen($_n[0],'utf-8')>10){
		$_file_name_cut=mb_substr($_n[0],0,10);
	}else {
		$_file_name_cut=$_n[0];
	}
	if (mb_strlen($_lesson_name,'utf-8')>16){
		$_lesson_name_cut=mb_substr($_lesson_name,0,8);
	}else {
		$_lesson_name_cut=$_lesson_name;
	}

	return $_file_new = $_work_dir.'/['.$_lesson_name_cut.$_num.']-'.$_file_name_cut.'.'.$_n[1];
	
}

/* html转义 ,如果输入字符串则直接过滤，如果是数组则过滤数组每一个字符串*/
function _html($_string){
	if(is_array($_string)){
		foreach($_string as $_key=>$_value){
			$_string[$_key]=_html($_value);
		}
	}else{
		$_string=htmlspecialchars($_string);
	}
	return $_string;
}

/* 对字符串进行转义，防止SQL注入 */
function _mysql_string($_string){
	if(is_array($_string)){
		foreach($_string as $_key=>$_value){
			$_string[$_key]=_mysql_string($_value);
		}
	}else{
		$_string=mysql_real_escape_string($_string);
	}
	return $_string;
}

/* 产生一个唯一标识符 */
function _sha1_uniqid(){
	return  _mysql_string(sha1(uniqid(rand(),true)));
}

/* 检查验证码是否匹配 */
function _check_code($_first_code,$_end_code){
	if($_first_code!=$_end_code){
		_alert_back('验证码不正确！');
	}
}

/* mysql模糊匹配特殊字符转义 */
function _search_string($_string){
	$_string = preg_replace('/\[/','/[',$_string);
	$_string = preg_replace('/_/','/_',$_string);
	$_string = preg_replace('/%/','/%',$_string);
	return $_string;
}

/* 验证权限 */
function _examine_level($_int_a,$_int_b){
	if($_int_a<$_int_b){
		_alert_back('权限不足！');
	}
}

/* 不能给自己发送消息 */
function _different_id($_first_id,$_second_id){
	if ($_first_id==$_second_id){
		_alert_close('不能给自己发送消息！');
	}
}

/* 生成头像 */
function _face_thumb($_filename,$_new_width,$_new_height){
	$_n = explode('.',$_filename);
	//获取文件信息，长和高
	list($_width, $_height) = getimagesize($_filename);
	$_new_image = imagecreatetruecolor($_new_width,$_new_height);
	//按照已有的图片创建一个画布
	switch ($_n[1]) {
		case 'jpg' : $_image = imagecreatefromjpeg($_filename);
		break;
		case 'gif' : $_image = imagecreatefromgif($_filename);
		break;
		default : $_image = imagecreatefrompng($_filename);
		break;
	}
	//将原图采集后重新复制到新图上，就缩略了
	imagecopyresampled($_new_image, $_image, 0, 0, 0, 0, $_new_width,$_new_height, $_width, $_height);
	switch ($_n[1]) {
		case 'jpg' : $_a=imagejpeg($_new_image,$_filename);
		break;
		case 'gif' : $_a=imagegif($_new_image,$_filename);
		break;
		default : $_a=imagepng($_new_image,$_filename);
		break;
	}
	imagedestroy($_new_image);
	imagedestroy($_image);
	return $_a;
}

/* 替换代码，显示UBB效果 */
function _ubb($_string) {
	$_string = nl2br($_string);
	$_string = preg_replace('/\[size=(.*)\]([\s\S]*)\[\/size\]/U','<span style="font-size:\1px">\2</span>',$_string);
	$_string = preg_replace('/\[b\]([\s\S]*)\[\/b\]/U','<strong>\1</strong>',$_string);
	$_string = preg_replace('/\[i\]([\s\S]*)\[\/i\]/U','<em>\1</em>',$_string);
	$_string = preg_replace('/\[u\]([\s\S]*)\[\/u\]/U','<span style="text-decoration:underline">\1</span>',$_string);
	$_string = preg_replace('/\[s\]([\s\S]*)\[\/s\]/U','<span style="text-decoration:line-through">\1</span>',$_string);
	$_string = preg_replace('/\[color=(.*)\]([\s\S]*)\[\/color\]/U','<span style="color:\1">\2</span>',$_string);
	$_string = preg_replace('/\[url\](.*)\[\/url\]/U','<a href="\1" target="_blank">\1</a>',$_string);
	$_string = preg_replace('/\[email\](.*)\[\/email\]/U','<a href="mailto:\1">\1</a>',$_string);
	$_string = preg_replace('/\[img\](.*)\[\/img\]/U','<img src="\1" alt="图片" />',$_string);
	$_string = preg_replace('/\[flash\](.*)\[\/flash\]/U','<embed style="width:650px;height:400px;" src="\1" />',$_string);
	return $_string;
}

/* 限制整数及其范围 */
function _limit_int($_int,$_min_size,$_max_size){
	if (empty($_int)||$_int<$_min_size||$_int>$_max_size||!is_numeric($_int)){
		$_int=1;
	}else{
		$_int=floor($_int);
	}
	return $_int;
}

/* 分页参数 */
function _page($_sql,$_size){
	//将几个参数变成全局变量：一页的资源数，页面起始的资源号的上一个资源号，当前页号，总页数，总资源数
	global $_pagesize,$_pagenum,$_page,$_pageabsolute,$_num;
	/* 分页模块 */
	if(isset($_GET['page'])){
		$_page=$_GET['page'];
		if(empty($_page)||$_page<=0||!is_numeric($_page)){
			$_page=1;
		}else{
			$_page=intval($_page);
		}
	}else{
		$_page=1;
	}
	$_pagesize=$_size;
	//获得结果集数量
	$_num=_num_rows(_query($_sql));
	if($_num==0){
		$_pageabsolute=1;
	}else{
		$_pageabsolute=ceil($_num/$_pagesize);
	}
	if($_page>$_pageabsolute){
		$_page=$_pageabsolute;
	}
	$_pagenum=($_page-1)*$_pagesize;
}

/* 分页函数 */
function _paging($_type){
	//不同函数间的全局变量都要定义
	global $_page,$_pageabsolute,$_num,$_id;
	if($_type==1){
		echo '<div id="page_num">';
		echo '<ul>';
		for($i=1;$i<=$_pageabsolute;$i++){
			if($i==$_page){
				echo '<li><a href="'.SCRIPT.'.php?'.$_id.'page='.$i.'" class="selected">'.$i.'</a></li>';
			}else{
				echo '<li><a href="'.SCRIPT.'.php?'.$_id.'page='.$i.'">'.$i.'</a></li>';
			}
		}
		echo '</ul>';
		echo '</div>';
	}elseif($_type==2){
		echo '<div id="page_text">';
		echo '<ul>';
		echo '<li>| '.$_page.'/'.$_pageabsolute.'页 | </li>';
		echo '<li>共有'.$_num.'条数据 | </li>';
		if($_page==1){
			echo '<li>首页 | <li>';
			echo '<li>上一页 | <li>';
		}else{
			echo '<li><a href="'.SCRIPT.'.php?'.$_id.'">首页</a> | <li>';
			echo '<li><a href="'.SCRIPT.'.php?'.$_id.'page='.($_page-1).'">上一页</a> | <li>';
		}
		if($_page==$_pageabsolute){
			echo '<li>下一页 | <li>';
			echo '<li>尾页 | <li>';
		}else{
			echo '<li><a href="'.SCRIPT.'.php?'.$_id.'page='.($_page+1).'">下一页</a> | <li>';
			echo '<li><a href="'.SCRIPT.'.php?'.$_id.'page='.$_pageabsolute.'">尾页</a> | <li>';
		}
		echo '</ul>';
		echo '</div>';
	}else{
		_alert_back('参数错误！');
	}
}

/* 生成缩略图 */
function _thumb($_filename,$_percent) {
	//生成png标头文件
	header('Content-type: image/png');
	$_n = explode('.',$_filename);
	//获取文件信息，长和高
	list($_width, $_height) = getimagesize($_filename);
	//生成缩微的长和高
	$_new_width = $_width * $_percent;
	$_new_height = $_height * $_percent;
	//创建一个以0.3百分比新长度的画布
	$_new_image = imagecreatetruecolor($_new_width,$_new_height);
	//按照已有的图片创建一个画布
	switch ($_n[1]) {
		case 'jpg' : $_image = imagecreatefromjpeg($_filename);
		break;
		case 'gif' : $_image = imagecreatefromgif($_filename);
		break;
		default : $_image = imagecreatefrompng($_filename);
		break;
	}
	//将原图采集后重新复制到新图上，就缩略了
	imagecopyresampled($_new_image, $_image, 0, 0, 0, 0, $_new_width,$_new_height, $_width, $_height);
	imagepng($_new_image);
	imagedestroy($_new_image);
	imagedestroy($_image);
}

/* 随机验证码函数 */
function _code($_rnd_code=4){
	//验证码大小公式
	$_width=$_rnd_code*75/4;
	$_height=26;
	//创建十六进制随机码，保存在session中
	for($i=0;$i<$_rnd_code;$i++){
		$_nmsg.=dechex(mt_rand(0, 15));
	}
	$_SESSION['code']=$_nmsg;

	header('Content-Type:image/png');
	$_img=imagecreatetruecolor($_width, $_height);

	//白色
	$_white=imagecolorallocate($_img,255,255,255);
	//填充
	imagefill($_img,0,0,$_white);
	//黑色边框
	$_flag=false;
	if($_flag){
		$_black=imagecolorallocate($_img,0,0,0);
		imagerectangle($_img,0,0,$_width-1,$_height-1,$_black);
	}
	//随机画出6个线条
	for($i=0;$i<6;$i++){
		$_rnd_color=imagecolorallocate($_img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
		imageline($_img,mt_rand(0,$_width),mt_rand(0,$_height),mt_rand(0,$_width),mt_rand(0,$_height),$_rnd_color);
	}
	//随机雪花
	for($i=0;$i<100;$i++){
		$_rnd_color=imagecolorallocate($_img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
		imagestring($_img,1,mt_rand(1,$_width),mt_rand(1,$_height),'*',$_rnd_color);
	}
	//输出验证码
	for($i=0;$i<strlen($_SESSION['code']);$i++){
		$_rnd_color=imagecolorallocate($_img,mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
		imagestring($_img,5,$i*$_width/$_rnd_code+mt_rand(1,10),mt_rand(1,$_height/2),$_SESSION['code'][$i],$_rnd_color);
	}
	//输出图像
	imagepng($_img);
	//释放掉图像
	imagedestory($_img);
}
function _forum_level($_experience){
	if ($_experience>=0 && $_experience<50){
		$_level = '边陲典史<span>1</span>';
	}elseif ($_experience>=50 && $_experience<100){
		$_level = '陪戎校尉<span>2</span>';
	}elseif ($_experience>=100 && $_experience<200){
		$_level = '黄门主薄<span>3</span>';
	}elseif ($_experience>=200 && $_experience<300){
		$_level = '仁勇校尉<span>4</span>';
	}elseif ($_experience>=300 && $_experience<500){
		$_level = '幽州别驾<span>5</span>';
	}elseif ($_experience>=500 && $_experience<700){
		$_level = '御侮校尉<span>6</span>';
	}elseif ($_experience>=700 && $_experience<1000){
		$_level = '京畿县丞<span>7</span>';
	}elseif ($_experience>=1000 && $_experience<1300){
		$_level = '宣节校尉<span>8</span>';
	}elseif ($_experience>=1300 && $_experience<1800){
		$_level = '国子监丞<span>9</span>';
	}elseif ($_experience>=1800 && $_experience<2500){
		$_level = '翊麾校尉<span>10</span>';
	}elseif ($_experience>=2500 && $_experience<3200){
		$_level = '监察御史<span>11</span>';
	}elseif ($_experience>=3200 && $_experience<4200){
		$_level = '致果校尉<span>12</span>';
	}elseif ($_experience>=4200 && $_experience<5200){
		$_level = '少府少监<span>13</span>';
	}elseif ($_experience>=5200 && $_experience<7200){
		$_level = '昭武校尉<span>14</span>';
	}elseif ($_experience>=7200 && $_experience<9200){
		$_level = '枢密承旨<span>15</span>';
	}elseif ($_experience>=9200 && $_experience<13000){
		$_level = '游骑将军<span>16</span>';
	}elseif ($_experience>=13000 && $_experience<18000){
		$_level = '太常少卿<span>17</span>';
	}elseif ($_experience>=18000 && $_experience<24000){
		$_level = '定远将军<span>18</span>';
	}elseif ($_experience>=24000 && $_experience<30000){
		$_level = '都指挥使<span>19</span>';
	}elseif ($_experience>=30000 && $_experience<35000){
		$_level = '云麾将军<span>20</span>';
	}elseif ($_experience>=35000 && $_experience<40000){
		$_level = '银青光禄<span>21</span>';
	}elseif ($_experience>=40000 && $_experience<45000){
		$_level = '金紫光禄<span>22</span>';
	}elseif ($_experience>=45000 && $_experience<50000){
		$_level = '御史大夫<span>23</span>';
	}elseif ($_experience>=50000 && $_experience<55000){
		$_level = '镇军大将<span>24</span>';
	}elseif ($_experience>=55000 && $_experience<60000){
		$_level = '太子少傅<span>25</span>';
	}elseif ($_experience>=60000 && $_experience<65000){
		$_level = '镇国大将<span>26</span>';
	}elseif ($_experience>=65000 && $_experience<70000){
		$_level = '骠骑大将<span>27</span>';
	}elseif ($_experience>=70000 && $_experience<75000){
		$_level = '太子太傅<span>28</span>';
	}elseif ($_experience>=75000 && $_experience<80000){
		$_level = '首辅丞相<span>29</span>';
	}elseif ($_experience>=80000){
		$_level = '冠军王侯<span>30</span>';
	}
	return $_level;
}