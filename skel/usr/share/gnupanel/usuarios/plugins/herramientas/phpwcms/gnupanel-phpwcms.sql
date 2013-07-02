




CREATE TABLE `phpwcms_address` (
  `address_id` int(11) NOT NULL auto_increment,
  `address_key` varchar(255) NOT NULL default '',
  `address_email` text NOT NULL,
  `address_name` text NOT NULL,
  `address_verified` int(1) NOT NULL default '0',
  `address_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `address_subscription` blob NOT NULL,
  `address_iddetail` int(11) NOT NULL default '0',
  `address_url1` varchar(255) NOT NULL default '',
  `address_url2` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`address_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_cid` int(11) NOT NULL default '0',
  `article_tid` int(11) NOT NULL default '0',
  `article_uid` int(11) NOT NULL default '0',
  `article_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `article_username` varchar(125) NOT NULL default '',
  `article_title` text NOT NULL,
  `article_keyword` text NOT NULL,
  `article_public` int(1) NOT NULL default '0',
  `article_deleted` int(1) NOT NULL default '0',
  `article_begin` datetime NOT NULL default '0000-00-00 00:00:00',
  `article_end` datetime NOT NULL default '0000-00-00 00:00:00',
  `article_aktiv` int(1) NOT NULL default '0',
  `article_subtitle` text NOT NULL,
  `article_summary` text NOT NULL,
  `article_redirect` text NOT NULL,
  `article_sort` int(11) NOT NULL default '0',
  `article_notitle` int(1) NOT NULL default '0',
  `article_hidesummary` int(1) NOT NULL default '0',
  `article_image` blob NOT NULL,
  `article_created` varchar(14) NOT NULL default '',
  `article_cache` varchar(10) NOT NULL default '0',
  `article_nosearch` char(1) NOT NULL default '0',
  `article_nositemap` int(1) NOT NULL default '0',
  `article_aliasid` int(11) NOT NULL default '0',
  `article_headerdata` int(1) NOT NULL default '0',
  `article_morelink` int(1) NOT NULL default '1',
  `article_pagetitle` varchar(255) NOT NULL default '',
  `article_paginate` int(1) NOT NULL default '0',
  `article_serialized` blob NOT NULL,
  PRIMARY KEY  (`article_id`),
  KEY `article_aktiv` (`article_aktiv`),
  KEY `article_public` (`article_public`),
  KEY `article_deleted` (`article_deleted`),
  KEY `article_nosearch` (`article_nosearch`),
  KEY `article_begin` (`article_begin`),
  KEY `article_end` (`article_end`),
  KEY `article_cid` (`article_cid`),
  KEY `article_tstamp` (`article_tstamp`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_articlecat` (
  `acat_id` int(11) NOT NULL auto_increment,
  `acat_name` text NOT NULL,
  `acat_info` text NOT NULL,
  `acat_public` int(1) NOT NULL default '0',
  `acat_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `acat_aktiv` int(1) NOT NULL default '0',
  `acat_uid` int(11) NOT NULL default '0',
  `acat_trash` int(1) NOT NULL default '0',
  `acat_struct` int(11) NOT NULL default '0',
  `acat_sort` int(11) NOT NULL default '0',
  `acat_alias` text NOT NULL,
  `acat_hidden` int(1) NOT NULL default '0',
  `acat_template` int(11) NOT NULL default '0',
  `acat_ssl` int(1) NOT NULL default '0',
  `acat_regonly` int(1) NOT NULL default '0',
  `acat_topcount` int(11) NOT NULL default '0',
  `acat_redirect` text NOT NULL,
  `acat_order` int(2) NOT NULL default '0',
  `acat_cache` varchar(10) NOT NULL default '',
  `acat_nosearch` char(1) NOT NULL default '',
  `acat_nositemap` int(1) NOT NULL default '0',
  `acat_permit` text NOT NULL,
  `acat_maxlist` int(11) NOT NULL default '0',
  `acat_cntpart` varchar(255) NOT NULL default '',
  `acat_pagetitle` varchar(255) NOT NULL default '',
  `acat_paginate` int(1) NOT NULL default '0',
  `acat_overwrite` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`acat_id`),
  KEY `acat_struct` (`acat_struct`),
  KEY `acat_sort` (`acat_sort`),
  FULLTEXT KEY `acat_alias` (`acat_alias`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_articlecontent` (
  `acontent_id` int(11) NOT NULL auto_increment,
  `acontent_aid` int(11) NOT NULL default '0',
  `acontent_uid` int(11) NOT NULL default '0',
  `acontent_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `acontent_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `acontent_title` text NOT NULL,
  `acontent_text` text NOT NULL,
  `acontent_type` int(10) NOT NULL default '0',
  `acontent_sorting` int(11) NOT NULL default '0',
  `acontent_image` text NOT NULL,
  `acontent_files` text NOT NULL,
  `acontent_visible` int(1) NOT NULL default '0',
  `acontent_subtitle` text NOT NULL,
  `acontent_before` varchar(10) NOT NULL default '',
  `acontent_after` varchar(10) NOT NULL default '',
  `acontent_top` int(1) NOT NULL default '0',
  `acontent_redirect` text NOT NULL,
  `acontent_html` text NOT NULL,
  `acontent_trash` int(1) NOT NULL default '0',
  `acontent_alink` text NOT NULL,
  `acontent_media` text NOT NULL,
  `acontent_form` mediumtext NOT NULL,
  `acontent_newsletter` mediumtext NOT NULL,
  `acontent_block` varchar(200) NOT NULL default '0',
  `acontent_anchor` int(1) NOT NULL default '0',
  `acontent_template` varchar(255) NOT NULL default '',
  `acontent_spacer` int(1) NOT NULL default '0',
  `acontent_tid` int(11) NOT NULL default '0',
  `acontent_livedate` datetime NOT NULL default '0000-00-00 00:00:00',
  `acontent_killdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `acontent_module` varchar(255) NOT NULL default '',
  `acontent_comment` text NOT NULL,
  `acontent_paginate_page` int(5) NOT NULL default '0',
  `acontent_paginate_title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`acontent_id`),
  KEY `acontent_aid` (`acontent_aid`),
  KEY `acontent_sorting` (`acontent_sorting`),
  KEY `acontent_type` (`acontent_type`),
  KEY `acontent_livedate` (`acontent_livedate`,`acontent_killdate`),
  KEY `acontent_paginate` (`acontent_paginate_page`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_bad_behavior` (
  `id` int(11) NOT NULL auto_increment,
  `ip` text NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `request_method` text NOT NULL,
  `request_uri` text NOT NULL,
  `server_protocol` text NOT NULL,
  `http_headers` text NOT NULL,
  `user_agent` text NOT NULL,
  `request_entity` text NOT NULL,
  `key` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ip` (`ip`(15)),
  KEY `user_agent` (`user_agent`(10))
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_bid` (
  `bid_id` int(11) NOT NULL auto_increment,
  `bid_cid` int(11) NOT NULL default '0',
  `bid_email` text NOT NULL,
  `bid_hash` varchar(255) NOT NULL default '',
  `bid_amount` float NOT NULL default '0',
  `bid_created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `bid_verified` int(1) NOT NULL default '0',
  `bid_trashed` int(1) NOT NULL default '0',
  `bid_vars` mediumblob NOT NULL,
  PRIMARY KEY  (`bid_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_blog` (
  `blog_id` int(11) NOT NULL auto_increment,
  `blog_cid` int(11) NOT NULL default '0',
  `blog_created` varchar(14) NOT NULL default '',
  `blog_changed` varchar(14) NOT NULL default '',
  `blog_editor` varchar(255) NOT NULL default '',
  `blog_var` mediumtext NOT NULL,
  `blog_active` int(1) NOT NULL default '0',
  `blog_trashed` int(1) NOT NULL default '0',
  PRIMARY KEY  (`blog_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_cache` (
  `cache_id` int(11) NOT NULL auto_increment,
  `cache_hash` varchar(50) NOT NULL default '',
  `cache_uri` text NOT NULL,
  `cache_cid` int(11) NOT NULL default '0',
  `cache_aid` int(11) NOT NULL default '0',
  `cache_timeout` varchar(20) NOT NULL default '0',
  `cache_isprint` int(1) NOT NULL default '0',
  `cache_changed` int(14) default NULL,
  `cache_use` int(1) NOT NULL default '0',
  `cache_searchable` int(1) NOT NULL default '0',
  `cache_page` longtext NOT NULL,
  `cache_stripped` longtext NOT NULL,
  PRIMARY KEY  (`cache_id`),
  KEY `cache_hash` (`cache_hash`),
  FULLTEXT KEY `cache_stripped` (`cache_stripped`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_chat` (
  `chat_id` int(11) NOT NULL auto_increment,
  `chat_uid` int(11) NOT NULL default '0',
  `chat_name` varchar(30) NOT NULL default '',
  `chat_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `chat_text` varchar(255) NOT NULL default '',
  `chat_cat` int(5) NOT NULL default '0',
  PRIMARY KEY  (`chat_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_country` (
  `country_id` int(4) NOT NULL auto_increment,
  `country_iso` char(2) NOT NULL default '',
  `country_name` varchar(100) NOT NULL default '',
  `country_name_de` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`country_id`),
  UNIQUE KEY `country_iso` (`country_iso`),
  UNIQUE KEY `country_name` (`country_name`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_crossreference` (
  `cref_id` int(11) NOT NULL auto_increment,
  `cref_type` int(11) NOT NULL default '0',
  `cref_rid` int(11) NOT NULL default '0',
  `cref_int` int(11) NOT NULL default '0',
  `cref_str` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cref_id`),
  KEY `cref_type` (`cref_type`,`cref_rid`,`cref_int`,`cref_str`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_file` (
  `f_id` int(11) NOT NULL auto_increment,
  `f_pid` int(11) NOT NULL default '0',
  `f_uid` int(11) NOT NULL default '0',
  `f_kid` int(2) NOT NULL default '0',
  `f_order` int(11) NOT NULL default '0',
  `f_trash` int(1) NOT NULL default '0',
  `f_aktiv` int(1) NOT NULL default '0',
  `f_public` int(1) NOT NULL default '0',
  `f_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `f_name` varchar(255) NOT NULL default '',
  `f_cat` varchar(200) NOT NULL default '',
  `f_created` varchar(10) NOT NULL default '',
  `f_changed` longblob NOT NULL,
  `f_size` varchar(15) NOT NULL default '',
  `f_type` varchar(200) NOT NULL default '',
  `f_ext` varchar(50) NOT NULL default '',
  `f_shortinfo` varchar(255) NOT NULL default '',
  `f_longinfo` blob NOT NULL,
  `f_log` longblob NOT NULL,
  `f_thumb_list` varchar(255) NOT NULL default '',
  `f_thumb_preview` varchar(255) NOT NULL default '',
  `f_keywords` varchar(255) NOT NULL default '',
  `f_hash` varchar(50) NOT NULL default '',
  `f_dlstart` int(11) NOT NULL default '0',
  `f_dlfinal` int(11) NOT NULL default '0',
  `f_refid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`f_id`),
  FULLTEXT KEY `f_name` (`f_name`),
  FULLTEXT KEY `f_shortinfo` (`f_shortinfo`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_filecat` (
  `fcat_id` int(11) NOT NULL auto_increment,
  `fcat_name` varchar(255) NOT NULL default '',
  `fcat_aktiv` int(1) NOT NULL default '0',
  `fcat_deleted` int(1) NOT NULL default '0',
  `fcat_needed` int(1) NOT NULL default '0',
  PRIMARY KEY  (`fcat_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_filekey` (
  `fkey_id` int(11) NOT NULL auto_increment,
  `fkey_cid` int(11) NOT NULL default '0',
  `fkey_name` varchar(255) NOT NULL default '',
  `fkey_aktiv` int(1) NOT NULL default '0',
  `fkey_deleted` int(1) NOT NULL default '0',
  PRIMARY KEY  (`fkey_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_fonts` (
  `font_id` int(11) NOT NULL auto_increment,
  `font_name` text NOT NULL,
  `font_shortname` text NOT NULL,
  `font_filename` text NOT NULL,
  PRIMARY KEY  (`font_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_fonts_colors` (
  `color_id` int(11) NOT NULL auto_increment,
  `color_name` text NOT NULL,
  `color_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`color_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_fonts_styles` (
  `style_id` int(11) NOT NULL auto_increment,
  `style_name` text NOT NULL,
  `style_info` text NOT NULL,
  PRIMARY KEY  (`style_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_formresult` (
  `formresult_id` int(11) NOT NULL auto_increment,
  `formresult_pid` int(11) NOT NULL default '0',
  `formresult_createdate` timestamp NULL default CURRENT_TIMESTAMP,
  `formresult_ip` varchar(50) NOT NULL default '',
  `formresult_content` mediumblob NOT NULL,
  PRIMARY KEY  (`formresult_id`),
  KEY `formresult_pid` (`formresult_pid`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_formtracking` (
  `formtracking_id` int(11) NOT NULL auto_increment,
  `formtracking_hash` varchar(50) NOT NULL default '',
  `formtracking_ip` varchar(20) NOT NULL default '',
  `formtracking_created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `formtracking_sentdate` varchar(20) NOT NULL default '',
  `formtracking_sent` int(1) NOT NULL default '0',
  PRIMARY KEY  (`formtracking_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_forum` (
  `forum_id` int(11) NOT NULL auto_increment,
  `forum_entry` tinyint(1) NOT NULL default '0',
  `forum_cid` int(11) NOT NULL default '0',
  `forum_pid` int(11) NOT NULL default '0',
  `forum_uid` int(11) NOT NULL default '0',
  `forum_ctopic` int(11) NOT NULL default '0',
  `forum_cpost` int(11) NOT NULL default '0',
  `forum_title` text NOT NULL,
  `forum_created` int(10) NOT NULL default '0',
  `forum_changed` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `forum_status` int(1) NOT NULL default '0',
  `forum_deleted` int(1) NOT NULL default '0',
  `forum_text` mediumtext NOT NULL,
  `forum_var` blob NOT NULL,
  `forum_lastpost` mediumtext NOT NULL,
  PRIMARY KEY  (`forum_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_guestbook` (
  `guestbook_id` int(11) NOT NULL auto_increment,
  `guestbook_cid` int(11) NOT NULL default '0',
  `guestbook_msg` text NOT NULL,
  `guestbook_name` text NOT NULL,
  `guestbook_email` text NOT NULL,
  `guestbook_created` varchar(14) NOT NULL default '',
  `guestbook_trashed` int(1) NOT NULL default '0',
  `guestbook_url` text NOT NULL,
  `guestbook_show` int(1) NOT NULL default '0',
  `guestbook_ip` varchar(20) NOT NULL default '',
  `guestbook_useragent` varchar(255) NOT NULL default '',
  `guestbook_image` varchar(255) NOT NULL default '',
  `guestbook_imagename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`guestbook_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_imgcache` (
  `imgcache_id` int(11) NOT NULL auto_increment,
  `imgcache_hash` varchar(50) NOT NULL default '',
  `imgcache_imgname` varchar(255) NOT NULL default '',
  `imgcache_width` int(11) NOT NULL default '0',
  `imgcache_height` int(11) NOT NULL default '0',
  `imgcache_wh` varchar(255) NOT NULL default '',
  `imgcache_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `imgcache_trash` int(1) NOT NULL default '0',
  PRIMARY KEY  (`imgcache_id`),
  KEY `imgcache_hash` (`imgcache_hash`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_keyword` (
  `keyword_id` int(11) NOT NULL auto_increment,
  `keyword_name` varchar(255) NOT NULL default '',
  `keyword_created` varchar(14) NOT NULL default '',
  `keyword_trash` int(1) NOT NULL default '0',
  `keyword_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `keyword_description` text NOT NULL,
  `keyword_link` varchar(255) NOT NULL default '',
  `keyword_sort` int(11) NOT NULL default '0',
  `keyword_important` int(1) NOT NULL default '0',
  `keyword_abbr` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`keyword_id`),
  KEY `keyword_abbr` (`keyword_abbr`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_language` (
  `lang_id` varchar(255) NOT NULL default '',
  `lang_html` int(1) NOT NULL default '1',
  `lang_type` int(1) NOT NULL default '0',
  `EN` text NOT NULL,
  `DE` text NOT NULL,
  `BG` text NOT NULL,
  `CA` text NOT NULL,
  `CZ` text NOT NULL,
  `DA` text NOT NULL,
  `EE` text NOT NULL,
  `ES` text NOT NULL,
  `FI` text NOT NULL,
  `FR` text NOT NULL,
  `GR` text NOT NULL,
  `HU` text NOT NULL,
  `IT` text NOT NULL,
  `LT` text NOT NULL,
  `NL` text NOT NULL,
  `NO` text NOT NULL,
  `PL` text NOT NULL,
  `PT` text NOT NULL,
  `RO` text NOT NULL,
  `SE` text NOT NULL,
  `SK` text NOT NULL,
  `VN` text NOT NULL,
  PRIMARY KEY  (`lang_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_log` (
  `log_id` int(11) NOT NULL auto_increment,
  `log_ts` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `log_ip` varchar(24) NOT NULL default '',
  `log_aktion` tinyint(4) NOT NULL default '0',
  `log_ref` varchar(250) NOT NULL default '',
  `log_browser` varchar(100) NOT NULL default '',
  `log_detail` tinyblob NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_map` (
  `map_id` int(11) NOT NULL auto_increment,
  `map_cid` int(11) NOT NULL default '0',
  `map_x` int(5) NOT NULL default '0',
  `map_y` int(5) NOT NULL default '0',
  `map_title` text NOT NULL,
  `map_zip` varchar(255) NOT NULL default '',
  `map_city` text NOT NULL,
  `map_deleted` int(1) NOT NULL default '0',
  `map_entry` text NOT NULL,
  `map_vars` text NOT NULL,
  PRIMARY KEY  (`map_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_message` (
  `msg_id` int(11) NOT NULL auto_increment,
  `msg_pid` int(11) NOT NULL default '0',
  `msg_uid` int(11) NOT NULL default '0',
  `msg_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `msg_subject` varchar(150) NOT NULL default '',
  `msg_text` blob NOT NULL,
  `msg_deleted` tinyint(1) NOT NULL default '0',
  `msg_read` tinyint(1) NOT NULL default '0',
  `msg_to` blob NOT NULL,
  `msg_from` int(11) NOT NULL default '0',
  `msg_from_del` int(1) NOT NULL default '0',
  PRIMARY KEY  (`msg_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_module` (
  `module_id` int(11) NOT NULL auto_increment,
  `module_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `module_name` varchar(30) NOT NULL default '',
  `module_mode` tinyint(1) NOT NULL default '0',
  `module_title` text NOT NULL,
  `module_description` text NOT NULL,
  PRIMARY KEY  (`module_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_newsletter` (
  `newsletter_id` int(11) NOT NULL auto_increment,
  `newsletter_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `newsletter_lastsending` timestamp NOT NULL default '0000-00-00 00:00:00',
  `newsletter_subject` text NOT NULL,
  `newsletter_changed` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `newsletter_vars` mediumblob NOT NULL,
  `newsletter_trashed` int(1) NOT NULL default '0',
  `newsletter_active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`newsletter_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_newsletterqueue` (
  `queue_id` int(11) NOT NULL auto_increment,
  `queue_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `queue_changed` timestamp NOT NULL default '0000-00-00 00:00:00',
  `queue_status` int(11) NOT NULL default '0',
  `queue_pid` int(11) NOT NULL default '0',
  `queue_rid` int(11) NOT NULL default '0',
  `queue_errormsg` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`queue_id`),
  KEY `nlqueue` (`queue_pid`,`queue_status`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_pagelayout` (
  `pagelayout_id` int(11) NOT NULL auto_increment,
  `pagelayout_name` varchar(255) NOT NULL default '',
  `pagelayout_default` int(1) NOT NULL default '0',
  `pagelayout_var` mediumblob NOT NULL,
  `pagelayout_trash` int(1) NOT NULL default '0',
  PRIMARY KEY  (`pagelayout_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_profession` (
  `prof_id` int(4) NOT NULL auto_increment,
  `prof_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`prof_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_subscription` (
  `subscription_id` int(11) NOT NULL auto_increment,
  `subscription_name` text NOT NULL,
  `subscription_info` blob NOT NULL,
  `subscription_active` int(1) NOT NULL default '0',
  `subscription_lang` varchar(100) NOT NULL default '',
  `subscription_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`subscription_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_sysvalue` (
  `sysvalue_key` varchar(255) NOT NULL default '',
  `sysvalue_tstamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `sysvalue_type` varchar(255) NOT NULL default '',
  `sysvalue_value` mediumblob NOT NULL,
  `sysvalue_status` int(1) NOT NULL default '0',
  PRIMARY KEY  (`sysvalue_key`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_template` (
  `template_id` int(11) NOT NULL auto_increment,
  `template_type` int(11) NOT NULL default '1',
  `template_name` varchar(255) NOT NULL default '',
  `template_default` int(1) NOT NULL default '0',
  `template_var` mediumblob NOT NULL,
  `template_trash` int(1) NOT NULL default '0',
  PRIMARY KEY  (`template_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_user` (
  `usr_id` int(11) NOT NULL auto_increment,
  `usr_login` varchar(30) NOT NULL default '',
  `usr_pass` varchar(255) NOT NULL default '',
  `usr_email` varchar(150) NOT NULL default '',
  `usr_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `usr_rechte` tinyint(4) NOT NULL default '0',
  `usr_admin` tinyint(1) NOT NULL default '0',
  `usr_avatar` varchar(50) NOT NULL default '',
  `usr_aktiv` int(1) NOT NULL default '0',
  `usr_name` varchar(100) NOT NULL default '',
  `usr_var_structure` blob NOT NULL,
  `usr_var_publicfile` blob NOT NULL,
  `usr_var_privatefile` blob NOT NULL,
  `usr_lang` varchar(50) NOT NULL default '',
  `usr_wysiwyg` int(2) NOT NULL default '0',
  `usr_fe` int(1) NOT NULL default '0',
  `usr_vars` mediumtext NOT NULL,
  PRIMARY KEY  (`usr_id`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_userdetail` (
  `detail_id` int(11) NOT NULL auto_increment,
  `detail_pid` int(11) NOT NULL default '0',
  `detail_formid` int(11) NOT NULL default '0',
  `detail_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `detail_title` varchar(255) NOT NULL default '',
  `detail_firstname` varchar(255) NOT NULL default '',
  `detail_lastname` varchar(255) NOT NULL default '',
  `detail_company` varchar(255) NOT NULL default '',
  `detail_street` varchar(255) NOT NULL default '',
  `detail_add` varchar(255) NOT NULL default '',
  `detail_city` varchar(255) NOT NULL default '',
  `detail_zip` varchar(255) NOT NULL default '',
  `detail_region` varchar(255) NOT NULL default '',
  `detail_country` varchar(255) NOT NULL default '',
  `detail_fon` varchar(255) NOT NULL default '',
  `detail_fax` varchar(255) NOT NULL default '',
  `detail_mobile` varchar(255) NOT NULL default '',
  `detail_signature` varchar(255) NOT NULL default '',
  `detail_prof` varchar(255) NOT NULL default '',
  `detail_notes` blob NOT NULL,
  `detail_public` int(1) NOT NULL default '1',
  `detail_aktiv` int(1) NOT NULL default '1',
  `detail_newsletter` int(11) NOT NULL default '0',
  `detail_website` varchar(255) NOT NULL default '',
  `detail_userimage` varchar(255) NOT NULL default '',
  `detail_gender` varchar(255) NOT NULL default '',
  `detail_birthday` date NOT NULL default '0000-00-00',
  `detail_varchar1` varchar(255) NOT NULL default '',
  `detail_varchar2` varchar(255) NOT NULL default '',
  `detail_varchar3` varchar(255) NOT NULL default '',
  `detail_varchar4` varchar(255) NOT NULL default '',
  `detail_varchar5` varchar(255) NOT NULL default '',
  `detail_text1` text NOT NULL,
  `detail_text2` text NOT NULL,
  `detail_text3` text NOT NULL,
  `detail_text4` text NOT NULL,
  `detail_text5` text NOT NULL,
  `detail_email` varchar(255) NOT NULL default '',
  `detail_login` varchar(255) NOT NULL default '',
  `detail_password` varchar(255) NOT NULL default '',
  `userdetail_lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `detail_int1` bigint(20) NOT NULL default '0',
  `detail_int2` bigint(20) NOT NULL default '0',
  `detail_int3` bigint(20) NOT NULL default '0',
  `detail_int4` bigint(20) NOT NULL default '0',
  `detail_int5` bigint(20) NOT NULL default '0',
  `detail_float1` float NOT NULL default '0',
  `detail_float2` float NOT NULL default '0',
  `detail_float3` float NOT NULL default '0',
  `detail_float4` float NOT NULL default '0',
  `detail_float5` float NOT NULL default '0',
  PRIMARY KEY  (`detail_id`),
  UNIQUE KEY `detail_login` (`detail_login`),
  KEY `detail_pid` (`detail_pid`),
  KEY `detail_formid` (`detail_formid`),
  KEY `detail_password` (`detail_password`),
  KEY `detail_aktiv` (`detail_aktiv`)
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_usergroup` (
  `group_id` int(11) NOT NULL auto_increment,
  `group_name` varchar(200) NOT NULL default '',
  `group_member` mediumtext NOT NULL,
  `group_value` longblob NOT NULL,
  `group_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `group_trash` int(1) NOT NULL default '0',
  `group_active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  KEY `group_member` (`group_member`(255))
) ENGINE=MyISAM;

CREATE TABLE `phpwcms_userlog` (
  `userlog_id` int(11) NOT NULL auto_increment,
  `logged_user` varchar(30) NOT NULL default '',
  `logged_username` varchar(100) NOT NULL default '',
  `logged_start` int(11) unsigned NOT NULL default '0',
  `logged_change` int(11) unsigned NOT NULL default '0',
  `logged_in` int(1) NOT NULL default '0',
  `logged_ip` varchar(24) NOT NULL default '',
  `logged_section` int(1) NOT NULL default '0',
  PRIMARY KEY  (`userlog_id`)
) ENGINE=MyISAM;

INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (1, 'AF', 'Afghanistan', 'Afghanistan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (2, 'AL', 'Albania', 'Albanien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (3, 'DZ', 'Algeria', 'Algerien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (4, 'AS', 'American Samoa', 'Amerikanisch Samoa');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (5, 'AD', 'Andorra', 'Andorra');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (6, 'AO', 'Angola', 'Angola');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (7, 'AI', 'Anguilla', 'Anguilla');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (8, 'AQ', 'Antarctica', 'Antarktis');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (9, 'AG', 'Antigua and Barbuda', 'Antigua und Barbuda');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (10, 'AR', 'Argentina', 'Argentinien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (11, 'AM', 'Armenia', 'Armenien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (12, 'AW', 'Aruba', 'Aruba');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (13, 'AU', 'Australia', 'Australien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (14, 'AT', 'Austria', 'Österreich');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (15, 'AZ', 'Azerbaijan', 'Aserbaidschan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (16, 'BS', 'Bahamas', 'Bahamas');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (17, 'BH', 'Bahrain', 'Bahrain');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (18, 'BD', 'Bangladesh', 'Bangladesch');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (19, 'BB', 'Barbados', 'Barbados');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (20, 'BY', 'Belarus', 'Belarus');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (21, 'BE', 'Belgium', 'Belgien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (22, 'BZ', 'Belize', 'Belize');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (23, 'BJ', 'Benin', 'Benin');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (24, 'BM', 'Bermuda', 'Bermuda');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (25, 'BT', 'Bhutan', 'Bhutan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (26, 'BO', 'Bolivia', 'Bolivien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (27, 'BA', 'Bosnia and Herzegovina', 'Bosnien und Herzegowina');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (28, 'BW', 'Botswana', 'Botsuana');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (29, 'BV', 'Bouvet Island', 'Bouvet-Insel');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (30, 'BR', 'Brazil', 'Brasilien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (31, 'IO', 'British Indian Ocean Territory', 'Britisches Territorium Im Indischen Ozean');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (32, 'BN', 'Brunei Darussalam', 'Brunei Darussalam');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (33, 'BG', 'Bulgaria', 'Bulgarien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (34, 'BF', 'Burkina Faso', 'Burkina Faso');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (35, 'BI', 'Burundi', 'Burundi');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (36, 'KH', 'Cambodia', 'Kambodscha');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (37, 'CM', 'Cameroon', 'Kamerun');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (38, 'CA', 'Canada', 'Kanada');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (39, 'CV', 'Cape Verde', 'Kap Verde');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (40, 'KY', 'Cayman Islands', 'Kaimaninseln');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (41, 'CF', 'Central African Republic', 'Zentralafrikanische Republik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (42, 'TD', 'Chad', 'Tschad');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (43, 'CL', 'Chile', 'Chile');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (44, 'CN', 'China', 'China');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (45, 'CX', 'Christmas Island', 'Weihnachtsinsel');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (46, 'CC', 'Cocos (Keeling) Islands', 'Kokosinseln (Keelingsinseln)');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (47, 'CO', 'Colombia', 'Kolumbien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (48, 'KM', 'Comoros', 'Komoren');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (49, 'CG', 'Congo', 'Kongo');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (50, 'CD', 'Congo, The Democratic Republic Of The', 'Kongo, Demokratische Republik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (51, 'CK', 'Cook Islands', 'Cook-Inseln');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (52, 'CR', 'Costa Rica', 'Costa Rica');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (53, 'CI', 'Côte D''Ivoire', 'Côte D''Ivoire');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (54, 'HR', 'Croatia', 'Kroatien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (55, 'CU', 'Cuba', 'Kuba');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (56, 'CY', 'Cyprus', 'Zypern');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (57, 'CZ', 'Czech Republic', 'Tschechische Republik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (58, 'DK', 'Denmark', 'Dänemark');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (59, 'DJ', 'Djibouti', 'Dschibuti');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (60, 'DM', 'Dominica', 'Dominica');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (61, 'DO', 'Dominican Republic', 'Dominikanische Republik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (62, 'TP', 'East Timor', 'Ost-Timor');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (63, 'EC', 'Ecuador', 'Ecuador');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (64, 'EG', 'Egypt', 'Ägypten');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (65, 'SV', 'El Salvador', 'El Salvador');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (66, 'GQ', 'Equatorial Guinea', 'Äquatorialguinea');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (67, 'ER', 'Eritrea', 'Eritrea');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (68, 'EE', 'Estonia', 'Estland');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (69, 'ET', 'Ethiopia', 'Äthiopien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (70, 'FK', 'Falkland Islands (Malvinas)', 'Falkland-Inseln (Malvinen)');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (71, 'FO', 'Faroe Islands', 'Färöer');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (72, 'FJ', 'Fiji', 'Fidschi');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (73, 'FI', 'Finland', 'Finnland');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (74, 'FR', 'France', 'Frankreich');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (75, 'GF', 'French Guiana', 'Französisch Guayana');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (76, 'PF', 'French Polynesia', 'Französisch Polynesien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (77, 'TF', 'French Southern Territories', 'Französische Südgebiete');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (78, 'GA', 'Gabon', 'Gabun');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (79, 'GM', 'Gambia', 'Gambia');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (80, 'GE', 'Georgia', 'Georgien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (81, 'DE', 'Germany', 'Deutschland');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (82, 'GH', 'Ghana', 'Ghana');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (83, 'GI', 'Gibraltar', 'Gibraltar');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (84, 'GR', 'Greece', 'Griechenland');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (85, 'GL', 'Greenland', 'Grönland');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (86, 'GD', 'Grenada', 'Grenada');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (87, 'GP', 'Guadeloupe', 'Guadeloupe');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (88, 'GU', 'Guam', 'Guam');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (89, 'GT', 'Guatemala', 'Guatemala');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (90, 'GN', 'Guinea', 'Guinea');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (91, 'GW', 'Guinea-Bissau', 'Guinea-Bissau');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (92, 'GY', 'Guyana', 'Guyana');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (93, 'HT', 'Haiti', 'Haiti');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (94, 'HM', 'Heard Island and McDonald Islands', 'Heard und McDonald');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (95, 'VA', 'Holy See (Vatican City State)', 'Vatikanstadt, Staat (Heiliger Stuhl)');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (96, 'HN', 'Honduras', 'Honduras');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (97, 'HK', 'Hong Kong', 'Hongkong');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (98, 'HU', 'Hungary', 'Ungarn');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (99, 'IS', 'Iceland', 'Island');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (100, 'IN', 'India', 'Indien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (101, 'ID', 'Indonesia', 'Indonesien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (102, 'IR', 'Iran, Islamic Republic Of', 'Iran (Islamische Republik)');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (103, 'IQ', 'Iraq', 'Irak');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (104, 'IE', 'Ireland', 'Irland');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (105, 'IL', 'Israel', 'Israel');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (106, 'IT', 'Italy', 'Italien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (107, 'JM', 'Jamaica', 'Jamaika');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (108, 'JP', 'Japan', 'Japan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (109, 'JO', 'Jordan', 'Jordanien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (110, 'KZ', 'Kazakhstan', 'Kasachstan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (111, 'KE', 'Kenya', 'Kenia');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (112, 'KI', 'Kiribati', 'Kiribati');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (113, 'KP', 'Korea, Democratic People''s Republic Of', 'Korea, Demokratische Volksrepublik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (114, 'KR', 'Korea, Republic Of', 'Korea, Republik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (115, 'KW', 'Kuwait', 'Kuwait');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (116, 'KG', 'Kyrgyzstan', 'Kirgisistan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (117, 'LA', 'Lao People''s Democratic Republic', 'Laos, Demokratische Volksrepublik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (118, 'LV', 'Latvia', 'Lettland');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (119, 'LB', 'Lebanon', 'Libanon');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (120, 'LS', 'Lesotho', 'Lesotho');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (121, 'LR', 'Liberia', 'Liberia');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (122, 'LY', 'Libyan Arab Jamahiriya', 'Libysch-Arabische Dschamahirija');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (123, 'LI', 'Liechtenstein', 'Liechtenstein');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (124, 'LT', 'Lithuania', 'Litauen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (125, 'LU', 'Luxembourg', 'Luxembourg');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (126, 'MO', 'Macao', 'Macau');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (127, 'MK', 'Macedonia, The Former Yugoslav Republic Of', 'Mazedonien, Ehemalige Jugoslawische Republik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (128, 'MG', 'Madagascar', 'Madagaskar');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (129, 'MW', 'Malawi', 'Malawi');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (130, 'MY', 'Malaysia', 'Malaysia');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (131, 'MV', 'Maldives', 'Malediven');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (132, 'ML', 'Mali', 'Mali');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (133, 'MT', 'Malta', 'Malta');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (134, 'MH', 'Marshall Islands', 'Marshallinseln');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (135, 'MQ', 'Martinique', 'Martinique');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (136, 'MR', 'Mauritania', 'Mauretanien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (137, 'MU', 'Mauritius', 'Mauritius');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (138, 'YT', 'Mayotte', 'Mayotte');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (139, 'MX', 'Mexico', 'Mexiko');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (140, 'FM', 'Micronesia, Federated States Of', 'Mikronesien, Föderierte Staaten Von');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (141, 'MD', 'Moldova, Republic Of', 'Moldau, Republik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (142, 'MC', 'Monaco', 'Monaco');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (143, 'MN', 'Mongolia', 'Mongolei');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (144, 'MS', 'Montserrat', 'Montserrat');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (145, 'MA', 'Morocco', 'Marokko');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (146, 'MZ', 'Mozambique', 'Mosambik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (147, 'MM', 'Myanmar', 'Myanmar');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (148, 'NA', 'Namibia', 'Namibia');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (149, 'NR', 'Nauru', 'Nauru');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (150, 'NP', 'Nepal', 'Nepal');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (151, 'NL', 'Netherlands', 'Niederlande');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (152, 'AN', 'Netherlands Antilles', 'Niederländische Antillen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (153, 'NC', 'New Caledonia', 'Neukaledonien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (154, 'NZ', 'New Zealand', 'Neuseeland');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (155, 'NI', 'Nicaragua', 'Nicaragua');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (156, 'NE', 'Niger', 'Niger');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (157, 'NG', 'Nigeria', 'Nigeria');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (158, 'NU', 'Niue', 'Niue');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (159, 'NF', 'Norfolk Island', 'Norfolk-Insel');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (160, 'MP', 'Northern Mariana Islands', 'Nördliche Marianen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (161, 'NO', 'Norway', 'Norwegen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (162, 'OM', 'Oman', 'Oman');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (163, 'PK', 'Pakistan', 'Pakistan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (164, 'PW', 'Palau', 'Palau');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (165, 'PS', 'Palestinian Territory, Occupied', 'Palästina');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (166, 'PA', 'Panama', 'Panama');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (167, 'PG', 'Papua New Guinea', 'Papua-Neuguinea');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (168, 'PY', 'Paraguay', 'Paraguay');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (169, 'PE', 'Peru', 'Peru');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (170, 'PH', 'Philippines', 'Philippinen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (171, 'PN', 'Pitcairn', 'Pitcairn');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (172, 'PL', 'Poland', 'Polen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (173, 'PT', 'Portugal', 'Portugal');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (174, 'PR', 'Puerto Rico', 'Puerto Rico');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (175, 'QA', 'Qatar', 'Katar');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (176, 'RE', 'Réunion', 'Réunion');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (177, 'RO', 'Romania', 'Rumänien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (178, 'RU', 'Russian Federation', 'Russische Föderation');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (179, 'RW', 'Rwanda', 'Ruanda');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (180, 'SH', 'Saint Helena', 'St. Helena');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (181, 'KN', 'Saint Kitts and Nevis', 'Saint Kitts und Nevis');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (182, 'LC', 'Saint Lucia', 'Santa Lucia');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (183, 'PM', 'Saint Pierre and Miquelon', 'Saint-Pierre und Miquelon');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (184, 'VC', 'Saint Vincent and The Grenadines', 'Saint Vincent und die Grenadinen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (185, 'WS', 'Samoa', 'Samoa');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (186, 'SM', 'San Marino', 'San Marino');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (187, 'ST', 'Sao Tome and Principe', 'São Tomé und Príncipe');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (188, 'SA', 'Saudi Arabia', 'Saudi-Arabien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (189, 'SN', 'Senegal', 'Senegal');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (190, 'SC', 'Seychelles', 'Seychellen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (191, 'SL', 'Sierra Leone', 'Sierra Leone');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (192, 'SG', 'Singapore', 'Singapur');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (193, 'SK', 'Slovakia', 'Slowakei (Slowakische Republik)');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (194, 'SI', 'Slovenia', 'Slowenien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (195, 'SB', 'Solomon Islands', 'Salomonen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (196, 'SO', 'Somalia', 'Somalia');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (197, 'ZA', 'South Africa', 'Südafrika');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (198, 'GS', 'South Georgia and The South Sandwich Islands', 'Südgeorgien und Südliche Sandwichinseln');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (199, 'ES', 'Spain', 'Spanien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (200, 'LK', 'Sri Lanka', 'Sri Lanka');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (201, 'SD', 'Sudan', 'Sudan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (202, 'SR', 'Suriname', 'Suriname');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (203, 'SJ', 'Svalbard and Jan Mayen', 'Svalbard und Jan Mayen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (204, 'SZ', 'Swaziland', 'Swasiland');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (205, 'SE', 'Sweden', 'Schweden');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (206, 'CH', 'Switzerland', 'Schweiz');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (207, 'SY', 'Syrian Arab Republic', 'Syrien, Arabische Republik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (208, 'TW', 'Taiwan, Province Of China', 'Taiwan (China)');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (209, 'TJ', 'Tajikistan', 'Tadschikistan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (210, 'TZ', 'Tanzania, United Republic Of', 'Tansania, Vereinigte Republik');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (211, 'TH', 'Thailand', 'Thailand');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (212, 'TG', 'Togo', 'Togo');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (213, 'TK', 'Tokelau', 'Tokelau');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (214, 'TO', 'Tonga', 'Tonga');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (215, 'TT', 'Trinidad and Tobago', 'Trinidad und Tobago');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (216, 'TN', 'Tunisia', 'Tunesien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (217, 'TR', 'Turkey', 'Türkei');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (218, 'TM', 'Turkmenistan', 'Turkmenistan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (219, 'TC', 'Turks Aand Caicos Islands', 'Turks- und Caicosinseln');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (220, 'TV', 'Tuvalu', 'Tuvalu');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (221, 'UG', 'Uganda', 'Uganda');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (222, 'UA', 'Ukraine', 'Ukraine');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (223, 'AE', 'United Arab Emirates', 'Vereinigte Arabische Emirate');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (224, 'GB', 'United Kingdom', 'United Kingdom');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (225, 'US', 'United States', 'Vereinigte Staaten');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (226, 'UM', 'United States Minor Outlying Islands', 'Kleinere entlegene Inseln der Vereinigten Staaten');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (227, 'UY', 'Uruguay', 'Uruguay');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (228, 'UZ', 'Uzbekistan', 'Usbekistan');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (229, 'VU', 'Vanuatu', 'Vanuatu');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (230, 'VE', 'Venezuela', 'Venezuela');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (231, 'VN', 'Viet Nam', 'Vietnam');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (232, 'VG', 'Virgin Islands, British', 'Jungferninseln (Britische)');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (233, 'VI', 'Virgin Islands, U.S.', 'Jungferninseln (Amerikanische)');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (234, 'WF', 'Wallis and Futuna', 'Wallis und Futuna');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (235, 'EH', 'Western Sahara', 'Westsahara');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (236, 'YE', 'Yemen', 'Jemen');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (237, 'YU', 'Yugoslavia', 'Jugoslawien');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (238, 'ZM', 'Zambia', 'Sambia');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (239, 'ZW', 'Zimbabwe', 'Simbabwe');
INSERT INTO `phpwcms_country` (`country_id`, `country_iso`, `country_name`, `country_name_de`) VALUES (240, 'AX', 'Åland Islands', 'Åland Inseln');

INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (1, 'architect');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (2, 'geologist');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (3, 'designer, graphic');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (4, 'designer, web');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (5, 'designer, industrial');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (6, 'designer, interieur');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (7, 'student');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (8, 'architect, landscape');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (9, 'teacher');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (10, 'writer');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (11, 'designer, interface');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (12, 'designer, screen');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (13, 'administrator');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (14, 'webmaster');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (15, 'geographer');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (16, 'artist');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (17, 'system operator');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (18, 'academic');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (19, 'theorist');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (20, 'critic');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (21, 'journalist');
INSERT INTO `phpwcms_profession` (`prof_id`, `prof_name`) VALUES (22, ' n/a');

INSERT INTO `phpwcms_template` VALUES (1, 1, 'Sample Template (very basic)', 1, 0x613a31343a7b733a343a226e616d65223b733a32383a2253616d706c652054656d706c61746520287665727920626173696329223b733a373a2264656661756c74223b693a313b733a363a226c61796f7574223b693a313b733a333a22637373223b613a313a7b693a303b733a31323a2266726f6e74656e642e637373223b7d733a383a2268746d6c68656164223b733a303a22223b733a383a226a736f6e6c6f6164223b733a303a22223b733a31303a2268656164657274657874223b733a303a22223b733a383a226d61696e74657874223b733a3637373a223c212d2d2076657279206261736963204449562062617365642074656d706c617465202d2d3e0d0a3c646976207374796c653d2270616464696e673a313070783b626f726465722d626f74746f6d3a31707820736f6c696420234343333330303b6d617267696e3a302030203132707820303b6261636b67726f756e642d636f6c6f723a234444444444443b223e0d0a3c68333e4d792073616d706c65206865616465723c2f68333e0d0a3c2f6469763e0d0a3c646976207374796c653d22706f736974696f6e3a72656c61746976653b746f703a2d313270783b666c6f61743a72696768743b70616464696e673a313070783b626f726465722d626f74746f6d3a31707820736f6c696420234343333330303b626f726465722d6c6566743a31707820736f6c696420234343333330303b6d617267696e3a302030203135707820313570783b6261636b67726f756e642d636f6c6f723a234444444444443b77696474683a31353070783b223e0d0a3c68363e6e617669676174696f6e3a3c2f68363e0d0a7b4e41565f4c4953545f554c3a502c307d0d0a3c2f6469763e0d0a3c70207374796c653d226d617267696e3a2030203020313070782030223e796f752061726520686572653a207b42524541444352554d427d3c2f703e0d0a7b434f4e54454e547d0d0a0d0a3c646976207374796c653d22626f726465722d746f703a31707820736f6c696420234343333330303b6d617267696e3a313570782030203020303b746578742d616c69676e3a63656e7465723b70616464696e673a3770783b223e0d0a636f707972696768742026636f70793b2032303037203c6120687265663d22687474703a2f2f7777772e70687077636d732e646522207461726765743d225f626c616e6b223e70687077636d732e64653c2f613e0d0a3c2f6469763e223b733a31303a22666f6f74657274657874223b733a303a22223b733a383a226c65667474657874223b733a303a22223b733a393a22726967687474657874223b733a303a22223b733a393a226572726f7274657874223b733a34323a223c68313e343034206572726f7220706167653c2f68313e0d0a3c703e4e6f20636f6e74656e743c2f703e223b733a31303a2266656c6f67696e75726c223b733a303a22223b733a323a226964223b693a313b7d, 0);

INSERT INTO `phpwcms_pagelayout` VALUES (1, 'Sample Pagelayout', 1, 0x613a36323a7b733a323a226964223b693a313b733a31313a226c61796f75745f6e616d65223b733a31373a2253616d706c6520506167656c61796f7574223b733a31343a226c61796f75745f64656661756c74223b693a313b733a31323a226c61796f75745f616c69676e223b693a303b733a31313a226c61796f75745f74797065223b693a303b733a31373a226c61796f75745f626f726465725f746f70223b733a303a22223b733a32303a226c61796f75745f626f726465725f626f74746f6d223b733a303a22223b733a31383a226c61796f75745f626f726465725f6c656674223b733a303a22223b733a31393a226c61796f75745f626f726465725f7269676874223b733a303a22223b733a31353a226c61796f75745f6e6f626f72646572223b693a313b733a31323a226c61796f75745f7469746c65223b733a393a22506167657469746c65223b733a31383a226c61796f75745f7469746c655f6f72646572223b693a343b733a31393a226c61796f75745f7469746c655f737061636572223b733a333a22207c20223b733a31343a226c61796f75745f6267636f6c6f72223b623a303b733a31343a226c61796f75745f6267696d616765223b733a303a22223b733a31353a226c61796f75745f6a736f6e6c6f6164223b733a303a22223b733a31363a226c61796f75745f74657874636f6c6f72223b623a303b733a31363a226c61796f75745f6c696e6b636f6c6f72223b623a303b733a31333a226c61796f75745f76636f6c6f72223b623a303b733a31333a226c61796f75745f61636f6c6f72223b623a303b733a31363a226c61796f75745f616c6c5f7769647468223b733a303a22223b733a31383a226c61796f75745f616c6c5f6267636f6c6f72223b623a303b733a31383a226c61796f75745f616c6c5f6267696d616765223b733a303a22223b733a31363a226c61796f75745f616c6c5f636c617373223b733a303a22223b733a32303a226c61796f75745f636f6e74656e745f7769647468223b733a303a22223b733a32323a226c61796f75745f636f6e74656e745f6267636f6c6f72223b623a303b733a32323a226c61796f75745f636f6e74656e745f6267696d616765223b733a303a22223b733a32303a226c61796f75745f636f6e74656e745f636c617373223b733a303a22223b733a31373a226c61796f75745f6c6566745f7769647468223b733a303a22223b733a31393a226c61796f75745f6c6566745f6267636f6c6f72223b623a303b733a31393a226c61796f75745f6c6566745f6267696d616765223b733a303a22223b733a31373a226c61796f75745f6c6566745f636c617373223b733a303a22223b733a31383a226c61796f75745f72696768745f7769647468223b733a303a22223b733a32303a226c61796f75745f72696768745f6267636f6c6f72223b623a303b733a32303a226c61796f75745f72696768745f6267696d616765223b733a303a22223b733a31383a226c61796f75745f72696768745f636c617373223b733a303a22223b733a32323a226c61796f75745f6c65667473706163655f7769647468223b733a303a22223b733a32343a226c61796f75745f6c65667473706163655f6267636f6c6f72223b623a303b733a32343a226c61796f75745f6c65667473706163655f6267696d616765223b733a303a22223b733a32323a226c61796f75745f6c65667473706163655f636c617373223b733a303a22223b733a32333a226c61796f75745f726967687473706163655f7769647468223b733a303a22223b733a32353a226c61796f75745f726967687473706163655f6267636f6c6f72223b623a303b733a32353a226c61796f75745f726967687473706163655f6267696d616765223b733a303a22223b733a32333a226c61796f75745f726967687473706163655f636c617373223b733a303a22223b733a32303a226c61796f75745f6865616465725f686569676874223b733a303a22223b733a32313a226c61796f75745f6865616465725f6267636f6c6f72223b623a303b733a32313a226c61796f75745f6865616465725f6267696d616765223b733a303a22223b733a31393a226c61796f75745f6865616465725f636c617373223b733a303a22223b733a32323a226c61796f75745f746f7073706163655f686569676874223b733a303a22223b733a32333a226c61796f75745f746f7073706163655f6267636f6c6f72223b623a303b733a32333a226c61796f75745f746f7073706163655f6267696d616765223b733a303a22223b733a32313a226c61796f75745f746f7073706163655f636c617373223b733a303a22223b733a32353a226c61796f75745f626f74746f6d73706163655f686569676874223b733a303a22223b733a32363a226c61796f75745f626f74746f6d73706163655f6267636f6c6f72223b623a303b733a32363a226c61796f75745f626f74746f6d73706163655f6267696d616765223b733a303a22223b733a32343a226c61796f75745f626f74746f6d73706163655f636c617373223b733a303a22223b733a32303a226c61796f75745f666f6f7465725f686569676874223b733a303a22223b733a32313a226c61796f75745f666f6f7465725f6267636f6c6f72223b623a303b733a32313a226c61796f75745f666f6f7465725f6267696d616765223b733a303a22223b733a31393a226c61796f75745f666f6f7465725f636c617373223b733a303a22223b733a31333a226c61796f75745f72656e646572223b693a323b733a31393a226c61796f75745f637573746f6d626c6f636b73223b733a303a22223b7d, 0);




