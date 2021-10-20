<?php
// ========================== 文件说明 ==========================//
// 本文件说明：后台公共函数
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

// 加载公用函数
require_once('../include/common.php');
// 加载后台常用函数
require_once(SABLOG_ROOT.'admin/adminfunctions.php');

if (!$sax_uid || !$sax_pw || ($sax_group != 1 && $sax_group != 2)) {
	loginpage();
}

// 加载缓存操作函数
require_once(SABLOG_ROOT.'include/cache.php');

// 检查安装文件是否存在
if (file_exists('../install')) {
	exit('Installation directory: install/ is still on your server. Please DELETE it or RENAME it now.');
}

$action = $_GET['action'] ? $_GET['action'] : $_POST['action'];
$job    = $_GET['job'] ? $_GET['job'] : $_POST['job'];

// 登陆验证
if ($action == 'login') {
	$password = md5($_POST['password']);
	$userinfo = $DB->fetch_one_array("SELECT username,password,logincount,groupid FROM {$db_prefix}users WHERE userid='$sax_uid'");
	if ($userinfo['password'] == $password && $userinfo['logincount'] == $logincount && $userinfo['username'] == $sax_user && ($userinfo['groupid'] == 1 || $userinfo['groupid'] == 2)) {
		$adminhash = getadminhash($sax_uid,$sax_user,$password,$logincount);
		$admininfo = getadmininfo($password);
		setcookie('sax_admin', authcode("$sax_uid\t$adminhash\t$admininfo\t$onlineip"));
		$DB->query("DELETE FROM {$db_prefix}sessions WHERE uid='$sax_uid' OR lastactivity+1800<'$timestamp' OR hash='$adminhash'");
		$DB->query("INSERT INTO {$db_prefix}sessions (hash,uid,groupid,ipaddress,lastactivity) VALUES ('$adminhash', '$sax_uid', '$sax_group', '$onlineip', '$timestamp')");
		loginresult('Succeed');
		if ($_SERVER['QUERY_STRING']) {
			redirect('登陆成功,请稍候...', 'admincp.php'.$_SERVER['QUERY_STRING']);
		}
        redirect('登陆成功,请稍候...', 'admincp.php');
	} else {
		loginresult('Failed');
		loginpage();
	}
}

// 验证用户是否处于登陆状态
list($admin_id, $admin_hash, $admin_info, $admin_ip) = $_COOKIE['sax_admin'] ? explode("\t", authcode($_COOKIE['sax_admin'], 'DECODE')) : array('', '', '', '');
$admin_id = intval($admin_id);
$admin_hash = addslashes($admin_hash);
$admin_info = addslashes($admin_info);
$admin_ip = addslashes($admin_ip);
if ($admin_id && $admin_hash && $admin_hash && $admin_ip) {
	$session = $DB->fetch_one_array("SELECT * FROM {$db_prefix}sessions WHERE uid='$admin_id' AND groupid='$sax_group' AND hash='$admin_hash' AND lastactivity+1800>'$timestamp' AND ipaddress='$admin_ip'");
	if (!$session) {
		$DB->query("DELETE FROM {$db_prefix}sessions WHERE uid='$admin_id' OR hash='$admin_hash'");
		loginpage();
	}
	$userinfo = $DB->fetch_one_array("SELECT userid,username,password,logincount,groupid FROM {$db_prefix}users WHERE userid='".$session['uid']."'");
	if (!$userinfo) {
		loginpage();
	}
	$adminhash = getadminhash($userinfo['userid'],$userinfo['username'],$userinfo['password'],$userinfo['logincount']);
	$admininfo = getadmininfo($userinfo['password']);
	if ($admin_hash != $adminhash || $admin_info != $admininfo || $admin_ip != $session['ipaddress']) {
		loginpage();
	}
	$DB->query("UPDATE {$db_prefix}sessions SET lastactivity='$timestamp' WHERE uid='$admin_id' AND hash='$admin_hash'");
} else {
	loginpage();
}
// 验证登陆状态结束

if ($action == 'logout') {
	$DB->query("DELETE FROM {$db_prefix}sessions WHERE uid='$admin_id' OR hash='$admin_hash'");
	setcookie('sax_admin', '');
	redirect('<b>注销成功, 请稍后...</b>', '../');
}

// 提交自动保存数据
if ($action == 'autosave') {
	if ($_POST['title'] || $_POST['description'] || $_POST['content']) {
		autosave_recache($_POST['title'], $_POST['description'], $_POST['content']);
	}
}

// 记录管理的一切操作
getlog();

?>