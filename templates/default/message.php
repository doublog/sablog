<!--<?php
if(!defined('SABLOG_ROOT')) {
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
<title>系统消息 $options[title_keywords] - Powered by Sablog-X</title>
</head>
<body>
<div id="message">
  <h2>$options[name]</h2>
  <p style="margin-bottom:20px;"><strong>$msg</strong></p>
<!--
EOT;
if ($returnurl) {print <<<EOT
-->
  <p>$min 秒后将自动跳转<br /><a href="$returnurl">如果不想等待或浏览器没有自动跳转请点击这里跳转</a></p>
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
