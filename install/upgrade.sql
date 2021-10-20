# 升级用

DROP TABLE IF EXISTS tmp_admin;
CREATE TABLE tmp_admin (
  adminid mediumint(8) unsigned NOT NULL auto_increment,
  username varchar(20) NOT NULL default '',
  nickname varchar(50) NOT NULL default '',
  password varchar(32) NOT NULL default '',
  logincount smallint(6) NOT NULL default '0',
  loginip varchar(16) NOT NULL default '',
  logintime int(10) NOT NULL default '0',
  email varchar(50) NOT NULL default '',
  face varchar(255) NOT NULL default '',
  signature text NOT NULL,
  articles int(11) NOT NULL default '0',
  allowarticle tinyint(1) NOT NULL default '1',
  allowattachment tinyint(1) NOT NULL default '1',
  allowcache tinyint(1) NOT NULL default '1',
  allowcategory tinyint(1) NOT NULL default '0',
  allowcomment tinyint(1) NOT NULL default '1',
  allowconfigurate tinyint(1) NOT NULL default '0',
  allowdatabase tinyint(1) NOT NULL default '0',
  allowlinks tinyint(1) NOT NULL default '1',
  allowlog tinyint(1) NOT NULL default '0',
  allowtags tinyint(1) NOT NULL default '1',
  allowtrackback tinyint(1) NOT NULL default '1',
  allowuser tinyint(1) NOT NULL default '0',
  selectcid smallint(4) NOT NULL default '0',
  PRIMARY KEY  (adminid)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS tmp_adminlog;
CREATE TABLE tmp_adminlog (
  adminlogid int(11) unsigned NOT NULL auto_increment,
  action varchar(50) NOT NULL default '',
  script varchar(255) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  ipaddress varchar(16) NOT NULL default '',
  username varchar(20) NOT NULL default '',
  PRIMARY KEY  (adminlogid)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS tmp_articles;
CREATE TABLE tmp_articles (
  articleid mediumint(8) unsigned NOT NULL auto_increment,
  cid smallint(6) unsigned NOT NULL default '0',
  cname varchar(50) NOT NULL default '',
  author varchar(50) NOT NULL default '',
  authorid mediumint(8) unsigned NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  description text NOT NULL,
  content MEDIUMTEXT NOT NULL,
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
  PRIMARY KEY  (articleid),
  KEY cid (cid),
  KEY authorid (authorid),
  KEY keywords (keywords),
  KEY dateline (dateline),
  KEY visible (visible)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS tmp_attachments;
CREATE TABLE tmp_attachments (
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


DROP TABLE IF EXISTS tmp_categories;
CREATE TABLE tmp_categories (
  cid smallint(6) unsigned NOT NULL auto_increment,
  name char(50) NOT NULL default '',
  displayorder tinyint(3) NOT NULL default '0',
  articles mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (cid),
  KEY displayorder (displayorder)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS tmp_comments;
CREATE TABLE tmp_comments (
  commentid int(10) unsigned NOT NULL auto_increment,
  articleid mediumint(8) unsigned NOT NULL default '0',
  author varchar(20) NOT NULL default '',
  authorid int(11) NOT NULL default '0',
  url varchar(255) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  content mediumtext NOT NULL,
  ipaddress varchar(16) NOT NULL default '',
  visible tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (commentid),
  KEY articleid (articleid),
  KEY authorid (authorid),
  KEY dateline (dateline),
  KEY ipaddress (ipaddress)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS tmp_links;
CREATE TABLE tmp_links (
  linkid smallint(6) unsigned NOT NULL auto_increment,
  displayorder tinyint(3) NOT NULL default '0',
  name varchar(100) NOT NULL default '',
  url varchar(200) NOT NULL default '',
  note varchar(200) NOT NULL default '',
  visible tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (linkid),
  KEY displayorder (displayorder)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS tmp_loginlog;
CREATE TABLE tmp_loginlog (
  loginlogid int(11) NOT NULL auto_increment,
  username varchar(100) NOT NULL default '',
  dateline int(10) NOT NULL default '0',
  ipaddress varchar(16) NOT NULL default '',
  result int(11) NOT NULL default '0',
  PRIMARY KEY  (loginlogid)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS tmp_searchindex;
CREATE TABLE tmp_searchindex (
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


DROP TABLE IF EXISTS tmp_settings;
CREATE TABLE tmp_settings (
  title varchar(50) NOT NULL default '',
  value text NOT NULL,
  PRIMARY KEY  (title)
) ENGINE=MyISAM;


INSERT INTO tmp_settings (title, value) VALUES ('name', 'yourname''s blog'),
('url', 'http://www.yourname.com/blog'),
('description', '记录我的一些生活写照、无聊的牢骚、内心世界的活动'),
('icp', ''),
('templatename', 'default'),
('viewmode', 'normal'),
('normal_shownum', '8'),
('list_shownum', '60'),
('article_order', 'dateline'),
('title_limit', '40'),
('tags_shownum', '100'),
('related_shownum', '10'),
('related_title_limit', '50'),
('related_order', 'dateline'),
('show_calendar', '1'),
('show_categories', '1'),
('hottags_shownum', '10'),
('show_archives', '1'),
('show_statistics', '1'),
('sidebarlinknum', '15'),
('random_links', '1'),
('show_debug', '1'),
('recentcomment_num', '10'),
('recentcomment_limit', '14'),
('audit_comment', '0'),
('seccode', '0'),
('comment_order', '1'),
('article_comment_num', '20'),
('comment_min_len', '4'),
('comment_max_len', '6000'),
('commentlist_num', '20'),
('comment_post_space', '20'),
('allow_search_comments', '1'),
('search_post_space', '30'),
('search_keywords_min_len', '3'),
('attachments_dir', 'attachments'),
('attachments_save_dir', '2'),
('attachments_display', '0'),
('attachments_thumbs', '1'),
('attachments_thumbs_size', '400x400'),
('display_attach', '1'),
('remote_open', '1'),
('server_timezone', '8'),
('normaltime', 'Y, F j, g:i A'),
('listtime', 'Y-m-d'),
('comment_timeformat', 'Y, F j, g:i A'),
('trackback_timeformat', 'Y, F j, g:i A'),
('recent_comment_timeformat', 'm-d'),
('close', '0'),
('close_note', '系统维护中....'),
('gzipcompress', '0'),
('attack_reject', 'close'),
('refreshtime', '2'),
('showmsg', '0'),
('closereg', '0'),
('censoruser', ''),
('audit_trackback', '0'),
('trackback_num', '25'),
('trackback_excerpt_limit', '60'),
('trackback_list_excerpt_limit', '250'),
('trackback_order', '1'),
('smarturl', '1'),
('artlink_ext', 'html'),
('title_keywords', ''),
('meta_keywords', ''),
('meta_description', ''),
('watermark', '1'),
('watermark_size', '400x400'),
('waterpos', '4'),
('watermarktrans', '70'),
('pos_padding', '5'),
('banip_enable', '0'),
('ban_ip', ''),
('spam_enable', '1'),
('spam_words', '操你妈,rinima,日你妈'),
('spam_url_num', '3'),
('spam_content_size', '2000'),
('tb_spam_level', 'strong'),
('js_enable', '0'),
('js_cache_life', '1000'),
('js_lock_url', '');


DROP TABLE IF EXISTS tmp_statistics;
CREATE TABLE tmp_statistics (
  cate_count int(11) NOT NULL default '0',
  article_count int(11) NOT NULL default '0',
  comment_count int(11) NOT NULL default '0',
  tag_count int(11) NOT NULL default '0',
  attachment_count int(11) NOT NULL default '0',
  trackback_count int(11) NOT NULL default '0',
  user_count int(11) NOT NULL default '0',
) ENGINE=MyISAM;


INSERT INTO tmp_statistics (cate_count, article_count, comment_count, tag_count, attachment_count, trackback_count, user_count) VALUES (0, 0, 0, 0, 0, 0, 0);


DROP TABLE IF EXISTS tmp_tags;
CREATE TABLE tmp_tags (
  tagid int(11) unsigned NOT NULL auto_increment,
  tag varchar(100) NOT NULL default '',
  usenum int(11) NOT NULL default '0',
  aids text NOT NULL,
  PRIMARY KEY  (tagid),
  KEY usenum (usenum)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS tmp_trackbacklog;
CREATE TABLE tmp_trackbacklog (
  trackbacklogid int(11) unsigned NOT NULL auto_increment,
  articleid int(11) unsigned NOT NULL default '0',
  dateline int(10) unsigned NOT NULL default '0',
  pingurl varchar(255) NOT NULL default '',
  PRIMARY KEY  (trackbacklogid),
  KEY articleid (articleid)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS tmp_trackbacks;
CREATE TABLE tmp_trackbacks (
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


DROP TABLE IF EXISTS tmp_users;
CREATE TABLE tmp_users (
  userid int(11) NOT NULL auto_increment,
  username varchar(50) NOT NULL default '',
  password varchar(50) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  regdateline int(10) NOT NULL default '0',
  ipaddress varchar(16) NOT NULL default '',
  lastpost int(10) NOT NULL default '0',
  PRIMARY KEY  (userid)
) ENGINE=MyISAM;
