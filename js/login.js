window.onload=function(){
	code();
	//表单验证
	var fm=document.getElementsByTagName('form')[0];
	
	//点击提交执行JS验证函数
	fm.onsubmit=function(){
		//能在客户端验证的尽量用JS
		//用户名验证
		if(fm.username.value.length<2||fm.username.value.length>20){
			alert('账号不得小于2位或者大于20位');
			fm.username.value='';//清空
			fm.username.focus();//将焦点移至名字框
			return false;
		}
		
		//密码验证
		if(fm.password.value.length<6){
			alert('密码不得小于6位!');
			fm.password.value='';//清空
			fm.password.focus();//将焦点移至名字框
			return false;
		}
		//验证码长度验证
		if(fm.code.value.length!=4){
			alert('验证码必须是4位！');
				fm.code.value='';//清空
				fm.code.focus();//将焦点移至名字框
				return false;
		}
	}
};