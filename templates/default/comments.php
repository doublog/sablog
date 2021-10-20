<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<h2 class="title">全部评论</h2>
<!--
EOT;
if ($tatol) {
foreach($commentdb as $key => $comment){print <<<EOT
--><p class="art-title"><a href="./?action=show&amp;id=$comment[articleid]">$comment[title]</a></p><p class="lesscontent">$comment[content]</p>
<p class="lessdate">Post by $comment[author] on $comment[dateline]</p>
<!--
EOT;
}print <<<EOT
-->
$multipage
<!--
EOT;
} else {print <<<EOT
-->
<p><strong>没有任何评论</strong></p>
<!--
EOT;
}
?>