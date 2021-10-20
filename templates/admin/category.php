<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<div class="mainbody">
<!--
EOT;
if ($action == 'taglist') {print <<<EOT
-->
  <p class="p_nav"><a href="admincp.php?job=category&action=taglist&ordered=usenum">使用次数</a> | <a href="admincp.php?job=category&action=taglist&ordered=tagid">ID</a></p>
<!--
EOT;
}print <<<EOT
-->
  <table border="0"  cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
        <div class="tableheader">分类管理</div>
        <div class="leftmenubody">
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=category&action=addcate">添加分类</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=category&action=catelist">分类管理</a></div>
        </div>
      </div>
	  <div class="tableborder">
        <div class="tableheader">标签管理</div>
        <div class="leftmenubody">
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=category&action=taglist">标签管理</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=category&action=tagclear">标签整理</a></div>
        </div>
      </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top">
	  <form action="admincp.php?job=category" method="POST"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	  <tr><td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
<!--
EOT;
if($action == 'catelist'){print <<<EOT
-->
    <tr class="tdbheader">
      <td width="10%" nowrap>排序</td>
      <td width="35%">名称</td>
      <td width="35%">文章数</td>
      <td width="20%" nowrap>操作</td>
    </tr>
<!--
EOT;
foreach($catedb as $key => $cate){print <<<EOT
-->
    <tr class="tablecell">
      <td nowrap><input class="formfield" style="text-align: center;font-size: 11px;" type="text" value="$cate[displayorder]" name="displayorder[$cate[cid]]" size="1"></td>
      <td><b>$cate[name]</b></td>
      <td>$cate[articles]</td>
      <td nowrap><a href="admincp.php?job=article&action=add&cid=$cate[cid]">添加文章</a> - <a href="admincp.php?job=category&action=modcate&cid=$cate[cid]">编辑</a> - <a href="admincp.php?job=category&action=delcate&cid=$cate[cid]">删除</a></td>
    </tr>
<!--
EOT;
}print <<<EOT
-->
    <tr class="tablecell">
      <td colspan="4" align="center">
        <input type="hidden" name="action" value="updatedisplayorder">
        <input type="submit" value="更新排序" class="formbutton">
      </td>
    </tr>
<!--
EOT;
} elseif (in_array($action, array('addcate', 'modcate'))){print <<<EOT
-->
    <input type="hidden" name="action" value="do{$action}">
    <input type="hidden" name="cid" value="$cate[cid]">
    <tr class="tdbheader">
      <td colspan="3">$subnav</td>
    </tr>
    <tr class="tablecell">
      <td>排序:</td>
      <td><input class="formfield" type="text" name="displayorder" size="4" maxlength="50" value="$cate[displayorder]"></td>
    </tr>
    <tr class="tablecell">
      <td>名称:</td>
      <td><input class="formfield" type="text" name="name" size="35" maxlength="50" value="$cate[name]"></td>
    </tr>
    <tr class="tablecell">
      <td colspan="2" align="center">
        <input type="submit" value="确定" class="formbutton">
      </td>
    </tr>
<!--
EOT;
} elseif ($action == 'delcate'){print <<<EOT
-->
    <input type="hidden" name="cid" value="$cate[cid]">
    <input type="hidden" name="action" value="dodelcate">
    <tr class="alertheader">
      <td>$subnav</td>
    </tr>
    <tr class="alertbox">
      <td><p>您确定要删除【$cate[name]】分类吗?</p>
	  <p><b>本操作不可恢复，并会删除该分类中的所有文章、附件、评论和Trackback!</b></p>
	  <p><input type="submit" value="确认" class="formbutton"></p>
	  </td>
    </tr>
<!--
EOT;
} elseif($action == 'taglist'){print <<<EOT
-->
    <input type="hidden" name="action" value="dodeltag">
    <tr class="tdbheader">
      <td width="34%">Tags名称</td>
      <td width="32%">使用次数</td>
      <td width="32%">操作</td>
      <td width="2%" nowrap><input name="chkall" type="checkbox" onclick="checkall(this.form)" value="on"></td>
    </tr>
<!--
EOT;
foreach($tagdb as $key => $tag){print <<<EOT
-->
    <tr class="tablecell">
      <td><a href="admincp.php?job=article&action=list&tag=$tag[url]">$tag[item]</a></td>
      <td>$tag[usenum]</td>
      <td><a href="admincp.php?job=category&action=modtag&tagid=$tag[tagid]">修改</a></td>
      <td nowrap><input type="checkbox" name="tag[$tag[item]]" value="$tag[tagid]">
      </td>
    </tr>
<!--
EOT;
}print <<<EOT
-->
        <tr class="tablecell">
          <td colspan="5" nowrap="nowrap"><div class="records">记录:$tatol</div>
                  <div class="multipage">$multipage</div></td>
        </tr>
    <tr class="tablecell">
      <td colspan="4" align="center">
        <input type="submit" value="删除所选" class="formbutton">
      </td>
    </tr>
<!--
EOT;
} elseif ($action == 'modtag') {print <<<EOT
-->
    <input type="hidden" name="tagid" value="$taginfo[tagid]">
    <input type="hidden" name="oldtag" value="$taginfo[item]">
    <input type="hidden" name="action" value="domodtag">
    <tr class="tdbheader">
      <td colspan="2"><b>修改标签</b></td>
    </tr>
    <tr class="tablecell">
      <td>标签: </td>
      <td><input class="formfield" type="text" name="tag" size="35" maxlength="50" value="$taginfo[item]" >
      </td>
    </tr>
    <tr class="tablecell">
      <td>使用次数: </td>
      <td>$taginfo[usenum]</td>
    </tr>
    <tr class="tablecell">
      <td valign="top">使用文章: </td>
      <td><!--
EOT;
foreach($articledb as $key => $article){print <<<EOT
--><a href="admincp.php?job=article&action=mod&articleid=$article[articleid]">$article[title]</a><br><!--
EOT;
}print <<<EOT
--></td>
    </tr>
    <tr class="tablecell">
      <td colspan="2" align="center"><input type="submit" value="确认" class="formbutton"></td>
    </tr>
<!--
EOT;
} elseif ($action == 'tagclear') {print <<<EOT
-->
    <input type="hidden" name="action" value="dotagclear">
    <tr class="tdbheader">
      <td>清理Tags</td>
    </tr>
    <tr>
      <td class="alertbox">
	  <p>程序难免记录错每篇文章的关键字和计算错每个关键字使用次数，本功能是重新统计各个Tag的使用次数和清理不使用的Tag。</p>
      <p>为了使Tags数据最准确，本次操作将清空Tags数据表，并读取每篇文章的关键字，重新写入Tags数据表，过程较久，请耐心等候。</p>
      <p>建议定期执行。</p>
	  <p>每次处理文章数: <input class="formfield" type="text" name="percount" size="15" maxlength="50" value="200"></p>
      <p><input type="submit" value="确认" class="formbutton"></p>
	  </td>
    </tr>
<!--
EOT;
}print <<<EOT
-->
    <tr>
      <td class="tablebottom" colspan="4"></td>
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