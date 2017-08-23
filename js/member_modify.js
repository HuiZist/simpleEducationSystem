window.onload=function(){
 	//表单验证
	var fm = document.getElementsByTagName('form')[0];
	var up = document.getElementById('up');
	var password_change = document.getElementById('password_change');
	up.onclick = function () {
		centerWindow('upface.php','上传头像','200','400');
	};
	password_change.onclick = function () {
		centerWindow('password_change.php','密码修改','250','400');
	};
	//点击提交执行JS验证函数
	fm.onsubmit=function(){
		//能在客户端验证的尽量用JS
		//用户名验证
		if(fm.nickname.value.length<2||fm.nickname.value.length>20){
			alert('用户名不得小于2位或者大于20位');
			fm.nickname.value='';//清空
			fm.nickname.focus();//将焦点移至名字框
			return false;
		}
		//邮箱验证
		if(!/^[\w\_\.]+@[\w\_\.]+(\.\w+)+$/.test(fm.email.value)){
			alert('邮箱格式不正确！');
			fm.email.value='';//清空
			fm.email.focus();//将焦点移至名字框
			return false;
		}	
		return true;	
	};
};
function centerWindow(url,name,height,width) {
	var left = (screen.width - width) / 2;
	var top = (screen.height - height) / 2;
	window.open(url,name,'height='+height+',width='+width+',top='+top+',left='+left);
}