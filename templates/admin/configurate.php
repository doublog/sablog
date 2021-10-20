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
          <div class="tableheader">系统设置</div>
          <div class="leftmenubody">
            <!--
EOT;
foreach ($settingsmenu as $key => $value) {print <<<EOT
-->
            <div class="leftmenuitem">&#8226; <a href="admincp.php?job=configurate&amp;type=$key">$value</a></div>
            <!--
EOT;
}print <<<EOT
-->
          </div>
        </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top"><form action="admincp.php?job=configurate" method="post">
          <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                  <!--
EOT;
if(!$type || $type=='basic'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">基本设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>博客名称:</b><br />
                      如果没有设置LOGO图片，则显示文本名称</td>
                    <td><input class="formfield" type="text" name="setting[name]" size="35" maxlength="50" value="$settings[name]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>博客地址:</b><br />
                      如果不填写此项将自动探测地址,假如您使用了多镜像服务器,请留空此项,否则镜像将不起作用.<br />
					  也可以填写 <u>http://{host}/blog</u> 来表示自动探测地址.</td>
                    <td><input class="formfield" type="text" name="setting[url]" size="35" maxlength="50" value="$settings[url]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>博客描述:</b></td>
                    <td><input class="formfield" type="text" name="setting[description]" size="35" maxlength="255" value="$settings[description]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>信息产业部网站备案号:</b></td>
                    <td><input class="formfield" type="text" name="setting[icp]" size="35" maxlength="50" value="$settings[icp]"></td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='display'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">显示设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>默认浏览模式:</b><br />
                      标准模式则显示文章内容,列表模式仅仅显示文章标题和阅读量这些简单信息.</td>
                    <td><select name="setting[viewmode]">
                        <option value="normal" $viewmode[normal]>标准模式</option>
                        <option value="list" $viewmode[list]>列表模式</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>标准模式每页显示文章的数量:</b><br />
                      默认10.</td>
                    <td><input class="formfield" type="text" name="setting[normal_shownum]" size="15" maxlength="50" value="$settings[normal_shownum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>列表模式每页显示文章的数量:</b><br />
                      默认50.</td>
                    <td><input class="formfield" type="text" name="setting[list_shownum]" size="15" maxlength="50" value="$settings[list_shownum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>文章排列依据:</b><br />
                      文章时间可以更改,如果修改时间则可能更改文章排列，时间是根据 dateline 字段降序排列。<br />
                      文章提交顺序不可以更改，即使修改过文章的时间也不会影响文章的排列，提交顺序是根据 articleid 主键降序排列。</td>
                    <td><select name="setting[article_order]">
                        <option value="articleid" $article_order[articleid]>按文章提交顺序</option>
                        <option value="dateline" $article_order[dateline]>按文章时间</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>文章列表标题截取字节数:</b><br />
                      因为模板的不同，如果设置过多，可能会把表格撑变形。根据界面美观设置.如果设置为0表示不截取.</td>
                    <td><input class="formfield" type="text" name="setting[title_limit]" size="15" maxlength="50" value="$settings[title_limit]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>标签列表每页的数量:</b></td>
                    <td><input class="formfield" type="text" name="setting[tags_shownum]" size="15" maxlength="50" value="$settings[tags_shownum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>相关文章显示数量:</b><br />
                      浏览文章的时候,可以显示使用相同标签的文章，选择不显示在浏览文章的时候减少一次查询以提高程序执行效率.建议不要设置太大,建议设置10,设置为0表示不显示相关文章.</td>
                    <td><input class="formfield" type="text" name="setting[related_shownum]" size="15" maxlength="50" value="$settings[related_shownum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>相关文章标题截取字数:</b><br />
                      因为模板的不同,如果设置过多,可能会把表格撑变形.根据界面美观设置.如果设置为0表示不截取.</td>
                    <td><input class="formfield" type="text" name="setting[related_title_limit]" size="15" maxlength="50" value="$settings[related_title_limit]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>相关文章排列依据:</b></td>
                    <td><select name="setting[related_order]">
                        <option value="dateline" $related_order[dateline]>按文章添加时间</option>
                        <option value="views" $related_order[views]>按文章阅读次数</option>
                        <option value="comments" $related_order[comments]>按文章评论数量</option>
                      </select>
                    </td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='sidebar'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">侧栏设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>显示日历:</b><br />
                      侧边栏的日历.</td>
                    <td><select name="setting[show_calendar]">
                        <option value="1" $show_calendar_Y>是</option>
                        <option value="0" $show_calendar_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>显示分类列表:</b><br />
                      侧边栏的分类列表</td>
                    <td><select name="setting[show_categories]">
                        <option value="1" $show_categories_Y>是</option>
                        <option value="0" $show_categories_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>显示热门标签的数量:</b><br />
                      首页测栏的热门标签显示数量,设置0则表示不显示.</td>
                    <td><input class="formfield" type="text" name="setting[hottags_shownum]" size="15" maxlength="50" value="$settings[hottags_shownum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>显示按月归档:</b><br />
                      侧边栏的归档列表</td>
                    <td><select name="setting[show_archives]">
                        <option value="1" $show_archives_Y>是</option>
                        <option value="0" $show_archives_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>按月归档显示最近多少个月:</b><br />
                      设置0则全部显示.</td>
                    <td><input class="formfield" type="text" name="setting[archives_num]" size="15" maxlength="50" value="$settings[archives_num]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>侧栏最新评论数量:</b><br />
                      侧边栏的最新评论显示数量,设置0则表示不显示.</td>
                    <td><input class="formfield" type="text" name="setting[recentcomment_num]" size="15" maxlength="50" value="$settings[recentcomment_num]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>侧栏最新评论截取字节数:</b><br />
                      因为模板的不同,如果设置过多,可能会把表格撑变形.根据界面美观设置.</td>
                    <td><input class="formfield" type="text" name="setting[recentcomment_limit]" size="15" maxlength="50" value="$settings[recentcomment_limit]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>显示博客统计:</b><br />
                      侧边栏的统计信息</td>
                    <td><select name="setting[show_statistics]">
                        <option value="1" $show_statistics_Y>是</option>
                        <option value="0" $show_statistics_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>侧栏显示友情链接数量:</b><br />
                      “0”则不显示, 如果友情连接数量大于设置的数量, 则显示“更多..”的链接.</td>
                    <td><input class="formfield" type="text" name="setting[sidebarlinknum]" size="15" maxlength="50" value="$settings[sidebarlinknum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>侧栏友情链接是否随机显示:</b><br />
                      当友情连接数量大于上面设置的数量本选项才生效</td>
                    <td><select name="setting[random_links]">
                        <option value="1" $random_links_Y>是</option>
                        <option value="0" $random_links_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='comment'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">评论设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>评论需要审核:</b><br />
                      访客发表的评论需要管理员在后台审核过才会在前台显示</td>
                    <td><select name="setting[audit_comment]">
                        <option value="1" $audit_comment_Y>是</option>
                        <option value="0" $audit_comment_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>评论启用验证码:</b><br />
                      图片验证码可以避免用灌水或刷新程序恶意批量发布或提交信息.<br />
                      $gd_version</td>
                    <td><select name="setting[seccode]">
                        <option value="1" $seccode_Y>是</option>
                        <option value="0" $seccode_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>新评论排列顺序:</b></td>
                    <td><select name="setting[comment_order]">
                        <option value="1" $comment_order_Y>靠后</option>
                        <option value="0" $comment_order_N>靠前</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>单篇文章显示评论数:</b><br />
                      如果评论特别多的话，可以设置一页显示多少条评论，设为“0”则显示全部评论</td>
                    <td><input class="formfield" type="text" name="setting[article_comment_num]" size="15" maxlength="50" value="$settings[article_comment_num]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>评论内容的最少字节数:</b><br />
                      两个字节是一个汉字</td>
                    <td><input class="formfield" type="text" name="setting[comment_min_len]" size="15" maxlength="50" value="$settings[comment_min_len]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>评论内容允许最大字数:</b><br />
                      可以有效控制游客输入内容的数据量</td>
                    <td><input class="formfield" type="text" name="setting[comment_max_len]" size="15" maxlength="50" value="$settings[comment_max_len]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>评论列表显示数量:</b><br />
                      评论列表每页显示的评论条数</td>
                    <td><input class="formfield" type="text" name="setting[commentlist_num]" size="15" maxlength="50" value="$settings[commentlist_num]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>提交评论时间间隔:</b><br />
                      可以防止他人灌水，设为“0”则不限制</td>
                    <td><input class="formfield" type="text" name="setting[comment_post_space]" size="15" maxlength="50" value="$settings[comment_post_space]"></td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='search'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">搜索设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>是否允许搜索评论:</b><br />
                      一般很少人搜索评论，看情况设置</td>
                    <td><select name="setting[allow_search_comments]">
                        <option value="1" $allow_search_comments_Y>是</option>
                        <option value="0" $allow_search_comments_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>搜索间隔:</b><br />
                      使用搜索功能的时间间隔，设为“0”则不限制</td>
                    <td><input class="formfield" type="text" name="setting[search_post_space]" size="15" maxlength="50" value="$settings[search_post_space]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>搜索关键字的最少字节数:</b><br />
                      至少输入多少个字节才可以进行搜索，设为“0”则不限制</td>
                    <td><input class="formfield" type="text" name="setting[search_keywords_min_len]" size="15" maxlength="50" value="$settings[search_keywords_min_len]"></td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='attach'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">附件设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>附件存放目录:</b><br />
                      相对于程序根目录,不要加“/”</td>
                    <td><input class="formfield" type="text" name="setting[attachments_dir]" size="35" maxlength="50" value="$settings[attachments_dir]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>附件存放方式:</b><br />
                      为了方便管理附件请选择一个适合您服务器情况的方式</td>
                    <td><select name="setting[attachments_save_dir]">
                        <option value="0" $attachments_save_dir[0]>全部存放同一目录</option>
                        <option value="1" $attachments_save_dir[1]>按分类存放</option>
                        <option value="2" $attachments_save_dir[2]>按月份存放</option>
                        <option value="3" $attachments_save_dir[3]>按文件类型存放</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>文章列表的附件显示方式:</b><br />
                      选择一个合适你模板的显示方式</td>
                    <td><select name="setting[attachments_display]">
                        <option value="0" $attachments_display[0]>显示全部附件</option>
                        <option value="1" $attachments_display[1]>提示该文章有附件</option>
                        <option value="2" $attachments_display[2]>不显示也不提示</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>图片附件生成缩略图:</b><br />
                      如果选“是”，上传的附件为图片，尺寸大于下面的设置，就生成缩略图，以减少页面输出带宽。需要GD库支持</td>
                    <td><select name="setting[attachments_thumbs]">
                        <option value="1" $attachments_thumbs_Y>是</option>
                        <option value="0" $attachments_thumbs_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>附件图片缩略图大小:</b><br />
                      如果开启了缩略图选项，请在这里定义缩略图的大小。例如：150x150</td>
                    <td><input class="formfield" type="text" name="setting[attachments_thumbs_size]" size="35" maxlength="50" value="$settings[attachments_thumbs_size]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>图片附件是否允许直接显示:</b><br />
                      如果选“是”,如果是图片附件,就直接显示出来,否则就会提示保存到本地.</td>
                    <td><select name="setting[display_attach]">
                        <option value="1" $display_attach_Y>是</option>
                        <option value="0" $display_attach_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>附件是否禁止从其他站查看:</b><br />
                      如果选“是”,就禁止直接从地址栏输入附件地址访问,也不允许从其他站点直接点击本站的附件地址访问.只能从附件所属文章点击.反之不做任何限制.</td>
                    <td><select name="setting[remote_open]">
                        <option value="1" $remote_open_Y>是</option>
                        <option value="0" $remote_open_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='watermark'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">水印设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>是否使用图片水印功能:</b><br />
                      上传的图片中加上图片水印,水印图片位于 ./templates/$options[templatename]/img/watermark.png,您可替换此文件以实现不同的水印效果.不支持动画 GIF 格式.</td>
                    <td><select name="setting[watermark]">
                        <option value="1" $watermark_Y>是</option>
                        <option value="0" $watermark_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>添加水印的图片大小控制:</b><br />
                      只对超过程序设置的大小的附件图片才加上水印图片或文字，如果留空则不做限制。例如：150x150</td>
                    <td><input class="formfield" type="text" name="setting[watermark_size]" size="35" maxlength="50" value="$settings[watermark_size]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>水印位置:</b><br />
                      如果开启了上面的水印功能,并且使用图片水印的话,请在这里设定图片水印出现的位置.</td>
                    <td><select name="setting[waterpos]">
                        <option value="1" $waterpos[1]>左上</option>
                        <option value="2" $waterpos[2]>左下</option>
                        <option value="3" $waterpos[3]>右上</option>
                        <option value="4" $waterpos[4]>右下</option>
                        <option value="5" $waterpos[5]>中间</option>
                        <option value="6" $waterpos[6]>随机</option>
                      </select></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>水印透明度:</b><br />
                      范围为 1~100 的整数,数值越大水印图片透明度越低.本功能需要开启水印功能后才有效.</td>
                    <td><input class="formfield" type="text" name="setting[watermarktrans]" size="35" maxlength="50" value="$settings[watermarktrans]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>水印边距:</b><br />
                      图片或者文字水印位于原图边缘的距离.请填入大于0的整数,不填默认为 5px.</td>
                    <td><input class="formfield" type="text" name="setting[pos_padding]" size="35" maxlength="50" value="$settings[pos_padding]"></td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='dateline'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">时间设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>服务器所在时区:</b><br />
                      Sablog-X所在的服务器是放在地球的哪个时区？</td>
                    <td><select name="setting[server_timezone]">
                        <option value="-12" $zone_012>(标准时-12:00) 日界线西</option>
                        <option value="-11" $zone_011>(标准时-11:00) 中途岛、萨摩亚群岛</option>
                        <option value="-10" $zone_010>(标准时-10:00) 夏威夷</option>
                        <option value="-9" $zone_09>(标准时-9:00) 阿拉斯加</option>
                        <option value="-8" $zone_08>(标准时-8:00) 太平洋时间(美国和加拿大)</option>
                        <option value="-7" $zone_07>(标准时-7:00) 山地时间(美国和加拿大)</option>
                        <option value="-6" $zone_06>(标准时-6:00) 中部时间(美国和加拿大)、墨西哥城</option>
                        <option value="-5" $zone_05>(标准时-5:00) 东部时间(美国和加拿大)、波哥大</option>
                        <option value="-4" $zone_04>(标准时-4:00) 大西洋时间(加拿大)、加拉加斯</option>
                        <option value="-3.5" $zone_03_5>(标准时-3:30) 纽芬兰</option>
                        <option value="-3" $zone_03>(标准时-3:00) 巴西、布宜诺斯艾利斯、乔治敦</option>
                        <option value="-2" $zone_02>(标准时-2:00) 中大西洋</option>
                        <option value="-1" $zone_01>(标准时-1:00) 亚速尔群岛、佛得角群岛</option>
                        <option value="111" $zone_111>(格林尼治标准时) 西欧时间、伦敦、卡萨布兰卡</option>
                        <option value="1" $zone_1>(标准时+1:00) 中欧时间、安哥拉、利比亚</option>
                        <option value="2" $zone_2>(标准时+2:00) 东欧时间、开罗，雅典</option>
                        <option value="3" $zone_3>(标准时+3:00) 巴格达、科威特、莫斯科</option>
                        <option value="3.5" $zone_3_5>(标准时+3:30) 德黑兰</option>
                        <option value="4" $zone_4>(标准时+4:00) 阿布扎比、马斯喀特、巴库</option>
                        <option value="4.5" $zone_4_5>(标准时+4:30) 喀布尔</option>
                        <option value="5" $zone_5>(标准时+5:00) 叶卡捷琳堡、伊斯兰堡、卡拉奇</option>
                        <option value="5.5" $zone_5_5>(标准时+5:30) 孟买、加尔各答、新德里</option>
                        <option value="6" $zone_6>(标准时+6:00) 阿拉木图、 达卡、新亚伯利亚</option>
                        <option value="7" $zone_7>(标准时+7:00) 曼谷、河内、雅加达</option>
                        <option value="8" $zone_8>(北京时间) 北京、重庆、香港、新加坡</option>
                        <option value="9" $zone_9>(标准时+9:00) 东京、汉城、大阪、雅库茨克</option>
                        <option value="9.5" $zone_9_5>(标准时+9:30) 阿德莱德、达尔文</option>
                        <option value="10" $zone_10>(标准时+10:00) 悉尼、关岛</option>
                        <option value="11" $zone_11>(标准时+11:00) 马加丹、索罗门群岛</option>
                        <option value="12" $zone_12>(标准时+12:00) 奥克兰、惠灵顿、堪察加半岛</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>标准模式文章的日期格式:</b><br />
                      Y, F j, g:i A 显示为 2005, May 10, 2:12 PM</td>
                    <td><input class="formfield" type="text" name="setting[normaltime]" size="35" maxlength="50" value="$settings[normaltime]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>列表模式文章的日期格式:</b><br />
                      Y-m-d 显示为 2005-05-10</td>
                    <td><input class="formfield" type="text" name="setting[listtime]" size="35" maxlength="50" value="$settings[listtime]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>评论内容的日期格式:</b><br />
                      Y, F j, g:i A 显示为 2005, May 10, 2:12 PM</td>
                    <td><input class="formfield" type="text" name="setting[comment_timeformat]" size="35" maxlength="50" value="$settings[comment_timeformat]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>Trackback内容的日期格式:</b><br />
                      Y, F j, g:i A 显示为 2005, May 10, 2:12 PM</td>
                    <td><input class="formfield" type="text" name="setting[trackback_timeformat]" size="35" maxlength="50" value="$settings[trackback_timeformat]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>侧栏评论内容的日期格式:</b><br />
                      m-d 显示为 05-10</td>
                    <td><input class="formfield" type="text" name="setting[recent_comment_timeformat]" size="35" maxlength="50" value="$settings[recent_comment_timeformat]"></td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='func'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">功能设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>关闭博客:</b><br />
                      选择“是”任何人将不能访问Blog</td>
                    <td><select name="setting[close]">
                        <option value="1" $close_Y>是</option>
                        <option value="0" $close_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>关闭的原因:</b><br />
                      关闭博客的原因</td>
                    <td><textarea id="close_note" class="formarea" type="text" name="setting[close_note]" style="width:300px;height:80px;">$settings[close_note]</textarea> <b><a href="###" onclick="resizeup('close_note');">[+]</a> <a href="###" onclick="resizedown('close_note');">[-]</a></b></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>页面Gzip压缩:</b><br />
                      将页面内容以 gzip 压缩后传输,可以加快传输速度，需 PHP 4.0.4 以上且支持 Zlib 模块才能使用</td>
                    <td><select name="setting[gzipcompress]">
                        <option value="1" $gzipcompress_Y>是</option>
                        <option value="0" $gzipcompress_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>显示信息提示页面:</b><br />
                      选择是“是”将在提交内容后，显示提示信息，否则直接跳转页面。建议流量非常大的选择是“是”。此开关影响发表评论、搜索引擎、跳转最新评论、跳转上一篇或下一篇文章。</td>
                    <td><select name="setting[showmsg]">
                        <option value="1" $showmsg_Y>是</option>
                        <option value="0" $showmsg_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='user'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">用户设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>禁止注册:</b><br />
                      选择“是”将禁止游客注册，但不影响过去已注册的用户</td>
                    <td><select name="setting[closereg]">
                        <option value="1" $closereg_Y>是</option>
                        <option value="0" $closereg_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>注册的用户名保留关键字:</b><br />
                      注册的用户名中无法使用这些关键字。每个关键字用半角逗号隔开，如 angel,4ngel<br />
                      <u><b>访客同样无法使用这些关键字作为用户名发表评论。</b></u></td>
                    <td><textarea id="censoruser" class="formarea" type="text" name="setting[censoruser]" style="width:300px;height:80px;">$settings[censoruser]</textarea> <b><a href="###" onclick="resizeup('censoruser');">[+]</a> <a href="###" onclick="resizedown('censoruser');">[-]</a></b></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>注册启用验证码:</b><br />
                      启用后可防止一些注册机注册大量用户.<br />
                      $gd_version</td>
                    <td><select name="setting[seccode_enable]">
                        <option value="1" $seccode_enable_Y>是</option>
                        <option value="0" $seccode_enable_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='trackback'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">Trackback设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>是否启用Trackback:</b><br />
                      这里控制所有的Trackback.如果不启用,Trackback功能将失效,并且也不会显示任何有关Trackback的链接.</td>
                    <td><select name="setting[enable_trackback]">
                        <option value="1" $enable_trackback_Y>是</option>
                        <option value="0" $enable_trackback_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>Trackback需要审核:</b><br />
                      可以修改<a href="admincp.php?job=configurate&type=ban">Trackback Spam机制防范等级</a>来防范大部分垃圾信息,防范Spam不会检查内容是否为国家法律允许内容,如果有比较敏感内容,建议开启人工审核所有接收的Trackback.</td>
                    <td><select name="setting[audit_trackback]">
                        <option value="1" $audit_trackback_Y>是</option>
                        <option value="0" $audit_trackback_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>Trackback有效期控制:</b><br />
                      如果开启该项,只有文章发表后的24小时内允许Trackback.</td>
                    <td><select name="setting[trackback_life]">
                        <option value="1" $trackback_life_Y>是</option>
                        <option value="0" $trackback_life_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>Trackback列表显示数量:</b><br />
                      这个设置将控制所有或单篇文章的Trackback列表每页的数量</td>
                    <td><input class="formfield" type="text" name="setting[trackback_num]" size="15" maxlength="50" value="$settings[trackback_num]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>文章中Trackback内容截取字节数:</b><br />
                      设置超过255个系统将忽略此选项</td>
                    <td><input class="formfield" type="text" name="setting[trackback_excerpt_limit]" size="15" maxlength="50" value="$settings[trackback_excerpt_limit]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>Trackback列表中内容截取字节数:</b><br />
                      如果设置超过255个系统将忽略此选项</td>
                    <td><input class="formfield" type="text" name="setting[trackback_list_excerpt_limit]" size="15" maxlength="50" value="$settings[trackback_list_excerpt_limit]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>新Trackback在文章中的排列顺序:</b></td>
                    <td><select name="setting[trackback_order]">
                        <option value="1" $trackback_order_Y>靠后</option>
                        <option value="0" $trackback_order_N>靠前</option>
                      </select>
                    </td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='seo'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">搜索引擎优化</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>启用Sablog-X Archiver:</b><br />
                      Sablog-X Archiver 能够将公开的内容模拟成静态页面，以便搜索引擎获取其中的内容。</td>
                    <td><select name="setting[smarturl]">
                        <option value="1" $smarturl_Y>是</option>
                        <option value="0" $smarturl_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>模拟静态文件的扩展名:</b><br />
                      可以是htm,html,php,asp,cgi,jsp等其中一个，推荐使用htm或html</td>
                    <td><input class="formfield" type="text" name="setting[artlink_ext]" size="35" value="$settings[artlink_ext]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top" width="60%"><b>标题附加字:</b><br />
                      网页标题通常是搜索引擎关注的重点，本附加字设置将出现在标题中，如果有多个关键字，建议用 &quot;|&quot;、&quot;,&quot; 等符号分隔</td>
                    <td><textarea id="title_keywords" class="formarea" type="text" name="setting[title_keywords]" style="width:300px;height:80px;">$settings[title_keywords]</textarea> <b><a href="###" onclick="resizeup('title_keywords');">[+]</a> <a href="###" onclick="resizedown('title_keywords');">[-]</a></b></td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top" width="60%"><b>Meta Keywords:</b><br />
                      Keywords 项出现在页面头部的 Meta 标签中，用于记录本页面的关键字，多个关键字间请用半角逗号 &quot;,&quot; 隔开</td>
                    <td><textarea id="meta_keywords" class="formarea" type="text" name="setting[meta_keywords]" style="width:300px;height:80px;">$settings[meta_keywords]</textarea> <b><a href="###" onclick="resizeup('meta_keywords');">[+]</a> <a href="###" onclick="resizedown('meta_keywords');">[-]</a></b></td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top" width="60%"><b>Meta Description:</b><br />
                      Description 出现在页面头部的 Meta 标签中，用于记录本页面的概要与描述</td>
                    <td><textarea id="meta_description" class="formarea" type="text" name="setting[meta_description]" style="width:300px;height:80px;">$settings[meta_description]</textarea> <b><a href="###" onclick="resizeup('meta_description');">[+]</a> <a href="###" onclick="resizedown('meta_description');">[-]</a></b></td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='wap'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">WAP设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>是否开启WAP功能:</b><br />
                      WAP是一种无线通信应用协议,开启WAP后用户可通过手机访问你的博客,实现浏览,评论等功能，你自己亦可以通过手机使用本功能发表文章.</td>
                    <td><select name="setting[wap_enable]">
                        <option value="1" $wap_enable_Y>是</option>
                        <option value="0" $wap_enable_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>WAP文章列表每页文章数:</b></td>
                    <td><input class="formfield" type="text" name="setting[wap_article_pagenum]" size="15" maxlength="50" value="$settings[wap_article_pagenum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>WAP文章列表标题截取字符数:</b></td>
                    <td><input class="formfield" type="text" name="setting[wap_article_title_limit]" size="15" maxlength="50" value="$settings[wap_article_title_limit]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>WAP每页标签Tags数:</b></td>
                    <td><input class="formfield" type="text" name="setting[wap_tags_pagenum]" size="15" maxlength="50" value="$settings[wap_tags_pagenum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>WAP每页评论数:</b></td>
                    <td><input class="formfield" type="text" name="setting[wap_comment_pagenum]" size="15" maxlength="50" value="$settings[wap_comment_pagenum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>WAP每页引用数:</b></td>
                    <td><input class="formfield" type="text" name="setting[wap_trackback_pagenum]" size="15" maxlength="50" value="$settings[wap_trackback_pagenum]"></td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='ban'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">限制设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>开启IP禁止功能:</b><br />
                      选择“是”将杜绝下面设置的IP提交评论.</td>
                    <td><select name="setting[banip_enable]">
                        <option value="1" $banip_enable_Y>是</option>
                        <option value="0" $banip_enable_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>禁止IP:</b><br />
                      输入禁止发表评论的IP地址,可以使用"*"作为通配符禁止某段地址,用","格开.</td>
                    <td><textarea id="ban_ip" class="formarea" type="text" name="setting[ban_ip]" style="width:300px;height:80px;">$settings[ban_ip]</textarea> <b><a href="###" onclick="resizeup('ban_ip');">[+]</a> <a href="###" onclick="resizedown('ban_ip');">[-]</a></b></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>开启Spam机制:</b><br />
                      Spam是指利用程序进行广播式的广告宣传的行为.这种行为给很多人的信箱、留言、评论里塞入大量无关或无用的信息.开启后以下设置才生效.</td>
                    <td><select name="setting[spam_enable]">
                        <option value="1" $spam_enable_Y>是</option>
                        <option value="0" $spam_enable_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>垃圾词语特征:</b><br />
                      开启Spam机制后,系统将用这里设置的词语匹配,不管程序还是人工发表,如果包含了则表示有可能是垃圾信息,需要人工审核.用","格开.设置的垃圾词语在开启Spam机制后,应用在评论、Trackback的内容中.</td>
                    <td><textarea id="spam_words" class="formarea" type="text" name="setting[spam_words]" style="width:300px;height:80px;">$settings[spam_words]</textarea> <b><a href="###" onclick="resizeup('spam_words');">[+]</a> <a href="###" onclick="resizedown('spam_words');">[-]</a></b></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>评论中允许出现的链接次数:</b><br />
                      如果出现的链接数大于所设置的数量,则怀疑是垃圾信息,需要人工审核.如果设置为"0"则不限制.</td>
                    <td><input class="formfield" type="text" name="setting[spam_url_num]" size="15" maxlength="50" value="$settings[spam_url_num]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>评论内容字节数:</b><br />
                      和上面的"评论内容允许最大字数"不同,超过这里设置的字节数则怀疑是垃圾信息,需要人工审核,如果设置为"0"或大于上面的最大字数则不启用此设置.</td>
                    <td><input class="formfield" type="text" name="setting[spam_content_size]" size="15" maxlength="50" value="$settings[spam_content_size]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>Trackback Spam机制防范等级:</b><br />
                      强的等级过滤最为严格,包括人工发送的重复性,弱等级会对数据来源做基本检查.无则不做任何检查直接通过验证.</td>
                    <td><select name="setting[tb_spam_level]">
                        <option value="strong" $tb_spam_level[strong]>强</option>
                        <option value="weak" $tb_spam_level[weak]>弱</option>
                        <option value="never" $tb_spam_level[never]>无</option>
                      </select>
                    </td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='js'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">JS调用设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>开启JS调用功能:</b><br />
                      JS调用可以将您的最新文章、热门文章、最新评论、统计信息等资料嵌入到您的普通网页中.</td>
                    <td><select name="setting[js_enable]">
                        <option value="1" $js_enable_Y>是</option>
                        <option value="0" $js_enable_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>JS数据缓存时间:</b><br />
                      JS调用采用动态缓存技术来实现数据的定期更新以达到减轻服务器的负担,否则直接调用将消耗较多的系统资源,默认值为3600秒机一个小时,设置为0则不缓存(极耗费系统资源)</td>
                    <td><input class="formfield" type="text" name="setting[js_cache_life]" size="15" maxlength="50" value="$settings[js_cache_life]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td valign="top"><b>JS来路限制:</b><br />
                      只允许列表中的域名才可以使用JS调用功能,每个域名一行,请勿包含 http:// 或其他非域名内容,留空为不限制来路,即任何网站均可调用.但是多网站调用会加重您的服务器负担.</td>
                    <td><textarea id="js_lock_url" class="formarea" type="text" name="setting[js_lock_url]" style="width:300px;height:80px;">$settings[js_lock_url]</textarea> <b><a href="###" onclick="resizeup('js_lock_url');">[+]</a> <a href="###" onclick="resizedown('js_lock_url');">[-]</a></b></td>
                  </tr>
                  <!--
EOT;
}
if(!$type || $type=='rss'){print <<<EOT
-->
                  <tr class="tdbheader">
                    <td colspan="2">RSS订阅设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>开启RSS订阅功能:</b><br />
                      开启后将允许用户使用 RSS 客户端软件接收最新的文章.</td>
                    <td><select name="setting[rss_enable]">
                        <option value="1" $rss_enable_Y>是</option>
                        <option value="0" $rss_enable_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>RSS 订阅文章数量:</b></td>
                    <td><input class="formfield" type="text" name="setting[rss_num]" size="15" maxlength="50" value="$settings[rss_num]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>RSS TTL(分钟):</b><br />
                      TTL(Time to Live) 是 RSS 2.0 的一项属性，用于控制订阅内容的自动刷新时间，时间越短则资料实时性就越高，但会加重服务器负担，通常可设置为 30～180 范围内的数值默认值为60分钟,设置为0则不缓存(稍微消耗系统资源)</td>
                    <td><input class="formfield" type="text" name="setting[rss_ttl]" size="15" maxlength="50" value="$settings[rss_ttl]"></td>
                  </tr>
                  <!--
EOT;
}print <<<EOT
-->
                  <input type="hidden" name="action" value="updatesetting" />
                  <input type="hidden" name="type" value="$type" />
                  <tr class="tablecell">
                    <td colspan="2" align="center"><input type="submit" value="提交" class="formbutton">
                      <input type="reset" value="重置" class="formbutton">
                    </td>
                  </tr>
                  <tr>
                    <td class="tablebottom" colspan="2"></td>
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
