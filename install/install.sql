# 安装用

DROP TABLE IF EXISTS sablog_articles;
CREATE TABLE sablog_articles (
  articleid mediumint(8) unsigned NOT NULL auto_increment,
  cid smallint(6) unsigned NOT NULL default '0',
  uid mediumint(8) unsigned NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  description text NOT NULL,
  content mediumtext NOT NULL,
  keywords varchar(120) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  views int(10) unsigned NOT NULL default '0',
  comments mediumint(8) unsigned NOT NULL default '0',
  attachments text NOT NULL,
  trackbacks mediumint(8) NOT NULL default '0',
  closecomment tinyint(1) NOT NULL default '0',
  closetrackback tinyint(1) NOT NULL default '0',
  visible tinyint(1) NOT NULL default '1',
  stick tinyint(1) unsigned NOT NULL default '0',
  readpassword varchar(20) NOT NULL default '',
  PRIMARY KEY  (articleid),
  KEY cid (cid),
  KEY uid (uid),
  KEY keywords (keywords),
  KEY dateline (dateline),
  KEY visible (visible)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_attachments;
CREATE TABLE sablog_attachments (
  attachmentid mediumint(8) unsigned NOT NULL auto_increment,
  articleid int(10) unsigned NOT NULL default '0',
  dateline int(10) unsigned NOT NULL default '0',
  filename varchar(100) NOT NULL default '',
  filetype varchar(50) NOT NULL default '',
  filesize int(10) unsigned NOT NULL default '0',
  downloads mediumint(8) NOT NULL default '0',
  filepath varchar(255) NOT NULL default '',
  thumb_filepath varchar(255) NOT NULL default '',
  thumb_width smallint(6) NOT NULL default '0',
  thumb_height smallint(6) NOT NULL default '0',
  isimage tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (attachmentid),
  KEY articleid (articleid),
  KEY dateline (dateline)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_categories;
CREATE TABLE sablog_categories (
  cid smallint(6) unsigned NOT NULL auto_increment,
  name char(50) NOT NULL default '',
  displayorder tinyint(3) NOT NULL default '0',
  articles mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (cid),
  KEY displayorder (displayorder)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_comments;
CREATE TABLE sablog_comments (
  commentid int(10) unsigned NOT NULL auto_increment,
  articleid mediumint(8) unsigned NOT NULL default '0',
  author varchar(20) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  content mediumtext NOT NULL,
  ipaddress varchar(16) NOT NULL default '',
  visible tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (commentid),
  KEY articleid (articleid),
  KEY dateline (dateline),
  KEY ipaddress (ipaddress)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_links;
CREATE TABLE sablog_links (
  linkid smallint(6) unsigned NOT NULL auto_increment,
  displayorder tinyint(3) NOT NULL default '0',
  name varchar(100) NOT NULL default '',
  url varchar(200) NOT NULL default '',
  note varchar(200) NOT NULL default '',
  visible tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (linkid),
  KEY displayorder (displayorder)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_searchindex;
CREATE TABLE sablog_searchindex (
  searchid int(11) NOT NULL auto_increment,
  keywords varchar(255) NOT NULL default '',
  dateline int(10) NOT NULL default '0',
  sortby varchar(32) NOT NULL default '',
  orderby varchar(4) NOT NULL default '',
  tatols smallint(6) NOT NULL default '0',
  ids text NOT NULL,
  searchfrom varchar(30) NOT NULL default '',
  ipaddress varchar(16) NOT NULL default '',
  PRIMARY KEY  (searchid),
  KEY dateline (dateline),
  KEY sortby (sortby),
  KEY orderby (orderby),
  KEY searchfrom (searchfrom)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_settings;
CREATE TABLE sablog_settings (
  title varchar(50) NOT NULL default '',
  value text NOT NULL,
  PRIMARY KEY  (title)
) ENGINE=MyISAM;


INSERT INTO sablog_settings VALUES ('name', 'yourname''s blog');
INSERT INTO sablog_settings VALUES ('url', '');
INSERT INTO sablog_settings VALUES ('description', '人的头脑太复杂,时间过得久,有时候连自己也被自己骗了,记下来才是最真实的......');
INSERT INTO sablog_settings VALUES ('icp', '');
INSERT INTO sablog_settings VALUES ('viewmode', 'normal');
INSERT INTO sablog_settings VALUES ('templatename', 'default');
INSERT INTO sablog_settings VALUES ('normal_shownum', '8');
INSERT INTO sablog_settings VALUES ('list_shownum', '50');
INSERT INTO sablog_settings VALUES ('article_order', 'dateline');
INSERT INTO sablog_settings VALUES ('title_limit', '0');
INSERT INTO sablog_settings VALUES ('tags_shownum', '100');
INSERT INTO sablog_settings VALUES ('related_shownum', '0');
INSERT INTO sablog_settings VALUES ('related_title_limit', '0');
INSERT INTO sablog_settings VALUES ('related_order', 'dateline');
INSERT INTO sablog_settings VALUES ('show_calendar', '1');
INSERT INTO sablog_settings VALUES ('show_categories', '1');
INSERT INTO sablog_settings VALUES ('hottags_shownum', '0');
INSERT INTO sablog_settings VALUES ('show_archives', '1');
INSERT INTO sablog_settings VALUES ('archives_num', '0');
INSERT INTO sablog_settings VALUES ('recentcomment_num', '10');
INSERT INTO sablog_settings VALUES ('recentcomment_limit', '16');
INSERT INTO sablog_settings VALUES ('show_statistics', '1');
INSERT INTO sablog_settings VALUES ('sidebarlinknum', '15');
INSERT INTO sablog_settings VALUES ('random_links', '1');
INSERT INTO sablog_settings VALUES ('show_debug', '1');
INSERT INTO sablog_settings VALUES ('audit_comment', '0');
INSERT INTO sablog_settings VALUES ('seccode', '1');
INSERT INTO sablog_settings VALUES ('comment_order', '1');
INSERT INTO sablog_settings VALUES ('article_comment_num', '20');
INSERT INTO sablog_settings VALUES ('comment_min_len', '4');
INSERT INTO sablog_settings VALUES ('comment_max_len', '6000');
INSERT INTO sablog_settings VALUES ('commentlist_num', '20');
INSERT INTO sablog_settings VALUES ('comment_post_space', '20');
INSERT INTO sablog_settings VALUES ('allow_search_comments', '0');
INSERT INTO sablog_settings VALUES ('search_post_space', '30');
INSERT INTO sablog_settings VALUES ('search_keywords_min_len', '3');
INSERT INTO sablog_settings VALUES ('attachments_dir', 'attachments');
INSERT INTO sablog_settings VALUES ('attachments_save_dir', '2');
INSERT INTO sablog_settings VALUES ('attachments_display', '0');
INSERT INTO sablog_settings VALUES ('attachments_thumbs', '1');
INSERT INTO sablog_settings VALUES ('attachments_thumbs_size', '500x500');
INSERT INTO sablog_settings VALUES ('display_attach', '1');
INSERT INTO sablog_settings VALUES ('remote_open', '1');
INSERT INTO sablog_settings VALUES ('watermark', '0');
INSERT INTO sablog_settings VALUES ('watermark_size', '300x300');
INSERT INTO sablog_settings VALUES ('waterpos', '2');
INSERT INTO sablog_settings VALUES ('watermarktrans', '100');
INSERT INTO sablog_settings VALUES ('pos_padding', '5');
INSERT INTO sablog_settings VALUES ('server_timezone', '8');
INSERT INTO sablog_settings VALUES ('normaltime', 'Y, F j, g:i A');
INSERT INTO sablog_settings VALUES ('listtime', 'Y-m-d');
INSERT INTO sablog_settings VALUES ('comment_timeformat', 'Y, F j, g:i A');
INSERT INTO sablog_settings VALUES ('trackback_timeformat', 'Y, F j, g:i A');
INSERT INTO sablog_settings VALUES ('recent_comment_timeformat', 'm-d');
INSERT INTO sablog_settings VALUES ('close', '0');
INSERT INTO sablog_settings VALUES ('close_note', '系统升级中....');
INSERT INTO sablog_settings VALUES ('gzipcompress', '1');
INSERT INTO sablog_settings VALUES ('showmsg', '0');
INSERT INTO sablog_settings VALUES ('closereg', '0');
INSERT INTO sablog_settings VALUES ('censoruser', 'test,操你妈,rinima,日你妈,鸡,鸭');
INSERT INTO sablog_settings VALUES ('seccode_enable', '1');
INSERT INTO sablog_settings VALUES ('enable_trackback', '1');
INSERT INTO sablog_settings VALUES ('audit_trackback', '0');
INSERT INTO sablog_settings VALUES ('trackback_life', '0');
INSERT INTO sablog_settings VALUES ('trackback_num', '25');
INSERT INTO sablog_settings VALUES ('trackback_excerpt_limit', '60');
INSERT INTO sablog_settings VALUES ('trackback_list_excerpt_limit', '250');
INSERT INTO sablog_settings VALUES ('trackback_order', '1');
INSERT INTO sablog_settings VALUES ('smarturl', '1');
INSERT INTO sablog_settings VALUES ('artlink_ext', 'html');
INSERT INTO sablog_settings VALUES ('title_keywords', '');
INSERT INTO sablog_settings VALUES ('meta_keywords', '');
INSERT INTO sablog_settings VALUES ('meta_description', '');
INSERT INTO sablog_settings VALUES ('banip_enable', '0');
INSERT INTO sablog_settings VALUES ('ban_ip', '');
INSERT INTO sablog_settings VALUES ('spam_enable', '1');
INSERT INTO sablog_settings VALUES ('spam_words', '虚拟主机,域名注册,出租网,六合彩,铃声下载,手机铃声,和弦铃声,手机游戏,免费铃声,彩铃,网站建设,操你妈,rinima,日你妈,αngel,鸡,操,鸡吧,小姐,fuck,胡锦涛,温家宝,胡温,李洪志,法轮,民运,反共,专制,专政,独裁,极权,中共,共产,共党,六四,民主,人权,毛泽东,中国政府,中央政府,游行示威,天安门,达赖,他妈的,我操,强奸,法轮');
INSERT INTO sablog_settings VALUES ('spam_url_num', '3');
INSERT INTO sablog_settings VALUES ('spam_content_size', '2000');
INSERT INTO sablog_settings VALUES ('tb_spam_level', 'strong');
INSERT INTO sablog_settings VALUES ('js_enable', '0');
INSERT INTO sablog_settings VALUES ('js_cache_life', '1000');
INSERT INTO sablog_settings VALUES ('js_lock_url', '');
INSERT INTO sablog_settings VALUES ('rss_enable', '1');
INSERT INTO sablog_settings VALUES ('rss_num', '20');
INSERT INTO sablog_settings VALUES ('rss_ttl', '30');
INSERT INTO sablog_settings VALUES ('rewrite_enable', '0');
INSERT INTO sablog_settings VALUES ('rewrite_ext', 'html');
INSERT INTO sablog_settings VALUES ('wap_enable', '0');
INSERT INTO sablog_settings VALUES ('wap_article_pagenum', '10');
INSERT INTO sablog_settings VALUES ('wap_article_title_limit', '0');
INSERT INTO sablog_settings VALUES ('wap_tags_pagenum', '15');
INSERT INTO sablog_settings VALUES ('wap_comment_pagenum', '5');
INSERT INTO sablog_settings VALUES ('wap_trackback_pagenum', '5');


DROP TABLE IF EXISTS sablog_sessions;
CREATE TABLE sablog_sessions (
  hash varchar(20) NOT NULL default '',
  uid mediumint(8) NOT NULL default '0',
  groupid smallint(6) NOT NULL,
  ipaddress varchar(16) NOT NULL default '',
  agent varchar(200) NOT NULL,
  lastactivity int(10) NOT NULL default '0',
  PRIMARY KEY  (hash)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_statistics;
CREATE TABLE sablog_statistics (
  cate_count int(11) NOT NULL default '0',
  article_count int(11) NOT NULL default '0',
  comment_count int(11) NOT NULL default '0',
  tag_count int(11) NOT NULL default '0',
  attachment_count int(11) NOT NULL default '0',
  all_view_count int(11) NOT NULL default '0',
  today_view_count int(11) NOT NULL default '0',
  trackback_count int(11) NOT NULL default '0',
  user_count int(11) NOT NULL default '0',
  curdate varchar(20) NOT NULL default ''
) ENGINE=MyISAM;

INSERT INTO sablog_statistics VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, '2007-02-28');

DROP TABLE IF EXISTS sablog_tags;
CREATE TABLE sablog_tags (
  tagid int(11) unsigned NOT NULL auto_increment,
  tag varchar(100) NOT NULL default '',
  usenum int(11) NOT NULL default '0',
  aids text NOT NULL,
  PRIMARY KEY  (tagid),
  KEY usenum (usenum)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_trackbacklog;
CREATE TABLE sablog_trackbacklog (
  trackbacklogid int(11) unsigned NOT NULL auto_increment,
  articleid int(11) unsigned NOT NULL default '0',
  dateline int(10) unsigned NOT NULL default '0',
  pingurl varchar(255) NOT NULL default '',
  PRIMARY KEY  (trackbacklogid),
  KEY articleid (articleid)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS sablog_trackbacks;
CREATE TABLE sablog_trackbacks (
  trackbackid int(11) unsigned NOT NULL auto_increment,
  articleid int(11) unsigned NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  excerpt varchar(255) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  blog_name varchar(255) NOT NULL default '',
  ipaddress varchar(16) NOT NULL default '',
  visible tinyint(1) NOT NULL default '0',
  point varchar(5) NOT NULL default '',
  PRIMARY KEY  (trackbackid),
  KEY articleid (articleid),
  KEY visible (visible)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_users;
CREATE TABLE sablog_users (
  userid mediumint(8) unsigned NOT NULL auto_increment,
  username varchar(20) NOT NULL default '',
  password varchar(32) NOT NULL default '',
  logincount smallint(6) NOT NULL default '0',
  loginip varchar(16) NOT NULL default '',
  logintime int(10) NOT NULL default '0',
  url varchar(255) NOT NULL,
  articles int(11) NOT NULL default '0',
  regdateline int(10) NOT NULL,
  regip varchar(16) NOT NULL,
  groupid smallint(4) NOT NULL,
  lastpost int(10) NOT NULL,
  PRIMARY KEY  (userid)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS sablog_stylevars;
CREATE TABLE sablog_stylevars (
  stylevarid mediumint(9) NOT NULL auto_increment,
  title varchar(200) NOT NULL,
  value text NOT NULL,
  visible tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (stylevarid)
) ENGINE=MyISAM;
