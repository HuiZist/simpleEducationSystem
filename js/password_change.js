window.onload=function(){
	code();
	//表单验证
	var fm=document.getElementsByTagName('form')[0];
	if(fm==undefined) return;
	//点击提交执行JS验证函数
	fm.onsubmit=function(){
		//登录密码验证
		if(fm.password.value.length<6||fm.password.value.length>20){
			alert('登录密码错误，不得小于6位或者大于20位!');
			fm.password.value='';//清空
			fm.password.focus();//将焦点移至名字框
			return false;
		}
		//新密码验证
		if(fm.newpassword.value==fm.password.value){
			alert('密码未修改!');
			fm.newpassword.value='';//清空
			fm.newpassword.focus();//将焦点移至名字框
			return false;
		}
		if(fm.newpassword.value.length<6||fm.newpassword.value.length>20){
			alert('新密码错误，不得小于6位或者大于20位!');
			fm.newpassword.value='';//清空
			fm.newpassword.focus();//将焦点移至名字框
			return false;
		}
		if(fm.notpassword.value!=fm.newpassword.value){
			alert('新密码与确认密码不相同！');
			fm.notpassword.value='';//清空
			fm.notpassword.focus();//将焦点移至名字框
			return false;
		}
		//验证码长度验证
		if(fm.code.value.length!=4){
			alert('验证码必须是4位！');
			fm.code.value='';//清空
			fm.code.focus();//将焦点移至名字框
			return false;
		}
		
		return true;
	}
}