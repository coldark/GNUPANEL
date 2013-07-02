
DROP TABLE IF EXISTS jos_banner;
DROP TABLE IF EXISTS jos_bannerclient;
DROP TABLE IF EXISTS jos_bannerfinish;
DROP TABLE IF EXISTS jos_categories;
DROP TABLE IF EXISTS jos_components;
DROP TABLE IF EXISTS jos_contact_details;
DROP TABLE IF EXISTS jos_content;
DROP TABLE IF EXISTS jos_content_frontpage;
DROP TABLE IF EXISTS jos_content_rating;
DROP TABLE IF EXISTS jos_core_acl_aro;
DROP TABLE IF EXISTS jos_core_acl_aro_groups;
DROP TABLE IF EXISTS jos_core_acl_aro_sections;
DROP TABLE IF EXISTS jos_core_acl_groups_aro_map;
DROP TABLE IF EXISTS jos_core_log_items;
DROP TABLE IF EXISTS jos_core_log_searches;
DROP TABLE IF EXISTS jos_groups;
DROP TABLE IF EXISTS jos_help;
DROP TABLE IF EXISTS jos_mambots;
DROP TABLE IF EXISTS jos_menu;
DROP TABLE IF EXISTS jos_messages;
DROP TABLE IF EXISTS jos_messages_cfg;
DROP TABLE IF EXISTS jos_modules;
DROP TABLE IF EXISTS jos_modules_menu;
DROP TABLE IF EXISTS jos_newsfeeds;
DROP TABLE IF EXISTS jos_newsflash;
DROP TABLE IF EXISTS jos_poll_data;
DROP TABLE IF EXISTS jos_poll_date;
DROP TABLE IF EXISTS jos_poll_menu;
DROP TABLE IF EXISTS jos_polls;
DROP TABLE IF EXISTS jos_sections;
DROP TABLE IF EXISTS jos_session;
DROP TABLE IF EXISTS jos_stats_agents;
DROP TABLE IF EXISTS jos_templates;
DROP TABLE IF EXISTS jos_template_positions;
DROP TABLE IF EXISTS jos_templates_menu;
DROP TABLE IF EXISTS jos_users;
DROP TABLE IF EXISTS jos_usertypes;
DROP TABLE IF EXISTS jos_weblinks;

CREATE TABLE `#__banner` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `type` varchar(10) NOT NULL default 'banner',
  `name` varchar(50) NOT NULL default '',
  `imptotal` int(11) NOT NULL default '0',
  `impmade` int(11) NOT NULL default '0',
  `clicks` int(11) NOT NULL default '0',
  `imageurl` varchar(100) NOT NULL default '',
  `clickurl` varchar(200) NOT NULL default '',
  `date` datetime default NULL,
  `showBanner` tinyint(1) NOT NULL default '0',
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `custombannercode` text,
  PRIMARY KEY  (`bid`),
  KEY `viewbanner` (`showBanner`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `#__bannerclient` (
  `cid` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `contact` varchar(60) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `extrainfo` text NOT NULL,
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` time default NULL,
  `editor` varchar(50) default NULL,
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM;

CREATE TABLE `#__bannerfinish` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `type` varchar(10) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `impressions` int(11) NOT NULL default '0',
  `clicks` int(11) NOT NULL default '0',
  `imageurl` varchar(50) NOT NULL default '',
  `datestart` datetime default NULL,
  `dateend` datetime default NULL,
  PRIMARY KEY  (`bid`)
) TYPE=MyISAM;

CREATE TABLE `#__categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default 0,
  `title` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `image` varchar(100) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `image_position` varchar(10) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_section` (`section`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) TYPE=MyISAM;

CREATE TABLE `#__components` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `menuid` int(11) unsigned NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `admin_menu_link` varchar(255) NOT NULL default '',
  `admin_menu_alt` varchar(255) NOT NULL default '',
  `option` varchar(50) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `admin_menu_img` varchar(255) NOT NULL default '',
  `iscore` tinyint(4) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `#__components` VALUES (1, 'Banners', '', 0, 0, '', 'Banner Management', 'com_banners', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `#__components` VALUES (2, 'Manage Banners', '', 0, 1, 'option=com_banners', 'Active Banners', 'com_banners', 1, 'js/ThemeOffice/edit.png', 0, '');
INSERT INTO `#__components` VALUES (3, 'Manage Clients', '', 0, 1, 'option=com_banners&task=listclients', 'Manage Clients', 'com_banners', 2, 'js/ThemeOffice/categories.png', 0, '');
INSERT INTO `#__components` VALUES (4, 'Web Links', 'option=com_weblinks', 0, 0, '', 'Manage Weblinks', 'com_weblinks', 0, 'js/ThemeOffice/globe2.png', 0, '');
INSERT INTO `#__components` VALUES (5, 'Web Link Items', '', 0, 4, 'option=com_weblinks', 'View existing weblinks', 'com_weblinks', 1, 'js/ThemeOffice/edit.png', 0, '');
INSERT INTO `#__components` VALUES (6, 'Web Link Categories', '', 0, 4, 'option=categories&section=com_weblinks', 'Manage weblink categories', '', 2, 'js/ThemeOffice/categories.png', 0, '');
INSERT INTO `#__components` VALUES (7, 'Contacts', 'option=com_contact', 0, 0, '', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/user.png', 1, '');
INSERT INTO `#__components` VALUES (8, 'Manage Contacts', '', 0, 7, 'option=com_contact', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/edit.png', 1, '');
INSERT INTO `#__components` VALUES (9, 'Contact Categories', '', 0, 7, 'option=categories&section=com_contact_details', 'Manage contact categories', '', 2, 'js/ThemeOffice/categories.png', 1, '');
INSERT INTO `#__components` VALUES (10, 'Front Page', 'option=com_frontpage', 0, 0, '', 'Manage Front Page Items', 'com_frontpage', 0, 'js/ThemeOffice/component.png', 1, '');
INSERT INTO `#__components` VALUES (11, 'Polls', 'option=com_poll', 0, 0, 'option=com_poll', 'Manage Polls', 'com_poll', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `#__components` VALUES (12, 'News Feeds', 'option=com_newsfeeds', 0, 0, '', 'News Feeds Management', 'com_newsfeeds', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `#__components` VALUES (13, 'Manage News Feeds', '', 0, 12, 'option=com_newsfeeds', 'Manage News Feeds', 'com_newsfeeds', 1, 'js/ThemeOffice/edit.png', 0, '');
INSERT INTO `#__components` VALUES (14, 'Manage Categories', '', 0, 12, 'option=com_categories&section=com_newsfeeds', 'Manage Categories', '', 2, 'js/ThemeOffice/categories.png', 0, '');
INSERT INTO `#__components` VALUES (15, 'Login', 'option=com_login', 0, 0, '', '', 'com_login', 0, '', 1, '');
INSERT INTO `#__components` VALUES (16, 'Search', 'option=com_search', 0, 0, '', '', 'com_search', 0, '', 1, '');
INSERT INTO `#__components` VALUES (17, 'Syndicate','',0,0,'option=com_syndicate&hidemainmenu=1','Manage Syndication Settings','com_syndicate',0,'js/ThemeOffice/component.png',0,'');
INSERT INTO `#__components` VALUES (18, 'Mass Mail', '', 0, 0, 'option=com_massmail&hidemainmenu=1', 'Send Mass Mail', 'com_massmail', 0, 'js/ThemeOffice/mass_email.png', 0, '');

CREATE TABLE `#__contact_details` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `con_position` varchar(50) default NULL,
  `address` text,
  `suburb` varchar(50) default NULL,
  `state` varchar(20) default NULL,
  `country` varchar(50) default NULL,
  `postcode` varchar(10) default NULL,
  `telephone` varchar(25) default NULL,
  `fax` varchar(25) default NULL,
  `misc` mediumtext,
  `image` varchar(100) default NULL,
  `imagepos` varchar(20) default NULL,
  `email_to` varchar(100) default NULL,
  `default_con` tinyint(1) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `#__content` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `title_alias` varchar(100) NOT NULL default '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL default '0',
  `sectionid` int(11) unsigned NOT NULL default '0',
  `mask` int(11) unsigned NOT NULL default '0',
  `catid` int(11) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL default '0',
  `created_by_alias` varchar(100) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` text NOT NULL,
  `version` int(11) unsigned NOT NULL default '1',
  `parentid` int(11) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idx_section` (`sectionid`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_mask` (`mask`)
) TYPE=MyISAM;

CREATE TABLE `#__content_frontpage` (
  `content_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`content_id`)
) TYPE=MyISAM;

CREATE TABLE `#__content_rating` (
  `content_id` int(11) NOT NULL default '0',
  `rating_sum` int(11) unsigned NOT NULL default '0',
  `rating_count` int(11) unsigned NOT NULL default '0',
  `lastip` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`content_id`)
) TYPE=MyISAM;

CREATE TABLE `#__core_log_items` (
  `time_stamp` date NOT NULL default '0000-00-00',
  `item_table` varchar(50) NOT NULL default '',
  `item_id` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM;

CREATE TABLE `#__core_log_searches` (
  `search_term` varchar(128) NOT NULL default '',
  `hits` int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM;

CREATE TABLE `#__groups` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `#__groups` VALUES (0, 'Public');
INSERT INTO `#__groups` VALUES (1, 'Registered');
INSERT INTO `#__groups` VALUES (2, 'Special');

CREATE TABLE `#__mambots` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `element` varchar(100) NOT NULL default '',
  `folder` varchar(100) NOT NULL default '',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(3) NOT NULL default '0',
  `iscore` tinyint(3) NOT NULL default '0',
  `client_id` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_folder` (`published`,`client_id`,`access`,`folder`)
) TYPE=MyISAM;

INSERT INTO `#__mambots` VALUES (1,'MOS Image','mosimage','content',0,-10000,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (2,'MOS Pagination','mospaging','content',0,10000,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (3,'Legacy Mambot Includer','legacybots','content',0,1,0,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (4,'SEF','mossef','content',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (5,'MOS Rating','mosvote','content',0,4,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (6,'Search Content','content.searchbot','search',0,1,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (7,'Search Weblinks','weblinks.searchbot','search',0,2,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (8,'Code support','moscode','content',0,2,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (9,'No WYSIWYG Editor','none','editors',0,0,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (10,'TinyMCE WYSIWYG Editor','tinymce','editors',0,0,1,1,0,0,'0000-00-00 00:00:00','theme=advanced');
INSERT INTO `#__mambots` VALUES (11,'MOS Image Editor Button','mosimage.btn','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (12,'MOS Pagebreak Editor Button','mospage.btn','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (13,'Search Contacts','contacts.searchbot','search',0,3,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__mambots` VALUES (14, 'Search Categories', 'categories.searchbot', 'search', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__mambots` VALUES (15, 'Search Sections', 'sections.searchbot', 'search', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__mambots` VALUES (16, 'Email Cloaking', 'mosemailcloak', 'content', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__mambots` VALUES (17, 'GeSHi', 'geshi', 'content', 0, 5, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__mambots` VALUES (18, 'Search Newsfeeds', 'newsfeeds.searchbot', 'search', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__mambots` VALUES (19, 'Load Module Positions', 'mosloadposition', 'content', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', '');

CREATE TABLE `#__menu` (
  `id` int(11) NOT NULL auto_increment,
  `menutype` varchar(25) default NULL,
  `name` varchar(100) default NULL,
  `link` text,
  `type` varchar(50) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `componentid` int(11) unsigned NOT NULL default '0',
  `sublevel` int(11) default '0',
  `ordering` int(11) default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `pollid` int(11) NOT NULL default '0',
  `browserNav` tinyint(4) default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `utaccess` tinyint(3) unsigned NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`,`access`),
  KEY `menutype` (`menutype`)
) TYPE=MyISAM;

INSERT INTO `#__menu` VALUES (1, 'mainmenu', 'Home', 'index.php?option=com_frontpage', 'components', 1, 0, 10, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'leading=1\r\nintro=2\r\nlink=1\r\nimage=1\r\npage_title=0\r\nheader=Welcome to the Frontpage\r\norderby_sec=front\r\nprint=0\r\npdf=0\r\nemail=0\r\nback_button=0');

CREATE TABLE `#__messages` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `user_id_from` int(10) unsigned NOT NULL default '0',
  `user_id_to` int(10) unsigned NOT NULL default '0',
  `folder_id` int(10) unsigned NOT NULL default '0',
  `date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` int(11) NOT NULL default '0',
  `priority` int(1) unsigned NOT NULL default '0',
  `subject` varchar(230) NOT NULL default '',
  `message` text NOT NULL,
  PRIMARY KEY  (`message_id`)
) TYPE=MyISAM;

CREATE TABLE `#__messages_cfg` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `cfg_name` varchar(100) NOT NULL default '',
  `cfg_value` varchar(255) NOT NULL default '',
  UNIQUE `idx_user_var_name` (`user_id`,`cfg_name`)
) TYPE=MyISAM;

CREATE TABLE `#__modules` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `position` varchar(10) default NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `module` varchar(50) default NULL,
  `numnews` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `showtitle` tinyint(3) unsigned NOT NULL default '1',
  `params` text NOT NULL,
  `iscore` tinyint(4) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`)
) TYPE=MyISAM;

INSERT INTO `#__modules` VALUES (0, 'Polls', '', 1, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_poll', 0, 0, 1, '', 0, 0);
INSERT INTO `#__modules` VALUES (0, 'User Menu', '', 2, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 1, 1, 'menutype=usermenu', 1, 0);
INSERT INTO `#__modules` VALUES (0, 'Main Menu', '', 1, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 1, 'menutype=mainmenu', 1, 0);
INSERT INTO `#__modules` VALUES (0, 'Login Form', '', 3, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, '', 1, 0);
INSERT INTO `#__modules` VALUES (0, 'Syndicate', '', 4, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_rssfeed', 0, 0, 1, '', 1, 0);
INSERT INTO `#__modules` VALUES (0, 'Latest News', '', 4, 'user1', 0, '0000-00-00 00:00:00', 1, 'mod_latestnews', 0, 0, 1, '', 1, 0);
INSERT INTO `#__modules` VALUES (0, 'Statistics', '', 4, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_stats', 0, 0, 1, 'serverinfo=1\nsiteinfo=1\ncounter=1\nincrease=0\nmoduleclass_sfx=', 0, 0);
INSERT INTO `#__modules` VALUES (0, 'Who\'s Online', '', 1, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_whosonline', 0, 0, 1, 'online=1\nusers=1\nmoduleclass_sfx=', 0, 0);
INSERT INTO `#__modules` VALUES (0, 'Popular', '', 6, 'user2', 0, '0000-00-00 00:00:00', 1, 'mod_mostread', 0, 0, 1, '', 0, 0);
INSERT INTO `#__modules` VALUES (0, 'Template Chooser','',6,'left',0,'0000-00-00 00:00:00',0,'mod_templatechooser', 0, 0, 1, 'show_preview=1', 0, 0);
INSERT INTO `#__modules` VALUES (0, 'Archive', '', 7, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_archive', 0, 0, 1, '', 1, 0);
INSERT INTO `#__modules` VALUES (0, 'Sections', '', 8, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_sections', 0, 0, 1, '', 1, 0);
INSERT INTO `#__modules` VALUES (0, 'Newsflash', '', 1, 'top', 0, '0000-00-00 00:00:00', 1, 'mod_newsflash', 0, 0, 1, 'catid=3\r\nstyle=random\r\nitems=\r\nmoduleclass_sfx=', 0, 0);
INSERT INTO `#__modules` VALUES (0, 'Related Items', '', 9, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_related_items', 0, 0, 1, '', 0, 0);
INSERT INTO `#__modules` VALUES (0, 'Search', '', 1, 'user4', 0, '0000-00-00 00:00:00', 1, 'mod_search', 0, 0, 0, '', 0, 0);
INSERT INTO `#__modules` VALUES (0, 'Random Image', '', 9, 'right', 0, '0000-00-00 00:00:00', 1, 'mod_random_image', 0, 0, 1, '', 0, 0);
INSERT INTO `#__modules` VALUES (0, 'Top Menu', '', 1, 'user3', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=topmenu\nmenu_style=list_flat\nmenu_images=n\nmenu_images_align=left\nexpand_menu=n\nclass_sfx=-nav\nmoduleclass_sfx=\nindent_image1=0\nindent_image2=0\nindent_image3=0\nindent_image4=0\nindent_image5=0\nindent_image6=0', 1, 0);
INSERT INTO `#__modules` VALUES (0, 'Banners', '', 1, 'banner', 0, '0000-00-00 00:00:00', 1, 'mod_banners', 0, 0, 0, 'banner_cids=\nmoduleclass_sfx=\n', 1, 0);
INSERT INTO `#__modules` VALUES (0,'Components','',2,'cpanel',0,'0000-00-00 00:00:00',1,'mod_components',0,99,1,'',1, 1);
INSERT INTO `#__modules` VALUES (0,'Popular','',3,'cpanel',0,'0000-00-00 00:00:00',1,'mod_popular',0,99,1,'',0, 1);
INSERT INTO `#__modules` VALUES (0,'Latest Items','',4,'cpanel',0,'0000-00-00 00:00:00',1,'mod_latest',0,99,1,'',0, 1);
INSERT INTO `#__modules` VALUES (0,'Menu Stats','',5,'cpanel',0,'0000-00-00 00:00:00',1,'mod_stats',0,99,1,'',0, 1);
INSERT INTO `#__modules` VALUES (0,'Unread Messages','',1,'header',0,'0000-00-00 00:00:00',1,'mod_unread',0,99,1,'',1, 1);
INSERT INTO `#__modules` VALUES (0,'Online Users','',2,'header',0,'0000-00-00 00:00:00',1,'mod_online',0,99,1,'',1, 1);
INSERT INTO `#__modules` VALUES (0,'Full Menu','',1,'top',0,'0000-00-00 00:00:00',1,'mod_fullmenu',0,99,1,'',1, 1);
INSERT INTO `#__modules` VALUES (0,'Pathway','',1,'pathway',0,'0000-00-00 00:00:00',1,'mod_pathway',0,99,1,'',1, 1);
INSERT INTO `#__modules` VALUES (0,'Toolbar','',1,'toolbar',0,'0000-00-00 00:00:00',1,'mod_toolbar',0,99,1,'',1, 1);
INSERT INTO `#__modules` VALUES (0,'System Message','',1,'inset',0,'0000-00-00 00:00:00',1,'mod_mosmsg',0,99,1,'',1, 1);
INSERT INTO `#__modules` VALUES (0,'Quick Icons','',1,'icon',0,'0000-00-00 00:00:00',1,'mod_quickicon',0,99,1,'',1,1);
INSERT INTO `#__modules` VALUES (0, 'Other Menu', '', 2, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=othermenu\nmenu_style=vert_indent\ncache=0\nmenu_images=0\nmenu_images_align=0\nexpand_menu=0\nclass_sfx=\nmoduleclass_sfx=\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=', 0, 0);
INSERT INTO `#__modules` VALUES (0,'Wrapper','',10,'left',0,'0000-00-00 00:00:00',0,'mod_wrapper',0,0,1,'',0, 0);
INSERT INTO `#__modules` VALUES (0,'Logged','',0,'cpanel',0,'0000-00-00 00:00:00',1,'mod_logged',0,99,1,'',0,1);

CREATE TABLE `#__modules_menu` (
  `moduleid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`moduleid`,`menuid`)
) TYPE=MyISAM;

INSERT INTO `#__modules_menu` VALUES (1,1);
INSERT INTO `#__modules_menu` VALUES (2,0);
INSERT INTO `#__modules_menu` VALUES (3,0);
INSERT INTO `#__modules_menu` VALUES (4,1);
INSERT INTO `#__modules_menu` VALUES (5,1);
INSERT INTO `#__modules_menu` VALUES (6,1);
INSERT INTO `#__modules_menu` VALUES (6,2);
INSERT INTO `#__modules_menu` VALUES (6,4);
INSERT INTO `#__modules_menu` VALUES (6,27);
INSERT INTO `#__modules_menu` VALUES (6,36);
INSERT INTO `#__modules_menu` VALUES (8,1);
INSERT INTO `#__modules_menu` VALUES (9,1);
INSERT INTO `#__modules_menu` VALUES (9,2);
INSERT INTO `#__modules_menu` VALUES (9,4);
INSERT INTO `#__modules_menu` VALUES (9,27);
INSERT INTO `#__modules_menu` VALUES (9,36);
INSERT INTO `#__modules_menu` VALUES (10,1);
INSERT INTO `#__modules_menu` VALUES (13,0);
INSERT INTO `#__modules_menu` VALUES (15,0);
INSERT INTO `#__modules_menu` VALUES (17,0);
INSERT INTO `#__modules_menu` VALUES (18,0);
INSERT INTO `#__modules_menu` VALUES (30,0);

CREATE TABLE `#__newsfeeds` (
  `catid` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `link` text NOT NULL,
  `filename` varchar(200) default NULL,
  `published` tinyint(1) NOT NULL default '0',
  `numarticles` int(11) unsigned NOT NULL default '1',
  `cache_time` int(11) unsigned NOT NULL default '3600',
  `checked_out` tinyint(3) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`)
) TYPE=MyISAM;

CREATE TABLE `#__poll_data` (
  `id` int(11) NOT NULL auto_increment,
  `pollid` int(4) NOT NULL default '0',
  `text` text NOT NULL,
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) TYPE=MyISAM;

CREATE TABLE `#__poll_date` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `poll_id` (`poll_id`)
) TYPE=MyISAM;

CREATE TABLE `#__polls` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `voters` int(9) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `access` int(11) NOT NULL default '0',
  `lag` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `#__poll_menu` (
  `pollid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pollid`,`menuid`)
) TYPE=MyISAM;

CREATE TABLE `#__sections` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `image` varchar(100) NOT NULL default '',
  `scope` varchar(50) NOT NULL default '',
  `image_position` varchar(10) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_scope` (`scope`)
) TYPE=MyISAM;

CREATE TABLE `#__session` (
  `username` varchar(50) default '',
  `time` varchar(14) default '',
  `session_id` varchar(200) NOT NULL default '0',
  `guest` tinyint(4) default '1',
  `userid` int(11) default '0',
  `usertype` varchar(50) default '',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`session_id`),
  KEY `whosonline` (`guest`,`usertype`)
) TYPE=MyISAM;

CREATE TABLE `#__stats_agents` (
  `agent` varchar(255) NOT NULL default '',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '1'
) TYPE=MyISAM;

CREATE TABLE `#__templates_menu` (
  `template` varchar(50) NOT NULL default '',
  `menuid` int(11) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`template`,`menuid`)
) TYPE=MyISAM;

INSERT INTO `#__templates_menu` VALUES ('rhuk_solarflare_ii', '0', '0');
INSERT INTO `#__templates_menu` VALUES ('joomla_admin', '0', '1');

CREATE TABLE `#__template_positions` (
  `id` int(11) NOT NULL auto_increment,
  `position` varchar(10) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `#__template_positions` VALUES (0, 'left', '');
INSERT INTO `#__template_positions` VALUES (0, 'right', '');
INSERT INTO `#__template_positions` VALUES (0, 'top', '');
INSERT INTO `#__template_positions` VALUES (0, 'bottom', '');
INSERT INTO `#__template_positions` VALUES (0, 'inset', '');
INSERT INTO `#__template_positions` VALUES (0, 'banner', '');
INSERT INTO `#__template_positions` VALUES (0, 'header', '');
INSERT INTO `#__template_positions` VALUES (0, 'footer', '');
INSERT INTO `#__template_positions` VALUES (0, 'newsflash', '');
INSERT INTO `#__template_positions` VALUES (0, 'legals', '');
INSERT INTO `#__template_positions` VALUES (0, 'pathway', '');
INSERT INTO `#__template_positions` VALUES (0, 'toolbar', '');
INSERT INTO `#__template_positions` VALUES (0, 'cpanel', '');
INSERT INTO `#__template_positions` VALUES (0, 'user1', '');
INSERT INTO `#__template_positions` VALUES (0, 'user2', '');
INSERT INTO `#__template_positions` VALUES (0, 'user3', '');
INSERT INTO `#__template_positions` VALUES (0, 'user4', '');
INSERT INTO `#__template_positions` VALUES (0, 'user5', '');
INSERT INTO `#__template_positions` VALUES (0, 'user6', '');
INSERT INTO `#__template_positions` VALUES (0, 'user7', '');
INSERT INTO `#__template_positions` VALUES (0, 'user8', '');
INSERT INTO `#__template_positions` VALUES (0, 'user9', '');
INSERT INTO `#__template_positions` VALUES (0, 'advert1', '');
INSERT INTO `#__template_positions` VALUES (0, 'advert2', '');
INSERT INTO `#__template_positions` VALUES (0, 'advert3', '');
INSERT INTO `#__template_positions` VALUES (0, 'icon', '');
INSERT INTO `#__template_positions` VALUES (0, 'debug', '');

CREATE TABLE `#__users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `username` varchar(25) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `usertype` varchar(25) NOT NULL default '',
  `block` tinyint(4) NOT NULL default '0',
  `sendEmail` tinyint(4) default '0',
  `gid` tinyint(3) unsigned NOT NULL default '1',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL default '',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`)
) TYPE=MyISAM;

CREATE TABLE `#__usertypes` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `mask` varchar(11) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `#__usertypes` VALUES (0, 'superadministrator', '');
INSERT INTO `#__usertypes` VALUES (1, 'administrator', '');
INSERT INTO `#__usertypes` VALUES (2, 'editor', '');
INSERT INTO `#__usertypes` VALUES (3, 'user', '');
INSERT INTO `#__usertypes` VALUES (4, 'author', '');
INSERT INTO `#__usertypes` VALUES (5, 'publisher', '');
INSERT INTO `#__usertypes` VALUES (6, 'manager', '');

CREATE TABLE `#__weblinks` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `url` varchar(250) NOT NULL default '',
  `description` varchar(250) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `archived` tinyint(1) NOT NULL default '0',
  `approved` tinyint(1) NOT NULL default '1',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`,`archived`)
) TYPE=MyISAM;

CREATE TABLE `#__core_acl_aro` (
  `aro_id` int(11) NOT NULL auto_increment,
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`aro_id`),
  UNIQUE KEY `#__gacl_section_value_value_aro` (`section_value`(100),`value`(100)),
  KEY `#__gacl_hidden_aro` (`hidden`)
) TYPE=MyISAM;

CREATE TABLE `#__core_acl_aro_groups` (
  `group_id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  KEY `parent_id_aro_groups` (`parent_id`),
  KEY `#__gacl_parent_id_aro_groups` (`parent_id`),
  KEY `#__gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) TYPE=MyISAM;

INSERT INTO `#__core_acl_aro_groups` VALUES (17,0,'ROOT',1,22);
INSERT INTO `#__core_acl_aro_groups` VALUES (28,17,'USERS',2,21);
INSERT INTO `#__core_acl_aro_groups` VALUES (29,28,'Public Frontend',3,12);
INSERT INTO `#__core_acl_aro_groups` VALUES (18,29,'Registered',4,11);
INSERT INTO `#__core_acl_aro_groups` VALUES (19,18,'Author',5,10);
INSERT INTO `#__core_acl_aro_groups` VALUES (20,19,'Editor',6,9);
INSERT INTO `#__core_acl_aro_groups` VALUES (21,20,'Publisher',7,8);
INSERT INTO `#__core_acl_aro_groups` VALUES (30,28,'Public Backend',13,20);
INSERT INTO `#__core_acl_aro_groups` VALUES (23,30,'Manager',14,19);
INSERT INTO `#__core_acl_aro_groups` VALUES (24,23,'Administrator',15,18);
INSERT INTO `#__core_acl_aro_groups` VALUES (25,24,'Super Administrator',16,17);

CREATE TABLE `#__core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '',
  `aro_id` int(11) NOT NULL default '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`)
) TYPE=MyISAM;

CREATE TABLE `#__core_acl_aro_sections` (
  `section_id` int(11) NOT NULL auto_increment,
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`section_id`),
  UNIQUE KEY `value_aro_sections` (`value`),
  UNIQUE KEY `#__gacl_value_aro_sections` (`value`),
  KEY `hidden_aro_sections` (`hidden`),
  KEY `#__gacl_hidden_aro_sections` (`hidden`)
) TYPE=MyISAM;

INSERT INTO `#__core_acl_aro_sections` VALUES (10,'users',1,'Users',0);

INSERT INTO `#__banner` VALUES (1, 1, 'banner', 'OSM 1', 0, 42, 0, 'osmbanner1.png', 'http://www.opensourcematters.org', '2004-07-07 15:31:29', 1, 0, '0000-00-00 00:00:00', NULL, NULL);
INSERT INTO `#__banner` VALUES (2, 1, 'banner', 'OSM 2', 0, 48, 0, 'osmbanner2.png', 'http://www.opensourcematters.org', '2004-07-07 15:31:29', 1, 0, '0000-00-00 00:00:00', NULL, NULL);

INSERT INTO `#__bannerclient` VALUES (1, 'Open Source Matters', 'Administrator', 'admin@opensourcematters.org', '', 0, '00:00:00', NULL);

INSERT INTO `#__categories` VALUES (1, 0, 'Latest', 'Latest News', 'taking_notes.jpg', '1', 'left', 'The latest news from the Joomla! Team', 1, 0, '0000-00-00 00:00:00', '', 0, 0, 1, '');
INSERT INTO `#__categories` VALUES (2, 0, 'Joomla!', 'Joomla!', 'clock.jpg', 'com_weblinks', 'left', 'A selection of links that are all related to the Joomla! Project.', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0, 0, '');
INSERT INTO `#__categories` VALUES (3, 0, 'Newsflash', 'Newsflash', '', '2', 'left', '', 1, 0, '0000-00-00 00:00:00', '', 0, 0, 0, '');
INSERT INTO `#__categories` VALUES (4, 0, 'Joomla!', 'Joomla!', '', 'com_newsfeeds', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 2, 0, 0, '');
INSERT INTO `#__categories` VALUES (5, 0, 'Business: general', 'Business: general', '', 'com_newsfeeds', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, '');
INSERT INTO `#__categories` VALUES (7, 0, 'Examples', 'Example FAQs', 'key.jpg', '3', 'left', 'Here you will find an example set of FAQs.', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0, 2, '');
INSERT INTO `#__categories` VALUES (9, 0, 'Finance', 'Finance', '', 'com_newsfeeds', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 5, 0, 0, '');
INSERT INTO `#__categories` VALUES (10, 0, 'Linux', 'Linux', '', 'com_newsfeeds', 'left', '<br />\r\n', 1, 0, '0000-00-00 00:00:00', NULL, 6, 0, 0, '');
INSERT INTO `#__categories` VALUES (11, 0, 'Internet', 'Internet', '', 'com_newsfeeds', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 7, 0, 0, '');
INSERT INTO `#__categories` VALUES (12, 0, 'Contacts', 'Contacts', '', 'com_contact_details', 'left', 'Contact Details for this website', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0, 0, '');

INSERT INTO `#__contact_details` VALUES (1, 'Name', 'Position', 'Street', 'Suburb', 'State', 'Country', 'Zip Code', 'Telephone', 'Fax', 'Miscellanous info', 'asterisk.png', 'top', 'email@email.com', 1, 1, 0, '0000-00-00 00:00:00', 1, '', 0, 12, 0);

INSERT INTO `#__content` VALUES (1, 'Welcome to Joomla!', 'Welcome', 'If you''ve read anything at all about Content Management Systems (CMS), you''ll probably know at least three things: CMS are the most exciting way to do business, CMS can be really, I mean <i>really</i>, complicated and lastly Portals are absolutely, outrageously, often <i>unaffordably</i> expensive. <br /><br />{mosimage}Joomla! is set to change all that ... Joomla! is different from the normal models for portal software. For a start, it''s not complicated. Joomla! has been developed for the masses. It''s licensed under the GNU/GPL license, easy to install and administer and reliable. Joomla! doesn''t even require the user or administrator of the system to know HTML to operate it once it''s up and running.', '<h4><font color="#ff6600">Joomla! features:</font></h4>\r\n<ul>\r\n<li>Completely database driven site engines </li>\r\n<li>News, products or services sections fully editable and manageable</li> \r\n<li>Topics sections can be added to by contributing authors </li>\r\n<li>Fully customisable layouts including left, center and right menu boxes </li>\r\n<li>Browser upload of images to your own library for use anywhere in the site </li>\r\n<li>Dynamic Forum/Poll/Voting booth for on-the-spot results </li>\r\n<li>Runs on Linux, FreeBSD, MacOSX server, Solaris and AIX \r\n</li></ul>\r\n<h4>Extensive Administration:</h4>\r\n<ul>\r\n<li>Change order of objects including news, FAQs, articles etc. </li>\r\n<li>Random Newsflash generator </li>\r\n<li>Remote author submission module for News, Articles, FAQs and Links </li>\r\n<li>Object hierarchy - as many sections, departments, divisions and pages as you want </li>\r\n<li>Image library - store all your PNGs, PDFs, DOCs, XLSs, GIFs and JPEGs online for easy use </li>\r\n<li>Automatic Path-Finder. Place a picture and let Joomla! fix the link </li>\r\n<li>News feed manager. Choose from over 360 news feeds from around the world </li>\r\n<li>Archive manager. Put your old articles into cold storage rather than throw them out </li>\r\n<li>Email-a-friend and Print-format for every story and article </li>\r\n<li>In-line Text editor similar to Word Pad </li>\r\n<li>User editable look and feel </li>\r\n<li>Polls/Surveys - Now put a different one on each page </li>\r\n<li>Custom Page Modules. Download custom page modules to spice up your site </li>\r\n<li>Template Manager. Download templates and implement them in seconds </li>\r\n<li>Layout preview. See how it looks before going live </li>\r\n<li>Banner manager. Make money out of your site</li></ul>', 1, 1, 0, 1, '2004-06-12 11:54:06', 62, 'Web Master', '2004-06-12 12:33:27', 62, 0, '0000-00-00 00:00:00', '2004-01-01 00:00:00', '0000-00-00 00:00:00', 'asterisk.png|left|Joomla! Logo|1|Example Caption|bottom|center|120', '', '', 1, 0, 1, '', '', 0, 0);
INSERT INTO `#__content` VALUES (2, 'Newsflash 1', '', 'Joomla! 1.0 - ''Experience the Freedom''!. It has never been easier to create\r\nyour own dynamic site. Manage all your content from the best CMS admin\r\ninterface.', '', 1, 2, 1, 3, '2004-08-09 08:30:34', 62, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '2004-08-09 00:00:00', '0000-00-00 00:00:00', '', '', '', 1, 0, 1, '', '', 0, 0);
INSERT INTO `#__content` VALUES (3, 'Newsflash 2', '', 'Yesterday all servers in the U.S. went out on strike in a bid to get more RAM and better CPUs. A spokes person said that the need for better RAM was due to some fool increasing the front-side bus speed. In future, busses will be told to slow down in residential motherboards.', '', 1, 2, 1, 3, '2004-08-09 08:30:34', 62, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '2004-08-09 00:00:00', '0000-00-00 00:00:00', '', '', '', 1, 0, 2, '', '', 0, 0);
INSERT INTO `#__content` VALUES (4, 'Newsflash 3', '', 'Aoccdrnig to a rscheearch at an Elingsh uinervtisy, it deosn''t mttaer in waht oredr the ltteers in a wrod are, the olny iprmoetnt tihng is taht frist and lsat ltteer is at the rghit pclae. The rset can be a toatl mses and you can sitll raed it wouthit porbelm. Tihs is bcuseae we do not raed ervey lteter by itslef but the wrod as a wlohe.', '', 1, 2, 1, 3, '2004-08-09 08:30:34', 62, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '2004-08-09 00:00:00', '0000-00-00 00:00:00', '', '', '', 1, 0, 3, '', '', 0, 1);
INSERT INTO `#__content` VALUES (5, 'Joomla! License Guidelines', '', '<p>This website is powered by <a href="http://www.joomla.org/">Joomla!</a>  The software and default templates on which it runs are Copyright 2005 <a href="http://www.opensourcematters.org/">Open Source Matters</a>.  All other content and data, including data entered into this website and templates added after installation, are copyrighted by their respective copyright owners.</p><p>If you want to distribute, copy or modify Joomla!, you are welcome to do so under the terms of the <a href="http://www.gnu.org/copyleft/gpl.html#SEC1">GNU General Public License</a>.  If you are unfamiliar with this license, you might want to read <a href="http://www.gnu.org/copyleft/gpl.html#SEC4">''How To Apply These Terms To Your Program''</a> and the <a href="http://www.gnu.org/licenses/gpl-faq.html">''GNU General Public License FAQ''</a>.</p>', '', 1, 0, 0, 0, '2004-08-19 20:11:07', 62, '', '2004-08-19 20:14:49', 62, 0, '0000-00-00 00:00:00', '2004-08-19 00:00:00', '0000-00-00 00:00:00', '', '', 'menu_image=\nitem_title=1\npageclass_sfx=\nback_button=\nrating=\nauthor=\ncreatedate=\nmodifydate=\npdf=\nprint=\nemail=', 1, 0, 11, '', '', 0, 10);
INSERT INTO `#__content` VALUES (6, 'Example News Item 1', 'News1', '{mosimage}Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,\r\nsed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit\r\namet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam\r\nvoluptua. At vero eos et accusam et justo duo dolores et ea rebum.', '<p>{mosimage}Duis autem vel eum iriure dolor in hendrerit in vulputate\r\nvelit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at\r\nvero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum\r\nzzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor\r\nsit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt\r\nut laoreet dolore magna aliquam erat volutpat.</p>\r\n\r\n<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation\r\nullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis\r\nautem vel eum iriure dolor in hendrerit in vulputate velit esse molestie\r\nconsequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan\r\net iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis\r\ndolore te feugait nulla facilisi.</p>\r\n\r\n<p>Nam liber tempor cum soluta nobis eleifend option congue\r\nnihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum\r\ndolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod\r\ntincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim\r\nveniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut\r\naliquip ex ea commodo consequat.</p>\r\n\r\n<p>Duis autem vel eum iriure dolor in hendrerit in vulputate\r\nvelit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. At\r\nvero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd\r\ngubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum\r\ndolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor\r\ninvidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero\r\neos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no\r\nsea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit\r\namet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores\r\nduo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet\r\nclita ea et gubergren, kasd magna no rebum. sanctus sea sed takimata ut vero\r\nvoluptua. est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet,\r\nconsetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore\r\net dolore magna aliquyam erat.</p>\r\n\r\n<p>Consetetur sadipscing elitr, sed diam nonumy eirmod tempor\r\ninvidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero\r\neos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no\r\nsea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit\r\namet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut\r\nlabore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam\r\net justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata\r\nsanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur\r\nsadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore\r\nmagna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo\r\ndolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est\r\nLorem ipsum dolor sit amet.</p>', 1, 1, 0, 1, '2004-07-07 11:54:06', 62, '', '2004-07-07 18:05:05', 62, 0, '0000-00-00 00:00:00', '2004-07-07 00:00:00', '0000-00-00 00:00:00', 'food/coffee.jpg|left||0\r\nfood/bread.jpg|right||0', '', '', 1, 0, 2, '', '', 0, 4);
INSERT INTO `#__content` VALUES (7, 'Example News Item 2', 'News2', '<p>{mosimage}Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,\r\nsed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit\r\namet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam\r\nvoluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem\r\nipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At\r\nvero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', '', 1, 1, 0, 1, '2004-07-07 11:54:06', 62, '', '2004-07-07 18:11:30', 62, 0, '0000-00-00 00:00:00', '2004-07-07 00:00:00', '0000-00-00 00:00:00', 'food/bun.jpg|right||0', '', '', 1, 0, 3, '', '', 0, 2);
INSERT INTO `#__content` VALUES (8, 'Example News Item 3', 'News3', '<p>{mosimage}Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,\r\nsed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit\r\namet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam\r\nvoluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem\r\nipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At\r\nvero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', '', 1, 1, 0, 1, '2004-04-12 11:54:06', 62, '', '2004-07-07 18:08:23', 62, 0, '0000-00-00 00:00:00', '2004-07-07 00:00:00', '0000-00-00 00:00:00', 'fruit/pears.jpg|right||0', '', '', 1, 0, 4, '', '', 0, 1);
INSERT INTO `#__content` VALUES (9, 'Example News Item 4', 'News4', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,\r\nsed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam\r\nvoluptua. At\r\nvero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', '<p>{mosimage}Duis autem vel eum iriure dolor in hendrerit in vulputate\r\nvelit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at\r\nvero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum\r\nzzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor\r\nsit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt\r\nut laoreet dolore magna aliquam erat volutpat.</p>\r\n\r\n{mospagebreak}<p>{mosimage}Ut wisi enim ad minim veniam, quis nostrud exerci tation\r\nullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis\r\nautem vel eum iriure dolor in hendrerit in vulputate velit esse molestie\r\nconsequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan\r\net iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis\r\ndolore te feugait nulla facilisi.</p>\r\n\r\n<p>{mosimage}Nam liber tempor cum soluta nobis eleifend option congue\r\nnihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum\r\ndolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod\r\ntincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim\r\nveniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut\r\naliquip ex ea commodo consequat.</p>\r\n\r\n<p>Duis autem vel eum iriure dolor in hendrerit in vulputate\r\nvelit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. At\r\nvero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd\r\ngubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum\r\ndolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor\r\ninvidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero\r\neos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no\r\nsea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit\r\namet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores\r\nduo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet\r\nclita ea et gubergren, kasd magna no rebum. sanctus sea sed takimata ut vero\r\nvoluptua. est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet,\r\nconsetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore\r\net dolore magna aliquyam erat.</p>\r\n\r\n{mospagebreak}<p>Consetetur sadipscing elitr, sed diam nonumy eirmod tempor\r\ninvidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero\r\neos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no\r\nsea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit\r\namet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut\r\nlabore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam\r\net justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata\r\nsanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur\r\nsadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore\r\nmagna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo\r\ndolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est\r\nLorem ipsum dolor sit amet.</p>', 1, 1, 0, 1, '2004-07-07 11:54:06', 62, '', '2004-07-07 18:10:23', 62, 0, '0000-00-00 00:00:00', '2004-07-07 00:00:00', '0000-00-00 00:00:00', 'fruit/strawberry.jpg|left||0\r\nfruit/pears.jpg|right||0\r\nfruit/cherry.jpg|left||0', '', '', 1, 0, 5, '', '', 0, 6);
INSERT INTO `#__content` VALUES (10, 'Example FAQ Item 1', 'FAQ1', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,\r\nsed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam\r\nvoluptua. At\r\nvero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', '', 1, 3, 0, 7, '2004-05-12 11:54:06', 62, '', '2004-07-07 18:10:23', 62, 0, '0000-00-00 00:00:00', '2004-01-01 00:00:00', '0000-00-00 00:00:00', '', '', '', 1, 0, 5, '', '', 0, 8);
INSERT INTO `#__content` VALUES (11, 'Example FAQ Item 2', 'FAQ2', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,\r\nsed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam\r\nvoluptua. At\r\nvero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>', '<p>{mosimage}Duis autem vel eum iriure dolor in hendrerit in vulputate\r\nvelit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at\r\nvero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum\r\nzzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor\r\nsit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt\r\nut laoreet dolore magna aliquam erat volutpat.</p>\r\n\r\n<p>{mosimage}Ut wisi enim ad minim veniam, quis nostrud exerci tation\r\nullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis\r\nautem vel eum iriure dolor in hendrerit in vulputate velit esse molestie\r\nconsequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan\r\net iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis\r\ndolore te feugait nulla facilisi.</p>\r\n\r\n<p>{mosimage}Nam liber tempor cum soluta nobis eleifend option congue\r\nnihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum\r\ndolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod\r\ntincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim\r\nveniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut\r\naliquip ex ea commodo consequat.</p>\r\n\r\n<p>Duis autem vel eum iriure dolor in hendrerit in vulputate\r\nvelit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. At\r\nvero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd\r\ngubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum\r\ndolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor\r\ninvidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero\r\neos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no\r\nsea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit\r\namet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores\r\nduo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet\r\nclita ea et gubergren, kasd magna no rebum. sanctus sea sed takimata ut vero\r\nvoluptua. est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet,\r\nconsetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore\r\net dolore magna aliquyam erat.</p>\r\n\r\n<p>Consetetur sadipscing elitr, sed diam nonumy eirmod tempor\r\ninvidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero\r\neos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no\r\nsea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit\r\namet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut\r\nlabore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam\r\net justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata\r\nsanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur\r\nsadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore\r\nmagna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo\r\ndolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est\r\nLorem ipsum dolor sit amet.</p>', 1, 3, 0, 7, '2004-05-12 11:54:06', 62, 'Web master', '2004-07-07 18:10:23', 62, 0, '0000-00-00 00:00:00', '2004-01-01 00:00:00', '0000-00-00 00:00:00', 'fruit/cherry.jpg|left||0\r\nfruit/peas.jpg|right||0\r\nfood/milk.jpg|left||0', '', '', 1, 0, 5, '', '', 0, 10);

INSERT INTO `#__content_frontpage` VALUES (1, 1);
INSERT INTO `#__content_frontpage` VALUES (2, 2);
INSERT INTO `#__content_frontpage` VALUES (3, 3);
INSERT INTO `#__content_frontpage` VALUES (4, 4);
INSERT INTO `#__content_frontpage` VALUES (5, 5);

INSERT INTO `#__menu` VALUES (2, 'mainmenu', 'News', 'index.php?option=com_content&task=section&id=1', 'content_section', 1, 0, 1, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (3, 'mainmenu', 'Contact Us', 'index.php?option=com_contact', 'components', 1, 0, 7, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (23, 'mainmenu', 'Links', 'index.php?option=com_weblinks', 'components', 1, 0, 4, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=web_links.jpg\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nheadings=1\nhits=\nitem_description=1\nother_cat=1\ndescription=1\ndescription_text=\nimage=-1\nimage_align=right\nweblink_icons=');
INSERT INTO `#__menu` VALUES (5, 'mainmenu', 'Search', 'index.php?option=com_search', 'components', 1, 0, 16, 0, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (6, 'mainmenu', 'Joomla! License', 'index.php?option=com_content&task=view&id=5', 'content_typed', 1, 0, 5, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '');
INSERT INTO `#__menu` VALUES (7, 'mainmenu', 'News Feeds', 'index.php?option=com_newsfeeds', 'components', 1, 0, 12, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader=');
INSERT INTO `#__menu` VALUES (8, 'mainmenu', 'Wrapper', 'index.php?option=com_wrapper', 'wrapper', 1, 0, 0, 0, 10, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1\npageclass_sfx=\nback_button=\npage_title=1\nheader=\nscrolling=auto\nwidth=100%\nheight=600\nheight_auto=0\nurl=www.joomla.org');
INSERT INTO `#__menu` VALUES (9, 'mainmenu', 'Blog', 'index.php?option=com_content&task=blogsection&id=0', 'content_blog_section', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1\npageclass_sfx=\nback_button=\nheader=A blog of all sections with no images\npage_title=1\nleading=0\nintro=6\ncolumns=2\nlink=4\norderby_pri=\norderby_sec=\npagination=2\npagination_results=1\nimage=0\ndescription=0\ndescription_image=0\ncategory=0\ncategory_link=0\nitem_title=1\nlink_titles=\nreadmore=\nrating=\nauthor=\ncreatedate=\nmodifydate=\npdf=\nprint=\nemail=\nsectionid=');
INSERT INTO `#__menu` VALUES (10, 'othermenu', 'Joomla! Home', 'http://www.joomla.org', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (11, 'othermenu', 'Joomla! Forums', 'http://forum.joomla.org', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (12, 'othermenu', 'OSM Home', 'http://www.opensourcematters.org', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (24, 'othermenu', 'Administrator', 'administrator/', 'url', 1, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1');
INSERT INTO `#__menu` VALUES (21, 'usermenu', 'Your Details', 'index.php?option=com_user&task=UserDetails', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 1, 3, '');
INSERT INTO `#__menu` VALUES (13, 'usermenu', 'Submit News', 'index.php?option=com_content&task=new&sectionid=1&Itemid=0', 'url', 1, 0, 0, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, '');
INSERT INTO `#__menu` VALUES (14, 'usermenu', 'Submit WebLink', 'index.php?option=com_weblinks&task=new', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, '');
INSERT INTO `#__menu` VALUES (15, 'usermenu', 'Check-In My Items', 'index.php?option=com_user&task=CheckIn', 'url', 1, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 1, 2, '');
INSERT INTO `#__menu` VALUES (16, 'usermenu', 'Logout', 'index.php?option=com_login', 'components', 1, 0, 15, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 1, 3, '');
INSERT INTO `#__menu` VALUES (17, 'topmenu', 'Home', 'index.php', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (18, 'topmenu', 'Contact Us', 'index.php?option=com_contact&Itemid=3', 'url', 1, 0, 0, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (19, 'topmenu', 'News', 'index.php?option=com_content&task=section&id=1&Itemid=2', 'url', 1, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '');
INSERT INTO `#__menu` VALUES (20, 'topmenu', 'Links', 'index.php?option=com_weblinks&Itemid=23', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1');
INSERT INTO `#__menu` VALUES (25, 'mainmenu', 'FAQs', 'index.php?option=com_content&task=category&sectionid=3&id=7', 'content_category', 1, 0, 7, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\npage_title=1\npageclass_sfx=\nback_button=\norderby=\ndate_format=\ndate=\nauthor=\ntitle=1\nhits=\nheadings=1\nnavigation=1\norder_select=1\ndisplay=1\ndisplay_num=50\nfilter=1\nfilter_type=title\nother_cat=1\nempty_cat=0\ncat_items=1\ncat_description=1');

INSERT INTO `#__newsfeeds` VALUES (4, 1, 'Joomla! - Official News', 'http://www.joomla.org/index.php?option=com_rss_xtd&feed=RSS2.0&type=com_frontpage&Itemid=1', '', 1, 5, 3600, 0, '0000-00-00 00:00:00', 8);
INSERT INTO `#__newsfeeds` VALUES (4, 2, 'Joomla! - Community News', 'http://www.joomla.org/index.php?option=com_rss_xtd&feed=RSS2.0&type=com_content&task=blogcategory&id=0&Itemid=33', '', 1, 5, 3600, 0, '0000-00-00 00:00:00', 9);
INSERT INTO `#__newsfeeds` VALUES (10, 4, 'Linux Today', 'http://linuxtoday.com/backend/my-netscape.rdf', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 1);
INSERT INTO `#__newsfeeds` VALUES (5, 5, 'Business News', 'http://headlines.internet.com/internetnews/bus-news/news.rss', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 2);
INSERT INTO `#__newsfeeds` VALUES (11, 6, 'Web Developer News', 'http://headlines.internet.com/internetnews/wd-news/news.rss', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 3);
INSERT INTO `#__newsfeeds` VALUES (10, 7, 'Linux Central:New Products', 'http://linuxcentral.com/backend/lcnew.rdf', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 4);
INSERT INTO `#__newsfeeds` VALUES (10, 8, 'Linux Central:Best Selling', 'http://linuxcentral.com/backend/lcbestns.rdf', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 5);
INSERT INTO `#__newsfeeds` VALUES (10, 9, 'Linux Central:Daily Specials', 'http://linuxcentral.com/backend/lcspecialns.rdf', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 6);
INSERT INTO `#__newsfeeds` VALUES (9, 10, 'Internet:Finance News', 'http://headlines.internet.com/internetnews/fina-news/news.rss', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 7);

INSERT INTO `#__poll_data` VALUES (1, 14, 'Absolutely simple', 1);
INSERT INTO `#__poll_data` VALUES (2, 14, 'Reasonably easy', 0);
INSERT INTO `#__poll_data` VALUES (3, 14, 'Not straight-forward but I worked it out', 0);
INSERT INTO `#__poll_data` VALUES (4, 14, 'I had to install extra server stuff', 0);
INSERT INTO `#__poll_data` VALUES (5, 14, 'I had no idea and got my friend to do it', 0);
INSERT INTO `#__poll_data` VALUES (6, 14, 'My dog ran away with the README ...', 0);
INSERT INTO `#__poll_data` VALUES (7, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (8, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (9, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (10, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (11, 14, '', 0);
INSERT INTO `#__poll_data` VALUES (12, 14, '', 0);

INSERT INTO `#__polls` VALUES (14, 'This Joomla! installation was ....', 1, 0, '0000-00-00 00:00:00', 1, 0, 86400);

INSERT INTO `#__poll_menu` VALUES (14, 1);

INSERT INTO `#__sections` VALUES (1, 'News', 'The News', 'articles.jpg', 'content', 'right', 'Select a news topic from the list below, then select a news article to read.', 1, 0, '0000-00-00 00:00:00', 1, 0, 1, '');
INSERT INTO `#__sections` VALUES (2, 'Newsflashes', 'Newsflashes', '', 'content', 'left', '', 1, 0, '0000-00-00 00:00:00', 2, 0, 1, '');
INSERT INTO `#__sections` VALUES (3, 'FAQs', 'Frequently Asked Questions', 'pastarchives.jpg', 'content', 'left', 'From the list below choose one of our FAQs topics, then select an FAQ to read. If you have a question which is not in this section, please contact us.', 1, 0, '0000-00-00 00:00:00', 2, 0, 1, '');

INSERT INTO `#__weblinks` VALUES (1, 2, 0, 'Joomla!', 'http://www.joomla.org', 'Home of Joomla!', '2005-02-14 15:19:02', 2, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0');
INSERT INTO `#__weblinks` VALUES (2, 2, 0, 'php.net', 'http://www.php.net', 'The language that Joomla! is developed in', '2004-07-07 11:33:24', 0, 1, 0, '0000-00-00 00:00:00', 3, 0, 1, '');
INSERT INTO `#__weblinks` VALUES (3, 2, 0, 'MySQL', 'http://www.mysql.com', 'The database that Joomla! uses', '2004-07-07 10:18:31', 0, 1, 0, '0000-00-00 00:00:00', 5, 0, 1, '');
INSERT INTO `#__weblinks` VALUES (4, 2, 0, 'OpenSourceMatters', 'http://www.opensourcematters.org', 'Home of OSM', '2005-02-14 15:19:02', 2, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0');
INSERT INTO `#__weblinks` VALUES (5, 2, 0, 'Joomla! - Forums', 'http://forum.joomla.org', 'Joomla! Forums', '2005-02-14 15:19:02', 2, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0');
