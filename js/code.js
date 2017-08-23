//局部刷新验证码
function code(){
	var code=document.getElementById('code');
	if(code!=undefined){
		code.onclick=function(){
			//重新载入地址code.php?tm=(一个0到1不含1的随机数)无效随机变量tm用于避免从缓存中读取code
			this.src='code.php?tm='+Math.random();
		};
	}
}