<?php
// ========================== 文件说明 ==========================//
// 本文件说明：图片函数
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

if(!defined('SABLOG_ROOT')) {
	exit('Access Denied');
}

// 创建水印
function create_watermark($uploadfile) {
	global $options;

	$waterimg = '../templates/'.$options['templatename'].'/img/watermark.png';
    if (file_exists($waterimg)) {
		$upload_info = @getimagesize($uploadfile);
		if (!$upload_info[0] || !$upload_info[1]) return;
		switch ($upload_info['mime']) {
			case 'image/jpeg':
				$tmp = @imagecreatefromjpeg($uploadfile);
				break;
			case 'image/gif':
				$tmp = @imagecreatefromgif($uploadfile);
				break;
			case 'image/png':
				$tmp = @imagecreatefrompng($uploadfile);
				break;
			default :
				return;
		}
		$marksize = @getimagesize($waterimg);
		$width    = $marksize[0];
		$height   = $marksize[1];
		unset($marksize);
		$pos_padding = ($options['pos_padding'] && $options['pos_padding'] > 0) ? $options['pos_padding'] : 5; //水印边距
		switch ($options['waterpos']) {
			// 左上
			case '1':
				$pos_x = $pos_padding;
				$pos_y = $pos_padding;
				break;
			// 左下
			case '2':
				$pos_x = $pos_padding;
				$pos_y = $upload_info[1] - $height - $pos_padding;
				break;
			// 右上
			case '3':
				$pos_x = $upload_info[0] - $width - $pos_padding;
				$pos_y = $pos_padding;
				break;
			// 右下
			case '4':
				$pos_x = $upload_info[0] - $width - $pos_padding;
				$pos_y = $upload_info[1] - $height - $pos_padding;
				break;
			// 中间
			case '5':
				$pos_x = ($upload_info[0] - $width) / 2;
				$pos_y = ($upload_info[1] - $height) / 2;
				break;
			// 随机
			default:
				$pos_x = rand(0,($upload_info[0] - $width));
				$pos_y = rand(0,($upload_info[1] - $height));
				break;
		}
		if($imgmark = @imagecreatefrompng($waterimg)) {
			if ($upload_info[0] < ($width * 2) || $upload_info[1] < ($height * 2)) {
				return;
				//如果水印占了原图一半就不搞水印了.影响浏览.抵制影响正常浏览的广告.
			}
			if ($options['watermarktrans']) {
				@imagecopymerge($tmp, $imgmark, $pos_x, $pos_y, 0, 0, $width, $height, $options['watermarktrans']);
			} else {
				@imagecopy($tmp, $imgmark, $pos_x, $pos_y, 0, 0, $width, $height);
			}
		}
		switch ($upload_info['mime']) {
			case 'image/jpeg':
				@imagejpeg($tmp,$uploadfile,100);
				@imagedestroy($tmp);
				break;
			case 'image/gif':
				@imagegif($tmp,$uploadfile);
				@imagedestroy($tmp);
				break;
			case 'image/png':
				@imagepng($tmp,$uploadfile);
				@imagedestroy($tmp);
				break;
			default :
				return;
		}
    }
}

?>