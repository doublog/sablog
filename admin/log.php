<?php
// ========================== 文件说明 ==========================//
// 本文件说明：后台记录
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

$action = in_array($action, array('adminlog', 'loginlog', 'deladminlog', 'delloginlog', 'dberrorlog', 'deldberrorlog')) ? $action : 'adminlog';
if (in_array($action, array('adminlog', 'deladminlog'))) {
	$logsfile = 'adminlog';
	$opname = '操作记录';
} elseif (in_array($action, array('loginlog', 'delloginlog'))) {
	$logsfile = 'loginlog';
	$opname = '登陆记录';
} elseif (in_array($action, array('dberrorlog', 'deldberrorlog'))) {
	$logsfile = 'dberrorlog';
	$opname = '数据库出错记录';
}
if (in_array($action, array('deladminlog', 'delloginlog', 'deldberrorlog'))) {
	$logfilename = SABLOG_ROOT.'cache/log/'.$logsfile.'.php';
	if(file_exists($logfilename)){
		$logfile = @file($logfilename);
	} else{
		$logfile=array();
	}
	$logs = array();
	if(is_array($logfile)) {
		foreach($logfile as $log) {
			$logs[] = $log;
		}
	}
	$logs = @array_reverse($logs);
	$tatol = count($logs);
	if ($tatol>100) {
		$output=@array_slice($logs,0,100);
		$output=@array_reverse($output);
		$output=@implode("",$output);

		@touch($logfilename);
		@$fp=fopen($logfilename,'rb+');
		@flock($fp,LOCK_EX);
		@fwrite($fp,$output);
		@ftruncate($fp,strlen($output));
		@fclose($fp);
		@chmod($filename,0777);

		redirect('多余的'.$opname.'已成功删除', 'admincp.php?job=log&action='.$logsfile);
	} else {
		redirect('记录少于100条不允许删除', 'admincp.php?job=log&action='.$logsfile);
	}
}//removelog

//管理日志页面
if (in_array($action, array('adminlog', 'loginlog', 'dberrorlog'))) {
	@$logfile = file(SABLOG_ROOT.'cache/log/'.$logsfile.'.php');
	$logs = $logdb = array();
	if(is_array($logfile)) {
		foreach($logfile as $log) {
			$logs[] = $log;
		}
	}
	$logs = @array_reverse($logs);

	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$tatol = count($logs);
	if ($tatol) {
		$multipage = multi($tatol, 30, $page, 'admincp.php?job=log&action='.$logsfile);
		for($i = 0; $i < $start_limit; $i++) {
			unset($logs[$i]);
		}
		for($i = $start_limit + 30; $i < $tatol; $i++) {
			unset($logs[$i]);
		}
		if ($action == 'adminlog') {
			foreach($logs as $logrow) {
				$logrow = explode("\t", $logrow);
				$logrow[1] = sadate('Y-m-d H:i:s', $logrow[1]);
				$logdb[] = $logrow;
			}
		} elseif ($action == 'loginlog') {
			foreach($logs as $logrow) {
				$logrow = explode("\t", $logrow);
				$logrow[1] = $logrow[1] ? htmlspecialchars($logrow[1]) : '<span class="no">Null</span>';
				$logrow[2] = sadate('Y-m-d H:i:s', $logrow[2]);
				$logrow[4] = trim($logrow[4]) == 'Succeed' ? '<span class="yes">Succeed</span>' : '<span class="no">Failed</span>';
				$logdb[] = $logrow;
			}
		} else {
			foreach($logs as $logrow) {
				$logrow = explode("\t", $logrow);
				$logrow[1] = sadate('Y-m-d H:i:s', $logrow[1]);
				$logdb[] = $logrow;
			}
		}
	}
	$subnav = $opname;
	unset($logrow);
}//end

$navlink_L = ' &raquo; <a href="admincp.php?job=log">运行记录</a>'.($subnav ? ' &raquo; '.$subnav : '');
cpheader();
include PrintEot('loging');
?>