window.onload=function(){
	//表单验证
	var fm=document.getElementById('change');
	if(fm==undefined) return;
	//点击提交执行JS验证函数
	fm.onsubmit=function(){
		//登录密码验证
		if(fm.newps.value.length<6||fm.newps.value.length>20){
			alert('密码错误，不得小于6位或者大于20位!');
			fm.newps.value='';//清空
			fm.newps.focus();//将焦点移至名字框
			return false;
		}
		//新密码验证
		if(fm.reps.value!=fm.newps.value){
			alert('新密码与确认密码不相同！');
			fm.reps.value='';//清空
			fm.reps.focus();//将焦点移至名字框
			return false;
		}
		
		return true;
	}
}