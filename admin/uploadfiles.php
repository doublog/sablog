<?php
// ========================== 文件说明 ==========================//
// 本文件说明：附件上传
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

if(!defined('SABLOG_ROOT') || !isset($php_self) || !preg_match("/[\/\\\\]admincp\.php$/", $php_self)) {
	exit('Access Denied');
}

$attachments = $attachs = $attach_data = array();

if(isset($_FILES['attach']) && is_array($_FILES['attach'])) {
	foreach($_FILES['attach'] as $key => $var) {
		foreach($var as $id => $val) {
			$attachments[$id][$key] = $val;
		}
	}
}

if($attachments) {
	$gd_version = gd_version();
	foreach($attachments as $key => $attach) {
		if(!disuploadedfile($attach['tmp_name']) || !($attach['tmp_name'] != 'none' && $attach['tmp_name'] && $attach['name'])) {
			continue;
		}

		$attach['name'] = strtolower($attach['name']);
		$attach['ext']  = getextension($attach['name']);

		$fnamehash = md5(uniqid(microtime()));
		// 判断上传目录的方式
		switch($options['attachments_save_dir']) {
			case 0: $attachsubdir = '/'; break; //全部放一起
			case 1: $attachsubdir = '/cate_'.$cid.'/'; break; //按分类放
			case 2: $attachsubdir = '/date_'.sadate('Ym').'/'; break; //按月放
			case 3: $attachsubdir = '/ext_'.$attach['ext'].'/'; break; //按文件类型
		}
		// 取得附件目录的绝对路径
		$attach_dir = SABLOG_ROOT.$options['attachments_dir'].$attachsubdir;
		if(!is_dir($attach_dir)) {
			mkdir($attach_dir, 0777);
			fclose(fopen($attach_dir.'/index.htm', 'w'));
			@chmod($attach_dir, 0777);
		}
		// 判断上传的类型
		// path变量为管理目录相对路径,后台操作用
		// filepath变量为跟目录相对路径,前台读取用
		// fnamehash变量为当前时间的MD5散列,重命名附件名
		if (!in_array($attach['ext'], array('gif', 'jpg', 'jpeg', 'png'))) {
			$path     = $attach_dir.$fnamehash.'.file';
			$filepath = $attachsubdir.$fnamehash.'.file';
		} else {
			$path     = $attach_dir.$fnamehash.'.'.$attach['ext'];
			$filepath = $attachsubdir.$fnamehash.'.'.$attach['ext'];
		}
		$attachment = upfile($attach['tmp_name'], $path);
		// 如果一种函数上传失败，还可以用其他函数上传
		if (!$attachment) {
			redirect('上传附件发生意外错误!');
		}

		$tmp_filesize = @filesize($attachment);
		if ($tmp_filesize != $attach['size']) {
			@unlink($attachment);
			redirect('上传附件发生意外错误!');
		}
		// 判断是否为图片格式
		if (in_array($attach['ext'], array('gif', 'jpg', 'jpeg', 'png'))) {
			if ($imginfo=@getimagesize($attachment)) {
				if (!$imginfo[2] || !$imginfo['bits']) {
					@unlink($attachment);
					redirect('上传的文件不是一个有效的GIF或者JPG文件!');
				} else {
					$isimage = '1';
				}
			}
			// 判断是否使用缩略图
			if ($options['attachments_thumbs'] && $gd_version) {
				$size = explode('x', strtolower($options['attachments_thumbs_size']));
				if (($imginfo[0] > $size[0] || $imginfo[1] > $size[1]) && $attach['size'] < 2048000) {
					$attach_thumb = array(
						'filepath'     => $attachment,
						'filename'     => $fnamehash,
						'extension'    => $attach['ext'],
						'attachsubdir' => $attachsubdir,
						'thumbswidth'  => $size[0],
						'thumbsheight' => $size[1],
					);
					$thumb_data = generate_thumbnail($attach_thumb);
					$attach_data['thumbwidth']    = $thumb_data['thumbwidth'];
					$attach_data['thumbheight']   = $thumb_data['thumbheight'];
					$attach_data['thumbfilepath'] = $attachsubdir.$thumb_data['thumbfilepath'];
				}
			}
			//水印
			$watermark_size = explode('x', strtolower($options['watermark_size']));
			if($isimage && $options['watermark'] && $imginfo[0] > $watermark_size[0] && $imginfo[1] > $watermark_size[1] && $attach['size'] < 2048000) {
				require_once(SABLOG_ROOT.'include/func_image.php');
				create_watermark($path);
				$attach['size'] = filesize($path);
			}
		}
		// 把文件信息插入数据库
		$DB->query("INSERT INTO {$db_prefix}attachments (filename,filesize,filetype,filepath,dateline,downloads,isimage,thumb_filepath,thumb_width,thumb_height) VALUES ('".addslashes($attach['name'])."', '".$attach['size']."', '".addslashes($attach['type'])."', '".addslashes($filepath)."', '$timestamp', '0', '$isimage', '".$attach_data['thumbfilepath']."', '".$attach_data['thumbwidth']."','".$attach_data['thumbheight']."')");
		$aidtmp = $DB->insert_id();
		$attachs[$aidtmp] = array(
			'attachmentid' => $aidtmp,
			'dateline' => $timestamp,
			'filename' => addslashes($attach['name']),
			'filetype' => addslashes($attach['type']),
			'filepath' => addslashes($filepath),
			'filesize' => addslashes($attach['size']),
			'downloads' => 0,
			'thumb_filepath' => $attach_data['thumbfilepath'],
			'thumb_width' => $attach_data['thumbwidth'],
			'thumb_height' => $attach_data['thumbheight'],
			'isimage' => $isimage
		);
		unset($isimage);
		unset($attach_data);

		$searcharray[] = '[localfile='.$key.']';
		$replacearray[] = '[attach='.$aidtmp.']';
	}
}

$attachment_count = count($attachs);
$attachmentids = 0;
foreach($attachs as $key => $value){
	$attachmentids .= ','.$key;
}
$attachs = $attachs ? addslashes(serialize($attachs)) : '';
?>