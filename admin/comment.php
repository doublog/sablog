<?php
// ========================== 文件说明 ==========================//
// 本文件说明：评论管理
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

if (!$action) {
    $action = 'cmlist';
}

$articleid = intval($_GET['articleid'] ? $_GET['articleid'] : $_POST['articleid']);
$trackbackid = intval($_GET['trackbackid'] ? $_GET['trackbackid'] : $_POST['trackbackid']);
$commentid = intval($_GET['commentid'] ? $_GET['commentid'] : $_POST['commentid']);
$do = in_array($do,array('hidden','display','del')) ? $do : '';

//设置状态
if($action == 'cmvisible') {
	if ($commentid) {
		$comment = $DB->fetch_one_array("SELECT visible,articleid FROM {$db_prefix}comments WHERE commentid='$commentid'");
		if ($comment['visible']) {
			$visible = '0';
			$query = '-';
			$state = '隐藏';
		} else {
			$visible = '1';
			$query = '+';
			$state = '显示';
		}
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments=comments".$query."1 WHERE articleid='".$comment['articleid']."'");
		$DB->unbuffered_query("UPDATE {$db_prefix}comments SET visible='$visible' WHERE commentid='$commentid'");
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET comment_count=comment_count".$query."1");
		newcomments_recache();
		statistics_recache();
		redirect('已经成功把该评论设置为 '.$state.' 状态', 'admincp.php?job=comment&action=cmlist&articleid='.$articleid);
	} else {
		redirect('缺少参数', 'admincp.php?job=comment&action=cmlist&articleid='.$articleid);
	}
}

//设置状态
if($action == 'tbvisible') {
	if ($trackbackid) {
		$trackback = $DB->fetch_one_array("SELECT visible,articleid FROM {$db_prefix}trackbacks WHERE trackbackid='$trackbackid'");
		if ($trackback['visible']) {
			$visible = '0';
			$query = '-';
			$state = '隐藏';
		} else {
			$visible = '1';
			$query = '+';
			$state = '显示';
		}
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET trackbacks=trackbacks".$query."1 WHERE articleid='".$trackback['articleid']."'");
		$DB->unbuffered_query("UPDATE {$db_prefix}trackbacks SET visible='$visible' WHERE trackbackid='$trackbackid'");
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET trackback_count=trackback_count".$query."1");
		statistics_recache();
		redirect('已经成功把该引用设置为 '.$state.' 状态', 'admincp.php?job=comment&action=tblist&articleid='.$articleid);
	} else {
		redirect('缺少参数', 'admincp.php?job=comment&action=tblist&articleid='.$articleid);
	}
}

// 修改评论
if($action == 'domodcm') {
	$author = trim($_POST['author']);
	$url = trim($_POST['url']);
	if(!$author || strlen($author) > 30) {
		redirect('用户名为空或用户名太长');
	}
	$name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
	foreach($name_key as $value){
		if (strpos($author,$value) !== false){ 
			redirect('此用户名包含不可接受字符或被管理员屏蔽,请选择其它用户名');
		}
	}
	$author = char_cv($author);
	if ($url) {
		if (!isemail($url)) {
			if (!preg_match("#^(http|news|https|ftp|ed2k|rtsp|mms)://#", $url)) {
				redirect('网站URL错误');
			}
			$key = array("\\",' ',"'",'"','*',',','<','>',"\r","\t","\n",'(',')','+',';');
			foreach($key as $value){
				if (strpos($url,$value) !== false){
					redirect('网站URL错误');
				}
			}
		}
		$url = char_cv($url);
	}
    $DB->unbuffered_query("UPDATE {$db_prefix}comments SET author='$author', url='$url', content='".addslashes($_POST['content'])."' WHERE commentid='$commentid'");
	newcomments_recache();
    redirect('修改评论成功', 'admincp.php?job=comment&action=cmlist&articleid='.$articleid);
}

// 显示全部评论
if($action == 'displayall') {	
	$DB->query("UPDATE {$db_prefix}comments SET visible='1'");
	$comment_count = $DB->num_rows($DB->query("SELECT c.commentid FROM {$db_prefix}comments c LEFT JOIN {$db_prefix}articles a ON (a.articleid=c.articleid) WHERE a.visible='1' AND c.visible='1'"));
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET comment_count='$comment_count'");
	$result = $DB->query("SELECT articleid FROM {$db_prefix}articles");
	while ($article = $DB->fetch_array($result)) {
		// 更新所有文章的评论数
		$query = "SELECT commentid FROM {$db_prefix}comments WHERE articleid='".$article['articleid']."'";
		$tatol = $DB->num_rows($DB->query($query));
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments='$tatol' WHERE articleid='".$article['articleid']."'");
	}
	newcomments_recache();
	statistics_recache();
	redirect('已显示全部评论', 'admincp.php?job=comment&action=cmlist');
}

// 隐藏全部评论
if($action == 'hiddenall') {
	$DB->unbuffered_query("UPDATE {$db_prefix}comments SET visible='0'");
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET comment_count='0'");
	$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments='0'");
	newcomments_recache();
	statistics_recache();
	redirect('已隐藏全部评论', 'admincp.php?job=comment&action=cmlist');
}

// 删除全部评论
if($action == 'dodelallcm') {
	$DB->unbuffered_query("TRUNCATE TABLE {$db_prefix}comments");
	$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments='0'");
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET comment_count='0'");
	newcomments_recache();
	statistics_recache();
	redirect('已删除所有评论', 'admincp.php?job=comment&action=cmlist');
}

//删除单个Trackback记录
if($action == 'delonelog') {
	$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacklog WHERE trackbacklogid='".intval($_GET['trackbacklogid'])."'");
	redirect('成功删除所选Trackback记录', 'admincp.php?job=article&action=mod&articleid='.$articleid);
}

//清理Trackback
if($action == 'dotbclear') {
	if ($_POST['delid'] && is_numeric($_POST['delid'])) {
		$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacks WHERE trackbackid >= '".intval($_POST['delid'])."'");
	} else {
		$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacks WHERE visible='0'");
	}
	$tbnum = $DB->num_rows($DB->query("SELECT t.trackbackid FROM {$db_prefix}trackbacks t LEFT JOIN {$db_prefix}articles a ON (a.articleid=t.articleid) WHERE a.visible='1' AND t.visible='1'"));
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET trackback_count='$tbnum'");
	redirect('清理Trackback完毕.', 'admincp.php?job=comment&action=tblist');
}

//批量处理评论状态
if($action == 'domorecmlist') {
	if ($do == 'display') {
		$visible = '1';
		$msg     = '所选评论已显示';
		$del     = false;
	} elseif ($do == 'hidden') {
		$visible = '0';
		$msg     = '所选评论已隐藏';
		$del     = false;
	} elseif ($do == 'del') {
		$msg     = '所选评论已删除';
		$del     = true;
	} else {
		redirect('未选择任何操作');
	}
	if ($cids = implode_ids($_POST['comment'])) {
		if ($del) {
			$DB->unbuffered_query("DELETE FROM {$db_prefix}comments WHERE commentid IN ($cids)");
		} else {
			$DB->unbuffered_query("UPDATE {$db_prefix}comments SET visible='$visible' WHERE commentid IN ($cids)");
		}
		$comment_count = $DB->num_rows($DB->query("SELECT c.commentid FROM {$db_prefix}comments c LEFT JOIN {$db_prefix}articles a ON (a.articleid=c.articleid) WHERE a.visible='1' AND c.visible='1'"));
		$DB->query("UPDATE {$db_prefix}statistics SET comment_count='$comment_count'");
		$query = $DB->query("SELECT articleid FROM {$db_prefix}articles");
		while ($article = $DB->fetch_array($query)) {
			// 更新所有文章的评论数
			$tatol = $DB->num_rows($DB->query("SELECT commentid FROM {$db_prefix}comments WHERE articleid='".$article['articleid']."' AND visible='1'"));
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments='$tatol' WHERE articleid='".$article['articleid']."'");
		}
		newcomments_recache();
		statistics_recache();
		redirect($msg, 'admincp.php?job=comment&action=cmlist&articleid='.$articleid);
	} else {		
		redirect('未选择任何评论');
	}
}

//批量处理
if($action == 'domoretblist') {
	if ($do == 'display') {
		$visible = '1';
		$msg     = '所选引用已显示';
		$del     = false;
	} elseif ($_POST['do'] == 'hidden') {
		$visible = false;
		$msg     = '所选引用已隐藏';
		$del     = false;
	} elseif ($_POST['do'] == 'del') {
		$msg     = '所选引用已删除';
		$del     = true;
	} else {
		redirect('未选择任何操作');
	}	
	if ($tids = implode_ids($_POST['trackback'])) {
		if ($del) {
			$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacks WHERE trackbackid IN ($tids)");
		} else {
			$DB->unbuffered_query("UPDATE {$db_prefix}trackbacks SET visible='$visible' WHERE trackbackid IN ($tids)");
		}
		$tbnum = $DB->num_rows($DB->query("SELECT t.trackbackid FROM {$db_prefix}trackbacks t LEFT JOIN {$db_prefix}articles a ON (a.articleid=t.articleid) WHERE a.visible='1' AND t.visible='1'"));
		$DB->query("UPDATE {$db_prefix}statistics SET trackback_count='$tbnum'");
		$query = $DB->query("SELECT articleid FROM {$db_prefix}articles");
		while ($article = $DB->fetch_array($query)) {
			// 更新所有文章的引用数
			$tatol = $DB->num_rows($DB->query("SELECT trackbackid FROM {$db_prefix}trackbacks WHERE articleid='".$article['articleid']."' AND visible='1'"));
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET trackbacks='$tatol' WHERE articleid='".$article['articleid']."'");
		}
		statistics_recache();
		redirect($msg, 'admincp.php?job=comment&action=tblist');
	} else {		
		redirect('未选择任何引用');
	}
	
}

//批量处理
if($action == 'domoretbsendlog') {
	if ($do != 'del') {
		redirect('未选择任何操作');
	}
	if ($tids = implode_ids($_POST['trackbacklog'])) {
		$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacklog WHERE trackbacklogid IN ($tids)");
		redirect('已删除所选引用发送记录', 'admincp.php?job=comment&action=tbsendlog&articleid='.$articleid);
	} else {
		redirect('未选择任何Trackback记录');
	}
}

//发送Trackback
if($action == 'sendpacket') {
	$pingurl = addslashes(htmlspecialchars($_POST['pingurl']));
	if($pingurl) {
		$setting = $DB->fetch_one_array("SELECT value FROM {$db_prefix}settings WHERE title='url'");
		if (!$setting['value']) {
			$thisdir = dirname($php_self);
			$options['url'] = str_replace($thisdir,'',$options['url']);
		}
		$url     = $options['url'].'?action=show&id='.$articleid;
		$article = $DB->fetch_one_array("SELECT title,content FROM {$db_prefix}articles WHERE articleid='$articleid'");			
		$title   = addslashes($article['title']);
		$content = addslashes($article['content']);
		$data = 'url='.rawurlencode($url).'&title='.rawurlencode($title).'&blog_name='.rawurlencode($options['name']).'&excerpt='.rawurlencode($content);
		$result = sendpacket($pingurl, $data);
		if (strpos($result, 'error>0</error')) {
			$DB->query("INSERT INTO {$db_prefix}trackbacklog (articleid, dateline, pingurl) VALUES ('$articleid', '$timestamp', '$pingurl')");
			$sendpacketmsg = '发送 Trackback 成功';
		} else {
			$sendpacketmsg = '发送 Trackback 失败';
		}
		redirect($sendpacketmsg, 'admincp.php?job=comment&action=tbsendlog&articleid='.$articleid, '2');
	} else {
		redirect('请填写 Trackback Url');
	}
}

if ($action == 'cmlist') {
	$sql_query = ' WHERE 1=1 ';
	$subnav = '全部评论';
	$kind = in_array($_GET['kind'],array('display','hidden')) ? $_GET['kind'] : '';
	if ($kind == 'display') {
		$sql_query .= " AND visible='1'";
		$pagelink   = '&kind=display';
		$subnav     = '全部显示的评论';
	}
	if ($kind == 'hidden') {
		$sql_query .= " AND visible='0'";
		$pagelink   = '&kind=hidden';
		$subnav     = '全部隐藏的评论';
	}
	if ($articleid) {
		$article = $DB->fetch_one_array("SELECT title FROM {$db_prefix}articles WHERE articleid='$articleid'");
		$sql_query .= " AND articleid='$articleid'";
		$pagelink   = '&articleid='.$articleid;
		$subnav     = '文章:'.$article['title'];
	}
	$ip = char_cv($_GET['ip']);
	if ($ip) {
		$frontlen = strrpos($ip, '.');
		$ipc = substr($ip, 0, $frontlen);
		$sql_query .= " AND (ipaddress LIKE '%".$ipc."%')";
		$pagelink   = '&ip='.$ip;
		$subnav     = '与 '.$ip.' 同一C段提交的评论';
	}
	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$tatol     = $DB->num_rows($DB->query("SELECT commentid FROM {$db_prefix}comments $sql_query"));
	$multipage = multi($tatol, 30, $page, 'admincp.php?job=comment&action=cmlist'.$pagelink);

	$query  = $DB->query("SELECT * FROM {$db_prefix}comments $sql_query ORDER BY commentid DESC LIMIT $start_limit, 30");
	$commentdb = array();
    while ($comment = $DB->fetch_array($query)) {
		$comment['visible'] = $comment['visible'] ? '<span class="yes">显示</span>' : '<span class="no">隐藏</span>';
		$comment['url'] = $comment['url'] ? (isemail($comment['url']) ? '<a href="mailto:'.$comment['url'].'" target="_blank">发送邮件</a>' : '<a href="'.$comment['url'].'" target="_blank">访问主页</a>') : '<font color="#FF0000">Null</font>';
		$comment['dateline'] = sadate('Y-m-d H:i',$comment['dateline']);
		$comment['content'] = trimmed_title(htmlspecialchars($comment['content']));
		$commentdb[] = $comment;
	}
	unset($comment);
	$DB->free_result($query);
}//end list

if ($action == 'modcm') {
	$comment = $DB->fetch_one_array("SELECT c.articleid,c.commentid,c.author,c.url,c.dateline,c.content, a.title FROM {$db_prefix}comments c LEFT JOIN {$db_prefix}articles a ON (a.articleid=c.articleid) WHERE commentid='$commentid'");
	$comment['content'] = htmlspecialchars($comment['content']);
	$subnav = '修改评论';
}//end mod

//删除全部评论页面
if ($action == 'delallcm') {
	$subnav = '删除所有评论';
}//end delall

//引用列表
if($action == 'tblist') {
	$sql_query = ' WHERE 1 ';
	$subnav    = '全部引用';
	$kind = in_array($_GET['kind'],array('display','hidden')) ? $_GET['kind'] : '';
	if ($kind == 'display') {
		$sql_query .= " AND t.visible='1'";
		$pagelink   = '&kind=display';
		$subnav     = '全部显示的引用';
	} elseif ($kind == 'hidden') {
		$sql_query .= " AND t.visible='0'";
		$pagelink   = '&kind=hidden';
		$subnav     = '全部隐藏的引用';
	}
	if ($articleid) {
		$article    = $DB->fetch_one_array("SELECT title FROM {$db_prefix}articles WHERE articleid='$articleid'");
		$sql_query .= " AND t.articleid = '$articleid'";
		$pagelink   = '&articleid='.$articleid;
		$subnav     = '文章:'.$article['title'];
	}
	$ip = char_cv($_GET['ip']);
	if ($ip) {
		$frontlen = strrpos($ip, '.');
		$ipc = substr($ip, 0, $frontlen);
		$sql_query .= " AND (t.ipaddress LIKE '%".$ipc."%')";
		$pagelink   = '&ip='.$ip;
		$subnav   = '与 '.$ip.' 同一C段发送的引用';
	}

	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$rs = $DB->fetch_one_array("SELECT count(*) AS trackbacks FROM {$db_prefix}trackbacks t ".$sql_query);
    $tatol = $rs['trackbacks'];
	$multipage = multi($tatol, 30, $page, 'admincp.php?job=comment&action=tblist'.$pagelink);

    $query = $DB->query("SELECT t.*,a.title as article FROM {$db_prefix}trackbacks t LEFT JOIN {$db_prefix}articles a ON (a.articleid=t.articleid) $sql_query ORDER BY t.trackbackid DESC LIMIT $start_limit, 30");

	$trackbackdb = array();
    while ($trackback = $DB->fetch_array($query)) {
		$trackback['visible'] = $trackback['visible'] ? '<span class="yes">显示</span>' : '<span class="no">隐藏</span>';
		$trackback['point'] = (!empty($trackback['point']) || $trackback['point'] == '0') ? $trackback['point'] : '&nbsp;';
		$trackback['url'] = stripslashes_array($trackback['url']);
		$trackback['blog_name'] = trimmed_title(stripslashes_array($trackback['blog_name']),14);
		$trackback['title'] = trimmed_title(stripslashes_array($trackback['title']),14);
		$trackback['dateline'] = sadate('Y-m-d H:i', $trackback['dateline']);
		$trackbackdb[] = $trackback;
	}
	unset($trackback);
	$DB->free_result($query);
}//list

//清理引用
if ($action == 'tbclear') {
	$subnav = '清理引用';
}//end hidden

if($action == 'tbsendlog') {
	$articleid = intval($articleid);
	if ($articleid) {
		$article = $DB->fetch_one_array("SELECT title FROM {$db_prefix}articles WHERE articleid='$articleid'");
		$add_query = "WHERE articleid = '$articleid'";
		$add_query2 = "WHERE t.articleid = '$articleid'";
		$subnav   = '文章:'.$article['title'];
	} else {
		$add_query = $add_query2 = '';
		$subnav   = '发送记录';
	}
	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$rs = $DB->fetch_one_array("SELECT count(*) AS sendlogs FROM {$db_prefix}trackbacklog ".$add_query);
    $tatol = $rs['sendlogs'];
	$multipage = multi($tatol, 30, $page, 'admincp.php?job=comment&action=tbsendlog');

    $query = $DB->query("SELECT t.*, a.title FROM {$db_prefix}trackbacklog t LEFT JOIN {$db_prefix}articles a ON t.articleid=a.articleid $add_query2 ORDER BY trackbacklogid DESC LIMIT $start_limit, 30");
	$tblogdb = array();
    while ($tblog = $DB->fetch_array($query)) {
		$tblog['title'] = trimmed_title($tblog['title'],30);
		$tblog['showurl'] = cuturl($tblog['pingurl']);
		$tblog['dateline'] = sadate('Y-m-d H:i', $tblog['dateline']);
		$tblogdb[] = $tblog;
	}
	unset($tblog);
	$DB->free_result($query);
}//listlog

if (strstr($action, 'cm')) {
	$catenav = '评论管理';
	$cateurl = 'cmlist';
} else {
	$catenav = '引用管理';
	$cateurl = 'tblist';
}

$navlink_L = ' &raquo; <a href="admincp.php?job=comment&action='.$cateurl.'">'.$catenav.'</a>'.($subnav ? ' &raquo; '.$subnav : '');
cpheader();
include PrintEot('comment');
?>