//等待网页加载完毕再执行
window.onload=function(){
	code();
	
	//表单验证
	var fm=document.getElementsByTagName('form')[0];
	if(fm==undefined) return;
	//点击提交执行JS验证函数
	fm.onsubmit=function(){
		//能在客户端验证的尽量用JS
		//账号验证
		if(fm.username.value.length<2||fm.username.value.length>20){
			alert('账号不得小于2位或者大于20位');
			fm.username.value='';//清空
			fm.username.focus();//将焦点移至名字框
			return false;
		}
		if(!/[\w\_]+/.test(fm.username.value)){
			alert('账号只能由字母，数字或下划线组成！');
			fm.username.value='';//清空
			fm.username.focus();//将焦点移至名字框
			return false;
		}
		
		//密码验证
		if(fm.password.value.length<6||fm.password.value.length>20){
			alert('密码不得小于6位或者大于20位!');
			fm.password.value='';//清空
			fm.password.focus();//将焦点移至名字框
			return false;
		}
		if(fm.notpassword.value!=fm.password.value){
			alert('密码与确认密码不相同！');
			fm.notpassword.value='';//清空
			fm.notpassword.focus();//将焦点移至名字框
			return false;
		}
		
		//密码提示问题与回答
		if(fm.question.value.length<2||fm.question.value.length>20){
			alert('密码提示不得小于2位或者大于20位!');
			fm.question.value='';//清空
			fm.question.focus();//将焦点移至名字框
			return false;
		}
		if(fm.answer.value.length<2||fm.answer.value.length>20){
			alert('密码回答不得小于2位或者大于20位!');
			fm.answer.value='';//清空
			fm.answer.focus();//将焦点移至名字框
			return false;
		}
		if(fm.question.value==fm.answer.value){
			alert('密码提示不得与密码回答相同！');
			fm.answer.value='';//清空
			fm.answer.focus();//将焦点移至名字框
			return false;
		}
		
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
		
		//验证码长度验证
		if(fm.code.value.length!=4){
			alert('验证码必须是4位！');
			fm.code.value='';//清空
			fm.code.focus();//将焦点移至名字框
			return false;
		}
		
		return true;
	};
};


