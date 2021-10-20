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
        <div class="tableheader">数据管理</div>
        <div class="leftmenubody">
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=database&action=backup">备份数据库</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=database&action=tools">数据库维护</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=database&action=filelist">数据文件管理</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=database&action=mysqlinfo">数据库信息</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=database&action=rssimport">导入RSS数据</a></div>
        </div>
      </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top">
<!--
EOT;
if ($action == 'rssimport') {print <<<EOT
-->
<div class="box">
<div class="alert">关于导入RSS数据</div>
<div class="alertmsg">本功能可以导入任何标准的 RSS 2.0 文件.
<br /><b>但需要注意以下几点:</b>
<br /><br />1. 只能导入到一个分类,并且只有文章标题,发布时间和文章内容.
<br />2. 如果RSS输出的文件是描述而不是全文,这里导入的就只有描述部分.
<br />3. 导入RSS数据不会覆盖已有数据,而是在数据库插入新记录.</div>
</div>
<!--
EOT;
}
if ($action == 'filelist') {print <<<EOT
-->
<div class="box">
<div class="alert">关于导入数据说明</div>
<div class="alertmsg">
1. 导入的数据必须是用Sablog-X备份的文件.<br />
2. 导入的数据文件内容必须全部是当前Sablog-X所使用的数据表.如果文件内的表前缀和当前系统不同.将不允许导入.<br />
3. 只允许从卷号1的文件开始恢复数据.</div>
</div>
<!--
EOT;
}print <<<EOT
-->
	  <form action="admincp.php?job=database" enctype="multipart/form-data" method="POST" name="form"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	  <tr><td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
<!--
EOT;
if (in_array($action, array('backup', 'tools'))) {print <<<EOT
-->
    <tr class="tdbheader">
      <td colspan="2">$tdtitle</td>
    </tr>
<!--
EOT;
if($action == 'backup'){print <<<EOT
-->
    <tr class="tablecell">
      <td>建表语句格式</td>
      <td><select name="sqlcompat">
          <option value="" selected>默认</option>
          <option value="MYSQL40">MySQL 4.0.x</option>
          <option value="MYSQL41">MySQL 4.1.x/5.x</option>
        </select></td>
    </tr>
    <tr class="tablecell">
      <td>使用扩展插入方式(Extended Insert)</td>
      <td><select name="extendins">
          <option value="1">是</option>
          <option value="0" selected>否</option>
        </select></td>
    </tr>
    <tr class="tablecell">
      <td>添加字符集限定(SET NAMES)</td>
      <td><select name="addsetnames">
          <option value="1">是</option>
          <option value="0" selected>否</option>
        </select></td>
    </tr>
    <tr class="tablecell">
      <td>分卷备份 - 每个文件长度限制为:</td>
      <td><input class="formfield" type="text" name="sizelimit" size="20" maxlength="20" value="2048"> KB</td>
    </tr>
    <tr class="tablecell">
      <td>备份文件名:</td>
      <td><input class="formfield" type="text" name="filename" size="40" maxlength="40" value="$backuppath">.sql</td>
    </tr>
<!--
EOT;
} else {print <<<EOT
-->
  <tr class="tablecell">
    <td width="40" align="right" nowrap><input type="checkbox" name="do[]" value="check" checked /></td>
    <td width="100%">检查表</td>
  </tr>
  <tr class="tablecell">
    <td width="40" align="right" nowrap><input type="checkbox" name="do[]" value="repair" checked /></td>
    <td width="100%">修复表</td>
  </tr>
  <tr class="tablecell">
    <td width="40" align="right" nowrap><input type="checkbox" name="do[]" value="analyze" checked /></td>
    <td width="100%">分析表</td>
  </tr>
  <tr class="tablecell">
    <td width="40" align="right" nowrap><input type="checkbox" name="do[]" value="optimize" checked /></td>
    <td width="100%">优化表</td>
  </tr>
<!--
EOT;
}print <<<EOT
-->
    <input type="hidden" name="action" value="$act">
    <tr class="tablecell">
      <td colspan="2" align="center"><input type="submit" value="提交" class="formbutton">
        <input type="reset" name="" value="重置" class="formbutton">
      </td>
    </tr>
<!--
EOT;
} elseif($action == 'filelist'){print <<<EOT
-->
    <input type="hidden" name="action" value="deldbfile">
    <tr class="tdbheader">
      <td width="34%" nowrap>文件名</td>
      <td width="17%" nowrap>备份时间</td>
      <td width="17%" nowrap>修改时间</td>
      <td width="8%" nowrap>版本</td>
      <td width="8%" nowrap>卷号</td>
      <td width="8%" nowrap>文件大小</td>
      <td width="6%" nowrap>操作</td>
      <td width="2%" nowrap><input name="chkall" value="on" type="checkbox" onclick="checkall(this.form)"></td>
    </tr>
<!--
EOT;
if ($noexists) {print <<<EOT
-->
    <tr class="tablecell">
      <td colspan="8">目录不存在或无法访问, 请检查 $backupdir 目录.</td>
    </tr>
<!--
EOT;
} else {
foreach($dbfiles as $key => $dbfile){print <<<EOT
-->
    <tr class="tablecell">
      <td><a href="$backupdir/$dbfile[filename]" title="右键另存为保存该文件">$dbfile[filename]</a></td>
      <td nowrap>$dbfile[bktime]</td>
      <td nowrap>$dbfile[mtime]</td>
      <td nowrap>$dbfile[version]</td>
      <td nowrap>$dbfile[volume]</td>
      <td nowrap>$dbfile[filesize]</td>
      <td nowrap>
	  <!--
EOT;
if ($dbfile['volume'] == '1') {print <<<EOT
--><a href="admincp.php?job=database&action=checkresume&sqlfile=$dbfile[filepath]">导入</a><!--
EOT;
} else {print <<<EOT
-->无
<!--
EOT;
}print <<<EOT
--></td>
      <td nowrap><input type="checkbox" name="sqlfile[$backupdir/$dbfile[filename]]" value="1"></td>
    </tr>
<!--
EOT;
}}print <<<EOT
-->
    <tr class="tablecell">
      <td colspan="8"><b>共有{$file_i}个备份文件</b></td>
    </tr>
    <tr class="tablecell">
      <td colspan="8" align="center">
        <input type="submit" value="删除所选文件" class="formbutton">
      </td>
    </tr><!--
EOT;
} elseif($action == 'mysqlinfo'){print <<<EOT
-->
  <tr class="tdbheader">
	<td colspan="3">MYSQL数据库信息</td>
  </tr>
  <tr class="tablecell">
	<td width="50%">数据库版本:</td>
	<td width="50%">$mysql_version</td>
  </tr>
  <tr class="tablecell">
	<td width="50%">数据库运行时间:</td>
	<td width="50%">$mysql_runtime</td>
  </tr>
  <tr>
    <td class="tablebottom" colspan="8"></td>
  </tr>
  </table></td>
    </tr>
    <tr>
      <td height="20"></td>
    </tr>
    <tr>
      <td valign="top" class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
  <tr class="tdbheader">
	<td width="20%">Sablog-X数据表名称</td>
	<td width="20%">创建时间</td>
	<td width="20%">最后更新时间</td>
	<td width="10%">记录数</td>
	<td width="10%">数据</td>
	<td width="10%">索引</td>
	<td width="10%">碎片</td>
  </tr>
<!--
EOT;
foreach($sablog_table as $sablog){print <<<EOT
-->
  <tr class="tablecell">
	<td>$sablog[Name]</td>
	<td nowrap>$sablog[Create_time]</td>
	<td nowrap>$sablog[Update_time]</td>
	<td nowrap>$sablog[Rows]</td>
	<td nowrap>$sablog[Data_length]</td>
	<td nowrap>$sablog[Index_length]</td>
	<td nowrap>$sablog[Data_free]</td>
  </tr>
<!--
EOT;
}print <<<EOT
-->
  <tr class="tablecell">
	<td colspan="3"><b>共计:{$sablog_table_num}个数据表</b></td>
	<td><b>$sablog_table_rows</b></td>
	<td><b>$sablog_data_size</b></td>
	<td><b>$sablog_index_size</b></td>
	<td><b>$sablog_free_size</b></td>
  </tr>
  <tr>
    <td class="tablebottom" colspan="8"></td>
  </tr>
  </table></td>
    </tr>
    <tr>
      <td height="20"></td>
    </tr>
    <tr>
      <td valign="top" class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
  <tr class="tdbheader">
	<td width="20%">其他数据表名称</td>
	<td width="20%">创建时间</td>
	<td width="20%">最后更新时间</td>
	<td width="10%">记录数</td>
	<td width="10%">数据</td>
	<td width="10%">索引</td>
	<td width="10%">碎片</td>
  </tr>
<!--
EOT;
foreach($other_table as $other){print <<<EOT
-->
  <tr class="tablecell">
	<td>$other[Name]</td>
	<td nowrap>$other[Create_time]</td>
	<td nowrap>$other[Update_time]</td>
	<td nowrap>$other[Rows]</td>
	<td nowrap>$other[Data_length]</td>
	<td nowrap>$other[Index_length]</td>
	<td nowrap>$other[Data_free]</td>
  </tr>
<!--
EOT;
}print <<<EOT
-->
  <tr class="tablecell">
	<td colspan="3"><b>共计:{$other_table_num}个数据表</b></td>
	<td><b>$other_table_rows</b></td>
	<td><b>$other_data_size</b></td>
	<td><b>$other_index_size</b></td>
	<td><b>$other_free_size</b></td>
  </tr>
  <!--
EOT;
} elseif($action == 'dotools') {
foreach ($dodb AS $do) {print <<<EOT
-->
  <tr class="tdbheader">
	<td colspan="2">$do[name]表</td>
  </tr>
<!--
EOT;
foreach($tabledb as $table){
if ($table['do'] == $do['do']) {print <<<EOT
-->
  <tr class="tablecell">
	<td>$table[table]</td>
	<td>$table[result]</td>
  </tr>
<!--
EOT;
}}}} elseif ($action == 'checkresume') {print <<<EOT
-->
  <input type="hidden" name="action" value="resume">
  <input type="hidden" name="sqlfile" value="$sqlfile">
    <tr class="alertheader">
      <td>导入备份数据</td>
    </tr>
    <tr>
      <td class="alertbox">
	  <p>导入文件:$sqlfile</p>
	  <p><b>恢复功能将覆盖原来的数据,您确认要导入备份数据?</b></p>
	  <p><input type="submit" value="确认" class="formbutton"></p>
	  </td>
    </tr>
<!--
EOT;
} elseif ($action == 'rssimport') {print <<<EOT
-->
    <tr class="tdbheader">
      <td colspan="2">导入RSS数据</td>
    </tr>
    <tr class="tablecell">
      <td valign="top">选择目标分类:</td>
      <td><select name="cid" id="cid">
          <option value="" selected>== 选择分类 ==</option>
<!--
EOT;
$i=0;
foreach($catedb as $key => $cate){
print <<<EOT
-->
          <option value="$cate[cid]">$cate[name]</option>
<!--
EOT;
}print <<<EOT
-->
        </select></td>
    </tr>
    <tr class="tablecell">
      <td valign="top">选择文章作者:</td>
      <td><select name="uid" id="uid">
          <option value="" selected>== 选择作者 ==</option>
<!--
EOT;
$i=0;
foreach($userdb as $key => $user){
print <<<EOT
-->
          <option value="$user[userid]">$user[username]</option>
<!--
EOT;
}print <<<EOT
-->
        </select></td>
    </tr>
    <tr class="tablecell">
      <td>选择XML文件</td>
      <td><input class="formfield" type="file" name="xmlfile"> 允许文件类型:xml</td>
    </tr>
    <input type="hidden" name="action" value="importrss">
    <tr class="tablecell">
      <td colspan="2" align="center">
        <input type="submit" value="确定" class="formbutton">
      </td>
    </tr>
<!--
EOT;
} print <<<EOT
-->
    <tr>
      <td class="tablebottom" colspan="8"></td>
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