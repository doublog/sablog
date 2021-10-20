<?php
// ========================== 文件说明 ==========================//
// 本文件说明：访问统计
// --------------------------------------------------------------//
// 本程序作者：angel
// --------------------------------------------------------------//
// 本程序版本：SaBlog-X Ver 1.6
// --------------------------------------------------------------//
// 本程序主页：http://www.4ngel.net
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

$kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
$kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';

//判断机器人，如果是不是机器人的信息，并且有浏览器的信息，才统计
if(!preg_match("/($kw_spiders)/", $_SERVER['HTTP_USER_AGENT']) && preg_match("/($kw_browsers)/", $_SERVER['HTTP_USER_AGENT'])) {
	if ($_COOKIE['UserIP'] != $onlineip) {
		setcookie ('UserIP', $onlineip, $timestamp + 24 * 3600);
		$curtime = sadate('Y-m-d');
		$rs = $DB->fetch_one_array("SELECT curdate FROM {$db_prefix}statistics WHERE curdate='$curtime'");
		if(!$rs) {
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET curdate ='$curtime', today_view_count = '1'");
		} else {
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET today_view_count = today_view_count+1");
		}
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET all_view_count = all_view_count+1");

		if ($stats_expire < $timestamp) {
			require_once(SABLOG_ROOT.'include/cache.php');
			statistics_recache();
		}
	}
}

?>