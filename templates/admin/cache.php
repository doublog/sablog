<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<div class="mainbody">
  <table border="0"  cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
        <div class="tableheader">系统维护</div>
        <div class="leftmenubody">
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=cache&action=cache">缓存管理</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=cache&action=rebuild">重建数据</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=cache&action=searchlog">搜索记录</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=cache&action=rewrite_1">URL优化设置向导</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=cache&action=js">JS调用向导</a></div>
        </div>
      </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top">
<!--
EOT;
if ($action == 'searchlog') {print <<<EOT
-->
<div class="box">
<div class="alert">提示: 清理搜索记录可以提高效率和准确性</div>
<div class="alertmsg">搜索记录为了提高搜索速度和效率,减轻服务器负担,但是搜索过后程序并不会自动清理,建议定期执行清理操作.</div>
</div>
<!--
EOT;
} elseif ($action == 'rebuild') {print <<<EOT
-->
<div class="box">
<div class="alert">提示: 经常重建数据有助于提高数据准确性</div>
<div class="alertmsg">通常执行大量的修改操作后, 建议执行一次此操作.可以提高数据准确性.</div>
</div>
<!--
EOT;
} elseif ($action == 'rewrite_1') {print <<<EOT
-->
<div class="box">
<div class="alert">关于URL优化功能</div>
<div class="alertmsg">本功能将对包括部分常用页面(如 文章列表,日期归档,评论,引用,阅读文章及它们的分页 等)进行 URL 静态化转换,以提高搜索引擎抓取.
<br />开启本功能后，检查页面上的链接是否都能正常访问,如果不正常请关闭URL优化功能.
<br />目前只支持Apache服务器.
<br /><br /><b>默认的URL形式为</b><br />http://www.sablog.net/blog/?action=show&id=254&page=2<br />http://www.sablog.net/blog/?action=search
<br /><b>开启本功能后新的URL形式为</b><br />http://www.sablog.net/blog/show-254-2.自定义的扩展名<br />http://www.sablog.net/blog/search.自定义的扩展名

<br /><br /><b>注意: 当访问量很大时,本功能会轻微加重服务器负担.</b></div>
</div>
<!--
EOT;
}
if ($view) {print <<<EOT
-->
<div class="box">
<div class="alert">$msg</div>
<div class="alertmsg"><textarea class="formarea" type="text" style="width:99%" rows="5">$showcode</textarea></div>
</div>
<!--
EOT;
}print <<<EOT
-->
	  <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	  <tr><td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
<!--
EOT;
if(!$action || $action == 'cache') {print <<<EOT
--><form action="admincp.php?job=cache"  method="POST">
    <tr class="tdbheader">
      <td width="26%"><b>缓存名称</b></td>
      <td width="21%"><b>生成时间</b></td>
      <td width="21%"><b>修改时间</b></td>
      <td width="16%"><b>缓存大小</b></td>
      <td width="16%" nowrap><b>管理操作</b></td>
    </tr>
<!--
EOT;
foreach($cachedb as $key => $cache){print <<<EOT
-->
        <tr class="tablecell">
          <td nowrap="nowrap" style="line-height:20px;"><b>$cache[name]</b><br/>$cache[desc]</td>
          <td nowrap="nowrap">$cache[ctime]</td>
          <td nowrap="nowrap">$cache[mtime]</td>
          <td nowrap="nowrap">$cache[size]</td>
          <td nowrap="nowrap"><a href="admincp.php?job=cache&action=update&id=$cache[name]">更新</a> - <a href="admincp.php?job=cache&action=show&id=$cache[name]">查看</a></td>
        </tr>
<!--
EOT;
}print <<<EOT
-->
    <input type="hidden" name="action" value="updateall">
    <tr class="tablecell">
      <td colspan="5" align="center"><input type="submit" value="更新所有缓存" class="formbutton">
      </td>
    </tr>
  </form>
<!--
EOT;
} elseif ($action == 'searchlog') {print <<<EOT
--><form action="admincp.php?job=cache"  method="POST">
    <tr class="tdbheader">
      <td width="20%"><b>关键字</b></td>
      <td width="20%"><b>搜索时间</b></td>
      <td width="20%"><b>搜索结果</b></td>
      <td width="20%"><b>搜索范围</b></td>
      <td width="20%"><b>IP地址</b></td>
    </tr>
<!--
EOT;
foreach($searchdb as $key => $search){print <<<EOT
-->
        <tr class="tablecell">
          <td nowrap="nowrap">$search[keywords]</td>
          <td nowrap="nowrap">$search[dateline]</td>
          <td nowrap="nowrap">$search[tatols] 条记录</td>
          <td nowrap="nowrap">$search[searchfrom]</td>
          <td nowrap="nowrap">$search[ipaddress]</td>
        </tr>
<!--
EOT;
}print <<<EOT
-->
        <tr class="tablecell">
          <td colspan="6" nowrap="nowrap"><div class="records">记录:$tatol</div>
                  <div class="multipage">$multipage</div></td>
        </tr>
    <input type="hidden" name="action" value="delsearchlog">
    <tr class="tablecell">
      <td colspan="6" align="center"><input type="submit" value="清空所有搜索记录" class="formbutton">
      </td>
    </tr>
  </form>
<!--
EOT;
} elseif ($action == 'rebuild') {print <<<EOT
-->
  <form action="admincp.php?job=cache" method="post">
    <input type="hidden" name="action" value="dostatsdata">
    <tr class="tdbheader">
      <td colspan="2">重建数据</td>
    </tr>
    <tr class="tablecell">
      <td>更新首页侧栏的统计数据<div class="desc">建议后台执行大量修改操作后执行</div></td>
      <td><input type="submit" value="确认" class="formbutton"></td>
    </tr>
  </form>
  <form action="admincp.php?job=cache" method="post">
    <input type="hidden" name="action" value="docatedata">
    <tr class="tablecell">
      <td>更新所有分类的文章数<div class="desc">建议后台执行大量文章修改操作后执行</div></td>
      <td><input type="submit" value="确认" class="formbutton"></td>
    </tr>
  </form>
  <form action="admincp.php?job=cache" method="post">
    <input type="hidden" name="action" value="doadmindata">
    <tr class="tablecell">
      <td>更新后台用户发表数量<div class="desc">建议后台执行大量文章修改操作后执行</div></td>
      <td><input type="submit" value="确认" class="formbutton"></td>
    </tr>
  </form>
  <form action="admincp.php?job=cache" method="post">
    <input type="hidden" name="action" value="doarticledata">
    <tr class="tablecell">
      <td>更新所有文章中的评论数、引用数及附件信息<div class="desc">建议经常定期执行, 配合附件管理中的附件修复操作, 可以提高数据准确性和程序的执行效率</div></td>
      <td>循环更新数量: <input class="formfield" type="text" name="percount" value="200" size="5"> <input type="submit" value="确认" class="formbutton"></td>
    </tr>
  </form>
  <form action="admincp.php?job=cache" method="post">
    <input type="hidden" name="action" value="dothumbdata">
    <tr class="tablecell">
      <td>重建附件缩略图<div class="desc">将会重新按照现在设定的缩略图尺寸重建所有附件图像的缩略图。<br>通常用于你更改了缩略图尺寸并希望更新全部附件的情况下。</div><b>这个操作会耗费一定服务器资源。</b></td>
      <td>循环更新数量: <input class="formfield" type="text" name="percount" value="20" size="5"> <input type="submit" value="确认" class="formbutton"></td>
    </tr>
  </form>
<!--
EOT;
} elseif ($action == 'show') {print <<<EOT
-->
  <tr class="tdbheader">
    <td><b>查看 $name 缓存</b></td>
  </tr>
  <tr class="tablecell">
    <td><br><blockquote><pre>$data</pre></blockquote></td>
  </tr>
  <tr class="tablecell">
    <td align="center"><input class="formbutton" type="button" onclick="window.location.href='admincp.php?job=cache&action=update&id=$name'" value="更新"></td>
  </tr>
<!--
EOT;
} elseif ($action == 'rewrite_1') {print <<<EOT
-->
  <form action="admincp.php?job=cache" method="POST">
  <input type="hidden" name="action" value="rewrite_2">
    <tr class="tdbheader">
      <td colspan="2">URL优化设置向导</td>
    </tr>
    <tr class="tablecell">
      <td><b>模拟静态文件的扩展名:</b><br />
	  可以是htm,html,php,asp,cgi,jsp等其中一个,推荐使用 htm 或 html.不填默认html.</td>
      <td><input class="formfield" type="text" name="rewrite_ext" size="35" maxlength="50" value="$settings[rewrite_ext]"></td>
    </tr>
    <tr class="tablecell">
      <td colspan="6" align="center"><input type="submit" class="formbutton" value="下一步" /></td>
    </tr>
    </form>
<!--
EOT;
} elseif ($action == 'rewrite_2') {print <<<EOT
-->
  <form action="admincp.php?job=cache" method="POST">
  <input type="hidden" name="action" value="dorewrite">
  <input type="hidden" name="rewrite_ext" value="$rewrite_ext">
    <tr class="tdbheader">
      <td>URL优化设置向导</td>
    </tr>
    <tr class="tablecell">
      <td style="line-height:22px;">1. 请把下面的内容保存为 <b>.htaccess</b> 文件并将该文件放到程序根目录下.<br />
	  2. 完成此步后,分别点击最下方的“未优化链接示例”和“优化后链接测试”两个链接.如果显示效果完全一样则表示“测试成功”.<br />
	  3. 成功后请点击“<b>成功 - 开启URL优化</b>”按钮,否则请点击“<b>失败 - 关闭URL优化</b>”.<br />
	  4. 如果 <b>.htaccess</b> 文件上传后整个网站无法打开,请删除该文件.</td>
    </tr>
    <tr class="tablecell">
      <td><textarea class="formarea" type="text" style="width:99%;height:300px;">$filecontent</textarea></td>
    </tr>
    <tr class="tdbheader">
      <td>URL优化测试</td>
    </tr>
    <tr class="tablecell">
      <td style="line-height:22px;"><a href="../index.php" target="_blank">打开未优化链接</a><br />
	  <a href="../index.{$rewrite_ext}" target="_blank">打开优化后链接</a><br></td>
    </tr>
    <tr class="tablecell">
      <td align="center"><input type="submit" class="formbutton" value="成功 - 开启URL优化" /> <input type="button" class="formbutton" onclick="javascript:window.location='admincp.php?job=cache&action=closeurlseo'" value="失败 - 关闭URL优化"></td>
    </tr>
    </form>
<!--
EOT;
} elseif ($action == 'js') {print <<<EOT
-->
  <form action="admincp.php?job=cache" method="POST">
  <input type="hidden" name="action" value="js">
  <input type="hidden" name="view" value="article">
    <tr class="tdbheader">
      <td colspan="2">文章调用</td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>调用分类:</b><br />
        设置要调用的分类,不选则调用全部</td>
      <td>
  <!--
EOT;
if($catecache){
foreach($catecache AS $data){
$data[name] = htmlspecialchars($data[name]);
print <<<EOT
-->
          <input type="checkbox" name="cate[]" value="$data[cid]" /> $data[name]<br />
<!--
EOT;
}}print <<<EOT
-->
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>调用类型</b></td>
      <td><select name="orderby">
        <option value="dateline" selected>文章发表的时间</option>
        <option value="views">浏览次数最多的文章</option>
        <option value="comments">评论次数最多的文章</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td><b>显示数据条数:</b><br />
        设置调用文章数量,请设置为大于 0 的整数,否则默认显示10条.</td>
      <td><input class="formfield" type="text" name="titlenum" size="35" maxlength="50" value="10"></td>
    </tr>
    <tr class="tablecell">
      <td><b>文章标题截取字节数:</b><br />
        如果设置了显示所在分类,则会把分类字节数计算进去.设置0为不截取.</td>
      <td><input class="formfield" type="text" name="titlelimit" size="35" maxlength="50" value="50"></td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>在新窗口打开链接:</b></td>
      <td><select name="newwindow">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示文章所在分类:</b></td>
      <td><select name="cname">
        <option value="1">是</option>
        <option value="0" selected>否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示文章作者:</b></td>
      <td><select name="author">
        <option value="1">是</option>
        <option value="0" selected>否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示文章发表时间:</b></td>
      <td><select name="dateline">
        <option value="1">是</option>
        <option value="0" selected>否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示文章数据:</b><br />
        包括浏览次数和评论次数</td>
      <td><select name="articleinfo">
        <option value="1">是</option>
        <option value="0" selected>否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td colspan="6" align="center"><input type="submit" class="formbutton" value="生成代码" /></td>
    </tr>
    </form>
    <tr>
      <td class="tablebottom" colspan="6"></td>
    </tr>
  </table></td>
    </tr>
    <tr>
      <td height="20"></td>
    </tr>
    <tr>
      <td valign="top" class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
    <tr class="tdbheader">
      <td colspan="2">统计信息调用</td>
    </tr>
  <form action="admincp.php?job=cache" method="POST">
  <input type="hidden" name="action" value="js">
  <input type="hidden" name="view" value="stat">
    <tr class="tablecell">
      <td valign="top"><b>显示分类数量:</b></td>
      <td><select name="showcate">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示文章数量:</b></td>
      <td><select name="showarticle">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示评论数量:</b></td>
      <td><select name="showcomment">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示标签数量:</b></td>
      <td><select name="showtag">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示附件数量:</b></td>
      <td><select name="showattach">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示引用数量:</b></td>
      <td><select name="showtrack">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示注册用户数量:</b></td>
      <td><select name="showreguser">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示今日访问次数:</b></td>
      <td><select name="showtoday">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>显示总访问量:</b></td>
      <td><select name="showallviews">
        <option value="1" selected>是</option>
        <option value="0">否</option>
        </select>
      </td>
    </tr>
    <tr class="tablecell">
      <td colspan="6" align="center"><input type="submit" class="formbutton" value="生成代码" /></td>
    </tr>
    </form>
<!--
EOT;
}print <<<EOT
-->
    <tr>
      <td class="tablebottom" colspan="6"></td>
    </tr>
      </table></td>
    </tr>
  </table></td>
    </tr>
  </table>
</div>
<!--
EOT;
?>
-->