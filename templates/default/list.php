<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<div id="top"><span>浏览模式: <a href="./?viewmode=normal{$modelink}">标准</a> | 列表</span><strong>$navtext</strong></div>
<!--
EOT;
if ($tatol) {print <<<EOT
-->
<ul class="list">
<!--
EOT;
foreach($articledb as $key => $article){
print <<<EOT
-->
<li>[$article[dateline]] - <!--
EOT;
if($article['stick']){
print <<<EOT
-->[置顶] <!--
EOT;
}
print <<<EOT
--><a href="./?action=show&amp;id=$article[articleid]">$article[title]</a> (浏览:<font color="#ff6600">$article[views]</font>,评论:<font color="#ff6600">$article[comments]</font>)</li>
<!--
EOT;
}print <<<EOT
-->
</ul>
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