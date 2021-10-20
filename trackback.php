<?php
// ========================== 文件说明 ==========================//
// 本文件说明：Trackback接收
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


// 加载前台常用函数
require_once('global.php');

header('Content-type: text/xml');	
// 加载编码转换函数
if (!$options['enable_trackback']) {
	showxml('Trackback功能没有开启');
}
$code = $_GET['code'] ? $_GET['code'] : $_POST['code'];
$code = authcode($code, 'DECODE');
$carr = explode("\t", $code);
if(count($carr) != 3) {
	showxml('参数不正确');
}
$articleid = intval($carr[0]);

//检查失效时间
if($options['trackback_life'] && ($timestamp-intval($carr[1])>(3600*24))) {
	showxml('已经超过本文允许Trackback的时间');
}

$article = $DB->fetch_one_array("SELECT dateline,trackbacks,closetrackback FROM {$db_prefix}articles WHERE articleid='$articleid'");
if (!$article) {
	showxml('文章不存在');
} elseif ($article['closetrackback']) {
	showxml('本文此时不允许引用');
} elseif ($article['trackbacks'] != intval($carr[2])) {
	showxml('trackback数量验证失败');
} elseif ($article['dateline'] != intval($carr[1])) {
	showxml('文章时间验证失败');
}

$url = addslashes(trim($_POST['url']));
if ($url) {
	$title     = html2text($_POST['title']);
	$excerpt   = trimmed_title(html2text($_POST['excerpt']), 255);
	$blog_name = html2text($_POST['blog_name']);
}

if (!$title || !$excerpt || !$url || !$blog_name) {
	showxml('参数不正确');
} elseif(substr($url, 0, 7) != 'http://') {
	showxml('参数不正确');
}

// 检查Spam
// 定义发送来的此条Trackback初始分数
$point = 0;
$options['tb_spam_level'] = in_array($options['tb_spam_level'], array('strong', 'weak', 'never')) ? $options['tb_spam_level'] : 'weak';

if ($options['audit_trackback']) {
	//如果人工审核
	$visible = '0';
} elseif ($options['tb_spam_level'] != 'never') {
	$source_content = '';
	$source_content = fopen_url($url);
	$this_server = str_replace(array('www.', 'http://'), '', $_SERVER['HTTP_HOST']);
	//获取接受来的url原代码和本服务器的hostname

	if (empty($source_content)) {
		//没有获得原代码就-1分
		$point -= 1;
	} else {
		if (strpos(strtolower($source_content), strtolower($this_server)) !== FALSE) {
			//对比链接，如果原代码中包含本站的hostname就+1分，这个未必成立
			$point += 1;
		}
		if (strpos(strtolower($source_content), strtolower($title)) !== FALSE) {
			//对比标题，如果原代码中包含发送来的title就+1分，这个基本可以成立
			$point += 1;
		}
		if (strpos(strtolower($source_content), strtolower($excerpt)) !== FALSE) {
			//对比内容，如果原代码中包含发送来的excerpt就+1分，这个由于标签或者其他原因，未必成立
			$point += 1;
		}
	}
	$interval = $options['tb_spam_level'] == 'strong' ? 300 : 600;
	//根据防范强度设置时间间隔，强的话在5分钟内发现有同一IP发送。弱的话就是10分钟内发现有同一IP发送.
	$query = $DB->query("SELECT trackbackid FROM {$db_prefix}trackbacks WHERE ipaddress='$onlineip' AND dateline+".$interval.">='$timestamp'");
	//在单位时间内发送的次数
	if ($DB->num_rows($query)) {
		//如果发现在单位时间内同一IP发送次数大于0就扣一分，人工有这么快发送trackback的吗？
		$point -= 1;
	}

	$query = $DB->query("SELECT trackbackid FROM {$db_prefix}trackbacks WHERE REPLACE(LCASE(url),'www.','')='".str_replace('www.','',strtolower($url))."'");
	//对比数据库中的url和接收来的
	if ($DB->num_rows($query)) {
		//如果发现有相同，扣一分。
		$point -= 1;
	}

	//禁止词语
	if ($options['spam_enable'] && $options['spam_words']) {
		$options['spam_words'] = str_replace('，', ',', $options['spam_words']);
		$badwords = explode(',', $options['spam_words']);
		if (is_array($badwords) && count($badwords) ) {
			foreach ($badwords AS $n) {
				if ($n) {
					if (preg_match( "/".preg_quote($n, '/' )."/i", $title.$excerpt.$url.$blog_name)) {
						$point -= 1;
					}
				}
			}
		}
	}

	if ($options['tb_spam_level'] == 'strong') {
		//高强度防范
		$query = $DB->query("SELECT title,ipaddress,articleid FROM {$db_prefix}trackbacks WHERE ipaddress='$onlineip' OR articleid='$articleid'");
		//搜索数据库内发送此trackback来本站的IP和文章ID
		while ($trackback = $DB->fetch_array($query)) {
			if ($trackback['title'] == $title && $trackback['ipaddress'] == $onlineip) {
				//如果数据库内的title和接收来的title一样，IP也一样，视为重复发送。就减1分。
				$point -= 1;
			}
			if ($trackback['ipaddress'] == $onlineip && $trackback['articleid'] == $articleid) {
				//如果数据库内的articleid和接收来的articleid一样，IP也一样，视为重复发送。就减1分。
				$point -= 1;
			}
		}
		// 防范强:最终分数少于1分就CUT！
		$visible = ($point < 1) ? '0' : '1';
	} else {
		// 防范弱:最终分数少于0分才CUT！
		$visible = ($point < 0) ? '0' : '1';
	}
} else {
	$visible = '1';
}
// 检查Spam完毕

$DB->query("INSERT INTO {$db_prefix}trackbacks (articleid, title, dateline, excerpt, url, blog_name, ipaddress, visible, point) VALUES('$articleid', '$title', '$timestamp', '$excerpt', '$url', '$blog_name', '$onlineip', '$visible', '$point')");
//更新文章Trackback数量
if ($visible) {
	$DB->unbuffered_query("UPDATE {$db_prefix}articles SET trackbacks=trackbacks+1 WHERE articleid='$articleid'");
	$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET trackback_count=trackback_count+1");
	if ($stats_expire < $timestamp) {
		require_once(SABLOG_ROOT.'include/cache.php');
		statistics_recache();
	}
}
showxml('Trackback 成功接收',0);

//发送消息页面
function showxml($message, $error = 1) {
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	echo "<response>\n";
	echo "\t<error>".$error."</error>\n";
	echo "\t<message>".$message."</message>\n";
	echo "</response>\n";
	exit;
}

//获取远程页面的内容
function fopen_url($url) {
	if (function_exists('file_get_contents')) {
		$file_content = file_get_contents($url);
	} elseif (ini_get('allow_url_fopen') && ($file = @fopen($url, 'rb'))){
		$i = 0;
		while (!feof($file) && $i++ < 1000) {
			$file_content .= strtolower(fread($file, 4096));
		}
		fclose($file);
	} elseif (function_exists('curl_init')) {
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT,2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl_handle, CURLOPT_FAILONERROR,1);
  		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'SaBlog-X Trackback Spam Check');
		$file_content = curl_exec($curl_handle);		
		curl_close($curl_handle);
	} else {
		$file_content = '';
	}
	return $file_content;
}

//转换到UTF-8编码
/*
还转个P呀。不是的utf-8的乱码就乱码吧。管你的。直接忽略非UTF-8编码的。
function iconv2utf($chs) {
	global $encode;
	if ($encode != 'utf-8') {
		if (function_exists('mb_convert_encoding')) {
			$chs = mb_convert_encoding($chs, 'UTF-8', $encode);
		} elseif (function_exists('iconv')) {
			$chs = iconv($encode, 'UTF-8', $chs);
		}
	}
	return $chs;
}
*/

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
	$content = strip_tags($content);
	$content = preg_replace("/\&\#.*?\;/i", "", $content);
	$content = char_cv($content);
	return $content;
}
?>