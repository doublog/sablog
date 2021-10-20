<?php
// ========================== 文件说明 ==========================//
// 本文件说明：JS调用
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

error_reporting(0);
define('SABLOG_ROOT', './');
$timestamp = time();

// 加载缓存操作函数
require_once(SABLOG_ROOT.'cache/cache_settings.php');

if(!$options['js_enable']) {
	exit("document.write(\"系统JS调用功能尚未启用\");");
}
// 系统URL
if (!$options['url']) {
	//HTTP_HOST已经包含端口信息,不必加SERVER_PORT了.
	$options['url'] = 'http://'.$_SERVER['HTTP_HOST'].dirname($php_self).'/';
} else {
	$options['url'] = str_replace(array('{host}','index.php'), array($_SERVER['HTTP_HOST'],''), $options['url']);
	if (substr($options['url'], -1) != '/') {
		$options['url'] = $options['url'].'/';
	}
}
($options['gzipcompress'] && function_exists('ob_gzhandler')) ? @ob_start('ob_gzhandler') : ob_start();

if ($options['js_lock_url'] && !in_array($_SERVER['HTTP_HOST'],explode("\r\n",$options['js_lock_url']))) {
	exit("document.write(\"<a href=\"".$options['url']."\">".$options['name']."</a>已限制调用\");");
}

$jscachelife = $options['js_cache_life'] ? intval($options['js_cache_life']) : 0;
$timeformat = isset($options['listtime']) ? $options['listtime'] : 'Y-m-d';

$expiration	= 0;
$view = in_array($_GET['view'], array('article', 'stat')) ? $_GET['view'] : 'article';
$cids = isset($_GET['cids']) ? $_GET['cids'] : '';
$titlenum =	isset($_GET['titlenum']) ? intval($_GET['titlenum']) : 10;
$newwindow = isset($_GET['newwindow']) ? $_GET['newwindow'] : 1;
$LinkTarget	= $newwindow ? ' target=\"_blank\"' : '';

if ($view == 'article') {
	$orderby = in_array($_GET['orderby'], array('dateline', 'views', 'comments')) ? $_GET['orderby'] : 'dateline';
	$cname = isset($_GET['cname']) ? $_GET['cname'] : 0;
	$author = isset($_GET['author']) ? $_GET['author'] : 0;
	$dateline = isset($_GET['dateline']) ? $_GET['dateline'] : 0;
	$titlelimit = isset($_GET['titlelimit']) ? intval($_GET['titlelimit']) : 50;
	$articleinfo = isset($_GET['articleinfo']) ? $_GET['articleinfo'] : 0;
	$cachefile = SABLOG_ROOT.'cache/js_'.md5("article|$cids|$titlenum|$orderby").'.php';
	if((@!include($cachefile)) || $expiration < $timestamp) {
		// 加载数据库配置信息
		require_once('config.php');
		// 加载数据库类
		require_once(SABLOG_ROOT.'include/func_db_mysql.php');
		// 初始化数据库类
		$DB = new DB_MySQL;
		$DB->connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect);
		unset($servername, $dbusername, $dbpassword, $dbname, $usepconnect);

		$datalist = array();
		$cidtmp = explode("_", $cids);
		for($i=0; $i<count($cidtmp); $i++) {
			$cidtmp[$i] = intval($cidtmp[$i]);
		}
		$cids = implode(',',$cidtmp);
		$sql = ($cids ? " AND a.cid IN ($cids)" : '');
		$query = $DB->query("SELECT a.articleid,a.cid,a.uid,a.dateline,a.title,a.views,a.comments,c.name as cname,u.username 
			FROM {$db_prefix}articles a 
			LEFT JOIN {$db_prefix}categories c ON c.cid=a.cid
			LEFT JOIN {$db_prefix}users u ON a.uid=u.userid
			WHERE a.visible='1' $sql AND a.cid > '0' ORDER BY a.$orderby DESC LIMIT 0,".$titlenum);

		while($article = $DB->fetch_array($query))	{
			$datalist[$article['articleid']]['cid'] = $article['cid'];
			$datalist[$article['articleid']]['cname'] = $article['cname'];
			$datalist[$article['articleid']]['cnamelen'] = strlen($datalist[$article['articleid']]['cname']);
			$datalist[$article['articleid']]['username'] = $article['username'];
			$datalist[$article['articleid']]['title'] = $article['title'];
			$datalist[$article['articleid']]['dateline'] = sadate($timeformat,$article['dateline']);
			$datalist[$article['articleid']]['views'] = $article['views'];
			$datalist[$article['articleid']]['comments'] = $article['comments'];
		}
		unset($article);
		$DB->free_result($query);
		$writedata = "\$datalist = unserialize('".addcslashes(serialize($datalist), '\\\'')."');";
		// 删除失效的缓存文件
		if (file_exists($cachefile)) {
			@unlink($cachefile);
		}
		updatecache($cachefile,$writedata);
	}
	if (is_array($datalist)) {
		foreach ($datalist AS $articleid=>$value) {
			echo "document.writeln(\"".($cname ? "[<a href=\\\"".getcateurl($value['cid'])."\\\" ".$LinkTarget.">".$value['cname']."</a>] " : '')."<a href=\\\"".getarticleurl($articleid)."\\\" title=\\\"".$value['title']."\\\" ".$LinkTarget.">".trimmed_title($value['title'],($cname ? ($titlelimit - $value['cnamelen']) : $titlelimit))."</a>".($author ? " by ".$value['username'] : '').($dateline ? " on ".$value['dateline'] : '').($articleinfo ? " (浏览:<font color=#CC0000>".$value['views']."</font> 评论:<font color=#CC0000>".$value['comments']."</font>)" : '')."<br />\");\r\n";
		}
	}
} elseif ($view == 'stat') {
	if(@!include(SABLOG_ROOT.'cache/cache_statistics.php')) {
		echo "document.write(\"读取统计信息失败\");\r\n";
	} else {
		if ($_GET['showcate']) {
			echo "document.write(\"分类数量: <font color=#CC0000>".$stats['cate_count']."</font><br />\");\r\n";
		}
		if ($_GET['showarticle']) {
			echo "document.write(\"文章数量: <font color=#CC0000>".$stats['article_count']."</font><br />\");\r\n";
		}
		if ($_GET['showcomment']) {
			echo "document.write(\"评论数量: <font color=#CC0000>".$stats['comment_count']."</font><br />\");\r\n";
		}
		if ($_GET['showtag']) {
			echo "document.write(\"标签数量: <font color=#CC0000>".$stats['tag_count']."</font><br />\");\r\n";
		}
		if ($_GET['showattach']) {
			echo "document.write(\"附件数量: <font color=#CC0000>".$stats['attachment_count']."</font><br />\");\r\n";
		}
		if ($_GET['showtrack'] && $options['enable_trackback']) {
			echo "document.write(\"引用数量: <font color=#CC0000>".$stats['trackback_count']."</font><br />\");\r\n";
		}
		if ($_GET['showreguser']) {
			echo "document.write(\"注册用户: <font color=#CC0000>".$stats['user_count']."</font><br />\");\r\n";
		}
		if ($_GET['showtoday']) {
			echo "document.write(\"今日访问: <font color=#CC0000>".$stats['today_view_count']."</font><br />\");\r\n";
		}
		if ($_GET['showallviews']) {
			echo "document.write(\"总访问量: <font color=#CC0000>".$stats['all_view_count']."</font><br />\");\r\n";
		}
	}
} else {
	exit("document.write(\"未定义操作\");");
}

//格式化时间
function sadate($format,$timestamp){
	global $options;
	$timeoffset = (!$options['server_timezone'] && $options['server_timezone'] == '111') ? 0 : $options['server_timezone'];
	return gmdate($format,$timestamp+$timeoffset*3600);
}

//更新缓存
function updatecache($cachfile,$data='') {
	global $jscachelife;
	if(!$fp = @fopen($cachfile, 'wb')) {
		exit("document.write(\"写入缓存文件失败\");");
	} else {
		@fwrite($fp, "<?php\r\nif(!defined('SABLOG_ROOT')) exit('Access Denied');\r\n\$expiration='".($timestamp + $jscachelife)."';\r\n".$data."\r\n?>");
		@fclose($fp);
		@chmod($cachfile,0777);
	}
}

//截取字数
function trimmed_title($text, $limit=12) {
	if ($limit) {
		$val = csubstr($text, 0, $limit);
		return $val[1] ? $val[0]."..." : $val[0];
	} else {
		return $text;
	}
}

function csubstr($text, $start=0, $limit=12) {
	if (function_exists('mb_substr')) {
		$more = (mb_strlen($text, 'UTF-8') > $limit) ? TRUE : FALSE;
		$text = mb_substr($text, 0, $limit, 'UTF-8');
		return array($text, $more);
	} elseif (function_exists('iconv_substr')) {
		$more = (iconv_strlen($text) > $limit) ? TRUE : FALSE;
		$text = iconv_substr($text, 0, $limit, 'UTF-8');
		return array($text, $more);
	} else {
		preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $text, $ar);   
		if(func_num_args() >= 3) {   
			if (count($ar[0])>$limit) {
				$more = TRUE;
				$text = join("",array_slice($ar[0],0,$limit))."..."; 
			} else {
				$more = FALSE;
				$text = join("",array_slice($ar[0],0,$limit)); 
			}
		} else {
			$more = FALSE;
			$text =  join("",array_slice($ar[0],0)); 
		}
		return array($text, $more);
	} 
}

// 根据伪静态状态返回相应的文章连接
function getarticleurl($articleid) {
	global $options;
	if ($options['rewrite_enable']) {
		$url = $options['url'].'show-'.$articleid.'-1.'.$options['rewrite_ext'];
	} else {
		$url = $options['url'].'?action=show&id='.$articleid;
	}
	return $url;
}

// 根据伪静态状态返回相应的分类连接
function getcateurl($cid) {
	global $options;
	if ($options['rewrite_enable']) {
		$url = $options['url'].'category-'.$cid.'-1.'.$options['rewrite_ext'];
	} else {
		$url = $options['url'].'?action=index&cid='.$cid;
	}
	return $url;
}

?>