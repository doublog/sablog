<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<script type="text/javascript">
function really(d,m,n) {
	if (confirm(m)) {
		window.location.href='admincp.php?job=template&action=delonetag&tag='+d+'&tagid='+n;
	}
}
</script>
<div class="mainbody">
  <table border="0"  cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
          <div class="tableheader">模板管理</div>
          <div class="leftmenubody">
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=template&action=template">模板管理</a></div>
          </div>
        </div>
        <div class="tableborder">
          <div class="tableheader">模板变量</div>
          <div class="leftmenubody">
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=template&action=stylevar">自定义变量</a></div>
          </div>
        </div>
        <div class="tableborder">
          <div class="tableheader">模板编辑</div>
          <div class="leftmenubody">
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=template&action=newtemplate">新建模板</a></div>
            <!--
EOT;
foreach($dirdb as $dir){print <<<EOT
-->
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=template&action=filelist&path=$dir">$dir</a></div>
            <!--
EOT;
}print <<<EOT
-->
          </div>
        </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top"><!--
EOT;
if ($action == 'filelist') {print <<<EOT
-->
        <div class="box">
          <div class="alert">敬告: 修改模板是不安全的功能.</div>
          <div class="alertmsg">利用修改模板的功能, 可以构造代码留下后门让非法用户执行危险操作.<br />
            安全天使网络安全小组建议您把所有模板文件设置为只读权限以达到良好的安全性.<br />
            如需修改模板可在本地修改完毕后上传. 否则后果自负, 本程序作者不提供任何技术支持.</div>
        </div>
        <!--
EOT;
} elseif ($action == 'mod') {print <<<EOT
-->
        <div class="box">
          <div class="alert">警告: 编辑模板不能改变其中的PHP代码.</div>
          <div class="alertmsg">模板内的PHP代码, 大部分起到处理程序流程的作用, 非专业人士请勿修改, 否则可能会造成程序不正常, 甚至崩溃.</div>
        </div>
        <!--
EOT;
} elseif ($action == 'stylevar') {print <<<EOT
-->
        <div class="box">
          <div class="alert">关于自定义模板变量</div>
          <div class="alertmsg">设置一个变量about,内容为 &lt;b&gt;关于我&lt;/b&gt;<br />
            在前后台模板的任意地方,均可以放一个 <b>\$stylevar[about]</b> 变量,模板则直接显示 <b>关于我</b></div>
        </div>
<!--
EOT;
} elseif ($action == 'newtemplate') {print <<<EOT
-->
        <div class="box">
          <div class="alert">关于新建模板</div>
          <div class="alertmsg">
		  新建模板的过程是完整复制default模板到新的模板名字的目录.然后再用编辑模板功能编辑.<br />
		  熟悉HTML和CSS的朋友完全可以通过模板编辑功能在后台不用任何工具做出一套全新的模板,从而免除登陆FTP的步骤.<br />
		  但前提是templates目录具备可写权限.</div>
        </div>
		<!--
EOT;
}print <<<EOT
-->
        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <!--
EOT;
if($action == 'template'){print <<<EOT
-->
                <tr class="tdbheader">
                  <td>当前模板</td>
                </tr>
                <tr>
                  <td class="alertbox"><!--
EOT;
if ($current_template_info) {print <<<EOT
-->
                    <table border="0" cellpadding="20">
                      <tr>
                        <td align="center"><table border="0" cellspacing="0" cellpadding="5" class="screenshot">
                            <tr>
                              <td><img alt="$current_template_info[name]" src="$current_template_info[screenshot]" border="0" /></td>
                            </tr>
                          </table></td>
                        <td valign="top"><ul class="templateinfo">
                            <li>$current_template_info[name]</li>
                            <li>制作者:$current_template_info[author]</li>
                            <li>适用版本:$current_template_info[version]</li>
                          </ul>
                          <div class="templateinfo2">$current_template_info[description]</div>
                          <div class="templateinfo2">模板目录(相对管理目录):<b>$current_template_info[templatedir]</b></div></td>
                      </tr>
                    </table>
                    <!--
EOT;
} else {print <<<EOT
-->
                    没有当前主题的相关资料
                    <!--
EOT;
}print <<<EOT
-->
                  </td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
          </tr>
          <tr>
            <td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <tr class="tdbheader">
                  <td>可用模板</td>
                </tr>
                <tr>
                  <td class="alertbox"><!--
EOT;
if ($available_template_db) {
foreach($available_template_db as $id => $template){print <<<EOT
-->
                    <div class="availabletheme">
                      <h3><a title="设置$template[name]主题为当前主题" href="admincp.php?job=template&action=settemplate&name=$template[dirurl]">$template[name]</a></h3>
                      <a href="admincp.php?job=template&action=settemplate&name=$template[dirurl]" class="screenshot"><img src="$template[screenshot]" border="0" alt="设置$template[name]主题为当前主题" /></a> </div>
                    <!--
EOT;
}} else {print <<<EOT
-->
                    <b>没有可用模板</b>
                    <!--
EOT;
}print <<<EOT
-->
                  </td>
                </tr>
                <tr>
                  <td class="tablebottom"></td>
                </tr>
                <!--
EOT;
} elseif($action == 'stylevar'){print <<<EOT
-->
                <form action="admincp.php?job=template" method="post" name="form">
				<input type="hidden" name="action" value="domorestylevar">
                  <tr class="tdbheader">
                    <td width="4%" nowrap="nowrap">状态</td>
                    <td width="31%" nowrap="nowrap">变量名</td>
                    <td width="61%" nowrap="nowrap">变量内容</td>
                    <td width="4%" nowrap="nowrap"><input name="chkall" value="on" type="checkbox" onclick="checkall(this.form)"></td>
                  </tr>
                  <!--
EOT;
foreach($stylevardb as $stylevar){print <<<EOT
-->
                  <tr class="tablecell">
				    <td nowrap="nowrap"><select name="visible[$stylevar[stylevarid]]">$stylevar[visible]</select></td>
                    <td nowrap="nowrap"><b>\$stylevar[$stylevar[title]]</b></td>
                    <td nowrap="nowrap"><textarea id="varid_$stylevar[stylevarid]" class="formarea" name="stylevar[$stylevar[stylevarid]]" style="width:400px;height:30px;">$stylevar[value]</textarea> <b><a href="###" onclick="resizeup('varid_$stylevar[stylevarid]');">[+]</a> <a href="###" onclick="resizedown('varid_$stylevar[stylevarid]');">[-]</a></b></td>
                    <td nowrap><input type="checkbox" name="delete[]" value="$stylevar[stylevarid]"></td>
                  </tr>
                  <!--
EOT;
}print <<<EOT
-->
                  <tr class="tablecell">
                    <td colspan="4" nowrap="nowrap"><div class="records">记录:$tatol</div>
                      <div class="multipage">$multipage</div></td>
                  </tr>
                  <tr class="tablecell">
                    <td colspan="4" align="center"><input type="submit" value="更新 / 删除(所选)" class="formbutton"></td>
                  </tr>
                  <tr>
                    <td class="tablebottom" colspan="4"></td>
                  </tr>
                </form>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
          </tr>
          <tr>
            <td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <form action="admincp.php?job=template" method="post" name="form">
				<input type="hidden" name="action" value="addstylevar">
                  <tr class="tdbheader">
                    <td nowrap="nowrap" colspan="2">添加自定义变量</td>
                  </tr>
                  <tr class="tablecell">
                    <td><b>变量名:</b></td>
                    <td><input class="formfield" type="text" name="title" size="35" maxlength="50"> 只允许英文</td>
                  </tr>
                  <tr class="tablecell">
                    <td><b>变量内容:</b></td>
                    <td valign="top"><textarea id="addvar" class="formarea" type="text" name="value" style="width:400px;height:50px;"></textarea> <b><a href="###" onclick="resizeup('addvar');">[+]</a> <a href="###" onclick="resizedown('addvar');">[-]</a></b></td>
                  </tr>
                  <tr class="tablecell">
                    <td colspan="2" align="center"><input type="submit" value="添加" class="formbutton"></td>
                  </tr>
                  <tr>
                    <td class="tablebottom" colspan="2"></td>
                  </tr>
                </form>
                <!--
EOT;
} elseif($action == 'filelist'){print <<<EOT
-->
                <tr class="tdbheader">
                  <td nowrap="nowrap">模板名</td>
                  <td nowrap="nowrap">操作</td>
                </tr>
                <!--
EOT;
foreach($filedb as $key => $file){print <<<EOT
-->
                <tr class="tablecell">
                  <td nowrap="nowrap"><b><a href="admincp.php?job=template&action=mod&path=$path&file=$file[filename]&ext=$file[extension]">$file[filename]</a></b></td>
                  <td nowrap="nowrap"><a href="admincp.php?job=template&action=del&path=$path&file=$file[filename]&ext=$file[extension]">删除</a></td>
                </tr>
                <!--
EOT;
}print <<<EOT
-->
                <tr class="tablecell">
                  <td colspan="2"><b>共有 $i 个模板文件</b></td>
                </tr>
                <!--
EOT;
} elseif ($action == 'mod') {print <<<EOT
-->
                <form action="admincp.php?job=template" method="post" name="form">
                  <input type="hidden" name="action" value="savefile">
                  <tr class="tdbheader">
                    <td colspan="2">编辑模板文件</td>
                  </tr>
                  <!--
EOT;
if (!$writeable) {print <<<EOT
-->
                  <tr class="tablecell">
                    <td><b>写入状态:</b></td>
                    <td><span class="no"><b>当前模板文件不可写入, 请设置为 0777 权限后再编辑此文件.</b></span></td>
                  </tr>
                  <!--
EOT;
}print <<<EOT
-->
                  <tr class="tablecell">
                    <td width="20%"><b>模板套系:</b></td>
                    <td width="80%">$path
                      <input type="hidden" name="path" value="$path"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="20%"><b>模板名称:</b></td>
                    <td width="80%">$file
                      <input type="hidden" name="file" value="$file"><input type="hidden" name="ext" value="$ext"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="20%" valign="top"><b>模板内容:</b><br /><b><a href="###" onclick="resizeup('filecontent');">[+]</a> <a href="###" onclick="resizedown('filecontent');">[-]</a></b></td>
                    <td width="80%"><textarea id="filecontent" class="formarea" cols="85" rows="25" name="content" style="width:95%;height:400px;font:12px'Courier New';">$contents</textarea></td>
                  </tr>
                  <tr nowrap class="tablecell">
                    <td colspan="2" align="center"><input type="submit" value="保存" class="formbutton">
                      <input type="reset" value="重置" class="formbutton">
                    </td>
                  </tr>
                </form>
                <!--
EOT;
} elseif ($action == 'newtemplate') {print <<<EOT
-->
                <form action="admincp.php?job=template" method="post" name="form">
                  <input type="hidden" name="action" value="donewtemplate">
                  <tr class="tdbheader">
                    <td colspan="2">新建模板</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="20%"><b>模板名称:</b></td>
                    <td width="80%"><input class="formfield" type="text" name="newtemplatename" value=""> 只允许英文、数字和下划线</td>
                  </tr>
                  <tr nowrap class="tablecell">
                    <td colspan="2" align="center"><input type="submit" value="保存" class="formbutton">
                      <input type="reset" value="重置" class="formbutton">
                    </td>
                  </tr>
                </form>
                <!--
EOT;
} elseif ($action == 'del') {print <<<EOT
-->
                <form action="admincp.php?job=template" method="post" name="form">
                  <tr class="alertheader">
                    <td colspan="1"><a name="删除模板"></a>删除模板</td>
                  </tr>
                  <tr>
                    <td class="alertbox"><p>模板套系: <a href="admincp.php?job=template&action=filelist&path=$path">$path</a></p>
                      <p>模板文件: <a href="admincp.php?job=template&action=mod&path=$path&file=$file">$file</a></p>
                      <p><b>注意: 删除模板文件将不会显示和该模板有关的一切页面，确定吗？</b></p>
                      <p>
                        <input type="submit" value="确认" class="formbutton">
                      </p>
                      <input type="hidden" name="path" value="$path">
                      <input type="hidden" name="file" value="$file">
					  <input type="hidden" name="ext" value="$ext">
                      <input type="hidden" name="action" value="delfile">
                    </td>
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
