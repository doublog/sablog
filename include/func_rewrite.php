<?php
// ========================== 文件说明 ==========================//
// 本文件说明：自动分析替换模板里的URL
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

if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}

//转换分类的连接
function rewrite_category($head, $cid, $page = 1, $class = '') {
	global $options;
	!$page && $page = 1;
	return '<a href="'.$head.'category-'.$cid.'-'.$page.'.'.$options['rewrite_ext'].'"'.($class ? ' class="'.$class.'"' : '').'>';
}

//转换归档的连接
function rewrite_archives($head, $date, $page = 1, $class = '') {
	global $options;
	!$page && $page = 1;
	return '<a href="'.$head.'archives-'.$date.'-'.$page.'.'.$options['rewrite_ext'].'"'.($class ? ' class="'.$class.'"' : '').'>';
}

//转换文章的连接
function rewrite_show($head, $id, $page = 1, $extra = '', $class = '') {
	global $options;
	!$page && $page = 1;
	return '<a href="'.$head.'show-'.$id.'-'.$page.'.'.$options['rewrite_ext'].($extra ? '#'.$extra : '').'"'.($class ? ' class="'.$class.'"' : '').'>';
}

//转换其他页面的连接
function rewrite_page($head, $action, $page = 1, $class = '') {
	global $options;
	!$page && $page = 1;
	if (in_array($action,array('search','archives','reg','login','links'))) {
		$pagelink = '';
	} else {
		$pagelink = '-'.$page;
	}
	return '<a href="'.$head.$action.$pagelink.'.'.$options['rewrite_ext'].'"'.($class ? ' class="'.$class.'"' : '').'>';
}

?>