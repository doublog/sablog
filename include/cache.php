<?php
// ========================== 文件说明 ==========================//
// 本文件说明：缓存相关函数
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

// 更新设置选项
function settings_recache()	{
	global $DB, $db_prefix;
	$settings = $DB->query("SELECT title, value FROM {$db_prefix}settings");
	$contents = "\$options = array(\r\n";
	while ($setting = $DB->fetch_array($settings)) {
		$contents.="\t'".addslashes($setting['title'])."' => '".addslashes($setting['value'])."',\r\n";
	}
	$contents .= ');';
	writetocache('settings',$contents);
}

// 更新分类
function categories_recache() {
	global $DB, $db_prefix;
	$categories = $DB->query("SELECT * FROM {$db_prefix}categories ORDER BY displayorder");
	$contents = "\$catecache = array(\r\n";
	while ($cate = $DB->fetch_array($categories)) {
		$contents.="\t'".$cate['cid']."' => array(\r\n\t\t'cid' => '".$cate['cid']."',\r\n\t\t'name' => '".addslashes($cate['name'])."',\r\n\t\t'articles' => '".$cate['articles']."',\r\n\t),\r\n";
	}
	$contents .= ');';
	writetocache('categories',$contents);
}

// 更新热门标签
function hottags_recache() {
	global $DB, $db_prefix;
	$setting = $DB->fetch_one_array("SELECT value FROM {$db_prefix}settings WHERE title='hottags_shownum'");
	$limit = $setting['value'] ? intval($setting['value']) : 0;
	$query = $DB->query("SELECT * FROM {$db_prefix}tags ORDER BY usenum DESC LIMIT ".$limit);
	$contents = "\$tagcache = array(\r\n";
	while ($tag = $DB->fetch_array($query)) {
		$contents.="\t'".$tag['tagid']."' => array(\r\n\t\t'tagid' => '".$tag['tagid']."',\r\n\t\t'tag' => '".char_cv($tag['tag'])."',\r\n\t\t'url' => '".urlencode(char_cv($tag['tag']))."',\r\n\t\t'usenum' => '".$tag['usenum']."',\r\n\t),\r\n";
	}
	$contents .= ');';
	writetocache('hottags',$contents);
}

// 更新归档
function archives_recache() {
	global $DB, $db_prefix;
	$query = $DB->query("SELECT dateline FROM {$db_prefix}articles WHERE visible = '1' ORDER BY dateline DESC");
	$contents = "\$archivecache = array(\r\n";
	$articledb = array();
	while ($article = $DB->fetch_array($query)) {
		$articledb[] = sadate('Y-m',$article['dateline']);
	}
	unset($article);
	$DB->free_result($query);
	$archivedb = array_count_values($articledb);
	unset($articledb);
	foreach($archivedb as $key => $val){
		$contents.="\t'".$key."' => '".$val."',\r\n";
	}
	$contents .= ');';
	writetocache('archives',$contents);
}

// 更新链接
function links_recache() {
	global $DB, $db_prefix;
	$links = $DB->query("SELECT linkid,name,url,note FROM {$db_prefix}links WHERE visible = '1' ORDER BY displayorder ASC, name ASC");
	$linkdb = array();
	while ($link = $DB->fetch_array($links)) {
		$linkdb[] = $link;
	}
	unset($link);
	$tatol = count($linkdb);

	$contents  = "\$link_count = $tatol;\r\n";
	$contents .= "\$linkcache = array(\r\n";
	foreach($linkdb as $link){
		$contents.="\t'".$link['linkid']."' => array(\r\n\t\t'name' => '".addslashes($link['name'])."',\r\n\t\t'url' => '".addslashes($link['url'])."',\r\n\t\t'note' => '".addslashes($link['note'])."',\r\n\t),\r\n";
	}
	$contents .= ');';
	writetocache('links',$contents);
}

// 更新最新评论
function newcomments_recache() {
	global $DB, $db_prefix;
	$query = $DB->query("SELECT * FROM {$db_prefix}settings");
	$set = array();
	while ($r = $DB->fetch_array($query)) {
		$set[$r['title']] = $r['value'];
	}
	unset($r);
	$newcomments = $DB->query("SELECT c.commentid, c.articleid, c.author, c.dateline, c.content FROM {$db_prefix}comments c LEFT JOIN {$db_prefix}articles a ON (a.articleid=c.articleid) WHERE a.readpassword = '' AND a.visible='1' AND c.visible='1' ORDER BY commentid DESC LIMIT ".intval($set['recentcomment_num']));
	$contents = "\$newcommentcache = array(\r\n";
	$i=0;
	while ($newcomment = $DB->fetch_array($newcomments)) {
		$newcomment['content'] = preg_replace("/\[quote=(.*?)\]\s*(.+?)\s*\[\/quote\]/is", "", $newcomment['content']);
		if (empty($newcomment['content'])) {
			$newcomment['content'] = '......';
		}
		$contents.="\t'".$i."' => array(\r\n\t\t'commentid' => '".$newcomment['commentid']."',\r\n\t\t'articleid' => '".$newcomment['articleid']."',\r\n\t\t'author' => '".addslashes($newcomment['author'])."',\r\n\t\t'dateline' => '".sadate($set['recent_comment_timeformat'], $newcomment['dateline'])."',\r\n\t\t'content' => '".trimmed_title(htmlspecialchars(addslashes(str_replace(array("\r\n","\n","\r"),'',$newcomment['content']))), $set['recentcomment_limit'])."',\r\n\t),\r\n";
		unset($newcomment);
		$i++;
	}
	$contents .= ');';
	writetocache('newcomments',$contents);
}

// 更新统计
function statistics_recache() {
	global $DB, $db_prefix, $timestamp;
	$statistics = $DB->query("SELECT * FROM {$db_prefix}statistics");
	//设置存活变量
	$contents  = "\$stats_expire = '".($timestamp + 600)."';\r\n";
	$contents .= "\$stats = array(\r\n";
	while ($stat = $DB->fetch_array($statistics)) {
		$contents .= "\t'cate_count' => '".$stat['cate_count']."',\r\n\t'article_count' => '".$stat['article_count']."',\r\n\t'comment_count' => '".$stat['comment_count']."',\r\n\t'tag_count' => '".$stat['tag_count']."',\r\n\t'attachment_count' => '".$stat['attachment_count']."',\r\n\t'trackback_count' => '".$stat['trackback_count']."',\r\n\t'user_count' => '".$stat['user_count']."',\r\n\t'today_view_count' => '".$stat['today_view_count']."',\r\n\t'all_view_count' => '".$stat['all_view_count']."',\r\n";
	}
	$contents .= ');';
	writetocache('statistics',$contents);
}

// 更新模板自定义变量
function stylevars_recache() {
	global $DB, $db_prefix;
	$stylevars = $DB->query("SELECT * FROM {$db_prefix}stylevars WHERE visible='1'");
	$contents = "\$stylevar = array(\r\n";
	while ($var = $DB->fetch_array($stylevars)) {
		$contents.="\t'".strtolower($var['title'])."' => '".addslashes($var['value'])."',\r\n";
	}
	$contents .= ');';
	writetocache('stylevars',$contents);
}

function autosave_recache($title = '', $description = '', $content = '') {
	global $sax_uid;
	$title = addslashes($title);
	$description = addslashes($description);
	$content = addslashes($content);
	$autosavedb = array();
	@include_once(SABLOG_ROOT.'cache/cache_autosave.php');
	$autosavedb[$sax_uid] = array(
		'title' => $title,
		'description' => $description,
		'content' => $content
	);
	$contents = "\$autosavedb = unserialize('".serialize($autosavedb)."');";
	writetocache('autosave',$contents);
}

// 写入缓存文件
function writetocache($cachename, $cachedata = '') {
	if(in_array($cachename, array('archives','categories','hottags','links','newcomments','settings','statistics','stylevars','autosave'))) {
		$cachedir = SABLOG_ROOT.'cache/';
		$cachefile = $cachedir.'cache_'.$cachename.'.php';
		if(!is_dir($cachedir)) {
			@mkdir($cachedir, 0777);
		}
		if($fp = @fopen($cachefile, 'wb')) {
			@fwrite($fp, "<?php\r\n//Sablog-X cache file\r\n//Created on ".sadate('Y-m-d H:i:s')."\r\n\r\nif(!defined('SABLOG_ROOT')) exit('Access Denied');\r\n\r\n".$cachedata."\r\n\r\n?>");
			@fclose($fp);
			@chmod($cachefile, 0777);
		} else {
			echo 'Can not write to '.$cachename.' cache files, please check directory ./cache/ .';
			exit;
		}
	}
}

// 重新统计各种数据
function rethestats($cachename = '') {
	global $DB, $db_prefix;
	if (!$cachename || $cachename == 'settings') {
		settings_recache();
	}
	if (!$cachename || $cachename == 'statistics') {
		// 更新首页显示的分类数
		$cate_count = $DB->num_rows($DB->query("SELECT cid FROM {$db_prefix}categories"));
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET cate_count='$cate_count'");	
		// 更新首页显示的文章数
		$article_count = $DB->num_rows($DB->query("SELECT articleid FROM {$db_prefix}articles WHERE visible = '1'"));
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count='$article_count'");
		// 更新首页显示的评论数
		$comment_count = $DB->num_rows($DB->query("SELECT c.commentid FROM {$db_prefix}comments c LEFT JOIN {$db_prefix}articles a ON (a.articleid=c.articleid) WHERE a.visible='1' AND c.visible='1'"));
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET comment_count='$comment_count'");
		// 更新首页显示的附件数
		$attachment_count = $DB->num_rows($DB->query("SELECT aa.attachmentid FROM {$db_prefix}attachments aa LEFT JOIN {$db_prefix}articles a ON (a.articleid=aa.articleid) WHERE a.visible='1'"));
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET attachment_count='$attachment_count'");
		// 更新首页显示的Trackback数
		$trackback_count = $DB->num_rows($DB->query("SELECT t.trackbackid FROM {$db_prefix}trackbacks t LEFT JOIN {$db_prefix}articles a ON (a.articleid=t.articleid) WHERE a.visible='1' AND t.visible='1'"));
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET trackback_count='$trackback_count'");
		// 更新首页显示的标签(Tags)数
		$tag_count = $DB->num_rows($DB->query("SELECT tagid FROM {$db_prefix}tags"));
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET tag_count='$tag_count'");
		// 更新主人发表文章数
		$query = $DB->query("SELECT userid FROM {$db_prefix}users WHERE groupid='1' OR groupid='2'");
		while ($user = $DB->fetch_array($query)) {
			$tatol = $DB->num_rows($DB->query("SELECT articleid FROM {$db_prefix}articles WHERE visible = '1' AND uid='".$user['userid']."'"));
			$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles='$tatol' WHERE userid='".$user['userid']."'");
		}
		statistics_recache();
	}
	if (!$cachename || $cachename == 'newcomments') {
		newcomments_recache();
	}
	if (!$cachename || $cachename == 'categories') {
		// 更新所有分类的文章数
		$query = $DB->query("SELECT cid, name FROM {$db_prefix}categories");
		while ($cate = $DB->fetch_array($query)) {
			$tatol = $DB->num_rows($DB->query("SELECT articleid FROM {$db_prefix}articles WHERE visible = '1' AND cid='".$cate['cid']."'"));
			$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles='$tatol' WHERE cid='".$cate['cid']."'");
		}
		categories_recache();
	}
	if (!$cachename || $cachename == 'archives') {
		// 重建文章数据
		$query = $DB->query("SELECT articleid, title FROM {$db_prefix}articles");
		while ($article = $DB->fetch_array($query)) {
			// 更新所有文章的评论数
			$tatol = $DB->num_rows($DB->query("SELECT commentid FROM {$db_prefix}comments WHERE articleid='".$article['articleid']."' AND visible='1'"));
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments='$tatol' WHERE articleid='".$article['articleid']."'");
			// 更新所有文章的Trackback数
			$tatol = $DB->num_rows($DB->query("SELECT trackbackid FROM {$db_prefix}trackbacks WHERE articleid='".$article['articleid']."' AND visible='1'"));
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET trackbacks='$tatol' WHERE articleid='".$article['articleid']."'");
		}
		archives_recache();
	}
	if (!$cachename || $cachename == 'hottags') {
		hottags_recache();
	}
	if (!$cachename || $cachename == 'links') {
		links_recache();
	}
	if (!$cachename || $cachename == 'stylevars') {
		stylevars_recache();
	}
}
?>