window.onload = function () {
	var img = document.getElementsByTagName('img');
	var fm = opener.document.getElementById('subform');
	for (i=0;i<img.length;i++) {
		img[i].onclick = function () {
			_opener(fm,this.alt);
		};
	}
};
function _opener(fm,src) {
	//opener表示父窗口.document表示文档
	fm.content.value += '[img]'+src+'[/img]';
}