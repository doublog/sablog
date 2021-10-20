<?php
// ========================== 文件说明 ==========================//
// 本文件说明：后台公共函数
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

// 控制面板各页面页眉
function cpheader($shownav = 1) {
	global $options,$adminitem,$job,$SABLOG_VERSION,$SABLOG_RELEASE,$navlink_L,$userinfo;
	include PrintEot('header');
}

// 操作提示页面
function redirect($msg, $url = 'javascript:history.go(-1);', $min='2') {
	include PrintEot('redirect');
	PageEnd();
}

// 控制面板各页面页脚
function cpfooter() {
	global $options,$adminitem,$action,$starttime,$DB,$SABLOG_VERSION,$SABLOG_RELEASE;
	$mtime     = explode(' ', microtime());
	$totaltime = number_format(($mtime[1] + $mtime[0] - $starttime), 6);
	$gzip = $options['gzipcompress'] ? 'enabled' : 'disabled';
	$debuginfo = 'Processed in '.$totaltime.' second(s), '.$DB->querycount.' queries, Gzip '.$gzip;
	include PrintEot('footer');
	PageEnd();
}

function PrintEot($template){
	global $options;
	if (!$template) $template = 'none';
	$path = SABLOG_ROOT.'templates/admin/'.$template.'.php';
	return $path;
}

// 返回GD函数版本号
function gd_version() {	
	if (function_exists('gd_info')) {
		$GDArray = gd_info(); 
		$gd_version_number = $GDArray['GD Version'] ? $GDArray['GD Version'] : 0;
		unset($GDArray);
	} else {
		$gd_version_number = 0;
	}
	return $gd_version_number;
}

//目录的实际大小
function dirsize($dir) { 
	$dh = @opendir($dir);
	$size = 0;
	while($file = @readdir($dh)) {
		if ($file != '.' && $file != '..') {
			$path = $dir.'/'.$file;
			if (@is_dir($path)) {
				$size += dirsize($path);
			} else {
				$size += @filesize($path);
			}
		}
	}
	@closedir($dh);
	return $size;
}

//目录个数
function dircount($dir) { 
	$dh = @opendir($dir);
	$count = 0;
	while($file = @readdir($dh)) {
		if ($file != '.' && $file != '..') {
			$path = $dir.'/'.$file;
			if (@is_dir($path)) {
				$count++;
			}
		}
	}
	@closedir($dh);
	return $count;
}

// 获取数据库大小单位
function get_real_size($size) {
	$kb = 1024;         // Kilobyte
	$mb = 1024 * $kb;   // Megabyte
	$gb = 1024 * $mb;   // Gigabyte
	$tb = 1024 * $gb;   // Terabyte

	if($size < $kb) {
		return $size.' Byte';
	}else if($size < $mb) {
		return round($size/$kb,2).' KB';
	}else if($size < $gb) {
		return round($size/$mb,2).' MB';
	}else if($size < $tb) {
		return round($size/$gb,2).' GB';
	}else {
		return round($size/$tb,2).' TB';
	}
}

// 获取管理员散列
function getadminhash($uid,$username,$password,$count) {
	global $timestamp;
	return substr(md5(substr($timestamp, 0, -7).$username.$uid.$password.$count), 8, 8);
}

// 获取管理员的信息
function getadmininfo($password) {
	return md5($_SERVER["HTTP_USER_AGENT"].$password);
}

// 后台登录记录
function loginresult($result) {
	global $timestamp,$onlineip,$sax_user;
	writelog(SABLOG_ROOT.'cache/log/loginlog.php', "<?PHP exit('Access Denied'); ?>\t$sax_user\t$timestamp\t$onlineip\t$result\n");
}

// 后台管理记录
function getlog() {
	global $timestamp, $onlineip, $sax_user;
	if ($_POST['action']) {
		$action = $_POST['action'];
		$script = str_replace('job=', '', $_SERVER['QUERY_STRING']);
		writelog(SABLOG_ROOT.'cache/log/adminlog.php', "<?PHP exit('Access Denied'); ?>\t$timestamp\t".htmlspecialchars($sax_user)."\t$onlineip\t".htmlspecialchars(trim($action))."\t".htmlspecialchars(trim($script))."\n");
	}
}

// 写日至
function writelog($filename,$filedata) {
	@$fp=fopen($filename, 'a');
	@flock($fp, 2);
	@fwrite($fp, $filedata);
	@fclose($fp);
	@chmod($filename, 0777);
}

// 检查标题是否符合逻辑
function checktitle($title) {
	if(!$title || strlen($title) > 120) {
		$result = '标题不能为空并且不能超过120个字符<br />';
		return $result;
	}
}

// 检查分类是否已选择
function checkcate($cid) {
	if(!$cid) {
		$result = '你还没有选择分类<br />';
		return $result;
	}
}

// 检查提交内容是否符合逻辑
function checkcontent($content) {
	if(!$content || strlen($content) < 4) {
		$result .= '内容不能为空并且不能少于4个字符<br />';
		return $result;
	}
}

// 检查提交关键字是否符合逻辑
function checkkeywords($keywords) {
	$v = explode(',', $keywords);
	$v_num = count($v);
	if ($v_num > 5) {
		$result .= '标签(Tags)的关键字不能超过5个<br />';
		return $result;
	} else {
		for($i=0; $i<$v_num; $i++) {
			if(strlen($v[$i]) > 15) {
				$result .= '标签(Tags)的每个关键字不能超过15个字符, ".htmlspecialchars($v[$i])." 超过了15个字符<br />';
				return $result;
			}
		}
	}
}

// 检查提交Tag是否符合逻辑
function checktag($tag) {
	$tag = str_replace('，', ',', $tag);
	if (strrpos($tag, ',')) {
		$result .= '关键字中不能含有“,”或“，”字符<br />';
		return $result;
	}
	if(strlen($tag) > 15) {
		$result .= '关键字不能超过15个字符<br />';
		return $result;
	}
}

// 检查链接URL是否符合逻辑
function checkurl($url,$allownull=1) {
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
				}
			}
		}
	} else {
		if (!$allownull) {
			$result .= '网站URL不允许为空.<br />';
			return $result;
		}
	}
}

// 链接缩短
function cuturl($url) {
	$length = 45;
	$urllink = '<a href="'.(substr(strtolower($url), 0, 4) == 'www.' ? "http://$url" : $url).'" target="_blank">';
	if(strlen($url) > $length) {
		$url = substr($url, 0, intval($length * 0.5)).' ... '.substr($url, - intval($length * 0.3));
	}
	$urllink .= $url.'</a>';
	return $urllink;
}

// 分页函数
function multi($num, $perpage, $curpage, $mpurl) {
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
	if($num > $perpage) {
		$page = 10;
		$offset = 5;
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
		$multipage = $multipage ? '页: '.$multipage : '';
	}
	return $multipage;
}

// 重建所有缓存
function restats() {
	links_recache();
	newcomments_recache();
	settings_recache();
	categories_recache();
	statistics_recache();
	archives_recache();
	hottags_recache();
	stylevars_recache();
}

// 发送数据包
function sendpacket($url, $data) {
	$uinfo = parse_url($url);
	if ($uinfo['query']) {
		$data .= '&'.$uinfo['query'];
	}
	if (!$fp = @fsockopen($uinfo['host'], ($uinfo['port'] ? $uinfo['port'] : '80'), $errno, $errstr, 3)) {
		return false;
	}
	fputs ($fp, "POST ".$uinfo['path']." HTTP/1.1\r\n");
	fputs ($fp, "Host: ".$uinfo['host']."\r\n");
	fputs ($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	fputs ($fp, "Content-length: ".strlen($data)."\r\n");
	fputs ($fp, "Connection: close\r\n\r\n");
	fputs ($fp, $data);
	$http_response = '';
	while(!feof($fp)) {
		$http_response .= fgets($fp, 128);
	}
	@fclose($fp);
	list($http_headers, $http_content) = explode('\r\n\r\n', $http_response);
	return $http_response;
}

// 修改Tags并处理数量
function updatetags($articleid, $newkeywords, $oldkeywords) {
	global $DB, $db_prefix;
	if (substr($newkeywords, -1) == ',') {
		$newkeywords = substr($newkeywords, 0, strlen($newkeywords)-1);
	}
	$arrtag     = explode(',', $newkeywords);
	$arrold     = explode(',', $oldkeywords);
	$arrtag_num = count($arrtag);
	$arrold_num = count($arrold);

	for($i=0; $i<$arrtag_num; $i++) {
		if (!in_array($arrtag[$i], $arrold)) {
			$arrtag[$i] = trim($arrtag[$i]);
			if ($arrtag[$i]) {
				$tag  = $DB->fetch_one_array("SELECT tagid,aids FROM {$db_prefix}tags WHERE tag='$arrtag[$i]'");
				if(empty($tag)) {
					$DB->query("INSERT INTO {$db_prefix}tags (tag,usenum,aids) VALUES ('$arrtag[$i]', '1', '$articleid')");
				} else {						
					$aids = $tag['aids'].','.$articleid;
					$DB->unbuffered_query("UPDATE {$db_prefix}tags SET usenum=usenum+1, aids='$aids' WHERE tag='$arrtag[$i]'");
				}
			}
		}
		unset($aids);
	}

	for($i=0; $i<$arrold_num; $i++) {
		if (!in_array($arrold[$i], $arrtag)) {
			$tag = $DB->fetch_one_array("SELECT aids FROM {$db_prefix}tags WHERE tag='$arrold[$i]'");
			$tag['aids'] = str_replace(','.$articleid, '', $tag['aids']);
			$tag['aids'] = str_replace($articleid.',', '', $tag['aids']);
			$DB->unbuffered_query("UPDATE {$db_prefix}tags SET usenum=usenum-1, aids='".$tag['aids']."' WHERE tag='$arrold[$i]'");
		}
	}
	$DB->unbuffered_query("DELETE FROM {$db_prefix}tags WHERE usenum='0'");
}

function ifselected($var, $out) {
	global ${$out.'_Y'},${$out.'_N'};
	if($var) {
		${$out.'_Y'} = 'selected';
	} else {
		${$out.'_N'} = 'selected';
	}
}

// 转换时间单位:秒 to XXX
function format_timespan($seconds = '') {
	if ($seconds == '') $seconds = 1;
	$str = '';
	$years = floor($seconds / 31536000);
	if ($years > 0) {
		$str .= $years.' 年, ';
	}
	$seconds -= $years * 31536000;
	$months = floor($seconds / 2628000);
	if ($years > 0 || $months > 0) {
		if ($months > 0) {
			$str .= $months.' 月, ';
		}
		$seconds -= $months * 2628000;
	}
	$weeks = floor($seconds / 604800);
	if ($years > 0 || $months > 0 || $weeks > 0) {
		if ($weeks > 0)	{
			$str .= $weeks.' 周, ';
		}
		$seconds -= $weeks * 604800;
	}
	$days = floor($seconds / 86400);
	if ($months > 0 || $weeks > 0 || $days > 0) {
		if ($days > 0) {
			$str .= $days.' 天, ';
		}
		$seconds -= $days * 86400;
	}
	$hours = floor($seconds / 3600);
	if ($days > 0 || $hours > 0) {
		if ($hours > 0) {
			$str .= $hours.' 小时, ';
		}
		$seconds -= $hours * 3600;
	}
	$minutes = floor($seconds / 60);
	if ($days > 0 || $hours > 0 || $minutes > 0) {
		if ($minutes > 0) {
			$str .= $minutes.' 分钟, ';
		}
		$seconds -= $minutes * 60;
	}
	if ($str == '') {
		$str .= $seconds.' 秒, ';
	}
	$str = substr(trim($str), 0, -1);
	return $str;
}

// 上传文件
function upfile($source, $target) {
	// 如果一种函数上传失败，还可以用其他函数上传
	if (function_exists('move_uploaded_file') && @move_uploaded_file($source, $target)) {
		@chmod($target, 0666);
		return $target;
	} elseif (@copy($source, $target)) {
		@chmod($target, 0666);
		return $target;
	} elseif (@is_readable($source)) {
		if ($fp = @fopen($source,'rb')) {
			@flock($fp,2);
			$filedata = @fread($fp,@filesize($source));
			@fclose($fp);
		}
		if ($fp = @fopen($target, 'wb')) {
			@flock($fp, 2);
			@fwrite($fp, $filedata);
			@fclose($fp);
			@chmod ($target, 0666);
			return $target;
		} else {
			return false;
		}
	}
}

// 判断文件是否是通过 HTTP POST 上传的
function disuploadedfile($file) {
	return function_exists('is_uploaded_file') && (is_uploaded_file($file) || is_uploaded_file(str_replace('\\\\', '\\', $file)));
}

// 连接多个ID
function implode_ids($array){
	$ids = $comma = '';
	if (is_array($array) && count($array)){
		foreach($array as $id) {
			$ids .= "$comma'".intval($id)."'";
			$comma = ', ';
		}
	}
	return $ids;
}

// 后台登陆入口页面
function loginpage(){
	global $sax_uid, $sax_user, $sax_group;
	setcookie('sax_admin', '');
	cpheader(0);
	include PrintEot('login');
	cpfooter();
}

// 检查权限
function permission() {
	global $sax_group;
	if ($sax_group != 1) {
		redirect('你没有此功能的管理权限','admincp.php');
	}
}

?>