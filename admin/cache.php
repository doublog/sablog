<?php
// ========================== 文件说明 ==========================//
// 本文件说明：缓存管理
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

$url = 'admincp.php?job=cache';
if ($action == 'updateall') {
	restats();
	redirect('所有缓存已经更新', $url);
}

if ($action == 'delsearchlog') {
	$DB->unbuffered_query("TRUNCATE TABLE {$db_prefix}searchindex");
	redirect('搜索记录已经清空', $url);
}
	
// 更新首页统计
if ($action == 'dostatsdata') {
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
	// 更新首页显示的用户数
	$user_count = $DB->num_rows($DB->query("SELECT userid FROM {$db_prefix}users"));
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET user_count='$user_count'");
	statistics_recache();
	redirect('首页统计已经更新', 'admincp.php?job=cache&action=rebuild');
}
// 更新所有分类的文章数
if ($action == 'docatedata') {
	$query = $DB->query("SELECT cid, name FROM {$db_prefix}categories");
	while ($cate = $DB->fetch_array($query)) {
		$tatol = $DB->num_rows($DB->query("SELECT articleid FROM {$db_prefix}articles WHERE visible = '1' AND cid='".$cate['cid']."'"));
		$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles='$tatol' WHERE cid='".$cate['cid']."'");
	}
	categories_recache();
	redirect('所有分类的文章数已经更新', 'admincp.php?job=cache&action=rebuild');
}
// 重建文章数据
if ($action == 'doarticledata') {
	$step = (!$step) ? 1 : $step;
	$percount = ($percount <= 0) ? 100 : $percount;
	$start    = ($step - 1) * $percount;
	$next     = $start + $percount;
	$step++;
	$jumpurl  = 'admincp.php?job=cache&action=doarticledata&step='.$step.'&percount='.$percount;
	$goon     = 0;
	$query = $DB->query("SELECT articleid FROM {$db_prefix}articles ORDER BY articleid LIMIT $start, $percount");
	while ($article = $DB->fetch_array($query)) {
		$goon = 1;
		// 更新所有文章的评论数
		$tatol = $DB->num_rows($DB->query("SELECT commentid FROM {$db_prefix}comments WHERE articleid='".$article['articleid']."' AND visible='1'"));
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments='$tatol' WHERE articleid='".$article['articleid']."'");
		// 更新所有文章的Trackback数
		$tatol = $DB->num_rows($DB->query("SELECT trackbackid FROM {$db_prefix}trackbacks WHERE visible='1' AND articleid='".$article['articleid']."'"));
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET trackbacks='$tatol' WHERE articleid='".$article['articleid']."'");
	}
	if($goon){
		redirect('正在更新 '.$start.' 到 '.$next.' 项', $jumpurl, '2');
	} else{
		$article_count = $DB->num_rows($DB->query("SELECT articleid FROM {$db_prefix}articles WHERE visible = '1'"));
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count='$article_count'");
		statistics_recache();
		redirect('成功重建所有文章数据', 'admincp.php?job=cache&action=rebuild');
	}
}
// 重建后台用户文章数量
if ($action == 'doadmindata') {
	$query = $DB->query("SELECT userid FROM {$db_prefix}users WHERE groupid='1' OR groupid='2'");
	while ($user = $DB->fetch_array($query)) {
		$tatol = $DB->num_rows($DB->query("SELECT articleid FROM {$db_prefix}articles WHERE visible='1' AND uid='".$user['userid']."'"));
		$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles='$tatol' WHERE userid='".$user['userid']."'");
	}
	redirect('成功重建所有后台用户数据', 'admincp.php?job=cache&action=rebuild');
}

// 重建附件缩略图
if ($action == 'dothumbdata') {
	require_once(SABLOG_ROOT.'include/func_attachment.php');
	$step = (!$step) ? 1 : $step;
	$percount = ($percount <= 0) ? 100 : $percount;
	$start    = ($step - 1) * $percount;
	$next     = $start + $percount;
	$step++;
	$jumpurl  = 'admincp.php?job=cache&action=dothumbdata&step='.$step.'&percount='.$percount;
	$goon     = 0;
	$size = explode('x', strtolower($options['attachments_thumbs_size']));

	$attachquery = $DB->query("SELECT * FROM {$db_prefix}attachments WHERE isimage='1' AND thumb_filepath <> '' ORDER BY attachmentid LIMIT $start, $percount");
	while($attach = $DB->fetch_array($attachquery)) {
		$goon = 1;
		if (file_exists(SABLOG_ROOT.$options['attachments_dir'].$attach['thumb_filepath'])) {
			@unlink(SABLOG_ROOT.$options['attachments_dir'].$attach['thumb_filepath']);
			$DB->unbuffered_query("UPDATE {$db_prefix}attachments SET thumb_filepath='', thumb_width='', thumb_height='' WHERE attachmentid='".$attach['attachmentid']."'");
		}
		if (!$options['attachments_thumbs']) {
			$attach_data['thumbwidth']    = '';
			$attach_data['thumbheight']   = '';
			$attach_data['thumbfilepath'] = '';
		} else {
			$extension = getextension($attach['filepath']);
			switch($options['attachments_save_dir']) {
				case 0: $attachsubdir = '/'; break;
				case 1: $attachsubdir = '/cate_'.$attach['cid'].'/'; break;
				case 2: $attachsubdir = '/date_'.sadate('Ym',$attach['dateline']).'/'; break; //按月放
				case 4: $attachsubdir = '/ext_'.$extension.'/'; break; //按文件类型
			}
			$thumbname = substr($attach['filepath'],strlen($attachsubdir),32);
			if ($imginfo=@getimagesize(SABLOG_ROOT.$options['attachments_dir'].$attach['filepath'])) {
				if ($imginfo[2]) {
					if (($imginfo[0] > $size[0]) || ($imginfo[1] > $size[1])) {
						$attach_thumb = array(
							'filepath'     => SABLOG_ROOT.$options['attachments_dir'].$attach['filepath'],
							'filename'     => $thumbname,
							'extension'    => $extension,
							'attachsubdir' => $attachsubdir,
							'thumbswidth'  => $size[0],
							'thumbsheight' => $size[1],
						);
						$thumb_data = generate_thumbnail($attach_thumb);
						$attach_data['thumbwidth']    = $thumb_data['thumbwidth'];
						$attach_data['thumbheight']   = $thumb_data['thumbheight'];
						$attach_data['thumbfilepath'] = $attachsubdir.$thumb_data['thumbfilepath'];
					}
				}
			}
		}
		$DB->unbuffered_query("UPDATE {$db_prefix}attachments SET thumb_filepath='".$attach_data['thumbfilepath']."', thumb_width='".$attach_data['thumbwidth']."', thumb_height='".$attach_data['thumbheight']."' WHERE attachmentid='".$attach['attachmentid']."'");
		$article = $DB->fetch_one_array("SELECT attachments FROM {$db_prefix}articles WHERE articleid='".$attach['articleid']."'");
		$attachs = unserialize(stripslashes_array($article['attachments']));
		@extract($attachs[$attach['attachmentid']]);
		$attachs[$attach['attachmentid']]['thumb_filepath'] = $attach_data['thumbfilepath'];
		$attachs[$attach['attachmentid']]['thumb_width'] = $attach_data['thumbwidth'];
		$attachs[$attach['attachmentid']]['thumb_height'] = $attach_data['thumbheight'];
		$attachs = addslashes(serialize($attachs));
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET attachments='$attachs' WHERE articleid='".$attach['articleid']."'");

		unset($attach_data);
	}
	if($goon){
		redirect('正在更新 '.$start.' 到 '.$next.' 项', $jumpurl, '2');
	} else{
		redirect('成功重建所有附件缩略图', 'admincp.php?job=cache&action=rebuild');
	}
}

if ($action == 'dorewrite') {	
	$DB->query("REPLACE INTO {$db_prefix}settings VALUES ('rewrite_enable', '1'),('rewrite_ext', '".addslashes($_POST['rewrite_ext'])."')");
	settings_recache();
	redirect('已经开启URL优化功能.', 'admincp.php?job=cache&action=rewrite_1');
}

if ($action == 'closeurlseo') {
	$DB->query("REPLACE INTO {$db_prefix}settings VALUES ('rewrite_enable', '0')");
	settings_recache();
	redirect('已经关闭URL优化功能.', 'admincp.php?job=cache&action=rewrite_1');
}

if ($action == 'update') {
	switch($_GET['id']) {
		case 'links':
			links_recache();
			redirect('友情链接缓存已经更新', $url);
			break;
		case 'newcomments':
			newcomments_recache();
			redirect('侧边栏显示的最新评论缓存已经更新', $url);
			break;
		case 'settings':
			settings_recache();
			redirect('常规选项缓存已经更新', $url);
			break;
		case 'categories':
			categories_recache();
			redirect('侧边栏分类缓存已经更新', $url);
			break;
		case 'statistics':
			statistics_recache();
			redirect('站点统计信息缓存已经更新', $url);
			break;
		case 'archives':
			archives_recache();
			redirect('文章归档缓存已经更新', $url);
			break;
		case 'hottags':
			hottags_recache();
			redirect('侧边栏热门标签缓存已经更新', $url);
			break;
		case 'stylevars':
			stylevars_recache();
			redirect('自定义模板变量缓存已经更新', $url);
			break;
		default:
			redirect('请选择需要更新的缓存', $url);
			break;
	}
}

if(!$action || $action == 'cache') {
	require_once(SABLOG_ROOT.'include/func_attachment.php');
	$cachedesc = array(
		'archives'	  => '文章归档',
		'categories'  => '侧边栏分类',
		'hottags'     => '侧边栏热门标签',
		'links'		  => '友情链接',
		'newcomments' => '侧边栏显示的最新评论',
		'settings'    => '常规选项',
		'statistics'  => '站点统计信息',
		'stylevars'   => '自定义模板变量'
	);
	$cachedb = array();
	foreach ($cachedesc AS $name => $desc)	{
		$filepath = '../cache/cache_'.$name.'.php';
		if(is_file($filepath)) {
			$cachefile['name'] = $name;
			$cachefile['desc'] = $desc;
			$cachefile['size'] = sizecount(filesize($filepath));
			$cachefile['mtime'] = @sadate('Y-m-d H:i',@filemtime($filepath));
			$fp=fopen($filepath,'rb');
			$bakinfo=fread($fp,200);
			fclose($fp);
			$detail=explode("\n",$bakinfo);
			$cachefile['ctime'] = (strlen($detail[2]) == 33) ? substr($detail[2],13,16) : '未知';
			$cachedb[] = $cachefile;
		}
	}
	unset($cachefile);
	$subnav = '缓存管理';
}

// 查看缓存
if ($action == 'show') {
	$name = $_GET['id'];	
	$name = in_array($name, array('archives', 'categories', 'hottags', 'links', 'newcomments', 'settings', 'statistics','stylevars')) ? $name : '';
	if ($name) {
		$filepath = '../cache/cache_'.$name.'.php';
		if(is_file($filepath)) {
			$fp = fopen($filepath,'rb');
			$cachedata = fread($fp,filesize($filepath));
			$cachedata = str_replace("<?php\r\n//Sablog-X cache file\r\n",'',$cachedata);
			$cachedata = str_replace("\r\nif(!defined('SABLOG_ROOT')) exit('Access Denied');\r\n",'',$cachedata);
			$cachedata = str_replace("\r\n\r\n?>",'',$cachedata);
			ob_start();
			print_r(htmlspecialchars($cachedata));
			$data = ob_get_contents();
			ob_end_clean();
			$subnav = '查看缓存';
		} else {		
			redirect('缓存文件不存在', $url);
		}
	} else {		
		redirect('缓存文件不存在', $url);
	}
}

// 重建数据
if($action == 'rebuild') {
	$subnav = '重建数据';
}//rebuild

if($action == 'searchlog') {
	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$dodesc = array(
		'article' => '文章',
		'comment' => '评论'
	);
	$searchs  = $DB->query("SELECT * FROM {$db_prefix}searchindex");
	$tatol     = $DB->num_rows($searchs);
	$multipage = multi($tatol, 30, $page, "admincp.php?job=cache&action=searchlog");
	$searchdb = array();
	$query = $DB->query("SELECT * FROM {$db_prefix}searchindex ORDER BY searchid DESC LIMIT $start_limit, 30");
	while ($search = $DB->fetch_array($query)) {
		$search['dateline'] = sadate('Y-m-d H:i',$search['dateline']);
		$search['searchfrom'] = $dodesc[$search['searchfrom']];
		$searchdb[] = $search;
	}//end while
	unset($search);
	$DB->free_result($query);
	$subnav = '搜索记录';
}

if ($action == 'rewrite_1') {
	$query = $DB->query("SELECT * FROM {$db_prefix}settings WHERE title='rewrite_enable' OR title='rewrite_ext'");
	while($setting = $DB->fetch_array($query)) {
		$settings[$setting['title']] = htmlspecialchars($setting['value']);
	}
	ifselected($settings['rewrite_enable'],'rewrite_enable');
	$subnav = 'URL优化';
}

if ($action == 'rewrite_2') {
	//读取rewrite规则源文件
	$rewritefile = SABLOG_ROOT.'include/apache_rewrite.txt';
	$filecontent = '';
	if(is_readable($rewritefile)) {
		$fp = fopen($rewritefile, 'rb'); 
		$filecontent = fread($fp, filesize($rewritefile));
		fclose($fp);
		$rewrite_ext = $rewrite_ext ? $rewrite_ext : 'html';
		$filecontent = str_replace(array('<SABLOG_ROOT>','<SABLOG_REWRITE_EXT>'),array(dirname(dirname($php_self)),$rewrite_ext),$filecontent);
	} else {
		redirect('rewrite规则源文件不存在', 'admincp.php?job=cache&action=rewrite_1');
	}
	$filecontent = htmlspecialchars($filecontent);
	$subnav = 'URL优化';
}

// JS调用
if ($action == 'js') {
	if(!$options['js_enable']) {
        redirect('系统JS调用功能尚未启用,请到系统设置开启', 'admincp.php?job=configurate&type=js');
	}
	$view = in_array($_POST['view'], array('article', 'stat')) ? $_POST['view'] : '';
	$orderby = in_array($_POST['orderby'], array('dateline', 'views', 'comments')) ? $_POST['orderby'] : 'dateline';

	$parentdir = basename(dirname($php_self));
	if ($view) {
		$code = '<script type="text/javascript" charset="utf-8" src="'.str_replace('/'.$parentdir,'',$options['url']).'js.php?view=';
		if ($view == 'article') {
			$msg = '调用文章代码';
			$code .= 'article';
			$code .= (empty($_POST['cate']) || !is_array($_POST['cate']) ? '' : '&cids='.implode('_',$_POST['cate'])). '&titlenum='.$_POST['titlenum'].'&titlelimit='.$_POST['titlelimit'].'&newwindow='.$_POST['newwindow'].'&cname='.$_POST['cname'].'&author='.$_POST['author'].'&orderby='.$orderby.'&dateline='.$_POST['dateline'].'&articleinfo='.$_POST['articleinfo'];
		} else {
			$msg = '调用统计信息代码';
			$code .= 'stat';
			$code .= '&showcate='.$_POST['showcate'].'&showarticle='.$_POST['showarticle'].'&showcomment='.$_POST['showcomment'].'&showtag='.$_POST['showtag'].'&showattach='.$_POST['showattach'].'&showtrack='.$_POST['showtrack'].'&showreguser='.$_POST['showreguser'].'&showtoday='.$_POST['showtoday'].'&showallviews='.$_POST['showallviews'];
		}
		$code .= "\"></script>";
	}
	$showcode = htmlspecialchars($code);
	$subnav = 'JS调用向导';
}

$navlink_L = ' &raquo; <a href="admincp.php?job=cache">系统维护</a>'.($subnav ? ' &raquo; '.$subnav : '');
cpheader();
include PrintEot('cache');
?>