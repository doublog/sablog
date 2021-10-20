<!--<?php
if(!defined('IN_SABLOG')) {
	exit('Access Denied');
}
print <<<EOT
-->
<div class="postinfo">
<div class="left">文章: <a href="../?action=show&amp;id=$article[articleid]">$article[title]</a> [<a href="###" onclick="this.style.visibility='hidden';window.print();this.style.visibility='visible'">打印</a>]</div>
<div class="right">分类: <a href="../?action=index&amp;cid=$article[cid]">$article[cname]</a></div>
</div>
<div class="post">
<div class="posttop">
<div class="username">作者: $article[username]</div>
<div class="dateline">$article[dateline]</div>
</div><!--
EOT;
if (!$article['allowread']) {print <<<EOT
-->
<div style="padding:20px;">这篇日志被加密了,<a href="../?action=show&amp;id=$article[articleid]">请返回查看完整版本.</a></div>
<!--
EOT;
} else {print <<<EOT
-->
<div class="posttext">$article[content]</div>
<!--
EOT;
if ($attachnum) {
print <<<EOT
--><div class="posttext"><div class="attach-desc">本文有${attachnum}个附件</div></div><!--
EOT;
}
if ($article['keywords']) {
print <<<EOT
--><div class="posttext"><b>标签</b>: $article[tags]</div><!--
EOT;
}
print <<<EOT
-->
</div>
<!--
EOT;
if ($article['trackbacks'] && $options['enable_trackback']) {print <<<EOT
-->
<hr />
<!--
EOT;
foreach($trackbackdb as $key => $trackback){print <<<EOT
-->
<div class="post">
<div class="posttop">
<div class="username">引用来自: <a href="$trackback[url]" target="_blank">$trackback[blog_name]</a></div>
<div class="dateline">$trackback[dateline]</div>
</div>
<div class="posttext">$trackback[excerpt]</div>
</div>
<!--
EOT;
}}
if ($article['comments']) {print <<<EOT
-->
<hr />
<!--
EOT;
foreach($commentdb as $key => $comment){print <<<EOT
-->
<div class="post">
<div class="posttop">
<div class="username">评论作者: $comment[author]</div>
<div class="dateline">$comment[dateline] / #<strong>$comment[cmtorderid]</strong></div>
</div>
<div class="posttext">$comment[content]</div>
</div>
<!--
EOT;
}}}
if (!$article['allowread']) {print <<<EOT
--></div><!--
EOT;
}
?>