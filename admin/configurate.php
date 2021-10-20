<?php
// ========================== 文件说明 ==========================//
// 本文件说明：系统设置
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

$settingsmenu = array(
	'basic' => '基本设置',
	'display' => '显示设置',
	'sidebar' => '侧栏设置',
	'comment' => '评论设置',
	'search' => '搜索设置',
	'attach' => '附件设置',
	'watermark' => '水印设置',
	'dateline' => '时间设置',
	'func' => '功能设置',
	'user' => '用户设置',
	'trackback' => 'Trackback设置',
	'seo' => '搜索引擎优化',
	'wap' => 'WAP设置',
	'ban' => '限制设置',
	'js' => 'JS调用设置',
	'rss' => 'RSS订阅设置',
);

// 更新配置以及配置文件
if($_POST['action'] == 'updatesetting') {
	//$DB->query("TRUNCATE TABLE {$db_prefix}settings");
	foreach($_POST['setting'] AS $key => $val) {
		$DB->query("REPLACE INTO {$db_prefix}settings VALUES ('".addslashes($key)."', '".addslashes($val)."')");
	}
	settings_recache();
	if ($type == 'sidebar') {
		links_recache();
		newcomments_recache();
		archives_recache();
		hottags_recache();
	}
	redirect('更新系统配置成功', 'admincp.php?job=configurate&type='.$type);
} //end update

$query = $DB->query("SELECT * FROM {$db_prefix}settings");
while($setting = $DB->fetch_array($query)) {
	$settings[$setting['title']] = htmlspecialchars($setting['value']);
}

ifselected($settings['show_calendar'],'show_calendar');
ifselected($settings['show_categories'],'show_categories');
ifselected($settings['show_archives'],'show_archives');
ifselected($settings['show_statistics'],'show_statistics');
ifselected($settings['show_debug'],'show_debug');
ifselected($settings['random_links'],'random_links');
ifselected($settings['audit_comment'],'audit_comment');
ifselected($settings['seccode'],'seccode');
ifselected($settings['comment_order'],'comment_order');
ifselected($settings['allow_search_comments'],'allow_search_comments');
ifselected($settings['attachments_thumbs'],'attachments_thumbs');
ifselected($settings['display_attach'],'display_attach');
ifselected($settings['remote_open'],'remote_open');
ifselected($settings['close'],'close');
ifselected($settings['closereg'],'closereg');
ifselected($settings['seccode_enable'],'seccode_enable');
ifselected($settings['gzipcompress'],'gzipcompress');
ifselected($settings['showmsg'],'showmsg');
ifselected($settings['enable_trackback'],'enable_trackback');
ifselected($settings['trackback_life'],'trackback_life');
ifselected($settings['audit_trackback'],'audit_trackback');
ifselected($settings['trackback_order'],'trackback_order');
ifselected($settings['smarturl'],'smarturl');
ifselected($settings['watermark'],'watermark');
ifselected($settings['wap_enable'],'wap_enable');
ifselected($settings['banip_enable'],'banip_enable');
ifselected($settings['spam_enable'],'spam_enable');
ifselected($settings['js_enable'],'js_enable');
ifselected($settings['rss_enable'],'rss_enable');

$viewmode = '';
$viewmode[$settings['viewmode']] = 'selected';
$article_order = '';
$article_order[$settings['article_order']] = 'selected';
$related_order[$settings['related_order']] = 'selected';
$attack_reject[$settings['attack_reject']] = 'selected';
$attachments_save_dir[$settings['attachments_save_dir']] = 'selected';
$attachments_display[$settings['attachments_display']] = 'selected';
$settings['server_timezone'] < 0 ? ${'zone_0'.str_replace('.','_',abs($settings['server_timezone']))}='selected' : ${'zone_'.str_replace('.','_',$settings['server_timezone'])}='selected';
$waterpos[$settings['waterpos']] = 'selected';
$tb_spam_level[$settings['tb_spam_level']] = 'selected';

$gd_version = gd_version();
$gd_version = $gd_version ? '服务器GD版本:'.$gd_version : '服务器不支持GD,因此该功能无法正常使用.';

$subnav = '';
if (in_array($type,array_flip($settingsmenu))) {
	$subnav = $settingsmenu[$type];
}
$navlink_L = ' &raquo; <a href="admincp.php?job=configurate">系统设置</a>'.($subnav ? ' &raquo; '.$subnav : '');
cpheader();
include PrintEot('configurate');
?>