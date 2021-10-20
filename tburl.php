<?php
// ========================== 文件说明 ==========================//
// 本文件说明：获取Trackback地址
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

require_once('global.php');

$id = intval($_GET['id']);

$article = $DB->fetch_one_array("SELECT dateline,trackbacks FROM {$db_prefix}articles WHERE articleid='$id'");
if (!$article) {
	message('文章不存在.', './');
}
$code = rawurlencode(authcode("$id\t$article[dateline]\t$article[trackbacks]"));

$html = '<h2><a href="javascript:;" onclick="$(\'ajax-div-trackback\').style.display=\'none\';">关闭</a>Trackback</h2>
<div>
<a href="trackback.php?code='.$code.'" onclick="setCopy(this.href);return false;" target="_self">点击复制Trackback链接到剪切板</a>
</div>';

header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<root><![CDATA[".$html."]]></root>\n";
?>