<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<h2 class="title">Trackbacks</h2><!--
EOT;
if ($options['enable_trackback'] && $stats['trackback_count']) {print <<<EOT
-->
<!--
EOT;
foreach($trackbackdb as $key => $trackback){print <<<EOT
--><p class="art-title"><a href="./?action=show&amp;id=$trackback[articleid]">$trackback[article]</a></p>
<p class="lesscontent">标题: <a href="$trackback[url]" target="_blank">$trackback[title]</a><br />
来自: <a href="$trackback[url]" target="_blank">$trackback[blog_name]</a><br />
摘要: $trackback[excerpt]</p>
<p class="lessdate">Tracked on $trackback[dateline]</p>
<!--
EOT;
}print <<<EOT
-->
$multipage
<!--
EOT;
} else {print <<<EOT
-->
<p><strong>没有任何引用</strong></p>
<!--
EOT;
}
?>