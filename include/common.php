<?php
// ========================== 文件说明 ==========================//
// 本文件说明：前后台公用函数
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

error_reporting(E_ALL);
@header("content-Type: text/html; charset=UTF-8");
$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];

define('SABLOG_ROOT', substr(dirname(__FILE__), 0, -7));
require_once(SABLOG_ROOT . 'include/sax_version.php');

$php_self = addslashes(htmlspecialchars($_SERVER['PHP_SELF']
    ? $_SERVER['PHP_SELF']
    : $_SERVER['SCRIPT_NAME']));
$timestamp = time();

// 防止 PHP 5.1.x 使用时间函数报错
if(function_exists('date_default_timezone_set')){
    @date_default_timezone_set('UTC');
}

// 加载数据库配置信息
require_once(SABLOG_ROOT . 'config.php');

// 检查防刷新或代理访问
if($attackevasive){
    require_once(SABLOG_ROOT . 'include/fense.php');
}

// 加载数据库类
require_once(SABLOG_ROOT . 'include/func_db_mysql.php');
// 初始化数据库类
$DB = new DB_MySQL;
$DB->connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect);
unset($servername, $dbusername, $dbpassword, $dbname, $usepconnect);

//获得IP地址
if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')){
    $onlineip = getenv('HTTP_CLIENT_IP');
} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')){
    $onlineip = getenv('HTTP_X_FORWARDED_FOR');
} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')){
    $onlineip = getenv('REMOTE_ADDR');
} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')){
    $onlineip = $_SERVER['REMOTE_ADDR'];
}
$onlineip = addslashes($onlineip);
@preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
$onlineip = $onlineipmatches[0]??'unknown';
unset($onlineipmatches);

// 读取缓存
if(!@include(SABLOG_ROOT . 'cache/cache_settings.php')){
    require_once(SABLOG_ROOT . 'include/cache.php');
    rethestats('settings');
    exit('<p>Settings caches successfully created, please refresh.</p>');
}
if($options['gzipcompress'] && function_exists('ob_gzhandler')){
    @ob_start('ob_gzhandler');
} else{
    $options['gzipcompress'] = 0;
    ob_start();
}
!$options['templatename'] && $options['templatename'] = 'default';
$options['title'] = $options['name'];
$timeoffset = (!$options['server_timezone'] || $options['server_timezone'] == '111')
    ? 0
    : $options['server_timezone'];
//检查主机是否支持mod_rewrite
if(function_exists('apache_get_modules')){
    $apache_mod = apache_get_modules();
    if(!in_array('mod_rewrite', $apache_mod)){
        $options['rewrite_enable'] = 0;
    }
}

$cachelost = '';
$cachelost .= (@include SABLOG_ROOT . 'cache/cache_statistics.php')
    ? ''
    : 'statistics,';
$cachelost .= (@include SABLOG_ROOT . 'cache/cache_newcomments.php')
    ? ''
    : 'newcomments,';
$cachelost .= (@include SABLOG_ROOT . 'cache/cache_categories.php')
    ? ''
    : 'categories,';
$cachelost .= (@include SABLOG_ROOT . 'cache/cache_archives.php')
    ? ''
    : 'archives,';
$cachelost .= (@include SABLOG_ROOT . 'cache/cache_hottags.php')
    ? ''
    : 'hottags,';
$cachelost .= (@include SABLOG_ROOT . 'cache/cache_links.php')
    ? ''
    : 'links,';
$cachelost .= (@include SABLOG_ROOT . 'cache/cache_stylevars.php')
    ? ''
    : 'stylevars,';

if($cachelost){
    require_once(SABLOG_ROOT . 'include/cache.php');
    $cachelost = explode(',', $cachelost);
    echo '<p>Cache List:</p><p>';
    foreach($cachelost as $name){
        if($name){
            rethestats($name);
            echo $name . '<br>';
        }
    }
    exit('</p><p>Caches successfully created, please refresh.</p>');
}

$options = stripslashes_array($options);
$linkcache = stripslashes_array($linkcache);
$catecache = stripslashes_array($catecache);
if($stylevar){
    $stylevar = stripslashes_array($stylevar);
}
$newcommentcache = stripslashes_array($newcommentcache);
$page = intval($_GET['page']??0);

// 允许程序在 register_globals = off 的环境下工作
$onoff = function_exists('ini_get')
    ? ini_get('register_globals')
    : get_cfg_var('register_globals');
if($onoff != 1){
    @extract($_POST, EXTR_SKIP);
    @extract($_GET, EXTR_SKIP);
    @extract($_COOKIE, EXTR_SKIP);
}

// 去除转义字符
function stripslashes_array(&$array)
{
    if(is_array($array)){
        foreach($array as $k => $v){
            $array[$k] = stripslashes_array($v);
        }
    } else{
        if(is_string($array)){
            $array = stripslashes($array);
        }
    }
    return $array;
}

// @set_magic_quotes_runtime(0);
// 判断 magic_quotes_gpc 状态
if(@get_magic_quotes_gpc()){
    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);
}

// 系统URL
if(!$options['url']){
    //HTTP_HOST已经包含端口信息,不必加SERVER_PORT了.
    $options['url'] = 'http://' . $_SERVER['HTTP_HOST'] . dirname($php_self) . '/';
} else{
    $options['url'] = str_replace(['{host}', 'index.php'], [$_SERVER['HTTP_HOST'], ''], $options['url']);
    if(substr($options['url'], -1) != '/'){
        $options['url'] = $options['url'] . '/';
    }
}
//文章排列依据
$article_order = in_array($options['article_order'], ['dateline', 'articleid'])
    ? $options['article_order']
    : 'dateline';

//前台身份验证
list($sax_uid, $sax_pw, $logincount) = $_COOKIE['sax_auth']
    ? explode("\t", authcode($_COOKIE['sax_auth'], 'DECODE'))
    : ['', '', ''];
$sax_uid = intval($sax_uid);
$sax_pw = addslashes($sax_pw);
$sax_group = 4;
if(!$sax_uid || !$sax_pw){
    $sax_uid = 0;
} else{
    $user = $DB->fetch_one_array("SELECT username,password,logincount,groupid FROM {$db_prefix}users WHERE userid='$sax_uid'");
    if($user['password'] == $sax_pw && $user['logincount'] == $logincount){
        $sax_user = $user['username'];
        $sax_group = $user['groupid'];
    } else{
        $sax_uid = 0;
        $sax_pw = '';
        setcookie('sax_auth', '');
    }
}
if($sax_group == 1){
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}
$formhash = formhash();
//身份验证结束

//格式化时间
function sadate($format, $timestamp = '')
{
    global $options, $timeoffset;
    !$timestamp && $timestamp = time();
    return gmdate($format, $timestamp + $timeoffset * 3600);
}

// 获得散列
function formhash()
{
    global $sax_user, $sax_uid, $sax_pw, $timestamp;
    return substr(md5(substr($timestamp, 0, -7) . $sax_user . $sax_uid . $sax_pw), 8, 8);
}

//获得某年某月的时间戳
function gettimestamp($year, $month)
{
    /*
        $start = mktime(0,0,0,$month,1,$year);
        if ($month == 12) {
            $nextyear = $year + 1;
            $nextmonth = $month;
        } else {
            $nextmonth = $month + 1;
            $nextyear = $year;
        }
        $end = mktime(0,0,0,$nextmonth,1,$nextyear);
    */
    $start = strtotime($year . '-' . $month . '-1');
    if($month == 12){
        $endyear = $year + 1;
        $endmonth = 1;
    } else{
        $endyear = $year;
        $endmonth = $month + 1;
    }
    $end = strtotime($endyear . '-' . $endmonth . '-1');
    return $start . '-' . $end;
}

function correcttime($timestamp)
{
    global $timeoffset;
    $z = date('Z');
    if($z != '0'){
        $timestamp = $timestamp - ($z - $timeoffset * 3600);
    } else{
        $timestamp = $timestamp - $timeoffset * 3600;
    }
    return $timestamp;
}

//截取字数
function trimmed_title($text, $limit = 12)
{
    if($limit){
        $val = csubstr($text, 0, $limit);
        return $val[1]
            ? $val[0] . "..."
            : $val[0];
    } else{
        return $text;
    }
}

//判断是否为邮件地址
function isemail($email)
{
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

function csubstr($text, $start = 0, $limit = 12)
{
    if(function_exists('mb_substr')){
        $more = (mb_strlen($text, 'UTF-8') > $limit)
            ? true
            : false;
        $text = mb_substr($text, 0, $limit, 'UTF-8');
        return [$text, $more];
    } elseif(function_exists('iconv_substr')){
        $more = (iconv_strlen($text) > $limit)
            ? true
            : false;
        $text = iconv_substr($text, 0, $limit, 'UTF-8');
        return [$text, $more];
    } else{
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $text, $ar);
        if(func_num_args() >= 3){
            if(count($ar[0]) > $limit){
                $more = true;
                $text = join("", array_slice($ar[0], 0, $limit)) . "...";
            } else{
                $more = false;
                $text = join("", array_slice($ar[0], 0, $limit));
            }
        } else{
            $more = false;
            $text = join("", array_slice($ar[0], 0));
        }
        return [$text, $more];
    }
}

//转换字符
function char_cv($string)
{
    $string = htmlspecialchars(addslashes($string));
    return $string;
}

//页面输出
function PageEnd()
{
    global $options;
    $output = str_replace(['<!--<!---->', '<!---->'], ['', ''], ob_get_contents());
    ob_end_clean();
    $options['gzipcompress']
        ? ob_start('ob_gzhandler')
        : ob_start();
    if($options['rewrite_enable']){
        require_once(SABLOG_ROOT . 'include/func_rewrite.php');
        $searcharray = [
            "/\<a href\=\"(\.*\/*)\?action\=index&amp;cid\=(\d+)(&amp;page\=(\d+))?\"( class\=\"(.+?)\")?\>/e",
            "/\<a href\=\"(\.*\/*)\?action\=index&amp;setdate\=(\d+)(&amp;page\=(\d+))?\"( class\=\"(.+?)\")?\>/e",
            "/\<a href\=\"(\.*\/*)\?action\=show&amp;id\=(\d+)(&amp;page\=(\d+))?(#(\w+))?\"( class\=\"(.+?)\")?\>/e",
            "/\<a href\=\"(\.*\/*)\?action\=(search|reg|login|archives|comments|tagslist|trackbacks|index|links)(&amp;page\=(\d+))?\"( class\=\"(.+?)\")?\>/e",
        ];
        $replacearray = [
            "rewrite_category('\\1', '\\2', '\\4', '\\6')",
            "rewrite_archives('\\1', '\\2', '\\4', '\\6')",
            "rewrite_show('\\1', '\\2', '\\4', '\\6', '\\8')",
            "rewrite_page('\\1', '\\2', '\\4', '\\6')",
        ];
        /*
        //正则真他妈烦人
        */
        // $output = preg_replace($searcharray, $replacearray, $output);
        $output = preg_replace_callback_array([
            "/\<a href\=\"(\.*\/*)\?action\=index&amp;cid\=(\d+)(&amp;page\=(\d+))?\"( class\=\"(.+?)\")?\>/"                                                 => function(&$matches) {
                return rewrite_category($matches[1], $matches[2], $matches[4], $matches[6]);
			},
            "/\<a href\=\"(\.*\/*)\?action\=index&amp;setdate\=(\d+)(&amp;page\=(\d+))?\"( class\=\"(.+?)\")?\>/"                                             => function(&$matches) {
                return rewrite_archives($matches[1], $matches[2], $matches[4], $matches[6]);
			},
            "/\<a href\=\"(\.*\/*)\?action\=show&amp;id\=(\d+)(&amp;page\=(\d+))?(#(\w+))?\"( class\=\"(.+?)\")?\>/"                                          => function(&$matches) {
                return rewrite_show($matches[1], $matches[2], $matches[4], $matches[6], $matches[8]);
			},
            "/\<a href\=\"(\.*\/*)\?action\=(search|reg|login|archives|comments|tagslist|trackbacks|index|links)(&amp;page\=(\d+))?\"( class\=\"(.+?)\")?\>/" => function(&$matches) {
                return rewrite_page($matches[1], $matches[2], $matches[4], $matches[6]);
			},
        ], $output);
    }
    echo $output;
    exit;
}

// base64编码函数
function authcode($string, $operation = 'ENCODE')
{
    $string = $operation == 'DECODE'
        ? base64_decode($string)
        : base64_encode($string);
    return $string;
}

/*
if ($options['active_plugins']) {
	//如果设置了插件
	$plugins = unserialize($options['active_plugins']);
	if (is_array($plugins)) {
		//遍历插件名字
		foreach ($plugins as $key => $plugin) {
			//存在并且不包含..才包含文件
			if ($plugin && file_exists(SABLOG_ROOT.'plugins/'.$plugin) && strpos($plugin,'..')===false) {
				include_once(SABLOG_ROOT.'plugins/'.$plugin);
			} else {
				//否则删除插件纪录
				unset($plugins[$key]);
				if ($plugins){
					$plugins = addslashes(serialize($plugins));
				}
				require_once(SABLOG_ROOT.'include/cache.php');
				$DB->query("REPLACE INTO {$db_prefix}settings VALUES ('active_plugins', '$plugins')");
				settings_recache();
			}
		}
	}
}
print_r($_GET);
echo '<hr>';
print_r($_POST);
echo '<hr>';
print_r($_REQUEST);
*/
?>
