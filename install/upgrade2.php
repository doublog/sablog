<?php
define('SABLOG_ROOT', '../');
// 加载数据库配置信息
require_once(SABLOG_ROOT.'config.php');
// 加载数据库类
require_once(SABLOG_ROOT.'include/func_db_mysql.php');
// 初始化数据库类
$DB = new DB_MySQL;
$DB->connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect);
unset($servername, $dbusername, $dbpassword, $dbname, $usepconnect);
$DB->unbuffered_query("ALTER TABLE `{$db_prefix}articles` CHANGE `content` `content` MEDIUMTEXT NOT NULL");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SABLOG-X v1.1 | Powered by 4ngel</title>
<link href="install.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="install_main">
  <div class="install_logo"><img src="top.gif" alt="安全天使" width="500" height="50" longdesc="http://www.4ngel.net/" /></div>
  <div id="install_innertext">
    <p class="p2">感谢您选择由 <a href="http://www.4ngel.net/" target="_blank">安全天使网络安全小组</a> 开发的 <a href="http://www.4ngel.net/">SaBlog-X</a> 博客程序!</p>
    <p class="p2">当看到此信息时您的版本已经成功升级为 <u>SaBlog-X v 1.1</u>.</p>
    <p class="p2">请检查 <?php echo $db_prefix;?>articles 表中的 content 字段类型是否为 <u>MEDIUMTEXT</u>, 如果不是请修改为为 <u>MEDIUMTEXT</u>.</p>
  </div>
</div>
<div class="copyright">Powered by SaBlog-X (C) 2003-2006 Security Angel Team</div>
</body>
</html>