<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<script type="text/javascript">
window.onload=function(){ 
	fiximage('$options[attachments_thumbs_size]');
}
</script>
<div id="top"><span>浏览模式: 标准 | <a href="./?viewmode=list{$modelink}">列表</a></span><strong>$navtext</strong></div>
<!--
EOT;
if ($tatol) {
foreach($articledb as $key => $article){
print <<<EOT
--><h2 class="posttitle"><!--
EOT;
if($article['stick']){
print <<<EOT
-->[置顶] <!--
EOT;
}
print <<<EOT
--><a href="./?action=show&amp;id=$article[articleid]">$article[title]</a></h2>
<p class="postdate">Submitted by <strong><a href="./?action=finduser&amp;userid=$article[uid]">$article[username]</a></strong> on $article[dateline]</p><!--
EOT;
if (!$article['allowread']) {print <<<EOT
-->
<div class="needpwd"><form action="./?action=show&amp;id=$article[articleid]" method="post">这篇日志被加密了，请输入密码后查看。<br /><input class="formfield" type="password" name="readpassword" style="margin-right:5px;" /> <button class="formbutton" type="submit">提交</button></form></div>
<!--
EOT;
} else {print <<<EOT
-->
<div class="content">$article[content]
<!--
EOT;
if ($article['description']) {print <<<EOT
-->
<p>&raquo; <a href="./?action=show&amp;id=$article[articleid]">阅读全文</a></p>
<!--
EOT;
}print <<<EOT
--></div>
<!--
EOT;
if ($options['attachments_display'] == 0) {
if ($article['image']) {
foreach ($article['image'] as $image) {
if($image[6]){print <<<EOT
--><p class="attach">图片附件(缩略图):<br /><a href="attachment.php?id=$image[0]" target="_blank"><img src="$image[1]" border="0" alt="大小: $image[2]&#13;尺寸: $image[3] x $image[4]&#13;浏览: $image[5] 次&#13;点击打开新窗口浏览全图" width="$image[3]" height="$image[4]" /></a></p>
<!--
EOT;
} else {print <<<EOT
--><p class="attach">图片附件:<br /><a href="attachment.php?id=$image[0]" target="_blank"><img src="$image[1]" border="0" alt="大小: $image[2]&#13;尺寸: $image[3] x $image[4]&#13;浏览: $image[5] 次&#13;点击打开新窗口浏览全图" width="$image[3]" height="$image[4]" /></a></p>
<!--
EOT;
}}}
if($article['file']){
foreach($article['file'] as $file){
if($file){print <<<EOT
--><p class="attach"><strong>附件: </strong><a href="attachment.php?id=$file[0]" target="_blank">$file[1]</a> ($file[2], 下载次数:$file[3])</p>
<!--
EOT;
}}}} elseif ($options['attachments_display'] == 1) {
$imagenum = count($article['image']);
$filenum = count($article['file']);
$attachnum = $filenum+$imagenum;
if ($attachnum != 0) {
print <<<EOT
--><p class="attach"><span class="attach-desc">本文有${attachnum}个附件 (图片:$imagenum, 文件:$filenum)</span></p>
<!--
EOT;
}}
if ($article['keywords']) {
print <<<EOT
--><p class="tags"><strong>Tags</strong>: $article[alltags]</p>
<!--
EOT;
}
print <<<EOT
--><p class="postmetadata"><a href="./?action=index&amp;cid=$article[cid]">$article[cname]</a> | <a href="./?action=show&amp;id=$article[articleid]#comment">评论</a>:<font color="#CC0000">$article[comments]</font>
<!--
EOT;
if ($options['enable_trackback']) {print <<<EOT
--> | <a href="./?action=show&amp;id=$article[articleid]#trackbacks">Trackbacks</a>:<font color="#CC0000">$article[trackbacks]</font>
<!--
EOT;
}print <<<EOT
--> | <a href="./?action=show&amp;id=$article[articleid]">阅读</a>:<font color="#CC0000">$article[views]</font></p>
<!--
EOT;
}}print <<<EOT
-->
$multipage
<!--
EOT;
} else {print <<<EOT
-->
<p><strong>没有任何文章</strong></p>
<!--
EOT;
}
?>