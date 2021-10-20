<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
$current_page_item = array();
if (in_array($action, array('index', 'finduser', 'tags'))) {
	$current_page_item['index'] = ' class="current_page_item"';
} elseif (in_array($action, array('archives', 'tagslist', 'comments', 'trackbacks', 'search', 'links'))) {
	$current_page_item[$action] = ' class="current_page_item"';
}
print <<<EOT
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="keywords" content="4ngel,4ngel.net,安全,天使,安全天使,技术,黑客,网络,原创,论坛,自由,严肃,网络安全,组织,系统安全,系统,windows,web,web安全,web开发,$options[meta_keywords]" />
<meta name="description" content="4ngel,4ngel.net,安全,天使,安全天使,技术,黑客,网络,原创,论坛,自由,严肃,网络安全,组织,系统安全,系统,windows,web,web安全,web开发,$options[meta_description]" />
<meta name="copyright" content="SaBlog" />
<meta name="author" content="angel,4ngel" />
<link rel="alternate" title="$options[name]" href="rss.php" type="application/rss+xml" />
<link rel="stylesheet" href="templates/$options[templatename]/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="include/code.css" type="text/css" media="all" />
<script type="text/javascript">
	var postminchars = parseInt("$options[comment_min_len]");
	var postmaxchars = parseInt("$options[comment_max_len]");
</script>
<script type="text/javascript" src="include/common.js"></script>
<title>$options[title] $options[title_keywords] - Powered by Sablog-X</title>
</head>
<body>
<div id="outmain">
<div id="header">
    <h1><a href="$options[url]">$options[name]</a></h1>
    <ul class="menu">
        <li{$current_page_item[index]}><a href="./">Home</a></li>
        <li{$current_page_item[archives]}><a href="./?action=archives">Archives222</a></li>
        <li{$current_page_item[search]}><a href="./?action=search">Search</a></li>
        <li{$current_page_item[tagslist]}><a href="./?action=tagslist">Tags</a></li>
        <li{$current_page_item[comments]}><a href="./?action=comments">Comments</a></li>
      <!--
EOT;
if ($options['enable_trackback']) {print <<<EOT
-->
        <li{$current_page_item[trackbacks]}><a href="./?action=trackbacks">Trackbacks</a></li>
      <!--
EOT;
}print <<<EOT
-->
        <li{$current_page_item[links]}><a href="./?action=links">Links</a></li>
	</ul>
</div>
<div id="topmenu">
  <span id="description">$options[description]</span>
  <span id="guestlink">
    <!--
EOT;
if ($sax_uid) {print <<<EOT
-->
    欢迎您，$sax_user &raquo; <a href="?action=profile">资料</a> | <a href="post.php?action=logout">注销</a>
<!--
EOT;
if ($sax_group == 1 || $sax_group == 2) {print <<<EOT
--> | <a href="admin/admincp.php" target="_blank">管理</a>
<!--
EOT;
}}else{print <<<EOT
-->
    <a href="./?action=reg">注册</a> | <a href="./?action=login">登陆</a>
    <!--
EOT;
}print <<<EOT
-->
  </span>
</div>
<div id="page">
  <div id="wrap">
<!--
EOT;
require_once PrintEot($pagefile);
print <<<EOT
-->
  </div>
  <div id="sidebar">
    <p>
    <!--
EOT;
if ($options['wap_enable']) {print <<<EOT
-->
      <a href="wap/" target="_blank" title="手机浏览"><img src="templates/$options[templatename]/img/wap.gif" border="0" alt="手机浏览" /></a>
    <!--
EOT;
}
if ($options['rss_enable']) {print <<<EOT
-->
      <a href="rss.php" target="_blank" title="RSS 2.0 订阅"><img src="templates/$options[templatename]/img/rss.gif" border="0" alt="RSS 2.0 订阅" /></a>
      <!--
EOT;
}print <<<EOT
-->
    </p>
<!--
EOT;
if ($options['show_calendar']) {print <<<EOT
-->
<table cellpadding="0" cellspacing="1">
  <tr align="center">
    <td colspan="7" class="curdate"><a href="./?action=index&amp;setdate=$calendar[prevmonth]">&laquo;</a> $calendar[cur_date] <a href="./?action=index&amp;setdate=$calendar[nextmonth]">&raquo;</a></td>
  </tr>
  <tr>
    <th class="week"><font color="#CC0000">日</font></th>
    <th class="week">一</th>
    <th class="week">二</th>
    <th class="week">三</th>
    <th class="week">四</th>
    <th class="week">五</th>
    <th class="week"><font color="#53A300">六</font></th>
  </tr>
  $calendar[html]
</table>
    <!--
EOT;
}
if ($options['show_categories']) {print <<<EOT
-->
    <h2>日志分类</h2>
    <ul>
      <!--
EOT;
if(empty($catecache)){print <<<EOT
-->
      <li>没有任何分类</li>
      <!--
EOT;
}else{
foreach($catecache AS $data){
print <<<EOT
-->
      <li><a href="./?action=index&amp;cid=$data[cid]">$data[name]</a> <a href="./rss.php?cid=$data[cid]" target="_blank" title="RSS 2.0 订阅这个分类"><img src="templates/$options[templatename]/img/rss.gif" border="0" alt="RSS 2.0 订阅这个分类" /></a> <span>[$data[articles]]</span></li>
      <!--
EOT;
}}print <<<EOT
-->
    </ul>
    <!--
EOT;
}
if ($options['hottags_shownum']) {print <<<EOT
-->
    <h2>热门标签</h2>
    <ul>
      <!--
EOT;
if(empty($tagcache)){print <<<EOT
-->
      <li>没有任何标签</li>
      <!--
EOT;
}else{
foreach($tagcache AS $data){
print <<<EOT
-->
      <li><a href="./?action=tags&amp;item=$data[url]">$data[tag]</a> <span>[$data[usenum]]</span></li>
      <!--
EOT;
}}print <<<EOT
-->
    </ul>
    <!--
EOT;
}
if ($options['show_archives']) {print <<<EOT
-->
    <h2>日志归档</h2>
    <ul>
      <!--
EOT;
if(empty($archivecache)){print <<<EOT
-->
      <li>没有任何归档</li>
      <!--
EOT;
}else{
if (is_numeric($options['archives_num']) && $options['archives_num']) {
	$archivecache = array_slice($archivecache,0,$options['archives_num']);
}
foreach($archivecache AS $key => $val){
$v = explode('-', $key);
//$e_month = ($v[1] < 10) ? str_replace('0', '', $v[1]) : $v[1];
print <<<EOT
-->
      <li><a href="./?action=index&amp;setdate=$v[0]$v[1]">{$v[0]}年{$v[1]}月</a> <span>[$val]</span></li>
      <!--
EOT;
}}print <<<EOT
-->
    </ul>
<!--
EOT;
if (is_numeric($options['archives_num']) && $options['archives_num']) {print <<<EOT
-->
    <p class="more"><a href="./?action=archives">更多...</a></p>
    <!--
EOT;
}}print <<<EOT
-->
    <h2>搜索文章</h2>
    <form method="post" action="post.php">
      <input type="hidden" name="formhash" value="$formhash" />
      <input type="hidden" name="action" value="search" />
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input class="formfield" maxlength="30" size="17" name="keywords" />
          </td>
          <td><button type="submit" class="formbutton">确定</button>
          </td>
        </tr>
        <tr>
          <td colspan="2"><a href="./?action=search">高级搜索</a></td>
        </tr>
      </table>
    </form>
    <!--
EOT;
if($options['allow_search_comments']){print <<<EOT
-->
    <h2>搜索评论</h2>
    <form method="post" action="post.php">
      <input type="hidden" name="formhash" value="$formhash" />
      <input type="hidden" name="action" value="search" />
      <input type="hidden" name="searchfrom" value="comment" />
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input class="formfield" maxlength="30" size="17" name="keywords" />
          </td>
          <td><button type="submit" class="formbutton">确定</button>
          </td>
        </tr>
        <tr>
          <td colspan="2">匹配评论人和内容</td>
        </tr>
      </table>
    </form>
    <!--
EOT;
}
if ($options['recentcomment_num']) {print <<<EOT
-->
    <h2>最新评论</h2>
    <ul>
      <!--
EOT;
if(empty($newcommentcache)){print <<<EOT
-->
      <li>没有任何评论</li>
      <!--
EOT;
}else{
foreach($newcommentcache AS $data){
$data[content] = stripslashes_array($data[content]);
print <<<EOT
-->
      <li><a href="./?action=show&amp;id=$data[articleid]&amp;cmid=$data[commentid]&amp;goto=newcm">$data[content]</a><br /><span>$data[dateline] - $data[author]</span></li>
      <!--
EOT;
}}print <<<EOT
-->
    </ul>
    <p class="more"><a href="./?action=comments">更多...</a></p>
    <!--
EOT;
}
if ($options['show_statistics']) {print <<<EOT
-->
    <h2>博客信息</h2>
    <ul>
      <li>分类数量: <span class="num">$stats[cate_count]</span></li>
      <li>文章数量: <span class="num">$stats[article_count]</span></li>
      <li>评论数量: <span class="num">$stats[comment_count]</span></li>
      <li>标签数量: <span class="num">$stats[tag_count]</span></li>
      <li>附件数量: <span class="num">$stats[attachment_count]</span></li>
      <!--
EOT;
if ($options['enable_trackback']) {print <<<EOT
-->
      <li>引用数量: <span class="num">$stats[trackback_count]</span></li>
      <!--
EOT;
}print <<<EOT
-->
      <li>注册用户: <span class="num">$stats[user_count]</span></li>
      <li>今日访问: <span class="num">$stats[today_view_count]</span></li>
      <li>总访问量: <span class="num">$stats[all_view_count]</span></li>
      <li>程序版本: <span class="num">$SABLOG_VERSION</span></li>
    </ul>
    <!--
EOT;
}
if ($options['sidebarlinknum']) {print <<<EOT
-->
    <h2>友情链接</h2>
    <ul>
      <!--
EOT;
if(!$linkcache){print <<<EOT
-->
      <li><a href="http://www.4ngel.net" target="_blank" title="安全天使网络安全小组">S4T</a></li>
      <li><a href="http://www.sablog.net" target="_blank" title="Sablog-X官方网站">Sablog-X</a></li>
      <li><a href="http://www.sablog.net/blog" target="_blank" title="angel's blog">angel's blog</a></li>
      <!--
EOT;
}else{
foreach($linkcache AS $data){
print <<<EOT
-->
      <li><a href="$data[url]" target="_blank" title="$data[note]">$data[name]</a></li>
      <!--
EOT;
}}print <<<EOT
-->
    </ul>
	<!--
EOT;
if ($link_count > $options['sidebarlinknum']) {print <<<EOT
-->
    <p class="more"><a href="./?action=links">更多...</a></p>
	<!--
EOT;
}}
print <<<EOT
-->
  </div>
</div>
<!--
EOT;
?>-->