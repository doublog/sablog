<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<script type="text/javascript">
function showhide(id){
	if(id == "search_article") {
		$("search_article").style.display='block';
		$("search_comment").style.display='none';
	} else {
		$("search_article").style.display='none';
		$("search_comment").style.display='block';
	}
}
</script>
<h2 class="title">高级搜索</h2>
<form method="post" action="post.php">
<input type="hidden" name="formhash" value="$formhash" />
<input type="hidden" name="action" value="search" />
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="formbox">
    <tr>
      <td colspan="3">关键字:<br />
          <input name="keywords" type="text" size="30" value="" class="formfield" />
          关键字中可使用通配符 &quot;*&quot;<br />
          <br />
          匹配多个关键字全部, 可用空格或 &quot;AND&quot; 连接. 如 angel AND 4ngel<br />匹配多个关键字其中部分, 可用 &quot;|&quot; 或 &quot;OR&quot; 连接. 如 angel OR 4ngel</td>
    </tr>
    <tr>
      <td colspan="3"><hr /></td>
    </tr>
    <tr>
      <td colspan="3"><fieldset>
<legend><strong>搜索范围</strong></legend>
<input name="searchfrom" type="radio" value="article" onclick='showhide("search_article");' checked="checked" />
搜索文章<br />
<!--
EOT;
if($options['allow_search_comments']){print <<<EOT
-->
<input name="searchfrom" type="radio" value="comment" onclick='showhide("search_comment");' />
搜索评论
<!--
EOT;
} print <<<EOT
--></fieldset></td>
    </tr>
    <tbody id="search_article" style="display:block">
    <tr>
      <td width="48%" valign="top"><fieldset>
      <legend><strong>搜索分类</strong></legend>
      <select name="cid[]" multiple="multiple" style="width:100%" size="8">
        <option value="" selected="selected">搜索所有分类</option>
<!--
EOT;
if($catecache){
foreach($catecache AS $data){print <<<EOT
-->
          <option value="$data[cid]">&nbsp;&nbsp;&#0124;-- $data[name]</option>
<!--
EOT;
}}print <<<EOT
-->
      </select><br />允许选择多个分类
</fieldset></td>
<td width="4%" align="center">&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td width="48%" valign="top"><fieldset>
      <legend><strong>搜索时间</strong></legend>
      <select name="dateline">
        <option value="" selected="selected">任何时间</option>
<!--
EOT;
if($archivecache){
foreach($archivecache AS $key => $val){
$v = explode('-', $key);
//$e_month = ($v[1] < 10) ? str_replace('0', '', $v[1]) : $v[1];
print <<<EOT
-->
        <option value="$v[0]$v[1]">{$v[0]}年{$v[1]}月</option>
<!--
EOT;
}}print <<<EOT
-->
      </select> 时间段内搜索
      </fieldset>
	  <fieldset>
<legend><strong>搜索方式</strong></legend>
<select name="searchin">
<option value="title" selected="selected">标题搜索</option>
<option value="content">全文搜索</option>
</select>
</fieldset>
<fieldset>
<legend><strong>搜索结果按照</strong></legend>
<select name="sortby">
<option value="dateline" selected="selected">发表时间</option>
<option value="views">阅读次数</option>
<option value="comments">评论数量</option>
<option value="cid">分类名称</option>
</select>
<select name="orderby">
<option value="desc" selected="selected">降序排列</option>
<option value="asc">升序排列</option>
</select>
</fieldset></td>
    </tr>
	</tbody>
    <tbody id="search_comment" style="display: none">
    <tr>
      <td colspan="3" valign="top"><fieldset>
<legend><strong>搜索方式</strong></legend>
<select name="csearchin">
<option value="content" selected="selected">全文搜索</option>
<option value="author">作者搜索</option>
</select>
</fieldset></td>
      </tr>
	</tbody>
    <tr>
      <td colspan="3" valign="top"><hr /></td>
   </tr>
    <tr>
      <td colspan="3" align="center"><button type="submit" class="formbutton">确定</button></td>
   </tr>
</table>
</form>
<!--
EOT;
?>