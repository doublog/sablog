<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<script type="text/javascript" src="./js/editor.js"></script>
<script type="text/javascript">
function really(d,m,n) {
	if (confirm(m)) {
		window.location.href='admincp.php?job=comment&action=delonelog&trackbacklogid='+n+'&articleid='+d;
	}
}
function popnew(url,title,width,height){
	var w = 1024;
	var h = 768;
	if (document.all || document.layers){
		w = screen.availWidth;
		h = screen.availHeight;
	}
	var leftPos = (w/2-width/2);
	var topPos = (h/2-height/2);
	window.open(url,title,"width="+width+",height="+height+",top="+topPos+",left="+leftPos+",scrollbars=no,resizable=no,status=no");
}
function checkform() {
	if ($('title') && $('title').value == "") {
		alert("请输入标题.");
		return false;
	}
	if ($('cid') && $('cid').value == "")	{
		alert("请选择分类.");
		return false;
	}
	$('submit').disabled = true;
	return true;
}
//插入上传附件
function addattach(attachid){
	addhtml('[attach=' + attachid + ']');
}
var smdirurl = '$smdirurl';
//插入表情
function insertsmiley(icon){
	addhtml('<img src="'+smdirurl+icon+'" border="0" alt="" />');
}
</script>
<div class="mainbody">
<!--
EOT;
if ($action == 'list') {print <<<EOT
--><p class="p_nav">[ 查看特定文章: <a href="admincp.php?job=article&action=list&view=stick&cid=$cid">置顶文章</a> | <a href="admincp.php?job=article&action=list&view=hidden&cid=$cid">隐藏文章</a> ]</p>
<!--
EOT;
}print <<<EOT
-->
  <table border="0"  cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
        <div class="tableheader">文章管理</div>
        <div class="leftmenubody">
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=article&action=add">添加文章</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=article&action=list">编辑文章</a></div>
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=article&action=search">搜索文章</a></div>
        </div>
      </div>
	  <div class="tableborder">
        <div class="tableheader">文章分类</div>
        <div class="leftmenubody">
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=category&action=addcate">添加分类</a></div>
<!--
EOT;
foreach($catedb as $key => $cate){print <<<EOT
-->
          <div class="leftmenuitem">&#8226; <a href="admincp.php?job=article&action=list&cid=$cate[cid]">$cate[name]</a></div>
<!--
EOT;
}print <<<EOT
-->
        </div>
      </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top">
	  <!--
EOT;
if ($action == 'list' && $sax_group == 1) {print <<<EOT
-->
<div class="box">
<div class="alert">提示: 重建数据和标签整理可以提高数据准确率</div>
<div class="alertmsg">由于文章关联数据较多,当您进行批量操作或者改变文章状态后,建议您花1-2分钟重建相关数据和整理标签,可以提高数据的准确率.</div>
</div>
<!--
EOT;
}print <<<EOT
-->
	  <form action="admincp.php?job=article" enctype="multipart/form-data" method="POST" name="form" onsubmit="return checkform();"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	  <tr><td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
<!--
EOT;
if($action == 'list'){print <<<EOT
-->
    <tr class="tdbheader">
      <td width="45%">标题</td>
      <td width="10%" nowrap>分类</td>
      <td width="6%" nowrap>评论</td>
      <td width="5%" nowrap>引用</td>
      <td width="5%" nowrap>附件</td>
      <td width="15%" nowrap>时间</td>
      <td width="12%" nowrap>操作</td>
      <td width="2%" nowrap><input name="chkall" value="on" type="checkbox" onclick="checkall(this.form)"></td>
    </tr>
<!--
EOT;
foreach($articledb as $key => $article){print <<<EOT
-->
    <tr class="tablecell">
      <td><a href="admincp.php?job=article&action=visible&articleid=$article[articleid]">$article[visible]</a>
<!--
EOT;
if($article['stick']){print <<<EOT
-->[置顶] <!--
EOT;
}print <<<EOT
--><a href="admincp.php?job=article&action=mod&articleid=$article[articleid]">$article[title]</a></td>
      <td nowrap><a href="admincp.php?job=article&action=list&cid=$article[cid]">$article[cname]</a></td>
      <td nowrap>$article[comments]</td>
      <td nowrap><a href="admincp.php?job=comment&action=tblist&articleid=$article[articleid]">$article[trackbacks]</a></td>
      <td nowrap>$article[attachment]</td>
      <td nowrap>$article[dateline]</td>
      <td nowrap><a href="admincp.php?job=comment&action=tbsendlog&articleid=$article[articleid]">发送</a> - <a href="admincp.php?job=comment&action=cmlist&articleid=$article[articleid]">评论</a></td>
      <td nowrap><input type="checkbox" name="article[]" value="$article[articleid]"></td>
    </tr>
<!--
EOT;
}print <<<EOT
-->
        <tr class="tablecell">
          <td colspan="8" nowrap="nowrap"><div class="records">记录:$tatol</div>
                  <div class="multipage">$multipage</div></td>
        </tr>
<!--
EOT;
} elseif (in_array($action, array('add', 'mod'))) {print <<<EOT
-->
    <tr class="tdbheader">
      <td colspan="2">$tdtitle</td>
    </tr>
    <tr class="tablecell">
      <td>文章标题:</td>
      <td><input class="formfield" type="text" name="title" id="title" size="35" value="$article[title]"></td>
    </tr>
    <tr class="tablecell">
      <td valign="top">选择分类:</td>
      <td><select name="cid" id="cid">
          <option value="">== 选择分类 ==</option>
          <option value="">--------------</option>
<!--
EOT;
$i=0;
foreach($catedb as $key => $cate){
$i++;
$selected = ($cate['cid'] == $article['cid']) ? "selected" : "";
print <<<EOT
-->
          <option value="$cate[cid]" $selected>$i. $cate[name]</option>
<!--
EOT;
}print <<<EOT
-->
        </select></td>
    </tr>
    <tr class="tablecell">
      <td>标签:</td>
      <td><input class="formfield" type="text" name="keywords" size="70" maxlength="110" value="$article[keywords]"> <img src="../templates/admin/images/insert.gif" alt="插入已经使用的Tag" onclick="popnew('admincp.php?job=category&action=getalltags','tag',250,330)" style="cursor:pointer" /><br />用“,”分隔多个关键字, 最多允许添加5个关键字, 每个关键字不能超过20个字符.</td>
    </tr>
    <tr class="tablecell">
      <td valign="top">文章描述:</td>
      <td>$descriptionarea</td>
    </tr>
    <tr class="tablecell">
      <td valign="top">文章内容:
<!--
EOT;
if ($insertsm && $smfiles) {print <<<EOT
-->
<div class="smbox"><table cellpadding="3" cellspacing="0" width="100%" border="0"><tr>
<!--
EOT;
$br = 0;
foreach($smfiles as $smile){
$br++;
print <<<EOT
--><td><img src="../images/smiles/{$smile}" alt="" onclick="insertsmiley('$smile')" /></td>
<!--
EOT;
if ($br >= 3) {print <<<EOT
--></tr><tr>
<!--
EOT;
$br = 0;
}}
if ($br != 0) {
for($i=$br; $i<3;$i++){print <<<EOT
--><td></td>
<!--
EOT;
}}print <<<EOT
--></tr></table></div>
<!--
EOT;
}print <<<EOT
--></td>
      <td>$contentarea</td>
    </tr>
<!--
EOT;
if ($action == 'add') {print <<<EOT
-->
    <tr class="tablecell">
      <td>自动保存:</td>
      <td><script type="text/javascript" src="./js/autosave.js"></script>
	  <span id="timemsg">禁止自动保存</span>
	  <a href="###" onclick="stopautosaver();">暂停</a> - <a href="###" onclick="restartautosaver();">开始</a> - <a href="###" onclick="stopforever();">禁止</a> - <a href="###" onclick="handsave();">立即保存</a>
	  <span id="timemsg2"></span></td>
    </tr>
<!--
EOT;
}print <<<EOT
-->
    <tr class="tablecell">
      <td>阅读密码:</td>
      <td><input class="formfield" type="text" name="readpassword" size="20" maxlength="20" value="$article[readpassword]"> 20个字符以内</td>
    </tr>
<!--
EOT;
if($tb_tatol > 0){print <<<EOT
-->
    <tr class="tablecell">
      <td valign="top">已发送的Trackback:<br />[<a href="admincp.php?job=comment&action=tbsendlog&articleid=$article[articleid]">继续发送</a>]</td>
      <td>
<!--
EOT;
foreach($tblogdb as $key => $tblog){print <<<EOT
--><input type="checkbox" name="del_trackbacklog[]" value="$tblog[trackbacklogid]"> 删除 $tblog[pingurl] [<a href="###" onclick="really('$article[articleid]','你确定要删除该记录吗?','$tblog[trackbacklogid]')">删除记录</a>]<br><!--
EOT;
}print <<<EOT
--></td>
    </tr>
<!--
EOT;
}print <<<EOT
-->
    <tr class="tablecell">
      <td valign="top">更多选项:</td>
      <td><input name="closecomment" type="checkbox" value="1" $closecomment_check>
        禁止访客发表评论<br />
        <input name="closetrackback" type="checkbox" value="1" $closetrackback_check>
        禁止引用本文<br />
        <input name="visible" type="checkbox" value="1" $visible_check>
        显示本文<br />
        <input name="stick" type="checkbox" value="1" $stick_check>
        置顶本文<br />
		<input name='edittime' type="checkbox" value="1">
		更改发布时间 <input class="formfield" name="newyear" type="text" value="$newyear" maxlength="4" style="width:30px"> 年 <input class="formfield" name="newmonth" type="text" value="$newmonth" maxlength="2" style="width:15px"> 月 <input class="formfield" name="newday" type="text" value="$newday" maxlength="2" style="width:15px"> 日 <input class="formfield" name="newhour" type="text" value="$newhour" maxlength="2" style="width:15px"> 时 <input class="formfield" name="newmin" type="text" value="$newmin" maxlength="2" style="width:15px"> 分 <input class="formfield" name="newsec" type="text" value="$newsec" maxlength="2" style="width:15px"> 秒 <input class="formbutton" type="button" onclick="alert('有效的时间戳典型范围是从格林威治时间 1901 年 12 月 13 日 星期五 20:45:54 到 2038年 1 月 19 日 星期二 03:14:07\\n\\n该日期根据 32 位有符号整数的最小值和最大值而来\\n\\n取值说明: 日取 01 到 30 之间, 时取 0 到 24 之间, 分和秒取 0 到 60 之间!\\n\\n系统会自动检查时间有效性,如果不在有效范围内,将不会执行更改时间操作\\n\\n注意:如果系统是按照时间而不是提交次序排列文章,修改时间可以改变文章的顺序.');" value="时间说明">		
		</td>
    </tr>
<!--
EOT;
if($attach_tatol > 0){print <<<EOT
-->
    <tr class="tablecell">
      <td valign="top">已上传的附件:</td>
      <td>
<!--
EOT;
foreach($attachdb as $key => $attach){print <<<EOT
--><input type="checkbox" name="keep[]" value="$attach[attachmentid]" checked> 保留 <a href="../attachment.php?id=$attach[attachmentid]" target="_blank"><b>$attach[filename]</b></a> ($attach[dateline], $attach[filesize]) <b> <a href="###" onclick="addattach('$attach[attachmentid]')">插入文章</a></b><br />
<!--
EOT;
}print <<<EOT
--></td>
    </tr>
<!--
EOT;
}print <<<EOT
-->
    <tr class="tablecell">
      <td valign="top">上传新附件:</td>
      <td style="padding-top:5px;"><table cellspacing="0" cellpadding="6" border="0" class="celltable">
  <tr><td>图片超过2M缩略图和水印均不生效.如果上传大于2M的图片请自行处理.</td></tr>
  <tbody id="attachbodyhidden" style="display:none"><tr><td><input type="file" name="attach[]" class="formfield" /><span id="localfile[]"></span><input type="hidden" name="localid[]" /></td></tr></tbody>
  <tbody id="attachbody"></tbody>
  </table>
  <script type="text/javascript" src="./js/attachment.js"></script>
  </td>
    </tr>

    <input type="hidden" name="action" value="$act">
    <input type="hidden" name="articleid" value="$articleid">
    <input type="hidden" name="oldtags" value="$article[keywords]">
    <tr class="tablecell">
      <td colspan="2" align="center"><input type="submit" name="submit" id="submit" value="提交" class="formbutton">
        <input type="reset" value="重置" class="formbutton"></td>
    </tr>
<!--
EOT;
} elseif ($_POST['do'] == 'move') {print <<<EOT
-->
    <tr class="tdbheader">
      <td colspan="1"><a name="移动文章"></a>移动文章</td>
    </tr>
    <tr>
      <td class="alertbox">
	  <p><ol>
        <br>
<!--
EOT;
foreach($articledb as $key => $article){print <<<EOT
-->
        <li><a href="admincp.php?job=article&action=mod&articleid=$article[articleid]">$article[title]</a><input type="hidden" name="article[]" value="$article[articleid]"></li>
<!--
EOT;
}print <<<EOT
-->
      </ol></p>
	  <p>将以上文章移动到
        <select name="cid">
            <option value="" selected>选择分类</option>
<!--
EOT;
foreach($catedb as $key => $cate){print <<<EOT
-->
            <option value="$cate[cid]">$cate[name]</option>
<!--
EOT;
}print <<<EOT
-->
          </select>
      </p>
      <p><input type="submit" name="submit" id="submit" value="确认" class="formbutton"></p>
      <input type="hidden" name="action" value="domove">
	  </td>
    </tr>
<!--
EOT;
} elseif ($_POST['do'] == 'delete') {print <<<EOT
-->
    <tr class="alertheader">
      <td colspan="1"><a name="删除文章"></a>删除文章</td>
    </tr>
    <tr>
      <td class="alertbox">
	  <p><ol>
        <br>
<!--
EOT;
foreach($articledb as $key => $article){print <<<EOT
-->
        <li><a href="admincp.php?job=article&action=mod&articleid=$article[articleid]">$article[title]</a><input type="hidden" name="article[]" value="$article[articleid]"></li>
<!--
EOT;
}print <<<EOT
-->
      </ol></p>
	  <p><b>注意: 删除以上文章将会连同相关评论、引用、附件一起删除，确定吗？</b></p>
      <p><input type="submit" name="submit" id="submit" value="确认" class="formbutton"></p>
      <input type="hidden" name="action" value="dodelete">
	  </td>
    </tr>
<!--
EOT;
} elseif ($action == 'search') {print <<<EOT
-->
    <tr class="tdbheader">
	  <td colspan="2">搜索文章</td>
    </tr>
    <tr class="tablecell">
      <td valign="top"><b>搜索分类:</b></td>
      <td><select name="cateid">
          <option value="">== 全部分类 ==</option>
<!--
EOT;
$i=0;
foreach($catedb as $key => $cate){
$i++;
$selected = ($cate['cid'] == $article['cid']) ? 'selected' : '';
print <<<EOT
-->
          <option value="$cate[cid]" $selected>$i. $cate[name]</option>
<!--
EOT;
}print <<<EOT
-->
        </select></td>
    </tr>
    <tr class="tablecell">
	  <td><b>标题、作者、描述、内容内的关键字:</b></td>
	  <td><input class="formfield" type="text" name="keywords" size="35" maxlength="50" value=""></td>
    </tr>
    <tr class="tablecell">
	  <td><b>添加时间早于:</b><br />
	  yyyy-mm-dd</td>
	  <td><input class="formfield" type="text" name="startdate" size="35" maxlength="50" value=""></td>
    </tr>
    <tr class="tablecell">
	  <td><b>添加时间晚于:</b><br />
	  yyyy-mm-dd</td>
	  <td><input class="formfield" type="text" name="enddate" size="35" maxlength="255" value=""></td>
    </tr>
    <input type="hidden" name="action" value="list">
    <input type="hidden" name="do" value="search">
    <tr class="tablecell">
      <td colspan="2" align="center" class="tablecell"><input type="submit" name="submit" id="submit" value="提交" class="formbutton">
        <input type="reset" value="重置" class="formbutton"></td>
    </tr>
<!--
EOT;
}print <<<EOT
-->
    <tr>
      <td class="tablebottom" colspan="8"></td>
    </tr>
      </table></td>
    </tr>
  </table>
<!--
EOT;
if ($action == 'list') {print <<<EOT
-->
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" height="40">
  <tr>
    <td align="right">
      <select name="do">
        <option value="">= 管理操作 =</option>
        <option value="stick">置顶</option>
        <option value="unstick">取消置顶</option>
        <option value="hidden">设置隐藏</option>
        <option value="display">设置可见</option>
        <option value="delete">删除</option>
        <option value="move">移动</option>
      </select>
      <input type="submit" name="submit" id="submit" value="确定" class="formbutton"><input type="hidden" name="action" value="domore"></td>
  </tr>
</table>
<!--
EOT;
}print <<<EOT
-->
</form></td>
    </tr>
  </table>
</div>
<!--
EOT;
?>
-->