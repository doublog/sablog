<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="keywords" content="4ngel,4ngel.net,安全,天使,安全天使,技术,黑客,网络,原创,论坛,自由,严肃,网络安全,组织,系统安全,系统,windows,web,web安全,web开发,$options[meta_keywords]" />
<meta name="description" content="4ngel,4ngel.net,安全,天使,安全天使,技术,黑客,网络,原创,论坛,自由,严肃,网络安全,组织,系统安全,系统,windows,web,web安全,web开发,$options[meta_description]" />
<meta name="copyright" content="SaBlog" />
<meta name="author" content="angel,4ngel" />
<title>插入已有的标签</title>
<style>
body{
	margin:0px;
	background:#E3E3C7;
	border-width:0px
}
#Top{
	border-bottom:1px solid #D5D59D;
	padding:8px;
	color:#737357;
	font-size:18px
}
#Mid{
	font: 12px Verdana, Tahoma, sans-serif;
	height:250px;
	overflow:auto;
	background:#F1F1E3;
}
#Bottom{
	border-top:1px solid #D5D59D;
	padding:8px;
	color:#737357;
	text-align:right
}
input{
	border:1px solid #737357;
	color:#3B3B1F;
	background:#C7C78F;
	font-size:12px;
}
a{
	display:block;
	background:#D7D79F;
	padding:4px;
	font-size:12px;
	color:#3B3B1F;
	margin:4px;
	border:1px solid #D7D79F;
	text-decoration:none;
}
a:hover{
	background:#EFEFDA;
	border:1px solid #D7D79F;
}
</style>
<script type="text/javascript">
var tagnum = 0;
function addTag(tagName) {
	if (opener) {
		if (tagnum < 5) {
			var getTagObj=opener.document.forms[0].keywords
			var tags
			if (getTagObj.value.length>0) {
				tags=getTagObj.value.split(",")
				for (i=0;i<tags.length;i++){
					if (tags[i].toLowerCase()==tagName.toLowerCase()) return 
				}
				getTagObj.value+=","+tagName
			} else {
				getTagObj.value+=tagName
			}
			tagnum++
		}
	}
}
</script>
</head>
<body scroll="no">
<div id="Top"><b>插入已有的标签</b></div>
<div id="Mid">
<!--
EOT;
foreach($tagdb as $key => $tag){print <<<EOT
-->  <a href="#" onclick="addTag('$tag[item]')" title="插入 $tag[item]">$tag[item] ($tag[usenum])</a>
<!--
EOT;
}print <<<EOT
-->
</div>
<div id="Bottom"><input type="button" value="关闭" onclick="window.close()" />
</div>
</body>
</html>
<!--
EOT;
?>
-->