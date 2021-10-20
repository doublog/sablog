<!--<?php
if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}
print <<<EOT
-->
<h2 class="title">修改资料</h2>
<!--
EOT;
if (!$sax_uid) {print <<<EOT
--><p>您没有登陆, 不能执行此操作, 请登陆后继续进行...</p><!--
EOT;
} else {print <<<EOT
-->
<form action="post.php" method="post">
<input type="hidden" name="action" value="modpro" />
<input type="hidden" name="formhash" value="$formhash">
<div class="formbox">
  <p>
    <label for="oldpassword">旧密码(*):<br />
	<input name="oldpassword" id="oldpassword" type="password" size="54" maxlength="20" tabindex="1" value="" class="formfield" />
    </label>
  </p>
  <p>
    <label for="newpassword">新密码(*):<br />
	<input name="newpassword" id="newpassword" type="password" size="54" maxlength="20" tabindex="2" value="" class="formfield" />
    </label>
  </p>
  <p>
    <label for="confirmpassword">确认密码(*):<br />
	<input name="confirmpassword" id="confirmpassword" type="password" size="54" maxlength="20" tabindex="3" value="" class="formfield" />
    </label>
  </p>
  <p>
    <label for="url">网址或电子邮件:<br />
	<input name="url" id="url" type="text" size="54" maxlength="100" tabindex="4" value="$user[url]" class="formfield" />
    </label>
  </p>
  <p>
    <button type="submit" class="formbutton">确定</button>
  </p>
</div>
</form>
<!--
EOT;
}
?>