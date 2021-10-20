<?php
// ========================== 文件说明 ==========================//
// 本文件说明：RSS输出
// --------------------------------------------------------------//
// 本程序作者：angel
// --------------------------------------------------------------//
// 本程序版本：SaBlog-X Ver 1.6
// --------------------------------------------------------------//
// 本程序主页：http://www.sablog.net
// ========================== 开发环境 ==========================//
// register_globals = Off
// --------------------------------------------------------------//
// magic_quotes_gpc = On
// --------------------------------------------------------------//
// safe_mode = On
// --------------------------------------------------------------//
// Windows server 2003 & Linux & FreeBSD
// --------------------------------------------------------------//
// Apache/1.3.33 & PHP/4.3.2 & MySQL/4.0.17
// --------------------------------------------------------------//
// Apache/1.3.34 & PHP/4.4.1 & MySQL/5.0.16
// --------------------------------------------------------------//
// Apache/2.0.55 & PHP/5.1.1 & MySQL/5.0.15
// --------------------------------------------------------------//
// Copyright (C) Security Angel Team All Rights Reserved.
// ==============================================================//

require_once('global.php');

if (!$options['rss_enable']) {
	exit('RSS Disabled');
}

$query_add = '';
$fileext = '_all';
$cid = intval($_GET['cid']);
if ($cid) {
	$query_add = "AND a.cid='$cid'";
	$fileext = '_'.$cid;
}

//读取缓存文件
$cachefile = SABLOG_ROOT.'cache/cache_rss'.$fileext.'.php';

//如果读取失败或缓存过期则从新读取数据库
if((@!include($cachefile)) || $expiration < $timestamp || !$option['rss_ttl']) {
	require_once(SABLOG_ROOT.'include/func_attachment.php');
	$rssdb = array();
	$query = $DB->query("SELECT a.articleid,a.cid,a.uid,a.dateline,a.title,a.description,a.content,a.readpassword,a.attachments,c.name as cname,u.username FROM {$db_prefix}articles a LEFT JOIN {$db_prefix}categories c ON c.cid=a.cid LEFT JOIN {$db_prefix}users u ON a.uid=u.userid WHERE a.visible='1' $query_add ORDER BY a.$article_order DESC LIMIT ".($options['rss_num'] ? intval($options['rss_num']) : 20));
	while ($article = $DB->fetch_array($query)) {
		if (empty($article['description'])) {
			//附件
			if ($article['attachments']) {
				$attachs= unserialize(stripslashes_array($article['attachments']));
				if (is_array($attachs)) {
					foreach ($attachs AS $attach) {
						$a_path = $options['attachments_dir'].'/'.$attach['filepath'];
						if (file_exists($a_path)) {
							$a_ext = strtolower(getextension($attach['filename']));
							if ($a_ext == 'gif' || $a_ext == 'jpg' || $a_ext == 'jpeg' || $a_ext == 'png') {
								$imagesize = @getimagesize($a_path);
								$a_size = sizecount($attach['filesize']);
								$a_thumb_path = $options['attachments_dir'].$attach['thumb_filepath'];
								if ($attach['thumb_filepath'] && $options['attachments_thumbs'] && file_exists($a_thumb_path)) {
									$article['image'][$attach['attachmentid']]=array($attach['attachmentid'],$a_thumb_path,$a_size,$attach['thumb_width'],$attach['thumb_height'],$attach['downloads'],1);
								} else {
									// 如果缩略图不存在
									$size = explode('x', strtolower($options['attachments_thumbs_size']));
									$im = scale_image( array(
										"max_width"  => $size[0],
										"max_height" => $size[1],
										"cur_width"  => $imagesize[0],
										"cur_height" => $imagesize[1]
									));
									$article['image'][$attach['attachmentid']]=array($attach['attachmentid'],$a_path,$a_size,$im['img_width'],$im['img_height'],$attach['downloads'],0);
								}
							} else {
								// 如果非图片文件
								$a_size = sizecount($attach['filesize']);	$article['file'][$attach['attachmentid']]=array($attach['attachmentid'],$attach['filename'],$a_size,$attach['downloads']);
							}
						}
					}
					//如果空,释放掉变量
					$attachmentids=array();
					
					$article['content'] = preg_replace("/\[attach=(\d+)\]/ie", "upload('\\1')", $article['content']);

					foreach($attachmentids as $key => $value){
						if($article['image'][$value]){
							unset($article['image'][$value]);
						}
						if($article['file'][$value]){
							unset($article['file'][$value]);
						}
					}
				}
			}
		} else {
			if ($options['rewrite_enable']) {
				$articleurl = $options['url'].'show-'.$article['articleid'].'-1.'.$options['rewrite_ext'];
			} else {
				$articleurl = $options['url'].'?action=show&amp;id='.$article['articleid'];
			}
			$article['content'] = $article['description'].'<br /><br /><a href="'.$articleurl.'" target="_blank">阅读全文</a><br /><br />';
		}

		//处理PHP高亮
		$article['content'] = preg_replace("/\s*\[php\](.+?)\[\/php\]\s*/ies", "phphighlite('\\1')", $article['content']);

		$extracontent = '';
		if ($article['image']) {
			foreach ($article['image'] as $image) {
				if($image[6]){
					$extracontent .= "<br /><br /><b>图片附件(缩略图):</b><br /><a href=\"".$options['url']."attachment.php?id=$image[0]\" target=\"_blank\"><img src=\"".$options['url']."$image[1]\" border=\"0\" alt=\"大小: $image[2]&#13;尺寸: $image[3] x $image[4]&#13;浏览: $image[5] 次&#13;点击打开新窗口浏览全图\" width=\"$image[3]\" height=\"$image[4]\" /></a>";
				} else {
					$extracontent .= "<br /><br /><b>图片附件:</b><br /><a href=\"".$options['url']."attachment.php?id=$image[0]\" target=\"_blank\"><img src=\"".$options['url']."$image[1]\" border=\"0\" alt=\"大小: $image[2]&#13;尺寸: $image[3] x $image[4]&#13;浏览: $image[5] 次&#13;点击打开新窗口浏览全图\" width=\"$image[3]\" height=\"$image[4]\" /></a>";
				}
			}
		}
		if($article['file']){
			foreach($article['file'] as $file){
				if($file){
					$extracontent .= "<br /><br /><b>附件: </b><a href=\"".$options['url']."attachment.php?id=$file[0]\" target=\"_blank\">$file[1]</a> ($file[2], 下载次数:$file[3])";
				}
			}
		}

		if ($extracontent) {
			$article['content'] = $article['content'].$extracontent;
		}

		$article['dateline'] = sadate('Y-m-d H:i',$article['dateline']);
		$rssdb[] = $article;
	}//end while

	unset($article);
	$DB->free_result($query);
	
	if($fp = @fopen($cachefile, 'wb')) {
	$cachedata = "\$rssdb = unserialize('".addcslashes(serialize($rssdb), '\\\'')."');";
		@fwrite($fp, "<?php\r\nif(!defined('SABLOG_ROOT')) exit('Access Denied');\r\n\$expiration='".($timestamp + $options['rss_ttl'] * 60)."';\r\n".$cachedata."\r\n?>");
		@fclose($fp);
		@chmod($cachefile, 0777);
	} else {
		exit('Can not write to cache files, please check directory ./cache/ .');
	}
}


header("Content-Type: application/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<rss version=\"2.0\">\n";
echo "\t<channel>\n";
echo "\t\t<title>".htmlspecialchars($options['name'])."</title>\n";
echo "\t\t<link>".$options['url']."</link>\n";
echo "\t\t<description>".htmlspecialchars($options['description'])."</description>\n";
echo "\t\t<copyright>Copyright (C) 2004 Security Angel Team [S4T] All Rights Reserved.</copyright>\n";
echo "\t\t<generator>SaBlog-X Version $SABLOG_VERSION Build $SABLOG_RELEASE</generator>\n";
echo "\t\t<lastBuildDate>".sadate('r', $timestamp)."</lastBuildDate>\n";
echo "\t\t<ttl>".$options['rss_ttl']."</ttl>\n";

if (is_array($rssdb)) {
	foreach ($rssdb AS $article) {
		if ($options['rewrite_enable']) {
			$articleurl = $options['url'].'show-'.$article['articleid'].'-1.'.$options['rewrite_ext'];
			$categoryurl = $options['url'].'category-'.$article['cid'].'-1.'.$options['rewrite_ext'];
		} else {
			$articleurl = $options['url'].'?action=show&amp;id='.$article['articleid'];
			$categoryurl = $options['url'].'?cid='.$article['cid'];
		}
		echo "\t\t<item>\n";
		echo "\t\t\t<guid>".$articleurl."</guid>\n";
		echo "\t\t\t<title>".$article['title']."</title>\n";
		echo "\t\t\t<author>".$article['username']."</author>\n";
		if ($article['readpassword']) {
			echo "\t\t\t<description>文章需要输入密码才能浏览.</description>\n";
		} else {
			echo "\t\t\t<description><![CDATA[".$article['content']."]]></description>\n";
		}
		echo "\t\t\t<link>".$articleurl."</link>\n";
		echo "\t\t\t<category domain=\"".$categoryurl."\">".$article['cname']."</category>\n";
		echo "\t\t\t<pubDate>".$article['dateline']."</pubDate>\n";
		echo "\t\t</item>\n";
	}
}

echo "\t</channel>\n";
echo "</rss>\n";
exit;

?>