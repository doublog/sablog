<?
// ========================== 文件说明 ==========================//
// 本文件说明：WAP功能页面
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

require_once('../include/common.php');

// 检查WAP开启状态
if (!$options['wap_enable']) {
	exit('Wap disabled');
}

require_once('global.php');
require_once(SABLOG_ROOT.'include/visits.php');

// 博客系统状态检查
if ($options['close']) {
	wap_norun('博客已经关闭', $options['close_note']);
}

// 身份验证
$sax_uid = 0;
$sax_group = 4;
$sax_user = $sax_pw = '';
$hash = addslashes($_GET['hash'] ? $_GET['hash'] : $_POST['hash']);

if ($hash) {
	$session = $DB->fetch_one_array("SELECT * FROM {$db_prefix}sessions WHERE hash='$hash' AND lastactivity+3600>'$timestamp'");
	$user    = $DB->fetch_one_array("SELECT username,logincount,groupid,password FROM {$db_prefix}users WHERE userid='".$session['uid']."'");
	if(getuserhash($session['uid'],$user['username'],$user['password'],$user['logincount']) == $hash && $session['agent'] == md5(addslashes($_SERVER['HTTP_USER_AGENT'])) && $session['groupid'] == $user['groupid']) {
		$sax_uid = $session['uid'];
		$sax_group = $user['groupid'];
		$sax_user = $user['username'];
		$sax_pw = $user['password'];
		$logincount = $user['logincount'];
		$DB->query("UPDATE {$db_prefix}sessions SET lastactivity='$timestamp' WHERE uid='$sax_uid' AND hash='$hash'");
	} else {
		$DB->query("DELETE FROM {$db_prefix}sessions WHERE lastactivity+3600<'$timestamp' OR hash='$hash'");
	}
}
// 身份验证结束

if ($action == 'logout') {
	$DB->query("DELETE FROM {$db_prefix}sessions WHERE hash='$hash' OR lastactivity+3600<'$timestamp'");
	$hash = '';
	$sax_uid = 0;
	wap_header('注销身份');
	wap_message('注销成功', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
}

// 首页
if (!$action || $action == 'index') {
	wap_header($options['name']);
	echo "<p>\n";
	echo "<a href=\"index.php?action=list\">浏览日志</a><br />\n";
	echo "<a href=\"index.php?action=tagslist\">标签列表</a><br />\n";
	echo "<a href=\"index.php?action=categories\">日志分类</a><br />\n";
	echo "<a href=\"index.php?action=archives\">日志归档</a><br />\n";
	echo "<a href=\"index.php?action=search\">搜索引擎</a><br />\n";
	echo "<a href=\"index.php?action=comments\">最新评论</a><br />\n";
	if ($options['enable_trackback']) {
		echo "<a href=\"index.php?action=trackbacks\">引用列表</a><br />\n";
	}
	echo "<a href=\"index.php?action=users\">博客主人</a><br />\n";
	echo "<a href=\"index.php?action=statistics\">博客信息</a><br />\n";
	echo "</p>\n";
	if ($sax_uid) {
		echo "<p>您好:".$sax_user." <a href=\"index.php?action=logout\">注销</a>";
		if ($sax_group == 1 || $sax_group == 2) {
			echo "<br /><a href=\"index.php?action=add\">添加文章</a>";
		}
		echo "</p>\n";
	} else {
		echo "<p><a href=\"index.php?action=login\">用户登陆</a>(只有登陆后才能发表评论)</p>\n";
	}
	wap_footer();
}


// 文章列表
if (in_array($action, array('list', 'finduser', 'tags', 'dosearch'))) {
	$pagenum = $options['wap_article_pagenum'];
	if($page) {
		$start_limit = ($page - 1) * $pagenum;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	//定义相同的查询语句前部分
	$query_sql = "SELECT a.articleid,a.title,a.dateline FROM {$db_prefix}articles a WHERE a.visible='1'";
	// 查看用户发表的文章
	if ($action == 'finduser') {
		$userid = intval($_GET['userid']);
		$user  = $DB->fetch_one_array("SELECT username,articles FROM {$db_prefix}users WHERE userid='$userid'");
		$tatol = $user['articles'];
		$catename = '查看'.$user['username'].'的文章';
		$query_sql .= " AND a.uid='$userid' ORDER BY a.$article_order DESC LIMIT $start_limit, ".$pagenum;
		$pageurl = 'index.php?action=finduser&amp;userid='.$userid;
	// 查看tags的相关文章
	} elseif ($action == 'tags') {
		$item = addslashes($_GET['item']);
		if ($item) {
			$tag = $DB->fetch_one_array("SELECT usenum,aids FROM {$db_prefix}tags WHERE tag='$item'");
			if (!$tag) {
				wap_header('系统消息');
				wap_message('记录不存在', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
			}
			$tatol = $tag['usenum'];
			$query_sql .= " AND a.articleid IN (".$tag['aids'].") ORDER BY a.$article_order DESC LIMIT $start_limit, ".$pagenum;
			$pageurl = 'index.php?action=tags&amp;item='.urlencode($item);
			$catename = 'Tag:'.htmlspecialchars($item);
		} else {
			wap_header('系统消息');
			wap_message('缺少参数', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
		}
	// 查看搜索结果的文章
	} elseif ($action == 'dosearch') {
		$searchid = intval($_GET['searchid']);
		if (!$searchid){
			wap_message('您指定的搜索不存在或已过期,请返回.', array('title' => '重新搜索', 'link' => 'index.php?action=search'));
		} else {
			$search = $DB->fetch_one_array("SELECT * FROM {$db_prefix}searchindex WHERE searchid='$searchid'");
			if (!$search || $search['searchfrom'] != 'article') {
				wap_message('您指定的搜索不存在或已过期,请返回.', array('title' => '重新搜索', 'link' => 'index.php?action=search'));
			}
			$tatol = $search['tatols'];
			$query_sql .= " AND a.articleid IN (".$search['ids'].") ORDER BY a.".$search['sortby']." ".$search['orderby']." LIMIT $start_limit, ".$pagenum;
			$pageurl = 'index.php?action=dosearch&amp;searchid='.$searchid;
			$catename = '搜索:'.$search['keywords'];
		}
	// 查看首页文章
	} else {
		$catename = '全部文章';
		$tatol = $stats['article_count'];
		// 检查是否设置$cid参数
		$cateadd = '';
		$cid = intval($_GET['cid']);
		if ($cid) {			
			$cateadd = " AND a.cid='$cid' ";
			$query_sql .= " AND a.cid='$cid' ";
			$r = $DB->fetch_one_array("SELECT name,articles FROM {$db_prefix}categories WHERE cid='$cid'");
			$catename = '分类:'.$r['name'];
			$tatol = $r['articles'];
		}
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
				$catename = $setyear.'年'.$setmonth.'月的文章';
			}
		} else {
			$setyear = sadate('Y');
			$setmonth = sadate('m');
			$start = $end = 0;
		}
		//*******************************//
		$startadd = $start ? " AND a.dateline >= '".correcttime($start)."' " : '';
		$endadd   = $end ? " AND a.dateline < '".correcttime($end)."' " : '';
		//*******************************//
		if($setdate) {
			$query = $DB->query("SELECT COUNT(*) FROM {$db_prefix}articles a WHERE a.visible='1' ".$cateadd.$startadd.$endadd);
			$tatol = $DB->result($query, 0);
		}
		//*******************************//
		$query_sql .= $startadd.$endadd." ORDER BY a.stick DESC, a.$article_order DESC LIMIT $start_limit, ".$pagenum;
		$pageurl = 'index.php?action=list&amp;cid='.$cid.'&amp;setdate='.$setdate;
	}
	// 执行查询

	wap_header($catename);
	if ($tatol) {
		$query = $DB->query($query_sql);
		$multipage = multi($tatol, $pagenum, $page, $pageurl);
		echo "<p>\n";
		while ($article = $DB->fetch_array($query)) {
			echo "<a href=\"index.php?action=show&amp;id=".$article['articleid']."\">".trimmed_title($article['title'], $options['wap_article_title_limit'])."</a> (".sadate('m-d',$article['dateline']).")<br />\n";
		}
		$DB->free_result($query);
		echo "</p>\n";
		echo "<p>".$tatol."篇文章</p>\n";
		echo $multipage;
	} else {
		echo "<p>没有任何日志</p>\n";
	}
	echo "<p><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

// 标签列表
if ($action == 'tagslist') {
	$pagenum = $options['wap_tags_pagenum'];
	if($page) {
		$start_limit = ($page - 1) * $pagenum;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$order = in_array($_GET['order'], array('tagid', 'usenum')) ? $_GET['order'] : 'tagid';
	$multipage = multi($stats['tag_count'], $pagenum, $page, 'index.php?action=tagslist&amp;order='.$order);
	wap_header("标签列表");
	if ($stats['tag_count']) {
		if ($order == 'usenum') {
			echo "<p><a href=\"index.php?action=tagslist&amp;order=tagid\">查看最新标签</a></p>\n";
		} else {
			echo "<p><a href=\"index.php?action=tagslist&amp;order=usenum\">查看热门标签</a></p>\n";
		}
		echo "<p>\n";
		$query = $DB->query("SELECT tag,usenum FROM {$db_prefix}tags ORDER BY $order DESC LIMIT $start_limit, ".$pagenum);
		while ($tag = $DB->fetch_array($query)) {
			echo "<a href=\"index.php?action=tags&amp;item=".urlencode($tag['tag'])."\">".htmlspecialchars($tag['tag'])."</a> (".$tag['usenum'].")<br />\n";
		}
		$DB->free_result($query);
		echo "</p>\n";
		echo "<p>共有".$stats['tag_count']."个标签</p>\n";
		echo $multipage;
	} else {
		echo "<p>没有任何标签</p>\n";
	}
	echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

// 博客主人
if ($action == 'users') {
	$tatol = $DB->num_rows($DB->query("SELECT userid FROM {$db_prefix}users WHERE groupid='1' OR groupid='2'"));
	wap_header('博客主人');
	if ($tatol) {
		echo "<p>\n";
		$query = $DB->query("SELECT userid,username,articles FROM {$db_prefix}users WHERE groupid='1' OR groupid='2' ORDER BY articles DESC");
		while ($user = $DB->fetch_array($query)) {
			echo "<a href=\"index.php?action=showuser&amp;userid=".$user['userid']."\">".$user['username']."</a> (文章:".$user['articles'].")<br />\n";
		}
		$DB->free_result($query);
		echo "</p>\n";
		echo "<p>共有".$tatol."个主人</p>\n";
	} else {
		echo "<p>没有任何主人</p>\n";
	}
	echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}
// 博客主人
if ($action == 'showuser') {
	wap_header("博客主人");
	$userid = intval($_GET['userid']);
	// 获取文章信息	
	if (!$userid) {
		wap_message('缺少参数', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
	} else {
		$user = $DB->fetch_one_array("SELECT userid,username,url,articles FROM {$db_prefix}users WHERE userid='".$userid."'");
		if (!$user) {
			wap_message('记录不存在', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
		}
	}
	$user['articles'] = $user['articles'] ? $user['articles'] : '从未发表';
	
	echo "<p><a href=\"index.php?action=finduser&amp;userid=".$userid."\">查看".$user['username']."的文章</a></p>\n";
	echo "<p>\n";
	echo "名字:".$user['username']."<br />\n";
	echo "发表文章:".$user['articles']."篇<br />\n";
	echo $user['url']."</p>\n";
	echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

// 日志分类
if ($action == 'categories') {
	wap_header('日志分类');
	echo "<p>\n";
	if (empty($catecache)) {
		echo '没有任何分类';
	} else {
		$pagenum = 10;
		if($page) {
			$start_limit = ($page - 1) * $pagenum;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$multipage = multi($stats['cate_count'], $pagenum, $page, 'index.php?action=categories');
		$catecache = @array_slice($catecache,$start_limit,$pagenum);
		foreach($catecache AS $data){
			echo "<a href=\"index.php?action=list&amp;cid=".$data['cid']."\">".$data['name']."</a> (".$data['articles'].")<br />\n";
		}
	}
	echo "</p>\n";
	echo $multipage;
	echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

// 日志归档
if ($action == 'archives') {
	wap_header('日志归档');
	echo "<p>\n";
	if (empty($archivecache)) {
		echo '没有任何归档';
	} else {
		$monthname = array('','一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月');
		$pagenum = 10;
		if($page) {
			$start_limit = ($page - 1) * $pagenum;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$multipage = multi(count($archivecache), $pagenum, $page, 'index.php?action=archives');
		$archivecache = @array_slice($archivecache,$start_limit,$pagenum);
		foreach($archivecache AS $key => $val){
			$v = explode('-', $key);
			$e_month = ($v[1] < 10) ? str_replace('0', '', $v[1]) : $v[1];
			echo "<a href=\"index.php?action=list&amp;setdate=".$v[0].$v[1]."\">".$monthname[$e_month].", ".$v[0]."</a> (".$val.")<br />\n";
		}
	}
	echo "</p>\n";
	echo $multipage;
	echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

// 搜索引擎
if ($action == 'search') {
	wap_header('搜索引擎');
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	if (!$keywords || strlen($keywords) < $options['search_keywords_min_len']) {
		echo "<p>注意:只搜索文章标题和内容,不对评论进行搜索.关键字不能少于".$options['search_keywords_min_len']."个字节.</p>\n";
		echo "<p>关键字中可使用通配符 &quot;*&quot;<br />匹配多个关键字全部, 可用空格或 &quot;AND&quot; 连接. 如: angel AND 4ngel<br />匹配多个关键字其中部分, 可用 &quot;|&quot; 或 &quot;OR&quot; 连接. 如: angel OR 4ngel</p>";
		echo "<p>关键字:<input name=\"keywords\" type=\"text\" /></p>\n";
		echo "<p><anchor title=\"submit\">确定\n";
		echo "<go href=\"index.php?action=search&amp;hash=".$hash."\" method=\"post\">\n";
		echo "<postfield name=\"keywords\" value=\"$(keywords)\" />\n";
		echo "</go>\n";
		echo "</anchor>\n";
		echo "</p>\n";
	} else {
		$searchindex = array('id' => 0, 'dateline' => '0');
		$query = $DB->query("SELECT searchid, dateline,	('".$options['search_post_space']."'<>'0' AND $timestamp-dateline<".$options['search_post_space'].") AS flood, searchfrom='article' AND  keywords='$keywords' AS indexvalid FROM {$db_prefix}searchindex WHERE ('".$options['search_post_space']."'<>'0' AND ipaddress='$onlineip' AND $timestamp-dateline<".$options['search_post_space'].") ORDER BY flood");
		while($index = $DB->fetch_array($query)) {
			if($index['indexvalid'] && $index['dateline'] > $searchindex['dateline']) {
				$searchindex = array('id' => $index['searchid'], 'dateline' => $index['dateline']);
				break;
			} elseif($index['flood']) {
				wap_message('对不起,您在 '.$options['search_post_space'].' 秒内只能进行一次搜索.', array('title' => '重新搜索', 'link' => 'index.php?action=search'));
			}
		}
		if($searchindex['id']) {
			$searchid = $searchindex['id'];
		} else {
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
			foreach(explode('+', $keywords) AS $text) {
				$text = trim($text);
				if($text) {
					$sqltxtsrch .= $andor;
					$sqltxtsrch .= "(content LIKE '%".str_replace('_', '\_', $text)."%' OR title LIKE '%".$text."%')";
				}
			}
			//搜索文章
			$tatols = $ids = 0;
			$query = $DB->query("SELECT articleid FROM {$db_prefix}articles WHERE visible='1' AND ($sqltxtsrch) ORDER BY dateline desc");
			while($article = $DB->fetch_array($query)) {
				$ids .= ','.$article['articleid'];
				$tatols++;
			}
			$DB->free_result($query);
			$DB->query("INSERT INTO {$db_prefix}searchindex (keywords, dateline, sortby, orderby, tatols, ids, searchfrom, ipaddress) VALUES ('".char_cv($keywords)."', '$timestamp', 'dateline', 'desc', '$tatols', '$ids', 'article', '$onlineip')");
			$searchid = $DB->insert_id();
		}
		wap_message('搜索成功完成', array('title' => '查看搜索结果', 'link' => 'index.php?action=dosearch&amp;searchid='.$searchid));
	}
	echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

// 最新评论
if ($action == 'comments') {
	$articleid = intval($_GET['articleid']);
	$query_sql = "SELECT c.articleid,c.author,c.commentid,c.dateline,c.content, a.title FROM {$db_prefix}comments c LEFT JOIN {$db_prefix}articles a ON (a.articleid=c.articleid) WHERE a.visible='1' AND c.visible='1'";
	if ($articleid) {
		$query_sql .= " AND c.articleid='$articleid'";
		$article = $DB->fetch_one_array("SELECT title,comments FROM {$db_prefix}articles WHERE articleid='$articleid'");
		$tatol = $article['comments'];
		$pageurl = 'index.php?action=comments&amp;articleid='.$articleid;
	} else {
		$tatol = $stats['comment_count'];
		$pageurl = 'index.php?action=comments';
	}
	wap_header('最新评论');
	if ($tatol) {
		$pagenum = $options['wap_comment_pagenum'];
		if($page) {
			$start_limit = ($page - 1) * $pagenum;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$query_sql .= " ORDER BY commentid DESC LIMIT $start_limit, $pagenum";
		$multipage = multi($tatol, $pagenum, $page, $pageurl);
		$query = $DB->query($query_sql);
		
		if ($articleid) {
			echo "<p>\"<a href=\"index.php?action=show&amp;id=".$articleid."\">".$article['title']."</a>\"的评论</p>\n";
		}
		while ($comment=$DB->fetch_array($query)) {
			echo "<p>\n";
			if (!$articleid) {
				echo "文章:<a href=\"index.php?action=show&amp;id=".$comment['articleid']."\">".$comment['title']."</a><br />\n";
			}
			echo '作者:'.$comment['author'];			
			if ($sax_group == 1) {
				echo "[<a href=\"index.php?action=editcomment&amp;commentid=".$comment['commentid']."\">编辑</a>]<br />\n";
			} else {
				echo '<br />';
			}
			echo "时间:".sadate('Y-m-d H:i',$comment['dateline'])."<br />\n";
			echo "内容:".html_clean($comment['content'])."\n";
			echo "</p>\n";
		}
		unset($comment);
		echo "<p>共有".$tatol."条评论</p>\n";
		echo $multipage;
		$DB->free_result($query);
	} else {
		echo "<p>没有任何评论</p>\n";
	}
	echo "<p>";	
	if ($articleid) {
		if (!$sax_uid || !$hash) {
			echo "<a href=\"index.php?action=login\">立即登陆发表评论</a><br />\n";
		} else {
			echo "<a href=\"index.php?action=addcomment&amp;articleid=".$articleid."\">发表评论</a><br />\n";
		}
		echo "<a href=\"index.php?action=show&amp;id=".$articleid."\">返回文章</a><br />";
	}
	echo "<a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

// 引用列表
if ($action == 'trackbacks' && $options['enable_trackback']) {
	$articleid = intval($_GET['articleid']);
	if ($articleid) {
		$article = $DB->fetch_one_array("SELECT title,trackbacks FROM {$db_prefix}articles WHERE visible='1' AND articleid='".$articleid."'");
		$tatol = $article['trackbacks'];
		$pageurl = 'index.php?action=trackbacks&amp;articleid='.$articleid;
		$add_query = "AND t.articleid='$articleid'";
	} else {
		$tatol = $stats['trackback_count'];
		$pageurl = 'index.php?action=trackbacks';
		$add_query = '';
	}
	wap_header('最新引用');
	if ($tatol) {
		$pagenum = $options['wap_trackback_pagenum'];
		if($page) {
			$start_limit = ($page - 1) * $pagenum;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$multipage = multi($tatol, $pagenum, $page, $pageurl);
		$query = $DB->query("SELECT t.trackbackid,t.title,t.dateline,t.url,t.blog_name,a.title as article FROM {$db_prefix}trackbacks t LEFT JOIN {$db_prefix}articles a ON (a.articleid=t.articleid) WHERE a.visible='1' AND t.visible='1' ".$add_query." ORDER BY trackbackid DESC LIMIT ".$start_limit.", ".$pagenum);
		if ($articleid) {
			echo "<p>\"<a href=\"index.php?action=show&amp;id=".$articleid."\">".$article['title']."</a>\"的引用</p>\n";
		}
		while ($trackback = $DB->fetch_array($query)) {
			echo "<p>\n";
			echo "标题:<a href=\"".cvurl($trackback['url'])."\">".$trackback['title']."</a><br />\n";
			echo "来自:<a href=\"".cvurl($trackback['url'])."\">".$trackback['blog_name']."</a><br />\n";
			echo "时间:".sadate('Y-m-d H:i',$trackback['dateline']);
			echo "</p>\n";
		}
		unset($trackback);
		$DB->free_result($query);
		echo "<p>共有".$tatol."条引用</p>\n";
		echo $multipage;
	} else {
		echo "<p>没有任何引用</p>";
	}
	echo "<p>";	
	if ($articleid) {
		echo "<a href=\"index.php?action=show&amp;id=".$articleid."\">返回文章</a><br />";
	}
	echo "<a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

// 博客信息
if ($action == 'statistics') {
	wap_header('博客信息');
	echo "<p>\n";
	echo "分类数量: ".$stats['cate_count']."<br />\n";
	echo "文章数量: ".$stats['article_count']."<br />\n";
	echo "评论数量: ".$stats['comment_count']."<br />\n";
	echo "标签数量: ".$stats['tag_count']."<br />\n";
	echo "附件数量: ".$stats['attachment_count']."<br />\n";
	if ($options['enable_trackback']) {
		echo "引用数量: ".$stats['trackback_count']."<br />\n";
	}
	echo "注册用户: ".$stats['user_count']."<br />\n";
	echo "今日访问: ".$stats['today_view_count']."<br />\n";
	echo "总访问量: ".$stats['all_view_count']."<br />\n";
	echo "</p>\n";
	echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

// 浏览日志
if ($action == 'show') {
	$articleid = intval($_GET['id']);
	// 获取文章信息	
	if (!$articleid) {
		wap_header('系统消息');
		wap_message('缺少参数', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
	} else {
		$article = $DB->fetch_one_array("SELECT a.articleid,a.cid,a.uid,a.title,a.content,a.keywords,a.dateline,a.views,a.comments,a.trackbacks,a.closecomment,a.readpassword,a.attachments,c.name as cname,u.username
			FROM {$db_prefix}articles a
			LEFT JOIN {$db_prefix}categories c ON c.cid=a.cid
			LEFT JOIN {$db_prefix}users u ON a.uid=u.userid
			WHERE a.visible='1' AND articleid='$articleid'");
		if (!$article) {
			wap_header('系统消息');
			wap_message('记录不存在', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
		}
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET views=views+1 WHERE articleid='$articleid'");
	}

	wap_header($article['title']);
	echo "<p>\n";
	echo "作者:<a href=\"index.php?action=showuser&amp;userid=".$article['uid']."\">".$article['username']."</a><br />";
	echo "时间:".sadate('Y-m-d H:i',$article['dateline'])."<br />";
	echo "分类:<a href=\"index.php?action=list&amp;cid=".$article['cid']."\">".$article['cname']."</a><br />";
	if ($article['readpassword']) {
		echo "</p>\n";
		echo "<p>本文需要输入密码才能浏览,请通过HTTP浏览器浏览.</p>\n";
	} else {
		if ($article['keywords']) {
			$tags = $tmark = '';
			$tagdb = explode(',', $article['keywords']);
			for($i=0; $i<count($tagdb); $i++) {
				$tagdb[$i] = trim($tagdb[$i]);
				$tags .= $tmark."<a href=\"index.php?action=tags&amp;item=".urlencode($tagdb[$i])."\">".htmlspecialchars($tagdb[$i])."</a>";
				$tmark = ', ';
			}
			echo "标签:".$tags."<br />";
		}
		//附件
		if ($article['attachments']) {
			$attachs = $tmark = '';
			$attachdb= unserialize(stripslashes_array($article['attachments']));
			if (is_array($attachdb)) {
				foreach ($attachdb AS $attach) {
					$a_path = '../'.$options['attachments_dir'].'/'.$attach['filepath'];
					if (file_exists($a_path)) {
						$attachs .= $tmark."<a href=\"index.php?action=downfile&amp;id=".$attach['attachmentid']."\">".$attach['filename']."</a>";
						$tmark = ', ';
					}
				}
			}
			if ($attachs) {
				echo '附件:'.$attachs.'<br />';
			}
		}
		$article['content'] = str_replace(array('[php]','[/php]'),'',$article['content']);

		echo "内容:".html2text($article['content'])."</p>";
		echo "<p>\n";
		if ($article['trackbacks'] && $options['enable_trackback']) {
			echo "<a href=\"index.php?action=trackbacks&amp;articleid=".$article['articleid']."\">查看引用</a><br />\n";
		}
		if ($article['comments']) {
			echo "<a href=\"index.php?action=comments&amp;articleid=".$article['articleid']."\">查看评论</a><br />\n";
		}
		if (!$article['closecomment']) {
			if (!$sax_uid || !$hash) {
				echo "<a href=\"index.php?action=login\">立即登陆发表评论</a><br />\n";
			} else {
				echo "<a href=\"index.php?action=addcomment&amp;articleid=".$article['articleid']."\">发表评论</a><br />\n";
			}
		} else {
			echo "本文因为某种原因此时不允许访客进行评论<br />\n";
		}
		echo "</p>\n";
	}
	echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br /><a href=\"index.php?action=index\">返回主页</a></p>\n";
	wap_footer();
}

//登陆
if ($action == 'login') {
	wap_header('用户登陆');
	if (!$do || $do != 'login') {
		echo "<p>用户:<input name=\"username\" type=\"text\"  format=\"M*m\"/></p>\n";
		echo "<p>密码:<input name=\"password\" type=\"password\"  format=\"M*m\"/></p>\n";
		echo "<p><anchor title=\"submit\">确定\n";
		echo "<go href=\"index.php?action=login&amp;hash=".$hash."\" method=\"post\">\n";
		echo "<postfield name=\"username\" value=\"$(username)\" />\n";
		echo "<postfield name=\"password\" value=\"$(password)\" />\n";
		echo "<postfield name=\"do\" value=\"login\" />\n";
		echo "</go></anchor>\n";
		echo "</p>\n";
		echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br />";
		echo "<a href=\"index.php?action=index\">返回主页</a></p>\n";
	} elseif ($do == 'login') {
		// 登陆验证
		$username = addslashes(trim($_POST['username'] ? $_POST['username'] : $_GET['username']));
		$password = md5($_POST['password'] ? $_POST['password'] : $_GET['password']);
		if (strlen($username) > 20) {
			wap_message('登陆失败', array('title' => '重新登陆', 'link' => 'index.php?action=login'));
		}
		$user = $DB->fetch_one_array("SELECT userid,username,logincount,groupid,password FROM {$db_prefix}users WHERE username='$username'");
		if ($user['userid'] && $user['password'] == $password) {
			$DB->unbuffered_query("UPDATE {$db_prefix}users SET logincount=logincount+1, logintime='$timestamp', loginip='$onlineip' WHERE userid='".$user['userid']."'");
			$hash = getuserhash($user['userid'], $user['username'], $user['password'], $user['logincount']+1);
			$DB->query("DELETE FROM {$db_prefix}sessions WHERE uid='".$user['userid']."' OR lastactivity+3600<'$timestamp' OR hash='$hash'");
			$DB->query("INSERT INTO {$db_prefix}sessions (hash,uid,groupid,ipaddress,agent,lastactivity) VALUES ('$hash', '".$user['userid']."', '".$user['groupid']."', '$onlineip', '".md5(addslashes($_SERVER['HTTP_USER_AGENT']))."', '$timestamp')");
			if ($user['groupid'] == 1 || $user['groupid'] == 2) {
				loginresult($username,'Succeed');
			}
			wap_message('登陆成功', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
		} else {
			$hash = '';
			$DB->query("DELETE FROM {$db_prefix}sessions WHERE lastactivity+3600<'$timestamp'");
			if ($user['groupid'] == 1 || $user['groupid'] == 2 || $sax_group == 1 || $sax_group == 2) {
				loginresult($username,'Failed');
			}
			wap_message('登陆失败', array('title' => '重新登陆', 'link' => 'index.php?action=login'));
		}
	}
	wap_footer();
}

// 添加评论
if ($action == 'addcomment') {
	wap_header('添加评论');
	$articleid = intval($articleid);
	if (!$articleid) {
		wap_message('缺少必要参数', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
	} else {
		$article = $DB->fetch_one_array("SELECT title, cid, closecomment FROM {$db_prefix}articles WHERE articleid='$articleid'");
		if ($article['closecomment']) {
			wap_message('本文因为某种原因此时不允许访客进行评论', array('title' => '返回日志列表', 'link' => 'index.php?action=list'));
		}
	}
	if($do == 'addcomment') {
		if ($options['banip_enable']) {
			$options['ban_ip'] = str_replace('，', ',', $options['ban_ip']);
			$ban_ips = explode(',', $options['ban_ip']);
			if (is_array($ban_ips) && count($ban_ips)) {
				foreach ($ban_ips AS $ban_ip) {
					$ban_ip = str_replace( '\*', '.*', preg_quote($ban_ip, "/") );
					if (preg_match("/^$ban_ip/", $onlineip)) {
						wap_message('您的IP已经被系统禁止发表评论.');
					}
				}
			}
		}			
		//如果没有登陆
		if (!$sax_uid || !$hash) {
			wap_message('只有登陆后才能发表评论.', array('title' => '立即登陆', 'link' => 'index.php?action=login'));
		} else {
			//如果已登陆
			$user = $DB->fetch_one_array("SELECT userid,username,logincount,groupid,password,url FROM {$db_prefix}users WHERE userid='$sax_uid'");
			if ($user['userid'] && $user['password'] == $sax_pw && getuserhash($user['userid'],$user['username'],$user['password'],$user['logincount']) == $hash && $user['logincount'] == $logincount && $user['groupid'] == $sax_group) {
				$username = addslashes($user['username']);
				$url = addslashes($user['url']);
			} else {			
				$DB->query("DELETE FROM {$db_prefix}sessions WHERE lastactivity+3600<'$timestamp' OR hash='$hash'");
				wap_message('读取用户信息出错,请重新登陆.', array('title' => '重新发表', 'link' => 'index.php?action=addcomment&amp;articleid='.$articleid));
			}
		}
		$content = addslashes(trim($_POST['content'] ? $_POST['content'] : $_GET['content']))." \n\n<自 WAP 发表>";
			
		// 检查限制选项
		if ($options['audit_comment']) {
			$spam = TRUE;
			//禁止IP		
		} elseif ($options['banip_enable']) {
			$options['ban_ip'] = str_replace('，', ',', $options['ban_ip']);
			$ban_ips = explode(',', $options['ban_ip']);
			if (is_array($ban_ips) && count($ban_ips)) {
				foreach ($ban_ips AS $ban_ip) {
					$ban_ip = str_replace( '\*', '.*', preg_quote($ban_ip, "/") );
					if (preg_match("/^$ban_ip/", $onlineip)) {
						$spam = TRUE;
						break;
					}
				}
			}
		} elseif ($options['spam_enable']) {
			//链接次数
			if (substr_count($content, 'http://') >= $options['spam_url_num']) {
				$spam = TRUE;
			}
			//禁止词语
			if ($options['spam_words']) {
				$options['spam_words'] = str_replace('，', ',', $options['spam_words']);
				$badwords = explode(',', $options['spam_words']);
				if (is_array($badwords) && count($badwords) ) {
					foreach ($badwords AS $n) {
						if ($n) {
							if (preg_match( "/".preg_quote($n, '/' )."/i", $content)) {
								$spam = TRUE;
								break;
							}
						}
					}
				}
			}
			//内容长度
			if (strlen($content) >= $options['spam_content_size']) {
				$spam = TRUE;
			}
		} else {
			$spam = FALSE;
		}

		$visible = $spam ? '0' : '1';
		if ($options['comment_post_space'] && $timestamp - $user['lastpost'] <= $options['comment_post_space']){
			wap_message('为防止灌水,发表评论时间间隔为'.$options['comment_post_space'].'秒.', array('title' => '重新发表', 'link' => 'index.php?action=addcomment&amp;articleid='.$articleid));
		}
		$result  = '';
		$result .= checkurl($url);
		$result .= checkcontent($content);
		if($result){
			wap_message($result, array('title' => '重新发表', 'link' => 'index.php?action=addcomment&amp;articleid='.$articleid));
		}
		$username = char_cv($username);
		$r = $DB->fetch_one_array("SELECT commentid FROM {$db_prefix}comments WHERE articleid='$articleid' AND author='$username' AND content='$content'");
		if($r['commentid']) {
			wap_message('该评论已存在', array('title' => '重新发表', 'link' => 'index.php?action=addcomment&amp;articleid='.$articleid));
		}
		unset($r);
		$msg = '添加评论成功, '.($spam ? '目前发表评论需要管理员审核才会显示,请耐心等待管理员审核...' : '返回即可看到您所发表的评论');
		$DB->query("INSERT INTO {$db_prefix}comments (articleid, author, url, dateline, content, ipaddress, visible) VALUES ('$articleid', '$username', '$url', '$timestamp', '$content', '$onlineip', '$visible')");
		$cmid = $DB->insert_id();
		if ($sax_uid && $hash) {
			$DB->unbuffered_query("UPDATE {$db_prefix}users SET lastpost='$timestamp' WHERE userid='$sax_uid'");
		}
		if (!$spam) {
			// 更新当前文章评论数
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments=comments+1 WHERE articleid='$articleid'");
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET comment_count=comment_count+1");
			require_once(SABLOG_ROOT.'include/cache.php');
			newcomments_recache();
			statistics_recache();
		}
		wap_message($msg, array('title' => '查看评论', 'link' => 'index.php?action=comments&amp;articleid='.$articleid));
	} else {
		echo "<p>文章:<a href=\"index.php?action=show&amp;id=".$articleid."\">".$article['title']."</a></p>\n";
		if ($sax_uid && $hash) {
			echo "<p>已经登陆为:".$sax_user." <a href=\"index.php?action=logout\">注销</a></p>\n";
			echo "<p>评论内容(*):<input name=\"content\" type=\"text\" /></p>\n";
			echo "<p><anchor title=\"submit\">确定\n";
			echo "<go href=\"index.php?action=addcomment&amp;hash=".$hash."\" method=\"post\">\n";
			echo "<postfield name=\"content\" value=\"$(content)\" />\n";
			echo "<postfield name=\"articleid\" value=\"".$articleid."\" />\n";
			echo "<postfield name=\"do\" value=\"addcomment\" />\n";
			echo "</go></anchor>\n";
			echo "</p>\n";
		} else {
			echo "<p>只有登陆后才能发表评论</p>\n";
			echo "<p><a href=\"index.php?action=login\">立即登陆</a></p>";
		}
		echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br />";
		echo "<a href=\"index.php?action=index\">返回主页</a></p>\n";
	}
	wap_footer();
}

// 添加文章
if ($action == 'add') {
	wap_header("添加文章");
	if ($sax_group == 1 || $sax_group == 2 && $hash) {
		// 添加文章
		if($do == 'add') {
			$title   = trim($_POST['title'] ? $_POST['title'] : $_GET['title']);
			$content = addslashes($_POST['content'] ? $_POST['content'] : $_GET['content']);
			$cid     = intval($cid);
			if($title == '' || strlen($title) > 120) {
				wap_message('标题不能为空并且不能多于120个字节', array('title' => '重新发表', 'link' => 'index.php?action=addarticle'));
			}
			if(!$cid) {
				wap_message('你还没有选择分类', array('title' => '重新发表', 'link' => 'index.php?action=addarticle'));
			}
			if(!$content) {
				wap_message('内容不能为空', array('title' => '重新发表', 'link' => 'index.php?action=addarticle'));
			}
			$title = char_cv($title);
			$r = $DB->num_rows($DB->query("SELECT articleid FROM {$db_prefix}articles WHERE title='$title' and cid='$cid' and content='$content'"));
			if($r) {
				wap_message('数据库中已存在相同的数据', array('title' => '重新发表', 'link' => 'index.php?action=addarticle'));
			}
			// 插入数据部分
			$DB->query("INSERT INTO {$db_prefix}articles (cid, uid, title, content, dateline) VALUES ('$cid', '$sax_uid', '$title', '$content <br /><br /><span style=\"font-weight:bold;color:#4685C4;background-color:#E9F1F8;\">自 WAP 发表</span>', '$timestamp')");
			$articleid = $DB->insert_id();
			$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles=articles+1 WHERE userid='$sax_uid'");
			$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles+1 WHERE cid='$cid'");
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count+1");
			require_once(SABLOG_ROOT.'include/cache.php');
			archives_recache();
			categories_recache();
			statistics_recache();
			getlog();
			wap_message('添加文章成功', array('title' => '查看文章', 'link' => 'index.php?action=show&amp;id='.$articleid));
		} else {
			echo "<p>标题:<input name=\"title\" type=\"text\" format=\"M*m\" /></p>\n";
			echo "<p>分类:<select name=\"cid\">\n";
			echo "<option value=\"\">"."选择分类"."</option>\n";
			$query = $DB->query("SELECT * FROM {$db_prefix}categories ORDER BY displayorder");
			while ($cate = $DB->fetch_array($query)) { 
				echo "<option value=\"".$cate['cid']."\">".$cate['name']."</option>\n";
			}
			echo "</select></p>\n";
			echo "<p>内容:<input name=\"content\" type=\"text\" format=\"M*m\" /></p>\n";
			echo "<p><anchor title=\"submit\">确定\n";
			echo "<go href=\"index.php?action=add&amp;hash=".$hash."\" method=\"post\">\n";
			echo "<postfield name=\"title\" value=\"$(title)\" />\n";
			echo "<postfield name=\"cid\" value=\"$(cid)\" />\n";
			echo "<postfield name=\"content\" value=\"$(content)\" />\n";
			echo "<postfield name=\"do\" value=\"add\" />\n";
			echo "</go></anchor>\n";
			echo "</p>\n";
			echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br />";
			echo "<a href=\"index.php?action=index\">返回主页</a></p>\n";
		}
	} else {
		wap_message('你没有权限进行此操作');
	}
	wap_footer();
}

// 管理评论
if ($action == 'editcomment') {
	wap_header("管理评论");
	if ($sax_group == 1 && $hash) {
		$commentid = intval($commentid);
		// 获取文章信息	
		if (!$commentid) {
			wap_message('缺少参数');
		} else {
			$comment = $DB->fetch_one_array("SELECT author,articleid,content FROM {$db_prefix}comments WHERE commentid='$commentid'");
			if (!$comment) {
				wap_message('记录不存在');
			}
		} 
		if($act == 'edit') {
			$do = in_array($do, array('hidden', 'delete')) ? $do : 'hidden';
			if($do == 'hidden') {
				$DB->query("UPDATE {$db_prefix}comments SET visible='0' WHERE commentid='$commentid'");
				$msg = '评论已隐藏';
			} else {
				$DB->query("DELETE FROM {$db_prefix}comments WHERE commentid='$commentid'");
				$msg = '评论已删除';
			}
			$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments=comments-1 WHERE articleid='".$comment['articleid']."'");
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET comment_count=comment_count-1");
			require_once(SABLOG_ROOT.'include/cache.php');
			newcomments_recache();
			statistics_recache();
			getlog();
			wap_message($msg, array('title' => '返回评论列表', 'link' => "index.php?action=comments&amp;articleid=".$comment['articleid']));
		} else {
			echo "<p>\n";
			echo "作者:".$comment['author']."<br />\n";
			echo "内容:".html_clean($comment['content'])."</p>\n";
			echo "<p>操作:<select name=\"do\">\n";
			echo "<option value=\"hidden\">隐藏</option>\n";
			echo "<option value=\"delete\">删除</option>\n";
			echo "</select></p>\n";
			echo "<p><anchor title=\"submit\">确定\n";
			echo "<go href=\"index.php?action=editcomment&amp;hash=".$hash."\" method=\"post\">\n";
			echo "<postfield name=\"commentid\" value=\"".$commentid."\" />\n";
			echo "<postfield name=\"do\" value=\"$(do)\" />\n";
			echo "<postfield name=\"act\" value=\"edit\" />\n";
			echo "</go></anchor>\n";
			echo "</p>\n";
			echo "<p><a href=\"index.php?action=list\">返回日志列表</a><br />";
			echo "<a href=\"index.php?action=index\">返回主页</a></p>\n";
		}
	} else {
		wap_message('你没有权限进行此操作');
	}
	wap_footer();
}
?>