<?php
// ========================== 文件说明 ==========================//
// 本文件说明：用户管理
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

if (!$action) {
    $action = 'list';
}

$groupdb = array(
	'1' => '管理组',
	'2' => '撰写组',
	'3' => '注册组',
	'4' => '访客组',
);

//添加用户
if($action == 'adduser') {
	$username       = trim($_POST['username']);
	$newpassword    = trim($_POST['newpassword']);
	$comfirpassword = trim($_POST['comfirpassword']);
	$url            = trim($_POST['url']);
	$groupid        = intval($_POST['groupid']);

	if (!$username || strlen($username) > 20) {
		redirect('登陆名不能为空并且不能超过20个字符');
    }
	$name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
	foreach($name_key as $value){
		if (strpos($username,$value) !== false){
			redirect('用户名包含敏感字符');
		}
	}
    if ($newpassword == '' || strlen($newpassword) < 8) {
		redirect('密码不能为空并且密码长度不能小于8位');
	}
    if ($newpassword != $comfirpassword) {
        redirect('请确认输入的密码一致');
    }
	if (strpos($newpassword,"\n") !== false || strpos($password,"\r") !== false || strpos($password,"\t") !== false) {
		redirect('密码包含不可接受字符.');
	}
	$url = char_cv($url);
	if ($url) {
		if (isemail($url)) {
			$r = $DB->fetch_one_array("SELECT userid FROM {$db_prefix}users WHERE url='$url'");
			if($r['userid']) {
				redirect('该E-mail已被注册');
			}
			unset($r);
		} else {			
			if (!preg_match("#^(http|news|https|ftp|ed2k|rtsp|mms)://#", $url)) {
				redirect('网站URL错误');
			}
			$key = array("\\",' ',"'",'"','*',',','<','>',"\r","\t","\n",'(',')','+',';');
			foreach($key as $value){
				if (strpos($url,$value) !== false){ 
					redirect('网站URL错误');
				}
			}
		}
	}
	$username    = char_cv($username);
	$newpassword = md5($newpassword);

    $query = $DB->query("SELECT userid FROM {$db_prefix}users WHERE username='$username'");
    if($DB->num_rows($query)) {
		redirect('该用户名已被注册');
    }

	$DB->query("INSERT INTO {$db_prefix}users (username, password, url, regdateline, regip, groupid) VALUES ('$username', '$newpassword', '$url', '$timestamp', '$onlineip', '$groupid')");
    redirect('添加新用户成功', 'admincp.php?job=user&action=list');
}

//修改用户
if($action == 'moduser') {	
	$username       = trim($_POST['username']);
	$newpassword    = trim($_POST['newpassword']);
	$comfirpassword = trim($_POST['comfirpassword']);
	$url            = trim($_POST['url']);
	$groupid        = intval($_POST['groupid']);
	$userid         = intval($_POST['userid']);
	if (!$username || strlen($username) > 20) {
		redirect('登陆名不能为空并且不能超过20个字符');
    }
	$password_sql = '';
	if ($newpassword) {
		if(strlen($newpassword) < 8) {
			redirect('新密码长度不能小于8位');
		}
		if ($newpassword != $comfirpassword) {
			redirect('请确认输入的新密码一致');
		}
		if (strpos($newpassword,"\n") !== false || strpos($password,"\r") !== false || strpos($password,"\t") !== false) {
			redirect('密码包含不可接受字符');
		}
		$password_sql = ", password='".md5($newpassword)."'";
	}
	$name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
	foreach($name_key as $value){
		if (strpos($username,$value) !== false){
			redirect('用户名包含敏感字符');
		}
	}
	$url = char_cv($url);
	if ($url) {
		if (isemail($url)) {
			$r = $DB->fetch_one_array("SELECT userid FROM {$db_prefix}users WHERE url='$url' AND userid!='$userid'");
			if($r['userid']) {
				redirect('该E-mail已被注册');
			}
			unset($r);
		} else {			
			if (!preg_match("#^(http|news|https|ftp|ed2k|rtsp|mms)://#", $url)) {
				redirect('网站URL错误');
			}
			$key = array("\\",' ',"'",'"','*',',','<','>',"\r","\t","\n",'(',')','+',';');
			foreach($key as $value){
				if (strpos($url,$value) !== false){ 
					redirect('网站URL错误');
				}
			}
		}
	}
	$username = char_cv($username);
    $r = $DB->fetch_one_array("SELECT userid FROM {$db_prefix}users WHERE username='$username' AND userid!='$userid'");
    if($r) {
		redirect('该用户名已被注册');
    }

	$usernamesql = $username ? "username='$username'," : '';
    $DB->unbuffered_query("UPDATE {$db_prefix}users SET $usernamesql url='$url', groupid='$groupid' $password_sql WHERE userid='$userid'");
    redirect('用户修改成功','admincp.php?job=user&action=mod&userid='.$userid);
}

//删除用户
if($action == 'delusers') {	
	if ($uids = implode_ids($_POST['user'])) {
		$user_count = count($_POST['user']);	
		if ($_POST['deluserarticle']) {
			$aids = $a_tatol = 0;
			// 删除该用户发表的文章以及相关数据
			require_once(SABLOG_ROOT.'include/func_attachment.php');
			$query = $DB->query("SELECT articleid,keywords,visible,cid FROM {$db_prefix}articles WHERE uid IN ($uids)");
			while ($article = $DB->fetch_array($query)) {
				if ($article['keywords']) {
					updatetags($article['articleid'], '', $article['keywords']);
				}
				if ($article['visible']) {
					$a_tatol++;
					$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles-1 WHERE cid='".$article['cid']."'");
				}
				$aids .= ','.$article['articleid'];
			}//end while
			
			// 删除该用户的文章中的附件
			$query  = $DB->query("SELECT attachmentid,filepath,thumb_filepath FROM {$db_prefix}attachments WHERE articleid IN ($aids)");
			$nokeep = array();
			while($attach = $DB->fetch_array($query)) {
				$nokeep[$attach['attachmentid']] = $attach;
			}
			removeattachment($nokeep);

			$DB->unbuffered_query("DELETE FROM {$db_prefix}comments WHERE articleid IN ($aids)");
			$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacks WHERE articleid IN ($aids)");
			$DB->unbuffered_query("DELETE FROM {$db_prefix}trackbacklog WHERE articleid IN ($aids)");
			$DB->unbuffered_query("DELETE FROM {$db_prefix}articles WHERE uid IN ($uids)");
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count-".$a_tatol);
		}
		// 删除用户
		$DB->unbuffered_query("DELETE FROM {$db_prefix}users WHERE userid IN ($uids)");
		$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET user_count=user_count-".$user_count);

		archives_recache();
		categories_recache();
		statistics_recache();
		redirect('删除用户成功', 'admincp.php?job=user&action=list');
	} else {		
		redirect('未选择任何用户');
	}
}

if($action == 'list') {
	$groupid = intval($_GET['groupid']);
	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$subnav = '全部用户';
	$sqladd = ' WHERE 1 ';
	$pagelink = '';
	//察看是否发表过评论
	$lastpost = in_array($_GET['lastpost'],array('already','never')) ? $_GET['lastpost'] : '';
	if ($lastpost == 'already') {
		$sqladd .= " AND lastpost <> '0'";
		$pagelink .= '&lastpost=already';
		$subnav = '发表过评论的用户';
	}
	if ($lastpost == 'never') {
		$sqladd .= " AND lastpost='0'";
		$pagelink .= '&lastpost=never';
		$subnav = '从未发表过评论的用户';
	}
	//察看用户组
	if ($groupid && in_array($groupid,array_flip($groupdb))) {
		$sqladd .= " AND groupid='$groupid'";
		$pagelink .= '&groupid='.$groupid;
		$subnav = $groupdb[$groupid].'的用户';
	}
	//察看IP段
	$ip = char_cv($_GET['ip']);
	if ($ip) {
		$frontlen = strrpos($ip, '.');
		$ipc = substr($ip, 0, $frontlen);
		$sqladd .= " AND (loginip LIKE '%".$ipc."%')";
		$pagelink .= '&ip='.$ip;
		$subnav  = '上次登陆IP为['.$ip.']同一C段的相关用户';
	}
	//搜索用户
	$srhname = char_cv($_GET['srhname'] ? $_GET['srhname'] : $_POST['srhname']);
	if ($srhname) {
		$sqladd .= " AND (BINARY username LIKE '%".str_replace('_', '\_', $srhname)."%' OR username='$srhname')";
		$pagelink .= '&srhname='.$srhname;
	}

	//排序
	$order = $_GET['order'];
	if ($order && in_array($order,array('username','logincount','regdateline'))) {
		$orderby = $order;
		$orderdb = array('username'=>'用户名','logincount'=>'登陆次数','regdateline'=>'注册时间');
		$subnav = '以'.$orderdb[$order].'降序察看全部用户';
		$pagelink .= '&order='.$order;
	} else {
		$orderby = 'userid';
	}
	$tatol     = $DB->num_rows($DB->query("SELECT userid FROM {$db_prefix}users ".$sqladd));
	$multipage = multi($tatol, 30, $page, 'admincp.php?job=user&action=list'.$pagelink);
	$query = $DB->query("SELECT userid,username,logincount,loginip,logintime,url,regdateline,groupid,lastpost FROM {$db_prefix}users $sqladd ORDER BY $orderby DESC LIMIT $start_limit, 30");
	$userdb = array();
	while ($user = $DB->fetch_array($query)) {
		$user['lastpost']    = $user['lastpost'] ? sadate('Y-m-d H:i',$user['lastpost']) : '从未发表';
		$user['regdateline'] = sadate('Y-m-d',$user['regdateline']);
		$user['url']         = $user['url'] ? (isemail($user['url']) ? '<a href="mailto:'.$user['url'].'" target="_blank">发送邮件</a>' : '<a href="'.$user['url'].'" target="_blank">访问主页</a>') : '<font color="#FF0000">Null</font>';
		$user['logintime'] = $user['logintime'] ? sadate('Y-m-d H:i',$user['logintime']) : '从未登陆';
		$user['loginip']   = $user['loginip'] ? $user['loginip'] : '从未登陆';
		$user['group'] = $groupdb[$user['groupid']];
		$user['disabled'] = ($user['groupid'] == 1 || $user['userid'] == 1) ? 'disabled' : '';
		$userdb[] = $user;
	}
	unset($user);
	$DB->free_result($query);
} //end list

if (in_array($action, array('add', 'mod'))) {
	if ($action == 'add') {
		$subnav = '添加用户';
		$do = 'adduser';
		$groupselect[3] = 'selected';
	} else {
		$userid = intval($_GET['userid']);
		$subnav = '修改用户';
		$do = 'moduser';
		$info = $DB->fetch_one_array("SELECT * FROM {$db_prefix}users WHERE userid='$userid'");
		$groupselect[$info['groupid']] = 'selected';
	}
} //end mod


if($action == 'del') {
	if ($uids = implode_ids($_POST['user'])) {
		$userdb = array();
		$query = $DB->query("SELECT userid,username FROM {$db_prefix}users WHERE userid IN ($uids) AND groupid <> '1' AND userid <> '1'");
		while ($user = $DB->fetch_array($query))	{ 
			$userdb[] = $user;
		}
		unset($user);
		$DB->free_result($query);
	} else {		
		redirect('未选择任何用户');
	}
	$subnav = '删除用户';
}// end del

$navlink_L = ' &raquo; <a href="admincp.php?job=user">用户管理</a>'.($subnav ? ' &raquo; '.$subnav : '');
cpheader();
include PrintEot('user');
?>