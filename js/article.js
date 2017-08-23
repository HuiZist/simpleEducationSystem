window.onload = function () {
	var ubb = document.getElementById('ubb');
	var fm = document.getElementById('subform');
	var font = document.getElementById('font');
	var color = document.getElementById('color');
	var html = document.getElementsByTagName('html')[0];
	var rere = document.getElementsByClassName('rere');
	var rea = document.getElementsByClassName('rea');
	
	if(rea[0]!=undefined){
		rea[0].onclick=function(){
			if(rere[0].style.display=='block'){
				rea[0].innerText='回复';
				rere[0].style.display='none';
			}else{
				rea[0].innerText='收起回复';
				rere[0].style.display='block';
			}
		}
	}
	if(rea[1]!=undefined){
		rea[1].onclick=function(){
			if(rere[1].style.display=='block'){
				rea[1].innerText='回复';
				rere[1].style.display='none';
			}else{
				rea[1].innerText='收起回复';
				rere[1].style.display='block';
			}
		}
	}
	if(rea[2]!=undefined){
		rea[2].onclick=function(){
			if(rere[2].style.display=='block'){
				rea[2].innerText='回复';
				rere[2].style.display='none';
			}else{
				rea[2].innerText='收起回复';
				rere[2].style.display='block';
			}
		}
	}
	if(rea[3]!=undefined){
		rea[3].onclick=function(){
			if(rere[3].style.display=='block'){
				rea[3].innerText='回复';
				rere[3].style.display='none';
			}else{
				rea[3].innerText='收起回复';
				rere[3].style.display='block';
			}
		}
	}
	if(rea[4]!=undefined){
		rea[4].onclick=function(){
			if(rere[4].style.display=='block'){
				rea[4].innerText='回复';
				rere[4].style.display='none';
			}else{
				rea[4].innerText='收起回复';
				rere[4].style.display='block';
			}
		}
	}
	if(rea[5]!=undefined){
		rea[5].onclick=function(){
			if(rere[5].style.display=='block'){
				rea[5].innerText='回复';
				rere[5].style.display='none';
			}else{
				rea[5].innerText='收起回复';
				rere[5].style.display='block';
			}
		}
	}
	
	if(fm!=undefined){
		fm.onsubmit = function () {
			if (fm.content.value.length < 10) {
				alert('内容不得小于10位');
				fm.content.focus(); //将焦点以至表单字段
				return false;
			}
			return true;
		};
	}
	
	var q = document.getElementById('q');
	if(q!=undefined){
		var qa = q.getElementsByTagName('a');
	
		qa[0].onclick = function() {
			window.open('q.php?num=48&path=qpic/1/','q','width=400,height=400,top=200,left=200,scrollbars=1');
		};
		qa[1].onclick = function() {
			window.open('q.php?num=10&path=qpic/2/','q','width=400,height=400,top=200,left=200,scrollbars=1');
		};
		qa[2].onclick = function() {
			window.open('q.php?num=39&path=qpic/3/','q','width=400,height=400,top=200,left=200,scrollbars=1');
		};
	}
	
	if(ubb!=undefined){
		html.onmouseup = function () {
		font.style.display = 'none';
		color.style.display = 'none';
		};
		
		var ubbimg = ubb.getElementsByTagName('img');
		ubbimg[0].onclick = function() {
			font.style.display = 'block';
		};
		ubbimg[2].onclick = function () {
			content('[b][/b]');
		};
		ubbimg[3].onclick = function () {
			content('[i][/i]');
		};
		ubbimg[4].onclick = function () {
			content('[u][/u]');
		};
		ubbimg[5].onclick = function () {
			content('[s][/s]');
		};
		ubbimg[7].onclick = function() {
			color.style.display = 'block';
			fm.t.focus();
		};
		ubbimg[8].onclick = function () {
			var url = prompt('请输入网址：','http://');
			if (url) {
				if (/^https?:\/\/(\w+\.)?[\w\-\.]+(\.\w+)+/.test(url)) {
					content('[url]'+url+'[/url]');
				} else {
					alert('网址不合法！');
				}
			}
		};
		ubbimg[9].onclick = function () {
			var email = prompt('请输入电子邮件：','@');
			if (email) {
				if (/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/.test(email)) {
					content('[email]'+email+'[/email]');
				} else {
					alert('电子邮件不合法！');
				}
			}
		};
		ubbimg[10].onclick = function () {
			var img = prompt('请输入图片地址：','');
			if (img) {
				content('[img]'+img+'[/img]');
			}
		};
		ubbimg[11].onclick = function () {
			var flash = prompt('请输入视频flash：','http://');
			if (flash) {
				if (/^https?:\/\/(\w+\.)?[\w\-\.]+(\.\w+)+/.test(flash)) {
					content('[flash]'+flash+'[/flash]');
				} else {
					alert('视频不合法！');
				}
			}
		};
		ubbimg[18].onclick = function () {
			fm.content.rows += 2;
		};
		ubbimg[19].onclick = function () {
			fm.content.rows -= 2;
		};
	}
	
	function content(string) {
			fm.content.value += string; 
		}
		
	if(fm!=undefined){
		fm.t.onclick = function () {
			showcolor(this.value);
		}
	}
};
function font(size) {
	var fm = document.getElementById('subform');
	fm.content.value += '[size='+size+'][/size]'
};

function showcolor(value) {
	var fm = document.getElementById('subform');
	fm.content.value += '[color='+value+'][/color]'
};

