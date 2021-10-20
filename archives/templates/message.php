<!--<?php
if(!defined('IN_SABLOG')) {
	exit('Access Denied');
}
print <<<EOT
--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="keywords" content="4ngel,4ngel.net,安全,天使,安全天使,技术,黑客,网络,原创,论坛,自由,严肃,网络安全,组织,系统安全,系统,windows,web,web安全,web开发,$options[meta_keywords]" />
<meta name="description" content="4ngel,4ngel.net,安全,天使,安全天使,技术,黑客,网络,原创,论坛,自由,严肃,网络安全,组织,系统安全,系统,windows,web,web安全,web开发,$options[meta_description]" />
<meta name="copyright" content="SaBlog" />
<meta name="author" content="angel,4ngel" />
<link rel="stylesheet" href="templates/$options[templatename]/style.css" type="text/css" media="all"  />
<!--
EOT;
if ($returnurl) {print <<<EOT
-->
<meta http-equiv="REFRESH" content="$min;URL=$returnurl">
<!--
EOT;
}print <<<EOT
-->
<style type="text/css">
body {
	margin: 0px;
	line-height: 140%;
	color: #000000;
	font: 12px "Verdana", "Tahoma", "sans-serif", "宋体";
	background-color: #cdd6dd;
	text-align: center;
}
#message {
	margin-top: 100px;
	background-color: #FFFFFF;
	text-align:center;
	width: 500px;
	padding: 20px;
	border: 1px dotted #386792;
	margin-right: auto;
	margin-left: auto;
}
</style>
<title>系统消息 $options[title_keywords] - Powered by Sablog-X</title>
</head>
<body style="table-layout:fixed; word-break:break-all">
<div id="message">
  <p><b>$msg</b></p>
<!--
EOT;
if ($returnurl) {print <<<EOT
-->
  <p>$min 秒后将自动跳转<br /><a href="$returnurl">如果你不想等待或浏览器没有自动跳转请点击这里跳转</a></p>
<!--
EOT;
}print <<<EOT
-->
</div>
</body>
</html>
<!--
EOT;
?>-->
