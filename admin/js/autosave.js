var AutoSaveTime	= 60;   //修改每次保存时间(秒)
var AutoHideMsg		= 55;

var autosave_time		= '秒后自动保存';
var autosave_wait		= '正在保存...';
var autosave_ok			= '已自动保存';
var autosave_error		= '自动保存错误';
var autosave_disable	= '禁止自动保存';

var autosaveroff = getCookie('autosaveroff');
if (autosaveroff!=1) {
	savertimer = window.setTimeout("timer()", 0);
}

savetime=AutoSaveTime;
function timer() { 
	var timemsg = document.getElementById('timemsg');
	var timemsg2 = document.getElementById('timemsg2');
	savetime = savetime - 1;
	timemsg.innerHTML = savetime + autosave_time;
	if (savetime >= 0){
		savertimer = window.setTimeout("timer()", 1000);
		if (savetime==AutoHideMsg) timemsg2.innerHTML='';
	}
	else {
		if (savetime<=-1000) {
			savetime=AutoSaveTime;timer();
		} else {
			timemsg.innerHTML = autosave_wait;
			savedraft();
			savetime=AutoSaveTime;
			timer();
		}
	}
}

function savedraft() {
	var title = document.getElementById('title').value;
	var description = FCKeditorAPI.GetInstance('description').GetXHTML(true);
	var content = FCKeditorAPI.GetInstance('content').GetXHTML(true);
	var gourl = 'admincp.php?action=autosave';
	var postData = 'title='+title+'&description='+description+'&content='+content;
	makeRequest(gourl, 'savemydraft', 'POST', postData);
}

function handsave(){
	savedraft();
}

function savemydraft() {
	var timemsg2=document.getElementById('timemsg2');
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			timemsg2.innerHTML = autosave_ok;
			savetime=-1000;
		}  else {
			alert(autosave_error);
		}
	}
}

function stopautosaver () {
	clearTimeout(savertimer);
}

function restartautosaver () {
	savertimer = window.setTimeout("timer()", 0);
	var dateObjexp= new Date();
	dateObjexp.setFullYear(2010); 
	setCookie('autosaveroff', 0,dateObjexp, null, null, false);
}

function stopforever() {
	var dateObjexp= new Date();
	dateObjexp.setFullYear(2010); 
	setCookie('autosaveroff', 1,dateObjexp, null, null, false);
	stopautosaver();
	document.getElementById('timemsg').innerHTML=autosave_disable;
}

var http_request = false;
function makeRequest(url, functionName, httpType, sendData) {

	http_request = false;
	if (!httpType) httpType = "GET";

	if (window.XMLHttpRequest) { // Non-IE...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/plain');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}

	if (!http_request) {
		alert('Cannot send an XMLHTTP request');
		return false;
	}

	var changefunc="http_request.onreadystatechange = "+functionName;
	eval (changefunc);
	//http_request.onreadystatechange = alertContents;
	http_request.open(httpType, url, true);
	http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8');
	http_request.send(sendData);
}

//Cookie
function setCookie(name,value,expiry,path,domain,secure) {
	var nameString = name + "=" + value;
	var expiryString = (expiry == null) ? "" : " ;expires = "+ expiry.toGMTString();
	var pathString = (path == null) ? "" : " ;path = "+ path;
	var domainString = (path == null) ? "" : " ;domain = "+ domain;
	var secureString = (secure) ?";secure" :"";
	document.cookie = nameString + expiryString + pathString + domainString + secureString;
}

function getCookie (name) {
	var CookieFound = false;
	var start = 0;
	var end = 0;
	var CookieString = document.cookie;
	var i = 0;

	while (i <= CookieString.length) {
		start = i ;
		end = start + name.length;
		if (CookieString.substring(start, end) == name){
			CookieFound = true;
			break;
		}
		i++;
	}

	if (CookieFound){
		start = end + 1;
		end = CookieString.indexOf(";",start);
		if (end < start) end = CookieString.length;
		return unescape(CookieString.substring(start, end));
	}
	return "";
}

function deleteCookie(name) {
	var expires = new Date();
	expires.setTime (expires.getTime() - 1);
	setCookie( name , "Delete Cookie", expires,null,null,false);
}