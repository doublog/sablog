<?php
// ========================== 文件说明 ==========================//
// 本文件说明：友情链接管理
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

//权限检查
permission();

// 检查链接名字是否符合逻辑
function checksitename($sitename) {
	if(!$sitename || strlen($sitename) > 30) {
		$result = '站点名不能空并不能大于30个字符<br />';
		return $result;
	}
	elseif(eregi("[<>{}(),%#|^&!`$]",$sitename)) {
		$result = '站点名中不能含有特殊字符<br />';
		return $result;
	}
}

// 检查链接描述是否符合逻辑
function checknote($note = '') {
	if($note && strlen($note) > 200) {
		$result = '站点描述不能大于200个字符<br />';
		return $result;
	}
}

//添加链接
if($_POST['action'] == 'addlink') {
	$name    = trim($_POST['name']);
	$url     = trim($_POST['url']);
	$note    = trim($_POST['note']);
	$visible = intval($_POST['visible']);
	$result  = checksitename($name);
	$result .= checkurl($url,0);
	$result .= checknote($note);
	if($result) {
		redirect($result);
	}
	$name    = char_cv($name);
	$url     = char_cv($url);
	$note    = char_cv($note);

	$rs = $DB->fetch_one_array("SELECT count(*) AS links FROM {$db_prefix}links WHERE name='$name' AND url='$url'");
    if($rs['links'])	{
        redirect('该链接在数据库中已存在', 'admincp.php?job=link');
    }
    $DB->query("INSERT INTO {$db_prefix}links (name, url, note, visible) VALUES ('$name', '$url', '$note' ,'$visible')");
	links_recache();
    redirect('添加链接成功', 'admincp.php?job=link');
}
//批量处理
if($_POST['action'] == 'domorelink') {
	if($ids = implode_ids($_POST['delete'])) {
		$DB->query("DELETE FROM	{$db_prefix}links WHERE linkid IN ($ids)");
	}
	if(is_array($_POST['name'])) {
		foreach($_POST['name'] as $linkid => $value) {
			$DB->unbuffered_query("UPDATE {$db_prefix}links SET displayorder='".intval($_POST['displayorder'][$linkid])."', name='".char_cv(trim($_POST['name'][$linkid]))."', url='".char_cv(trim($_POST['url'][$linkid]))."', note='".char_cv(trim($_POST['note'][$linkid]))."', visible='".intval($_POST['visible'][$linkid])."' WHERE linkid='".intval($linkid)."'");
		}
	}
	links_recache();
    redirect('链接已成功更新', 'admincp.php?job=link');
}

if(!$action) {
	$action = 'list';
}

if($action == 'add') {
	$subnav = '添加链接';
}

if ($action == 'list') {
	$query = $DB->query("SELECT * FROM {$db_prefix}links ORDER BY displayorder");
	$linkdb = array();
	while ($link = $DB->fetch_array($query)) {
		if ($link['visible'] == '1') {
			$link['visible'] = '<option value="1" selected>显示</option><option value="0">隐藏</option>';
		} else {
			$link['visible'] = '<option value="1">显示</option><option value="0" selected>隐藏</option>';
		}
		$linkdb[] = $link;
	}
	unset($link);
	$DB->free_result($query);
	$subnav = '编辑链接';
}

$navlink_L = ' &raquo; <a href="admincp.php?job=link">链接管理</a>'.($subnav ? ' &raquo; '.$subnav : '');
cpheader();
include PrintEot('link');
?>