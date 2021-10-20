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

// 加载后台函数集合
require_once('global.php');

if ($sax_group == 1) {
	$adminitem = array(
		'configurate' => '系统设置',
		'article' => '文章管理',
		'comment' => '评论管理',
		'attachment' => '附件管理',
		'category' => '分类管理',
		'user' => '用户管理',
		'link' => '链接管理',
		'template' => '模板管理',
		'cache' => '系统维护',
		'database' => '数据管理',
		'log' => '运行记录'
	);
} else {
	// 撰写组不显示菜单
	$job = in_array($job, array('article','main')) ? $job : 'article';
}

if (!$job) {
	$job = 'main';
} else {
	if (strlen($job) > 20) {
		$job = 'main';
	}
	$job = str_replace(array('.','/','\\',"'",':','%'),'',$job);
	$job = basename($job);
	$job = in_array($job, array('configurate','article','comment','attachment','category','user','link','template','cache','database','log')) ? $job : 'main';
}

$subnav = '';
if (file_exists($job.'.php')) {
	include ($job.'.php');
} else {
	include ('main.php');
}

cpfooter();
?>