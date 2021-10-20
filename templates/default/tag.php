<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<h2 class="title">Tags</h2><!--
EOT;
if ($stats['tag_count']) {print <<<EOT
-->
<div class="tags">
<!--
EOT;
foreach($tagdb as $key => $tag){
print <<<EOT
-->
<span style="line-height:160%;font-size:$tag[fontsize]px;margin-right:10px;"><a href="./?action=tags&amp;item=$tag[url]" title="使用次数: $tag[usenum]">$tag[item]</a></span>
<!--
EOT;
}
print <<<EOT
-->
</div>
$multipage
<!--
EOT;
} else {print <<<EOT
-->
<p><strong>没有任何标签</strong></p>
<!--
EOT;
}
?>