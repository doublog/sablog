<?php
// ========================== 文件说明 ==========================//
// 本文件说明：前台公共函数
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

// 加载公用函数
require_once('include/common.php');

// 检查模版
$t_dir = SABLOG_ROOT.'templates/'.$options['templatename'].'/';
if (!is_dir($t_dir)) {
	if (is_dir(SABLOG_ROOT.'templates/default')) {
		$options['templatename'] = 'default';
	} else {
		exit('Template Error: '.$t_dir.' is not a directory.');
	}
}

// 状态检查
if ($options['close']) {
	message(html_clean($options['close_note']),'');
}

// 检查浏览模式
$viewmode = $_GET['viewmode'] ? $_GET['viewmode'] : $_COOKIE['viewmode'];
$viewmode = in_array($viewmode, array('normal', 'list')) ? $viewmode : $options['viewmode'];
if (!in_array($_COOKIE['viewmode'], array('normal', 'list')) || $viewmode != $_COOKIE['viewmode']) {
	setcookie('viewmode',$viewmode,$timestamp+31536000);
}

// 获取时间，假如是WIN系统，一定要做范围的限制。否则.....
$setdate = intval($_GET['setdate']);
if ($setdate && strlen($setdate) == 6) {
	$setyear = substr($setdate,0,4);
	if ($setyear >= 2038 || $setyear <= 1970) {
		$setyear = sadate('Y');
		$setmonth = sadate('m');
		$start = $end = 0;
	} else {
		$setmonth = substr($setdate,-2);
		list($start, $end) = explode('-', gettimestamp($setyear,$setmonth));
	}
} else {
	$setyear = sadate('Y');
	$setmonth = sadate('m');
	$start = $end = 0;
}

// 查询按月归档
$monthname = array('','January','February','March','April','May','June','July','August','September','October','November','December');
// 查询并生成日历
if ($options['show_calendar']) {
	$calendar = calendar($setyear,$setmonth);
}

// 是否随机友情链接
if ($options['random_links']) {
	shuffle($linkcache);
}
// 显示一定数量的友情链接
$options['sidebarlinknum'] = intval($options['sidebarlinknum']);
$linkcache = array_slice($linkcache,0,$options['sidebarlinknum']);

// 日历获取日历部分
function calendar($y,$m){
	//我操他妈的日历搞得我头痛!
	global $DB,$db_prefix,$options,$timestamp,$timeoffset,$monthname;
	!$y && $y = sadate('Y');
	!$m && $m = sadate('m');

	//当前月等于1
	if ($m == 1) {
		$lastyear = $y-1;
		$lastmonth = 12;
		$nextmonth = $m+1;
		$nextyear = $y;
	} elseif ($m == 12) {
		$lastyear = $y;
		$lastmonth = $m - 1;
		$nextyear = $y + 1;
		$nextmonth = 1;
	} else {
		$lastmonth = $m - 1;
		$nextmonth = $m + 1;
		$lastyear = $nextyear = $y;
	}
	if ($nextmonth < 10) $nextmonth = '0'.$nextmonth;
	if ($lastmonth < 10) $lastmonth = '0'.$lastmonth;

	$weekday   = sadate('w',mktime(0,0,0,$m,1,$y));
	$totalday  = sadate('t',mktime(0,0,0,$m,1,$y));
	list($start, $end) = explode('-', gettimestamp($y,$m));
	// 动态缓存
	$expiration	= 0;
	$cachefile = SABLOG_ROOT.'cache/cache_calendar.php';
	if (($m != sadate('m')) || ($y != sadate('Y')) || (!@include($cachefile)) || $expiration < $timestamp) {
		$query = $DB->query("SELECT dateline FROM {$db_prefix}articles WHERE visible='1' AND dateline >= '".correcttime($start)."' AND dateline < '".correcttime($end)."'");
		$datelines = array();
		$articledb = array();
		while($article = $DB->fetch_array($query)) {
			$datelines[] = sadate('Y-m-j',$article['dateline']);
			$day = sadate('j', $article['dateline']);
			if (!isset($articledb[$day])) {
				$articledb[$day]['num'] = 1;
			} else {
				$articledb[$day]['num']++;
			}
		}
		$br = 0;
		$ret['html'] = "<tr>\n";
		for ($i=1; $i<=$weekday; $i++) {
			$ret['html'] .= "<td class=\"cal_day1\"></td>\n";
			$br++;
		}
		for($i=1; $i<=$totalday; $i++){
			$br++;
			if (in_array($y.'-'.$m.'-'.$i, $datelines)) {
				$td = '<a title="'.$i.'日发表了 '.$articledb[$i]['num'].' 篇文章" href="./?action=index&amp;setdate='.$y.$m.'&amp;setday='.$i.'&amp;page=1">'.$i.'</a>';
			} else{
				$td = $i;
			}
			if ($i == sadate('d') && $m == sadate('m') && $y == sadate('Y')) {
				$class = 'cal_day2';
			} else {
				$class = 'cal_day1';
			}
			$ret['html'] .= "<td class=\"".$class."\">".$td."</td>\n";
			if ($br >= 7) {
				$ret['html'] .= "</tr>\n<tr>\n";
				$br = 0;
			}
		}
		if ($br != 0) {
			for($i=$br; $i<7;$i++){
				 $ret['html'] .= "<td class=\"cal_day1\"></td>\n";
			}
		}
		$ret['html'] .= "</tr>\n";
		if ($y.$m == sadate('Ym')) {
			if($fp = @fopen($cachefile, 'wb')) {
				$cachedata = "\$ret = unserialize('".addcslashes(serialize($ret), '\\\'')."');";
				@fwrite($fp, "<?php\r\nif(!defined('SABLOG_ROOT')) exit('Access Denied');\r\n\$expiration='".($timestamp + 300)."';\r\n".$cachedata."\r\n?>");
				// 5分钟自动更新,需要改变请手工修改上面的300,单位秒
				@fclose($fp);
				@chmod($cachefile, 0777);
			} else {
				echo 'Can not write to calendar cache files, please check directory ./cache/ .';
				exit;
			}
		}
	}
	$ret['prevmonth'] = $lastyear.$lastmonth;
	$ret['nextmonth'] = $nextyear.$nextmonth;
	$ret['cur_month'] = $m;
	$e_month = ($m < 10) ? str_replace('0', '', $m) : $m;
	//$ret['cur_date'] = $monthname[$e_month].' '.$y;
	$ret['cur_date'] = $y.'年'.$m.'月';
	return $ret;
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

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="p_redirect">&laquo;</a>' : '').($curpage > 1 ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="p_redirect">&#8249;</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<span class="p_curpage">'.$i.'</span>' : '<a href="'.$mpurl.'page='.$i.'" class="p_num">'.$i.'</a>';
		}
		$multipage .= ($curpage < $pages ? '<a href="'.$mpurl.'page='.($curpage + 1).'" class="p_redirect">&#8250;</a>' : '').($to < $pages ? '<a href="'.$mpurl.'page='.$pages.'" class="p_redirect">&raquo;</a>' : '');
		$multipage = $multipage ? '<div class="p_bar"><span class="p_info">Records:'.$num.'</span>'.$multipage.'</div>' : '';
	}
	return $multipage;
}

// 清除HTML代码
function html_clean($content) {
	$content = htmlspecialchars($content);
	$content = str_replace("\n", "<br />", $content);
	$content = str_replace("  ", "&nbsp;&nbsp;", $content);
	$content = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $content);
	$content = preg_replace("/\[quote=(.*?)\]\s*(.+?)\s*\[\/quote\]/is", "<div style=\"font-weight: bold\">引用 \\1 说过的话:</div><div class=\"quote\">\\2</div>", $content);
	return $content;
}

// 高亮显示PHP
function phphighlite($code) {
	if (floor(phpversion())<4) {
		$buffer = $code;
	} else {
		$code = preg_replace("/<style .*?<\/style>/is", "", $code);
		$code = preg_replace("/<script .*?<\/script>/is", "", $code);
		$code = preg_replace("/<br\s*\/?>/i", "\n", $code);
		$code = preg_replace("/<\/?p>/i", "\n", $code);
		$code = preg_replace("/<\/?td>/i", "\n", $code);
		$code = preg_replace("/<\/?div>/i", "\n", $code);
		$code = preg_replace("/<\/?blockquote>/i", "\n", $code);
		$code = preg_replace("/<\/?li>/i", "\n", $code);
		$code = strip_tags($code);
		$code = preg_replace("/\&\#.*?\;/i", "", $code);
		$code = str_replace("&nbsp;&nbsp;&nbsp;", "&nbsp;&nbsp;", $code);
		$code = str_replace("&nbsp;&nbsp;", "&nbsp;", $code);
		$code = str_replace("&nbsp;", "\t", $code);
		$code = str_replace("&quot;", '"', $code);
		$code = str_replace("<br>", "", $code);
		$code = str_replace("<br />", "", $code);
		$code = str_replace("&gt;", ">", $code);
		$code = str_replace("&lt;", "<", $code);
		$code = str_replace("&amp;", "&", $code);
		//$code = str_replace('$', '\$', $code);
		if (!strpos($code,"<?\n") and substr($code,0,4)!="<?\n") {
			$code="<?".trim($code)."?>";
			$addedtags=1;
		}
		ob_start();
		$oldlevel=error_reporting(0);
		highlight_string($code);
		error_reporting($oldlevel);
		$buffer = ob_get_contents();
		ob_end_clean();
		if ($addedtags) {
		  $openingpos = strpos($buffer,"&lt;?");
		  $closingpos = strrpos($buffer, "?");
		  $buffer=substr($buffer, 0, $openingpos).substr($buffer, $openingpos+5, $closingpos-($openingpos+5)).substr($buffer, $closingpos+5);
		}
		$buffer = str_replace("&quot;", "\"", $buffer);
	}
	return $buffer;
}

// 获取页面调试信息
function footer() {
	global $DB, $starttime, $options, $stylevar;
	$mtime = explode(' ', microtime());
	$totaltime = number_format(($mtime[1] + $mtime[0] - $starttime), 6);
	$gzip = $options['gzipcompress'] ? 'enabled' : 'disabled';
	$sa_debug = 'Processed in '.$totaltime.' second(s), '.$DB->querycount.' queries, Gzip '.$gzip;
	include PrintEot('footer');
	PageEnd();
}

// 同上
function upload($aid){
	global $article, $attachmentids, $options;
	if ($article['image'][$aid]) {
		$attachmentids[]=$aid;
		return "<a href=\"".$options['url']."attachment.php?id={$article[image][$aid][0]}\" target=\"_blank\"><img src=\"".$options['url']."{$article[image][$aid][1]}\" border=\"0\" alt=\"大小: {$article[image][$aid][2]}&#13;尺寸: {$article[image][$aid][3]} x {$article[image][$aid][4]}&#13;浏览: {$article[image][$aid][5]} 次&#13;点击打开新窗口浏览全图\" width=\"{$article[image][$aid][3]}\" height=\"{$article[image][$aid][4]}\" /></a>";
	} elseif ($article['file'][$aid]) {
		$attachmentids[]=$aid;
		return "<a href=\"".$options['url']."attachment.php?id={$article[file][$aid][0]}\" title=\"{$article[file][$aid][2]}, 下载次数:{$article[file][$aid][3]}\" target=\"_blank\">{$article[file][$aid][1]}</a>";
	} else {
		return "[attach=$aid]";
	}
}

// 消息显示页面
function message($msg,$returnurl='javascript:history.go(-1);',$min='3') {
	global $options, $stylevar;
	require_once PrintEot('message');
	PageEnd();
}

// 获得模板文件的路径
function PrintEot($template){
	global $options;
	if(!$template) $template='none';
	$path = SABLOG_ROOT.'templates/'.$options['templatename'].'/'.$template.'.php';
	if (file_exists($path)) {
		return $path;
	} else {
		return SABLOG_ROOT.'templates/default/'.$template.'.php';
	}
}

// 根据伪静态状态返回相应的文章连接
function getarticleurl($articleid, $page = 1) {
	global $options;
	!$page && $page = 1;
	if ($options['rewrite_enable']) {
		$articleurl = 'show-'.$articleid.'-'.$page.'.'.$options['rewrite_ext'];
	} else {
		$articleurl = './?action=show&id='.$articleid.'&page='.$page;
	}
	return $articleurl;
}

$modelink = '';
if ($action) {
	$modelink .= '&amp;action='.$action;
}
if ($cid) {
	$modelink .= '&amp;cid='.$cid;
}
if ($setdate) {
	$modelink .= '&amp;setdate='.$setdate;
}
if ($setday) {
	$modelink .= '&amp;setday='.$setday;
}
if (intval($_GET['searchid'])) {
	$modelink .= '&amp;searchid='.$_GET['searchid'];
}
if (intval($_GET['userid'])) {
	$modelink .= "&amp;userid=".$_GET['userid'];
}
if ($_GET['item']) {
	$item = urlencode(addslashes($item));
	$modelink .= '&amp;item='.$item;
}
?>