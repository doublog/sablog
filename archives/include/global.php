<?php
// ========================== 文件说明 ==========================//
// 本文件说明：归档公共函数
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

if(!defined('IN_SABLOG')) {
	exit('Access Denied');
}

// 加载公用函数
require_once('../include/common.php');

// 状态检查
if ($options['close']) {
	message(html_clean($options['close_note']));
}
if (!$options['smarturl']) {
	message('Sorry, Sablog-X Archiver is not available.');
}

// 消息显示页面
function message($msg,$returnurl='',$min='3') {
	global $options, $stylevar;
	require_once PrintEot('message');
	PageEnd();
}

function PrintEot($template){
	if(!$template) $template='none';
	return SABLOG_ROOT.'archives/templates/'.$template.'.php';
}

// 清除HTML代码
function html_clean($content) {
	$content = htmlspecialchars($content);
	$content = str_replace("\n", "<br />", $content);
	$content = str_replace("  ", "&nbsp;&nbsp;", $content);
	$content = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $content);
	$content = preg_replace("/\[quote=(.*?)\]\s*(.+?)\s*\[\/quote\]/is", "<div style=\"font-weight: bold\">引用 \\1 说过的话:</div><div class=\"quote\">\\2</div>", $content);
	return $content;
}

// 高亮显示PHP
function phphighlite($code) {
	if (floor(phpversion())<4) {
		$buffer = $code;
	} else {
		$code = preg_replace("/<style .*?<\/style>/is", "", $code);
		$code = preg_replace("/<script .*?<\/script>/is", "", $code);
		$code = preg_replace("/<br\s*\/?>/i", "\n", $code);
		$code = preg_replace("/<\/?p>/i", "\n", $code);
		$code = preg_replace("/<\/?td>/i", "\n", $code);
		$code = preg_replace("/<\/?div>/i", "\n", $code);
		$code = preg_replace("/<\/?blockquote>/i", "\n", $code);
		$code = preg_replace("/<\/?li>/i", "\n", $code);
		$code = strip_tags($code);
		$code = preg_replace("/\&\#.*?\;/i", "", $code);
		$code = str_replace("&nbsp;&nbsp;&nbsp;", "&nbsp;&nbsp;", $code);
		$code = str_replace("&nbsp;&nbsp;", "&nbsp;", $code);
		$code = str_replace("&nbsp;", "\t", $code);
		$code = str_replace("&quot;", '"', $code);
		$code = str_replace("<br>", "", $code);
		$code = str_replace("<br />", "", $code);
		$code = str_replace("&gt;", ">", $code);
		$code = str_replace("&lt;", "<", $code);
		$code = str_replace("&amp;", "&", $code);
		//$code = str_replace('$', '\$', $code);
		if (!strpos($code,"<?\n") and substr($code,0,4)!="<?\n") {
			$code="<?".trim($code)."?>";
			$addedtags=1;
		}
		ob_start();
		$oldlevel=error_reporting(0);
		highlight_string($code);
		error_reporting($oldlevel);
		$buffer = ob_get_contents();
		ob_end_clean();
		if ($addedtags) {
		  $openingpos = strpos($buffer,"&lt;?");
		  $closingpos = strrpos($buffer, "?");
		  $buffer=substr($buffer, 0, $openingpos).substr($buffer, $openingpos+5, $closingpos-($openingpos+5)).substr($buffer, $closingpos+5);
		}
		$buffer = str_replace("&quot;", "\"", $buffer);
	}
	return $buffer;
}

// 获取页面调试信息
function footer() {
	global $DB, $starttime, $options, $stylevar;
	//$mtime = explode(' ', microtime());
	//$totaltime = number_format(($mtime[1] + $mtime[0] - $starttime), 6);
	//$sa_debug = 'Processed in '.$totaltime.' second(s), '.$DB->querycount.' queries';
	include PrintEot('footer');
	PageEnd();
}
?>