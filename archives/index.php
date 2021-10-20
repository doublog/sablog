<?php
// ========================== 文件说明 ==========================//
// 本文件说明：归档首页
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

define('IN_SABLOG', TRUE);
require_once('include/global.php');
require_once(SABLOG_ROOT.'include/visits.php');

$script = $_SERVER['QUERY_STRING'];

$script = preg_replace("/\.".$options['artlink_ext']."$/i", '', trim($script));

if($script) {
	$parts = explode('-', $script);
	foreach($parts as $part) {
		if(empty($lastpart)) {
			$lastpart = in_array($part, array('article')) ? $part : '';
		} else {
			$$lastpart = intval($part);
			$lastpart = '';
		}
	}
}

if($article) {
	$action = 'article';
} else {
	$action = 'index';
}

require_once('include/'.$action.'.inc.php');
footer();
?>