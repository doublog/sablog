<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<h2 class="title">Archives</h2>
<!--
EOT;
if ($stats['article_count']) {
if ($options['smarturl']) {print <<<EOT
-->
<p><a href="archives/" target="_blank">查看全部归档明细</a></p>
<!--
EOT;
}
foreach($archivedb as $y => $arr){print <<<EOT
-->
<ul class="linkover">
  <p class="linkgroup">{$y}年</p>
<!--
EOT;
if (is_array($arr)) {
ksort($arr);
foreach($arr as $m => $num){print <<<EOT
-->
    <li><a href="./?action=index&amp;setdate={$y}{$m}">{$m}月</a> ($num)</li>
<!--
EOT;
}}print <<<EOT
-->
</ul>
<!--
EOT;
}} else {print <<<EOT
-->
<p><strong>没有任何归档</strong></p>
<!--
EOT;
}
?>