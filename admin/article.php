<?php
// ========================== 文件说明 ==========================//
// 本文件说明：文章管理
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

// 加载附件相关函数
require_once(SABLOG_ROOT.'include/func_attachment.php');

$uquery = '';
if ($sax_group != 1) {
	$uquery = " AND uid='$sax_uid'";
}

if($_POST['action'] == 'addarticle') {
	// 取值并过滤部分
	$title        = trim($_POST['title']);
	$cid          = intval($_POST['cid']);
	$description  = addslashes($_POST['description']);
	$content      = addslashes($_POST['content']);
	$readpassword = addslashes($_POST['readpassword']);
	$keywords     = strtolower(addslashes(trim($_POST['keywords'])));
	$closecomment   = intval($_POST['closecomment']);
	$closetrackback = intval($_POST['closetrackback']);
	$visible     = intval($_POST['visible']);
	$stick       = intval($_POST['stick']);
	// 时间变量
	$edittime    = intval($_POST['edittime']);
	$newyear     = intval($_POST['newyear']);
	$newmonth    = intval($_POST['newmonth']);
	$newday      = intval($_POST['newday']);
	$newhour     = intval($_POST['newhour']);
	$newmin      = intval($_POST['newmin']);
	$newsec      = intval($_POST['newsec']);
	
	$keywords    = str_replace('，', ',', $keywords);
	$keywords    = str_replace(',,', ',', $keywords);	
	if (substr($keywords, -1) == ',') {
		$keywords = substr($keywords, 0, strlen($keywords)-1);
	}

	// 暂时把提交的数据保存到COOKIE
	$cookietime=$timestamp+2592000;
	setcookie('cid',$cid,$cookietime);
	setcookie('title',$title,$cookietime);
	setcookie('description',$description,$cookietime);
	setcookie('content',$content,$cookietime);
	setcookie('readpassword',$readpassword,$cookietime);
	setcookie('keywords',$keywords,$cookietime);
	setcookie('closecomment',$closecomment,$cookietime);
	setcookie('closetrackback',$closetrackback,$cookietime);
	setcookie('visible',$visible,$cookietime);
	setcookie('stick',$stick,$cookietime);
	// 检查变量
	$result  = '';
	$result .= checktitle($title);
	$result .= checkcate($cid);
	$result .= checkcontent($content);
	$result .= checkkeywords($keywords);
	if($result)	{
		redirect($result);
	}
	$title = char_cv($title);
    $query = $DB->query("SELECT articleid FROM {$db_prefix}articles WHERE title='$title' and cid='$cid' and description='$description' and content='$content'");
    if($DB->num_rows($query)) {
        redirect('数据库中已存在相同的数据', 'admincp.php?job=article&action=add');
    }
	// 上传附件
	$searcharray = array();
	$replacearray = array();
	require_once(SABLOG_ROOT.'admin/uploadfiles.php');
	// 上传结束

	// 修改时间
	if ($edittime) {
		if (checkdate($newmonth, $newday, $newyear)) {
			if (substr(PHP_OS, 0, 3) == 'WIN' && $newyear < 1970) {
				$posttime = $timestamp;
			} else {
				$posttime = gmmktime($newhour, $newmin, $newsec, $newmonth, $newday, $newyear) - $timeoffset * 3600;
			}
		} else {
			$posttime = $timestamp;
		}
	} else {
		$posttime = $timestamp;
	}

	// 插入数据部分
    $DB->query("INSERT INTO {$db_prefix}articles (cid, uid, title, description, content, keywords, dateline, views, comments, attachments, trackbacks, closecomment, closetrackback, visible, stick, readpassword) VALUES ('$cid', '$admin_id', '$title', '$description', '$content', '$keywords', '$posttime', '0', '0', '$attachs', '0', '$closecomment', '$closetrackback', '$visible', '$stick', '$readpassword')");
	$articleid = $DB->insert_id();
	$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles=articles+1 WHERE userid='$admin_id'");
	if($attachmentids){
		$DB->unbuffered_query("UPDATE {$db_prefix}attachments SET articleid='$articleid' WHERE attachmentid IN($attachmentids)");
	}
	if ($searcharray && $replacearray) {
		$content = str_replace($searcharray, $replacearray, $content);
		$DB->query("UPDATE {$db_prefix}articles SET content='$content' WHERE articleid='$articleid'");
	}
	// 插入/更新Tags
	if ($keywords) {
		$tagdb = explode(',', $keywords);
		$tagnum = count($tagdb);
		for($i=0; $i<$tagnum; $i++) {
			$tagdb[$i] = trim($tagdb[$i]);
			if ($tagdb[$i]) {
				$tag  = $DB->fetch_one_array("SELECT tagid,aids FROM {$db_prefix}tags WHERE tag='$tagdb[$i]'");
				if(!$tag) {
					$DB->query("INSERT INTO {$db_prefix}tags (tag,usenum,aids) VALUES ('$tagdb[$i]', '1', '$articleid')");
					$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET tag_count=tag_count+1");
				} else {						
					$aids = $tag['aids'].','.$articleid;
					$DB->unbuffered_query("UPDATE {$db_prefix}tags SET usenum=usenum+1, aids='$aids' WHERE tag='$tagdb[$i]'");
				}
			}
			unset($aids);
		}
	}
	// 更新分类文章数量
	$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles+1 WHERE cid='$cid'");
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count+1");
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET attachment_count=attachment_count+".$attachment_count);
	hottags_recache();
	archives_recache();
	categories_recache();
	statistics_recache();
	// 清除临时数据
	if (@include_once(SABLOG_ROOT.'cache/cache_autosave.php')){
		autosave_recache();
	}
	// 清除临时数据
	setcookie('cid','');
	setcookie('title','');
	setcookie('description','');
	setcookie('content','');
	setcookie('readpassword','');
	setcookie('keywords','');
	setcookie('closecomment','');
	setcookie('closetrackback','');
	setcookie('visible','');
	setcookie('stick','');
    redirect('添加文章成功', 'admincp.php?job=article&action=add');
}

//修改文章

if($_POST['action'] == 'modarticle') {
	$title       = trim($_POST['title']);
	$articleid    = intval($_POST['articleid']);
	$cid		  = intval($_POST['cid']);
	$description  = addslashes($_POST['description']);
	$content      = addslashes($_POST['content']);
	$readpassword = addslashes($_POST['readpassword']);
	$keywords     = strtolower(addslashes(trim($_POST['keywords'])));
	$closecomment   = intval($_POST['closecomment']);
	$closetrackback = intval($_POST['closetrackback']);
	$visible     = intval($_POST['visible']);
	$stick       = intval($_POST['stick']);
	// 时间变量
	$edittime    = intval($_POST['edittime']);
	$newyear     = intval($_POST['newyear']);
	$newmonth    = intval($_POST['newmonth']);
	$newday      = intval($_POST['newday']);
	$newhour     = intval($_POST['newhour']);
	$newmin      = intval($_POST['newmin']);
	$newsec      = intval($_POST['newsec']);

	$keywords    = str_replace('，', ',', $keywords);
	$keywords    = str_replace(',,', ',', $keywords);
	if (substr($keywords, -1) == ',') {
		$keywords = substr($keywords, 0, strlen($keywords)-1);
	}
	$result  = '';
	$result .= checktitle($title);
	$result .= checkcate($cid);
	$result .= checkcontent($content);
	$result .= checkkeywords($keywords);
	if($result)	{
		redirect($result);
	}
	$title = char_cv($title);
    $article = $DB->fetch_one_array("SELECT uid,attachments,cid,visible FROM {$db_prefix}articles WHERE articleid='$articleid'");
	if ($sax_group != 1 && $sax_uid != $article['uid']) {
		redirect('您不能修改不是您写的文章', 'admincp.php?job=article&action=list');
	}
	// 删除发送Trackback记录
    if ($tblogids = implode_ids($_POST['del_trackbacklog'])) {
		$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacklog WHERE trackbacklogid IN ($tblogids)");
	}

	// 修改附件
	$oldattach=array();	
	$aid = $article['attachments'];
	if ($aid){
		$oldattach = unserialize(stripslashes_array($aid));
		$nokeep = array();
		foreach ($oldattach AS $id => $value){
			if (!@in_array($id,$_POST['keep'])){
				$nokeep[$id]['filepath'] = $value['filepath'];
				$nokeep[$id]['thumb_filepath'] = $value['thumb_filepath'];
				unset($oldattach[$id]);
			}
		}
		removeattachment($nokeep);
	}
	$searcharray = array();
	$replacearray = array();
	require_once(SABLOG_ROOT.'admin/uploadfiles.php');
	if ($attachs){
		$attachs=unserialize(stripslashes_array($attachs));
		foreach ($attachs as $key=>$value){
			$oldattach[$key]=$value;
		}
	}
	if ($oldattach){
		$oldattach=addslashes(serialize($oldattach));
	} else {
		$oldattach='';
	}
	if($attachmentids){
		$DB->unbuffered_query("UPDATE {$db_prefix}attachments SET articleid='$articleid' WHERE attachmentid IN($attachmentids)");
	}
	if ($searcharray && $replacearray) {
		$content = str_replace($searcharray, $replacearray, $content);
	}
	// 修改附件结束

	//***************** 处理tags及计数 *****************//
	$oldtags = $_POST['oldtags'] ? strtolower(addslashes($_POST['oldtags'])) : '';	
	updatetags($articleid, $keywords, $oldtags);
	//***************** 处理tags及计数结束 *************//
	
	// 修改时间
	$edittimesql = '';
	if ($edittime) {
		if (checkdate($newmonth, $newday, $newyear)) {
			if (substr(PHP_OS, 0, 3) == 'WIN' && $newyear < 1970) {
				$edittimesql = '';
			} else {
				$posttime = gmmktime($newhour, $newmin, $newsec, $newmonth, $newday, $newyear) - $timeoffset * 3600;
				$edittimesql = ", dateline='$posttime'";
			}
		}
	}

	$DB->unbuffered_query("UPDATE {$db_prefix}articles SET cid='$cid', title='$title', description='$description', content='$content', attachments='$oldattach', keywords='$keywords', closecomment='$closecomment', closetrackback='$closetrackback', visible='$visible', stick='$stick', readpassword='$readpassword' $edittimesql WHERE articleid='$articleid'");
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET attachment_count=attachment_count+".$attachment_count);
	if ($article['cid'] != $cid) {		
		$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles-1 WHERE cid='".$article['cid']."'");
		if ($visible) {
			$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles+1 WHERE cid='$cid'");
		}
	}
	if ($article['visible'] != $visible) {
		if ($article['visible']) {
			$visible = 0;
			$query = '-';
		} else {
			$visible = 1;
			$query = '+';
		}
		$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles".$query."1 WHERE cid='".$article['cid']."'");
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count".$query."1");
		$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles=articles".$query."1 WHERE userid='$admin_id'");
	}
	archives_recache();
	hottags_recache();
	categories_recache();
	statistics_recache();
    redirect('修改文章成功', 'admincp.php?job=article&action=list');
}

//设置状态
if($action == 'visible') {
	$articleid = intval($_GET['articleid']);
	if ($articleid) {
		$article = $DB->fetch_one_array("SELECT visible,title,cid,uid FROM {$db_prefix}articles WHERE articleid='$articleid'");
		if ($sax_group != 1 && $sax_uid != $article['uid']) {
			redirect('您不能修改不是您写的文章', 'admincp.php?job=article&action=list');
		}
		if ($article['visible']) {
			$visible = 0;
			$query = '-';
			$state = '隐藏';
		} else {
			$visible = 1;
			$query = '+';
			$state = '显示';
		}
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET visible='$visible' WHERE articleid='$articleid'");
		$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles".$query."1 WHERE cid='".$article['cid']."'");
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count".$query."1");
		$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles=articles".$query."1 WHERE userid='".$article['uid']."'");
		archives_recache();
		categories_recache();
		statistics_recache();
		redirect('已经成功把《'.$article['title'].'》设置为"'.$state.'"状态', 'admincp.php?job=article&action=list');
	} else {
		redirect('缺少参数', 'admincp.php?job=article&action=list');
	}
}

//批量操作文章
if($action == 'domore') {
	if ($aids = implode_ids($_POST['article'])) {
		if($_POST['do'] == 'move') {
			$articledb = array();
			$query = $DB->query("SELECT title,articleid FROM {$db_prefix}articles WHERE articleid IN ($aids)".$uquery);
			while ($article=$DB->fetch_array($query))	{ 
				$articledb[] = $article;
			}
			unset($article);
			$query = $DB->query("SELECT cid,name FROM {$db_prefix}categories ORDER BY displayorder");
			$catedb = array();
			while ($cate=$DB->fetch_array($query))	{ 
				$catedb[] = $cate;
			}
			unset($cate);
			$DB->free_result($query);
			$subnav = '移动文章';
		} elseif($_POST['do'] == 'delete') {
			$articledb = array();
			$query = $DB->query("SELECT title,articleid FROM {$db_prefix}articles WHERE articleid IN ($aids)".$uquery);
			while ($article=$DB->fetch_array($query))	{ 
				$articledb[] = $article;
			}
			unset($article);
			$DB->free_result($query);
			$subnav = '删除文章';
		} elseif ($_POST['do'] == 'hidden') {
			$a_tatol = 0;
			$query  = $DB->query("SELECT cid,visible,uid FROM {$db_prefix}articles WHERE articleid IN ($aids)".$uquery);
			while($article = $DB->fetch_array($query)) {
				if ($article['visible']) {
					$a_tatol++;
					$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles=articles-1 WHERE userid='".$article['uid']."'");
					$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles-1 WHERE cid='".$article['cid']."'");
				}
			}
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET visible='0' WHERE articleid IN ($aids)");
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count-".$a_tatol);
			archives_recache();
			categories_recache();
			statistics_recache();
			redirect('所选文章已隐藏', 'admincp.php?job=article&action=list');
		} elseif ($_POST['do'] == 'display') {
			$a_tatol = 0;
			$query  = $DB->query("SELECT cid,visible,uid FROM {$db_prefix}articles WHERE articleid IN ($aids)".$uquery);
			while($article = $DB->fetch_array($query)) {
				if (!$article['visible']) {
					$a_tatol++;
					$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles=articles+1 WHERE userid='".$article['uid']."'");
					$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles+1 WHERE cid='".$article['cid']."'");
				}
			}
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET visible='1' WHERE articleid IN ($aids)");
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count+".$a_tatol);
			archives_recache();
			categories_recache();
			statistics_recache();
			redirect('所选文章已显示', 'admincp.php?job=article&action=list');
		} elseif ($_POST['do'] == 'stick') {
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET stick='1' WHERE articleid IN ($aids)".$uquery);
			redirect('所选文章已置顶', 'admincp.php?job=article&action=list');
		} elseif ($_POST['do'] == 'unstick') {
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET stick='0' WHERE articleid IN ($aids)".$uquery);
			redirect('所选文章已取消置顶', 'admincp.php?job=article&action=list');
		} else {
			redirect('没有选择具体操作');
		}
	} else {
		redirect('未选择任何文章');
	}
}
//执行移动和删除操作
if (in_array($_POST['action'], array('domove', 'dodelete'))) {
	if ($aids = implode_ids($_POST['article'])) {
		$a_tatol = $comment_count = $trackback_count = 0;
		if($_POST['action'] == 'domove') {
			$msg = '移动文章成功';
			$cid = intval($_POST['cid']);
			$cate = $DB->fetch_one_array("SELECT name FROM {$db_prefix}categories WHERE cid='$cid'");
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET cid='$cid' WHERE articleid IN ($aids)".$uquery);
			$catequery = $DB->query("SELECT cid, name FROM {$db_prefix}categories");
			while ($cate = $DB->fetch_array($catequery)) {
				$query = "SELECT articleid FROM {$db_prefix}articles WHERE visible = '1' AND cid='".$cate['cid']."'";
				$tatol = $DB->num_rows($DB->query($query));
				$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles='$tatol' WHERE cid='".$cate['cid']."'");
			}
		} else {
			$msg = '删除文章成功';
			$query  = $DB->query("SELECT articleid,keywords,cid,visible,uid,comments,trackbacks FROM {$db_prefix}articles WHERE articleid IN ($aids)".$uquery);
			while($article = $DB->fetch_array($query)) {
				if ($article['keywords']) {
					updatetags($article['articleid'], '', $article['keywords']);
				}
				if ($article['visible']) {
					$a_tatol++;
					$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles=articles-1 WHERE userid='".$article['uid']."'");
					$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles-1 WHERE cid='".$article['cid']."'");
				}
				$comment_count = $comment_count + $article['comments'];
				$trackback_count = $trackback_count + $article['trackbacks'];
			}
			$query  = $DB->query("SELECT attachmentid,filepath,thumb_filepath FROM {$db_prefix}attachments WHERE articleid IN ($aids)");
			if ($DB->num_rows($query)) {
				$nokeep = array();
				while($attach = $DB->fetch_array($query)) {
					$nokeep[$attach['attachmentid']] = $attach;
				}
				removeattachment($nokeep);
			}
			$DB->unbuffered_query("DELETE FROM {$db_prefix}articles WHERE articleid IN ($aids)".$uquery);
			$DB->unbuffered_query("DELETE FROM {$db_prefix}comments WHERE articleid IN ($aids)");
			$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacks WHERE articleid IN ($aids)");
			$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacklog WHERE articleid IN ($aids)");
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count-".$a_tatol);
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET comment_count=comment_count-".$comment_count);
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET trackback_count=trackback_count-".$trackback_count);
			hottags_recache();
			archives_recache();
			statistics_recache();
		}
		categories_recache();
		redirect($msg, 'admincp.php?job=article&action=list');
	} else {
		redirect('未选择任何文章');
	}
}

//操作结束

if (!$action) {
	$action = 'list';
}

$catedb = array();
$query = $DB->query("SELECT cid,name FROM {$db_prefix}categories ORDER BY displayorder");
while ($cate = $DB->fetch_array($query)) {
	$catedb[] = $cate;		
}
unset($cate);
$DB->free_result($query);

if (in_array($action, array('add', 'mod'))) {
	$smdir = SABLOG_ROOT.'images/smiles/';
	$insertsm = false;
	if(is_dir($smdir)) {
		$dirs = dir($smdir);
		$smfiles = array();
		while ($file = $dirs->read()) {
			$filepath = $smdir.$file;
			$pathinfo = pathinfo($filepath);
			if(is_file($filepath) && in_array($pathinfo['extension'],array('gif','jpg','jpeg','png'))) {
				$smfiles[] = $file;
			}
		}
		unset($file);
		$dirs->close();
		$insertsm = true;
		//获取表情目录的URL
		$parentdir = basename(dirname($php_self));
		$smdirurl = str_replace('/'.$parentdir,'',$options['url']).'images/smiles/';
	}

	if ($action == 'mod') {
		$act = 'modarticle';
		$tdtitle = '修改文章';
		$articleid = intval($_GET['articleid']);
		$article = $DB->fetch_one_array("SELECT * FROM {$db_prefix}articles WHERE articleid='$articleid'");
		if (empty($article)) {
			redirect('文章不存在');
		}
		if ($sax_group != 1 && $sax_uid != $article['uid']) {
			redirect('您不能修改不是您写的文章', 'admincp.php?job=article&action=list');
		}
		$article['keywords'] = htmlspecialchars($article['keywords']);
		$article['description'] = str_replace('\r\n', '', $article['description']);
		$article['content'] = str_replace('\r\n', '', $article['content']);

		//发送的Trackback记录
		$query = $DB->query("SELECT * FROM {$db_prefix}trackbacklog WHERE articleid = '".$article['articleid']."'");
		$tb_tatol = $DB->num_rows($query);
		if ($tb_tatol > 0) {
			$tblogdb = array();
			while($tblog = $DB->fetch_array($query)) {
				$tblog['pingurl'] = htmlspecialchars($tblog['pingurl']);
				$tblogdb[] = $tblog;
			}
			unset($tblog);
		}
		//附件
		$query = $DB->query("SELECT attachmentid,articleid,dateline,filename,filesize FROM {$db_prefix}attachments WHERE articleid = '".$article['articleid']."'");
		$attach_tatol = $DB->num_rows($query);
		if ($attach_tatol) {
			$attachdb = array();
			while($attach = $DB->fetch_array($query)) {
				$attach['filename'] = htmlspecialchars($attach['filename']);
				$attach['dateline'] = sadate('Y-m-d H:i:s',$attach['dateline']);
				$attach['filesize'] = sizecount($attach['filesize']);
				$attachdb[] = $attach;
			}
			unset($attach);
		}
		$closecomment_check = $article['closecomment'] ? 'checked' : '';
		$closetrackback_check = $article['closetrackback'] ? 'checked' : '';
		$visible_check = $article['visible'] ? 'checked' : '';
		$stick_check = $article['stick'] ? 'checked' : '';
		$article['time_tmp'] = sadate('Y-n-j-H-i-s', $article['dateline']);
		$subnav = '修改文章';
	} else {
		@include_once(SABLOG_ROOT.'cache/cache_autosave.php');
		$autosavedb = stripslashes_array($autosavedb);

		$article['title'] = $autosavedb[$sax_uid]['title'];
		$article['description'] = $autosavedb[$sax_uid]['description'];
		$article['content'] = $autosavedb[$sax_uid]['content'];
		$act = 'addarticle';
		$tdtitle = '添加文章';
		$article['closecomment'] = isset($_COOKIE['closecomment']) ? intval($_COOKIE['closecomment']) : '0';
		$article['closetrackback'] = isset($_COOKIE['closetrackback']) ? intval($_COOKIE['closetrackback']) : '0';
		$article['visible'] = isset($_COOKIE['visible']) ? intval($_COOKIE['visible']) : '1';
		$article['stick'] = isset($_COOKIE['stick']) ? intval($_COOKIE['stick']) : '0';		
		$article['cid'] = intval($_GET['cid']);
		$visible_check = 'checked';
		$article['time_tmp'] = sadate('Y-n-j-H-i-s', $timestamp);
		$subnav = '添加文章';
	}
	
	@list($newyear, $newmonth, $newday, $newhour, $newmin, $newsec) = explode('-', $article['time_tmp']);

	//载入编辑器
	include('editor.php');
}//end add or mod

if ($action == 'list') {
	$cid  = intval($_GET['cid']);
	$view = $_GET['view'];
	if ($_GET['tag']) {
		$item = addslashes($_GET['tag']);
		$tag = $DB->fetch_one_array("SELECT usenum,aids FROM {$db_prefix}tags WHERE tag='$item'");
		if (!$tag) {
			redirect('标签不存在', 'admincp.php?job=article&action=list');
		}
		$addquery = " AND a.articleid IN (".$tag['aids'].")";
		$subnav = 'Tags:'.$item;
		$pagelink = '&tag='.urlencode($item);
	} elseif ($view == 'stick') {
		$addquery = " AND a.stick='1'";
		$subnav = '置顶文章';
		$pagelink = '&view=stick';
	} elseif ($view == 'hidden') {
		$addquery = " AND a.visible='0'";
		$subnav = '隐藏文章';
		$pagelink = '&view=hidden';
	} elseif ($cid) {
		$cate = $DB->fetch_one_array("SELECT name FROM {$db_prefix}categories WHERE cid='$cid'");
		$subnav = '分类:'.$cate['name'];
		$addquery = " AND a.cid='$cid'";
		$pagelink = '&cid='.$cid;
	} else {
		$subnav = '全部文章';
		$addquery = '';
		$pagelink = '';
	}
	if ($sax_group != 1) {
		$subnav = '您发表的全部文章';
	}
	// 搜索部分
	$searchsql = '';
	if ($do == 'search') {
		$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
		if ($keywords) {
			$keywords = str_replace("_","\_",$keywords);
			$keywords = str_replace("%","\%",$keywords);
			if(preg_match("(AND|\+|&|\s)", $keywords) && !preg_match("(OR|\|)", $keywords)) {
				$andor = ' AND ';
				$sqltxtsrch = '1';
				$keywords = preg_replace("/( AND |&| )/is", "+", $keywords);
			} else {
				$andor = ' OR ';
				$sqltxtsrch = '0';
				$keywords = preg_replace("/( OR |\|)/is", "+", $keywords);
			}
			$keywords = str_replace('*', '%', addcslashes($keywords, '%_'));
			foreach(explode("+", $keywords) AS $text) {
				$text = trim($text);
				if($text) {
					$sqltxtsrch .= $andor;
					$sqltxtsrch .= "(a.content LIKE '%".str_replace('_', '\_', $text)."%' OR a.description LIKE '%".$text."%' OR a.title LIKE '%".$text."%')";
				}
			}
			$searchsql .= " AND ($sqltxtsrch)";
		}
		//分类
		$cids = array();
		foreach($catecache AS $data){
			$cids[] = $data['cid'];
		}
		unset($data);
		if (in_array($_POST['cateid'], $cids)) {
			$searchsql .= " AND a.cid='".intval($_POST['cateid'])."'";
		}
		$dateoffset = date('Z') - $timeoffset * 3600;
		$searchsql .= $_POST['startdate'] ? " AND a.dateline < '".(intval(strtotime($_POST['startdate'])) + $dateoffset)."'" : '';
		$searchsql .= $_POST['enddate'] ? " AND a.dateline > '".(intval(strtotime($_POST['enddate'])) + $dateoffset)."'" : '';
		$subnav = '搜索结果';
	}
	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$rs = $DB->fetch_one_array("SELECT count(*) AS articles FROM {$db_prefix}articles a WHERE 1 $searchsql $addquery $uquery");
	$tatol = $rs['articles'];
	$multipage = multi($tatol, 30, $page, 'admincp.php?job=article&action=list'.$pagelink);

	$query = $DB->query("SELECT a.articleid,a.title,a.cid,a.dateline,a.comments,a.trackbacks,a.attachments,a.visible,a.stick,c.name as cname
		FROM {$db_prefix}articles a 
		LEFT JOIN {$db_prefix}categories c ON c.cid=a.cid
		WHERE 1 $searchsql $addquery $uquery ORDER BY articleid DESC LIMIT $start_limit, 30");

	$articledb = array();
    while ($article = $DB->fetch_array($query)) {
		if ($article['attachments']) {
			$article['attachments'] = count(unserialize(stripslashes_array($article['attachments'])));
			$article['attachment'] = '<a href="admincp.php?job=attachment&action=list&amp;articleid='.$article['articleid'].'">操作</a>('.$article['attachments'].')';
		} else {
			$article['attachment'] = '<a href="admincp.php?job=attachment&action=list&amp;articleid='.$article['articleid'].'"><span class="yes">上传</span></a>';
		}
		$article['visible'] = $article['visible'] ? '<span class="yes">显示</span>' : '<span class="no">隐藏</span>';
		$article['title'] = trimmed_title($article['title'],20);
		$article['dateline'] = sadate('Y-m-d H:i',$article['dateline']);
		$articledb[] = $article;
	}
	unset($article);
	$DB->free_result($query);
} //end list

if ($action == 'search') {
	$subnav = '搜索文章';
}

$navlink_L = ' &raquo; <a href="admincp.php?job=article">文章管理</a>'.($subnav ? ' &raquo; '.$subnav : '');
cpheader();
include PrintEot('article');
?>