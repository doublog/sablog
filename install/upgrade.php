<?php
error_reporting(0);

define('SABLOG_ROOT', TRUE);
ob_start();

// 允许程序在 register_globals = off 的环境下工作
if (function_exists('ini_get')) {
	$onoff = ini_get('register_globals');
} else {
	$onoff = get_cfg_var('register_globals');
}
if ($onoff != 1) {
	@extract($_POST, EXTR_SKIP);
	@extract($_GET, EXTR_SKIP);
}

// 去除转义字符
function stripslashes_array(&$array) {
	if (is_array($array)) {
		foreach ($array as $k => $v) {
			$array[$k] = stripslashes_array($v);
		}
	} else if (is_string($array)) {
		$array = stripslashes($array);
	}
	return $array;
}

// 判断 magic_quotes_gpc 状态
if (get_magic_quotes_gpc()) {
    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
}

set_magic_quotes_runtime(0);

$step = (isset($_GET['step'])) ? $_GET['step'] : $_POST['step'];
$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
$action = ($_GET['action']) ? $_GET['action'] : $_POST['action'];
$dbcharset = 'utf8';
$configfile = '../config.php';


$sqlfile = 'upgrade.sql';
if(!is_readable($sqlfile)) {
	exit('数据库文件不存在或者读取失败');
}
$fp = fopen($sqlfile, 'rb');
$sql = fread($fp, 2048000);
fclose($fp);


include ($configfile);
include ('../include/func_db_mysql.php');
$DB = new DB_MySQL;
$DB->connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect);
unset($servername, $dbusername, $dbpassword, $usepconnect);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SaBlog-X升级脚本</title>
<link href="install.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="main">
  <form method="post" action="<?php echo $php_self;?>">
    <p class="title">SaBlog-X V1.1 升级向导</p>
    <hr noshade="noshade" />
<?php
if (empty($step) || $step == '1') {
?>
    <p class="title">第一步:升级须知</p>
	<ol>
	<li>本程序只适用于SaBlog V2.8. 如果不是此版本请勿使用本程序升级或先升级到SaBlog V2.8再使用本程序升级到SaBlog-X V1.1</li>
	<li>SaBlog-X V1.1和SaBlog V2.8相比, 数据结构和编码都有了变化,因此升级前一定要备份数据!</li>
	<li>本程序没有身份验证功能, 本文件可以改名执行, 并且升级完毕后一定删除 <b>install</b> 目录!</li>
	<li>升级过程中千万不能关闭浏览器!</li>
	<li>没有遵照步骤提示操作造成的一切后果自己承担,本程序作者不会负任何责任!</li>
	</ol>
    <p><b>请确保数据已经转换成UTF-8编码,<a href="../doc/convert.htm">转换方法请参阅这里</a></b><br>当然也可以尝试升级,如果没有出现乱码即可正常使用,如果出现乱码则必须执行转换操作.请务必备份原始数据.</p>
    <p>&nbsp;</p>
    <p>点击下一步就开始升级操作!</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="2" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '2') {
?>
    <p class="title">第二步:建立数据表</p>
    <p>本步骤建立 SaBlog-X V1.1 的临时数据表</p>
	<p>
<?php
	runquery($sql);
?>
    </p>
    <p>&nbsp;</p>
	<p>点击下一步就开始把 SaBlog V2.8 的数据分步骤转移到刚才建立的临时数据表中.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="3" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '3') {
?>
    <p class="title">第三步:转移数据</p>
	<ol>
	<li>用户数据</li>
	<li>分类数据</li>
	<li>附件数据</li>
	</ol>
	<p>
<?php
	$query = $DB->query("SELECT * FROM {$db_prefix}user");
	while ($user = $DB->fetch_array($query)) {		
		$DB->query("INSERT INTO tmp_admin (adminid, username,nickname,password,email,logincount,logintime,loginip,allowarticle, allowattachment, allowcache, allowcategory, allowcomment, allowconfigurate, allowdatabase, allowlinks, allowlog, allowtags, allowtrackback, allowuser) VALUES ('".$user['userid']."','".$user['username']."','".$user['nickname']."','".$user['password']."','".$user['email']."','".$user['logincount']."','".$user['logintime']."','".$user['loginip']."','1','1','1','1','1','1','1','1','1','1','1','1')");
	}
	echo '将 <b>2.8</b> 的 <b>user</b> 表的数据转移到 <b>X</b> 的 <b>admin</b> 表... <font color="#0000EE">成功</font><br />';
	$query = $DB->query("SELECT * FROM {$db_prefix}sort");
	while ($cate = $DB->fetch_array($query)) {		
		$DB->query("INSERT INTO tmp_categories (cid,name,displayorder,articles) VALUES ('".$cate['sortid']."','".$cate['sortname']."','".$cate['displayorder']."','".$cate['bcount']."')");
	}
	echo '将 <b>2.8</b> 的 <b>sort</b> 表的数据转移到 <b>X</b> 的 <b>categories</b> 表... <font color="#0000EE">成功</font><br />';
	$query = $DB->query("SELECT * FROM {$db_prefix}attachment");
	while ($attach = $DB->fetch_array($query)) {		
		$DB->query("INSERT INTO tmp_attachments (attachmentid, articleid, dateline, filename, filetype, filesize, downloads, filepath, thumb_filepath, thumb_width, thumb_height, isimage) VALUES ('".$attach['attachmentid']."','".$attach['blogid']."','".$attach['addtime']."','".$attach['filename']."','".$attach['filetype']."','".$attach['filesize']."','".$attach['counter']."','".$attach['filepath']."','".$attach['thumb_location']."','".$attach['thumb_width']."','".$attach['thumb_height']."','".$attach['isimage']."')");
	}
	echo '将 <b>2.8</b> 的 <b>attachment</b> 表的数据转移到 <b>X</b> 的 <b>attachments</b> 表... <font color="#0000EE">成功</font><br />';
?>
    </p>
    <p>&nbsp;</p>
	<p>点击下一步继续把 SaBlog V2.8 的数据转移到刚才建立的临时数据表中.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="4" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '4') {
?>
    <p class="title">第四步:转移数据</p>
	<ol>
	<li>文章数据</li>
	</ol>
	<p>
<?php
	$query = $DB->query("SELECT * FROM tmp_categories");
	$catedb = array();
	while ($cate = $DB->fetch_array($query)) {
		$catedb[$cate['cid']] = $cate['name'];
	}
	unset($cate);
	$query = $DB->query("SELECT * FROM tmp_admin");
	$admindb = array();
	while ($admin = $DB->fetch_array($query)) {
		if ($admin['nickname']) {
			$admindb[$admin['adminid']] = $admin['nickname'];
		} else {
			$admindb[$admin['adminid']] = $admin['username'];
		}
	}
	unset($admin);
	$attachs = array();
	$query = $DB->query("SELECT * FROM {$db_prefix}blog");
	while ($article = $DB->fetch_array($query)) {
		$attachsql = $DB->query("SELECT * FROM tmp_attachments WHERE articleid='".$article['blogid']."'");
		while ($attach = $DB->fetch_array($attachsql)) {			
			$attachs[$attach['attachmentid']] = array(
				'attachmentid' => $attach['attachmentid'],
				'dateline' => $attach['dateline'],
				'filename' => $attach['filename'],
				'filetype' => $attach['filetype'],
				'filepath' => $attach['filepath'],
				'filesize' => $attach['filesize'],
				'downloads' => $attach['downloads'],
				'thumb_filepath' => $attach['thumb_filepath'],
				'thumb_width' => $attach['thumb_width'],
				'thumb_height' => $attach['thumb_height'],
				'isimage' => $attach['isimage']
			);
		}
		$attachs = $attachs ? addslashes(serialize($attachs)) : '';
		$article['closecomment'] = ($article['iscomment']) ? '0' : '1';
		$article['closetrackback'] = ($article['istrackback']) ? '0' : '1';
		$DB->query("INSERT INTO tmp_articles (articleid, cid, cname, author, authorid, title, description, content, keywords, dateline, views, comments, attachments, trackbacks, closecomment, closetrackback, visible, stick) VALUES ('".$article['blogid']."','".$article['sortid']."','".addslashes($catedb[$article['sortid']])."','".addslashes($admindb[$article['userid']])."','".$article['userid']."','".addslashes($article['title'])."','".addslashes($article['description'])."','".addslashes($article['content'])."','".addslashes($article['keywords'])."','".$article['addtime']."','".$article['viewcount']."','".$article['ccount']."','".$attachs."','".$article['tbcount']."','".$article['closecomment']."','".$article['closetrackback']."','".$article['visible']."','".$article['stick']."')");
		unset($attach);
		unset($attachsql);
		unset($attachs);
	}
	echo '将 <b>2.8</b> 的 <b>blog</b> 表的数据转移到 <b>X</b> 的 <b>articles</b> 表... <font color="#0000EE">成功</font><br />';
?>
    </p>
    <p>&nbsp;</p>
	<p>点击下一步继续把 SaBlog V2.8 的数据转移到刚才建立的临时数据表中.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="5" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '5') {
?>
    <p class="title">第五步:转移数据</p>
	<ol>
	<li>评论数据</li>
	</ol>
	<p>
<?php
	$query = $DB->query("SELECT * FROM {$db_prefix}comment");
	while ($comment = $DB->fetch_array($query)) {		
		$DB->query("INSERT INTO tmp_comments (commentid, articleid, author, url, dateline, content, ipaddress, visible) VALUES ('".$comment['commentid']."','".$comment['blogid']."','".addslashes($comment['author'])."','".addslashes($comment['email'])."','".$comment['addtime']."','".addslashes($comment['content'])."','".$comment['ipaddress']."','".$comment['visible']."')");
	}
	echo '将 <b>2.8</b> 的 <b>comment</b> 表的数据转移到 <b>X</b> 的 <b>comments</b> 表... <font color="#0000EE">成功</font><br />';
?>
    </p>
    <p>&nbsp;</p>
	<p>点击下一步继续把 SaBlog V2.8 的数据转移到刚才建立的临时数据表中.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="6" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '6') {
?>
    <p class="title">第六步:转移数据</p>
	<ol>
	<li>后台管理记录数据</li>
	<li>后台登陆记录数据</li>
	</ol>
	<p>
<?php
	$query = $DB->query("SELECT * FROM {$db_prefix}adminlog");
	while ($adminlog = $DB->fetch_array($query)) {		
		$DB->query("INSERT INTO tmp_adminlog (action,script,dateline,ipaddress,username) VALUES ('".$adminlog['action']."','".$adminlog['script']."','".$adminlog['date']."','".$adminlog['ipaddress']."','Unkown')");
	}
	echo '将 <b>2.8</b> 的 <b>adminlog</b> 表的数据转移到 <b>X</b> 的 <b>adminlog</b> 表... <font color="#0000EE">成功</font><br />';
	$query = $DB->query("SELECT * FROM {$db_prefix}loginlog");
	while ($loginlog = $DB->fetch_array($query)) {
		$loginlog['result'] = $loginlog['result'] ? '1' : '0';
		$DB->query("INSERT INTO tmp_loginlog (loginlogid, username, dateline, ipaddress, result) VALUES ('".$loginlog['loginlogid']."','".addslashes($loginlog['username'])."','".$loginlog['date']."','".$loginlog['ipaddress']."','".$loginlog['result']."')");
	}
	echo '将 <b>2.8</b> 的 <b>loginlog</b> 表的数据转移到 <b>X</b> 的 <b>loginlog</b> 表... <font color="#0000EE">成功</font><br />';
?>
    </p>
    <p>&nbsp;</p>
	<p>点击下一步继续把 SaBlog V2.8 的数据转移到刚才建立的临时数据表中.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="7" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '7') {
?>
    <p class="title">第七步:转移数据</p>
	<ol>
	<li>友情链接数据</li>
	<li>统计数据</li>
	</ol>
	<p>
<?php
	$query = $DB->query("SELECT * FROM {$db_prefix}link");
	while ($link = $DB->fetch_array($query)) {		
		$DB->query("INSERT INTO tmp_links (linkid, name, url, note, visible) VALUES ('".$link['linkid']."','".addslashes($link['sitename'])."','".addslashes($link['url'])."','".addslashes($link['description'])."','".$link['visible']."')");
	}
	echo '将 <b>2.8</b> 的 <b>link</b> 表的数据转移到 <b>X</b> 的 <b>links</b> 表... <font color="#0000EE">成功</font><br />';
?>
    </p>
    <p>&nbsp;</p>
	<p>点击下一步继续把 SaBlog V2.8 的数据转移到刚才建立的临时数据表中.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="8" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '8') {
?>
    <p class="title">第八步:转移数据</p>
	<ol>
	<li>Trackback接收记录</li>
	<li>Trackback发送记录</li>
	</ol>
	<p>
<?php
	$query = $DB->query("SELECT * FROM {$db_prefix}trackback");
	while ($tb = $DB->fetch_array($query)) {		
		$DB->query("INSERT INTO tmp_trackbacks (trackbackid, articleid, title, dateline, excerpt, url, blog_name, ipaddress) VALUES ('".$tb['trackbackid']."','".$tb['blogid']."','".addslashes($tb['title'])."','".$tb['addtime']."','".addslashes($tb['excerpt'])."','".addslashes($tb['url'])."','".addslashes($tb['blog_name'])."','".$tb['ipaddress']."')");
	}
	echo '将 <b>2.8</b> 的 <b>trackback</b> 表的数据转移到 <b>X</b> 的 <b>trackbacks</b> 表... <font color="#0000EE">成功</font><br />';
	$query = $DB->query("SELECT * FROM {$db_prefix}trackbacklog");
	while ($tblog = $DB->fetch_array($query)) {		
		$DB->query("INSERT INTO tmp_trackbacklog (trackbacklogid, articleid, dateline, pingurl) VALUES ('".$tblog['trackbacklogid']."','".$tblog['blogid']."','".$tblog['addtime']."','".addslashes($tblog['pingurl'])."')");
	}
	echo '将 <b>2.8</b> 的 <b>trackbacklog</b> 表的数据转移到 <b>X</b> 的 <b>trackbacklog</b> 表... <font color="#0000EE">成功</font><br />';
?>
    </p>
    <p>&nbsp;</p>
	<p>点击下一步把 SaBlog V2.8 的数据表删除!现在备份2.8的数据还来得及.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="9" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '9') {
	$extrasql = "
	DROP TABLE {$db_prefix}adminlog;
	DROP TABLE {$db_prefix}attachment;
	DROP TABLE {$db_prefix}blog;
	DROP TABLE {$db_prefix}cache;
	DROP TABLE {$db_prefix}comment;
	DROP TABLE {$db_prefix}info;
	DROP TABLE {$db_prefix}link;
	DROP TABLE {$db_prefix}loginlog;
	DROP TABLE {$db_prefix}setting;
	DROP TABLE {$db_prefix}settinggroup;
	DROP TABLE {$db_prefix}sort;
	DROP TABLE {$db_prefix}stat;
	DROP TABLE {$db_prefix}tags;
	DROP TABLE {$db_prefix}trackback;
	DROP TABLE {$db_prefix}trackbacklog;
	DROP TABLE {$db_prefix}user;";

	runquery($extrasql);
?>
    <p class="title">第九步:删除旧数据</p>
    <p>成功将下列表删除</p>
	<ol>
	<li><?php echo $db_prefix;?>adminlog</li>
	<li><?php echo $db_prefix;?>attachment</li>
	<li><?php echo $db_prefix;?>blog</li>
	<li><?php echo $db_prefix;?>cache</li>
	<li><?php echo $db_prefix;?>comment</li>
	<li><?php echo $db_prefix;?>info</li>
	<li><?php echo $db_prefix;?>link</li>
	<li><?php echo $db_prefix;?>loginlog</li>
	<li><?php echo $db_prefix;?>setting</li>
	<li><?php echo $db_prefix;?>settinggroup</li>
	<li><?php echo $db_prefix;?>sort</li>
	<li><?php echo $db_prefix;?>stat</li>
	<li><?php echo $db_prefix;?>tags</li>
	<li><?php echo $db_prefix;?>trackback</li>
	<li><?php echo $db_prefix;?>trackbacklog</li>
	<li><?php echo $db_prefix;?>user</li>
	</ol>
    <p>&nbsp;</p>
	<p>点击下一步将刚才建立的临时表改名.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="10" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '10') {
	$extrasql = "
	ALTER TABLE tmp_admin RENAME {$db_prefix}admin;
	ALTER TABLE tmp_adminlog RENAME {$db_prefix}adminlog;
	ALTER TABLE tmp_articles RENAME {$db_prefix}articles;
	ALTER TABLE tmp_attachments RENAME {$db_prefix}attachments;
	ALTER TABLE tmp_categories RENAME {$db_prefix}categories;
	ALTER TABLE tmp_comments RENAME {$db_prefix}comments;
	ALTER TABLE tmp_links RENAME {$db_prefix}links;
	ALTER TABLE tmp_loginlog RENAME {$db_prefix}loginlog;
	ALTER TABLE tmp_searchindex RENAME {$db_prefix}searchindex;
	ALTER TABLE tmp_settings RENAME {$db_prefix}settings;
	ALTER TABLE tmp_statistics RENAME {$db_prefix}statistics;
	ALTER TABLE tmp_tags RENAME {$db_prefix}tags;
	ALTER TABLE tmp_trackbacklog RENAME {$db_prefix}trackbacklog;
	ALTER TABLE tmp_trackbacks RENAME {$db_prefix}trackbacks;
	ALTER TABLE tmp_users RENAME {$db_prefix}users;";

	runquery($extrasql);
?>
    <p class="title">第十步:数据表改名</p>
    <p>改名成功</p>
	<ol>
	<li><b>tmp_admin</b> 改名为 <b><?php echo $db_prefix;?>admin</b></li>
	<li><b>tmp_adminlog</b> 改名为 <b><?php echo $db_prefix;?>adminlog</b></li>
	<li><b>tmp_articles</b> 改名为 <b><?php echo $db_prefix;?>articles</b></li>
	<li><b>tmp_attachments</b> 改名为 <b><?php echo $db_prefix;?>attachments</b></li>
	<li><b>tmp_categories</b> 改名为 <b><?php echo $db_prefix;?>categories</b></li>
	<li><b>tmp_comments</b> 改名为 <b><?php echo $db_prefix;?>comments</b></li>
	<li><b>tmp_links</b> 改名为 <b><?php echo $db_prefix;?>links</b></li>
	<li><b>tmp_loginlog</b> 改名为 <b><?php echo $db_prefix;?>loginlog</b></li>
	<li><b>tmp_searchindex</b> 改名为 <b><?php echo $db_prefix;?>searchindex</b></li>
	<li><b>tmp_settings</b> 改名为 <b><?php echo $db_prefix;?>settings</b></li>
	<li><b>tmp_statistics</b> 改名为 <b><?php echo $db_prefix;?>statistics</b></li>
	<li><b>tmp_tags</b> 改名为 <b><?php echo $db_prefix;?>tags</b></li>
	<li><b>tmp_trackbacklog</b> 改名为 <b><?php echo $db_prefix;?>trackbacklog</b></li>
	<li><b>tmp_trackbacks</b> 改名为 <b><?php echo $db_prefix;?>trackbacks</b></li>
	<li><b>tmp_users</b> 改名为 <b><?php echo $db_prefix;?>users</b></li>
	</ol>
    <p>&nbsp;</p>
	<p>数据转换完毕!点击下一步将告诉您一些注意事项(重要).</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="11" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
} elseif ($step == '11') {
?>
    <p class="title">第十一步:注意事项</p>
    <p>到此升级的主要部分已经执行完毕了,您可以将 <b>install</b> 目录删除了.别忘记了.</p>
    <p>现在您需要做的一些事情.</p>
	<ol>
	<li>不敢保证升级过后数据一点问题没有,建议您检查一遍所有数据.</li>
	<li>到后台重新设置您的系统的配置选项.</li>
	<li>执行一次附件修复.清除一些垃圾数据使您的系统运行速度更快.</li>
	<li>执行一次清理标签的操作.将您的标签数据修复.</li>
	<li>执行一次重建数据的操作.将所有数据重新统计.否则可能有些功能不正常.</li>
	<li>执行一次更新全部缓存操作.</li>
	</ol>
    <p>&nbsp;</p>
	<p>如果上面的操作都做完而且没有出现异常情况.恭喜你.平滑升级成功!</p>
	<p>如果有问题可以给我发电子邮件!</p>
    <p><a href="../">点击这里进入博客</a></p>
    <hr noshade="noshade" />
    <p align="right"><a href="http://www.4ngel.net">Welcome to Security Angel Team</a></p>
<?php
}
?>
  </form>
</div>
<strong>Powered by SaBlog-X (C) 2003-2005 Security Angel Team</strong>
</body>
</html>
<?php

function runquery($sql) {
	global $dbcharset, $db_prefix, $DB, $tablenum;

	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == '#' ? '' : $query;
		}
		$num++;
	}
	unset($sql);

	foreach($ret as $query) {
		$query = trim($query);
		if($query) {
			if(substr($query, 0, 12) == 'CREATE TABLE') {
				$name = preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query);
				echo '创建表 '.$name.' ... <font color="#0000EE">成功</font><br />';
				$DB->query(createtable($query, $dbcharset));
				$tablenum++;
			} else {
				$DB->query($query);
			}
		}
	}
}

function createtable($sql, $dbcharset) {
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
		(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=$dbcharset" : " TYPE=$type");
}

?>