<?php
// ========================== 文件说明 ==========================//
// 本文件说明：后台首页
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

if(!defined('SABLOG_ROOT') || !isset($php_self) || !preg_match("/[\/\\\\]admincp\.php$/", $php_self)) {
	exit('Access Denied');
}

function getphpcfg($varname) {
	switch($result = get_cfg_var($varname)) {
		case 0:
			return '关闭';
			break;
		case 1:
			return '打开';
			break;
		default:
			return $result;
			break;
	}
}

function getfun($funName) {
	return (function_exists($funName)) ? '支持' : '不支持';
}

if (@ini_get('file_uploads')) {
	$fileupload = '允许 '.ini_get('upload_max_filesize');
} else {
	$fileupload = '<font color="red">禁止</font>';
}

$globals  = getphpcfg('register_globals');
$safemode = getphpcfg('safe_mode');
$gd_version = gd_version();
$gd_version = $gd_version ? '版本:'.$gd_version : '不支持';

//查询数据信息
$hiddenarttatol = $DB->num_rows($DB->query("SELECT articleid FROM {$db_prefix}articles WHERE visible='0'"));
$hiddencomtatol = $DB->num_rows($DB->query("SELECT commentid FROM {$db_prefix}comments WHERE visible='0'"));
$hiddentracktatol = $DB->num_rows($DB->query("SELECT trackbackid FROM {$db_prefix}trackbacks WHERE visible='0'"));

$server['datetime'] = sadate('Y-m-d　H:i:s');
$server['software'] = $_SERVER['SERVER_SOFTWARE'];
if (function_exists('memory_get_usage')) {
	$server['memory_info'] = get_real_size(memory_get_usage());
}

$onlines = $waponlines = array();
$query = $DB->query("SELECT s.uid,s.ipaddress,s.lastactivity,s.groupid,u.username FROM {$db_prefix}sessions s LEFT JOIN {$db_prefix}users u ON (s.uid=u.userid) ORDER BY s.lastactivity");
while($online = $DB->fetch_array($query)) {
	$online['lastactivity'] = sadate('Y-m-d H:i:s', $online['lastactivity']);
	if ($online['groupid'] == 1 || $online['groupid'] == 2) {
		$onlines[] = $online;
	} else {
		$waponlines[] = $online;
	}
}
unset($online);
$DB->free_result($query);

$now_version = rawurlencode($SABLOG_VERSION);
$now_release = rawurlencode($SABLOG_RELEASE);
$now_hostname = rawurlencode($_SERVER['HTTP_HOST']);

$navlink_L = ' &raquo; 后台首页';
cpheader();
include PrintEot('main');
?>