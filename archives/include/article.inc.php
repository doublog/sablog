<?php
// ========================== 文件说明 ==========================//
// 本文件说明：归档文章浏览
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

$articleid = intval($article);
// 获取文章信息	
if (!$articleid) {
	message('缺少参数', './');
} else {
	$article = $DB->fetch_one_array("SELECT a.articleid,a.cid,a.uid,a.title,a.content,a.keywords,a.dateline,a.comments,a.attachments,a.trackbacks,a.readpassword,c.name as cname,u.username 
		FROM {$db_prefix}articles a 
		LEFT JOIN {$db_prefix}categories c ON c.cid=a.cid
		LEFT JOIN {$db_prefix}users u ON a.uid=u.userid
		WHERE a.visible='1' AND articleid='$articleid'");

	if (!$article) {
		message('记录不存在', './');
	}
}

if ($article['readpassword']) {
	$article['allowread'] = false;
} else {
	$article['allowread'] = true;
	$DB->unbuffered_query("UPDATE {$db_prefix}articles SET views=views+1 WHERE articleid='$articleid'");

	//附件
	if ($article['attachments']) {
		$attachs= unserialize(stripslashes_array($article['attachments']));
		if (is_array($attachs)) {
			$attachnum = 0;
			foreach ($attachs AS $attach) {
				$a_path = '../'.$options['attachments_dir'].$attach['filepath'];
				if (file_exists($a_path)) {
					$attachnum++;
				}
			}
		}
	}
	//获取附件结束

	if (preg_match("/\[php\].+?\[\/php\]/is", $article['content'])) {
		$article['content'] = preg_replace("/\s*\[php\](.+?)\[\/php\]\s*/ies", "phphighlite('\\1')", $article['content']);
	}
	$article['dateline'] = sadate('Y-m-d H:i',$article['dateline']);

	//TAGS
	if ($article['keywords']) {
		$articletags = $tmark = '';
		$v = explode(',', $article['keywords']);
		$v_num = count($v);
		for($i=0; $i<$v_num; $i++) {
			$v[$i] = trim($v[$i]);
			$articletags .= $tmark.'<a href="../?action=tags&amp;item='.urlencode($v[$i]).'">'.htmlspecialchars($v[$i]).'</a>';
			$tmark = ', ';
		}
		$article['tags'] = $articletags;
	}
	unset($articletags);

	// Trackback
	if ($options['enable_trackback'] && $article['trackbacks']) {
		$tborderid = 0;
		$tborder = $options['trackback_order'] ? 'ASC' : 'DESC';
		$query = $DB->query("SELECT title,dateline,excerpt,url,blog_name FROM {$db_prefix}trackbacks WHERE visible='1' AND articleid='$articleid' ORDER BY trackbackid ".$tborder);
		if ($options['trackback_excerpt_limit'] > 255) {
			$options['trackback_excerpt_limit'] = 60;
		}
		$trackbackdb=array();
		while ($trackback = $DB->fetch_array($query)) {
			$tborderid++;
			$trackback['tborderid'] = $tborderid;
			$trackback['excerpt'] = trimmed_title($trackback['excerpt'], $options['trackback_excerpt_limit']);
			$trackback['dateline'] = sadate('Y-m-d H:i', $trackback['dateline']);
			$trackbackdb[] = $trackback;
		}
		unset($trackback);
		$DB->free_result($query);
	}
			
	// 评论
	if ($article['comments']) {
		$cmtorder = $options['comment_order'] ? 'ASC' : 'DESC';
		$query = $DB->query("SELECT author,url,dateline,content FROM {$db_prefix}comments WHERE articleid='$articleid' AND visible='1' ORDER BY commentid ".$cmtorder);
		$commentdb=array();
		while ($comment=$DB->fetch_array($query)) {
			$cmtorderid++;
			$comment['cmtorderid'] = $cmtorderid;
			if ($comment['url']) {
				if (isemail($comment['url'])) {
					//分解邮件地址并采用javascript输出
					$frontlen = strrpos($comment['url'], '@');
					$front    = substr($comment['url'], 0, $frontlen);
					$emaillen = strlen($comment['url']);
					$back     = substr($comment['url'], $frontlen+1, $emaillen);
					$comment['author'] = "<a href=\"javascript:navigate('mai' + 'lto:' + '".$front."' + '@' + '".$back."')\" target=\"_blank\">".$comment['author']."</a>";
				} else {
					$comment['author'] = '<a href="'.$comment['url'].'" target="_blank">'.$comment['author'].'</a>';
				}
			}
			$comment['content'] = html_clean($comment['content']);
			$comment['dateline'] = sadate('Y-m-d H:i', $comment['dateline']);
			$commentdb[]=$comment;
		}
		unset($comment);
		$DB->free_result($query);
	}
}

$options['title'] = $article['title'];
include PrintEot('header');
include PrintEot('article');
?>