<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<div class="mainbody"><!--
EOT;
if (in_array($action, array('loginlog', 'adminlog', 'dberrorlog'))) {print <<<EOT
--><p align="right"><b><a onclick="if(!confirm('此操作会只保留最新的100条{$opname},而将其他更早的记录删除.确定吗?')) return false;" href="admincp.php?job=log&action=del{$action}">删除多余{$opname}</a></b></p>
<!--
EOT;
}print <<<EOT
-->
  <table border="0"  cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
        <div class="tableheader">后台记录</div>
        <div class="leftmenubody">
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=log&action=adminlog">操作记录</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=log&action=loginlog">登陆记录</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=log&action=dberrorlog">数据库出错记录</a></div>
        </div>
      </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top">
	  <form action="admincp.php?job=log" method="POST" name="form"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	  <tr><td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
<!--
EOT;
if($action == 'adminlog') {print <<<EOT
-->
        <tr class="tdbheader">
          <td nowrap="nowrap">用户</td>
          <td nowrap="nowrap">IP地址</td>
          <td nowrap="nowrap">访问时间</td>
          <td nowrap="nowrap">访问模块</td>
          <td nowrap="nowrap">操作</td>
        </tr>
<!--
EOT;
foreach($logdb as $key => $log){print <<<EOT
-->
        <tr class="tablecell">
          <td nowrap="nowrap">$log[2]</td>
          <td nowrap="nowrap">$log[3]</td>
          <td nowrap="nowrap">$log[1]</td>
          <td nowrap="nowrap"><a href="?job=$log[5]">$log[5]</a></td>
          <td nowrap="nowrap">$log[4]</td>
        </tr>
<!--
EOT;
}} elseif ($action == "loginlog") {print <<<EOT
-->
        <tr class="tdbheader">
          <td nowrap="nowrap">用户名</td>
          <td nowrap="nowrap">登陆时间</td>
          <td nowrap="nowrap">IP地址</td>
          <td nowrap="nowrap">登陆结果</td>
        </tr>
<!--
EOT;
foreach($logdb as $key => $log){print <<<EOT
-->
        <tr class="tablecell">
          <td nowrap="nowrap">$log[1]</td>
          <td nowrap="nowrap">$log[2]</td>
          <td nowrap="nowrap">$log[3]</td>
          <td nowrap="nowrap">$log[4]</td>
        </tr>
<!--
EOT;
}} elseif ($action == "dberrorlog") {print <<<EOT
-->
        <tr class="tdbheader">
          <td nowrap="nowrap">时间</td>
          <td nowrap="nowrap">IP地址</td>
          <td nowrap="nowrap">文件</td>
          <td nowrap="nowrap">错误描述</td>
          <td nowrap="nowrap">SQL语句</td>
        </tr>
<!--
EOT;
foreach($logdb as $key => $log){print <<<EOT
-->
        <tr class="tablecell">
          <td nowrap="nowrap">$log[1]</td>
          <td nowrap="nowrap">$log[2]</td>
          <td nowrap="nowrap">$log[3]</td>
          <td>$log[4]</td>
          <td>$log[5]</td>
        </tr>
<!--
EOT;
}}print <<<EOT
-->
        <tr class="tablecell">
          <td colspan="5" nowrap="nowrap" class="tablecelllight"><div class="records">记录:$tatol</div>
                  <div class="multipage">$multipage</div></td>
        </tr>
    <tr>
      <td class="tablebottom" colspan="5"></td>
    </tr>
      </table></td>
    </tr>
  </table>
</form></td>
    </tr>
  </table>
</div>
<!--
EOT;
?>
-->