<?php
define('SABLOG_ROOT', '../');
$onoff = function_exists('ini_get') ? ini_get('register_globals') : get_cfg_var('register_globals');
if ($onoff != 1) {
	@extract($_POST, EXTR_SKIP);
	@extract($_GET, EXTR_SKIP);
}
$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];

if ($action == 'first' || $action == 'second' || $action == 'three' || $action == 'four' || $action == 'fifthly') {
	// 允许程序在 register_globals = off 的环境下工作
	// 加载数据库配置信息
	require_once(SABLOG_ROOT.'config.php');
	// 加载数据库类
	require_once(SABLOG_ROOT.'include/func_db_mysql.php');
	// 初始化数据库类
	$DB = new DB_MySQL;
	$DB->connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect);
	unset($servername, $dbusername, $dbpassword, $dbname, $usepconnect);

	$step = (!$step) ? 1 : $step;
	$a = (!$a) ? 0 : $a;
	$percount = ($percount <= 0) ? 500 : $percount;
	$start    = ($step - 1) * $percount;
	$next     = $start + $percount;
	$step++;
	$jumpurl = $php_self.'?action='.$action.'&step='.$step.'&percount='.$percount;
	$goon = 0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SABLOG-X | Powered by 4ngel</title>
<link href="install.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
if ($action == 'first') {
	$query = $DB->query("SELECT commentid,url,email FROM {$db_prefix}comments ORDER BY commentid LIMIT ".$start.", ".$percount);
	while ($comment = $DB->fetch_array($query)) {
		$goon = 1;
		if (!$comment['url'] && $comment['email']) {
			$DB->unbuffered_query("UPDATE {$db_prefix}comments SET url=email WHERE commentid='".$comment['commentid']."'");
		}
	}
	echo '<div class="install_main">';
	if($goon) {
		echo '<p class="p2">评论数据正在更新 '.$start.' 到 '.$next.' 项</p><p class="p2"><a href="'.$jumpurl.'">程序将自动跳转.如果没有自动跳转,请点击这里.</a></p>';
		echo '<meta HTTP-EQUIV="REFRESH" content="3;URL='.$jumpurl.'">';
	} else {
		echo '<p class="p2">成功重建所有评论数据</p><p class="p2"><a href="?action=second">程序将自动跳转.如果没有自动跳转,请点击这里.</a></p>';
		echo '<meta HTTP-EQUIV="REFRESH" content="3;URL=?action=second">';
	}
	echo '</div></body></html>';
	exit;
} elseif ($action == 'second') {
	$query = $DB->query("SELECT userid,url,email FROM {$db_prefix}users ORDER BY userid LIMIT ".$start.", ".$percount);
	while ($user = $DB->fetch_array($query)) {
		$goon = 1;
		if (!$user['url'] && $user['email']) {
			$DB->unbuffered_query("UPDATE {$db_prefix}users SET url=email WHERE userid='".$user['userid']."'");
		}
	}
	echo '<div class="install_main">';
	if($goon) {
		echo '<p class="p2">用户数据正在更新 '.$start.' 到 '.$next.' 项</p><p class="p2"><a href="'.$jumpurl.'">程序将自动跳转.如果没有自动跳转,请点击这里.</a></p>';
		echo '<meta HTTP-EQUIV="REFRESH" content="3;URL='.$jumpurl.'">';
	} else {
		echo '<p class="p2">成功重建所有用户数据</p><p class="p2"><a href="?action=three">程序将自动跳转.如果没有自动跳转,请点击这里.</a></p>';
		echo '<meta HTTP-EQUIV="REFRESH" content="3;URL=?action=three">';
	}
	echo '</div></body></html>';
	exit;
} elseif ($action == 'three') {
	$DB->query("DROP TABLE {$db_prefix}adminlog");
	$DB->query("DROP TABLE {$db_prefix}loginlog");
	$DB->query("DROP TABLE {$db_prefix}searchstats");
	$DB->query("ALTER TABLE {$db_prefix}admin DROP qq, DROP msn, DROP birthday, DROP location, DROP interest, DROP skill;");
	$DB->query("ALTER TABLE {$db_prefix}articles DROP cname, DROP author;");
	$DB->query("ALTER TABLE {$db_prefix}comments DROP email;");
	$DB->query("ALTER TABLE {$db_prefix}users DROP email, DROP comments;");

	$DB->query("DROP TABLE IF EXISTS {$db_prefix}sessions;");
	$DB->query("CREATE TABLE {$db_prefix}sessions ( hash varchar(20) NOT NULL default '', uid mediumint(8) NOT NULL default '0', groupid smallint(6) NOT NULL, ipaddress varchar(16) NOT NULL default '', agent varchar(200) NOT NULL, lastactivity int(10) NOT NULL default '0', PRIMARY KEY (hash));");
	$DB->query("DROP TABLE IF EXISTS {$db_prefix}stylevars;");
	$DB->query("CREATE TABLE {$db_prefix}stylevars ( stylevarid mediumint(9) NOT NULL auto_increment, title varchar(200) NOT NULL, value text NOT NULL, visible tinyint(1) NOT NULL default '1', PRIMARY KEY  (stylevarid));");

	echo '<div class="install_main">';
	echo '<p class="p2">成功删除多余的数据并添加新的数据表</p><p class="p2"><a href="?action=four">程序将自动跳转.如果没有自动跳转,请点击这里.</a></p>';
	echo '<meta HTTP-EQUIV="REFRESH" content="3;URL=?action=four">';
	echo '</div></body></html>';
	exit;
} elseif ($action == 'four') {
	$DB->query("ALTER TABLE {$db_prefix}admin DROP nickname, DROP face, DROP signature, DROP allowarticle, DROP allowattachment, DROP allowcache, DROP allowcategory, DROP allowcomment, DROP allowconfigurate, DROP allowdatabase, DROP allowlinks, DROP allowlog, DROP allowtags, DROP allowtemplate, DROP allowtrackback, DROP allowuser, DROP selectcid;");

	$DB->query("ALTER TABLE {$db_prefix}admin ADD regdateline INT( 10 ) NOT NULL ,ADD regip VARCHAR( 16 ) NOT NULL ,ADD groupid SMALLINT ( 4 ) NOT NULL ,ADD lastpost INT( 10 ) NOT NULL ;");

	$DB->query("ALTER TABLE {$db_prefix}admin CHANGE email url VARCHAR( 255 ) NOT NULL;");
	$DB->query("ALTER TABLE {$db_prefix}admin CHANGE adminid userid MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT ;");

	$DB->unbuffered_query("UPDATE {$db_prefix}admin SET groupid='1'");
	$DB->unbuffered_query("ALTER TABLE {$db_prefix}articles CHANGE authorid uid MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0'");
	$DB->unbuffered_query("ALTER TABLE {$db_prefix}comments DROP authorid");

	$admindb = array();
	$query = $DB->query("SELECT username FROM {$db_prefix}admin");
	while ($admin = $DB->fetch_array($query)) {
		$admindb[] = $admin['username'];
	}
	$query = $DB->query("SELECT userid,username,password,url,regdateline,ipaddress,lastpost FROM {$db_prefix}users");
	while ($user = $DB->fetch_array($query)) {
		if (!in_array($user['username'],$admindb)) {
			$DB->query("INSERT INTO {$db_prefix}admin (username,password,loginip,logintime,url,regdateline,regip,groupid,lastpost) VALUES ('".addslashes($user['username'])."','".addslashes($user['password'])."','".addslashes($user['ipaddress'])."','".addslashes($user['regdateline'])."','".addslashes($user['url'])."','".addslashes($user['regdateline'])."','".addslashes($user['ipaddress'])."','3','".addslashes($user['lastpost'])."')");
		}
	}
	echo '<meta HTTP-EQUIV="REFRESH" content="3;URL=?action=fifthly">';
	echo '<div class="install_main">';
	echo '<p class="p2">用户转换数据完毕.</p><p class="p2"><a href="?action=fifthly">程序将自动跳转.如果没有自动跳转,请点击这里.</a></p>';
	echo '</div></body></html>';
	exit;
} elseif ($action == 'fifthly') {
	$DB->query("DROP TABLE {$db_prefix}users;");
	$DB->query("RENAME TABLE {$db_prefix}admin TO {$db_prefix}users;");
	echo '<div class="install_main">';
	echo '<p class="p2">升级完毕.请立即删除升级程序.</p>';
	echo '</div></body></html>';
	exit;
} else {
?>
<div class="install_main">
  <div class="install_logo"><img src="top.gif" alt="安全天使" width="500" height="50" longdesc="http://www.4ngel.net/" /></div>
  <div id="install_innertext">
    <p class="p2">感谢您选择由 <a href="http://www.4ngel.net/" target="_blank">安全天使网络安全小组</a> 开发的 <a href="http://www.4ngel.net/">SaBlog-X</a> 博客程序!</p>
    <p class="p2">当前版本为 <u>SaBlog-X v 1.2</u></p>
    <p class="p2">目标版本为 <u>SaBlog-X v 1.6</u></p>
    <p class="p2">升级过程完全不用人工干预,请耐心等待成功带来的喜悦.</p>
	<p class="p2"><a href="<?=$php_self?>?action=first">升级数据</a></p>
  </div>
</div>
<?php
}
?>
<div class="copyright">Powered by SaBlog-X (C) 2003-2006 Security Angel Team</div>
</body>
</html>
