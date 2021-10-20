<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="keywords" content="4ngel,4ngel.net,安全,天使,安全天使,技术,黑客,网络,原创,论坛,自由,严肃,网络安全,组织,系统安全,系统,windows,web,web安全,web开发,$options[meta_keywords]" />
<meta name="description" content="4ngel,4ngel.net,安全,天使,安全天使,技术,黑客,网络,原创,论坛,自由,严肃,网络安全,组织,系统安全,系统,windows,web,web安全,web开发,$options[meta_description]" />
<meta name="copyright" content="SaBlog" />
<meta name="author" content="angel,4ngel" />
<title>控制面板</title>
<link rel="stylesheet" href="../templates/admin/cp.css" type="text/css">
<script type="text/javascript" src="js/global.js"></script>
</head>
<body>
<a name="TOP" id="TOP"></a>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="../templates/admin/images/page_bg.jpg">
  <tr>
    <td><div class="topBar">
      <table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
        <tr>
          <td class="topLinksLeft">SaBlog-X Ver $SABLOG_VERSION</td>
<!--
EOT;
if ($userinfo['username']) {print <<<EOT
-->
          <td class="topLinks">欢迎您 $userinfo[username] [<a href="admincp.php?action=logout">注销身份</a>] [<a href="admincp.php">后台首页</a>] [<a href="../index.php" target="_blank">站点首页</a>]</td>
<!--
EOT;
}print <<<EOT
-->
        </tr>
      </table>
    </div>
	<!--
EOT;
if (isset($adminitem) && $adminitem && $userinfo) {print <<<EOT
-->
    <table width="100%" height="25" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>&nbsp;</td>
<!--
EOT;
foreach ($adminitem AS $link => $title)	{
if ($job == $link) {
print <<<EOT
-->          <td width="9%" class="navcell_cure"><div class="cpnavmenu_cure" id="$link">$title</div></td>
<!--
EOT;
} else {
print <<<EOT
-->          <td width="9%" class="navcell" onMouseover="document.getElementById('$link').className='cpnavmenuHover'" onMouseout="document.getElementById('$link').className='cpnavmenu'"><div class="cpnavmenu" id="$link"><a href="admincp.php?job=$link">$title</a></div></td>
<!--
EOT;
}}print <<<EOT
-->
          <td>&nbsp;</td>
        </tr>
      </table>
<!--
EOT;
}print <<<EOT
--></td>
  </tr>
</table>
<!--
EOT;
if ($shownav) {
print <<<EOT
-->
<div class="mainbody">
<div class="navlink"><a href="admincp.php?job=main">控制面版</a>$navlink_L</div>
</div>
<!--
EOT;
}
?>
-->