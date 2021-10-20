<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<script type="text/javascript">
function really(d,m) {
	if (confirm(m)) {
		window.location.href='admincp.php?job=comment&action='+d;
	}
}
</script>
<div class="mainbody">
  <p class="p_nav">[ 查看评论: 
    <!--
EOT;
if ($articleid) {print <<<EOT
-->
    <a href="admincp.php?job=comment&action=cmlist&articleid=$articleid">文章内全部</a> |
    <!--
EOT;
}print <<<EOT
-->
    <a href="admincp.php?job=comment&action=cmlist&kind=hidden&articleid=$articleid">已隐藏</a> | <a href="admincp.php?job=comment&action=cmlist&kind=display&articleid=$articleid">已显示</a> ] [ 查看引用: 
    <!--
EOT;
if ($articleid) {print <<<EOT
-->
    <a href="admincp.php?job=comment&action=tblist&articleid=$articleid">文章内全部</a> |
    <!--
EOT;
}print <<<EOT
-->
    <a href="admincp.php?job=comment&action=tblist&kind=display&articleid=$articleid">已显示</a> | <a href="admincp.php?job=comment&action=tblist&kind=hidden&articleid=$articleid">已隐藏</a> ]</p>
  <table border="0"  cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
          <div class="tableheader">评论管理</div>
          <div class="leftmenubody">
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=comment">评论管理</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=comment&action=delallcm"><font color="#FF0000">删除全部</font></a></div>
            <div class="leftmenuitem">&#8226; <a href="###" onclick="really('displayall','你确定要显示全部评论吗?')">显示全部</a></div>
            <div class="leftmenuitem">&#8226; <a href="###" onclick="really('hiddenall','你确定要隐藏全部评论吗?')">隐藏全部</a></div>
          </div>
        </div>
        <div class="tableborder">
          <div class="tableheader">引用管理</div>
          <div class="leftmenubody">
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=comment&action=tblist">引用管理</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=comment&action=tbclear">清理引用</a></div>
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=comment&action=tbsendlog">发送记录</a></div>
          </div>
        </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top"><form action="admincp.php?job=comment" method="POST" name="form">
          <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <!--
EOT;
if($action == 'cmlist'){print <<<EOT
-->
                <tr class="tdbheader">
                  <td nowrap width="6%">状态</td>
                  <td nowrap width="10%">作者</td>
                  <td nowrap width="20%">联系方法</td>
                  <td nowrap width="10%">IP</td>
                  <td nowrap width="10%">时间</td>
                  <td nowrap width="34%">内容</td>
                  <td nowrap width="2%"><input name="chkall" value="on" type="checkbox" onClick="checkall(this.form)"></td>
                </tr>
                <!--
EOT;
foreach($commentdb as $key => $comment){print <<<EOT
-->
                <tr class="tablecell">
                  <td nowrap><a href="admincp.php?job=comment&action=cmvisible&commentid=$comment[commentid]">$comment[visible]</a></td>
                  <td nowrap>$comment[author]</td>
                  <td nowrap>$comment[url]</td>
                  <td nowrap><a href="admincp.php?job=comment&action=cmlist&ip=$comment[ipaddress]" title="查看此IP同一C段发表的评论">$comment[ipaddress]</a></a></td>
                  <td nowrap>$comment[dateline]</td>
                  <td nowrap><a href="admincp.php?job=comment&action=modcm&commentid=$comment[commentid]">$comment[content]</a></td>
                  <td nowrap><input type="checkbox" name="comment[]" value="$comment[commentid]"></td>
                </tr>
                <!--
EOT;
}print <<<EOT
-->
                <input type="hidden" name="articleid" value="$articleid">
                <tr class="tablecell">
                  <td colspan="9" nowrap="nowrap"><div class="records">记录:$tatol</div>
                    <div class="multipage">$multipage</div></td>
                </tr>
                <!--
EOT;
} elseif ($action == 'modcm') {print <<<EOT
-->
                <tr class="tdbheader">
                  <td colspan="2"><a name="编辑评论"></a>编辑评论</td>
                </tr>
                <tr class="tablecell">
                  <td>所在文章:</td>
                  <td><a href="admincp.php?job=article&action=mod&articleid=$comment[articleid]">$comment[title]</a></td>
                </tr>
                <tr class="tablecell">
                  <td>评论作者:</td>
                  <td><input class="formfield" type="text" name="author" size="50" value="$comment[author]"></td>
                </tr>
                <tr class="tablecell">
                  <td>评论作者联系方式:</td>
                  <td><input class="formfield" type="text" name="url" size="50" value="$comment[url]"></td>
                </tr>
                <tr class="tablecell">
                  <td valign="top">评论内容:</td>
                  <td><textarea class="formarea" type="text" name="content" cols="75" rows="20">$comment[content]</textarea></td>
                </tr>
                <input type="hidden" name="commentid" value="$comment[commentid]">
                <input type="hidden" name="articleid" value="$comment[articleid]">
                <input type="hidden" name="action" value="domodcm">
                <tr class="tablecell">
                  <td colspan="2" align="center"><input type="submit" value="提交" class="formbutton">
                    <input type="reset" value="重置" class="formbutton">
                  </td>
                </tr>
                <!--
EOT;
} elseif ($action == 'delallcm') {print <<<EOT
-->
                <tr class="alertheader">
                  <td>删除所有评论</td>
                </tr>
                <tr>
                  <td class="alertbox"><p><b>如果不想显示所有评论,隐藏所有评论即可.</b></p>
                    <p><b>这是危险操作且不可恢复,确实要删除所有评论吗?</b></p>
                    <p>
                      <input type="submit" value="确认" class="formbutton">
                      <input type="hidden" name="action" value="dodelallcm">
                    </p></td>
                </tr>
                <!--
EOT;
} elseif(in_array($action, array('tblist', 'tbsendlog'))){
if($action == 'tblist'){print <<<EOT
-->
                <tr class="tdbheader">
                  <td width="4%" nowrap>状态</td>
                  <td width="5%" nowrap>分数</td>
                  <td width="33%" nowrap>标题</td>
                  <td width="18%" nowrap>来源</td>
                  <td width="14%" nowrap>IP</td>
                  <td width="14%" nowrap>时间</td>
                  <td width="4%" nowrap>文章</td>
                  <td width="2%" nowrap><input name="chkall" type="checkbox" onclick="checkall(this.form)" value="on"></td>
                </tr>
                <!--
EOT;
foreach($trackbackdb as $key => $trackback){print <<<EOT
-->
                <tr class="tablecell">
                  <td nowrap><a href="admincp.php?job=comment&action=tbvisible&trackbackid=$trackback[trackbackid]">$trackback[visible]</a></td>
                  <td>$trackback[point]</td>
                  <td><a href="$trackback[url]" target="_blank">$trackback[title]</a></td>
                  <td nowrap><a href="$trackback[url]" target="_blank">$trackback[blog_name]</a></td>
                  <td nowrap><a href="admincp.php?job=comment&action=tblist&ip=$trackback[ipaddress]" title="查看此IP同一C段发送的引用">$trackback[ipaddress]</a></a></td>
                  <td nowrap>$trackback[dateline]</td>
                  <td nowrap><a title="$trackback[article]" href="../?action=show&id=$trackback[articleid]" target="_blank">查看</a></td>
                  <td nowrap><input type="checkbox" name="trackback[]" value="$trackback[trackbackid]">
                  </td>
                </tr>
                <!--
EOT;
}} elseif ($action == 'tbsendlog') {
if ($articleid) {print <<<EOT
-->
                <input type="hidden" name="action" value="sendpacket">
                <input type="hidden" name="articleid" value="$articleid">
                <tr class="tdbheader">
                  <td colspan="2">发送引用</td>
                </tr>
                <tr class="tablecell">
                  <td>发送文章: </td>
                  <td><a href="admincp.php?job=article&action=mod&articleid=$articleid">$article[title]</a></td>
                </tr>
                <tr class="tablecell">
                  <td>发送地址:</td>
                  <td><input class="formfield" type="text" name="pingurl" size="70" maxlength="255" value=""></td>
                </tr>
                <tr class="tablecell">
                  <td colspan="2" align="center"><input type="submit" value="提交" class="formbutton"></td>
                </tr>
                <tr>
                  <td class="tablebottom" colspan="2"></td>
                </tr>
              </table>
        </form></td>
    </tr>
    <tr>
      <td height="20"></td>
    </tr>
    <tr>
    
    <td valign="top" class="rightmainbody">
    <form action="admincp.php?job=comment" method="POST">
    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
      <!--
EOT;
}print <<<EOT
-->
      <input type="hidden" name="articleid" value="$articleid">
      <tr class="tdbheader">
        <td width="35%"><b>发送文章</b></td>
        <td width="45%"><b>发送目标地址</b></td>
        <td width="18%"><b>发送时间</b></td>
        <td width="2%" nowrap><input name="chkall" type="checkbox" onclick="checkall(this.form)" value="on"></td>
      </tr>
      <!--
EOT;
foreach($tblogdb as $key => $tblog){print <<<EOT
-->
      <tr class="tablecell">
        <td nowrap><a href="admincp.php?job=article&action=mod&articleid=$tblog[articleid]">$tblog[title]</a></td>
        <td nowrap><a href="$tblog[pingurl]" target="_blank">$tblog[showurl]</a></td>
        <td nowrap>$tblog[dateline]</td>
        <td nowrap><input type="checkbox" name="trackbacklog[]" value="$tblog[trackbacklogid]">
        </td>
      </tr>
      <!--
EOT;
}}print <<<EOT
-->
      <tr class="tablecell">
        <td colspan="9" nowrap><div class="records">记录:$tatol</div>
          <div class="multipage">$multipage</div></td>
      </tr>
      <!--
EOT;
} elseif ($action == 'tbclear') {print <<<EOT
-->
      <input type="hidden" name="action" value="dotbclear">
      <tr class="alertheader">
        <td>删除所有隐藏引用</td>
      </tr>
      <tr>
        <td class="alertbox"><p><b>注意:输入ID后将删除包括此ID之后的所有隐藏和显示的Trackback,不输入或输入任何非数字字符则只删除所有隐藏的Trackback.</b></p>
          <p>输入ID范围:
            <input class="formfield" type="text" name="delid" size="5" value="">
            可以把鼠标放到引用的状态上,从状态栏获取ID.<br />
          </p>
          <p><b>此操作不可恢复.确定吗?</b></p>
          <p>
            <input type="submit" value="确认" class="formbutton">
          </p></td>
      </tr>
      <!--
EOT;
}print <<<EOT
-->
      <tr>
        <td class="tablebottom" colspan="9"></td>
      </tr>
    </table>
    </td>
    
    </tr>
    
  </table>
  <!--
EOT;
if (in_array($action, array('cmlist', 'tblist', 'tbsendlog'))) {print <<<EOT
-->
  <table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" height="40">
    <tr>
      <td align="right"><select name="do">
          <option value="">= 管理操作 =</option>
          <!--
EOT;
if ($action == 'cmlist' || $action == 'tblist') {print <<<EOT
-->
          <option value="hidden">隐藏选定</option>
          <option value="display">显示选定</option>
          <!--
EOT;
}print <<<EOT
-->
          <option value="del">删除选定</option>
        </select>
        <input type="submit" value="确定" class="formbutton">
        <input type="hidden" name="action" value="domore{$action}"></td>
    </tr>
  </table>
  <!--
EOT;
}print <<<EOT
-->
  </form>
  </td>
  </tr>
  </table>
</div>
<!--
EOT;
?>
-->
