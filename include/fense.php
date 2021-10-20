<?php
// ========================== 文件说明 ==========================//
// 本文件说明：检查刷新&代理
// --------------------------------------------------------------//
// 本程序作者：angel
// --------------------------------------------------------------//
// 本程序版本：SaBlog-X Ver 1.6
// --------------------------------------------------------------//
// 本程序主页：http://www.sablog.net
// ========================== 开发环境 ==========================//
// register_globals = Off
// --------------------------------------------------------------//
// magic_quotes_gpc = On
// --------------------------------------------------------------//
// safe_mode = On
// --------------------------------------------------------------//
// Windows server 2003 & Linux & FreeBSD
// --------------------------------------------------------------//
// Apache/1.3.33 & PHP/4.3.2 & MySQL/4.0.17
// --------------------------------------------------------------//
// Apache/1.3.34 & PHP/4.4.1 & MySQL/5.0.16
// --------------------------------------------------------------//
// Apache/2.0.55 & PHP/5.1.1 & MySQL/5.0.15
// --------------------------------------------------------------//
// Copyright (C) Security Angel Team All Rights Reserved.
// ==============================================================//

if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}

if($attackevasive == 1 || $attackevasive == 3) {
	if ($_COOKIE['lastrequest']) {
		list($lastrequest,$lastpath) = explode("\t",$_COOKIE['lastrequest']);
		$onlinetime = $timestamp - $lastrequest;
	} else {
		$lastrequest = $lastpath = '';
	}
	$REQUEST_URI  = $php_self.'?'.$_SERVER['QUERY_STRING'];
	if ($REQUEST_URI == $lastpath && $onlinetime < 2) {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Refresh" content="2;url=<?php echo $REQUEST_URI;?>">
<title>Refresh Limitation Enabled</title>
</head>
<body style="table-layout:fixed; word-break:break-all">
<center>
<div style="margin-top:100px;background-color:#f1f1f1;text-align:center;width:600px;padding:20px;margin-right: auto;margin-left: auto;font-family: Verdana, Tahoma; color: #666666; font-size: 12px">
  <p><b>Refresh Limitation Enabled</b></p>
  <p>The time between your two requests is smaller than 2 seconds, please do NOT refresh and wait for automatical forwarding ...</p>
</div>
</center>
</body>
</html>
<?
		exit;
	}
	setcookie('lastrequest',$timestamp."\t".$REQUEST_URI);
}

if(($attackevasive == 2 || $attackevasive == 3) && ($_SERVER['HTTP_X_FORWARDED_FOR'] || $_SERVER['HTTP_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION'] || $_SERVER['HTTP_USER_AGENT_VIA'])) {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Proxy Connection Denied</title>
</head>
<body style="table-layout:fixed; word-break:break-all">
<center>
<div style="margin-top:100px;background-color:#f1f1f1;text-align:center;width:600px;padding:20px;margin-right: auto;margin-left: auto;font-family: Verdana, Tahoma; color: #666666; font-size: 12px">
  <p><b>Proxy Connection Denied</b></p>
  <p>Your request was forbidden due to the administrator has set to deny all proxy connection.</p>
</div>
</center>
</body>
</html>
<?
	exit;
}
?>