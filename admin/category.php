<?php
// ========================== 文件说明 ==========================//
// 本文件说明：分类管理
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

// 检查分类名是否符合逻辑
function checkname($name) {
	if(!$name || strlen($name) > 30) {
		$result = '分类名不能为空并且不能超过30个字符<br />';
		return $result;
	}
}
// 删除Tag函数
function removetag($item,$tagid) {
	global $DB, $db_prefix;
	$item = addslashes($item);
	$tag = $DB->fetch_one_array("SELECT aids FROM {$db_prefix}tags WHERE tag='$item'");
	if ($tag) {
		$query  = $DB->query("SELECT articleid, keywords FROM {$db_prefix}articles WHERE articleid IN (".$tag['aids'].")");
		while ($article = $DB->fetch_array($query)) {
			$article['keywords'] = str_replace(','.$item.',', ',', $article['keywords']);
			$article['keywords'] = str_replace(','.$item, '', $article['keywords']);
			$article['keywords'] = str_replace($item.',', '', $article['keywords']);
			$article['keywords'] = str_replace($item, '', $article['keywords']);
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET keywords='".addslashes($article['keywords'])."' WHERE articleid='".$article['articleid']."'");
		}
		$DB->unbuffered_query("DELETE FROM {$db_prefix}tags WHERE tagid='".intval($tagid)."'");
	}
}

//修改Tag
if($action == 'domodtag') {
	$newitem = addslashes($_POST['tag']);
	$olditem = addslashes($_POST['oldtag']);
	$tagid   = intval($_POST['tagid']);
	$result  = checktag($newitem);
	if($result)	{
		redirect($result);
	}
    $result = $DB->num_rows($DB->query("SELECT tagid FROM {$db_prefix}tags WHERE tag='$newitem'"));
    if($result) {
        redirect('数据库中已存在相同的数据', 'admincp.php?job=category&action=modtag&tagid='.$tagid);
    }
	$tag = $DB->fetch_one_array("SELECT aids FROM {$db_prefix}tags WHERE tag='$olditem'");
	if ($tag) {
		$query  = $DB->query("SELECT articleid, keywords FROM {$db_prefix}articles WHERE articleid IN (".$tag['aids'].")");
		while ($article = $DB->fetch_array($query)) {
			$newtag = str_replace($olditem, $newitem, $article['keywords']);
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET keywords='".addslashes($newtag)."' WHERE articleid='".$article['articleid']."'");
		}
		$DB->unbuffered_query("UPDATE {$db_prefix}tags SET tag='$newitem' WHERE tagid='$tagid'");
	}
	hottags_recache();
    redirect('修改Tags成功', 'admincp.php?job=category&action=taglist');
}

//批量删除Tag
if($action == 'dodeltag') {
    if (!$_POST['tag'] || !is_array($_POST['tag'])) {
        redirect('未选择任何Tags');
    }
	$tag_count=0;
	foreach ($tag as $name => $id) {
		removetag($name, $id);
		$tag_count++;
	}
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET tag_count=tag_count-".$tag_count);
	hottags_recache();
	statistics_recache();
    redirect('成功删除所选Tags', 'admincp.php?job=category&action=taglist');
}

//清理Tags
if ($action == 'dotagclear') {
	//清空Tags表
	if (!$step) {
		$DB->unbuffered_query("TRUNCATE TABLE {$db_prefix}tags");
	    $step=1;
	}
	$percount = ($percount <= 0) ? 100 : $percount;
	$start    = ($step - 1) * $percount;
	$next     = $start + $percount;
	$step++;
	$jumpurl  = 'admincp.php?job=category&action=dotagclear&step='.$step.'&percount='.$percount;
	$goon     = 0;
	$query    = $DB->query("SELECT articleid, keywords FROM {$db_prefix}articles WHERE visible='1' ORDER BY articleid LIMIT $start, $percount");
	while($article = $DB->fetch_array($query)){
		$goon = 1;
		if ($article['keywords']) {
			$tagdb = explode(',', $article['keywords']);
			$tagnum = count($tagdb);
			for($i=0; $i<$tagnum; $i++) {
				$tagdb[$i] = trim($tagdb[$i]);
				if ($tagdb[$i]) {
					$tag  = $DB->fetch_one_array("SELECT tagid,aids FROM {$db_prefix}tags WHERE tag='$tagdb[$i]'");
					if(!$tag) {
						$DB->query("INSERT INTO {$db_prefix}tags (tag,usenum,aids) VALUES ('$tagdb[$i]', '1', '".$article['articleid']."')");
					} else {						
						$aids = $tag['aids'].','.$article['articleid'];
						$DB->unbuffered_query("UPDATE {$db_prefix}tags SET usenum=usenum+1, aids='$aids' WHERE tag='$tagdb[$i]'");
					}
				}
				unset($aids);
			}
		}
	}
	if($goon){
		redirect('正在更新 '.$start.' 到 '.$next.' 项', $jumpurl, '2');
	} else{
		$tag_count = $DB->num_rows($DB->query("SELECT tagid FROM {$db_prefix}tags"));
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET tag_count='$tag_count'");
		hottags_recache();
		statistics_recache();
		redirect('成功执行清理操作', 'admincp.php?job=category&action=taglist');
	}
}

//添加分类
if($action == 'doaddcate') {
	$name   = trim($_POST['name']);
	$displayorder = intval($_POST['displayorder']);
	$result = checkname($name);
	if($result) {
		redirect($result);
	}
	$name = char_cv($name);
    $rs = $DB->fetch_one_array("SELECT count(*) AS categories FROM {$db_prefix}categories WHERE name='$name'");
    if($rs['categories']) {
		redirect('该分类名在数据库中已存在');
    }
    $DB->query("INSERT INTO {$db_prefix}categories (name,displayorder) VALUES ('$name','$displayorder')");
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET cate_count=cate_count+1");
	categories_recache();
	statistics_recache();
    redirect('添加新分类成功', 'admincp.php?job=category');
}

//修改分类
if($action == 'domodcate') {
	$name   = trim($_POST['name']);
	$cid    = intval($_POST['cid']);
	$result = checkname($name);
	if($result)	{
		redirect($result);
	}
	$name = char_cv($name);
	$rs = $DB->fetch_one_array("SELECT count(*) AS categories FROM {$db_prefix}categories WHERE cid!='$cid' AND name='$name'");
    if($rs['categories']) {
		redirect('已经有其他分类使用【'.$name.'】这个名称');
    }
	// 更新分类
    $DB->unbuffered_query("UPDATE {$db_prefix}categories SET name='$name' WHERE cid='$cid'");
	categories_recache();
    redirect('修改分类成功', 'admincp.php?job=category');
}

//删除分类
if($action == 'dodelcate') {
	$cid = intval($_POST['cid']);
	$aids = $a_tatol = 0;
	// 删除分类
	$DB->query("DELETE FROM {$db_prefix}categories WHERE cid='$cid'");
	// 加载附件相关函数
	require_once(SABLOG_ROOT.'include/func_attachment.php');
	$query = $DB->query("SELECT articleid, keywords, uid, visible FROM {$db_prefix}articles WHERE cid='$cid' ORDER BY articleid");
	while ($article = $DB->fetch_array($query)) {
		$aids .= ','.$article['articleid'];
		if ($article['keywords']) {
			updatetags($article['articleid'], '', $article['keywords']);
		}
		if ($article['visible']) {
			$a_tatol++;
			$DB->query("UPDATE {$db_prefix}users SET articles=articles-1 WHERE userid='".$article['uid']."'");
		}
	}//end while

	// 删除该分类下文章中的附件
	$query  = $DB->query("SELECT attachmentid,filepath,thumb_filepath FROM {$db_prefix}attachments WHERE articleid IN ($aids)");
	$nokeep = array();
	while($attach = $DB->fetch_array($query)) {
		$nokeep[$attach['attachmentid']] = $attach;
	}
	removeattachment($nokeep);

	$DB->unbuffered_query("DELETE FROM {$db_prefix}comments WHERE articleid IN ($aids)");
	$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacks WHERE articleid IN ($aids)");
	$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacklog WHERE articleid IN ($aids)");
	// 删除分类下的文章
    $DB->unbuffered_query("DELETE FROM {$db_prefix}articles WHERE cid='$cid'");
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET cate_count=cate_count-1");
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count-".$a_tatol);
	hottags_recache();
	archives_recache();
	categories_recache();
	statistics_recache();
    redirect('成功删除分类和该分类下所有文章以及相关评论', 'admincp.php?job=category');
}

// 更新分类排序
if ($action == 'updatedisplayorder') {
	if (!$_POST['displayorder'] || !is_array($_POST['displayorder'])) {
		redirect('未选择任何分类');
	}
	foreach($displayorder as $cid => $order) {
		$DB->unbuffered_query("UPDATE {$db_prefix}categories SET displayorder='".intval($order)."' WHERE cid='$cid'");
	}
	categories_recache();
	redirect('所有分类的排序已更新', 'admincp.php?job=category');
}

if(!$action) {
	$action = 'catelist';
}

//分类列表
if ($action == 'catelist') {
	$catedb = array();
	$query = $DB->query("SELECT * FROM {$db_prefix}categories ORDER BY displayorder");
	$tatol = $DB->num_rows($query);
	while($cate = $DB->fetch_array($query)){
		$catedb[] = $cate;
	}
	unset($cate);
	$DB->free_result($query);
	$subnav = '分类管理';
}

//分类操作
if (in_array($action, array('addcate', 'modcate', 'delcate'))) {
	if ($action == 'addcate') {
		$subnav = '添加分类';
	} else {
		$cate = $DB->fetch_one_array("SELECT * FROM {$db_prefix}categories WHERE cid='".intval($_GET['cid'])."'");
		if($action == 'modcate') {
			$subnav = '修改分类';
		} else {
			$subnav = '删除分类';
		}
	}
}

//标签列表
if (in_array($action, array('taglist', 'getalltags'))) {
	if($action == 'taglist') {
		$order = $_GET['ordered'] == 'usenum' ? 'usenum' : 'tagid';
		if($page) {
			$start_limit = ($page - 1) * 30;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$numsql = "LIMIT $start_limit, 30";
		$rs = $DB->fetch_one_array("SELECT count(*) AS tags FROM {$db_prefix}tags");
		$tatol = $rs['tags'];
		$multipage = multi($tatol, 30, $page, 'admincp.php?job=category&action=taglist');
	} else {
		$order = 'tagid';
		$numsql = '';
	}
    $query = $DB->query("SELECT tagid,tag,usenum FROM {$db_prefix}tags ORDER BY $order DESC $numsql");
	$tagdb = array();
    while ($tag = $DB->fetch_array($query)) {
		$tag['url'] = urlencode($tag['tag']);
		$tag['item'] = htmlspecialchars($tag['tag']);
		$tagdb[] = $tag;
	}
	unset($tag);
	$DB->free_result($query);

	if($action == 'taglist') {
		$subnav = '全部标签';
	} else {
		include PrintEot('getalltags');
		PageEnd();
	}
}//list

//修改标签
if($action == 'modtag') {
	$tagid = intval($_GET['tagid']);
	$taginfo = $DB->fetch_one_array("SELECT tagid,tag,usenum,aids FROM {$db_prefix}tags WHERE tagid='$tagid'");
	if ($taginfo) {
		$taginfo['item'] = htmlspecialchars($taginfo['tag']);
		$query  = $DB->query("SELECT articleid, title FROM {$db_prefix}articles WHERE articleid IN (".$taginfo['aids'].")");
		$articledb = array();
		while ($article = $DB->fetch_array($query)) {
			$articledb[] = $article;
		}
		unset($article);
		$DB->free_result($query);
	}
	$subnav = '修改标签';
}//mod

//标签清理
if($action == 'tagclear') {
	$subnav = '标签清理';
}

if (strstr($action, 'tag')) {
	$catenav = '标签管理';
	$cateurl = 'taglist';
} else {
	$catenav = '分类管理';
	$cateurl = 'catelist';
}

$navlink_L = ' &raquo; <a href="admincp.php?job=category&action='.$cateurl.'">'.$catenav.'</a>'.($subnav ? ' &raquo; '.$subnav : '');
cpheader();
include PrintEot('category');
?>