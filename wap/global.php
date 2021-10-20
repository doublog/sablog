<?php
// ========================== 文件说明 ==========================//
// 本文件说明：WAP公共函数
// --------------------------------------------------------------//
// 本程序作者：angel
// --------------------------------------------------------------//
// 本程序版本：SaBlog-X Ver 1.6
// --------------------------------------------------------------//
// 本程序主页：http://www.4ngel.net
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


// 清除HTML代码
function html_clean($content) {
	$content = htmlspecialchars($content);
	$content = str_replace("\n", "<br />", $content);
	$content = str_replace("  ", "&nbsp;&nbsp;", $content);
	$content = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $content);
	$content = str_replace("$", "$$", $content);
	return $content;
}

// 转换&#39
function cvurl($content) {
	$content = str_replace("&", "&amp;", $content);
	return $content;
}

// 获取用户的hash
function getuserhash($uid, $name, $pass, $logincount) {
	return substr(md5(substr($timestamp, 0, -7).$uid.$name.$pass.$logincount.$_SERVER['HTTP_USER_AGENT']), 8, 8);
}

// HTML转换为纯文本
function html2text($content) {
	$content = preg_replace("/<style .*?<\/style>/is", "", $content);
	$content = preg_replace("/<script .*?<\/script>/is", "", $content);
	$content = preg_replace("/<br\s*\/?>/i", "\n", $content);
	$content = preg_replace("/<\/?p>/i", "\n", $content);
	$content = preg_replace("/<\/?td>/i", "\n", $content);
	$content = preg_replace("/<\/?div>/i", "\n", $content);
	$content = preg_replace("/<\/?blockquote>/i", "\n", $content);
	$content = preg_replace("/<\/?li>/i", "\n", $content);
	$content = preg_replace("/\&nbsp\;/i", " ", $content);
	$content = preg_replace("/\&nbsp/i", " ", $content); 
	$content = strip_tags($content);
	$content = preg_replace("/\&\#.*?\;/i", "", $content);
	return $content;
}

// 后台登录记录
function loginresult($username = '', $result) {
	global $timestamp,$onlineip;
	writelog(SABLOG_ROOT.'cache/log/loginlog.php', "<?PHP exit('Access Denied'); ?>\t$username\t$timestamp\t$onlineip\t$result\n");
}

function writelog($filename,$filedata) {
	@$fp=fopen($filename, 'a');
	@flock($fp, 2);
	@fwrite($fp, $filedata);
	@fclose($fp);
}
// 后台管理记录
function getlog() {
	global $timestamp, $onlineip, $action, $sax_user;
	if ($action) {
		writelog(SABLOG_ROOT.'cache/log/adminlog.php', "<?PHP exit('Access Denied'); ?>\t$timestamp\t$sax_user\t$onlineip\t".htmlspecialchars(trim($action))."\twap\n");
	}
}
// 检查用户提交内容合法性
function checkcontent($content) {
	global $options;
    if(empty($content)) {
        $result .= '内容不能为空.<br />';
        return $result;
	}
    if(strlen($content) < $options['comment_min_len']) {
        $result .= '内容不能少于'.$options['comment_min_len'].'字节.<br />';
        return $result;
	}
	if(strlen($content) > $options['comment_max_len']) {
        $result .= '内容不能超过'.$options['comment_max_len'].'字节.<br />';
        return $result;
	}
}

// 检查链接URL是否符合逻辑
function checkurl($url) {
	if($url) {
		if (isemail($url)) {
			return false;
		} else {
			if (!preg_match("#^(http|news|https|ftp|ed2k|rtsp|mms)://#", $url)) {
				$result .= '网站URL错误.<br />';
				return $result;
			}
			$key = array("\\",' ',"'",'"','*',',','<','>',"\r","\t","\n",'(',')','+',';');
			foreach($key as $value){
				if (strpos($url,$value) !== false){ 
					$result .= '网站URL错误.<br />';
					return $result;
					break;
				}
			}
		}
	}
}

// 系统消息
function wap_message($msg,$link = array()) {
	echo '<p>'.$msg.'</p>';
	if ($link) {
		echo '<p><a href="'.$link['link'].'">'.$link['title'].'</a></p>';
	}
	echo "<p><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
	exit();
}

function wap_norun($title,$msg='') {
	$msg = $msg ? $msg : $title;
	wap_header($title);
	echo '<p>'.$msg.'</p>';
	wap_footer();
}

// WML文档头
function wap_header($title) {
	header("Content-type: text/vnd.wap.wml; charset=utf-8");
	echo "<?xml version=\"1.0\"?>\n";
	echo "<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\" \"http://www.wapforum.org/ DTD/wml_1.1.xml\">\n\n";
	echo "<wml>\n";
	echo "<head>\n";
	echo "<meta http-equiv=\"cache-control\" content=\"max-age=180,private\" />\n";
	echo "</head>\n";
	echo "<card title=\"".$title."\">\n";
}

// WML文档尾
function wap_footer() {
	echo "</card>\n";
	echo "</wml>\n";
	wap_output();
	exit;
}

function transhash($url, $tag = '') {
	global $hash;
	$tag = stripslashes_array($tag);
	if(!$tag || (!preg_match("/^(http:\/\/|mailto:|#|javascript)/i", $url) && !strpos($url, 'hash='))) {
		if($pos = strpos($url, '#')) {
			$urlret = substr($url, $pos);
			$url = substr($url, 0, $pos);
		} else {
			$urlret = '';
		}
		$url .= (strpos($url, '?') ? '&amp;' : '?').'hash='.$hash.$urlret;
	}
	return $tag.$url;
}

function wap_output() {
	global $charset, $chs;
	$content = ob_get_contents();
	$content = preg_replace("/\<a(\s*[^\>]+\s*)href\=([\"|\']?)([^\"\'\s]+)/ies", "transhash('\\3','<a\\1href=\\2')", $content);
	ob_end_clean();
	//要做内容编码转换将来对这个content就可以了
	echo $content;
}

// 分页函数
function multi($num, $perpage, $curpage, $mpurl) {
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
	if($num > $perpage) {
		$page = 7;
		$offset = 3;
		$pages = @ceil($num / $perpage);
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $curpage + $page - $offset - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $curpage - $pages + $to;
				$to = $pages;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$from = $pages - $page + 1;
				}
			}
		}

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1">第一页</a> ' : '').($curpage > 1 ? '<a href="'.$mpurl.'page='.($curpage - 1).'">上一页</a> ' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? $i.' ' : '<a href="'.$mpurl.'page='.$i.'">['.$i.']</a> ';
		}
		$multipage .= ($curpage < $pages ? '<a href="'.$mpurl.'page='.($curpage + 1).'">下一页</a>' : '').($to < $pages ? ' <a href="'.$mpurl.'page='.$pages.'">最后一页</a>' : '');
		$multipage = $multipage ? '<p>页: '.$multipage."</p>\n" : '';
	}
	return $multipage;
}

?>