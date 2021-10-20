<?php
// ========================== 文件说明 ==========================//
// 本文件说明：前台主程序
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

if (!$action) {
	$action = 'index';
}

require_once(SABLOG_ROOT.'include/visits.php');

//文章列表
if (in_array($action, array('index', 'finduser', 'search', 'tags'))) {
	session_start();
	// 检查浏览模式
	$nolist = FALSE; //该变量是检查调用搜索结果还是搜索表单
	if ($viewmode == 'normal') {
		$pagefile = 'normal';
		$pagenum = intval($options['normal_shownum']);
		$timeformat = $options['normaltime'];
		$query_sql = "SELECT a.articleid,a.cid,a.uid,a.stick,a.dateline,a.title,a.description,a.content,a.keywords,a.views,a.comments,a.attachments,a.trackbacks,a.readpassword,c.name as cname,u.username
			FROM {$db_prefix}articles a 
			LEFT JOIN {$db_prefix}categories c ON c.cid=a.cid
			LEFT JOIN {$db_prefix}users u ON a.uid=u.userid
			WHERE a.visible='1'";
	} else {
		$pagefile = 'list';
		$pagenum = intval($options['list_shownum']);
		$timeformat = $options['listtime'];
		$query_sql = "SELECT a.articleid,a.stick,a.dateline,a.title,a.views,a.comments FROM {$db_prefix}articles a WHERE a.visible='1'";
	}
	if($page) {
		$start_limit = ($page - 1) * $pagenum;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	// 查看用户发表的文章
	if ($action == 'finduser') {
		$userid = intval($_GET['userid']);
		$user  = $DB->fetch_one_array("SELECT username,articles FROM {$db_prefix}users WHERE userid='$userid'");
		$tatol = $user['articles'];
		$query_sql .= " AND a.uid='$userid' ORDER BY a.$article_order DESC LIMIT $start_limit, ".$pagenum;
		$pageurl = './?action=finduser&amp;userid='.$userid;
		$navtext = '查看'.$user['username'].'的文章';
	// 查看tags的相关文章
	} elseif ($action == 'tags') {
		$item = addslashes($_GET['item']);
		if ($item) {
			$tag = $DB->fetch_one_array("SELECT usenum,aids FROM {$db_prefix}tags WHERE tag='$item'");
			if (!$tag) {
				message('记录不存在.', './');
			}
			$tatol = $tag['usenum'];
			$query_sql .= " AND a.articleid IN (".$tag['aids'].") ORDER BY a.$article_order DESC LIMIT $start_limit, ".$pagenum;
			$pageurl = './?action=tags&amp;item='.urlencode($item);
			$navtext = 'Tag:'.htmlspecialchars($item);
		} else {
			message('缺少参数.', './');
		}
	// 查看搜索结果的文章
	} elseif ($action == 'search') {
		$searchid = intval($_GET['searchid']);
		if (!$searchid){
			$nolist = TRUE;
		} else {
			$search = $DB->fetch_one_array("SELECT * FROM {$db_prefix}searchindex WHERE searchid='".$searchid."'");
			if (!$search) {
				message('您指定的搜索不存在或已过期,请返回.', './');
			} elseif ($search['searchfrom'] != 'article') {
				message('您指定的搜索不存在或已过期,请返回.', './');				
			}
			$tatol = $search['tatols'];
			$query_sql .= " AND a.articleid IN (".$search['ids'].") ORDER BY a.".$search['sortby']." ".$search['orderby']." LIMIT $start_limit, ".$pagenum;
			$pageurl = './?action=search&amp;searchid='.$searchid;
			$navtext = '搜索:'.$search['keywords'];
		}
	// 查看首页文章
	} else {
		$pageurl = './?action=index';

		$navtext = '全部文章';
		$tatol = $stats['article_count'];
		// 检查是否设置$cid参数
		$cateadd = '';
		$cid = intval($_GET['cid']);
		if ($cid) {			
			$cateadd = " AND a.cid='$cid' ";
			$query_sql .= " AND a.cid='$cid' ";
			$r = $DB->fetch_one_array("SELECT name,articles FROM {$db_prefix}categories WHERE cid='$cid'");
			$navtext = '分类:'.$r['name'];
			$tatol = $r['articles'];
			$pageurl .= '&amp;cid='.$cid;
		}
		// 检查是否设置$setdate参数
		if ($setdate && strlen($setdate) == 6) {
			$navtext = $setyear.'年'.$setmonth.'月的文章';
			$pageurl .= '&amp;setdate='.$setdate;
			// 检查是否设置$setday参数
			$setday = intval($_GET['setday']);
			if ($setday && is_numeric($setday)) {
				if ($setday > 31 || $setday < 1) {
					$setday = sadate('d');
				}
				$navtext = $setyear.'年'.$setmonth.'月'.$setday.'日的文章';
				$start = strtotime($setyear.'-'.$setmonth.'-'.$setday);
				$end = $start + 86400;
				$pageurl .= '&amp;setday='.$setday;
			}
		}
		//*******************************//
		$startadd = $start ? " AND a.dateline >= '".correcttime($start)."' " : '';
		$endadd   = $end ? " AND a.dateline < '".correcttime($end)."' " : '';
		//*******************************//
		if($setdate || $setday) {
			$query = $DB->query("SELECT COUNT(*) FROM {$db_prefix}articles a WHERE a.visible='1' ".$cateadd.$startadd.$endadd);
			$tatol = $DB->result($query, 0);
		}
		//*******************************//
		$query_sql .= $startadd.$endadd." ORDER BY a.stick DESC, a.$article_order DESC LIMIT $start_limit, ".$pagenum;
	}
	// 执行查询

	if ($tatol) {
		require_once(SABLOG_ROOT.'include/func_attachment.php');
		$query = $DB->query($query_sql);
		$multipage = multi($tatol, $pagenum, $page, $pageurl);
		$articledb=array();
		while ($article = $DB->fetch_array($query)) {
			//隐藏变量,默认模板用不着,方便那些做模板可以单独显示月份和号数的的朋友.
			$article['month'] = sadate('M', $article['dateline']);
			$article['day'] = sadate('d', $article['dateline']);

			$article['dateline'] = sadate($timeformat, $article['dateline']);
			$article['title'] = trimmed_title($article['title'], $options['title_limit']);
			if ($viewmode == 'normal') {
				if ($article['readpassword'] && ($_SESSION['readpassword_'.$article['articleid']] != $article['readpassword']) && $sax_group != 1 && $sax_group != 2) {
					$article['allowread'] = false;
				} else {
					$article['allowread'] = true;
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
												));	$article['image'][$attach['attachmentid']]=array($attach['attachmentid'],$a_path,$a_size,$im['img_width'],$im['img_height'],$attach['downloads'],0);
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
						$article['content'] = $article['description'];
					}
					//处理PHP高亮
					// $article['content'] = preg_replace("/\s*\[php\](.+?)\[\/php\]\s*/ies", "phphighlite('\\1')", $article['content']);
                    $article['content'] = preg_replace_callback("/\s*\[php\](.+?)\[\/php\]\s*/is", function(&$matches){
             		    return phphighlite($matches[1]);
                     }, $article['content']);

					//TAGS
					if ($article['keywords']) {
						$articletags = $tmark = '';
						$tagdb = explode(',', $article['keywords']);
						$tagnum = count($tagdb);
						for($i=0; $i<$tagnum; $i++) {
							$tagdb[$i] = trim($tagdb[$i]);
							$articletags .= $tmark.'<a href="./?action=tags&amp;item='.urlencode($tagdb[$i]).'">'.htmlspecialchars($tagdb[$i]).'</a>';
							$tmark = ', ';
						}
						$article['alltags'] = $articletags;
					}
				}
			}
			$articledb[]=$article;
			unset($articletags);
		}
		unset($article);
		$DB->free_result($query);
	}
	if ($nolist) {
		$pagefile = 'search';
	}
}

// 显示文章
elseif ($_GET['action'] == 'show') {
	session_start();
	$articleid = intval($_GET['id']);
	// 获取文章信息	
	$article = $DB->fetch_one_array("SELECT a.*,c.name as cname,u.username
		FROM {$db_prefix}articles a
		LEFT JOIN {$db_prefix}categories c ON c.cid=a.cid
		LEFT JOIN {$db_prefix}users u ON a.uid=u.userid
		WHERE a.visible='1' AND articleid='$articleid'");
	if (!$article) {
		message('记录不存在', './');
	}
	if ($_POST['readpassword'] && ($article['readpassword'] == addslashes($_POST['readpassword']))) {
		$_SESSION['readpassword_'.$articleid] = addslashes($_POST['readpassword']);
	}

	//设置文章的分类名、作者、TAG、标题成为meta\title信息
	$options['meta_keywords'] = $article['cname'].','.$article['username'].','.($article['keywords'] ? $article['keywords'].',' : '').$article['title'].','.$options['meta_keywords'];
	$options['meta_description'] = $article['cname'].','.$article['username'].','.($article['keywords'] ? $article['keywords'].',' : '').$article['title'].','.$options['meta_description'];
	$options['title_keywords'] = ' - '.$article['cname'].','.($article['keywords'] ? $article['keywords'].',' : '').$article['username'].','.$options['title_keywords'];

	//隐藏变量,默认模板用不着,方便那些做模板可以单独显示月份和号数的的朋友.
	$article['month'] = sadate('M', $article['dateline']);
	$article['day'] = sadate('d', $article['dateline']);

	$article['dateline'] = sadate($options['normaltime'], $article['dateline']);

	if ($article['readpassword'] && ($_SESSION['readpassword_'.$articleid] != $article['readpassword']) && $sax_group != 1 && $sax_group != 2) {
		$article['allowread'] = false;
	} else {
		$article['allowread'] = true;
		$DB->unbuffered_query("UPDATE {$db_prefix}articles SET views=views+1 WHERE articleid='$articleid'");

		// 跳转
		$goto = $_GET['goto'];
		$article_comment_num = intval($options['article_comment_num']);
		if ($goto == 'newcm') {
			//跳转到评论
			$cmid = intval($_GET['cmid']);
			if ($options['comment_order']) {
				$cmnum = '#cm'.$cmid;
				if ($article_comment_num) {
					$cpost = $DB->fetch_one_array("SELECT COUNT(*) as comments FROM {$db_prefix}comments WHERE articleid='$articleid' AND visible='1' AND commentid<='$cmid'");
					if (($cpost['comments'] / $article_comment_num) <= 1 ) {
						$page = 1;
					} else {
						$page = @ceil(($cpost['comments']) / $article_comment_num);
					}
				} else {
					$page = 1;
				}
				if ($options['showmsg']) {
					message('正在读取.请稍侯.', getarticleurl($articleid, $page).$cmnum);
				} else {
					@header('Location: '.getarticleurl($articleid, $page).$cmnum);
				}
			} else {
				if ($options['showmsg']) {
					message('正在读取.请稍侯.', getarticleurl($articleid).'#comment');
				} else {
					@header('Location: '.getarticleurl($articleid).'#comment');
				}
			}
		} elseif ($goto == 'next') {
			//跳转到下一篇文章
			$query    = $DB->query("SELECT dateline FROM {$db_prefix}articles WHERE articleid='$articleid'");
			$lastpost = $DB->result($query, 0);
			$row      = $DB->fetch_one_array("SELECT articleid FROM {$db_prefix}articles WHERE dateline > '$lastpost' AND visible='1' ORDER BY dateline ASC LIMIT 1");
			if($row) {
				if ($options['showmsg']) {
					message('正在读取.请稍侯.', getarticleurl($row['articleid']));
				} else {
					@header('Location: '.getarticleurl($row['articleid']));
				}
			} else {
				message('没有比当前更新的文章', getarticleurl($articleid));
			}
		} elseif ($goto == 'previous') {
			//跳转到上一篇文章
			$query    = $DB->query("SELECT dateline FROM {$db_prefix}articles WHERE articleid='$articleid'");
			$lastpost = $DB->result($query, 0);
			$row      = $DB->fetch_one_array("SELECT articleid FROM {$db_prefix}articles WHERE dateline < '$lastpost' AND visible='1' ORDER BY dateline DESC LIMIT 1");
			if($row) {
				if ($options['showmsg']) {
					message('正在读取.请稍侯.', getarticleurl($row['articleid']));
				} else {
					@header('Location: '.getarticleurl($row['articleid']));
				}
			} else {
				message('没有比当前更早的文章', getarticleurl($articleid));
			}
		}
		//附件
		if ($article['attachments']) {
			require_once(SABLOG_ROOT.'include/func_attachment.php');
			$attachs= unserialize(stripslashes_array($article['attachments']));
			if (is_array($attachs)) {
				foreach ($attachs AS $attach) {
					$a_path = $options['attachments_dir'].$attach['filepath'];
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
									'max_width'  => $size[0],
									'max_height' => $size[1],
									'cur_width'  => $imagesize[0],
									'cur_height' => $imagesize[1]
								));	
								$article['image'][$attach['attachmentid']]=array($attach['attachmentid'],$a_path,$a_size,$im['img_width'],$im['img_height'],$attach['downloads'],0);
							}
						} else {
							// 如果非图片文件
							$a_size = sizecount($attach['filesize']);	
							$article['file'][$attach['attachmentid']]=array($attach['attachmentid'],$attach['filename'],$a_size,$attach['downloads']);
						}
					}
				}
				//如果空,释放掉变量
				$attachmentids=array();

				// $article['content'] = preg_replace("/\[attach=(\d+)\]/ie", "upload('\\1')", $article['content']);
				$article['content'] = preg_replace_callback("/\[attach=(\d+)\]/is", function(&$matches){
				    return upload($matches[1]);
                }, $article['content']);

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
		// 获取附件结束

		//处理PHP高亮
		// $article['content'] = preg_replace("/\s*\[php\](.+?)\[\/php\]\s*/ies", "phphighlite('\\1')", $article['content']);
		$article['content'] = preg_replace_callback("/\s*\[php\](.+?)\[\/php\]\s*/is", function(&$matches){
		    return phphighlite($matches[1]);
        }, $article['content']);

		//TAGS
		if ($article['keywords']) {
			$tagdb = explode(',', $article['keywords']);
			$articletags = $tmark = '';
			for($i=0; $i<count($tagdb); $i++) {
				$tagdb[$i] = trim($tagdb[$i]);
				$articletags .= $tmark.'<a href="./?action=tags&amp;item='.urlencode($tagdb[$i]).'">'.htmlspecialchars($tagdb[$i]).'</a>';
				$tmark = ', ';
			}
			$article['tags'] = $articletags;
			// 如果显示相关文章
			if ($options['related_shownum']) {
				$tags = $comma = '';
				for($i=0; $i<count($tagdb); $i++) {
					$tags .= $comma."'".addslashes($tagdb[$i])."'";
					$comma = ',';
				}
				$query = $DB->query("SELECT aids FROM {$db_prefix}tags WHERE tag IN ($tags)");
				$relaids = 0;
				while ($tag = $DB->fetch_array($query)) {
					$relaids .= ','.$tag['aids'];
				}
				$relids = explode(',', $relaids);
				// 清除重复值的单元并删除当前ID
				$relids = array_unique($relids);
				$relids = array_flip($relids);
				unset($relids[$articleid]);
				$relids = array_flip($relids);
				////////
				$related_tatol = count($relids);
				$relids = implode(',',$relids);
				if ($related_tatol > 1 && $relids != $articleid) {
					$order = in_array($options['related_order'], array('dateline', 'views', 'comments')) ? $options['related_order'] : 'dateline';
					$query = $DB->query("SELECT articleid,title,views,comments FROM {$db_prefix}articles WHERE visible='1' AND articleid IN ($relids) ORDER BY ".$order." DESC LIMIT ".intval($options['related_shownum']));
					$titledb=array();
					while ($title = $DB->fetch_array($query)) {
						$title['title'] = trimmed_title($title['title'], $options['related_title_limit']);
						$titledb[] = $title;
					}
					unset($title);
					$DB->free_result($query);
				}
			}
			unset($articletags);
		}

		// Trackback
		if ($options['enable_trackback'] && $article['trackbacks']) {
			$tborderid = 0;
			$tborder = $options['trackback_order'] ? 'ASC' : 'DESC';
			$query = $DB->query("SELECT title,dateline,excerpt,url,blog_name FROM {$db_prefix}trackbacks WHERE visible='1' AND articleid='$articleid' ORDER BY trackbackid ".$tborder);
			if ($options['trackback_excerpt_limit'] > 255) {
				$options['trackback_excerpt_limit'] = 60;
			}
			$trackbackdb=array();
			while ($trackback = $DB->fetch_array($query)) {
				$tborderid++;
				$trackback['tborderid'] = $tborderid;
				$trackback['url'] = stripslashes_array($trackback['url']);
				$trackback['title'] = stripslashes_array($trackback['title']);
				$trackback['blog_name'] = stripslashes_array($trackback['blog_name']);
				$trackback['excerpt'] = trimmed_title(stripslashes_array($trackback['excerpt']), $options['trackback_excerpt_limit']);
				$trackback['dateline'] = sadate($options['trackback_timeformat'], $trackback['dateline']);
				$trackbackdb[] = $trackback;
			}
			unset($trackback);
			$DB->free_result($query);
		}
			
		// 评论	
		if ($article['comments']) {
			$commentsql = '';
			if($article_comment_num) {
				if($page) {
					$cmtorderid = ($page - 1) * $article_comment_num;
					$start_limit = ($page - 1) * $article_comment_num;
				} else {
					$cmtorderid = 0;
					$start_limit = 0;
					$page = 1;
				}
				$multipage = multi($article['comments'], $article_comment_num, $page, "./?action=show&amp;id=$articleid");
				$commentsql = " LIMIT $start_limit, $article_comment_num";
			}
			$cmtorder = $options['comment_order'] ? 'ASC' : 'DESC';
			$query = $DB->query("SELECT commentid,author,url,dateline,content FROM {$db_prefix}comments WHERE articleid='$articleid' AND visible='1' ORDER BY commentid $cmtorder $commentsql");
			$commentdb=array();
			while ($comment=$DB->fetch_array($query)) {
				$cmtorderid++;
				$comment['cmtorderid'] = $cmtorderid;
				$comment['quoteuser'] = $comment['author'];
				if ($comment['url']) {
					if (isemail($comment['url'])) {
						//分解邮件地址并采用javascript输出
						$frontlen = strrpos($comment['url'], '@');
						$front    = substr($comment['url'], 0, $frontlen);
						$emaillen = strlen($comment['url']);
						$back     = substr($comment['url'], $frontlen+1, $emaillen);
						$comment['author'] = "<a href=\"javascript:navigate('mai' + 'lto:' + '".$front."' + '@' + '".$back."')\" target=\"_blank\">".$comment['author']."</a>";
					} else {
						$comment['author'] = '<a href="'.$comment['url'].'" target="_blank">'.$comment['author'].'</a>';
					}
				}
				$comment['content'] = html_clean($comment['content']);
				$comment['dateline'] = sadate($options['comment_timeformat'], $comment['dateline']);
				$commentdb[]=$comment;
			}
			unset($comment);
			$DB->free_result($query);
		}
	}
	$options['title'] = $article['title'];
	$pagefile = 'show';
}

// 标签 (Tags)
elseif ($_GET['action'] == 'tagslist') {
	if ($stats['tag_count']) {
		$pagenum = intval($options['tags_shownum']);
		if($page) {
			$start_limit = ($page - 1) * $pagenum;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$multipage = multi($stats['tag_count'], $pagenum, $page, './?action=tagslist');
		$tagdb=array();
		$query = $DB->query("SELECT * FROM {$db_prefix}tags ORDER BY tagid DESC LIMIT $start_limit, ".$pagenum);
		while ($tag = $DB->fetch_array($query)) {
			$tag['fontsize'] = 12 + $tag['usenum'] / 2;
			$tag['url'] = urlencode($tag['tag']);
			$tag['usenum'] = intval($tag['usenum']);
			$tag['item'] = htmlspecialchars($tag['tag']);
			$tagdb[]=$tag;
		}
		unset($tag);
		$DB->free_result($query);
	}
	$pagefile = 'tag';
}
// 注册
elseif ($_GET['action'] == 'reg') {
	$pagefile = 'reg';
}
// 登陆
elseif ($_GET['action'] == 'login') {
	$pagefile = 'login';
}
// 修改资料
elseif ($_GET['action'] == 'profile') {
	$user = $DB->fetch_one_array("SELECT userid,username,logincount,groupid,password,url FROM {$db_prefix}users WHERE userid='$sax_uid'");
	if (!$user['userid'] || !($user['password'] == $sax_pw) || !($user['logincount'] == $logincount) && !($user['groupid'] == $sax_group)) {
		message('请登陆后再进行此操作.','./');
	}
	$pagefile = 'profile';
}

// 所有评论
elseif ($_GET['action'] == 'comments') {
	$searchid = intval($_GET['searchid']);
	$query_sql = "SELECT c.articleid,c.author,c.url,c.dateline,c.content,a.title FROM {$db_prefix}comments c LEFT JOIN {$db_prefix}articles a ON (a.articleid=c.articleid) WHERE a.visible='1' AND c.visible='1'";
	if ($searchid) {
		$search = $DB->fetch_one_array("SELECT * FROM {$db_prefix}searchindex WHERE searchid='$searchid'");
		if (empty($search)) {
			message('您指定的搜索不存在或已过期,请返回.', './');
		} elseif ($search['searchfrom'] != "comment") {
			message('您指定的搜索不存在或已过期,请返回.', './');	
		}
		$tatol = $search['tatols'];
		$query_sql .= " AND c.commentid IN (".$search['ids'].")";
		$pageurl = './?action=comments&amp;searchid='.$searchid;
	} else {
		$tatol = $stats['comment_count'];
		$pageurl = './?action=comments';
	}
	if ($tatol) {
		$commentlist_num = intval($options['commentlist_num']);
		if($page) {
			$start_limit = ($page - 1) * $commentlist_num;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$query_sql .= " ORDER BY commentid DESC LIMIT $start_limit, $commentlist_num";
		$multipage = multi($tatol, $commentlist_num, $page, $pageurl);
		$query = $DB->query($query_sql);
		$commentdb=array();
		while ($comment=$DB->fetch_array($query)) {
			//分解邮件地址并采用javascript输出
			if ($comment['url']) {
				if (isemail($comment['url'])) {
					//分解邮件地址并采用javascript输出
					$frontlen = strrpos($comment['url'], '@');
					$front    = substr($comment['url'], 0, $frontlen);
					$emaillen = strlen($comment['url']);
					$back     = substr($comment['url'], $frontlen+1, $emaillen);
					$comment['author'] = "<a href=\"javascript:navigate('mai' + 'lto:' + '".$front."' + '@' + '".$back."')\" target=\"_blank\">".$comment['author']."</a>";
				} else {
					$comment['author'] = '<a href="'.$comment['url'].'" target="_blank">'.$comment['author'].'</a>';
				}
			}
			$comment['content'] = html_clean($comment['content']);
			$comment['dateline'] = sadate($options['comment_timeformat'], $comment['dateline']);
			$commentdb[]=$comment;
		}
		unset($comment);
		$DB->free_result($query);
	}
	$pagefile = 'comments';
}

// 所有Trackback
elseif ($_GET['action'] == 'trackbacks') {
	if ($options['enable_trackback'] && $stats['trackback_count']) {
		$trackback_num = intval($options['trackback_num']);
		if($page) {
			$start_limit = ($page - 1) * $trackback_num;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$multipage = multi($stats['trackback_count'], $trackback_num, $page, './?action=trackbacks');
		$query = $DB->query("SELECT t.articleid,t.title,t.dateline,t.excerpt,t.url,t.blog_name, a.title as article FROM {$db_prefix}trackbacks t LEFT JOIN {$db_prefix}articles a ON (a.articleid=t.articleid) WHERE a.visible='1' AND a.readpassword='' AND t.visible='1' ORDER BY trackbackid DESC LIMIT $start_limit, $trackback_num");
		if ($options['trackback_excerpt_limit'] > 255) {
			$options['trackback_excerpt_limit'] = 60;
		}
		$trackbackdb=array();
		while ($trackback = $DB->fetch_array($query)) {
			$trackback['url'] = stripslashes_array($trackback['url']);
			$trackback['title'] = stripslashes_array($trackback['title']);
			$trackback['blog_name'] = stripslashes_array($trackback['blog_name']);
			$trackback['excerpt'] = trimmed_title(stripslashes_array($trackback['excerpt']), $options['trackback_list_excerpt_limit']);
			$trackback['dateline'] = sadate($options['trackback_timeformat'], $trackback['dateline']);
			$trackbackdb[] = $trackback;
		}
		unset($trackback);
		$DB->free_result($query);
	}
	$pagefile = 'trackbacks';
}

// 归档
elseif ($_GET['action'] == 'archives') {
	$query = $DB->query("SELECT dateline FROM {$db_prefix}articles WHERE visible = '1' ORDER BY dateline DESC");
	$articledb = $arr = $archivedb = array();
	while ($article = $DB->fetch_array($query)) {
		$articledb[] = sadate('Y-m',$article['dateline']);
	}
	unset($article);
	$DB->free_result($query);
	$arr = array_count_values($articledb);
	unset($articledb);
	foreach($arr as $key => $val){
		list($y, $m) = explode('-', $key);
		$archivedb[$y][$m] = $val;
	}
	$pagefile = 'archives';
}

// 友情链接
elseif ($_GET['action'] == 'links') {
	if ($link_count) {
		$query = $DB->query("SELECT displayorder,name,url,note FROM {$db_prefix}links WHERE visible = '1' ORDER BY displayorder ASC, name ASC");
		$linkdb = array();
		while ($link = $DB->fetch_array($query)) {
			$link['note'] = $link['note'] ? $link['note'] : $link['url'];
			$linkdb[] = $link;
		}
		unset($link);
		$DB->free_result($query);
	}
	$pagefile = 'links';
}

require_once PrintEot('index');
footer();
?>
