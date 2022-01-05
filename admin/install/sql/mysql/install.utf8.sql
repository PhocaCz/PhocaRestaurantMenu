-- -------------------------------------------------------------------- --
-- Phoca Restaurant Menu manual installation                            --
-- -------------------------------------------------------------------- --
-- See documentation on https://www.phoca.cz/                            --
--                                                                      --
-- Change all prefixes #__ to prefix which is set in your Joomla! site  --
-- (e.g. from #__phocamenu_config to jos_phocamenu_config)              --
-- Run this SQL queries in your database tool, e.g. in phpMyAdmin       --
-- If you have questions, just ask in Phoca Forum                       --
-- https://www.phoca.cz/forum/                                           --
-- -------------------------------------------------------------------- --

CREATE TABLE IF NOT EXISTS `#__phocamenu_config` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `type` int(3) NOT NULL default '0',
  `title` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `header` text,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_from` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_to` datetime NOT NULL default '0000-00-00 00:00:00',
  `footer` text,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `language` char(7) NOT NULL default '',
  `params` text,
  `metakey` text,
  `metadesc` text,
  `metadata` text,
  PRIMARY KEY  (`id`),
  KEY `catid` (`published`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__phocamenu_day` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `type` int(3) NOT NULL default '0',
  `title` datetime NOT NULL default '0000-00-00 00:00:00',
  `alias` varchar(100) NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `language` char(7) NOT NULL Default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__phocamenu_list` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `type` int(3) NOT NULL default '0',
  `title` text,
  `alias` text,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `language` char(7) NOT NULL Default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__phocamenu_email` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `type` int(3) NOT NULL default '0',
  `from` varchar(100) NOT NULL,
  `fromname` varchar(100) NOT NULL,
  `to` text,
  `subject` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `message` text,
  `mode` int(1) NOT NULL default '0',
  `cc` text,
  `bcc` text,
  `attachment` text,
  `replyto` varchar(100) NOT NULL,
  `replytoname` varchar(100) NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `language` char(7) NOT NULL Default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`published`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__phocamenu_group` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `type` int(3) NOT NULL default '0',
  `title` text,
  `alias` text,
  `message` text,
  `display_second_price` tinyint(1) NOT NULL default '0',
  `header_price` varchar(255) NOT NULL default '',
  `header_price2` varchar(255) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `language` char(7) NOT NULL Default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__phocamenu_item` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `imageid` int(11) NOT NULL default '0',
  `type` int(3) NOT NULL default '0',
  `quantity` varchar(20) NOT NULL,
  `title` text,
  `alias` text,
  `price` varchar(20) NOT NULL,
  `price2` varchar(20) NOT NULL,
  `description` text,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `language` char(7) NOT NULL Default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`)
)  DEFAULT CHARSET=utf8;

-- UTF-8 test: ä, ö, ü
