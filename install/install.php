<?php
error_reporting(7);

define('SABLOG_ROOT', TRUE);
ob_start();
define('SABLOG_VERSION', '1.6');

// 允许程序在 register_globals = off 的环境下工作
$onoff = function_exists('ini_get') ? ini_get('register_globals') : get_cfg_var('register_globals');
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

// set_magic_quotes_runtime(0);

$step = $_GET['step'] ? $_GET['step'] : $_POST['step'];
$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
$dbcharset = 'utf8';
$configfile = '../config.php';


$sqlfile = 'install.sql';
if(!is_readable($sqlfile)) {
	exit('数据库文件不存在或者读取失败');
}
$fp = fopen($sqlfile, 'rb');
$sql = fread($fp, 2048000);
fclose($fp);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SaBlog-X安装脚本</title>
<link href="install.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="main">
  <form method="post" action="<?php echo $php_self;?>">
    <p class="title">SaBlog-X V<?php echo SABLOG_VERSION;?>安装向导</p>
    <hr noshade="noshade" />
    <?php
if (!$step || $step == '1') {
?>
    <p class="title">第一步:安装须知</p>
    <p>欢迎使用 SaBlog-X V<?php echo SABLOG_VERSION;?>，中本脚本将帮助您将程序完整地安装在您的服务器内。请您先确认以下安装配置: </p>
    <ul>
      <li>MySQL 主机名称/IP 地址 </li>
      <li>MySQL 用户名和密码 </li>
      <li>MySQL 数据库名称 (如果没有创建新数据库的权限) </li>
      <li>./admin/backupdata 目录权限为 0777 (*nix系统) </li>
      <li>./attachments 目录权限为 0777 (*nix系统) </li>
      <li>./config.php 文件权限为 0777 (*nix系统) </li>
      <li>./cache 目录权限为 0777 (*nix系统) </li>
      <li>./cache/log 目录权限为 0777 (*nix系统) </li>
    </ul>
    <p>如果您无法确认以上的配置信息, 请与您的服务商联系, 我们无法为您提供任何帮助.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="2" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
    <?php
} elseif ($step == '2') {
	
	$exist_error = FALSE;
	$write_error = FALSE;
	if (file_exists($configfile)) {
		$fileexists = result(1, 0);
	} else {
		$fileexists = result(0, 0);
		$exist_error = TRUE;
	}
	if (is_writeable($configfile)) {
		$filewriteable = result(1, 0);
	} else {
		$filewriteable = result(0, 0);
		$write_error = TRUE;
	}
	if ($exist_error) {
		$config_info = 'config.php 文件不存在, 无法继续.';
	} elseif($write_error) {
		$config_info = '安装向导无法写入配置文件, 请修改配置文件权限.';
	}
?>
    <p class="title">第二步:配置数据库信息</p>
    <p>config.php 存在检查 <?php echo $fileexists;?></p>
    <p>config.php 可写检查 <?php echo $filewriteable;?></p>
<?php
	if ($config_info) {
?>
    <p><?php echo $config_info;?></p>
    <hr noshade="noshade" />
    <p align="right">
      <input class="formbutton" type="button" value="上一步" onclick="history.back(1)" />
    </p>
<?php
	} else {
?>
    <hr noshade="noshade" />
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td width="30%" nowrap>服务器地址:</td>
        <td><input type="text" value="" name="servername" class="formfield" style="width:150px"> 一般是 localhost</td>
      </tr>
      <tr>
        <td width="30%" nowrap>数据库名:</td>
        <td><input type="text" value="" name="dbname" class="formfield" style="width:150px"></td>
      </tr>
      <tr>
        <td width="30%" nowrap>数据库用户名:</td>
        <td><input type="text" value="" name="dbusername" class="formfield" style="width:150px"></td>
      </tr>
      <tr>
        <td width="30%" nowrap>数据库用户密码:</td>
        <td><input type="password" value="" name="dbpassword" class="formfield" style="width:150px"></td>
      </tr>
      <tr>
        <td width="30%" nowrap>数据表前缀:</td>
        <td><input type="text" value="" name="db_prefix" class="formfield" style="width:150px"> 不填则默认是 sablog_</td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <p>如果您无法确认以上的配置信息, 请与您的服务商联系, 我们无法为您提供任何帮助.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="3" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
    <?php
	}
} elseif ($step == '3') {
	if(trim($_POST['dbname']) == "" || trim($_POST['servername']) == "" || trim($_POST['dbusername']) == ""){

?>
    <p>请返回并确认所有选项均已填写.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input class="formbutton" type="button" value="上一步" onclick="history.back(1)" />
    </p>
<?php
	} else {
?>
    <p class="title">第三步:设置管理员账号</p>
<?php
		if(is_writeable($configfile)) {

			$servername = trim($_POST['servername']);
			$dbusername = trim($_POST['dbusername']);
			$dbpassword = trim($_POST['dbpassword']);
			$dbname = trim($_POST['dbname']);
			$db_prefix = trim($_POST['db_prefix']);
			$db_prefix = $db_prefix ? $db_prefix : 'sablog_';

			$fp = @fopen($configfile, 'r');
			$filecontent = @fread($fp, @filesize($configfile));
			@fclose($fp);

			$filecontent = preg_replace("/[$]servername\s*\=\s*[\"'].*?[\"']/is", "\$servername = '$servername'", $filecontent);
			$filecontent = preg_replace("/[$]dbusername\s*\=\s*[\"'].*?[\"']/is", "\$dbusername = '$dbusername'", $filecontent);
			$filecontent = preg_replace("/[$]dbpassword\s*\=\s*[\"'].*?[\"']/is", "\$dbpassword = '$dbpassword'", $filecontent);
			$filecontent = preg_replace("/[$]dbname\s*\=\s*[\"'].*?[\"']/is", "\$dbname = '$dbname'", $filecontent);
			$filecontent = preg_replace("/[$]db_prefix\s*\=\s*[\"'].*?[\"']/is", "\$db_prefix = '$db_prefix'", $filecontent);

			$fp = @fopen($configfile, 'w');
			@fwrite($fp, trim($filecontent));
			@fclose($fp);

		}

		include ($configfile);
		include ('../include/func_db_mysql.php');
		$DB = new DB_MySQL;
		$DB->connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect);
		unset($servername, $dbusername, $dbpassword, $usepconnect);

		$msg = '';
		$quit = FALSE;
		$curr_os = PHP_OS;
		$curr_php_version = PHP_VERSION;
		if($curr_php_version < '4.0.6') {
			$msg .= "<font color=\"#FF0000\">您的PHP版本低于4.0.6, 无法使用 SaBlog-X</font><br />";
			$quit = TRUE;
		}

		$query = $DB->query("SELECT VERSION()");
		$curr_mysql_version = $DB->result($query, 0);

		$query = $DB->query("show tables");
        // while($result = $DB->fetch_array($query)){
        //     echo "<pre>";
        //     print_r($result);
        //     echo "</pre>";
        //
        // }

		if($curr_mysql_version < '3.23') {
			$msg .= "<font color=\"#FF0000\">您的MySQL版本低于3.23, 由于程序没有经过此平台的测试, 建议您换 MySQL4 的数据库服务器.</font><br />";
			$quit = TRUE;
		}

		if(strstr($db_prefix, '.')) {
			$msg .= "<font color=\"#FF0000\">您指定的数据表前缀包含点字符，请返回修改.</font><br />";
			$quit = TRUE;
		}

		// $DB->select_db($dbname);
		if( ($err = $DB->geterrdesc()) && $err[0] != '0000' ) {
			if($DB->version() > '4.1') {
				$DB->query("CREATE DATABASE $dbname DEFAULT CHARACTER SET $dbcharset");
			} else {
				$DB->query("CREATE DATABASE $dbname");
			}
			if($DB->geterrdesc()) {
				$msg .= "<font color=\"#FF0000\">指定的数据库不存在, 系统也无法自动建立, 无法安装 SaBlog-X.</font><br />";
				$quit = TRUE;
			} else {
				$DB->select_db($dbname);
				$msg .= "成功建立指定数据库<br />";
			}
		}

		$query = $DB->query("SELECT COUNT(*) FROM {$db_prefix}settings", 'SILENT');
        if( ($err = $DB->geterrdesc()) && $err[0] != '0000' ) {
			$msg .= "<font color=\"#FF0000\">数据库中已经安装过 SaBlog-X, 继续安装会清空原有数据.</font><br />";
			$alert = " onclick=\"return confirm('继续安装会清空全部原有数据, 您确定要继续吗?');\"";
		} else {
			$alert = '';
		}

		if($quit) {
			$msg .= "<font color=\"#FF0000\">由于您目录属性或服务器配置原因, 无法继续安装 SaBlog-X, 请仔细阅读安装说明.</font>";
		} else {
			$msg .= "您的服务器可以安装和使用 SaBlog-X, 请进入下一步安装.";
		}
		if ($msg) {
			echo "<p>".$msg."</p>";
		}
		if($quit) {
?>
    <p align="right">
      <input type="button" value="退出" onclick="javascript: window.close();">
    </p>
<?php
		} else {
?>
    <p>&nbsp;</p>
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td width="30%" nowrap>用户名:</td>
        <td><input type="text" value="" name="username" class="formfield" style="width:150px"></td>
      </tr>
      <tr>
        <td width="30%" nowrap>密码:</td>
        <td><input type="password" value="" name="password" class="formfield" style="width:150px"></td>
      </tr>
      <tr>
        <td width="30%" nowrap>确认密码:</td>
        <td><input type="password" value="" name="comfirpassword" class="formfield" style="width:150px"></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="4" />
      <input class="formbutton" type="submit" value="下一步" <?php echo $alert;?> />
    </p>
<?php
		}
	}
} elseif ($step == '4') {
	$username = addslashes(trim($_POST['username']));
	$password = $_POST['password'];
	$comfirpassword = $_POST['comfirpassword'];
?>
    <p class="title">第四步:检查信息合法性</p>
<?php
    if ($username == "" || $password == "" || $comfirpassword == "") {
		$msg = "<p>请返回并输入所有必填选项, 请返回重新输入.</p>";
		$quit = TRUE;
    } elseif (strlen($_POST['password']) < 8) {
		$msg = "<p>从系统的安全角度考虑, 密码长度不能少于8字节, 请返回重新输入.</p>";
		$quit = TRUE;
	} elseif ($password != $comfirpassword) {
		$msg = "<p>两个输入的密码不相同, 请返回重新输入.</p>";
		$quit = TRUE;
    } else {
		$msg = "<p>检查信息合法性... 成功</p>";
		$quit = FALSE;
	}
	$name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
	foreach($name_key as $value){
		if (strpos($username,$value) !== false){ 
			$msg = "<p>用户名包含敏感字符.</p>";
			$quit = TRUE;
		}
	}
	if ($quit) {
		echo $msg;
?>
    <hr noshade="noshade" />
    <p align="right">
      <input class="formbutton" type="button" value="上一步" onclick="history.back(1)" />
    </p>
<?php
	} else {
		echo $msg;
?>
    <p>&nbsp;</p>
    <p>用户名: <?php echo $username;?><input type="hidden" name="username" value="<?php echo $username;?>" /></p>
    <p>密码: <?php echo $password;?><input type="hidden" name="password" value="<?php echo $password;?>" /></p>
    <p>&nbsp;</p>
    <p>核对无误后点击下一步开始导入数据.</p>
    <hr noshade="noshade" />
    <p align="right">
      <input type="hidden" name="step" value="5" />
      <input class="formbutton" type="submit" value="下一步" />
    </p>
<?php
	}
} elseif ($step == '5') {
	$username = addslashes(trim($_POST['username']));
	$password = $_POST['password'];
?>
    <p class="title">第五步:导入数据</p>
	<p>
<?php
	include ($configfile);
	include ('../include/func_db_mysql.php');

	$DB = new DB_MySQL;
	$DB->connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect);
	unset($servername, $dbusername, $dbpassword, $usepconnect);

	runquery($sql);

	$DB->query("INSERT INTO {$db_prefix}users (username, password, groupid) VALUES ('$username', '".md5($password)."', '1')");
?>
    </p>
    <p>共创建了<?php echo $tablenum;?>个数据表.</p>
    <hr noshade="noshade" />
    <p>安装程序已经顺利执行完毕，请尽快删除整个 install 目录，以免被他人恶意利用。</p>
    <p>感谢您使用Sa系列Web应用程序.</p>
    <p>&nbsp;</p>
    <p>用户名: <?php echo $username;?></p>
    <p>密码: <?php echo $password;?></p>
    <p>&nbsp;</p>
    <p><a href="../">点击这里进入博客</a></p>
    <hr noshade="noshade" />
    <p align="right"><a href="http://www.4ngel.net">Welcome to Security Angel Team</a></p>
<?php
}
?>
  </form>
</div>
<strong>Powered by SaBlog-X <?php echo SABLOG_VERSION;?> (C) 2003-2005 Security Angel Team</strong>
</body>
</html>
<?php

function result($result = 1, $output = 1) {
	if($result) {
		$text = '... <font color="#0000EE">Yes</font><br />';
		if(!$output) {
			return $text;
		}
		echo $text;
	} else {
		$text = '... <font color="#FF0000">No</font><br />';
		if(!$output) {
			return $text;
		}
		echo $text;
	}
}

function runquery($sql) {
	global $dbcharset, $db_prefix, $DB, $tablenum;

	$sql = str_replace("\r", "\n", str_replace(' sablog_', ' '.$db_prefix, $sql));
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
    global $DB;
    $serversion = $DB->version();
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
		($serversion > '4.1' ? " ENGINE=$type DEFAULT CHARSET=$dbcharset" : " TYPE=$type");
}

?>
