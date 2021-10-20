<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<div id="footer">本站采用<a href="http://creativecommons.org/licenses/by-nc-sa/2.5/deed.zh" target="_blank">创作共享</a>版权协议, 要求署名、非商业和保持一致. 本站欢迎任何非商业应用的转载, 但须注明出自"<a href="$options[url]">$options[name]</a>", 保留原始链接, 此外还必须标注原文标题和链接.
<br />Supported by <a href="http://www.4ngel.net" target="_blank">Security Angel Team</a>. Powered by <a href="http://www.sablog.net" target="_blank">SaBlog-X</a>. Copyright &copy; 2004-2006 <a href="$options[url]">$options[name]</a><!--
EOT;
if($options['show_debug']){print <<<EOT
--><br />$sa_debug<!--
EOT;
}
print <<<EOT
--> <a href="http://validator.w3.org/check?uri=referer" target="_blank">XHTML 1.0</a>. <a href="post.php?action=clearcookies">清除Cookies</a>.
<!--
EOT;
if($options['icp']){print <<<EOT
-->
<a href="http://www.miibeian.gov.cn/" target="_blank">$options[icp]</a>
<!--
EOT;
}print <<<EOT
-->
</div>
</div>
</body></html><!--
EOT;
?>-->