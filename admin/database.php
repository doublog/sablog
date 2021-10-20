<?php
// ========================== 文件说明 ==========================//
// 本文件说明：数据管理
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

$backupdir = 'backupdata';

$tables = array(
	$db_prefix.'articles',
	$db_prefix.'attachments',
	$db_prefix.'categories',
	$db_prefix.'comments',
	$db_prefix.'links',
	$db_prefix.'searchindex',
	$db_prefix.'settings',
	$db_prefix.'sessions',
	$db_prefix.'statistics',
	$db_prefix.'stylevars',
	$db_prefix.'tags',
	$db_prefix.'trackbacklog',
	$db_prefix.'trackbacks',
	$db_prefix.'users'
);

// 恢复数据库文件
if ($action == 'resume') {
	$sqlfile = $_GET['sqlfile'] ? $_GET['sqlfile'] : $_POST['sqlfile'];
	$file = $backupdir.'/'.$sqlfile;
	$path_parts = pathinfo($file);
	if (strtolower($path_parts['extension']) != 'sql') {
		redirect('只能恢复SQL文件!','admincp.php?job=database&action=filelist');
	}
	if(@$fp = fopen($file, 'rb')) {
		$sqldump = fgets($fp, 256);
		$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", $sqldump)));
		$sqldump .= fread($fp, filesize($file));
		fclose($fp);
	} else {
		if($autoimport) {
			restats();
			redirect('分卷数据成功导入数据库','admincp.php?job=database&action=filelist');
		} else {
			redirect('分卷数据导入数据库失败','admincp.php?job=database&action=filelist');
		}
	}

	if($identify[0] && $identify[1] == $SABLOG_VERSION && $identify[2]) {
		$sqlquery = splitsql($sqldump);
		unset($sqldump);
		foreach($sqlquery as $sql) {
			if(trim($sql) != '') {
				$DB->query($sql, 'SILENT');
			}
		}

		$file_next = basename(preg_replace("/_($identify[2])(\..+)$/", "_".($identify[2] + 1)."\\2", $file));

		if($identify[2] == 1) {
			redirect('分卷数据成功导入数据库,程序将自动导入本次其他的备份.','admincp.php?job=database&action=resume&sqlfile='.rawurlencode($file_next).'&autoimport=yes');
		} elseif($autoimport) {
			redirect('数据文件卷号 '.$identify[2].' 成功导入，程序将自动继续。', 'admincp.php?job=database&action=resume&sqlfile='.rawurlencode($file_next).'&autoimport=yes');
		} else {
			restats();
			redirect('数据成功导入','admincp.php?job=database&action=filelist');
		}
	} else {
		redirect('数据文件非 Sablog-X 格式或与程序当前版本信息不符,无法导入.','admincp.php?job=database&action=filelist',5);
	}
}

// 备份操作
if ($action == 'dobackup') {
	$volume = intval($volume) + 1;
	$sqlfilename = 'backupdata/'.$filename.'_'.$volume.'.sql';

	if(!$sqlfilename || preg_match("/(\.)(exe|jsp|asp|asa|htr|stm|shtml|php3|aspx|cgi|fcgi|pl|php|bat)(\.|$)/i", $sqlfilename)) {
		redirect('您没有输入备份文件名或文件名中使用了敏感的扩展名.', 'admincp.php?job=database&action=backup',5);
	}

	$idstring = '# Identify: '.base64_encode("$timestamp,$SABLOG_VERSION,$volume")."\n";

	//清除表内临时的数据
	$DB->unbuffered_query("TRUNCATE TABLE {$db_prefix}searchindex");

	$sqlcompat = in_array($sqlcompat, array('MYSQL40', 'MYSQL41')) ? $sqlcompat : '';
	$dumpcharset = str_replace('-', '', $charset);
	$setnames = intval($addsetnames) || ($DB->version() > '4.1' && (!$sqlcompat || $sqlcompat == 'MYSQL41')) ? "SET character_set_connection=".$dumpcharset.", character_set_results=".$dumpcharset.", character_set_client=binary;\n\n" : '';

	if($DB->version() > '4.1') {
		$DB->query("SET character_set_connection=$dumpcharset, character_set_results=$dumpcharset, character_set_client=binary;");
		if($sqlcompat == 'MYSQL40') {
			$DB->query("SET SQL_MODE='MYSQL40'");
		}
	}
		
	$sqldump = '';
	$tableid = $tableid ? $tableid - 1 : 0;
	$startfrom = intval($startfrom);
	for($i = $tableid; $i < count($tables) && strlen($sqldump) < $sizelimit * 1000; $i++) {
		$sqldump .= sqldumptable($tables[$i], $startfrom, strlen($sqldump));
		$startfrom = 0;
	}
	$tableid = $i;
	if(trim($sqldump)) {
		$sqldump = "$idstring".
			"# <?exit();?>\n".
			"# Sablog-X bakfile Multi-Volume Data Dump Vol.$volume\n".
			"# Version: $SABLOG_VERSION\n".
			"# Time: ".sadate('Y-m-d H:i',$timestamp)."\n".
			"# Sablog-X: http://www.sablog.net\n".
			"# --------------------------------------------------------\n\n\n".$setnames.$sqldump;

		@$fp = fopen($sqlfilename, 'wb');
		@flock($fp, 2);
		if(@!fwrite($fp, $sqldump)) {
			@fclose($fp);
			redirect('数据文件无法备份到服务器, 请检查目录属性.', 'admincp.php?job=database&action=backup',5);
		} else {
			redirect('分卷备份:数据文件 '.$volume.' 成功创建,程序将自动继续.', "admincp.php?job=database&action=dobackup&filename=".rawurlencode($filename)."&sizelimit=".rawurlencode($sizelimit)."&volume=".rawurlencode($volume)."&tableid=".rawurlencode($tableid)."&startfrom=".rawurlencode($startrow)."&extendins=".rawurlencode($extendins)."&sqlcompat=".rawurlencode($sqlcompat));
		}
	} else {
		redirect('数据成功备份至服务器指定文件中', 'admincp.php?job=database&action=filelist');
	}

}// 备份操作结束

// 导入RSS
if ($action == 'importrss') {
	$cid = intval($_POST['cid']);
	if (!$cid) {
		redirect('请选择目标分类', 'admincp.php?job=database&action=rssimport');
	}
	$uid = intval($_POST['uid']);
	if (!$uid) {
		redirect('请选择文章作者', 'admincp.php?job=database&action=rssimport');
	}
	$xmlfile = $_FILES['xmlfile'];
	if (is_array($xmlfile)) {
		$attachment      = $xmlfile['tmp_name'];
		$attachment_name = $xmlfile['name'];
		$attachment_size = $xmlfile['size'];
		$attachment_type = $xmlfile['type'];
	}
	if (trim($attachment) != 'none' && trim($attachment) != '' && trim($attachment_name) != '') {
		$rssinfo = pathinfo($attachment_name);
		if ($rssinfo['extension'] == 'xml') {
			$attachment = upfile($attachment, SABLOG_ROOT.'cache/rss_xml_tmp.xml');
			// 如果一种函数上传失败，还可以用其他函数上传
			if (!$attachment) {
				redirect('上传XML文件发生意外错误!');
			}
			$fp = fopen($attachment, 'rb'); 
			$filecontent = fread($fp, filesize($attachment));
			fclose($fp);

			$rssdata = getrssdata($filecontent);
			$i = 0;
			if (is_array($rssdata)) {
				foreach ($rssdata as $rss) {
					if ($rss['title'] && $rss['dateline'] && $rss['content']) {
						$i++;
						$DB->query("INSERT INTO {$db_prefix}articles (cid, uid, title, content, dateline) VALUES ('$cid', '$uid', '".$rss['title']."', '".$rss['content']."', '".$rss['dateline']."')");
					}
				}
			}
			@unlink($attachment);

			$DB->unbuffered_query("UPDATE {$db_prefix}users SET articles=articles+$i WHERE userid='$uid'");
			$DB->unbuffered_query("UPDATE {$db_prefix}categories SET articles=articles+$i WHERE cid='$cid'");
			$DB->unbuffered_query("UPDATE {$db_prefix}statistics SET article_count=article_count+$i");
			archives_recache();
			categories_recache();
			statistics_recache();

			redirect('导入RSS数据成功', 'admincp.php?job=article');
		} else {
			redirect('只允许上传XML格式的文件', 'admincp.php?job=database&action=rssimport');
		}
	} else {
		redirect('请选择要上传的XML文件', 'admincp.php?job=database&action=rssimport');
	}
}

function sqldumptable($table, $startfrom = 0, $currsize = 0) {
	global $DB, $sizelimit, $startrow, $extendins, $sqlcompat, $dumpcharset;

	$offset = 300;
	$tabledump = '';

	if(!$startfrom) {
		$tabledump = "DROP TABLE IF EXISTS $table;\n";
		$createtable = $DB->query("SHOW CREATE TABLE $table");
		$create = $DB->fetch_row($createtable);
		$tabledump .= $create[1];

		if($sqlcompat == 'MYSQL41' && $DB->version() < '4.1') {
			$tabledump = preg_replace("/TYPE\=(.+)/", "ENGINE=\\1 DEFAULT CHARSET=".$dumpcharset, $tabledump);
		}
		if($DB->version() > '4.1' && $dumpcharset) {
			$tabledump = preg_replace("/(DEFAULT)*\s*CHARSET=.+/", "DEFAULT CHARSET=".$dumpcharset, $tabledump);
		}

		$query = $DB->query("SHOW TABLE STATUS LIKE '$table'");
		$tablestatus = $DB->fetch_array($query);
		$tabledump .= ($tablestatus['Auto_increment'] ? " AUTO_INCREMENT=$tablestatus[Auto_increment]" : '').";\n\n";
		if($sqlcompat == 'MYSQL40' && $DB->version() >= '4.1') {
			if($tablestatus['Auto_increment'] <> '') {
				$temppos = strpos($tabledump, ',');
				$tabledump = substr($tabledump, 0, $temppos).' auto_increment'.substr($tabledump, $temppos);
			}
		}
	}

	$tabledumped = 0;
	$numrows = $offset;
	if($extendins == '0') {
		while($currsize + strlen($tabledump) < $sizelimit * 1000 && $numrows == $offset) {
			$tabledumped = 1;
			$rows = $DB->query("SELECT * FROM $table LIMIT $startfrom, $offset");
			$numfields = $DB->num_fields($rows);
			$numrows = $DB->num_rows($rows);
			while($row = $DB->fetch_row($rows)) {
				$comma = '';
				$tabledump .= "INSERT INTO $table VALUES (";
				for($i = 0; $i < $numfields; $i++) {
					$tabledump .= $comma.'\''.mysql_escape_string($row[$i]).'\'';
					$comma = ',';
				}
				$tabledump .= ");\n";
			}
			$startfrom += $offset;
		}
	} else {
		while($currsize + strlen($tabledump) < $sizelimit * 1000 && $numrows == $offset) {
			$tabledumped = 1;
			$rows = $DB->query("SELECT * FROM $table LIMIT $startfrom, $offset");
			$numfields = $DB->num_fields($rows);
			if($numrows = $DB->num_rows($rows)) {
				$tabledump .= "INSERT INTO $table VALUES";
				$commas = '';
				while($row = $DB->fetch_row($rows)) {
					$comma = '';
					$tabledump .= $commas." (";
					for($i = 0; $i < $numfields; $i++) {
						$tabledump .= $comma.'\''.mysql_escape_string($row[$i]).'\'';
						$comma = ',';
					}
					$tabledump .= ')';
					$commas = ',';
				}
				$tabledump .= ";\n";
			}
			$startfrom += $offset;
		}
	}

	$startrow = $startfrom;
	$tabledump .= "\n";
	return $tabledump;
}

//获得的全部RSS内容并列入数组
function getrssdata($data) {
	$data = str_replace(array("\r","\n",'<![CDATA[',']]>'),'',$data);
	preg_match_all("/<item>(.+?)<\/item>/is", $data, $article);

	$rssdb = $article[1];
	$articledb = array();
	if (!is_array($rssdb)) {
		$articledb[]=parserss($rssdb);
	} else {
		foreach ($rssdb as $rss) {
			$articledb[]=parserss($rss);
		}
	}
	return $articledb;
}

//分析出RSS的每篇文章
function parserss($rssdata) {
	global $options,$timeoffset;
	if (preg_match("/<title>(.+?)<\/title>/is", $rssdata, $match)) {
		$title = addslashes($match[1]);
	}
	if (preg_match("/<pubDate>(.+?)<\/pubDate>/is", $rssdata, $match)) {
		$dateline = strtotime($match[1])-$timeoffset*3600;
	}
	if (preg_match("/<content:encoded>(.+?)<\/content:encoded>/is", $rssdata, $match)) {
	} else {
		preg_match("/<description>(.+?)<\/description>/is", $rssdata, $match);
	}
	$content = addslashes($match[1]);
	return array('title'=>$title, 'dateline'=>$dateline, 'content'=>$content);
}

//批量删除备份文件
if($action == 'deldbfile') {
    if (!$sqlfile || !is_array($sqlfile)) {
        redirect('未选择任何文件');
    }
	$selected = count($sqlfile);
	$succ = $fail = 0;
    foreach ($sqlfile AS $file=>$value) {
		if (file_exists($file)) {
			@chmod($file, 0777);
			if (@unlink($file)) {
				$succ++;
			} else {
				$fail++;
			}
		} else {
			redirect(basename($file).' 文件已不存在', 'admincp.php?job=database&action=filelist');
		}
    }
    redirect('删除数据文件操作完毕,删除'.$selected.'个,成功'.$succ.'个,失败'.$fail.'个.', 'admincp.php?job=database&action=filelist',5);
}

// 数据库维护操作
if($action == 'dotools') {
	$doname = array(
		'check' => '检查',
		'repair' => '修复',
		'analyze' => '分析',
		'optimize' => '优化'
	);
	$dodb = $tabledb = array();
	foreach ($do AS $value) {
		$dodb[] = array('do'=>$value,'name'=>$doname[$value]);
		foreach ($tables AS $table) {
			if ($DB->query($value.' TABLE '.$table)) {
				$result = '<span class="yes">成功</span>';
			} else {
				$result = '<span class="no">失败</span>';
			}
			$tabledb[] = array('do'=>$value,'table'=>$table,'result'=>$result);
		}
	}
	$subnav = '数据库维护';
}// 数据库维护操作结束

// 获取文件大小
function sizecount($filesize) {
	if($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' G';
	} elseif($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . ' M';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' K';
	} else {
		$filesize = $filesize . ' bytes';
	}
	return $filesize;
}

function splitsql($sql) {
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == "#" ? NULL : $query;
		}
		$num++;
	}
	return($ret);
}

function random($length) {
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);

	$hash = '';
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}

if(!$action) {
	$action = 'mysqlinfo';
}

if (in_array($action, array('backup', 'tools'))) {
	if ($action == 'backup') {
		$backuppath = sadate('Y-m-d',$timestamp).'_'.random(8);
		$tdtitle = '备份数据库';
		$act = 'dobackup';
	} else {
		$tdtitle = '数据库维护';
		$act = 'dotools';
	}
	$subnav = ''.$tdtitle;
}//backup

// 数据库信息
if ($action == 'mysqlinfo') {
	$mysql_version = mysql_get_server_info();
	$mysql_runtime = '';
	$query = $DB->query("SHOW STATUS");
	while ($r = $DB->fetch_array($query)) {
		if (eregi("^uptime", $r['Variable_name'])){
			$mysql_runtime = $r['Value'];
		}
	}
	$mysql_runtime = format_timespan($mysql_runtime);

	$query = $DB->query("SHOW TABLE STATUS");
	$sablog_table_num = $sablog_table_rows = $sablog_data_size = $sablog_index_size = $sablog_free_size = 0;
	$other_table_num = $other_table_rows = $other_data_size = $other_index_size = $other_free_size = 0;
	$sablog_table = $other_table = array();
	while($table = $DB->fetch_array($query)) {
		if(in_array($table['Name'],$tables)) {
			$sablog_data_size = $sablog_data_size + $table['Data_length'];
			$sablog_index_size = $sablog_index_size + $table['Index_length'];
			$sablog_table_rows = $sablog_table_rows + $table['Rows'];
			$sablog_free_size = $sablog_free_size + $table['Data_free'];
			$table['Data_length'] = get_real_size($table['Data_length']);
			$table['Index_length'] = get_real_size($table['Index_length']);
			$table['Data_free'] = get_real_size($table['Data_free']);
			$sablog_table_num++;
			$sablog_table[] = $table;
		} else {
			$other_data_size = $other_data_size + $table['Data_length'];
			$other_index_size = $other_index_size + $table['Index_length'];
			$other_table_rows = $other_table_rows + $table['Rows'];
			$other_free_size = $other_free_size + $table['Data_free'];
			$table['Data_length'] = get_real_size($table['Data_length']);
			$table['Index_length'] = get_real_size($table['Index_length']);
			$table['Data_free'] = get_real_size($table['Data_free']);
			$other_table_num++;
			$other_table[] = $table;
		}
	}
	$sablog_data_size = get_real_size($sablog_data_size);
	$sablog_index_size = get_real_size($sablog_index_size);
	$sablog_free_size = get_real_size($sablog_free_size);
	$other_data_size = get_real_size($other_data_size);
	$other_index_size = get_real_size($other_index_size);
	$other_free_size = get_real_size($other_free_size);
	unset($table);
	$subnav = '数据库信息';
}

// 管理数据文件
if ($action == 'filelist') {
	$file_i = 0;
	if(is_dir($backupdir)) {
		$dirs = dir($backupdir);
		$dbfiles = array();
		$today = @sadate('Y-m-d',$timestamp);
		while ($file = $dirs->read()) {
			$filepath = $backupdir.'/'.$file;
			$pathinfo = pathinfo($file);
			if(is_file($filepath) && $pathinfo['extension'] == 'sql') {
				$fp = fopen($filepath, 'rb');
				$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 200))));
				fclose($fp);
				$moday = @sadate('Y-m-d',@filemtime($filepath));
				$mtime = @sadate('Y-m-d H:i',@filemtime($filepath));
				$dbfile = array(
					'filesize' => sizecount(filesize($filepath)),
					'mtime' => ($moday == $today) ? '<font color="#FF0000">'.$mtime.'</font>' : $mtime,
					'bktime' => $identify[0] ? @sadate('Y-m-d H:i',$identify[0]) : '未知',
					'version' => $identify[1] ? $identify[1] : '未知',
					'volume' => $identify[2] ? $identify[2] : '未知',
					'filepath' => urlencode($file),
					'filename' => htmlspecialchars($file),
				);
				$file_i++;
				$dbfiles[] = $dbfile;
			}
		}
		unset($dbfile);
		$dirs->close();
		$noexists = 0;
	} else {
		$noexists = 1;
	}
	$subnav = '数据文件管理';
} // end filelist

if ($action == 'checkresume') {
	$subnav = '导入备份数据';
	$sqlfile = htmlspecialchars($sqlfile);
	$fp = fopen($backupdir.'/'.$sqlfile, 'rb');
	$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 200))));
	fclose($fp);
	if (count($identify) != 3) {
		redirect('SQL文件有可能是当前程序的老版本备份的,为了程序程序正常运作,不允许导入.确实要导入,请通过其他MYSQL管理程序导入.', 'admincp.php?job=database&action=filelist',5);
	}
	if ($identify[2] != 1) {
		redirect('为了程序正常运作,只允许导入卷号为1的SQL文件.', 'admincp.php?job=database&action=filelist',5);
	}
	if ($identify[1] != $SABLOG_VERSION) {
		redirect('SQL文件版本信息和当前程序版本不匹配,为了程序程序正常运作,不允许导入.确实要导入,请通过其他MYSQL管理程序导入.', 'admincp.php?job=database&action=filelist',5);
	}
}//backup

if ($action == 'rssimport') {
	$subnav = '导入RSS数据';
	$catedb = array();
	$query = $DB->query("SELECT cid,name FROM {$db_prefix}categories ORDER BY displayorder");
	while ($cate = $DB->fetch_array($query)) {
		$catedb[] = $cate;		
	}
	unset($cate);

	$query = $DB->query("SELECT userid,username FROM {$db_prefix}users WHERE groupid='1' OR groupid='2' ORDER BY userid");
	$userdb = array();
	while ($user = $DB->fetch_array($query)) {
		$userdb[] = $user;
	}
	unset($user);
	$DB->free_result($query);
}//backup

$navlink_L = ' &raquo; <a href="admincp.php?job=database">数据管理</a>'.($subnav ? ' &raquo; '.$subnav : '');
cpheader();
include PrintEot('database');
?>