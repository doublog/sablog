<?php
// ========================== 文件说明 ==========================//
// 本文件说明：编辑器调用
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

//强烈建议换编辑器选择允许一个页面多次调用的。否则不能正常使用。不关我的事。如果是代码高手。修改自然很简单。
//这里是调用FCK编辑器的。如果改变其他的编辑器。在这里设置加载的方法。
//$article['description'] 是文章描述
//$article['content'] 是文章内容

//调用编辑器主体
include('editor/fckeditor.php') ;

//设置描述区域
$oFCKeditor = new FCKeditor('description');
$oFCKeditor->Value = $article['description'];
$oFCKeditor->Height = '200';
$oFCKeditor->ToolbarSet = 'Basic';
//描述区域的模板变量
$descriptionarea = $oFCKeditor->CreateHtml();

//设置内容区域
$oFCKeditor = new FCKeditor('content');
$oFCKeditor->Value = $article['content'];
//内容区域的模板变量
$contentarea = $oFCKeditor->CreateHtml();

?>