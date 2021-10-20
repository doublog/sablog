### 感谢您选择由 [http://www.4ngel.net](http://www.4ngel.net)安全天使网络安全小组 开发的SaBlog-X 博客程序!
#### 当前版本为 SaBlog-X v 1.6 for php 7
#### 前言
  Sablog-X是一个采用PHP和MySQL构建的博客系统.作为Sablog的后继产品,Sablog-X在代码质量,运行效率,负载能力,安全等级,功能可操控性和权限严密性等方面都在原有的基础上,更上一层楼.凭借Sablog-X作者7年多的安全技术经验,4年的PHP开发经验,强于创新,追求完美的设计理念,使得Sablog-X已获得业内越来越多专家和用户的认可. 
### 全新安装
    上传所有文件到空间，设置以下目录和文件的权限：
- ./admin/backupdata 目录权限为 0777 
- ./attachments 目录权限为 0777 
- ./config.php 文件权限为 0777 
- ./cache 目录权限为 0777
- ./cache/log 目录权限为 0777
	      
访问/install目录。程序会自动引导并提示安装过程。 

 ### 验证码不显示已修改
 因为php5.4以后，php中session_register已禁用，所以需要在seccode.php中禁用掉这行代码。仓库内代码已修订。

 ### 特别说明
    本程序为安全天使的 angel   独立开发，请尊重作者的劳动成果。
    本程序为免费程序，任何人可以任意修改或者二次开发，但请说明修改自本程序。
    商业用途需要获得我们的授权，对于未经获权的企业使用，我们将保留任何法律追究的权利。
    本程序为膘叔升级PHP7版。
    
### Thanks

- [http://www.4ngel.net](http://www.4ngel.net)
- [https://www.neatstudio.com/show-2719-1.shtml](https://www.neatstudio.com/show-2719-1.shtml)
