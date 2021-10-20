<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<div class="mainbody">
  <table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
          <div class="tableheader">快捷链接</div>
          <div class="leftmenubody">
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=article&action=add">添加文章</a></div>
<!--
EOT;
if ($sax_group == 1) {print <<<EOT
-->
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=category&action=addcate">添加分类</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=link&action=add">添加链接</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=attachment&action=repair">附件修复</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=attachment&action=clear">附件清理</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=comment&action=tbclear">引用清理</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=category&action=tagclear">标签整理</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=cache&action=rebuild">重建数据</a></div>
			<div class="leftmenuitem">&#8226; <a href="admincp.php?job=cache&action=updateall">更新所有缓存</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=database&action=backup">备份数据库</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=database&action=tools">数据库维护</a></div>
<!--
EOT;
} else {print <<<EOT
-->
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=article&action=list">编辑文章</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=article&action=search">搜索文章</a></div>
<!--
EOT;
}print <<<EOT
-->
          </div>
        </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top">
        <div id="news_box" class="box" style="display:none;">
          <div id="news_title" class="alert">读取中...</div>
          <div id="news_content" class="alertmsg">读取中...</div>
        </div>
        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="rightmainbody">
              <table width="100%" border="0" align="center" cellpadding="4" cellspacing="0">
                <tr class="tdbheader">
                  <td width="30%">后台在线用户</td>
                  <td width="30%">IP地址</td>
                  <td width="40%">最后活动时间</td>
                </tr>
<!--
EOT;
foreach($onlines as $key => $online){print <<<EOT
-->
                <tr class="tablecell">
                  <td><a href="admincp.php?job=user&action=mod&userid=$online[uid]">$online[username]</a></td>
                  <td>$online[ipaddress]</td>
                  <td>$online[lastactivity]</td>
                </tr>
<!--
EOT;
}print <<<EOT
-->
                  <td class="tablebottom" colspan="3"></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
          </tr>
<!--
EOT;
if ($waponlines) {print <<<EOT
-->
          <tr>
            <td class="rightmainbody">
              <table width="100%" border="0" align="center" cellpadding="4" cellspacing="0">
                <tr class="tdbheader">
                  <td width="30%">WAP在线用户</td>
                  <td width="30%">IP地址</td>
                  <td width="40%">最后活动时间</td>
                </tr>
<!--
EOT;
foreach($waponlines as $key => $online){print <<<EOT
-->
                <tr class="tablecell">
                  <td><a href="admincp.php?job=user&action=mod&userid=$online[uid]">$online[username]</a></td>
                  <td>$online[ipaddress]</td>
                  <td>$online[lastactivity]</td>
                </tr>
<!--
EOT;
}print <<<EOT
-->
                  <td class="tablebottom" colspan="3"></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
          </tr>
<!--
EOT;
}print <<<EOT
-->
          <tr>
            <td valign="top" class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <tr class="tdbheader">
                  <td colspan="2">系统信息</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">服务器时间:</td>
                  <td width="50%">$server[datetime]</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">服务器解译引擎:</td>
                  <td width="50%">$server[software]</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">文件上传:</td>
                  <td width="50%">$fileupload</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">全局变量 register_globals:</td>
                  <td width="50%">$globals</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">安全模式 safe_mode:</td>
                  <td width="50%">$safemode</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">图形处理 GD Library:</td>
                  <td width="50%">$gd_version</td>
                </tr>
				<!--
EOT;
if ($server['memory_info']) {print <<<EOT
-->
                <tr class="tablecell">
                  <td width="50%">内存占用:</td>
                  <td width="50%">$server[memory_info]</td>
                </tr>
                <tr>
				<!--
EOT;
}print <<<EOT
-->
                  <td class="tablebottom" colspan="2"></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
          </tr>
          <tr>
            <td valign="top" class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <tr class="tdbheader">
                  <td colspan="2">数据统计</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">隐藏文章数量:</td>
                  <td width="50%">$hiddenarttatol</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">未审核评论数量:</td>
                  <td width="50%">$hiddencomtatol</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">未审核引用数量:</td>
                  <td width="50%">$hiddentracktatol</td>
                </tr>
                <tr>
                  <td class="tablebottom" colspan="2"></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
          </tr>
          <tr>
            <td valign="top" class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <tr class="tdbheader">
                  <td colspan="2">程序相关信息</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">当前版本:</td>
                  <td width="50%">$SABLOG_VERSION Build $SABLOG_RELEASE</td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">最新版本:</td>
                  <td width="50%"><span id="newest_version">读取中...</span></td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">程序开发:</td>
                  <td width="50%"><a href="mailto:4ngel@21cn.com" target="_blank" title="QQ:291427">angel</a></td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">模板设计:</td>
                  <td width="50%"><a href="mailto:4ngel@21cn.com" target="_blank" title="QQ:291427">angel</a></td>
                </tr>
                <tr class="tablecell">
                  <td width="50%">官方主页:</td>
                  <td width="50%"><a href="http://www.4ngel.net" target="_blank">http://www.4ngel.net</a>, <a href="http://www.sablog.net" target="_blank">http://www.sablog.net</a></td>
                </tr>
                <tr>
                  <td class="tablebottom" colspan="2"></td>
                </tr>
              </table></td>
          </tr>
        </table></td>
    </tr>
  </table>
</div>
<script type="text/javascript">
i=1;
var autourl=new Array();
autourl[1] = 'www.sablog.net';
autourl[2] = 'cnc.sablog.net';
function auto(url){
	if(i){
		i=0;
		var oHead = document.getElementsByTagName('head').item(0); 
		var oScript= document.createElement("script"); 
		oScript.type = "text/javascript"; 
		oScript.src = "http://"+url+"/update.php?version=$now_version&release=$now_release&hostname=$now_hostname"; 
		oHead.appendChild(oScript); 
	}
}
function run(){
	for(var i=1;i<autourl.length;i++) {
		document.write("<img src=http://"+autourl[i]+" width=1 height=1 onerror=auto('"+autourl[i]+"')>");
	}
}
run();
</script>
<!--
EOT;
?>
-->
