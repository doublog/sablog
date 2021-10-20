<?php
// ========================== 文件说明 ==========================//
// 本文件说明：归档主页面
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

include PrintEot('header');
echo "<div id=\"content\">\n";
if (!$stats['article_count']) {
	echo "<p>没有任何归档</p>\n";
} else {
	$articledb = array();
	$query = $DB->query("SELECT articleid,title,dateline,comments FROM {$db_prefix}articles WHERE visible='1' ORDER BY dateline DESC");
	while ($article = $DB->fetch_array($query)) {
		$articledb[] = $article;
	}
	$lastmonth = '';
	foreach($articledb as $key => $article){
		list($year,$month,$day) = explode('-', sadate('Y-m-d',$article['dateline']));
		$currmonth = $year.'年'.$month.'月';
		if ($lastmonth != $currmonth && $key == 0) {
			echo "<h4>$currmonth</h4>\n<ul>\n";
		}
		if ($lastmonth != $currmonth && $key > 0) {
			echo "</ul>\n<h4>$currmonth</h4>\n<ul>\n";
		}
		$lastmonth = $currmonth;
		echo "<li>$month/$day : <a href=\"?article-".$article['articleid'].".".$options['artlink_ext']."\">".$article['title']."</a> (".$article['comments']."条评论)</li>\n";
	}
	echo "</ul>\n";
}
echo "</div>\n";
?>