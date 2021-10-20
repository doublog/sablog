function $(id) {
	return document.getElementById(id);
}

function ctlent(event) {
	if((event.ctrlKey && event.keyCode == 13) || (event.altKey && event.keyCode == 83)) {
		$("submit").click();
	}
}

function addquote(obj,strAuthor){
	var text = $(obj).innerHTML;
	text = text.replace(/alt\=(\"|)([^\"\s]*)(\"|)/g,"> $2 <");
	text = text.replace(/\<[^\<\>]+\>/g,"\n");
	text = text.replace(/ +/g," ");
	text = text.replace(/\n+/g,"\n");
	text = text.replace(/^\n*/gm,"");
	text = text.replace(/^\s*/gm,"");
	text = text.replace(/\n*$/gm,"");
	text = text.replace(/\s*$/gm,"");
	text = text.replace(/&lt;/g,"<");
	text = text.replace(/&gt;/g,">");
	text = text.replace(/&nbsp;&nbsp;/g,"  ");
	text = text.replace(/&amp;/g,"&");
	$("content").value += "[quote="+strAuthor+"]"+text+"[/quote]";
	$("content").focus();
}

function checkform() {
	if ($('username') && $('username').value == "") {
		alert("请输入您的名字.");
		return false;
	}
	if ($('content') && $('content').value == "")	{
		alert("请输入内容.");
		return false;
	}
	if ($('clientcode') && $('clientcode').value == "")	{
		alert("请输入验证码.");
		return false;
	}
	if (((postminchars != 0 && $('content').value.length < postminchars) || (postmaxchars != 0 && $('content').value.length > postmaxchars))) {
		alert("您的评论内容长度不符合要求。\n\n当前长度: "+$('content').value.length+" 字节\n系统限制: "+postminchars+" 到 "+postmaxchars+" 字节");
		return false;
	}			
	$('submit').disabled = true;
	return true;
}

function checkloginform() {
	if ($('username').value == "") {
		alert("请输入您的名字.");
		return false;
	}
	if ($('password').value == "" || ($('confirmpassword') && $('confirmpassword').value == "")) {
		alert("请输入密码和确认密码.");
		return false;
	}
	if ($('confirmpassword') && $('password').value !== $('confirmpassword').value) {
		alert("两次输入密码不一样,请重新输入.");
		return false;
	}
	if ($('clientcode') && $('clientcode').value == "")	{
		alert("请输入验证码.");
		return false;
	}
	return true;
}

function showhide(obj) {
	$(obj).style.display = $(obj).style.display == "none" ? "block" : "none";
}

function showajaxdiv(action, url, width) {
	var x = new Ajax('statusid', 'XML');
	x.get(url, function(s) {
		if($("ajax-div-"+action)) {
			var divElement = $("ajax-div-"+action);
		} else {
			var divElement = document.createElement("DIV");
			divElement.id = "ajax-div-"+action;
			divElement.className = "ajaxdiv";
			document.body.appendChild(divElement);
		}
		divElement.style.cssText = "width:"+width+"px;";
		var userAgent = navigator.userAgent.toLowerCase();
		var is_opera = (userAgent.indexOf('opera') != -1);
		var clientHeight = scrollTop = 0; 
		if(is_opera) {
			clientHeight = document.body.clientHeight /2;
			scrollTop = document.body.scrollTop;
		} else {
			clientHeight = document.documentElement.clientHeight /2;
			scrollTop = document.documentElement.scrollTop;
		}
		divElement.innerHTML = s.lastChild.firstChild.nodeValue;
		divElement.style.left = (document.documentElement.clientWidth /2 +document.documentElement.scrollLeft - width/2)+"px";
		divElement.style.top = (clientHeight +　scrollTop - divElement.clientHeight/2)+"px";
	});	
}

function setCopy(content){
	if(navigator.userAgent.toLowerCase().indexOf('ie') > -1) {
		clipboardData.setData('Text',content);
		alert ("该地址已经复制到剪切板");
	} else {
		prompt("请复制网站地址:",content); 
	}
}

function fiximage(thumbs_size) {
	var max = thumbs_size.split('x');
	var fixwidth = max[0];
	var fixheight = max[1];
	imgs = document.getElementsByTagName('img');
	for(i=0;i<imgs.length;i++) {
		w=imgs[i].width;h=imgs[i].height;
		if(w>fixwidth) { imgs[i].width=fixwidth;imgs[i].height=h/(w/fixwidth);}
		if(h>fixheight) { imgs[i].height=fixheight;imgs[i].width=w/(h/fixheight);}
	}
}