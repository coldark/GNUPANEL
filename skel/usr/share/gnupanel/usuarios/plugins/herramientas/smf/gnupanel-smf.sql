
CREATE TABLE smf_attachments (
ID_ATTACH int(10) unsigned NOT NULL auto_increment,
ID_THUMB int(10) unsigned NOT NULL default '0',
ID_MSG int(10) unsigned NOT NULL default '0',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
attachmentType tinyint(3) unsigned NOT NULL default '0',
filename tinytext NOT NULL,
size int(10) unsigned NOT NULL default '0',
downloads mediumint(8) unsigned NOT NULL default '0',
width mediumint(8) unsigned NOT NULL default '0',
height mediumint(8) unsigned NOT NULL default '0',
PRIMARY KEY (ID_ATTACH),
UNIQUE ID_MEMBER (ID_MEMBER, ID_ATTACH),
KEY ID_MSG (ID_MSG)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_ban_groups (
ID_BAN_GROUP mediumint(8) unsigned NOT NULL auto_increment,
name varchar(20) NOT NULL default '',
ban_time int(10) unsigned NOT NULL default '0',
expire_time int(10) unsigned,
cannot_access tinyint(3) unsigned NOT NULL default '0',
cannot_register tinyint(3) unsigned NOT NULL default '0',
cannot_post tinyint(3) unsigned NOT NULL default '0',
cannot_login tinyint(3) unsigned NOT NULL default '0',
reason tinytext NOT NULL,
notes text NOT NULL,
PRIMARY KEY (ID_BAN_GROUP)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_ban_items (
ID_BAN mediumint(8) unsigned NOT NULL auto_increment,
ID_BAN_GROUP smallint(5) unsigned NOT NULL default '0',
ip_low1 tinyint(3) unsigned NOT NULL default '0',
ip_high1 tinyint(3) unsigned NOT NULL default '0',
ip_low2 tinyint(3) unsigned NOT NULL default '0',
ip_high2 tinyint(3) unsigned NOT NULL default '0',
ip_low3 tinyint(3) unsigned NOT NULL default '0',
ip_high3 tinyint(3) unsigned NOT NULL default '0',
ip_low4 tinyint(3) unsigned NOT NULL default '0',
ip_high4 tinyint(3) unsigned NOT NULL default '0',
hostname tinytext NOT NULL,
email_address tinytext NOT NULL,
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
hits mediumint(8) unsigned NOT NULL default '0',
PRIMARY KEY (ID_BAN),
KEY ID_BAN_GROUP (ID_BAN_GROUP)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_board_permissions (
ID_GROUP smallint(5) NOT NULL default '0',
ID_BOARD smallint(5) unsigned NOT NULL default '0',
permission varchar(30) NOT NULL default '',
addDeny tinyint(4) NOT NULL default '1',
PRIMARY KEY (ID_GROUP, ID_BOARD, permission)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_board_permissions
(ID_GROUP, ID_BOARD, permission)
VALUES (-1, 0, 'poll_view'),
(0, 0, 'remove_own'),
(0, 0, 'lock_own'),
(0, 0, 'mark_any_notify'),
(0, 0, 'mark_notify'),
(0, 0, 'modify_own'),
(0, 0, 'poll_add_own'),
(0, 0, 'poll_edit_own'),
(0, 0, 'poll_lock_own'),
(0, 0, 'poll_post'),
(0, 0, 'poll_view'),
(0, 0, 'poll_vote'),
(0, 0, 'post_attachment'),
(0, 0, 'post_new'),
(0, 0, 'post_reply_any'),
(0, 0, 'post_reply_own'),
(0, 0, 'delete_own'),
(0, 0, 'report_any'),
(0, 0, 'send_topic'),
(0, 0, 'view_attachments'),
(2, 0, 'moderate_board'),
(2, 0, 'post_new'),
(2, 0, 'post_reply_own'),
(2, 0, 'post_reply_any'),
(2, 0, 'poll_post'),
(2, 0, 'poll_add_any'),
(2, 0, 'poll_remove_any'),
(2, 0, 'poll_view'),
(2, 0, 'poll_vote'),
(2, 0, 'poll_edit_any'),
(2, 0, 'report_any'),
(2, 0, 'lock_own'),
(2, 0, 'send_topic'),
(2, 0, 'mark_any_notify'),
(2, 0, 'mark_notify'),
(2, 0, 'delete_own'),
(2, 0, 'modify_own'),
(2, 0, 'make_sticky'),
(2, 0, 'lock_any'),
(2, 0, 'remove_any'),
(2, 0, 'move_any'),
(2, 0, 'merge_any'),
(2, 0, 'split_any'),
(2, 0, 'delete_any'),
(2, 0, 'modify_any'),
(3, 0, 'moderate_board'),
(3, 0, 'post_new'),
(3, 0, 'post_reply_own'),
(3, 0, 'post_reply_any'),
(3, 0, 'poll_post'),
(3, 0, 'poll_add_own'),
(3, 0, 'poll_remove_any'),
(3, 0, 'poll_view'),
(3, 0, 'poll_vote'),
(3, 0, 'report_any'),
(3, 0, 'lock_own'),
(3, 0, 'send_topic'),
(3, 0, 'mark_any_notify'),
(3, 0, 'mark_notify'),
(3, 0, 'delete_own'),
(3, 0, 'modify_own'),
(3, 0, 'make_sticky'),
(3, 0, 'lock_any'),
(3, 0, 'remove_any'),
(3, 0, 'move_any'),
(3, 0, 'merge_any'),
(3, 0, 'split_any'),
(3, 0, 'delete_any'),
(3, 0, 'modify_any');

CREATE TABLE smf_boards (
ID_BOARD smallint(5) unsigned NOT NULL auto_increment,
ID_CAT tinyint(4) unsigned NOT NULL default '0',
childLevel tinyint(4) unsigned NOT NULL default '0',
ID_PARENT smallint(5) unsigned NOT NULL default '0',
boardOrder smallint(5) NOT NULL default '0',
ID_LAST_MSG int(10) unsigned NOT NULL default '0',
ID_MSG_UPDATED int(10) unsigned NOT NULL default '0',
memberGroups varchar(255) NOT NULL default '-1,0',
name tinytext NOT NULL,
description text NOT NULL,
numTopics mediumint(8) unsigned NOT NULL default '0',
numPosts mediumint(8) unsigned NOT NULL default '0',
countPosts tinyint(4) NOT NULL default '0',
ID_THEME tinyint(4) unsigned NOT NULL default '0',
permission_mode tinyint(4) unsigned NOT NULL default '0',
override_theme tinyint(4) unsigned NOT NULL default '0',
PRIMARY KEY (ID_BOARD),
UNIQUE categories (ID_CAT, ID_BOARD),
KEY ID_PARENT (ID_PARENT),
KEY ID_MSG_UPDATED (ID_MSG_UPDATED),
KEY memberGroups (memberGroups(48))
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_boards
(ID_BOARD, ID_CAT, boardOrder, ID_LAST_MSG, ID_MSG_UPDATED, name, description, numTopics, numPosts, memberGroups)
VALUES (1, 1, 1, 1, 1, 'General Discussion', 'Feel free to talk about anything and everything in this board.', 1, 1, '-1,0');

CREATE TABLE smf_calendar (
ID_EVENT smallint(5) unsigned NOT NULL auto_increment,
startDate date NOT NULL default '0001-01-01',
endDate date NOT NULL default '0001-01-01',
ID_BOARD smallint(5) unsigned NOT NULL default '0',
ID_TOPIC mediumint(8) unsigned NOT NULL default '0',
title varchar(48) NOT NULL default '',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
PRIMARY KEY (ID_EVENT),
KEY startDate (startDate),
KEY endDate (endDate),
KEY topic (ID_TOPIC, ID_MEMBER)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_calendar_holidays (
ID_HOLIDAY smallint(5) unsigned NOT NULL auto_increment,
eventDate date NOT NULL default '0001-01-01',
title varchar(30) NOT NULL default '',
PRIMARY KEY (ID_HOLIDAY),
KEY eventDate (eventDate)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_calendar_holidays
(title, eventDate)
VALUES ('New Year\'s', '0004-01-01'),
('Christmas', '0004-12-25'),
('Valentine\'s Day', '0004-02-14'),
('St. Patrick\'s Day', '0004-03-17'),
('April Fools', '0004-04-01'),
('Earth Day', '0004-04-22'),
('United Nations Day', '0004-10-24'),
('Halloween', '0004-10-31'),
('Mother\'s Day', '2004-05-09'),
('Mother\'s Day', '2005-05-08'),
('Mother\'s Day', '2006-05-14'),
('Mother\'s Day', '2007-05-13'),
('Mother\'s Day', '2008-05-11'),
('Mother\'s Day', '2009-05-10'),
('Mother\'s Day', '2010-05-09'),
('Mother\'s Day', '2011-05-08'),
('Mother\'s Day', '2012-05-13'),
('Mother\'s Day', '2013-05-12'),
('Mother\'s Day', '2014-05-11'),
('Mother\'s Day', '2015-05-10'),
('Mother\'s Day', '2016-05-08'),
('Mother\'s Day', '2017-05-14'),
('Mother\'s Day', '2018-05-13'),
('Mother\'s Day', '2019-05-12'),
('Mother\'s Day', '2020-05-10'),
('Father\'s Day', '2004-06-20'),
('Father\'s Day', '2005-06-19'),
('Father\'s Day', '2006-06-18'),
('Father\'s Day', '2007-06-17'),
('Father\'s Day', '2008-06-15'),
('Father\'s Day', '2009-06-21'),
('Father\'s Day', '2010-06-20'),
('Father\'s Day', '2011-06-19'),
('Father\'s Day', '2012-06-17'),
('Father\'s Day', '2013-06-16'),
('Father\'s Day', '2014-06-15'),
('Father\'s Day', '2015-06-21'),
('Father\'s Day', '2016-06-19'),
('Father\'s Day', '2017-06-18'),
('Father\'s Day', '2018-06-17'),
('Father\'s Day', '2019-06-16'),
('Father\'s Day', '2020-06-21'),
('Summer Solstice', '2004-06-20'),
('Summer Solstice', '2005-06-20'),
('Summer Solstice', '2006-06-21'),
('Summer Solstice', '2007-06-21'),
('Summer Solstice', '2008-06-20'),
('Summer Solstice', '2009-06-20'),
('Summer Solstice', '2010-06-21'),
('Summer Solstice', '2011-06-21'),
('Summer Solstice', '2012-06-20'),
('Summer Solstice', '2013-06-21'),
('Summer Solstice', '2014-06-21'),
('Summer Solstice', '2015-06-21'),
('Summer Solstice', '2016-06-20'),
('Summer Solstice', '2017-06-20'),
('Summer Solstice', '2018-06-21'),
('Summer Solstice', '2019-06-21'),
('Summer Solstice', '2020-06-20'),
('Vernal Equinox', '2004-03-19'),
('Vernal Equinox', '2005-03-20'),
('Vernal Equinox', '2006-03-20'),
('Vernal Equinox', '2007-03-20'),
('Vernal Equinox', '2008-03-19'),
('Vernal Equinox', '2009-03-20'),
('Vernal Equinox', '2010-03-20'),
('Vernal Equinox', '2011-03-20'),
('Vernal Equinox', '2012-03-20'),
('Vernal Equinox', '2013-03-20'),
('Vernal Equinox', '2014-03-20'),
('Vernal Equinox', '2015-03-20'),
('Vernal Equinox', '2016-03-19'),
('Vernal Equinox', '2017-03-20'),
('Vernal Equinox', '2018-03-20'),
('Vernal Equinox', '2019-03-20'),
('Vernal Equinox', '2020-03-19'),
('Winter Solstice', '2004-12-21'),
('Winter Solstice', '2005-12-21'),
('Winter Solstice', '2006-12-22'),
('Winter Solstice', '2007-12-22'),
('Winter Solstice', '2008-12-21'),
('Winter Solstice', '2009-12-21'),
('Winter Solstice', '2010-12-21'),
('Winter Solstice', '2011-12-22'),
('Winter Solstice', '2012-12-21'),
('Winter Solstice', '2013-12-21'),
('Winter Solstice', '2014-12-21'),
('Winter Solstice', '2015-12-21'),
('Winter Solstice', '2016-12-21'),
('Winter Solstice', '2017-12-21'),
('Winter Solstice', '2018-12-21'),
('Winter Solstice', '2019-12-21'),
('Winter Solstice', '2020-12-21'),
('Autumnal Equinox', '2004-09-22'),
('Autumnal Equinox', '2005-09-22'),
('Autumnal Equinox', '2006-09-22'),
('Autumnal Equinox', '2007-09-23'),
('Autumnal Equinox', '2008-09-22'),
('Autumnal Equinox', '2009-09-22'),
('Autumnal Equinox', '2010-09-22'),
('Autumnal Equinox', '2011-09-23'),
('Autumnal Equinox', '2012-09-22'),
('Autumnal Equinox', '2013-09-22'),
('Autumnal Equinox', '2014-09-22'),
('Autumnal Equinox', '2015-09-23'),
('Autumnal Equinox', '2016-09-22'),
('Autumnal Equinox', '2017-09-22'),
('Autumnal Equinox', '2018-09-22'),
('Autumnal Equinox', '2019-09-23'),
('Autumnal Equinox', '2020-09-22');

INSERT INTO smf_calendar_holidays
(title, eventDate)
VALUES ('Independence Day', '0004-07-04'),
('Cinco de Mayo', '0004-05-05'),
('Flag Day', '0004-06-14'),
('Veterans Day', '0004-11-11'),
('Groundhog Day', '0004-02-02'),
('Thanksgiving', '2004-11-25'),
('Thanksgiving', '2005-11-24'),
('Thanksgiving', '2006-11-23'),
('Thanksgiving', '2007-11-22'),
('Thanksgiving', '2008-11-27'),
('Thanksgiving', '2009-11-26'),
('Thanksgiving', '2010-11-25'),
('Thanksgiving', '2011-11-24'),
('Thanksgiving', '2012-11-22'),
('Thanksgiving', '2013-11-21'),
('Thanksgiving', '2014-11-20'),
('Thanksgiving', '2015-11-26'),
('Thanksgiving', '2016-11-24'),
('Thanksgiving', '2017-11-23'),
('Thanksgiving', '2018-11-22'),
('Thanksgiving', '2019-11-21'),
('Thanksgiving', '2020-11-26'),
('Memorial Day', '2004-05-31'),
('Memorial Day', '2005-05-30'),
('Memorial Day', '2006-05-29'),
('Memorial Day', '2007-05-28'),
('Memorial Day', '2008-05-26'),
('Memorial Day', '2009-05-25'),
('Memorial Day', '2010-05-31'),
('Memorial Day', '2011-05-30'),
('Memorial Day', '2012-05-28'),
('Memorial Day', '2013-05-27'),
('Memorial Day', '2014-05-26'),
('Memorial Day', '2015-05-25'),
('Memorial Day', '2016-05-30'),
('Memorial Day', '2017-05-29'),
('Memorial Day', '2018-05-28'),
('Memorial Day', '2019-05-27'),
('Memorial Day', '2020-05-25'),
('Labor Day', '2004-09-06'),
('Labor Day', '2005-09-05'),
('Labor Day', '2006-09-04'),
('Labor Day', '2007-09-03'),
('Labor Day', '2008-09-01'),
('Labor Day', '2009-09-07'),
('Labor Day', '2010-09-06'),
('Labor Day', '2011-09-05'),
('Labor Day', '2012-09-03'),
('Labor Day', '2013-09-09'),
('Labor Day', '2014-09-08'),
('Labor Day', '2015-09-07'),
('Labor Day', '2016-09-05'),
('Labor Day', '2017-09-04'),
('Labor Day', '2018-09-03'),
('Labor Day', '2019-09-09'),
('Labor Day', '2020-09-07'),
('D-Day', '0004-06-06');

CREATE TABLE smf_categories (
ID_CAT tinyint(4) unsigned NOT NULL auto_increment,
catOrder tinyint(4) NOT NULL default '0',
name tinytext NOT NULL,
canCollapse tinyint(1) NOT NULL default '1',
PRIMARY KEY (ID_CAT)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_categories
VALUES (1, 0, 'General Category', 1);

CREATE TABLE smf_collapsed_categories (
ID_CAT tinyint(4) unsigned NOT NULL default '0',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
PRIMARY KEY (ID_CAT, ID_MEMBER)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_actions (
ID_ACTION int(10) unsigned NOT NULL auto_increment,
logTime int(10) unsigned NOT NULL default '0',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ip char(16) NOT NULL default ' ',
action varchar(30) NOT NULL default '',
extra text NOT NULL,
PRIMARY KEY (ID_ACTION),
KEY logTime (logTime),
KEY ID_MEMBER (ID_MEMBER)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_activity (
date date NOT NULL default '0001-01-01',
hits mediumint(8) unsigned NOT NULL default '0',
topics smallint(5) unsigned NOT NULL default '0',
posts smallint(5) unsigned NOT NULL default '0',
registers smallint(5) unsigned NOT NULL default '0',
mostOn smallint(5) unsigned NOT NULL default '0',
PRIMARY KEY (date),
KEY hits (hits),
KEY mostOn (mostOn)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_banned (
ID_BAN_LOG mediumint(8) unsigned NOT NULL auto_increment,
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ip char(16) NOT NULL default ' ',
email tinytext NOT NULL,
logTime int(10) unsigned NOT NULL default '0',
PRIMARY KEY (ID_BAN_LOG),
KEY logTime (logTime)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_boards (
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ID_BOARD smallint(5) unsigned NOT NULL default '0',
ID_MSG int(10) unsigned NOT NULL default '0',
PRIMARY KEY (ID_MEMBER, ID_BOARD)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_errors (
ID_ERROR mediumint(8) unsigned NOT NULL auto_increment,
logTime int(10) unsigned NOT NULL default '0',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ip char(16) NOT NULL default ' ',
url text NOT NULL,
message text NOT NULL,
session char(32) NOT NULL default ' ',
PRIMARY KEY (ID_ERROR),
KEY logTime (logTime),
KEY ID_MEMBER (ID_MEMBER),
KEY ip (ip(16))
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_floodcontrol (
ip char(16) NOT NULL default ' ',
logTime int(10) unsigned NOT NULL default '0',
PRIMARY KEY (ip(16))
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_karma (
ID_TARGET mediumint(8) unsigned NOT NULL default '0',
ID_EXECUTOR mediumint(8) unsigned NOT NULL default '0',
logTime int(10) unsigned NOT NULL default '0',
action tinyint(4) NOT NULL default '0',
PRIMARY KEY (ID_TARGET, ID_EXECUTOR),
KEY logTime (logTime)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_mark_read (
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ID_BOARD smallint(5) unsigned NOT NULL default '0',
ID_MSG int(10) unsigned NOT NULL default '0',
PRIMARY KEY (ID_MEMBER, ID_BOARD)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_notify (
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ID_TOPIC mediumint(8) unsigned NOT NULL default '0',
ID_BOARD smallint(5) unsigned NOT NULL default '0',
sent tinyint(1) unsigned NOT NULL default '0',
PRIMARY KEY (ID_MEMBER, ID_TOPIC, ID_BOARD)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_online (
session varchar(32) NOT NULL default '',
logTime timestamp(14) /*!40102 NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP */,
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ip int(10) unsigned NOT NULL default '0',
url text NOT NULL,
PRIMARY KEY (session),
KEY logTime (logTime),
KEY ID_MEMBER (ID_MEMBER)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_polls (
ID_POLL mediumint(8) unsigned NOT NULL default '0',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ID_CHOICE tinyint(3) unsigned NOT NULL default '0',
PRIMARY KEY (ID_POLL, ID_MEMBER, ID_CHOICE)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_search_messages (
ID_SEARCH tinyint(3) unsigned NOT NULL default '0',
ID_MSG int(10) unsigned NOT NULL default '0',
PRIMARY KEY (ID_SEARCH, ID_MSG)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_search_results (
ID_SEARCH tinyint(3) unsigned NOT NULL default '0',
ID_TOPIC mediumint(8) unsigned NOT NULL default '0',
ID_MSG int(10) unsigned NOT NULL default '0',
relevance smallint(5) unsigned NOT NULL default '0',
num_matches smallint(5) unsigned NOT NULL default '0',
PRIMARY KEY (ID_SEARCH, ID_TOPIC)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_search_subjects (
word varchar(20) NOT NULL default '',
ID_TOPIC mediumint(8) unsigned NOT NULL default '0',
PRIMARY KEY (word, ID_TOPIC),
KEY ID_TOPIC (ID_TOPIC)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_search_topics (
ID_SEARCH tinyint(3) unsigned NOT NULL default '0',
ID_TOPIC mediumint(9) NOT NULL default '0',
PRIMARY KEY (ID_SEARCH, ID_TOPIC)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_log_topics (
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ID_TOPIC mediumint(8) unsigned NOT NULL default '0',
ID_MSG int(10) unsigned NOT NULL default '0',
PRIMARY KEY (ID_MEMBER, ID_TOPIC),
KEY ID_TOPIC (ID_TOPIC)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_membergroups (
ID_GROUP smallint(5) unsigned NOT NULL auto_increment,
groupName varchar(80) NOT NULL default '',
onlineColor varchar(20) NOT NULL default '',
minPosts mediumint(9) NOT NULL default '-1',
maxMessages smallint(5) unsigned NOT NULL default '0',
stars tinytext NOT NULL,
PRIMARY KEY (ID_GROUP),
KEY minPosts (minPosts)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_membergroups
(ID_GROUP, groupName, onlineColor, minPosts, stars)
VALUES (1, 'Administrator', '#FF0000', -1, '5#staradmin.gif'),
(2, 'Global Moderator', '#0000FF', -1, '5#stargmod.gif'),
(3, 'Moderator', '', -1, '5#starmod.gif'),
(4, 'Newbie', '', 0, '1#star.gif'),
(5, 'Jr. Member', '', 50, '2#star.gif'),
(6, 'Full Member', '', 100, '3#star.gif'),
(7, 'Sr. Member', '', 250, '4#star.gif'),
(8, 'Hero Member', '', 500, '5#star.gif');

CREATE TABLE smf_members (
ID_MEMBER mediumint(8) unsigned NOT NULL auto_increment,
memberName varchar(80) NOT NULL default '',
dateRegistered int(10) unsigned NOT NULL default '0',
posts mediumint(8) unsigned NOT NULL default '0',
ID_GROUP smallint(5) unsigned NOT NULL default '0',
lngfile tinytext NOT NULL,
lastLogin int(10) unsigned NOT NULL default '0',
realName tinytext NOT NULL,
instantMessages smallint(5) NOT NULL default 0,
unreadMessages smallint(5) NOT NULL default 0,
buddy_list text NOT NULL,
pm_ignore_list tinytext NOT NULL,
messageLabels text NOT NULL,
passwd varchar(64) NOT NULL default '',
emailAddress tinytext NOT NULL,
personalText tinytext NOT NULL,
gender tinyint(4) unsigned NOT NULL default '0',
birthdate date NOT NULL default '0001-01-01',
websiteTitle tinytext NOT NULL,
websiteUrl tinytext NOT NULL,
location tinytext NOT NULL,
ICQ tinytext NOT NULL,
AIM varchar(16) NOT NULL default '',
YIM varchar(32) NOT NULL default '',
MSN tinytext NOT NULL,
hideEmail tinyint(4) NOT NULL default '0',
showOnline tinyint(4) NOT NULL default '1',
timeFormat varchar(80) NOT NULL default '',
signature text NOT NULL,
timeOffset float NOT NULL default '0',
avatar tinytext NOT NULL,
pm_email_notify tinyint(4) NOT NULL default '0',
karmaBad smallint(5) unsigned NOT NULL default '0',
karmaGood smallint(5) unsigned NOT NULL default '0',
usertitle tinytext NOT NULL,
notifyAnnouncements tinyint(4) NOT NULL default '1',
notifyOnce tinyint(4) NOT NULL default '1',
notifySendBody tinyint(4) NOT NULL default '0',
notifyTypes tinyint(4) NOT NULL default '2',
memberIP tinytext NOT NULL,
memberIP2 tinytext NOT NULL,
secretQuestion tinytext NOT NULL,
secretAnswer varchar(64) NOT NULL default '',
ID_THEME tinyint(4) unsigned NOT NULL default '0',
is_activated tinyint(3) unsigned NOT NULL default '1',
validation_code varchar(10) NOT NULL default '',
ID_MSG_LAST_VISIT int(10) unsigned NOT NULL default '0',
additionalGroups tinytext NOT NULL,
smileySet varchar(48) NOT NULL default '',
ID_POST_GROUP smallint(5) unsigned NOT NULL default '0',
totalTimeLoggedIn int(10) unsigned NOT NULL default '0',
passwordSalt varchar(5) NOT NULL default '',
PRIMARY KEY (ID_MEMBER),
KEY memberName (memberName(30)),
KEY dateRegistered (dateRegistered),
KEY ID_GROUP (ID_GROUP),
KEY birthdate (birthdate),
KEY posts (posts),
KEY lastLogin (lastLogin),
KEY lngfile (lngfile(30)),
KEY ID_POST_GROUP (ID_POST_GROUP)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_message_icons (
ID_ICON smallint(5) unsigned NOT NULL auto_increment,
title varchar(80) NOT NULL default '',
filename varchar(80) NOT NULL default '',
ID_BOARD mediumint(8) unsigned NOT NULL default 0,
iconOrder smallint(5) unsigned NOT NULL default 0,
PRIMARY KEY (ID_ICON),
KEY ID_BOARD (ID_BOARD)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_message_icons
(filename, title, iconOrder)
VALUES ('xx', 'Standard', '0'),
('thumbup', 'Thumb Up', '1'),
('thumbdown', 'Thumb Down', '2'),
('exclamation', 'Exclamation point', '3'),
('question', 'Question mark', '4'),
('lamp', 'Lamp', '5'),
('smiley', 'Smiley', '6'),
('angry', 'Angry', '7'),
('cheesy', 'Cheesy', '8'),
('grin', 'Grin', '9'),
('sad', 'Sad', '10'),
('wink', 'Wink', '11');

CREATE TABLE smf_messages (
ID_MSG int(10) unsigned NOT NULL auto_increment,
ID_TOPIC mediumint(8) unsigned NOT NULL default '0',
ID_BOARD smallint(5) unsigned NOT NULL default '0',
posterTime int(10) unsigned NOT NULL default '0',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
ID_MSG_MODIFIED int(10) unsigned NOT NULL default '0',
subject tinytext NOT NULL,
posterName tinytext NOT NULL,
posterEmail tinytext NOT NULL,
posterIP tinytext NOT NULL,
smileysEnabled tinyint(4) NOT NULL default '1',
modifiedTime int(10) unsigned NOT NULL default '0',
modifiedName tinytext NOT NULL,
body text NOT NULL,
icon varchar(16) NOT NULL default 'xx',
PRIMARY KEY (ID_MSG),
UNIQUE topic (ID_TOPIC, ID_MSG),
UNIQUE ID_BOARD (ID_BOARD, ID_MSG),
UNIQUE ID_MEMBER (ID_MEMBER, ID_MSG),
KEY ipIndex (posterIP(15), ID_TOPIC),
KEY participation (ID_MEMBER, ID_TOPIC),
KEY showPosts (ID_MEMBER, ID_BOARD),
KEY ID_TOPIC (ID_TOPIC)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_messages
(ID_MSG, ID_MSG_MODIFIED, ID_TOPIC, ID_BOARD, posterTime, subject, posterName, posterEmail, posterIP, modifiedName, body, icon)
VALUES (1, 1, 1, 1, UNIX_TIMESTAMP(), 'Welcome to SMF!', 'Simple Machines', 'info@simplemachines.org', '127.0.0.1', '', 'Welcome to Simple Machines Forum!

We hope you enjoy using your forum.  If you have any problems, please feel free to [url=http://www.simplemachines.org/community/index.php]ask us for assistance[/url].

Thanks!
Simple Machines', 'xx');

CREATE TABLE smf_moderators (
ID_BOARD smallint(5) unsigned NOT NULL default '0',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
PRIMARY KEY (ID_BOARD, ID_MEMBER)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_package_servers (
ID_SERVER smallint(5) unsigned NOT NULL auto_increment,
name tinytext NOT NULL,
url tinytext NOT NULL,
PRIMARY KEY (ID_SERVER)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_package_servers
(name, url)
VALUES ('Simple Machines Third-party Mod Site', 'http://mods.simplemachines.org');

CREATE TABLE smf_permissions (
ID_GROUP smallint(5) NOT NULL default '0',
permission varchar(30) NOT NULL default '',
addDeny tinyint(4) NOT NULL default '1',
PRIMARY KEY (ID_GROUP, permission)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_permissions
(ID_GROUP, permission)
VALUES (-1, 'search_posts'),
(-1, 'calendar_view'),
(-1, 'view_stats'),
(-1, 'profile_view_any'),
(0, 'view_mlist'),
(0, 'search_posts'),
(0, 'profile_view_own'),
(0, 'profile_view_any'),
(0, 'pm_read'),
(0, 'pm_send'),
(0, 'calendar_view'),
(0, 'view_stats'),
(0, 'who_view'),
(0, 'profile_identity_own'),
(0, 'profile_extra_own'),
(0, 'profile_remove_own'),
(0, 'profile_server_avatar'),
(0, 'profile_upload_avatar'),
(0, 'profile_remote_avatar'),
(0, 'karma_edit'),
(2, 'view_mlist'),
(2, 'search_posts'),
(2, 'profile_view_own'),
(2, 'profile_view_any'),
(2, 'pm_read'),
(2, 'pm_send'),
(2, 'calendar_view'),
(2, 'view_stats'),
(2, 'who_view'),
(2, 'profile_identity_own'),
(2, 'profile_extra_own'),
(2, 'profile_remove_own'),
(2, 'profile_server_avatar'),
(2, 'profile_upload_avatar'),
(2, 'profile_remote_avatar'),
(2, 'profile_title_own'),
(2, 'calendar_post'),
(2, 'calendar_edit_any'),
(2, 'karma_edit');

CREATE TABLE smf_personal_messages (
ID_PM int(10) unsigned NOT NULL auto_increment,
ID_MEMBER_FROM mediumint(8) unsigned NOT NULL default '0',
deletedBySender tinyint(3) unsigned NOT NULL default '0',
fromName tinytext NOT NULL,
msgtime int(10) unsigned NOT NULL default '0',
subject tinytext NOT NULL,
body text NOT NULL,
PRIMARY KEY (ID_PM),
KEY ID_MEMBER (ID_MEMBER_FROM, deletedBySender),
KEY msgtime (msgtime)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_pm_recipients (
ID_PM int(10) unsigned NOT NULL default '0',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
labels varchar(60) NOT NULL default '-1',
bcc tinyint(3) unsigned NOT NULL default '0',
is_read tinyint(3) unsigned NOT NULL default '0',
deleted tinyint(3) unsigned NOT NULL default '0',
PRIMARY KEY (ID_PM, ID_MEMBER),
UNIQUE ID_MEMBER (ID_MEMBER, deleted, ID_PM)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_polls (
ID_POLL mediumint(8) unsigned NOT NULL auto_increment,
question tinytext NOT NULL,
votingLocked tinyint(1) NOT NULL default '0',
maxVotes tinyint(3) unsigned NOT NULL default '1',
expireTime int(10) unsigned NOT NULL default '0',
hideResults tinyint(3) unsigned NOT NULL default '0',
changeVote tinyint(3) unsigned NOT NULL default '0',
ID_MEMBER mediumint(8) unsigned NOT NULL default '0',
posterName tinytext NOT NULL,
PRIMARY KEY (ID_POLL)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_poll_choices (
ID_POLL mediumint(8) unsigned NOT NULL default '0',
ID_CHOICE tinyint(3) unsigned NOT NULL default '0',
label tinytext NOT NULL,
votes smallint(5) unsigned NOT NULL default '0',
PRIMARY KEY (ID_POLL, ID_CHOICE)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_settings (
variable tinytext NOT NULL,
value text NOT NULL,
PRIMARY KEY (variable(30))
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_settings
(variable, value)
VALUES ('smfVersion', '1.1.2'),
('news', 'SMF - Just Installed!'),
('compactTopicPagesContiguous', '5'),
('compactTopicPagesEnable', '1'),
('enableStickyTopics', '1'),
('todayMod', '1'),
('karmaMode', '0'),
('karmaTimeRestrictAdmins', '1'),
('enablePreviousNext', '1'),
('pollMode', '1'),
('enableVBStyleLogin', '1'),
('enableCompressedOutput', '0'),
('karmaWaitTime', '1'),
('karmaMinPosts', '0'),
('karmaLabel', 'Karma:'),
('karmaSmiteLabel', '[smite]'),
('karmaApplaudLabel', '[applaud]'),
('attachmentSizeLimit', '128'),
('attachmentPostLimit', '192'),
('attachmentNumPerPostLimit', '4'),
('attachmentDirSizeLimit', '10240'),
('attachmentUploadDir', ''),
('attachmentExtensions', 'doc,gif,jpg,mpg,pdf,png,txt,zip'),
('attachmentCheckExtensions', '0'),
('attachmentShowImages', '1'),
('attachmentEnable', '1'),
('attachmentEncryptFilenames', '1'),
('attachmentThumbnails', '1'),
('attachmentThumbWidth', '150'),
('attachmentThumbHeight', '150'),
('censorIgnoreCase', '1'),
('mostOnline', '1'),
('mostOnlineToday', '1'),
('mostDate', UNIX_TIMESTAMP()),
('allow_disableAnnounce', '1'),
('trackStats', '1'),
('userLanguage', '1'),
('titlesEnable', '1'),
('topicSummaryPosts', '15'),
('enableErrorLogging', '1'),
('max_image_width', '0'),
('max_image_height', '0'),
('onlineEnable', '0'),
('cal_holidaycolor', '000080'),
('cal_bdaycolor', '920AC4'),
('cal_eventcolor', '078907'),
('cal_enabled', '0'),
('cal_maxyear', '2010'),
('cal_minyear', '2004'),
('cal_daysaslink', '0'),
('cal_defaultboard', ''),
('cal_showeventsonindex', '0'),
('cal_showbdaysonindex', '0'),
('cal_showholidaysonindex', '0'),
('cal_showeventsoncalendar', '1'),
('cal_showbdaysoncalendar', '1'),
('cal_showholidaysoncalendar', '1'),
('cal_showweeknum', '0'),
('cal_maxspan', '7'),
('smtp_host', ''),
('smtp_port', '25'),
('smtp_username', ''),
('smtp_password', ''),
('mail_type', '0'),
('timeLoadPageEnable', '0'),
('totalTopics', '1'),
('totalMessages', '1'),
('simpleSearch', '0'),
('censor_vulgar', ''),
('censor_proper', ''),
('enablePostHTML', '0'),
('theme_allow', '1'),
('theme_default', '1'),
('theme_guests', '1'),
('enableEmbeddedFlash', '0'),
('xmlnews_enable', '1'),
('xmlnews_maxlen', '255'),
('hotTopicPosts', '15'),
('hotTopicVeryPosts', '25'),
('registration_method', '0'),
('send_validation_onChange', '0'),
('send_welcomeEmail', '1'),
('allow_editDisplayName', '1'),
('allow_hideOnline', '1'),
('allow_hideEmail', '1'),
('guest_hideContacts', '0'),
('spamWaitTime', '5'),
('pm_spam_settings', '10,5,20'),
('reserveWord', '0'),
('reserveCase', '1'),
('reserveUser', '1'),
('reserveName', '1'),
('reserveNames', 'Admin\nWebmaster\nGuest\nroot'),
('autoLinkUrls', '1'),
('banLastUpdated', '0'),
('smileys_dir', ''),
('smileys_url', ''),
('avatar_directory', ''),
('avatar_url', ''),
('avatar_max_height_external', '65'),
('avatar_max_width_external', '65'),
('avatar_action_too_large', 'option_html_resize'),
('avatar_max_height_upload', '65'),
('avatar_max_width_upload', '65'),
('avatar_resize_upload', '1'),
('avatar_download_png', '1'),
('failed_login_threshold', '3'),
('oldTopicDays', '120'),
('edit_wait_time', '90'),
('edit_disable_time', '0'),
('autoFixDatabase', '1'),
('allow_guestAccess', '1'),
('time_format', '%B %d, %Y, %I:%M:%S %p'),
('number_format', '1234.00'),
('enableBBC', '1'),
('max_messageLength', '20000'),
('max_signatureLength', '300'),
('autoOptDatabase', '7'),
('autoOptMaxOnline', '0'),
('autoOptLastOpt', '0'),
('defaultMaxMessages', '15'),
('defaultMaxTopics', '20'),
('defaultMaxMembers', '30'),
('enableParticipation', '1'),
('recycle_enable', '0'),
('recycle_board', '0'),
('maxMsgID', '1'),
('enableAllMessages', '0'),
('fixLongWords', '0'),
('knownThemes', '1,2,3'),
('who_enabled', '1'),
('time_offset', '0'),
('cookieTime', '60'),
('lastActive', '15'),
('smiley_sets_known', 'default,classic'),
('smiley_sets_names', 'Default\nClassic'),
('smiley_sets_default', 'default'),
('cal_days_for_index', '7'),
('requireAgreement', '1'),
('unapprovedMembers', '0'),
('default_personalText', ''),
('package_make_backups', '1'),
('databaseSession_enable', '1'),
('databaseSession_loose', '1'),
('databaseSession_lifetime', '2880'),
('search_cache_size', '50'),
('search_results_per_page', '30'),
('search_weight_frequency', '30'),
('search_weight_age', '25'),
('search_weight_length', '20'),
('search_weight_subject', '15'),
('search_weight_first_message', '10'),
('search_max_results', '1200'),
('permission_enable_deny', '0'),
('permission_enable_postgroups', '0'),
('permission_enable_by_board', '0');

CREATE TABLE smf_sessions (
session_id char(32) NOT NULL,
last_update int(10) unsigned NOT NULL,
data text NOT NULL,
PRIMARY KEY (session_id)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE smf_smileys (
ID_SMILEY smallint(5) unsigned NOT NULL auto_increment,
code varchar(30) NOT NULL default '',
filename varchar(48) NOT NULL default '',
description varchar(80) NOT NULL default '',
smileyRow tinyint(4) unsigned NOT NULL default '0',
smileyOrder smallint(5) unsigned NOT NULL default '0',
hidden tinyint(4) unsigned NOT NULL default '0',
PRIMARY KEY (ID_SMILEY)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_smileys
(code, filename, description, smileyOrder, hidden)
VALUES (':)', 'smiley.gif', 'Smiley', 0, 0),
('PRIMER', 'wink.gif', 'Wink', 1, 0),
(':D', 'cheesy.gif', 'Cheesy', 2, 0),
('SEGUND', 'grin.gif', 'Grin', 3, 0),
('>:(', 'angry.gif', 'Angry', 4, 0),
(':(', 'sad.gif', 'Sad', 5, 0),
(':o', 'shocked.gif', 'Shocked', 6, 0),
('8)', 'cool.gif', 'Cool', 7, 0),
('???', 'huh.gif', 'Huh?', 8, 0),
('::)', 'rolleyes.gif', 'Roll Eyes', 9, 0),
(':P', 'tongue.gif', 'Tongue', 10, 0),
(':-[', 'embarrassed.gif', 'Embarrassed', 11, 0),
(':-X', 'lipsrsealed.gif', 'Lips Sealed', 12, 0),
(':-\\', 'undecided.gif', 'Undecided', 13, 0),
(':-*', 'kiss.gif', 'Kiss', 14, 0),
(':\'(', 'cry.gif', 'Cry', 15, 0),
('>:D', 'evil.gif', 'Evil', 16, 1),
('^-^', 'azn.gif', 'Azn', 17, 1),
('O0', 'afro.gif', 'Afro', 18, 1);

CREATE TABLE smf_themes (
ID_MEMBER mediumint(8) NOT NULL default '0',
ID_THEME tinyint(4) unsigned NOT NULL default '1',
variable tinytext NOT NULL,
value text NOT NULL,
PRIMARY KEY (ID_THEME, ID_MEMBER, variable(30)),
KEY ID_MEMBER (ID_MEMBER)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_themes
(ID_THEME, variable, value)
VALUES (1, 'name', 'SMF Default Theme - Core'),
(1, 'theme_url', 'http://test.debianarg.com.ar/smf/Themes/default'),
(1, 'images_url', 'http://test.debianarg.com.ar/smf/Themes/default/images'),
(1, 'theme_dir', '/var/www/sitios/admin/rmalvarez@gnupanel.com.ar/debianarg.com.ar/subdominios/test/smf/Themes/default'),
(1, 'show_bbc', '1'),
(1, 'show_latest_member', '1'),
(1, 'show_modify', '1'),
(1, 'show_user_images', '1'),
(1, 'show_blurb', '1'),
(1, 'show_gender', '0'),
(1, 'show_newsfader', '0'),
(1, 'number_recent_posts', '0'),
(1, 'show_member_bar', '1'),
(1, 'linktree_link', '1'),
(1, 'show_profile_buttons', '1'),
(1, 'show_mark_read', '1'),
(1, 'show_sp1_info', '1'),
(1, 'linktree_inline', '0'),
(1, 'show_board_desc', '1'),
(1, 'newsfader_time', '5000'),
(1, 'allow_no_censored', '0'),
(1, 'additional_options_collapsable', '1'),
(1, 'use_image_buttons', '1'),
(1, 'enable_news', '1'),
(2, 'name', 'Classic YaBB SE Theme'),
(2, 'theme_url', 'http://test.debianarg.com.ar/smf/Themes/classic'),
(2, 'images_url', 'http://test.debianarg.com.ar/smf/Themes/classic/images'),
(2, 'theme_dir', '/var/www/sitios/admin/rmalvarez@gnupanel.com.ar/debianarg.com.ar/subdominios/test/smf/Themes/classic'),
(3, 'name', 'Babylon Theme'),
(3, 'theme_url', 'http://test.debianarg.com.ar/smf/Themes/babylon'),
(3, 'images_url', 'http://test.debianarg.com.ar/smf/Themes/babylon/images'),
(3, 'theme_dir', '/var/www/sitios/admin/rmalvarez@gnupanel.com.ar/debianarg.com.ar/subdominios/test/smf/Themes/babylon');

CREATE TABLE smf_topics (
ID_TOPIC mediumint(8) unsigned NOT NULL auto_increment,
isSticky tinyint(4) NOT NULL default '0',
ID_BOARD smallint(5) unsigned NOT NULL default '0',
ID_FIRST_MSG int(10) unsigned NOT NULL default '0',
ID_LAST_MSG int(10) unsigned NOT NULL default '0',
ID_MEMBER_STARTED mediumint(8) unsigned NOT NULL default '0',
ID_MEMBER_UPDATED mediumint(8) unsigned NOT NULL default '0',
ID_POLL mediumint(8) unsigned NOT NULL default '0',
numReplies int(10) unsigned NOT NULL default '0',
numViews int(10) unsigned NOT NULL default '0',
locked tinyint(4) NOT NULL default '0',
PRIMARY KEY (ID_TOPIC),
UNIQUE lastMessage (ID_LAST_MSG, ID_BOARD),
UNIQUE firstMessage (ID_FIRST_MSG, ID_BOARD),
UNIQUE poll (ID_POLL, ID_TOPIC),
KEY isSticky (isSticky),
KEY ID_BOARD (ID_BOARD)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO smf_topics
(ID_TOPIC, ID_BOARD, ID_FIRST_MSG, ID_LAST_MSG, ID_MEMBER_STARTED, ID_MEMBER_UPDATED)
VALUES (1, 1, 1, 1, 0, 0);

INSERT INTO smf_settings (variable, value) VALUES ('global_character_set', 'UTF-8');
REPLACE INTO smf_settings (variable, value) VALUES ('default_timezone','Etc/GMT+3') ;
OPTIMIZE TABLE `smf_attachments`, `smf_ban_groups`, `smf_ban_items`, `smf_board_permissions`, `smf_boards`, `smf_calendar`, `smf_calendar_holidays`, `smf_categories`, `smf_collapsed_categories`, `smf_log_actions`, `smf_log_activity`, `smf_log_banned`, `smf_log_boards`, `smf_log_errors`, `smf_log_floodcontrol`, `smf_log_karma`, `smf_log_mark_read`, `smf_log_notify`, `smf_log_online`, `smf_log_polls`, `smf_log_search_messages`, `smf_log_search_results`, `smf_log_search_subjects`, `smf_log_search_topics`, `smf_log_topics`, `smf_membergroups`, `smf_members`, `smf_message_icons`, `smf_messages`, `smf_moderators`, `smf_package_servers`, `smf_permissions`, `smf_personal_messages`, `smf_pm_recipients`, `smf_poll_choices`, `smf_polls`, `smf_sessions`, `smf_settings`, `smf_smileys`, `smf_themes`, `smf_topics`;
ALTER TABLE smf_boards ORDER BY ID_BOARD ;



