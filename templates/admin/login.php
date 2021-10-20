<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<div id="simpleHeader"></div>
<div class="loginBox">
<!--
EOT;
if ($sax_uid && ($sax_group == 1 || $sax_group == 2)) {print <<<EOT
-->
  <table border="0" cellpadding="5" cellspacing="1">
    <form method="post" action="admincp.php">
      <input type="hidden" name="action" value="login" />
      <tr>
        <td nowrap="nowrap"><h4>您好 $sax_user , 请输入您的密码</h4></td>
      </tr>
      <tr>
        <td nowrap="nowrap">密码:<br/>
          <input class="formfield" type="password" name="password" value="" style="width:150px" /></td>
      </tr>
      <tr>
        <td><input type="submit" class="formbutton" value="登陆" /></td>
      </tr>
    </form>
  </table>
<!--
EOT;
} else {print <<<EOT
--><h4>您没有权限访问后台</h4>
<!--
EOT;
}print <<<EOT
-->
</div><!--
EOT;
?>-->