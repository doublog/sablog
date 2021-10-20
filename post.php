<?php
// ========================== 文件说明 ==========================//
// 本文件说明：提交数据操作
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

require_once('global.php');

if($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || $_POST['formhash'] != formhash() || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) !== preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST']))) {
	message('您的请求来路不正确,无法提交');
}

//如果开启伪静态则设置伪静态的页面的URL
if ($options['rewrite_enable']) {
	$loginurl = 'login.'.$options['rewrite_ext'];
	$regurl = 'reg.'.$options['rewrite_ext'];
} else {
	$loginurl = './?action=login';
	$regurl = './?action=reg';
}

if($_POST['action'] == 'register' || $_POST['action'] == 'modpro') {
	// 取值并过滤部分
	$doreg = $_POST['action'] == 'register' ? true : false;

	$username        = trim($_POST['username']);
	$password        = $_POST['password'];
	$confirmpassword = $_POST['confirmpassword'];
	$url             = trim($_POST['url']);
	$result = checkurl($url);
	if($result) {
		message($result);
	}
	if ($doreg) {
		//注册
		if ($options['seccode_enable']) {
			$clientcode = $_POST['clientcode'];
			session_start();
			if (!$clientcode || strtolower($clientcode) != strtolower($_SESSION['code'])) {
				unset($_SESSION['code']);
				message('验证码错误,请返回重新输入.', $regurl);
			}
		}

		if(!$username || strlen($username) > 30) {
			message('用户名为空或者超过30字节.', $regurl);
		}

		if ($options['censoruser']) {
			$options['censoruser'] = str_replace('，', ',', $options['censoruser']);
			$banname = explode(',',$options['censoruser']);
			foreach($banname as $value){
				if (strpos($username,$value) !== false){
					message('此用户名包含不可接受字符或被管理员屏蔽,请选择其它用户名.', $regurl);
				}
			}
		}

		$name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
		foreach($name_key as $value){
			if (strpos($username,$value) !== false){
				message('此用户名包含不可接受字符或被管理员屏蔽,请选择其它用户名.', $regurl);
			}
		}

		if (!$password || strlen($password) < 8) {
			message('密码不能为空并且密码长度不能小于8位.',$regurl);
		}
		if ($password != $confirmpassword) {
			message('请确认输入的密码一致.', $regurl);
		}
		if (strpos($newpassword,"\n") !== false || strpos($password,"\r") !== false || strpos($password,"\t") !== false) {
			message('密码包含不可接受字符.', $regurl);
		}
		$username = char_cv($username);
		$r = $DB->fetch_one_array("SELECT userid FROM {$db_prefix}users WHERE username='$username'");
		if($r['userid']) {
			message('该用户名已被注册,请返回重新选择其他用户名.', $regurl);
			unset($r);
		}
		$url = char_cv($url);
		if ($url && isemail($url)) {
			$r = $DB->fetch_one_array("SELECT userid FROM {$db_prefix}users WHERE url='$url'");
			if($r['userid']) {
				message('该E-mail已被注册.', $regurl);
			}
			unset($r);
		}

		$password = md5($password);

		$DB->query("INSERT INTO {$db_prefix}users (username, password, logincount, loginip, logintime, url, regdateline, regip, groupid) VALUES ('$username', '$password', '1', '$onlineip', '$timestamp', '$url', '$timestamp', '$onlineip', '3')");
		$userid = $DB->insert_id();
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET user_count=user_count+1");
		setcookie('sax_auth', authcode("$userid\t$password\t1"), $timestamp+2592000);
		require_once(SABLOG_ROOT.'include/cache.php');
		statistics_recache();
		message('注册成功.', './');
	} else {
		//修改资料
		$password_sql = '';
		$oldpassword = md5($_POST['oldpassword']);
		$newpassword = $_POST['newpassword'];
		if ($newpassword) {
			$user = $DB->fetch_one_array("SELECT password FROM {$db_prefix}users WHERE userid='$sax_uid'");
			if (!$user) {
				message('出错,请尝试重新登陆再进行此操作');
			}
			if ($oldpassword != $user['password']) {
				message('密码无效');
			}
			if(strlen($newpassword) < 8) {
				message('新密码长度不能小于8位');
			}
			if ($newpassword != $confirmpassword) {
				message('请确认输入的新密码一致');
			}
			if (strpos($newpassword,"\n") !== false || strpos($newpassword,"\r") !== false || strpos($newpassword,"\t") !== false) {
				message('密码包含不可接受字符');
			}
			$password_sql = ", password='".md5($newpassword)."'";
		}
		$url = char_cv($url);
		if ($url && isemail($url)) {
			$r = $DB->fetch_one_array("SELECT userid FROM {$db_prefix}users WHERE url='$url' AND userid!='$sax_uid'");
			if($r['userid']) {
				message('该E-mail已被注册');
			}
			unset($r);
		}
		$DB->unbuffered_query("UPDATE {$db_prefix}users SET url='$url' $password_sql WHERE userid='$sax_uid'");
		if ($newpassword) {
			setcookie('sax_auth', '');
			setcookie('comment_post_time', '');
			setcookie('search_post_time', '');
			setcookie('comment_username', '');
			setcookie('comment_url', '');
			message('资料已修改成功,您修改了密码,需要重新登陆.', $loginurl);
		} else {
			message('资料已修改成功.', './?action=profile');
		}
	}
}

//登陆
if($_POST['action'] == 'dologin') {
	if ($options['seccode_enable']) {
		$clientcode = $_POST['clientcode'];
		session_start();
		if (!$clientcode || strtolower($clientcode) != strtolower($_SESSION['code'])) {
			unset($_SESSION['code']);
			message('验证码错误,请返回重新输入.', $loginurl);
		}
	}
	// 取值并过滤部分
	$username = char_cv(trim($_POST['username']));
	$password = md5($_POST['password']);
	$userinfo = $DB->fetch_one_array("SELECT userid,password,logincount,url,groupid FROM {$db_prefix}users WHERE username='$username'");
	if($userinfo['userid'] && $userinfo['password'] == $password) {
		$DB->unbuffered_query("UPDATE {$db_prefix}users SET logincount=logincount+1, logintime='$timestamp', loginip='$onlineip' WHERE userid='".$userinfo['userid']."'");
		$logincount = $userinfo['logincount']+1;
		setcookie('sax_auth', authcode("$userinfo[userid]\t$password\t$logincount"), $timestamp+2592000);
		message('登陆成功', './');
	} else {
		message('登陆失败', $loginurl);
	}
}

//注销
if ($_GET['action'] == 'logout') {
	setcookie('sax_auth', '');
	setcookie('comment_post_time', '');
	setcookie('search_post_time', '');
	setcookie('comment_username', '');
	setcookie('comment_url', '');
	message('注销成功', './');
}

//清除cookie
if ($_GET['action'] == 'clearcookies') {
	if(is_array($_COOKIE)) {
		foreach ($_COOKIE as $key => $val) {
			setcookie($key, '');
		}
	}
	message('清除COOKIE成功', './');
}

//添加评论
if($_POST['action'] == 'addcomment') {
	$cookietime = $timestamp+2592000;
	$articleid = intval($_POST['articleid']);
	$username = trim($_POST['username']);
	$password = $_POST['password'];
	$url = trim($_POST['url']);
	$content = addslashes(trim($_POST['content']));
	//把评论内容保存到cookie里以免丢失
	setcookie('cmcontent', $content, $cookietime);

	if (!$articleid) {
		message('缺少必要参数', './');
	}

	//禁止IP	
	if ($options['banip_enable'] && $options['ban_ip']) {
		$options['ban_ip'] = str_replace('，', ',', $options['ban_ip']);
		$ban_ips = explode(',', $options['ban_ip']);
		if (is_array($ban_ips) && count($ban_ips)) {
			foreach ($ban_ips AS $ban_ip) {
				$ban_ip = str_replace( '\*', '.*', preg_quote($ban_ip, "/") );
				if (preg_match("/^$ban_ip/", $onlineip)) {
					message('您的IP已经被系统禁止发表评论.', getarticleurl($articleid));
				}
			}
		}
	}

	if ($options['seccode'] && $sax_group != 1 && $sax_group !=2) {
		$clientcode = $_POST['clientcode'];
		session_start();
		if (!$clientcode || strtolower($clientcode) != strtolower($_SESSION['code'])) {
			unset($_SESSION['code']);
			message('验证码错误,请返回重新输入.', getarticleurl($articleid));
		}
	}

	//如果没有登陆
	if (!$sax_uid) {
		if(!$username || strlen($username) > 30) {
			message('用户名为空或用户名太长.', getarticleurl($articleid).'#addcomment');
		}
		$name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
		foreach($name_key as $value){
			if (strpos($username,$value) !== false){ 
				message('此用户名包含不可接受字符或被管理员屏蔽,请选择其它用户名.', getarticleurl($articleid).'#addcomment');
			}
		}
		$username = char_cv($username);
		//用户名和密码都非空
		if ($username && $password) {
			$user = $DB->fetch_one_array("SELECT userid,username,password,logincount,url FROM {$db_prefix}users WHERE username='$username'");
			$password = md5($_POST['password']);
			if($user['userid'] && $user['password'] == $password) {
				$DB->unbuffered_query("UPDATE {$db_prefix}users SET logincount=logincount+1, logintime='$timestamp', loginip='$onlineip' WHERE userid='".$user['userid']."'");
				$logincount = $user['logincount']+1;
				setcookie('sax_auth', authcode("$user[userid]\t$password\t$logincount"), $cookietime);
				//自动读取作者资料
				$sax_uid = $user['userid'];
				$username = addslashes($user['username']);
				$url = addslashes($user['url']);
			} else {
				message('验证失败,请登陆后再发表或重新输入正确的用户名和密码.', getarticleurl($articleid).'#addcomment');
			}
		//如果只有用户名没有密码
		} elseif ($username && !$password) {
			if ($options['censoruser']) {
				$options['censoruser'] = str_replace('，', ',', $options['censoruser']);
				$banname=explode(',',$options['censoruser']);				
				foreach($banname as $value){
					if (strpos($username,$value) !== false && !$DB->fetch_one_array("SELECT userid FROM {$db_prefix}users WHERE username='$username'")){
						message('此用户名包含不可接受字符或被管理员屏蔽.您不能使用这个用户名发表评论.', getarticleurl($articleid).'#addcomment');
					}
				}
			}
			$r = $DB->fetch_one_array("SELECT userid FROM {$db_prefix}users WHERE username='$username'");
			if($r['userid']) {
				message('该用户名已存在,如果是您注册的,请先登陆.', getarticleurl($articleid).'#addcomment');
			}
			unset($r);
			//把用户名和URL信息保存到cookie
			setcookie('comment_username',$username,$cookietime);
			setcookie('comment_url',$url,$cookietime);
			$url = char_cv($url);
		}
	} else {
		//如果已登陆
		$user = $DB->fetch_one_array("SELECT userid,username,logincount,groupid,password,url FROM {$db_prefix}users WHERE userid='$sax_uid'");
		if ($user['userid'] && $user['password'] == $sax_pw && $user['logincount'] == $logincount && $user['groupid'] == $sax_group) {
			$username = addslashes($user['username']);
			$url = addslashes($user['url']);
		} else {			
			message('读取用户信息出错,请重新登陆.', $loginurl);
		}
	}
	
	// 检查限制选项
	if ($options['audit_comment']) {
		$spam = TRUE;
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
	
	if ($sax_group != 1 && $sax_group != 2) {
		$lastposttime = $user['lastpost'] ? $user['lastpost'] : $_COOKIE['comment_post_time'];
		if ($options['comment_post_space'] && $timestamp - $lastposttime <= $options['comment_post_space'] && $sax_group != 1){
			message('为防止灌水,发表评论时间间隔为'.$options['comment_post_space'].'秒.', getarticleurl($articleid).'#addcomment');
		}
	}
	$article = $DB->fetch_one_array("SELECT closecomment FROM {$db_prefix}articles WHERE articleid='$articleid'");
	if ($article['closecomment']) {
		message('本文因为某种原因此时不允许访客进行评论.', getarticleurl($articleid));
	}
	$result  = '';
	$result .= checkurl($url);
	$result .= checkcontent($content);
	if($result){
		message($result, getarticleurl($articleid).'#addcomment');
	}
    $r = $DB->fetch_one_array("SELECT commentid FROM {$db_prefix}comments WHERE articleid='$articleid' AND author='$username' AND content='$content'");
    if($r['commentid']) {
		message('该评论已存在.', getarticleurl($articleid));
	}
	unset($r);
    $DB->query("INSERT INTO {$db_prefix}comments (articleid, author, url, dateline, content, ipaddress, visible) VALUES ('$articleid', '$username', '$url', '$timestamp', '$content', '$onlineip', '$visible')");
	$cmid = $DB->insert_id();
	if ($sax_uid) {
		$DB->unbuffered_query("UPDATE {$db_prefix}users SET lastpost='$timestamp' WHERE userid='$sax_uid'");
		// 更新用户最后发表时间
	}
	if (!$spam) {
		// 如果不是垃圾则更新当前文章评论数
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET comments=comments+1 WHERE articleid='$articleid'");
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET comment_count=comment_count+1");
		require_once(SABLOG_ROOT.'include/cache.php');
		newcomments_recache();
		statistics_recache();
	}
	setcookie('comment_post_time',$timestamp);
	// 跳转到最新发表的评论
	$cmnum = '#cm'.$cmid;
	$article_comment_num = intval($options['article_comment_num']);
	if ($article_comment_num) {
		$cpost = $DB->fetch_one_array("SELECT COUNT(*) as comment FROM {$db_prefix}comments WHERE articleid='$articleid' AND visible='1' AND commentid<='$cmid'");
		if (($cpost['comment'] / $article_comment_num) <= 1 ) {
			$page = 1;
		} else {
			$page = @ceil(($cpost['comment']) / $article_comment_num);
		}
	} else {
		$page = 1;
	}
	if ($spam) {
		message('添加评论成功,目前发表评论需要管理员审核才会显示,请耐心等待管理员审核.', getarticleurl($articleid));
	}

	setcookie('cmcontent','');

	if ($options['comment_order']) { //新评论靠后排序
		if ($options['showmsg']) {
			message('添加评论成功,返回即可看到您所发表的评论.', getarticleurl($articleid, $page).$cmnum);
		} else {
			@header('Location: '.getarticleurl($articleid, $page).$cmnum);
		}
	} else {
		if ($options['showmsg']) {
			message('添加评论成功,返回即可看到您所发表的评论.', getarticleurl($articleid).'#comment');
		} else {
			@header('Location: '.getarticleurl($articleid).'#comment');
		}
	}
}

//搜索
if ($_POST['action'] == 'search') {
	$searchfrom = in_array($_POST['searchfrom'], array('article', 'comment')) ? $_POST['searchfrom'] : 'article';
	if ($options['rewrite_enable']) {
		$searchurl = 'search.'.$options['rewrite_ext'];
	} else {
		$searchurl = './?action=search';
	}
	if ((!$options['allow_search_comments']) && $searchfrom == 'comment') {
		message('系统不允许在评论里执行搜索操作', $searchurl);
	}
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	if (!$keywords) {
		message('您没有指定要搜索的关键字.', $searchurl);
	} else {
		if(strlen($keywords) < $options['search_keywords_min_len']) {
			message('关键字不能少于'.$options['search_keywords_min_len'].'个字节.', $searchurl);
		}
		$searchindex = array('id' => 0, 'dateline' => '0');
		$query = $DB->query("SELECT searchid, dateline, ('".$options['search_post_space']."'<>'0' AND $timestamp-dateline<".$options['search_post_space'].") AS flood, searchfrom='$searchfrom' AND keywords='$keywords' AS indexvalid FROM {$db_prefix}searchindex WHERE ('".$options['search_post_space']."'<>'0' AND ipaddress='$onlineip' AND $timestamp-dateline<".$options['search_post_space'].") ORDER BY flood");
		while($index = $DB->fetch_array($query)) {
			if($index['indexvalid'] && $index['dateline'] > $searchindex['dateline']) {
				$searchindex = array('id' => $index['searchid'], 'dateline' => $index['dateline']);
				break;
			} elseif($index['flood']) {
				message('对不起,您在 '.$options['search_post_space'].' 秒内只能进行一次搜索.', $searchurl);
			}
		}
		if($searchindex['id']) {
			$searchid = $searchindex['id'];
			$gourl = './?action=search&amp;searchid='.$searchid;
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
			foreach(explode("+", $keywords) AS $text) {
				$text = trim($text);
				if($text) {
					$sqltxtsrch .= $andor;
					if ($searchfrom == "article") {
						$sqltxtsrch .= ($_POST['searchin'] == 'content') ? "(content LIKE '%".str_replace('_', '\_', $text)."%' OR description LIKE '%".$text."%' OR title LIKE '%".$text."%')" : "title LIKE '%".$text."%'";
					} else {
						$sqltxtsrch .= ($_POST['csearchin'] == 'author') ? "author LIKE '%".$text."%'" : "(content LIKE '%".str_replace('_', '\_', $text)."%' OR author LIKE '%".$text."%')";
					}
				}
			}
			//搜索文章
			if ($searchfrom == 'article') {
				$query_sql = "SELECT articleid FROM {$db_prefix}articles WHERE visible='1'";
				$sortby = in_array($_POST['sortby'], array('dateline', 'views', 'comments', 'cid')) ? $_POST['sortby'] : 'dateline';
				$orderby = $_POST['orderby'] == 'asc' ? 'asc' : 'desc';
				$cid = $_POST['cid'];
				//分类
				$catearray = array();
				if($cid) {
					foreach((is_array($cid) ? $cid : explode('_', $cid)) as $cateid) {
						if($cateid = intval(trim($cateid))) {
							$catearray[] = $cateid;
						}
					}
				}
				$cids = $comma = '';
				foreach($catecache as $data) {
					if(!$catearray || in_array($data['cid'], $catearray)) {
						$cids .= $comma.intval($data['cid']);
						$comma = ',';
					}
				}
				if ($cids) {
					$query_sql .= " AND cid IN ($cids)";
				}

				//归档日期
				$archivedb = array();
				foreach($archivecache as $key => $val){
					$key = str_replace('-', '', $key);
					$archivedb[] = $key;
				}
				if (in_array($_POST['dateline'], $archivedb)) {
					$dateline = intval($_POST['dateline']);
					$setyear = substr($dateline,0,4);
					$setmonth = substr($dateline,-2);
					list($start, $end) = explode('-', gettimestamp($setyear,$setmonth));
				}
				$startadd = $start ? " AND dateline >= '".correcttime($start)."' " : '';
				$endadd   = $end ? " AND dateline < '".correcttime($end)."' " : '';
				$query_sql .= $startadd.$endadd." AND ($sqltxtsrch) ORDER BY $sortby $orderby";

				$tatols = $ids = 0;
				$query = $DB->query($query_sql);
				while($article = $DB->fetch_array($query)) {
					$ids .= ','.$article['articleid'];
					$tatols++;
				}
				$searchfrom = 'article';
				$gourl = './?action=search&amp;searchid=';
			//搜索评论
			} else {
				$query_sql  = "SELECT commentid FROM {$db_prefix}comments WHERE visible='1'";
				$query_sql .= " AND ($sqltxtsrch) ORDER BY dateline DESC";
				$tatols = $ids = 0;
				$query = $DB->query($query_sql);
				while($article = $DB->fetch_array($query)) {
					$ids .= ','.$article['commentid'];
					$tatols++;
				}
				$searchfrom = 'comment';
				$gourl = './?action=comments&amp;searchid=';
			}
			$DB->free_result($query);
			$DB->query("INSERT INTO {$db_prefix}searchindex (keywords, dateline, sortby, orderby, tatols, ids, searchfrom, ipaddress) VALUES ('".char_cv($keywords)."', '$timestamp', '$sortby', '$orderby', '$tatols', '$ids', '$searchfrom', '$onlineip')");
			$searchid = $DB->insert_id();
			$gourl .= $searchid;
		}
		if ($options['showmsg']) {
			message('搜索成功完成,现在将转入结果页面.', $gourl);
		} else {
			$gourl = str_replace("&amp;", "&", $gourl);
			@header("Location: ".$gourl);
		}
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

?>