<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<h2 class="title">Links</h2><!--
EOT;
if ($link_count && $linkdb) {print <<<EOT
-->
<ul class="linkover">
<!--
EOT;
foreach($linkdb as $key => $link){print <<<EOT
-->
	<li class="onelink"><a href="$link[url]" target="_blank">$link[name]</a><br />
	$link[note]</li>
<!--
EOT;
}print <<<EOT
-->
</ul>
<!--
EOT;
} else {print <<<EOT
-->
<p><strong>没有任何友情链接</strong></p>
<!--
EOT;
}
?>